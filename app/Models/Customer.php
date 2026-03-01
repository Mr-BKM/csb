<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'cus_id',
        'cus_name',
        'cus_address',
        'cus_telephone',
        'cus_description'
    ];
}
