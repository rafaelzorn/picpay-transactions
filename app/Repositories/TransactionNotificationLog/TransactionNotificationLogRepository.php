<?php

namespace App\Repositories\TransactionNotificationLog;

use App\Repositories\Base\BaseRepository;
use App\Repositories\TransactionNotificationLog\Contracts\TransactionNotificationLogRepositoryInterface;
use App\Models\TransactionNotificationLog;

class TransactionNotificationLogRepository extends BaseRepository implements TransactionNotificationLogRepositoryInterface
{
    /**
     * @param TransactionNotificationLog $transactionNotificationLog
     *
     * @return void
     */
    public function __construct(TransactionNotificationLog $transactionNotificationLog)
    {
        $this->model = $transactionNotificationLog;
    }
}
