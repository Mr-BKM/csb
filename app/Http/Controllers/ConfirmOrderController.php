<?php

namespace App\Http\Controllers;

use App\Models\Orderm;
use App\Models\Item;
use Illuminate\Http\Request;

class ConfirmOrderController extends Controller
{
    /**
     * Fetch and display orders that are currently in a 'Pending' state.
     */
    public function showData()
    {
        // Retrieve all orders where po_state is 'Pending', ordered by order_id ascending
        $orderms = Orderm::where('po_state', 'Pending')->orderBy('order_id', 'asc')->get();

        // Pass the retrieved orders to the confirmorder view
        return view('pages.confirmorder', compact('orderms'));
    }

    /**
     * Process the confirmation of selected orders and update item statuses.
     */
    public function finish(Request $request)
    {
        // Validate the incoming request data to ensure all required fields are present
        $data = $request->validate([
            'ids' => 'required|array', // Array of selected order IDs
            'po_date' => 'required|date', // Purchase Order date
            'po_number' => 'required|string', // Purchase Order reference number
            'sup_id' => 'required|string', // Supplier ID
            'sup_name' => 'required|string', // Supplier Name
        ]);

        // Bulk update the selected orders: assign PO details and change state to 'Added'
        Orderm::whereIn('id', $data['ids'])->update([
            'po_date' => $data['po_date'],
            'po_number' => $data['po_number'],
            'sup_id' => $data['sup_id'],
            'sup_name' => $data['sup_name'],
            'po_state' => 'Added',
        ]);

        // Retrieve the specific item codes (itm_code) associated with the confirmed orders
        $itemCodes = Orderm::whereIn('id', $data['ids'])->pluck('itm_code');

        // Update the status of these items to 'active' in the Items table
        Item::whereIn('itm_code', $itemCodes)->update(['itm_status' => 'active']);

        // Redirect back to the order list with a success message
        return redirect()->route('confirmorder.showData')->with('success', 'Selected Order items "Confirmed" successfully!');
    }
}
