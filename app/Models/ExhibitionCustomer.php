<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitionCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exhibition_customers';

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'company_name',
        'exhibition_name',
        'description',
        'status',
        'type',
        'year',
        'month',
        'request_agency',
        'registrant_name'
    ];
}
