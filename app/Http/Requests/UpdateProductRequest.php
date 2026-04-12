<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('product');

        return [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'retail_price' => 'sometimes|required|numeric|min:0',
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'stock_quantity' => 'nullable|integer|min:0',
            'image_files' => 'nullable|array',
            'image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'image_metadata' => 'nullable|array',
            'image_metadata.*.sort_order' => 'nullable|integer|min:0',
            'image_metadata.*.is_primary' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'price_tiers' => 'nullable|array',
            'price_tiers.*.min_quantity' => 'required_with:price_tiers|integer|min:1',
            'price_tiers.*.max_quantity' => 'nullable|integer|min:1',
            'price_tiers.*.price' => 'required_with:price_tiers|numeric|min:0',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('price_tiers')) {
                foreach ($this->price_tiers as $index => $tier) {
                    if (isset($tier['max_quantity']) && isset($tier['min_quantity'])) {
                        if ($tier['max_quantity'] <= $tier['min_quantity']) {
                            $validator->errors()->add(
                                "price_tiers.{$index}.max_quantity",
                                'The max quantity must be greater than min quantity.'
                            );
                        }
                    }
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }

        // Parse JSON strings from FormData
        if ($this->has('price_tiers') && is_string($this->price_tiers)) {
            $this->merge([
                'price_tiers' => json_decode($this->price_tiers, true) ?? [],
            ]);
        }

        if ($this->has('image_metadata') && is_string($this->image_metadata)) {
            $this->merge([
                'image_metadata' => json_decode($this->image_metadata, true) ?? [],
            ]);
        }
    }
}
