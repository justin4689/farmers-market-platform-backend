<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id'      => $this->product_id,
            'product_name'    => $this->whenLoaded('product', fn () => $this->product->name),
            'quantity'        => $this->quantity,
            'unit_price_fcfa' => $this->unit_price_fcfa,
            'subtotal_fcfa'   => $this->quantity * $this->unit_price_fcfa,
        ];
    }
}
