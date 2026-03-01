<?php

namespace App\Http\Controllers;

use App\Models\Issuingloan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Issuing;
use App\Models\Item;
use Validator;

class IssuingController extends Controller
{
    protected $issuing;

    public function __construct()
    {
        // Initialize the Issuing model
        $this->issuing = new Issuing();
    }

    /**
     * Display the current running issue data on the issuing page.
     */
    public function showData(Request $request)
    {
        // Retrieve the most recent issue that is currently in 'Running' state
        $lastAutoOrder = Issuing::where('issue_typ', 'Running')->orderBy('issue_id', 'desc')->first();

        // If no active running issue is found, return empty collections to the view
        if (!$lastAutoOrder) {
            return view('pages.issuing', [
                'issuings' => collect(),
                'issueId' => null,
                'currentCustomer' => null,
                'currentIssueDate' => null,
            ]);
        }

        /**
         * UPDATED: Get only items for that specific issue_id that are still marked as 'Running'.
         */
        $issuings = Issuing::with('item')
            ->where('issue_id', $lastAutoOrder->issue_id)
            ->where('issue_typ', 'Running') 
            ->get();

        // Extract customer details (ID and Name) for the UI
        $currentCustomer = ['cus_id' => $lastAutoOrder->cus_id, 'cus_name' => $lastAutoOrder->cus_name];

        // Fetch the initial issue date for this specific batch
        $currentIssueDate = Issuing::where('issue_id', $lastAutoOrder->issue_id)->orderBy('id', 'asc')->value('issue_date');

        return view('pages.issuing', [
            'issuings' => $issuings, 
            'issueId' => $lastAutoOrder->issue_id, 
            'currentCustomer' => $currentCustomer, 
            'currentIssueDate' => $currentIssueDate
        ]);
    }

    /**
     * Temporarily save an item to the issue list and update inventory levels.
     */
    public function tempsaveData(Request $request)
    {
        // Validation rules for the incoming request
        $rules = [
            'issue_id' => 'required|string|max:20',
            'issue_typ' => 'required|string|max:255',
            'cus_name' => 'required|string|max:255',
            'cus_id' => 'required|string|max:255',
            'itm_code' => 'required|string|max:15',
            'itm_stockinhand' => 'required|string|max:255',
            'itm_qty' => 'required|string|max:255',
            'issue_date' => 'required|string|max:255',
        ];

        // Custom error messages for validation
        $messages = [
            'issue_id.required' => 'Issue Number is required.',
            'itm_code.required' => 'Item Code is required.',
            'itm_qty.required' => 'Item QTY is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Use a Database Transaction to ensure data integrity during stock updates
        DB::transaction(function () use ($request) {
            // Find the item by its code and lock the row to prevent concurrent update issues
            $item = Item::where('itm_code', $request->itm_code)->lockForUpdate()->first();

            if ($item) {
                // 1. Deduct the issued quantity from the 'Book Stock'
                $item->itm_book_stock = $item->itm_book_stock - $request->itm_qty;

                // 2. Re-calculate 'Physical Stock'
                // Physical Stock = Updated Book Stock - Loan Stock
                $item->itm_stock = $item->itm_book_stock - ($item->itm_loan_stock ?? 0);

                // Save updated inventory values
                $item->save();

                // Create the record in the Issuing table
                $this->issuing->create([
                    'issue_id' => $request->issue_id,
                    'issue_typ' => $request->issue_typ,
                    'cus_name' => $request->cus_name,
                    'cus_id' => $request->cus_id,
                    'itm_code' => $request->itm_code,
                    'itm_stockinhand' => $request->itm_stockinhand,
                    'itm_qty' => $request->itm_qty,
                    'issue_date' => $request->issue_date,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Item issued successfully and stock updated');
    }

    /**
     * Finalize the order by changing the status from 'Running' to 'Issued'.
     */
    public function finishOrder(Request $request)
    {
        $request->validate(['order_id' => 'required|string']);

        $orderId = $request->order_id;

        DB::transaction(function () use ($orderId) {
            // 1. Mark all items in this issue as officially 'Issued'
            Issuing::where('issue_id', $orderId)->update(['issue_typ' => 'Issued']);

            // 2. Also update related loan records if they exist
            Issuingloan::where('issue_id', $orderId)->update(['issue_typ' => 'Issued']);
        });

        return redirect()->route('issuing.showData')->with('success', 'Order status updated to Issued successfully.');
    }

    /**
     * Mark an entire running order as a 'Loan'.
     */
    public function markLoan(Request $request)
    {
        $request->validate(['order_id' => 'required|string']);

        // Fetch all items currently under this Order ID
        $items = Issuing::where('issue_id', $request->order_id)->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'No items found for this Order ID.');
        }

        DB::transaction(function () use ($items, $request) {
            // 1. Update the original Issuing table records to 'Loan' status
            Issuing::where('issue_id', $request->order_id)->update([
                'issue_id' => 'Loan',
                'issue_typ' => 'Loan',
            ]);

            // 2. Duplicate these records into the Issuingloan table for tracking
            foreach ($items as $item) {
                Issuingloan::create([
                    'issue_table_id' => $item->id, // Reference to the original Issuing ID
                    'issue_id' => 'Loan',
                    'cus_id' => $item->cus_id,
                    'itm_code' => $item->itm_code,
                    'itm_stockinhand' => $item->itm_stockinhand,
                    'itm_qty' => $item->itm_qty,
                    'issue_date' => $item->issue_date,
                    'issue_typ' => 'Loan',
                ]);
            }
        });

        return redirect()->route('issuing.showData')->with('success', 'Order marked as Loan and recorded successfully.');
    }

    /**
     * Delete an issued item and restore the stock quantities.
     */
    public function deleteData($id)
    {
        DB::transaction(function () use ($id) {
            // Find the specific issue record and lock for deletion
            $issuing = Issuing::lockForUpdate()->find($id);

            if (!$issuing) {
                throw new \Exception('Order Item not found.');
            }

            // Find the item associated with this record to restore stock
            $item = Item::where('itm_code', $issuing->itm_code)->lockForUpdate()->first();

            if (!$item) {
                throw new \Exception('Item not found.');
            }

            // 1. Add the issued quantity back to the Book Stock
            $item->itm_book_stock = $item->itm_book_stock + $issuing->itm_qty;

            // 2. Re-calculate the Physical Stock based on the restored Book Stock
            $item->itm_stock = $item->itm_book_stock - ($item->itm_loan_stock ?? 0);

            // Save inventory changes
            $item->save();

            // Permanently remove the issuing record
            $issuing->delete();
        });

        return redirect()->back()->with('success', 'Order Item deleted and stock restored successfully.');
    }
}