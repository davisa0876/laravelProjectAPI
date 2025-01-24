<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.string' => __('validation.name.string'),
            'name.max' => __('validation.name.max', ['max' => 255]),
            'password.string' => __('validation.password.string'),
            'password.min' => __('validation.password.min', ['min' => 8]),
            'password.confirmed' => __('validation.password.confirmed'),
        ];
    }
} 