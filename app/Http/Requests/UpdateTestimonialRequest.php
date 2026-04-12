<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestimonialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // In production, add admin authorization
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'role' => 'nullable|string|max:255',
            'content' => 'sometimes|required|string|min:10|max:1000',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'avatar' => 'nullable|string|max:255',
            'is_approved' => 'sometimes|boolean',
        ];
    }
}
