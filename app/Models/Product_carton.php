<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_carton extends Model
{
    protected $table = 'product_carton';

    protected $fillable = [
        'item_id',
        'group_id',
        'carton_id',
        'max_number'
    ];
}
