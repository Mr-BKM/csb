<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issuing extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'issue_typ',
        'cus_name',
        'cus_id',
        'itm_code',
        'itm_stockinhand',
        'itm_qty',
        'issue_date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itm_code', 'itm_code');
    }
}
