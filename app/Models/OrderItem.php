<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'count',
        'discount',
    ];


     public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
