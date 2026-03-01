<?php

namespace App\Http\Controllers;

use App\Models\Orderreceived;
use Illuminate\Http\Request;

class ItemreceivededitController extends Controller
{
    /**
     * Fetch and display all items that have been marked as 'Item Received'.
     * This view allows users to add invoice details to received items.
     */
    public function showData()
    {
        // Retrieve received records where invoice details are yet to be finalized
        $orderreceiveds = Orderreceived::where('itm_rec_state', 'Item Received')
                                       ->orderBy('order_id', 'asc')
                                       ->get();

        return view('pages.itemreceivededit', compact('orderreceiveds'));
    }

    /**
     * Finalize the received items by adding Invoice Date and Invoice Number.
     * This marks the items as 'Added' and ready for the next stage (billing/audit).
     */
    public function finish(Request $request)
    {
        // Validate the incoming invoice information
        $data = $request->validate([
            'ids'           => 'required|array',  // Array of selected record IDs from the table
            'itm_inv_date'  => 'required|date',   // Invoice Date
            'itm_inv_numer' => 'required|string', // Invoice Number (typo 'numer' matches your DB column)
        ]);

        // Bulk update the selected records with invoice details and update the state to 'Added'
        Orderreceived::whereIn('id', $data['ids'])->update([
            'itm_inv_date'  => $data['itm_inv_date'],
            'itm_inv_numer' => $data['itm_inv_numer'],
            'itm_rec_state' => 'Added',
        ]);

        // Redirect back to the list with a success message
        return redirect()->route('itemreceivededit.showData')
                         ->with('success', 'Selected Order items "Confirmed" successfully!');
    }
}
