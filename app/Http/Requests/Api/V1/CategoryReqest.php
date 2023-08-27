<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CategoryReqest extends FormRequest
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
            'title' => 'required|string|max:255'
        ];
    }

    protected function failedValidation(Validator $validator) {

        $response = response()->json([
            'success' => false,
            'data' => [],
            'error' => 'Invalid request',
            'errors' => $validator->errors(),
            "extra" => []
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
