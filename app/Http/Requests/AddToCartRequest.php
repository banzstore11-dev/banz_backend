<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('product_id') && $this->has('quantity')) {
                $product = Product::find($this->product_id);
                
                if ($product) {
                    // Check if product is active
                    if (!$product->is_active) {
                        $validator->errors()->add(
                            'product_id',
                            'This product is not available.'
                        );
                    }

                    // Check stock availability
                    $requestedQuantity = $this->integer('quantity');
                    $currentCartQuantity = 0;
                    
                    // If user is authenticated, check existing cart quantity
                    if ($this->user()) {
                        $existingCart = \App\Models\Cart::where('user_id', $this->user()->id)
                            ->where('product_id', $product->id)
                            ->first();
                        if ($existingCart) {
                            $currentCartQuantity = $existingCart->quantity;
                        }
                    }

                    $totalNeeded = $currentCartQuantity + $requestedQuantity;
                    
                    if ($product->stock_quantity < $totalNeeded) {
                        $available = $product->stock_quantity - $currentCartQuantity;
                        $validator->errors()->add(
                            'quantity',
                            "Insufficient stock. Only {$available} item(s) available."
                        );
                    }
                }
            }
        });
    }
}
