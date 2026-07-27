<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $table = 'logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'module',
        'table_name',
        'process_id',
        'action',
        'description',
    ];

}
