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
        $this->issuing = new Issuing();
    }

    public function showData(Request $request)
    {
        // Get last running issue
        $lastAutoOrder = Issuing::where('issue_typ', 'Running')->orderBy('issue_id', 'desc')->first();

        // If no running issue exists
        if (!$lastAutoOrder) {
            return view('pages.issuing', [
                'issuings' => collect(),
                'issueId' => null,
                'currentCustomer' => null,
                'currentIssueDate' => null,
            ]);
        }

        // Get items for that issue
        $issuings = Issuing::with('item')->where('issue_id', $lastAutoOrder->issue_id)->get();

        // Extract customer info from the running issue
        $currentCustomer = ['cus_id' => $lastAutoOrder->cus_id, 'cus_name' => $lastAutoOrder->cus_name];

        $currentIssueDate = Issuing::where('issue_id', $lastAutoOrder->issue_id)->orderBy('id', 'asc')->value('issue_date');

        // $currentIssueDate = Issuing::where('issue_id', $request->order_id)->orderBy('id', 'asc')->value('issue_date');

        return view('pages.issuing', ['issuings' => $issuings, 'issueId' => $lastAutoOrder->issue_id, 'currentCustomer' => $currentCustomer, 'currentIssueDate' => $currentIssueDate]);
    }

    public function tempsaveData(Request $request)
    {
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

        $messages = [
            'issue_id.required' => 'Issue Number is required.',
            'issue_id.string' => 'Issue Number must be a string.',
            'issue_id.max' => 'Issue Number must not exceed 20 characters.',

            'issue_typ.required' => 'Issue Type is required.',
            'issue_typ.string' => 'Issue Type must be a string.',
            'issue_typ.max' => 'Issue Type must not exceed 225 characters.',

            'cus_name.required' => 'Customer Name is required.',
            'cus_name.string' => 'Customer Name must be a string.',
            'cus_name.max' => 'Customer Name must not exceed 255 characters.',

            'cus_id.required' => 'Customer ID is required.',
            'cus_id.string' => 'Customer ID must be a string.',
            'cus_id.max' => 'Customer ID must not exceed 255 characters.',

            'itm_code.required' => 'Item Code is required.',
            'itm_code.string' => 'Item Code  must be a string.',
            'itm_code.max' => 'Item Code must not exceed 15 characters.',

            'itm_qty.required' => 'Item QTY is required.',
            'itm_qty.string' => 'Item QTY must be a string.',
            'itm_qty.max' => 'Item QTY must not exceed 255 characters.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
            // 🔎 Find item by itm_code (and lock it for update)
            $item = Item::where('itm_code', $request->itm_code)->lockForUpdate()->first();

            if ($item) {
                // 1. Book Stock එකෙන් Issue කරන ප්‍රමාණය අඩු කරනවා
                // itm_book_stock = itm_book_stock - itm_qty
                $item->itm_book_stock = $item->itm_book_stock - $request->itm_qty;

                // 2. Physical Stock (itm_stock) එක calculate කරනවා
                // itm_stock = itm_book_stock - itm_loan_stock
                // මෙතනදී උඩ line එකේ update වුණ අලුත් book_stock එක තමයි පාවිච්චි වෙන්නේ
                $item->itm_stock = $item->itm_book_stock - ($item->itm_loan_stock ?? 0);

                // 💾 Item table එක save කරනවා
                $item->save();

                // ➕ Save issuing record
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

    public function finishOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $orderId = $request->order_id;

        DB::transaction(function () use ($orderId) {
            // 1. Update Issuing table
            Issuing::where('issue_id', $orderId)->update([
                'issue_typ' => 'Issued',
            ]);

            // 2. Update Issuingloan table (Loan ekak widiyata kalin record wela thibuna nam)
            // Meken loan table eketh status eka update wenawa
            Issuingloan::where('issue_id', $orderId)->update([
                'issue_typ' => 'Issued',
            ]);
        });

        return redirect()->route('issuing.showData')->with('success', 'Order status updated to Issued successfully.');
    }

    public function markLoan(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        // 1. Get the items first
        $items = Issuing::where('issue_id', $request->order_id)->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'No items found for this Order ID.');
        }

        DB::transaction(function () use ($items, $request) {
            // 2. Update the original table
            Issuing::where('issue_id', $request->order_id)->update([
                'issue_id' => 'Loan',
                'issue_typ' => 'Loan',
            ]);

            // 3. Loop and insert into the issuingloan table
            foreach ($items as $item) {
                Issuingloan::create([
                    'issue_table_id' => $item->id, // Issuing table eke ID eka methanata yanawa
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

    public function deleteData($id)
    {
        DB::transaction(function () use ($id) {
            // 🔎 Find issuing record
            $issuing = Issuing::lockForUpdate()->find($id);

            if (!$issuing) {
                throw new \Exception('Order Item not found.');
            }

            // 🔎 Find related item
            $item = Item::where('itm_code', $issuing->itm_code)->lockForUpdate()->first();

            if (!$item) {
                throw new \Exception('Item not found.');
            }

            // 1. ➕ ADD BACK issued quantity to BOOK STOCK
            // Issue එක delete කරන නිසා book stock එක වැඩි වෙනවා
            $item->itm_book_stock = $item->itm_book_stock + $issuing->itm_qty;

            // 2. 🔄 RE-CALCULATE Physical Stock
            // itm_stock = (අලුත් book_stock) - itm_loan_stock
            $item->itm_stock = $item->itm_book_stock - ($item->itm_loan_stock ?? 0);

            // 💾 Save the updated item
            $item->save();

            // ❌ Delete issuing record
            $issuing->delete();
        });

        return redirect()->back()->with('success', 'Order Item deleted and stock restored successfully.');
    }
}
