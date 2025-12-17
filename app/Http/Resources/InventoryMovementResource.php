<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryMovementResource extends JsonResource
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
            'batch_id' => $this->batch_id,
            'location_id' => $this->location_id,
            'type' => $this->type,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'quantity' => (float) $this->quantity,
            'unit_cost' => $this->unit_cost ? (float) $this->unit_cost : null,
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'movement_date' => $this->movement_date?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'batch' => new BatchResource($this->whenLoaded('batch')),
            'location' => new LocationResource($this->whenLoaded('location')),
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
