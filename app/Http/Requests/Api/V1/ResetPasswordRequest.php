<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'bail|required|email|max:255|exists:users,email',
            'password' => 'required|string|min:8|max:255|confirmed',
            'token' => [
                'required',
                'string','max:255',
                Rule::exists('password_resets', 'token')
                ->where(function ($query) {
                    return $query->where('email', $this->email);
                })
            ]
        ];
    }
}
