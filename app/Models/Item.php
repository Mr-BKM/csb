<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    use HasFactory;

    protected $fillable = [
        'itm_code',
        'itm_barcode',
        'itm_name',
        'itm_sinhalaname',
        'itm_book_code',
        'itm_page_num',
        "itm_group_id",
        'itm_group',
        "itm_subgroup_id",
        'itm_subgroup',
        'itm_unit_of_measure',
        'itm_book_stock',
        'itm_stock',
        'itm_reorder_level',
        'itm_reorder_flag',
        'itm_description',
        'itm_status'
    ];

    public function ordertemps()
    {
        return $this->hasMany(Ordertemp::class, 'itm_code', 'itm_code');
    }

    public function orderm()
    {
        return $this->hasMany(Orderm::class, 'itm_code', 'itm_code');
    }

}
