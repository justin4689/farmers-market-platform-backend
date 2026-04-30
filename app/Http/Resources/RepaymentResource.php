<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'farmer_id'            => $this->farmer_id,
            'kg_received'          => (float) $this->kg_received,
            'commodity_rate_fcfa'  => $this->commodity_rate_fcfa,
            'total_fcfa_credited'  => $this->total_fcfa_credited,
            'debts_affected'       => $this->whenLoaded('debts', fn () =>
                $this->debts->map(fn ($debt) => [
                    'id'                  => $debt->id,
                    'transaction_id'      => $debt->transaction_id,
                    'amount_fcfa'         => $debt->amount_fcfa,
                    'remaining_fcfa'      => $debt->remaining_fcfa,
                    'status'              => $debt->status,
                    'amount_applied_fcfa' => $debt->pivot->amount_applied_fcfa,
                ])->values()
            ),
            'created_at'           => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
