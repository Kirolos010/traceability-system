<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:locations,code',
            'type' => 'required|in:warehouse,shop,production,supplier,customer',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
