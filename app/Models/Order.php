<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'code',
        'customer_id',
        'customer_name',
        'status',
        'status_title',
        'copan',
        'total_price',
        'send_price',
        'send_type',
        'send_time',
        'address_id',
        'invoice',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
