<?php

namespace App\Repositories\TransactionLog;

use App\Repositories\Base\BaseRepository;
use App\Repositories\TransactionLog\Contracts\TransactionLogRepositoryInterface;
use App\Models\TransactionLog;

class TransactionLogRepository extends BaseRepository implements TransactionLogRepositoryInterface
{
    /**
     * @param TransactionLog $transactionLog
     *
     * @return void
     */
    public function __construct(TransactionLog $transactionLog)
    {
        $this->model = $transactionLog;
    }
}
