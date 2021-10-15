<?php

namespace App\Repositories\Transaction;

use App\Repositories\Base\BaseRepository;
use App\Repositories\Transaction\Contracts\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * @param Transaction $transaction
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }
}
