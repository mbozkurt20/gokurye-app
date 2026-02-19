<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Restaurant extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'restaurant_code',
        'restaurant_name',
        'name',
        'slug',
        'menu_template',
        'latitude',
        'longitude',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'tax_name',
        'tax_number',
        'address',
        'status',
        'getir',
        'yemeksepeti',
        'trendyol',
        'migros',
        'entegra_restaurant_id',
        'gpsyemek_api_key',
        'remember_token',
        'package_price',
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

    public function admin(): BelongsTo{
        return $this->belongsTo(Admin::class,'admin_id');
    }

    public function orders(): HasMany{
        return $this->hasMany(Order::class, 'restaurant_id');
    }

    public function payments()
    {
        return $this->morphMany(ProgressPaymentRecord::class, 'payable');
    }
}
