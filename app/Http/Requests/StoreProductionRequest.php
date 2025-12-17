<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'output_batch_id' => 'nullable|exists:batches,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|numeric|min:0.0001',
            'status' => 'in:pending,in_progress,completed,cancelled',
            'production_date' => 'required|date',
            'notes' => 'nullable|string',
            'materials' => 'required|array|min:1',
            'materials.*.batch_id' => 'required|exists:batches,id',
            'materials.*.quantity' => 'required|numeric|min:0.0001',
        ];
    }
}
