<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ordertemp;
use App\Models\Orderm;
use App\Models\Item;
use Validator;

class OrdermController extends Controller
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
     * Display the order management page and handle dynamic Order ID generation.
     */
    public function showData(Request $request)
    {
        // Retrieve the most recent records for different order categories
        $lastAutoOrder = Ordertemp::where('order_typ', 'Auto')->orderBy('order_id', 'desc')->first();
        $lastRunningOrder = Ordertemp::where('order_typ', 'Running')->orderBy('order_id', 'desc')->first();
        $lastCompleteOrder = Orderm::where('order_typ', 'Finish')->orderBy('order_id', 'desc')->first();

        $currentYear = date('Y');
        $neworderId = '';

        // --- ORDER ID GENERATION LOGIC ---

        // CASE 1: If there is an active 'Running' order, keep using that existing ID.
        if ($lastRunningOrder) {
            $neworderId = $lastRunningOrder->order_id;

        // CASE 2: If Auto or Completed orders exist, calculate the next sequence number.
        } elseif ($lastAutoOrder || $lastCompleteOrder) {
            // Use regex to extract the numeric suffix from the last known IDs
            preg_match('/(\d+)$/', $lastAutoOrder->order_id ?? '', $autoMatch);
            preg_match('/(\d+)$/', $lastCompleteOrder->order_id ?? '', $completeMatch);

            $autoNum = isset($autoMatch[1]) ? (int) $autoMatch[1] : 0;
            $completeNum = isset($completeMatch[1]) ? (int) $completeMatch[1] : 0;

            // Pick the highest number among them and increment by 1
            $lastNumber = max($autoNum, $completeNum) + 1;

            // Format the new ID: THA/FP/[Year]/[00X]
            $neworderId = 'THA/FP/' . $currentYear . '/' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

        // CASE 3: If this is the first order of the system/year, start with 001.
        } else {
            $neworderId = 'THA/FP/' . $currentYear . '/001';
        }

        // Fetch items, pending orders, and customer details associated with the current order ID
        $orderms = Ordertemp::with('item')->where('order_id', $neworderId)->get();
        $pendingOrders = Ordertemp::where('order_typ', 'Pending')->select('order_id')->distinct()->orderBy('order_id', 'desc')->get();
        $currentCustomer = Ordertemp::where('order_id', $neworderId)->orderBy('id', 'desc')->select('cus_id', 'cus_name')->first();
        $currentOrderDate = Ordertemp::where('order_id', $neworderId)->orderBy('id', 'asc')->value('order_date');

        return view('pages.orderm', compact('orderms', 'neworderId', 'pendingOrders', 'currentCustomer', 'currentOrderDate'));
    }

    /**
     * Store a single item into the temporary orders table with validation.
     */
    public function tempsaveData(Request $request)
    {
        // Define validation rules for the temporary item
        $rules = [
            'order_id'   => 'required|string|max:20',
            'cus_name'   => 'required|string|max:255',
            'cus_id'     => 'required|string|max:255',
            'itm_code'   => 'required|string|max:15',
            'itm_qty'    => 'required|string|max:255',
            'order_typ'  => 'required|string|max:255',
            'order_date' => 'required|string|max:255',
        ];

        // Custom error messages for the validation
        $messages = [
            'order_id.required' => 'Order ID is required.',
            'itm_code.required' => 'Item Code is required.',
            'itm_qty.required'  => 'Item QTY is required.',
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
            'cus_id'   => 'required|string|max:255',
            'itm_code' => 'required|string|max:15',
            'itm_qty'  => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        $ordertemp = $this->ordertemp->find($id);

        if (!$ordertemp) {
            return redirect('orderm')->with('error', 'Order Item not found.');
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
            return redirect()->back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Transfer all "Running" items to the final Order table and clear temporary records.
     */
    public function finishOrder(Request $request)
    {
        $orderId = $request->input('order_id');

        // Fetch all items currently active (Running) for this specific order
        $pendingItems = Ordertemp::where('order_id', $orderId)->where('order_typ', 'Running')->get();

        if ($pendingItems->isEmpty()) {
            return redirect()->back()->with('error', 'No running items found for this order.');
        }

        foreach ($pendingItems as $item) {
            // Move item to the permanent 'Orderm' table with 'Finish' status
            Orderm::create([
                'order_id'   => $item->order_id,
                'cus_id'     => $item->cus_id,
                'cus_name'   => $item->cus_name,
                'itm_code'   => $item->itm_code,
                'itm_qty'    => $item->itm_qty,
                'order_typ'  => 'Finish',
                'order_date' => $item->order_date,
            ]);

            // Update the master item record status to indicate it is now ordered
            Item::where('itm_code', $item->itm_code)->update(['itm_status' => 'ordered']);
        }

        // Remove the processed items from the temporary table
        Ordertemp::where('order_id', $orderId)->where('order_typ', 'Running')->delete();

        $encodedOrderId = urlencode($orderId);

        return redirect()
            ->back()
            ->with('success', 'Order successfully finished')
            ->with('print_url', route('orderm.orderprint', ['order_id' => $encodedOrderId]));
    }

    /**
     * Mark an entire active order as "Pending" and assign a new sequence ID (e.g., P001).
     */
    public function markPending(Request $request)
    {
        $orderId = $request->input('order_id');
        $orderExists = Ordertemp::where('order_id', $orderId)->exists();

        if (!$orderExists) {
            return redirect()->back()->with('error', 'No order found with this ID.');
        }

        // Generate the next Pending ID (Incrementing P001, P002, etc.)
        $lastPendingOrder = Ordertemp::where('order_typ', 'Pending')->orderBy('order_id', 'desc')->first();

        if ($lastPendingOrder && preg_match('/P(\d+)/', $lastPendingOrder->order_id, $matches)) {
            $nextNumber = str_pad($matches[1] + 1, 3, '0', STR_PAD_LEFT);
            $newPendingOrderId = 'P' . $nextNumber;
        } else {
            $newPendingOrderId = 'P001';
        }

        // Update all associated items to the new Pending ID and Type
        Ordertemp::where('order_id', $orderId)->update([
            'order_typ' => 'Pending',
            'order_id'  => $newPendingOrderId,
        ]);

        return redirect()->back()->with('success', 'Order marked as Pending successfully');
    }

    /**
     * Fetch and view items for a specific pending order.
     */
    public function showPendingOrder($orderId)
    {
        $orderms = Ordertemp::where('order_id', $orderId)->get();
        $neworderId = $orderId;

        // Fetch list of all pending IDs for the sidebar/navigation
        $pendingOrders = Ordertemp::where('order_typ', 'Pending')->select('order_id')->distinct()->orderBy('order_id', 'asc')->get();

        return view('pages.orderm', compact('orderms', 'neworderId', 'pendingOrders'));
    }

    /**
     * Display the print-ready view for a finished order.
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

    /**
     * Convert a Pending order back into an active "Running" order with a new system ID.
     */
    public function loadPendingOrder(Request $request)
    {
        $newRunningOrderId = $request->input('new_running_order_id');
        $pendingOrderId = $request->input('pending_order_slt');

        if (empty($newRunningOrderId) || empty($pendingOrderId)) {
            return redirect()->back()->with('error', 'Missing Order IDs for loading.');
        }

        $orderExists = Ordertemp::where('order_id', $pendingOrderId)->exists();

        if (!$orderExists) {
            return redirect()->back()->with('error', 'No pending order found.');
        }

        // Change the ID from P-series back to the standard THA/FP format and set type to Running
        $updatedRows = Ordertemp::where('order_id', $pendingOrderId)->update([
            'order_id'  => $newRunningOrderId,
            'order_typ' => 'Running',
        ]);

        if ($updatedRows > 0) {
            return redirect()->back()->with('success', 'Order loaded and updated successfully.');
        }

        return redirect()->back()->with('error', 'Could not update order items.');
    }

    /**
     * Convert automatically generated (Auto) items based on reorder levels into active Running items.
     */
    public function loadreorderlevelorder(Request $request)
    {
        $findreorder = Ordertemp::where('order_typ', 'Auto')->exists();

        if (!$findreorder) {
            return redirect()->back()->with('error', 'No Reorder found');
        }

        // Switch all 'Auto' items to 'Running' so they can be processed and finished
        Ordertemp::where('order_typ', 'Auto')->update([
            'order_typ' => 'Running',
        ]);

        return redirect()->back()->with('success', 'Reorder successfully Loaded');
    }
}
