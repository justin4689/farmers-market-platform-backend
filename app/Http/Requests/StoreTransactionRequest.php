<?php

namespace App\Http\Requests;

class StoreTransactionRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farmer_id'              => ['required', 'integer', 'exists:farmers,id'],
            'payment_method'         => ['required', 'string', 'in:cash,credit'],
            'interest_rate'          => ['required_if:payment_method,credit', 'nullable', 'numeric', 'min:0', 'max:1'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'interest_rate.required_if' => 'Interest rate is required when payment method is credit.',
        ];
    }
}
