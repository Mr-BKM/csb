<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cus_id',
        'cus_name',
        'itm_code',
        'itm_name',
        'itm_unit_of_measure',
        'itm_qty',
        'itm_stockinhand',
        'order_date',
        'order_typ',
    ];
}

