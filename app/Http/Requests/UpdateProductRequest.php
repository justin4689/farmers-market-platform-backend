<?php

namespace App\Http\Requests;

class UpdateProductRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price_fcfa'  => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ];
    }
}
