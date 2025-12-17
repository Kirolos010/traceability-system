<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'customer_location_id' => 'nullable|exists:locations,id',
            'customer_name' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_price' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
