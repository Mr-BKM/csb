<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Issuing;
use Carbon\Carbon;
use DB;

class ItemReportController extends Controller
{
    public function showData(Request $request)
    {
        $reportType = $request->input('report_type');
        $selectedGroup = $request->input('group_id');
        $groups = Group::orderBy('grp_name', 'asc')->get();
        $query = Item::orderBy('itm_code', 'asc');

        if ($selectedGroup) {
            $query->where('itm_group', $selectedGroup);
        }

        // --- 1. Buffer Stock Report එක නම් Forecast logic එක call කරනවා ---
        if ($reportType == 'I_B_S_Report') {
            $this->runAutoForecasting(); // මෙතනදී තමයි වෙනම තියෙන function එක call කරන්නේ

            $items = $query->get();
            return view('reports.item.report.itembufferstock', compact('items', 'groups'));
        }

        // --- 2. Group Wise Report ---
        if ($reportType == 'G_V_I_S_Report') {
            $groupedItems = $query->get()->groupBy('itm_group');
            return view('reports.item.report.totalitemsgrpvise', compact('groupedItems', 'groups'));
        }

        // --- 3. Default View ---
        $items = $query->get();
        return view('reports.item.report.totalitems', compact('items', 'groups'));
    }

    public function export(Request $request)
{
    ini_set('max_execution_time', 300);
    ini_set('memory_limit', '512M');

    $reportType = $request->input('report_type');
    $exportType = $request->input('type', 'excel');

    // Filter එකෙන් එන group id එකත් ගන්නවා
    $selectedGroup = $request->input('group_id');

    $query = Item::orderBy('itm_code', 'asc');

    // Group filter එකක් තියෙනවා නම් ඒක අදාළ කරගන්නවා
    if ($selectedGroup) {
        $query->where('itm_group', $selectedGroup);
    }

    // --- 1. CSV / Excel Export ---
    if ($exportType == 'excel') {
        $fileName = ($reportType == 'I_B_S_Report' ? 'item_buffer_stock_' : ($reportType == 'G_V_I_S_Report' ? 'group_wise_items_' : 'all_items_')) . date('Ymd') . '.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Report Type එක අනුව Columns තීරණය කිරීම
        if ($reportType == 'I_B_S_Report') {
            $columns = ['#', 'Item Code', 'Item Name', 'Unit', 'Stock', 'Reorder Level', 'Status'];
        } elseif ($reportType == 'G_V_I_S_Report') {
            $columns = ['#', 'Group/Category', 'Item Code', 'Item Name', 'Unit', 'Current Stock'];
        } else {
            $columns = ['#', 'Item Code', 'Item Name', 'Unit', 'Current Stock'];
        }

        $callback = function () use ($query, $columns, $reportType) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xef) . chr(0xbb) . chr(0xbf)); // Sinhala support
            fputcsv($file, $columns);

            $serial = 1;

            if ($reportType == 'G_V_I_S_Report') {
                // --- Group Wise Excel ---
                $groupedData = $query->get()->groupBy('itm_group');
                foreach ($groupedData as $groupName => $items) {
                    foreach ($items as $item) {
                        fputcsv($file, [
                            $serial++,
                            $groupName ?: 'Not Assigned',
                            $item->itm_code,
                            $item->itm_name . ($item->itm_sinhalaname ? ' - ' . $item->itm_sinhalaname : ''),
                            $item->itm_unit_of_measure,
                            $item->itm_stock
                        ]);
                    }
                }
            } elseif ($reportType == 'I_B_S_Report') {
                // --- Buffer Stock Excel ---
                $items = $query->get();
                foreach ($items as $item) {
                    fputcsv($file, [
                        $serial++,
                        $item->itm_code,
                        $item->itm_name . ($item->itm_sinhalaname ? ' - ' . $item->itm_sinhalaname : ''),
                        $item->itm_unit_of_measure,
                        $item->itm_stock,
                        $item->itm_reorder_level,
                        $item->itm_status == 'ordered' ? 'Ordered' : 'Not ordered'
                    ]);
                }
            } else {
                // --- Default Stock Excel ---
                $items = $query->get();
                foreach ($items as $item) {
                    fputcsv($file, [
                        $serial++,
                        $item->itm_code,
                        $item->itm_name . ($item->itm_sinhalaname ? ' - ' . $item->itm_sinhalaname : ''),
                        $item->itm_unit_of_measure,
                        $item->itm_stock
                    ]);
                }
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // --- 2. Word Export ---
    if ($exportType == 'word') {
        $date = date('Y-m-d H:i A');

        if ($reportType == 'G_V_I_S_Report') {
            $groupedItems = $query->get()->groupBy('itm_group');
            return response()->view('reports.item.word.totalitemsgrpviseword', compact('groupedItems', 'date'))
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename=group_items_report.doc');
        }

        if ($reportType == 'I_B_S_Report') {
            $items = $query->get();
            return response()->view('reports.item.word.itembufferstockword', compact('items', 'date'))
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename=item_buffer_stock_report.doc');
        }

        // Default Word Report
        $items = $query->get();
        return response()->view('reports.item.word.totalitemsword', compact('items', 'date'))
            ->header('Content-Type', 'application/msword')
            ->header('Content-Disposition', 'attachment; filename=items_report.doc');
    }
}

    private function runAutoForecasting()
    {
        // 1. මේ වසරේ අද දක්වා ගතවූ දින ගණන (උදා: පෙබ 28 = 59)
        $daysPassed = Carbon::now()->dayOfYear;

        // 2. Reorder condition එකට ගැලපෙන items ටික ගන්නවා

        $items = Item::where('itm_reorder_flag', 'Yes')->where('itm_status', 'active')->whereColumn('itm_reorder_level', '>=', 'itm_stock')->get();

        if ($items->isEmpty()) {
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                // 3. මේ වසරේදී අදාළ අයිතමය නිකුත් කර ඇති මුළු ප්‍රමාණය (Usage)
                $totalIssuedQty = Issuing::where('itm_code', $item->itm_code)->whereYear('issue_date', date('Y'))->sum('itm_qty');

                // 4. FORECAST CALCULATION: (Actual Usage / Days Gone) * 365
                $forecastedQty = $daysPassed > 0 ? ($totalIssuedQty / $daysPassed) * 120 : 0;
                $finalOrderQty = round($forecastedQty);

                // 5. වැදගත්ම කොටස: ගණනය කරපු අගය Item table එකේ reorder level එකට save කරනවා
                if ($finalOrderQty > 0) {
                    $item->update([
                        'itm_reorder_level' => $finalOrderQty,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Item Reorder Level Update Error: ' . $e->getMessage());
        }
    }
}
