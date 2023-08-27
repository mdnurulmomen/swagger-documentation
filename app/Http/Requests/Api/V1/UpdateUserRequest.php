<?php

namespace App\Http\Requests\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->is_marketing) {

            $this->merge([
                'is_marketing' => filter_var($this->is_marketing, FILTER_VALIDATE_BOOLEAN)
            ]);

        }

        if ($this->guard()->user()->is_admin && $this->is_admin) {

            $this->merge([
                'is_admin' => filter_var($this->is_admin, FILTER_VALIDATE_BOOLEAN)
            ]);

        }
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        if ($this->route()->named('admin.users.update')) {

            $user = User::where('uuid', $this->route('uuid'))->firstOrFail();

        } else {

            $user = $this->guard()->user();

        }

        return [
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'required|string|min:8|max:255|confirmed',
            'avatar' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'is_marketing' => 'nullable|boolean',
            'is_admin' => 'nullable|boolean'
        ];
    }

    public function getValidatorInstance()
    {
        $this->prepareForValidation();

        return parent::getValidatorInstance();
    }
}
