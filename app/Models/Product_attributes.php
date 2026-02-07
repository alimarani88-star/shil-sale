<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product_attributes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_attributes';

    protected $fillable = ['meta_key', 'meta_name','meta_value', 'product_id'];


}
