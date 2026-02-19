<?php

namespace App\Models;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Courier extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $fillable = [
        'admin_id',
        'restaurant_id',
        'name',
        'phone',
        'status',
        'password',
        'last_assigned_at',
        'latitude',
        'longitude',
        'price_type',
        'price',
        'km_price',
        'km_distance_later',
        'fixed_price',
        'code',
        'birthday',
        'point',
        'day_point',
        'is_active',
        'fcm_token',
        'iban',
        'bank',
        'profile_photo',
        'tc_id',
        'age',
        'vehicle_type',
        'plate',
        'blood_type',
    ];

    public function payments()
    {
        return $this->morphMany(ProgressPaymentRecord::class, 'payable');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
