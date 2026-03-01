<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderm extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cus_name',
        'cus_id',
        'itm_code',
        'itm_barcode',
        'itm_name',
        'itm_sinhalaname',
        'itm_book_code',
        'itm_page_num',
        'itm_group',
        'itm_subgroup',
        'itm_unit_of_measure',
        'itm_stockinhand',
        'itm_qty',
        'order_typ',
        'order_date',
        'po_date',
        'po_number',
        'sup_id',
        'sup_name',
        'po_state',
        'itm_rec_date',
        'itm_inv_numer',
        'itm_res_qty',
        'itm_warranty',
        'itm_unit_price',
        'itm_tot_price',
        'inv_tot_price',
        'itm_rec_state',
        'bill_submit_date',
        'bill_number',
        'bill_state'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'itm_code', 'itm_code');
    }

        public function received()
    {
        return $this->hasMany(orderreceived::class, 'table_id', 'id');
    }
}
