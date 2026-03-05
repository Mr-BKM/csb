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
        $issuings = Issuing::with('item')->where('issue_id', $lastAutoOrder->issue_id)->where('issue_typ', 'Running')->get();

        // Extract customer details (ID and Name) for the UI
        $currentCustomer = ['cus_id' => $lastAutoOrder->cus_id, 'cus_name' => $lastAutoOrder->cus_name];

        // Fetch the initial issue date for this specific batch
        $currentIssueDate = Issuing::where('issue_id', $lastAutoOrder->issue_id)->orderBy('id', 'asc')->value('issue_date');

        return view('pages.issuing', [
            'issuings' => $issuings,
            'issueId' => $lastAutoOrder->issue_id,
            'currentCustomer' => $currentCustomer,
            'currentIssueDate' => $currentIssueDate,
        ]);
    }

    /**
     * Temporarily save an item to the issue list and update inventory levels.
     */

    public function tempsaveData(Request $request)
    {
        // Define validation rules for the incoming request
        $rules = [
            'issue_id' => 'required|string|max:20',
            'issue_typ' => 'required|string|max:255',
            'cus_name' => 'required|string|max:255',
            'cus_id' => 'required|string|max:255',
            'itm_code' => 'required|string|exists:items,itm_code', // Validates item exists
            'itm_qty' => 'required|numeric|min:0.01', // Ensures positive decimal value
            'issue_date' => 'required|date', // Ensures a valid date format
        ];

        // Custom error messages for better user experience
        $messages = [
            'itm_code.exists' => 'The selected Item Code does not exist in our inventory.',
            'itm_qty.numeric' => 'The quantity must be a valid number.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // If validation fails, redirect back with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Start a Database Transaction to ensure data integrity
            DB::transaction(function () use ($request) {
                // Fetch the item and lock the row to prevent other users from updating it at the same time
                $item = Item::where('itm_code', $request->itm_code)->lockForUpdate()->first();

                if ($item) {
                    // Convert values to float for accurate mathematical operations
                    $currentBookStock = floatval($item->itm_book_stock);
                    $qtyToDeduct = floatval($request->itm_qty);
                    $loanStock = floatval($item->itm_loan_stock ?? 0);

                    // Check if enough stock is available to fulfill the request
                    if ($currentBookStock < $qtyToDeduct) {
                        throw new \Exception('Insufficient stock! Available Book Stock: ' . $currentBookStock);
                    }

                    // Update Book Stock
                    $item->itm_book_stock = $currentBookStock - $qtyToDeduct;

                    // Re-calculate Physical Stock (Physical = Book Stock - Loan Stock)
                    $item->itm_stock = $item->itm_book_stock - $loanStock;

                    // Save updated values back to the Items table
                    $item->save();

                    // Create a record in the Issuing table for historical tracking
                    $this->issuing->create([
                        'issue_id' => $request->issue_id,
                        'issue_typ' => $request->issue_typ,
                        'cus_name' => $request->cus_name,
                        'cus_id' => $request->cus_id,
                        'itm_code' => $request->itm_code,
                        'itm_stockinhand' => $currentBookStock, // Log the stock level before this deduction
                        'itm_qty' => $qtyToDeduct,
                        'issue_date' => $request->issue_date,
                    ]);
                } else {
                    throw new \Exception('Item record not found in the system.');
                }
            });

            // Redirect on success
            return redirect()->back()->with('success', 'Item issued successfully and stock levels updated.');
        } catch (\Exception $e) {
            // Catch any errors (stock issues or DB errors) and return with the message
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
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
        try {
            DB::transaction(function () use ($id) {
                // 1. Find the specific issue record and lock for deletion
                $issuing = Issuing::lockForUpdate()->find($id);

                if (!$issuing) {
                    throw new \Exception('Order Item not found.');
                }

                // 2. Find the item associated with this record to restore stock
                $item = Item::where('itm_code', $issuing->itm_code)->lockForUpdate()->first();

                if (!$item) {
                    throw new \Exception('Associated Item not found in the inventory.');
                }

                // 3. Convert values to float for precise calculation
                $currentBookStock = floatval($item->itm_book_stock);
                $qtyToRestore = floatval($issuing->itm_qty);
                $loanStock = floatval($item->itm_loan_stock ?? 0);

                // 4. Add the issued quantity back to the Book Stock
                $item->itm_book_stock = $currentBookStock + $qtyToRestore;

                // 5. Re-calculate Physical Stock (Physical = Updated Book Stock - Loan Stock)
                $item->itm_stock = $item->itm_book_stock - $loanStock;

                // Save inventory changes
                $item->save();

                // 6. Permanently remove the issuing record
                $issuing->delete();
            });

            return redirect()->back()->with('success', 'Order Item deleted and stock restored successfully.');
        } catch (\Exception $e) {
            // Return with error message if anything goes wrong
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
