<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'user_name',
        'product_name',
        'group_id_in_app',
        'product_id_in_app',
        'main_group_id_in_app',
        'price',
        'price_unit',
        'status',
        'marketable',
        'sales_start_date',
        'sales_end_date',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }


    public function meta()
    {
        return $this->hasMany(Product_attributes::class);
    }


    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'target_id', 'discount_id')
            ->withPivot('percentage','id','target_type','target_name')
            ->wherePivot('deleted_at',null);
    }

    public function favoritedBy() {
        return $this->morphToMany(User::class, 'favoritable', 'favorites')->withTimestamps();
    }

}
