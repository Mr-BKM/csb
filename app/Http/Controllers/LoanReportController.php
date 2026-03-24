<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issuing;
// use Illuminate\Support\Facades\DB;

class LoanReportController extends Controller
{
    public function showData(Request $request)
    {

        $groups = Issuing::select('cus_id as grp_name')
                         ->distinct()
                         ->get();


        $query = Issuing::query();
        if ($request->filled('group_id')) {
            $query->where('cus_id', $request->group_id);
        }
        $items = $query->orderBy('cus_id', 'asc')->get();
        $groupedItems = $items->groupBy('cus_id');

        return view('reports.loan.report.loanreport', compact('groupedItems', 'groups'));
    }
}
