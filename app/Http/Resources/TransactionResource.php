<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'farmer'               => $this->whenLoaded('farmer', fn () => [
                'id'         => $this->farmer->id,
                'identifier' => $this->farmer->identifier,
                'firstname'  => $this->farmer->firstname,
                'lastname'   => $this->farmer->lastname,
            ]),
            'operator'             => $this->whenLoaded('operator', fn () => [
                'id'   => $this->operator->id,
                'name' => $this->operator->name,
            ]),
            'items'                => TransactionItemResource::collection($this->whenLoaded('items')),
            'total_fcfa'           => $this->total_fcfa,
            'payment_method'       => $this->payment_method,
            'interest_rate'        => $this->interest_rate,
            'interest_amount_fcfa' => $this->interest_amount_fcfa,
            'debt'                 => $this->whenLoaded('debt', fn () => $this->debt ? [
                'id'             => $this->debt->id,
                'amount_fcfa'    => $this->debt->amount_fcfa,
                'remaining_fcfa' => $this->debt->remaining_fcfa,
                'status'         => $this->debt->status,
            ] : null),
            'created_at'           => $this->created_at->toISOString(),
        ];
    }
}
