<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Details_pattern extends Model
{
    protected $table = 'details_pattern';
    protected $fillable = [
        'pattern_id',
        'packing_id',
        'quantity'
    ];

    public function pattern()
    {
        return $this->belongsTo(Packing_pattern::class, 'pattern_id');
    }

}
