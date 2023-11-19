<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $first_name
 * @property string $phone_number
 * @property string $password
 */
class SignUpRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'unique:users', 'regex:/^\+[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/'],
            'first_name' => ['required', 'regex:/^[\p{L} ]+$/u', 'min:2', 'max:24'],
            'password' => ['required', 'min:6', 'max:124'],
        ];
    }
}
