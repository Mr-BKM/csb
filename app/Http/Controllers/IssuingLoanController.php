<?php

namespace App\Http\Controllers;

use App\Models\Issuingloan;
use Illuminate\Support\Facades\DB;
use App\Models\Issuing;
use App\Models\Item;
use Illuminate\Http\Request;

class IssuingLoanController extends Controller
{
    public function showData()
    {
        $issuings = Issuing::where('issue_typ', 'Loan')->orderBy('issue_id', 'asc')->get(); // no paginate()

        return view('pages.issuingloan', compact('issuings'));
    }

    public function finish(Request $request) 
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'issue_date' => 'required|date',
            'issue_id' => 'required|string',
        ]);

        DB::transaction(function () use ($data) {
            // 1. Issuing table update
            Issuing::whereIn('id', $data['ids'])->update([
                'issue_date' => $data['issue_date'],
                'issue_id' => $data['issue_id'],
                'issue_typ' => 'Issued',
            ]);

            // 2. Issuingloan table update
            // Poddak balanna migration eke 'issue_table_id' kiyana nama hariyatama liyala thiyeda kiyala
            Issuingloan::whereIn('issue_table_id', $data['ids'])->update([
                'issue_date' => $data['issue_date'],
                'issue_id' => $data['issue_id'],
                'issue_typ' => 'Issued',
            ]);
        });

        return back()->with('success', 'Tables dekama update una!');
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:issuings,id',
            'settled_qty' => 'required|integer|min:1',
        ]);

        $issuing = Issuing::findOrFail($request->id);

        $originalQty = (int) $issuing->itm_qty;
        $settledQty = (int) $request->settled_qty;
        $remainingQty = $originalQty - $settledQty;

        if ($settledQty >= $originalQty) {
            return back()->withErrors('Settled Item Quantity must be less than Item Quantity.');
        }

        DB::transaction(function () use ($issuing, $settledQty, $remainingQty) {
            // --- 1. UPDATE EXISTING RECORDS (The Settled Part) ---

            // Issuing table eka update karanawa
            $issuing->update(['itm_qty' => $settledQty]);

            // Issuingloan table eke match wena record eka hoyagannawa (using issue_table_id)
            $loanRecord = Issuingloan::where('issue_table_id', $issuing->id)->first();

            if ($loanRecord) {
                $loanRecord->update(['itm_qty' => $settledQty]);
            }

            // --- 2. CREATE NEW RECORDS (The Remaining Part) ---

            // Issuing table eke aluth row ekak hadanawa
            $newIssuing = Issuing::create([
                'issue_id' => $issuing->issue_id,
                'issue_typ' => $issuing->issue_typ,
                'cus_name' => $issuing->cus_name,
                'cus_id' => $issuing->cus_id,
                'itm_code' => $issuing->itm_code,
                'itm_stockinhand' => $issuing->itm_stockinhand,
                'itm_qty' => $remainingQty,
                'issue_date' => $issuing->issue_date,
            ]);

            // Issuingloan table eketh aluth row ekak hadanawa (Remaining part ekata)
            // Methana 'issue_table_id' ekata yanne uda hadapu $newIssuing->id eka
            if ($loanRecord) {
                Issuingloan::create([
                    'issue_table_id' => $newIssuing->id, // Aluth row ID eka connection ekata
                    'issue_id' => $loanRecord->issue_id,
                    'cus_id' => $loanRecord->cus_id,
                    'itm_code' => $loanRecord->itm_code,
                    'itm_stockinhand' => $loanRecord->itm_stockinhand,
                    'itm_qty' => $remainingQty,
                    'issue_date' => $loanRecord->issue_date,
                    'issue_typ' => $loanRecord->issue_typ,
                ]);
            }
        });

        return back()->with('success', 'Item quantity split successfully in both tables.');
    }

    public function delete($id)
{
    // 1. Record eka thiyeda kiyala check karagannawa
    $issuing = Issuing::findOrFail($id);

    DB::transaction(function () use ($issuing) {

        // 2. Stock eka reverse kirima (Table 3)
        // 'itm_code' eken item eka hoyala quantity eka ayeth ekathu karanawa
        $item = Item::where('itm_code', $issuing->itm_code)->first();
        if ($item) {
            $item->increment('itm_stock', $issuing->itm_qty);
        }

        // 3. Issuingloan table eken delete kirima (Table 2)
        Issuingloan::where('issue_table_id', $issuing->id)->delete();

        // 4. Issuing table eken delete kirima (Table 1)
        $issuing->delete();
    });

    return back()->with('success', 'Record deleted and stock updated successfully!');
}
}
