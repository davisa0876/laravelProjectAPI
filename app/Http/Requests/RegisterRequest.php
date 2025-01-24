<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Return true if everyone can register,
        // or add logic for restricting who can do so
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.string' => __('validation.name.string'),
            'name.max' => __('validation.name.max', ['max' => 255]),
            'email.required' => __('validation.email.required'),
            'email.email' => __('validation.email.email'),
            'email.unique' => __('validation.email.unique'),
            'password.required' => __('validation.password.required'),
            'password.string' => __('validation.password.string'),
            'password.min' => __('validation.password.min', ['min' => 8]),
            'password.confirmed' => __('validation.password.confirmed'),
        ];
    }
}
