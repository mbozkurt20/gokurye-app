<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourierOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'courier_id',
        'order_id',
        'status',
        'reason',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
