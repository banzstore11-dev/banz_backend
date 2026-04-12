<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow users to update their own reviews, or admins
        $review = $this->route('review');
        return $this->user() && (
            $this->user()->id === $review->user_id ||
            $this->user()->is_admin ?? false
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string|max:2000',
        ];
    }
}
