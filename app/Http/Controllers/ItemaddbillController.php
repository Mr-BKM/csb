<?php

namespace App\Http\Controllers;

use App\Models\Orderreceived;
use Illuminate\Http\Request;

class ItemaddbillController extends Controller
{
    /**
     * Fetch and display orders that are ready to be billed.
     */
    public function showData()
    {
        // Retrieve records where item state is 'Added' and bill state is 'Pending'
        // Ordered by 'order_id' in ascending order.
        $orderreceiveds = Orderreceived::where('itm_rec_state', 'Added')
                                        ->where('bill_state', 'Pending')
                                        ->orderBy('order_id', 'asc')
                                        ->get();

        // Return the view and pass the retrieved data using compact.
        return view('pages.itemaddbill', compact('orderreceiveds'));
    }

    /**
     * Update the billing information for the selected orders.
     */
    public function finish(Request $request)
    {
        // Validate the incoming request data to ensure all fields are present and correct.
        $data = $request->validate([
            'ids' => 'required|array',          // Expects an array of record IDs.
            'bill_submit_date' => 'required|date',
            'bill_number' => 'required|string',
        ]);

        // Bulk update the records matching the provided IDs.
        Orderreceived::whereIn('id', $data['ids'])->update([
            'bill_submit_date' => $data['bill_submit_date'],
            'bill_number' => $data['bill_number'],
            'bill_state' => 'Added', // Change billing state to 'Added' after processing.
        ]);

        // Redirect back to the data list with a success notification.
        return redirect()->route('itemaddbill.showData')->with('success', 'Selected Order items "Confirmed" successfully!');
    }
}