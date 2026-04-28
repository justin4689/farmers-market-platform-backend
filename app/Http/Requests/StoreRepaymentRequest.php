<?php

namespace App\Http\Requests;

class StoreRepaymentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farmer_id'           => ['required', 'integer', 'exists:farmers,id'],
            'kg_received'         => ['required', 'numeric', 'min:0.001'],
            'commodity_rate_fcfa' => ['required', 'integer', 'min:1'],
        ];
    }
}
