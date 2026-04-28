<?php

namespace App\Http\Requests;

class StoreFarmerRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier'        => ['required', 'string', 'max:255', 'unique:farmers,identifier'],
            'firstname'         => ['required', 'string', 'max:255'],
            'lastname'          => ['required', 'string', 'max:255'],
            'phone_number'      => ['required', 'string', 'max:50', 'unique:farmers,phone_number'],
            'credit_limit_fcfa' => ['required', 'integer', 'min:0'],
        ];
    }
}
