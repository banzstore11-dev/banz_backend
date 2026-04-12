<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'user_id' => 'nullable|integer|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'shipping_address' => 'required|array',
            'shipping_address.first_name' => 'required|string|max:255',
            'shipping_address.last_name' => 'required|string|max:255',
            'shipping_address.email' => 'nullable|email|max:255',
            'shipping_address.phone' => 'nullable|string|max:255',
            'shipping_address.address_line_1' => 'required|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:255',
            'shipping_address.state' => 'nullable|string|max:255',
            'shipping_address.postal_code' => 'required|string|max:255',
            'shipping_address.country' => 'required|string|max:255',
            'billing_address' => 'nullable|array',
            'billing_address.first_name' => 'required_with:billing_address|string|max:255',
            'billing_address.last_name' => 'required_with:billing_address|string|max:255',
            'billing_address.email' => 'nullable|email|max:255',
            'billing_address.phone' => 'nullable|string|max:255',
            'billing_address.address_line_1' => 'required_with:billing_address|string|max:255',
            'billing_address.address_line_2' => 'nullable|string|max:255',
            'billing_address.city' => 'required_with:billing_address|string|max:255',
            'billing_address.state' => 'nullable|string|max:255',
            'billing_address.postal_code' => 'required_with:billing_address|string|max:255',
            'billing_address.country' => 'required_with:billing_address|string|max:255',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tax' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ];
    }
}
