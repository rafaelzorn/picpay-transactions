<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;
use App\Helpers\FormatHelper;
use Throwable;

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
     * @param ExternalNotificationServiceInterface $externalNotificationService
     *
     * @return void
     */
    public function handle(ExternalNotificationServiceInterface $externalNotificationService): void
    {
        $payer = $this->transaction->payerWallet->user;
        $payee = $this->transaction->payeeWallet->user;

        $message = trans('messages.transfer_notification', [
            'payee_full_name' => $payee->full_name,
            'payer_full_name' => $payer->full_name,
            'created_at'      => FormatHelper::formatMysqlDateTime($this->transaction->created_at),
            'value'           => FormatHelper::formatMoneyToBrl($this->transaction->value),
        ]);

        $externalNotificationService->send($payee->email, $message);
    }

    /**
     * @param Throwable $exception
     *
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        // TODO
        // print($this->attempts());
    }
}
