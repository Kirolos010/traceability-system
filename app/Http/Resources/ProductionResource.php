<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductionResource extends JsonResource
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
            'production_number' => $this->production_number,
            'product_id' => $this->product_id,
            'output_batch_id' => $this->output_batch_id,
            'location_id' => $this->location_id,
            'quantity' => (float) $this->quantity,
            'status' => $this->status,
            'production_date' => $this->production_date?->format('Y-m-d'),
            'notes' => $this->notes,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'product' => new ProductResource($this->whenLoaded('product')),
            'output_batch' => new BatchResource($this->whenLoaded('outputBatch')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'materials' => ProductionMaterialResource::collection($this->whenLoaded('materials')),
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
