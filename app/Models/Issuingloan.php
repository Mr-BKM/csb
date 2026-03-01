<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class issuingloan extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_table_id',
        'issue_id',
        'cus_id',
        'itm_code',
        'itm_stockinhand',
        'itm_qty',
        'issue_date',
        'issue_typ'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itm_code', 'itm_code');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cus_id', 'cus_id');
    }

    public function issuing()
    {
        return $this->belongsTo(Issuing::class, 'issue_table_id', 'id');
    }
}
