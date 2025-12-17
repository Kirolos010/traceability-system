<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sale_number' => $this->sale_number,
            'batch_id' => $this->batch_id,
            'location_id' => $this->location_id,
            'customer_location_id' => $this->customer_location_id,
            'customer_name' => $this->customer_name,
            'quantity' => (float) $this->quantity,
            'unit_price' => $this->unit_price ? (float) $this->unit_price : null,
            'total_amount' => $this->total_amount ? (float) $this->total_amount : null,
            'sale_date' => $this->sale_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'batch' => new BatchResource($this->whenLoaded('batch')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'customer_location' => new LocationResource($this->whenLoaded('customerLocation')),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }
}
