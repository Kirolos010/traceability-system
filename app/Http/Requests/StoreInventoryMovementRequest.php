<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_id' => 'required|exists:batches,id',
            'location_id' => 'required|exists:locations,id',
            'type' => 'required|in:in,out,adjustment',
            'reference_type' => 'nullable|in:supplier,production,transfer,sale,adjustment,other',
            'reference_id' => 'nullable|integer',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'movement_date' => 'required|date',
        ];
    }
}
