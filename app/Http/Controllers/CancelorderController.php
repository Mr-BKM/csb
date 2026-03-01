<?php

namespace App\Http\Controllers;

use App\Models\Orderm;
use App\Models\Item;
use Illuminate\Http\Request;

class CancelorderController extends Controller
{
    // The __construct method is now unnecessary since you aren't using the model
    protected $orderm;
    public function __construct()
    {
        $this->orderm = new Orderm();
    }

    public function showData(Request $request)
    {
        $orderms = orderm::where('itm_rec_state', 'Pending')->orderBy('order_id', 'asc')->get(); // no paginate()

        return view('pages.cancelorder', compact('orderms'));
    }

    public function updateData($id)
    {
        $orderm = orderm::findOrFail($id);

        $orderm->order_typ = "Cancel";
        $orderm->po_state  = "Cancel";
        $orderm->itm_rec_state = "Cancel";
        $orderm->bill_state = "Cancel";

        $orderm->save();

        Item::where('itm_code', $orderm->itm_code)->update(['itm_status' => 'active']);

        return redirect()->route('cancelorder.showData')->with('success', 'Order Item canceled successfully.');
    }
}
