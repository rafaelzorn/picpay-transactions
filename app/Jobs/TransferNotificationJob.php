<?php

namespace App\Jobs;

use Exception;
use App\Models\Transaction;
use App\Repositories\TransferNotificationLog\Contracts\TransferNotificationLogRepositoryInterface;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;
use App\Helpers\FormatHelper;
use App\Constants\TransferNotificationLogStatusConstant;

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
     * @var $transferNotificationLogRepository
     */
    private $transferNotificationLogRepository;

    /**
     * @var $externalNotificationService
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
     * @param TransferNotificationLogRepositoryInterface $transferNotificationLogRepository
     *
     * @return void
     */
    public function handle(
        ExternalNotificationServiceInterface $externalNotificationService,
        TransferNotificationLogRepositoryInterface $transferNotificationLogRepository
    ): void
    {
        $this->externalNotificationService       = $externalNotificationService;
        $this->transferNotificationLogRepository = $transferNotificationLogRepository;

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

        try {
            $send = $this->externalNotificationService->send($payee->email, $message);

            $this->transferNotificationLog(
                $this->transaction->id,
                $payee->email,
                $message,
                $attemps,
                TransferNotificationLogStatusConstant::SUCCESS,
            );
        } catch (Exception $e) {
            if ($attemps === 3) {
                $this->transferNotificationLog(
                    $this->transaction->id,
                    $payee->email,
                    $message,
                    $attemps,
                    TransferNotificationLogStatusConstant::FAILED,
                );
            }

            $this->release();
        }
    }

    /**
     * @param int $transactionId
     * @param string $to
     * @param string $message
     * @param int $attemps
     * @param string $status
     *
     * @return void
     */
    private function transferNotificationLog(int $transactionId, string $to, string $message, int $attemps, string $status): void
    {
        $this->transferNotificationLogRepository->updateOrCreate(
            ['transaction_id' => $transactionId],
            ['to' => $to, 'message' => $message, 'attemps' => $attemps, 'status' => $status]
        );
    }
}
