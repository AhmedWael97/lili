<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company' => ['nullable', 'string', 'max:255'],
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'terms' => ['accepted'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'terms.accepted' => 'You must accept the Terms & Privacy Policy.',
            'package_id.required' => 'Please select a package.',
            'package_id.exists' => 'Please select a valid package.',
        ];
    }
}
