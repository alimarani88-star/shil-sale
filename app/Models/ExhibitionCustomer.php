<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExhibitionCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exhibition_customers';

    public const BOOTH_SHIL_IRAN = 'شیل ایران';
    public const BOOTH_SIM_CABLE_IRANIAN = 'سیم وکابل ایرانیان';

    public const BOOTHS = [
        self::BOOTH_SHIL_IRAN,
        self::BOOTH_SIM_CABLE_IRANIAN,
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'company_name',
        'raffle_participate',
        'raffle_company_number',
        'exhibition_name',
        'booth',
        'description',
        'status',
        'type',
        'year',
        'month',
        'request_agency',
        'registrant_name'
    ];
}
