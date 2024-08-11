<?php

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdateGuestRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string',
            'last_name'  => 'string',
            'email'      => 'email|unique:guest,email',
            'phone'      => [new Phone, 'unique:guest,phone'],
            'country'    => 'string'
        ];
    }

    public function messages()
    {
        return [
            'email.email' => ['email' => 'Email is invalid format'],
            'email.unique' => ['unique' => 'Email is already exist'],
            'phone.unique' => ['unique' => 'Phone is already exist'],
            'phone' => ['phone' => ['phone' => 'Phone is invalid format']]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 400)
        );
    }
}
