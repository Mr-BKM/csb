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
    protected $ordertemp;

    /**
     * Initialize the controller and the Ordertemp model instance.
     */
    public function __construct()
    {
        $this->ordertemp = new Ordertemp();
    }

    /**
     * Prepare and display the Auto-Order generation page.
     * Manages ID sequencing and determines if a new order can be submitted.
     */

    // public function showData(){
    //     return view('pages.excessitem');
    // }

    public function showData(Request $request)
    {
        // Retrieve the most recent records for different order categories
        $lastAutoOrder = Ordertemp::where('order_typ', 'excessitem')->orderBy('order_id', 'desc')->first();

        $neworderId = '';

        // --- ORDER ID GENERATION LOGIC ---

        // CASE 1: If there is an active 'Running' order, keep using that existing ID.
        if ($lastAutoOrder) {
            $neworderId = $lastAutoOrder->order_id;

            // CASE 2: If Auto or Completed orders exist, calculate the next sequence number.
        } else {
            $neworderId = '';
        }

        // Fetch items, pending orders, and customer details associated with the current order ID
        $excessitems = Ordertemp::with('item')->where('order_id', $neworderId)->where('order_typ', 'excessitem')->get();
        $currentCustomer = Ordertemp::where('order_id', $neworderId)->orderBy('id', 'desc')->select('cus_id', 'cus_name')->first();
        $currentOrderDate = Ordertemp::where('order_id', $neworderId)->orderBy('id', 'asc')->value('order_date');

        return view('pages.excessitem', compact('excessitems', 'neworderId', 'currentCustomer', 'currentOrderDate'));
    }

    /**
     * Store a single item into the temporary orders table with validation.
     */
    public function tempsaveData(Request $request)
    {
        // Define validation rules for the temporary item
        $rules = [
            'order_id' => 'required|string|max:20',
            'cus_name' => 'required|string|max:255',
            'cus_id' => 'required|string|max:255',
            'itm_code' => 'required|string|max:15',
            'itm_qty' => 'required|string|max:255',
            'order_typ' => 'required|string|max:255',
            'order_date' => 'required|string|max:255',
        ];

        // Custom error messages for the validation
        $messages = [
            'order_id.required' => 'Order ID is required.',
            'itm_code.required' => 'Item Code is required.',
            'itm_qty.required' => 'Item QTY is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $validatedData = $validator->validated();

        // Save validated data to the temporary table
        $this->ordertemp->create($validatedData);

        return redirect()->back()->with('success', 'Item added to temporary list')->withInput();
    }

    /**
     * Update the details of an existing item in the temporary table.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            'cus_name' => 'required|string|max:255',
            'cus_id' => 'required|string|max:255',
            'itm_code' => 'required|string|max:15',
            'itm_qty' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        $ordertemp = $this->ordertemp->find($id);

        if (!$ordertemp) {
            return redirect('excessitem')->with('error', 'Order Item not found.');
        }

        // Update record with validated data
        $ordertemp->update($validator->validated());

        return redirect()->back()->with('success', 'Order Item updated successfully.');
    }

    /**
     * Delete an item from the temporary order list.
     */
    public function deleteData($id)
    {
        try {
            $ordertemp = Ordertemp::find($id);

            if (!$ordertemp) {
                return redirect()->back()->with('error', 'Order Item not found.');
            }

            $ordertemp->delete();
            return redirect()->back()->with('success', 'Order Item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Transfer all "Running" items to the final Order table and clear temporary records.
     */
    public function finishOrder(Request $request)
    {
        $orderId = $request->input('order_id');
        $pendingItems = Ordertemp::where('order_id', $orderId)->where('order_typ', 'excessitem')->get();

        if ($pendingItems->isEmpty()) {
            return redirect()->back()->with('error', 'No running items found for this order.');
        }

        // Transaction ekak use kalama serama success unoth pamanak save wenawa
        DB::transaction(function () use ($pendingItems, $orderId, $request) {
            foreach ($pendingItems as $item) {
                // 1. CREATE MASTER ORDER RECORD
                $orderm = Orderm::create([
                    'order_id' => $item->order_id,
                    'cus_id' => $item->cus_id,
                    'cus_name' => $item->cus_name,
                    'itm_code' => $item->itm_code,
                    'itm_qty' => $item->itm_qty,
                    'order_typ' => 'excessitem',
                    'order_date' => $item->order_date,
                    'po_date' => $item->order_date,
                    'po_number' => $item->order_id,
                    'sup_id' => '-',
                    'sup_name' => '-',
                    'po_state' => 'excessitem',
                    'itm_rec_date' => $item->order_date,
                    'itm_inv_numer' => '-',
                    'itm_res_qty' => '0',
                    'itm_warranty' => '-',
                    'itm_unit_price' => '0',
                    'itm_tot_price' => '0',
                    'inv_tot_price' => '0',
                    'itm_rec_state' => 'excessitem',
                    'bill_submit_date' => $item->order_date,
                    'bill_number' => $item->order_id,
                    'bill_state' => 'excessitem',
                ]);

                // 2. UPDATE MASTER ITEM STOCK & STATUS
                // lockForUpdate() use karala thiyenne race conditions nathi karanna
                $masterItem = Item::where('itm_code', $item->itm_code)->lockForUpdate()->first();

                if ($masterItem) {
                    // Precise calculation using floatval
                    $receivedQty = floatval($item->itm_qty); // Ordertemp eke thiyena qty eka

                    // Add to Book Stock first
                    $masterItem->itm_book_stock = floatval($masterItem->itm_book_stock) + $receivedQty;

                    // Re-calculate Physical Stock (Physical = Updated Book Stock - Loan Stock)
                    $loanStock = floatval($masterItem->itm_loan_stock ?? 0);
                    $masterItem->itm_stock = $masterItem->itm_book_stock - $loanStock;

                    // Status eka update kirima
                    $masterItem->itm_status = 'ordered';
                    $masterItem->save();
                }

                // 3. LOG TRANSACTION HISTORY
                // Note: $request eken ena additional data (warranty, prices) methanadi use karanna puluwan
                Orderreceived::create([
                    'table_id' => $orderm->id,
                    'order_id' => $orderm->order_id,
                    'cus_id' => $orderm->cus_id,
                    'itm_code' => $orderm->itm_code,
                    'itm_qty' => $orderm->itm_qty,
                    'sup_id' => 'S001',
                    'itm_rec_date' => $orderm->order_date,
                    'itm_res_qty' => '0',
                    'itm_warranty' => '-',
                    'itm_unit_price' => '0',
                    'itm_tot_price' => '0',
                    'itm_rec_state' => 'excessitem',
                    'itm_inv_date' => $orderm->order_date,
                    'itm_inv_numer' => $orderm->order_id,
                    'bill_submit_date' => $orderm->order_date,
                    'bill_number' => $orderm->order_id,
                    'bill_state' => $orderm->order_id,
                ]);
            }

            // Temp table eken delete kirima
            Ordertemp::where('order_id', $orderId)->where('order_typ', 'excessitem')->delete();
        });

        return redirect()->back()->with('success', 'Order successfully finished and stock updated.');
    }
}
