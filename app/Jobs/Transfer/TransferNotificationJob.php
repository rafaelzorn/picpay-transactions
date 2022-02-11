<?php

namespace App\Jobs\Transfer;

use Exception;
use App\Models\Transaction;
use App\Models\TransactionNotificationLog;
use App\Repositories\TransactionNotificationLog\Contracts\TransactionNotificationLogRepositoryInterface;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;
use App\Helpers\FormatHelper;
use App\Jobs\Job;

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
     * @var Transaction
     */
    private $transaction;

    /**
     * @var TransactionNotificationLogRepositoryInterface
     */
    private $transactionNotificationLogRepository;

    /**
     * @var ExternalNotificationServiceInterface
     */
    private $externalNotificationService;

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
     * @param TransactionNotificationLogRepositoryInterface $transactionNotificationLogRepository
     *
     * @return void
     */
    public function handle(
        ExternalNotificationServiceInterface $externalNotificationService,
        TransactionNotificationLogRepositoryInterface $transactionNotificationLogRepository
    ): void
    {
        $this->externalNotificationService          = $externalNotificationService;
        $this->transactionNotificationLogRepository = $transactionNotificationLogRepository;

        $this->send();
    }

    /**
     * @return void
     */
    private function send(): void
    {
        $payer = $this->transaction->payerWallet->user;
        $payee = $this->transaction->payeeWallet->user;

        $message = trans('messages.transfer_notification', [
            'payee_full_name' => $payee->full_name,
            'payer_full_name' => $payer->full_name,
            'created_at'      => FormatHelper::formatMysqlDateTime($this->transaction->created_at),
            'value'           => FormatHelper::formatMoneyToBrl($this->transaction->value),
        ]);

        $attemps = $this->attempts();

        $log = [
            'to'      => $payee->email,
            'message' => $message,
            'attemps' => $attemps,
        ];

        try {
            $send = $this->externalNotificationService->send($payee->email, $message);

            $log['status'] = TransactionNotificationLog::STATUS_SUCCESS;

            $this->transactionNotificationLog($this->transaction->id, $log);
        } catch (Exception $e) {

            $log['status']            = TransactionNotificationLog::STATUS_FAILED;
            $log['exception_message'] = $e->getMessage();
            $log['exception_trace']   = $e->getTraceAsString();

            $this->transactionNotificationLog($this->transaction->id, $log);

            $this->release();
        }
    }

    /**
     * @param int $transactionId
     * @param array $log
     *
     * @return void
     */
    private function transactionNotificationLog(int $transactionId, array $log): void
    {
        $this->transactionNotificationLogRepository->updateOrCreate(['transaction_id' => $transactionId], $log);
    }
}
