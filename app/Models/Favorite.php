<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'favorites';

    protected $fillable = [
        'user_id',
        'favoritable_id',
        'favoritable_type',
    ];

    /**
     * موجودیت لایک‌شده
     */
    public function favoritable()
    {
        return $this->morphTo();
    }

    /**
     * کاربری که لایک کرده
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
