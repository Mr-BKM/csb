<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item; // Hardware items thiyena model eka
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{


public function downloadReport()
{
    $items = Item::all(); // Database eken okkoma items gannawa

    $data = [
        'title' => 'H-FLOWSTOCK Inventory Report',
        'date' => date('m/d/Y'),
        'items' => $items
    ];

    $pdf = Pdf::loadView('reports.my_report', $data);
    return $pdf->stream('h-flowstock-report.pdf');
}
}



