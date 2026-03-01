<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'sup_id',
        'sup_name',
        'sup_address',
        'sup_telephone',
        'sup_description'
    ];

}
