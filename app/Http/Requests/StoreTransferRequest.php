<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'batch_id' => 'required|exists:batches,id',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'quantity' => 'required|numeric|min:0.0001',
            'status' => 'in:pending,in_transit,completed,cancelled',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
