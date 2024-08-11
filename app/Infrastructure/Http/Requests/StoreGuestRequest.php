<?php

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Propaganistas\LaravelPhone\Rules\Phone;

class StoreGuestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => 'required|email|unique:guest,email',
            'phone'      => ['required', new Phone, 'unique:guest,phone'],
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => ['required' => 'First name is require'],
            'last_name.required' => ['required' => 'Last name is require'],
            'email.required' => ['required' => 'Email is require'],
            'email.email' => ['email' => 'Email is invalid format'],
            'email.unique' => ['unique' => 'Email is already exist'],
            'phone.required' => ['required' => 'Phone is require'],
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
