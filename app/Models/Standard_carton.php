<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Standard_carton extends Model
{
    protected $table = 'standard_carton';

    protected $fillable = [
        'name',
        'length',
        'width',
        'height',
        'id_post',
        'box_weight',
        'type',
        'user_id',
        'user_name',
    ];

}
