<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orderm;
use App\Models\Item;
use App\Models\Ordertemp;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Orderreceived;
use Validator;

class ExcessitemController extends Controller
{
    // Property to hold the Ordertemp model instance
    protected $ordertemp;

    /**
     * Constructor to initialize the Ordertemp model instance for dependency usage.
     */
    public function __construct()
    {
        $this->ordertemp = new Ordertemp();
    }

    /**
     * Prepare and display the Excess Item page.
     * It handles the logic for retrieving existing session data based on the Order ID.
     */
    public function showData(Request $request)
    {
        // 1. Fetch the most recent record specifically for 'excessitem' type to maintain order continuity
        $lastAutoOrder = Ordertemp::where('order_typ', 'excessitem')
                                   ->orderBy('order_id', 'desc')
                                   ->first();

        $neworderId = '';

        // --- ORDER ID PERSISTENCE LOGIC ---
        // If an order is currently in progress (exists in temp table), keep using that ID.
        if ($lastAutoOrder) {
            $neworderId = $lastAutoOrder->order_id;
        } else {
            // Otherwise, initialize as empty (logic for generating a brand new ID can be added here)
            $neworderId = '';
        }

        // 2. Fetch all items currently added to this specific Order ID from the temp table
        // 'with(item)' eager loads the related item details for the view
        $excessitems = Ordertemp::with('item')
                                ->where('order_id', $neworderId)
                                ->where('order_typ', 'excessitem')
                                ->get();

        // 3. Retrieve the customer details (Name/ID) assigned to the current order session
        $currentCustomer = Ordertemp::where('order_id', $neworderId)
                                    ->orderBy('id', 'desc')
                                    ->select('cus_id', 'cus_name')
                                    ->first();

        // 4. Retrieve the original date the current order was started
        $currentOrderDate = Ordertemp::where('order_id', $neworderId)
                                     ->orderBy('id', 'asc')
                                     ->value('order_date');

        // Return the view with the necessary data objects
        return view('pages.excessitem', compact('excessitems', 'neworderId', 'currentCustomer', 'currentOrderDate'));
    }

    /**
     * Store an individual item into the temporary orders table.
     * This acts as a "Draft" or "Shopping Cart" before the order is finalized.
     */
    public function tempsaveData(Request $request)
    {
        // Define strict validation rules
        $rules = [
            'order_id'   => 'required|string|max:20',
            'cus_name'   => 'required|string|max:255',
            'cus_id'     => 'required|string|max:255',
            'itm_code'   => 'required|string|max:15',
            'itm_qty'    => 'required|string|max:255',
            'order_typ'  => 'required|string|max:255',
            'order_date' => 'required|string|max:255',
        ];

        // Define user-friendly custom error messages
        $messages = [
            'order_id.required' => 'Order ID is required.',
            'itm_code.required' => 'Item Code is required.',
            'itm_qty.required'  => 'Item QTY is required.',
        ];

        // Run the validation
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // Save the validated item entry into the temporary table
        $this->ordertemp->create($validatedData);

        return redirect()->back()->with('success', 'Item added to temporary list')->withInput();
    }

    /**
     * Update details of a specific item already inside the temporary table.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            'cus_name' => 'required|string|max:255',
            'cus_id'   => 'required|string|max:255',
            'itm_code' => 'required|string|max:15',
            'itm_qty'  => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        $ordertemp = $this->ordertemp->find($id);

        if (!$ordertemp) {
            return redirect('excessitem')->with('error', 'Order Item not found.');
        }

        // Apply validated updates to the record
        $ordertemp->update($validator->validated());

        return redirect()->back()->with('success', 'Excess Item updated successfully.');
    }

    /**
     * Remove an item from the temporary list (delete from draft).
     */
    public function deleteData($id)
    {
        try {
            $ordertemp = Ordertemp::find($id);

            if (!$ordertemp) {
                return redirect()->back()->with('error', 'Order Item not found.');
            }

            $ordertemp->delete();
            return redirect()->back()->with('success', 'Excess Item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Finalize the order: Transfers data from Temp table to Master tables and updates inventory.
     * Uses DB Transaction to ensure data integrity (all or nothing).
     */
    public function finishOrder(Request $request)
    {
        $orderId = $request->input('order_id');

        // Retrieve all temporary items for the current session
        $pendingItems = Ordertemp::where('order_id', $orderId)
                                 ->where('order_typ', 'excessitem')
                                 ->get();

        if ($pendingItems->isEmpty()) {
            return redirect()->back()->with('error', 'No running items found for this order.');
        }

        // Use a Transaction to wrap all DB operations
        DB::transaction(function () use ($pendingItems, $orderId, $request) {
            foreach ($pendingItems as $item) {

                // 1. CREATE MASTER ORDER RECORD (Orderm)
                // This converts the temporary item into a permanent order entry
                $orderm = Orderm::create([
                    'order_id'         => $item->order_id,
                    'cus_id'           => $item->cus_id,
                    'cus_name'         => $item->cus_name,
                    'itm_code'         => $item->itm_code,
                    'itm_qty'          => $item->itm_qty,
                    'order_typ'        => 'excessitem',
                    'order_date'       => $item->order_date,
                    'po_date'          => $item->order_date,
                    'po_number'        => $item->order_id,
                    'sup_id'           => '-',
                    'sup_name'         => '-',
                    'po_state'         => 'excessitem',
                    'itm_rec_date'     => $item->order_date,
                    'itm_inv_numer'    => '-',
                    'itm_res_qty'      => '0',
                    'itm_warranty'     => '-',
                    'itm_unit_price'   => '0',
                    'itm_tot_price'    => '0',
                    'inv_tot_price'    => '0',
                    'itm_rec_state'    => 'excessitem',
                    'bill_submit_date' => $item->order_date,
                    'bill_number'      => $item->order_id,
                    'bill_state'       => 'excessitem',
                ]);

                // 2. UPDATE MASTER INVENTORY STOCK
                // lockForUpdate() prevents other users from changing the stock at the exact same millisecond
                $masterItem = Item::where('itm_code', $item->itm_code)->lockForUpdate()->first();

                if ($masterItem) {
                    $receivedQty = floatval($item->itm_qty);

                    // A. Update the Book Stock (Total stock recorded in system)
                    $masterItem->itm_book_stock = floatval($masterItem->itm_book_stock) + $receivedQty;

                    // B. Calculate Physical Stock
                    // Formula: Physical Stock = (New Book Stock) - (Current Loan Stock)
                    $loanStock = floatval($masterItem->itm_loan_stock ?? 0);
                    $masterItem->itm_stock = $masterItem->itm_book_stock - $loanStock;

                    // C. Set item status to 'ordered' and save changes
                    $masterItem->itm_status = 'ordered';
                    $masterItem->save();
                }

                // 3. LOG TRANSACTION HISTORY (Orderreceived)
                // This creates an audit trail for the stock increase
                Orderreceived::create([
                    'table_id'         => $orderm->id,
                    'order_id'         => $orderm->order_id,
                    'cus_id'           => $orderm->cus_id,
                    'itm_code'         => $orderm->itm_code,
                    'itm_qty'          => $orderm->itm_qty,
                    'sup_id'           => 'S012', // Static Supplier ID assigned to excess items
                    'itm_rec_date'     => $orderm->order_date,
                    'itm_res_qty'      => '0',
                    'itm_warranty'     => '-',
                    'itm_unit_price'   => '0',
                    'itm_tot_price'    => '0',
                    'itm_rec_state'    => 'excessitem',
                    'itm_inv_date'     => $orderm->order_date,
                    'itm_inv_numer'    => $orderm->order_id,
                    'bill_submit_date' => $orderm->order_date,
                    'bill_number'      => $orderm->order_id,
                    'bill_state'       => 'excessitem',
                ]);
            }

            // 4. CLEANUP: Clear the temporary table for this Order ID once finalized
            Ordertemp::where('order_id', $orderId)
                     ->where('order_typ', 'excessitem')
                     ->delete();
        });

        return redirect()->back()->with('success', 'Successfully Excess Item stock updated.');
    }
}
