<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issuing;

class IssuingbookupdateController extends Controller
{
    /**
     * Display a list of items that have been issued but not yet updated in the books.
     */
    public function showData()
    {
        // Fetch all issuing records where the status is 'Issued'
        // Results are ordered by the Issue ID in ascending order
        $issuings = Issuing::where('issue_typ', 'Issued')->orderBy('issue_id', 'asc')->get(); 

        // Return the view and pass the issuing data to it
        return view('pages.issuingbookupdate', compact('issuings'));
    }

    /**
     * Update the status of multiple issuing records to indicate that the books have been updated.
     */
    public function finish(Request $request)
    {
        // Validate that 'ids' is provided in the request and that it is an array
        $data = $request->validate([
            'ids' => 'required|array',
        ]);

        // Update the 'issue_typ' status to 'Book Updated' for all selected record IDs
        Issuing::whereIn('id', $data['ids'])->update([
            'issue_typ' => 'Book Updated',
        ]);

        // Redirect back to the previous page with a success message
        return back()->with('success', 'Item quantity split successfully.');
    }
}