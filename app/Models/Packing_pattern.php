<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packing_pattern extends Model
{
    protected $table = 'packing_pattern';
    protected $fillable = [
        'carton_id',
    ];


    public function details()
    {
        return $this->hasMany(Details_pattern::class, 'pattern_id');
    }

}
