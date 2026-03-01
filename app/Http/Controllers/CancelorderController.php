<?php

namespace App\Http\Controllers;

use App\Models\Orderm;
use App\Models\Item;
use Illuminate\Http\Request;

class CancelorderController extends Controller
{
    protected $orderm;

    public function __construct()
    {
        // Initialize the Orderm model
        $this->orderm = new Orderm();
    }

    /**
     * Display the list of orders that are currently in a 'Pending' state.
     */
    public function showData(Request $request)
    {
        // Retrieve all order records where the receiving state is 'Pending'
        // Results are sorted by Order ID in ascending order
        $orderms = Orderm::where('itm_rec_state', 'Pending')
                         ->orderBy('order_id', 'asc')
                         ->get(); 

        // Pass the retrieved orders to the cancelorder blade view
        return view('pages.cancelorder', compact('orderms'));
    }

    /**
     * Update the status of a specific order to 'Cancel' and reactivate the item.
     * * @param int $id The unique ID of the order record to be updated.
     */
    public function updateData($id)
    {
        // Find the specific order record by its ID or fail with a 404 error if not found
        $orderm = Orderm::findOrFail($id);

        // Update various state fields to mark the order as canceled
        $orderm->order_typ = "Cancel";       // Set order type to Cancel
        $orderm->po_state  = "Cancel";       // Set purchase order state to Cancel
        $orderm->itm_rec_state = "Cancel";   // Set item receiving state to Cancel
        $orderm->bill_state = "Cancel";      // Set billing state to Cancel

        // Save the updated order status to the database
        $orderm->save();

        // Find the corresponding item in the Item table using itm_code
        // and update its status back to 'active' so it can be used again
        Item::where('itm_code', $orderm->itm_code)->update(['itm_status' => 'active']);

        // Redirect back to the data display page with a success message
        return redirect()->route('cancelorder.showData')->with('success', 'Order Item canceled successfully.');
    }
}