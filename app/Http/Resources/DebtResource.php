<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount_fcfa'    => $this->amount_fcfa,
            'remaining_fcfa' => $this->remaining_fcfa,
            'status'         => $this->status,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
