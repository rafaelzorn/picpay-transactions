<?php

namespace App\Repositories\TransferNotificationLog;

use App\Repositories\Base\BaseRepository;
use App\Repositories\TransferNotificationLog\Contracts\TransferNotificationLogRepositoryInterface;
use App\Models\TransferNotificationLog;

class TransferNotificationLogRepository extends BaseRepository implements TransferNotificationLogRepositoryInterface
{
    /**
     * @param TransferNotificationLog $transferNotificationLog
     *
     * @return void
     */
    public function __construct(TransferNotificationLog $transferNotificationLog)
    {
        $this->model = $transferNotificationLog;
    }
}
