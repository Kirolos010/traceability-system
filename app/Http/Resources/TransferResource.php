<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'transfer_number' => $this->transfer_number,
            'batch_id' => $this->batch_id,
            'from_location_id' => $this->from_location_id,
            'to_location_id' => $this->to_location_id,
            'quantity' => (float) $this->quantity,
            'status' => $this->status,
            'transfer_date' => $this->transfer_date?->format('Y-m-d'),
            'received_date' => $this->received_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'batch' => new BatchResource($this->whenLoaded('batch')),
            'from_location' => new LocationResource($this->whenLoaded('fromLocation')),
            'to_location' => new LocationResource($this->whenLoaded('toLocation')),
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
