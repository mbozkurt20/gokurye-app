<?php

namespace App\Http\Resources;

use App\Helpers\OrdersHelper;
use App\Models\Customer;
use App\Models\Restaurant;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $restaurant = Restaurant::find($this->restaurant_id);

        return [
            'id' =>$this->id,
            'platform' => $this->platform,
            'restaurantName' => $restaurant?->restaurant_name,
            'restaurantAddress' => $restaurant?->address,
            'restaurantPhone' => $restaurant?->phone,
            'restaurantLat' => $restaurant?->latitude,
            'restaurantLong' => $restaurant?->longitude,
            'distance' => OrdersHelper::formatDistance($this->distance),
            'full_name' => $this->full_name,
            'tracking_id' => $this->tracking_id,
            'phone' => $this->phone,
            'discount' => $this->discount,
            'sub_amount' => $this->sub_amount,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'address' => $this->address,
            'customer' => new CustomerResource(Customer::find($this->customer_id)),
            'notes' => $this->notes,
            'status' => $this->status,
            'assigned_at' => date('d-m-Y h:i:s',strtotime($this->assigned_at)),
            'created_at' => date('d-m-Y h:i:s',strtotime($this->created_at)),
            'products' => json_decode($this->items ), //OrderItemResource::collection($this->whenLoaded('order_items'))
            'is_sms' => $restaurant?->admin?->is_sms,
        ];
    }
}
