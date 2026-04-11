<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
// use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $item;

    public function __construct()
    {
        $this->item = new Item();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->item->all();
    }
}
