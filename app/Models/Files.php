<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Files extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'files';

    protected $fillable = ['file_url', 'process_id', 'type' , 'deleted_at' ];


}
