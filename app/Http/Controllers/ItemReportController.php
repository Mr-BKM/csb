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
        ini_set('max_execution_time', 300); // Seconds 300 (Minutes 5)
        ini_set('memory_limit', '512M');

        $items = Item::all();
        $type = $request->input('type');

        if ($type == 'pdf') {
            // Methana 'pages.itemsreport' kiyana eka file name ekatama match wenna ona
            $pdf = Pdf::loadView('reports.itemsreport', compact('items'))->setPaper('a4', 'landscape');

            return $pdf->stream('items_report.pdf');
        }

        if ($type == 'word') {
            $headers = [
                'Content-type' => 'application/vnd.ms-word',
                'Content-Disposition' => 'attachment;Filename=items_report.doc',
            ];
            return response()->view('reports.itemsreport', compact('items'), 200, $headers);
        }
    }
}
