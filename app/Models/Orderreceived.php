<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderreceived extends Model
{
    use HasFactory;
    protected $fillable = ['table_id', 'order_id', 'cus_id', 'itm_code', 'itm_qty', 'sup_id', 'itm_rec_date', 'itm_inv_numer', 'itm_res_qty', 'itm_warranty', 'itm_unit_price', 'itm_tot_price', 'itm_rec_state', 'bill_submit_date', 'bill_number', 'bill_state'];
    public function orderMaster()
    {
        return $this->belongsTo(orderm::class, 'table_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cus_id', 'cus_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'itm_code', 'itm_code');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'sup_id', 'sup_id');
    }
}
