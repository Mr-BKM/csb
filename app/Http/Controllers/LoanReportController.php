<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issuing;
// use Illuminate\Support\Facades\DB;

class LoanReportController extends Controller
{
    public function showData(Request $request)
{
    // Dropdown එක සඳහා loan ගත් අයගේ ලැයිස්තුව
    $groups = Issuing::where('issue_typ', 'loan')
        ->select('cus_id', 'cus_name')
        ->distinct()
        ->orderBy('cus_id', 'asc')
        ->get();

    $query = Issuing::query()->where('issue_typ', 'loan');

    if ($request->filled('group_id')) {
        $query->where('cus_id', $request->group_id);
    }

    $items = $query->orderBy('cus_id', 'asc')->get();

    /**
     * Map එක පාවිච්චි කරලා දත්ත සකස් කිරීම
     */
    $groupedItems = $items->groupBy('cus_id')->map(function ($customerRows) {
        // සෑම පාරිභෝගිකයෙකුටම අදාළ items ටික itm_code එක අනුව නැවත group කරනවා
        return $customerRows->groupBy('itm_code')->map(function ($itemGroup) {
            // පළමු row එක copy කරගන්නවා (itm_name, unit වගේ දේවල් ගන්න)
            $groupedRow = $itemGroup->first();

            // එම item code එකට අදාළ සියලුම qty ටික එකතු කරනවා
            $groupedRow->itm_qty = $itemGroup->sum('itm_qty');

            return $groupedRow;
        })->sortBy('itm_code');
    });

    return view('reports.loan.report.loanreport', compact('groupedItems', 'groups'));
}
}
