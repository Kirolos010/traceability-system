<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'batch_number' => 'required|string|max:100|unique:batches,batch_number',
            'lot_number' => 'nullable|string|max:100',
            'manufacturing_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:manufacturing_date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string',
        ];
    }
}
