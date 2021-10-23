<?php

namespace App\Repositories\TransactionFailedLog;

use App\Repositories\Base\BaseRepository;
use App\Repositories\TransactionFailedLog\Contracts\TransactionFailedLogRepositoryInterface;
use App\Models\TransactionFailedLog;

class TransactionFailedLogRepository extends BaseRepository implements TransactionFailedLogRepositoryInterface
{
    /**
     * @param TransactionFailedLog $transactionFailedLog
     *
     * @return void
     */
    public function __construct(TransactionFailedLog $transactionFailedLog)
    {
        $this->model = $transactionFailedLog;
    }
}
