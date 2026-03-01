<?php

namespace App\Http\Controllers;
use App\Models\Orderreceived;

use Illuminate\Http\Request;

class ItemaddbillController extends Controller
{
    public function showData()
    {
        $orderreceiveds = Orderreceived::where('itm_rec_state', 'Added')->where('bill_state', 'Pending')->orderBy('order_id', 'asc')->get();
        return view('pages.itemaddbill', compact('orderreceiveds'));
    }

    public function finish(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'bill_submit_date' => 'required|date',
            'bill_number' => 'required|string',
        ]);

        Orderreceived::whereIn('id', $data['ids'])->update([
            'bill_submit_date' => $data['bill_submit_date'],
            'bill_number' => $data['bill_number'],
            'bill_state' => 'Added',
        ]);

        // Get related item codes from the order table
        // $itemCodes = orderreceived::whereIn('id', $data['ids'])->pluck('itm_code');

        // Update items status
        // Item::whereIn('itm_code', $itemCodes)->update(['itm_status' => 'active']);

        // return response()->json(['success' => true]);
        return redirect()->route('itemaddbill.showData')->with('success', 'Selected Order items "Confirmed" successfully!');
    }
}

// <?php

// namespace App\Http\Controllers;
// use App\Models\orderreceived;

// use Illuminate\Http\Request;

// class ItemreceivededitController extends Controller
// {
    // public function showData()
    // {
    //     $orderreceiveds = orderreceived::where('itm_rec_state', 'Item Received')->orderBy('order_id', 'asc')->get();
    //     return view('pages.itemreceivededit', compact('orderreceiveds'));
    // }

    //     public function finish(Request $request)
    // {
    //     $data = $request->validate([
    //         'ids' => 'required|array',
    //         'itm_inv_date' => 'required|date',
    //         'itm_inv_numer' => 'required|string',
    //     ]);

    //     orderreceived::whereIn('id', $data['ids'])->update([
    //         'itm_inv_date' => $data['itm_inv_date'],
    //         'itm_inv_numer' => $data['itm_inv_numer'],
    //         'itm_rec_state' => 'Added',
    //     ]);

    //     // Get related item codes from the order table
    //     // $itemCodes = orderreceived::whereIn('id', $data['ids'])->pluck('itm_code');

    //     // Update items status
    //     // Item::whereIn('itm_code', $itemCodes)->update(['itm_status' => 'active']);

    //     // return response()->json(['success' => true]);
    //     return redirect()->route('itemreceivededit.showData')->with('success', 'Selected Order items "Confirmed" successfully!');
    // }
// }

