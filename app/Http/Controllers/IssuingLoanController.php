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
            // 1. Find the issuing record first
            $issuing = Issuing::findOrFail($id);

            return DB::transaction(function () use ($issuing) {
                // 2. Find the item and lock it for update
                $item = Item::where('itm_code', $issuing->itm_code)->lockForUpdate()->first();

                if ($item) {
                    // Precision calculation using floatval
                    $qtyToRestore = floatval($issuing->itm_qty);

                    // --- STOCK REVERSAL LOGIC ---
                    // We must update the Book Stock, not just the physical stock field
                    $item->itm_book_stock = floatval($item->itm_book_stock) + $qtyToRestore;

                    // Re-calculate the Physical Stock based on your business logic
                    // Physical Stock = Book Stock - Loan Stock
                    $loanStock = floatval($item->itm_loan_stock ?? 0);
                    $item->itm_stock = $item->itm_book_stock - $loanStock;

                    $item->save();
                }

                // 3. Delete the linked record in Issuingloan table
                Issuingloan::where('issue_table_id', $issuing->id)->delete();

                // 4. Delete the main record in Issuing table
                $issuing->delete();

                return back()->with('success', 'Record deleted and stock updated successfully!');
            });
        } catch (\Exception $e) {
            // Handle any errors that occur during the transaction
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
