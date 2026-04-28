<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'identifier'            => $this->identifier,
            'firstname'             => $this->firstname,
            'lastname'              => $this->lastname,
            'phone_number'          => $this->phone_number,
            'credit_limit_fcfa'     => $this->credit_limit_fcfa,
            'total_outstanding_debt' => (int) ($this->total_outstanding_debt ?? 0),
            'created_at'            => $this->created_at->toISOString(),
        ];
    }
}
