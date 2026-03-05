<?php

namespace App\Http\Controllers;

use App\Models\Orderm;
use App\Models\Item;
use App\Models\Orderreceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemreceivedController extends Controller
{
    protected $orderm;

    /**
     * Constructor to initialize the Orderm model instance.
     */
    public function __construct()
    {
        $this->orderm = new Orderm();
    }

    /**
     * Display the list of items expected to be received.
     * Only shows orders that are confirmed (Added) but have a 'Pending' receiving status.
     */
    public function showData(Request $request)
    {
        // Fetch orders that are confirmed and awaiting reception
        $orderms = Orderm::where('po_state', 'Added')
            ->where('itm_rec_state', 'Pending')
            ->with('received') // Eager load the 'received' relationship
            ->orderBy('order_id', 'asc')
            ->get();

        return view('pages.itemreceived', compact('orderms'));
    }

    /**
     * Process the reception of an item, update order status,
     * increase master stock levels, and log the transaction.
     */
    public function update(Request $request)
    {
        // 1. Validate the incoming receiving data
        $data = $request->validate([
            'id' => 'required|integer|exists:orderms,id',
            'itm_rec_date' => 'required|date',
            'itm_res_qty' => 'required|numeric|min:0', // Quantity actually received
            'itm_warranty' => 'nullable|string|max:255',
            'itm_unit_price' => 'required|numeric',
            'itm_tot_price' => 'required|numeric',
            'inlineRadioOptions' => 'required|string|in:option1,option2',
        ]);

        try {
            return DB::transaction(function () use ($data, $request) {
                // Find the order record and lock for update
                $orderm = Orderm::lockForUpdate()->findOrFail($request->id);

                // Determine the final state
                $receivedState = $data['inlineRadioOptions'] === 'option1' ? 'Finnished' : 'Pending';

                // Update the record in the 'orderm' table
                $orderm->update([
                    'itm_rec_date' => $data['itm_rec_date'],
                    'itm_res_qty' => $data['itm_res_qty'],
                    'itm_warranty' => $data['itm_warranty'],
                    'itm_unit_price' => $data['itm_unit_price'],
                    'itm_tot_price' => $data['itm_tot_price'],
                    'itm_rec_state' => $receivedState,
                ]);

                // 2. UPDATE MASTER ITEM STOCK
                $item = Item::where('itm_code', $orderm->itm_code)->lockForUpdate()->first();

                if ($item) {
                    // Precise calculation using floatval
                    $receivedQty = floatval($data['itm_res_qty']);

                    // Add to Book Stock first
                    $item->itm_book_stock = floatval($item->itm_book_stock) + $receivedQty;

                    // Re-calculate Physical Stock (Physical = Updated Book Stock - Loan Stock)
                    $loanStock = floatval($item->itm_loan_stock ?? 0);
                    $item->itm_stock = $item->itm_book_stock - $loanStock;

                    $item->save();
                }

                // 3. LOG TRANSACTION HISTORY
                Orderreceived::create([
                    'table_id' => $orderm->id,
                    'order_id' => $orderm->order_id,
                    'cus_id' => $orderm->cus_id,
                    'itm_code' => $orderm->itm_code,
                    'itm_qty' => $orderm->itm_qty, // Original ordered quantity
                    'sup_id' => $orderm->sup_id,
                    'itm_rec_date' => $data['itm_rec_date'],
                    'itm_res_qty' => $data['itm_res_qty'], // Actually received quantity
                    'itm_warranty' => $data['itm_warranty'],
                    'itm_unit_price' => $data['itm_unit_price'],
                    'itm_tot_price' => $data['itm_tot_price'],
                    'itm_rec_state' => 'Item Received',
                ]);

                return redirect()->route('itemreceived.showData')->with('success', 'Item updated, stock increased & saved to history!');
            });
        } catch (\Exception $e) {
            // Return back with error message if something fails
            return redirect()
                ->back()
                ->with('error', 'Error updating stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel an order item during the receiving process.
     * Updates the status to 'Cancel' for both receiving and billing.
     */
    public function cancelorder(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
        ]);

        $orderm = Orderm::findOrFail($request->id);

        // Mark the order as canceled in the database
        $orderm->update([
            'itm_rec_state' => 'Cancel',
            'bill_state' => 'Cancel',
        ]);

        return redirect()->route('itemreceived.showData')->with('success', 'Order item has been canceled successfully.');
    }
}
