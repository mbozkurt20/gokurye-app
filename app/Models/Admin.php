<?php

namespace App\Models;

use App\Traits\HasRandomCode;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes;
    use HasRandomCode;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by_id',
        'created_by_type',
        'name',
        'email',
        'code',
        'password',
        'phone',
        'address',
        'auto_orders',
        'top_up_balance',
        'latitude',
        'city_id',
        'district_id',
        'longitude',
        'is_active',
        'top_up_price',
        'vatan_sms_customer',
        'vatan_sms_username',
        'vatan_sms_password',
        'vatan_sms_orginator',
        'is_sms', //sms ile doğrulama
        'is_test',
        'distance_limit',
        'max_package_limit',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

}
