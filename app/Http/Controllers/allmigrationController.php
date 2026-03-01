<?php

namespace App\Http\Controllers;

use App\Imports\CustomerImport;
use Illuminate\Http\Request;
// use App\Models\Group;
use App\Imports\GroupImport;
use App\Imports\SubGroupImport;
use App\Imports\ItemImport;
use Maatwebsite\Excel\Facades\Excel;

class allmigrationController extends Controller
{
    public function showData()
    {
       return view('pages.allmigration');
    }

    public function importGeneral(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx',
        'import_type' => 'required'
    ]);

    $file = $request->file('file');
    $type = $request->import_type;

    try {
        match ($type) {
            'group'    => Excel::import(new GroupImport, $file),
            'subgroup' => Excel::import(new SubGroupImport, $file),
            'item'     => Excel::import(new ItemImport, $file),
            'customer'     => Excel::import(new CustomerImport(), $file),
            default    => throw new \Exception("Invalid import type selected."),
        };

        return back()->with('success', ucfirst($type) . ' imported successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}


