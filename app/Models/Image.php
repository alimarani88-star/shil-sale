<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'images';

    protected $fillable = ['image_url', 'imageable_id', 'imageable_type','primary','position' , 'alt' ];


    public function imageable()
    {
        return $this->morphTo();
    }

}
