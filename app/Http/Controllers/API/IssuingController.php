<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issuing;
// use Illuminate\Http\Request;

class IssuingController extends Controller
{
     protected $issuing;

    public function __construct()
    {
        $this->issuing = new Issuing();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->issuing
        ->where('issue_typ', 'Loan')
        ->join('items', 'issuings.itm_code', '=', 'items.itm_code') // Items table eka ekka connect kala
        ->select('issuings.*', 'items.itm_name', 'items.itm_unit_of_measure as itm_unit') // Item name ekai unit ekai gaththa
        ->get();
    }
}
