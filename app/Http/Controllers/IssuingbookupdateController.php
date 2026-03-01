<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issuing;

class IssuingbookupdateController extends Controller
{
    public function showData()
    {
        $issuings = Issuing::where('issue_typ', 'Issued')->orderBy('issue_id', 'asc')->get(); // no paginate()

        return view('pages.issuingbookupdate', compact('issuings'));
    }

    public function finish(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
        ]);

        Issuing::whereIn('id', $data['ids'])->update([
            'issue_typ' => 'Book Updated',
        ]);

        return back()->with('success', 'Item quantity split successfully.');
    }
}
