<?php

namespace App\Repositories\Eloquent;

use App\Models\TransactionItem;
use App\Repositories\Interfaces\TransactionItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransactionItemRepository implements TransactionItemRepositoryInterface
{
    public function __construct(private readonly TransactionItem $model) {}

    public function createMany(int $transactionId, array $items): Collection
    {
        $created = new Collection();
        foreach ($items as $item) {
            $created->push($this->model->create(array_merge($item, ['transaction_id' => $transactionId])));
        }
        return $created;
    }

    public function findByTransaction(int $transactionId): Collection
    {
        return $this->model->where('transaction_id', $transactionId)->with('product')->get();
    }
}
