<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;

class TransferNotificationJob extends Job
{
    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var int
     */
    public $backoff = 10;

    /**
     * @var $transaction
     */
    private $transaction;

    /**
     * @param Transaction $transaction
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @return void
     */
    public function handle(ExternalNotificationServiceInterface $externalNotificationService): void
    {
        // $this->transaction

        $externalNotificationService->send();
    }
}
