<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyInfo extends Model
{
    use SoftDeletes;

    protected $fillable =[
        "title","value","parent_id"
    ];
}
