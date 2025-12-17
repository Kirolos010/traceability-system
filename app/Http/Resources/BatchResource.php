<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'product_id' => $this->product_id,
            'batch_number' => $this->batch_number,
            'lot_number' => $this->lot_number,
            'manufacturing_date' => $this->manufacturing_date?->format('Y-m-d'),
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'supplier_id' => $this->supplier_id,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'product' => new ProductResource($this->whenLoaded('product')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'stock_levels' => StockLevelResource::collection($this->whenLoaded('stockLevels')),
        ];
    }
}
