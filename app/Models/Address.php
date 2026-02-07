<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
         'user_id',
        'province_id',
        'city_id',
        'postal_code',
        'address',
        'no',
        'unit',
        'recipient_first_name',
        'recipient_last_name',
        'mobile',
        'is_default',
        'status',
    ];

    public function city(){
        return $this->belongsTo(ProvinceCity::class ,'city_id','id');
    }

    public function province(){
        return $this->belongsTo(ProvinceCity::class ,'province_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function latestAddress()
    {
        return $this->hasOne(Address::class, 'user_id', 'id')
            ->whereNull('deleted_at')
            ->latest('id');
    }
}
