<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourierResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'online' => $this->online,
            'birthday' => $this->birthday,
            'code' => $this->code,
            'name' => $this->name,
            'price_type' => $this->price_type,
            'price' => $this->price,
            'fixed_price' => $this->fixed_price,
            'km_price' => $this->km_price,
            'phone' => $this->phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'iban' => $this->iban,
            'bank' => $this->bank,
            'vehicle_type' => $this->vehicle_type,
            'plate' => $this->plate,
            'blood_type' => $this->blood_type,
        ];
    }
}
