<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_profile';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'national_code',
        'profile_photo_path',
        'type',
        'economic_number',
        'company_national_id',
        'card_number'

    ];
}
