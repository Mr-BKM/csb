<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordertemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cus_name',
        'cus_id',
        'itm_code',
        'itm_qty',
        'order_typ',
        'order_date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itm_code', 'itm_code');
    }
}
