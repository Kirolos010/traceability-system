<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraceabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'backward_trace' => $this->when(isset($this->backward_trace), $this->backward_trace),
            'forward_trace' => $this->when(isset($this->forward_trace), $this->forward_trace),
            'batch' => $this->when(isset($this->batch), new BatchResource($this->batch)),
            'sale' => $this->when(isset($this->sale), new SaleResource($this->sale)),
            'batch_trace' => $this->when(isset($this->batch_trace), $this->batch_trace),
        ];
    }
}
