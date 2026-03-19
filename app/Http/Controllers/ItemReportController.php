<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemReportController extends Controller
{
    public function showdata()
    {
        // $items = Item::all();
        $groupedItems = Item::all()->groupBy('itm_group');
        return view('reports.itemsreport', compact('groupedItems'));
        // compact('items') kiyana eka aniwaren danna ona data tika blade ekata yawanna
        // return view('reports.itemsreport', compact('items'));
    }

  public function export(Request $request)
    {
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        // Data optimization: Ona tika vitharak gannawa
        $groupedItems = Item::select(['itm_code', 'itm_name', 'itm_group', 'itm_unit_of_measure', 'itm_stock', 'itm_reorder_flag', 'itm_reorder_level'])
                            ->get()
                            ->groupBy('itm_group');
        
        $type = $request->input('type', 'excel');

        // --- CSV / Excel Export ---
        if ($type == 'excel') {
            $fileName = 'items_report_' . date('Ymd') . '.csv';
            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['#', 'Code', 'Item Name', 'Category', 'Unit', 'Stock'];

            $callback = function() use($groupedItems, $columns) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel Sinhala support
                fputcsv($file, $columns);

                $serial = 1;
                foreach ($groupedItems as $groupName => $items) {
                    foreach ($items as $item) {
                        fputcsv($file, [
                            $serial++,
                            $item->itm_code,
                            $item->itm_name,
                            $groupName ?: 'Other',
                            $item->itm_unit_of_measure,
                            $item->itm_stock
                        ]);
                    }
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        // --- Word Export ---
        if ($type == 'word') {
            return response()->view('reports.items_report_pdf', compact('groupedItems'))
                ->header('Content-Type', 'application/msword; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename=items_report_' . date('Ymd') . '.doc');
        }

        // --- PDF Export (The missing part) ---
        if ($type == 'pdf') {
            // View eka load karala A4 portrait hadanawa
            $pdf = Pdf::loadView('reports.items_report_pdf', compact('groupedItems'))
                      ->setPaper('a4', 'portrait');
            
            return $pdf->download('items_report_' . date('Ymd') . '.pdf');
        }

        return redirect()->back()->with('error', 'Invalid export type.');
    }

//     public function export(Request $request)
// {
//     ini_set('max_execution_time', 300);
//     ini_set('memory_limit', '512M');

//     // Meka mehema wenas karanna
//     $groupedItems = Item::all()->groupBy('itm_group'); 
//     $type = $request->input('type');

//     if ($type == 'pdf') {
//         // 'compact' ekata 'groupedItems' danna
//         $pdf = Pdf::loadView('reports.itemsreport', compact('groupedItems'))->setPaper('a4', 'landscape');

//         return $pdf->stream('items_report.pdf');
//     }

//     // Word ekatath e widiyatama danna
//     if ($type == 'word') {
//         $headers = [
//             'Content-type' => 'application/vnd.ms-word',
//             'Content-Disposition' => 'attachment;Filename=items_report.doc',
//         ];
//         return response()->view('reports.itemsreport', compact('groupedItems'), 200, $headers);
//     }
// }

    // public function export(Request $request)
    // {
    //     ini_set('max_execution_time', 300); // Seconds 300 (Minutes 5)
    //     ini_set('memory_limit', '512M');

    //     $items = Item::all();
    //     $type = $request->input('type');

    //     if ($type == 'pdf') {
    //         // Methana 'pages.itemsreport' kiyana eka file name ekatama match wenna ona
    //         $pdf = Pdf::loadView('reports.itemsreport', compact('items'))->setPaper('a4', 'landscape');

    //         return $pdf->stream('items_report.pdf');
    //     }

    //     if ($type == 'word') {
    //         $headers = [
    //             'Content-type' => 'application/vnd.ms-word',
    //             'Content-Disposition' => 'attachment;Filename=items_report.doc',
    //         ];
    //         return response()->view('reports.itemsreport', compact('items'), 200, $headers);
    //     }
    // }
}
