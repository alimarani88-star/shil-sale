<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discounts';

    protected $fillable = [
        'discount_name',
        'discount_type',
        'start_date',
        'end_date',
        'status',
        'description',
        'user_id',
        'user_name'
    ];

    public static $typeMap = [
        'amazingsale' => 'فروش شگفت‌انگیز',
        'common' => 'تخفیف عادی',
    ];

    public function getDiscountTypeFaAttribute()
    {
        return self::$typeMap[$this->discount_type] ?? $this->discount_type;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_product', 'discount_id', 'target_id')
            ->withPivot('percentage','id','target_type','target_name')
            ->wherePivot('deleted_at',null);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->whereNull('deleted_at');
    }


}
