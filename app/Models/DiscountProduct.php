<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discount_product';

    protected $fillable = [
        'discount_id',
        'target_type',
        'target_id',
        'target_name',
        'percentage',
        'description',
        'user_id',
        'user_name'
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'target_id');
    }

    public function discounts()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
