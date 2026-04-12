<?php

namespace App\Http\Requests;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
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
            'quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $cartId = $this->route('cart');
            $cart = Cart::with('product')->find($cartId);
            
            if ($cart && $this->has('quantity')) {
                $product = $cart->product;
                $requestedQuantity = $this->integer('quantity');
                
                if ($product) {
                    // Check if product is active
                    if (!$product->is_active) {
                        $validator->errors()->add(
                            'quantity',
                            'This product is no longer available.'
                        );
                    }

                    // Check stock availability
                    if ($product->stock_quantity < $requestedQuantity) {
                        $validator->errors()->add(
                            'quantity',
                            "Insufficient stock. Only {$product->stock_quantity} item(s) available."
                        );
                    }
                }
            }
        });
    }
}
