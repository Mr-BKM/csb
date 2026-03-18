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
        // 1. Issuing records ටික ගන්නවා අදාල qty සහ item code බලාගන්න
        $itemsToSettle = Issuing::whereIn('id', $data['ids'])->get();

        foreach ($itemsToSettle as $issuingRecord) {
            // 2. Item Master එක Lock කරලා ගන්නවා
            $item = Item::where('itm_code', $issuingRecord->itm_code)->lockForUpdate()->first();

            if ($item) {
                $qty = floatval($issuingRecord->itm_qty);

                // --- OYA ILLAPU LOGIC EKA ---
                
                // 3. Book Stock එකෙන් අඩු කරනවා (මොකද මේක දැන් විකුණල ඉවරයි)
                $item->itm_book_stock = floatval($item->itm_book_stock) - $qty;

                // 4. Loan Stock එකෙනුත් අඩු කරනවා (මොකද මේක දැන් තවදුරටත් Loan එකක් නෙවෙයි)
                $item->itm_loan_stock = floatval($item->itm_loan_stock ?? 0) - $qty;

                // 5. Physical Stock එක update කරනවා
                // Equation: Physical = Book - Loan
                // Example: (100 - 10) = 90 thibba eka, Dan (90 - 0) = 90 wenawa.
                $item->itm_stock = $item->itm_book_stock - $item->itm_loan_stock;

                $item->save();
            }
        }

        // 6. Tables update කරනවා
        Issuing::whereIn('id', $data['ids'])->update([
            'issue_date' => $data['issue_date'],
            'issue_id' => $data['issue_id'],
            'issue_typ' => 'Issued',
        ]);

        Issuingloan::whereIn('issue_table_id', $data['ids'])->update([
            'issue_date' => $data['issue_date'],
            'issue_id' => $data['issue_id'],
            'issue_typ' => 'Issued',
        ]);
    });

    return back()->with('success', 'Loan settled and Stock updated successfully!');
}
    public function update(Request $request)
    {
        // 1. Validation with English comments
        $request->validate([
            'id' => 'required|exists:issuings,id',
            'settled_qty' => 'required|numeric|min:0.01', // Changed to numeric for decimal support
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Find the original issuing record and lock it
                $issuing = Issuing::lockForUpdate()->findOrFail($request->id);

                // Precision calculation using floatval
                $originalQty = floatval($issuing->itm_qty);
                $settledQty = floatval($request->settled_qty);
                $remainingQty = $originalQty - $settledQty;

                // Security check: cannot settle more than what was issued
                if ($settledQty >= $originalQty) {
                    throw new \Exception('Settled quantity must be less than the original issued quantity.');
                }

                // --- 1. UPDATE EXISTING RECORDS (The Settled Part) ---

                // Update the existing Issuing record
                $issuing->update(['itm_qty' => $settledQty]);

                // Find matching loan record and update it
                $loanRecord = Issuingloan::where('issue_table_id', $issuing->id)->lockForUpdate()->first();

                if ($loanRecord) {
                    $loanRecord->update(['itm_qty' => $settledQty]);
                }

                // --- 2. CREATE NEW RECORDS (The Remaining Part) ---

                // Create a new row in Issuing table for the remaining balance
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

                // Create a new row in Issuingloan table if a loan record exists
                if ($loanRecord) {
                    Issuingloan::create([
                        'issue_table_id' => $newIssuing->id, // Linked to the new issuing record ID
                        'issue_id' => $loanRecord->issue_id,
                        'cus_id' => $loanRecord->cus_id,
                        'itm_code' => $loanRecord->itm_code,
                        'itm_stockinhand' => $loanRecord->itm_stockinhand,
                        'itm_qty' => $remainingQty,
                        'issue_date' => $loanRecord->issue_date,
                        'issue_typ' => $loanRecord->issue_typ,
                    ]);
                }

                return redirect()->back()->with('success', 'Item quantity split successfully in both tables.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

public function delete($id)
{
    try {
        $issuing = Issuing::findOrFail($id);

        return DB::transaction(function () use ($issuing) {
            $item = Item::where('itm_code', $issuing->itm_code)->lockForUpdate()->first();

            if ($item) {
                $qtyToRestore = floatval($issuing->itm_qty);

                // --- LOAN DELETE LOGIC ---

                // 1. 
                $item->itm_loan_stock = floatval($item->itm_loan_stock ?? 0) - $qtyToRestore;

                // 2. Book Stock 

                // 3. Physical Stock re-calculate.
                // Physical = Book Stock - Loan Stock
                $item->itm_stock = floatval($item->itm_book_stock) - $item->itm_loan_stock;

                $item->save();
            }

            // 3. Delete records
            Issuingloan::where('issue_table_id', $issuing->id)->delete();
            $issuing->delete();

            return back()->with('success', 'Loan cancelled and Physical stock restored!');
        });
    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}
