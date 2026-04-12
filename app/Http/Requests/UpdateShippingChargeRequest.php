<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'country' => 'nullable|string|max:255',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_order_value' => 'nullable|numeric|min:0|gt:min_order_value',
            'charge' => 'sometimes|numeric|min:0',
            'charge_type' => 'sometimes|in:fixed,percentage',
            'priority' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ];
    }
}
