<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orderm;
use App\Models\Item;
use App\Models\Ordertemp;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Issuing;
use Validator;

class OrderaController extends Controller
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
    public function showData(Request $request)
    {
        // Step 1: Retrieve the latest order records for each status category
        $lastAutoOrder = Ordertemp::where('order_typ', 'Auto')->orderBy('order_id', 'desc')->first();
        $lastRunningOrder = Ordertemp::where('order_typ', 'Running')->orderBy('order_id', 'desc')->first();
        $lastCompleteOrder = Orderm::where('order_typ', 'Finish')->orderBy('order_id', 'desc')->first();

        $currentYear = date('Y');
        $neworderId = '';
        $disableSubmit = false;

        // --- DYNAMIC ID GENERATION LOGIC ---

        // CASE 1: If an 'Auto' generated order already exists, reuse its ID and lock further generation
        if ($lastAutoOrder) {
            $neworderId = $lastAutoOrder->order_id;
            $disableSubmit = true;

        // CASE 2: If 'Running' or 'Finished' orders exist, calculate the next number in sequence
        } elseif ($lastRunningOrder || $lastCompleteOrder) {
            // Safely extract numeric suffix from existing order IDs (e.g., extracts 005 from THA/FP/2026/005)
            preg_match('/(\d+)$/', $lastRunningOrder->order_id ?? '', $runMatch);
            preg_match('/(\d+)$/', $lastCompleteOrder->order_id ?? '', $compMatch);

            $runNum = isset($runMatch[1]) ? (int) $runMatch[1] : 0;
            $compNum = isset($compMatch[1]) ? (int) $compMatch[1] : 0;

            // Increment the highest found sequence number
            $lastNumber = max($runNum, $compNum) + 1;

            $neworderId = 'THA/FP/' . $currentYear . '/' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);
            $disableSubmit = false;

        // CASE 3: Final Fallback - If no records exist, start at 001
        } else {
            $neworderId = 'THA/FP/' . $currentYear . '/001';
            $disableSubmit = false;
        }

        // Step 2: Fetch all current order items that were automatically generated
        $orderas = Ordertemp::with('item')->where('order_typ', 'Auto')->latest()->get();

        // Step 3: Pass data to the view
        return view('pages.ordera', compact('orderas', 'neworderId', 'disableSubmit'));
    }

    /**
     * Process Book Codes to identify items hitting reorder levels
     * and calculate forecasted quantities based on current year usage.
     */
    public function processBookCode(Request $request)
    {
        // Validate input book code and target order ID
        $request->validate([
            'itm_book_code' => 'required|string',
            'order_id'      => 'required|string',
        ]);

        $bookCode = $request->input('itm_book_code');
        $orderId = $request->input('order_id');

        // 1. Calculate how many days have passed in the current year (e.g., Feb 28 = 59 days)
        $daysPassed = Carbon::now()->dayOfYear;

        // Fetch items matching criteria: correct book code, reorder flag enabled, and stock below reorder level
        $items = Item::where('itm_book_code', $bookCode)
                     ->where('itm_reorder_flag', 'Yes')
                     ->where('itm_status', 'active')
                     ->whereColumn('itm_reorder_level', '>=', 'itm_stock')
                     ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No items found that match reorder condition.');
        }

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // 2. Query 'Issuing' table for total quantity issued for this item within the current year
                $totalIssuedQty = Issuing::where('itm_code', $item->itm_code)
                                         ->whereYear('issue_date', date('Y'))
                                         ->sum('itm_qty');

                // 3. FORECAST CALCULATION: (Actual Usage / Days Gone) * 365 Days
                // Provides an estimated annual requirement based on real-time consumption
                $forecastedQty = $daysPassed > 0 ? ($totalIssuedQty / $daysPassed) * 365 : 0;

                // Round value to nearest whole number
                $finalOrderQty = round($forecastedQty);

                // Create temporary order record for each matching item
                Ordertemp::create([
                    'order_id'   => $orderId,
                    'cus_name'   => 'Consumables Stores - B', // Pre-defined customer
                    'cus_id'     => 'C001',
                    'itm_code'   => $item->itm_code,
                    'itm_qty'    => $finalOrderQty,
                    'order_typ'  => 'Auto',
                    'order_date' => now(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Forecasted items loaded to Ordertemp successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Delete all temporary items belonging to a specific Order ID.
     */
    public function alldeleteData($order_id)
    {
        try {
            $deletedCount = Ordertemp::where('order_id', $order_id)->delete();

            if ($deletedCount > 0) {
                return redirect()->route('ordera.showData')->with('success', 'All Auto Orders deleted successfully.');
            } else {
                return redirect()->route('ordera.showData')->with('error', 'No orders found to delete.');
            }
        } catch (\Exception $e) {
            return redirect()->route('ordera.showData')->with('error', 'Error deleting orders: ' . $e->getMessage());
        }
    }

    /**
     * Update the quantity of a specific auto-generated order item.
     */
    public function updateData(Request $request, $id)
    {
        $rules = [
            'itm_qty' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        $ordertemp = $this->ordertemp->find($id);

        if (!$ordertemp) {
            return redirect('ordera')->with('error', 'Order Item not found.');
        }

        // Apply updated quantity to the record
        $ordertemp->update($validator->validated());

        return redirect()->back()->with('success', 'Order Item updated successfully.');
    }

    /**
     * Delete a single record from the temporary order list.
     */
    public function deleteData($id)
    {
        try {
            $ordertemp = Ordertemp::find($id);
            if (!$ordertemp) return redirect()->back()->with('error', 'Order Item not found.');

            $ordertemp->delete();
            return redirect()->back()->with('success', 'Order Item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Finalize the auto-order: Transfer items to final 'Orderm' table and update Item master status.
     */
    public function finishOrder(Request $request)
    {
        $orderId = $request->input('order_id');

        // Retrieve items categorized as 'Auto' for the current ID
        $pendingItems = Ordertemp::where('order_id', $orderId)->where('order_typ', 'Auto')->get();

        if ($pendingItems->isEmpty()) {
            return redirect()->back()->with('error', 'No auto items found for this order.');
        }

        foreach ($pendingItems as $item) {
            // Save to permanent table with 'Finish' status
            Orderm::create([
                'order_id'   => $item->order_id,
                'cus_id'     => $item->cus_id,
                'cus_name'   => $item->cus_name,
                'itm_code'   => $item->itm_code,
                'itm_qty'    => $item->itm_qty,
                'order_typ'  => 'Finish',
                'order_date' => $item->order_date,
            ]);

            // Mark the item in the master catalog as 'ordered'
            Item::where('itm_code', $item->itm_code)->update(['itm_status' => 'ordered']);
        }

        // Clear temporary records after successful transfer
        Ordertemp::where('order_id', $orderId)->where('order_typ', 'Auto')->delete();

        $encodedOrderId = urlencode($orderId);

        return redirect()
            ->back()
            ->with('success', 'Order successfully finished')
            ->with('print_url', route('ordera.orderprint', ['order_id' => $encodedOrderId]));
    }

    /**
     * Retrieve finished order details for a print-friendly view.
     */
    public function printView($order_id)
    {
        $orderDetails = Orderm::with('item')->where('order_id', $order_id)->get();

        if ($orderDetails->isEmpty()) {
            return redirect()->back()->with('error', 'No order found for printing.');
        }

        $orderDate = $orderDetails->first()->order_date ?? null;

        return view('pages.orderprint', compact('orderDetails', 'order_id', 'orderDate'));
    }
}
