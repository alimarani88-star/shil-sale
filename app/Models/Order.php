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
        'tax_percent',
        'tax_amount',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function logs()
    {
        return $this->hasMany(Log::class, 'process_id', 'id')
            ->where('table_name', 'orders');
    }

}
