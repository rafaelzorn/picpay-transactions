<?php

namespace App\Services\Transfer;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Transaction as TransactionModel;
use App\Models\TransactionFailedLog;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Repositories\TransactionFailedLog\Contracts\TransactionFailedLogRepositoryInterface;
use App\Services\Transfer\Contracts\TransferServiceInterface;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;
use App\Services\Transfer\Transaction;
use App\Services\Transfer\TransferValidateData;
use App\Constants\HttpStatusConstant;
use App\Constants\EnvironmentConstant;
use App\Exceptions\TransferValidateDataException;
use App\Exceptions\ExternalAuthorizerException;
use App\Jobs\TransferNotificationJob;
use App\Resources\TransferResource;

class TransferService implements TransferServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var TransactionFailedLogRepositoryInterface
     */
    private $transactionFailedLogRepository;

    /**
     * @var ExternalAuthorizerServiceInterface
     */
    private $externalAuthorizerService;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var TransferValidateData
     */
    private $transferValidateData;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param TransactionFailedLogRepositoryInterface $transactionFailedLogRepository
     * @param ExternalAuthorizerServiceInterface $externalAuthorizerService
     * @param Transaction $transaction
     * @param TransferValidateData $transferValidateData
     *
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        TransactionFailedLogRepositoryInterface $transactionFailedLogRepository,
        ExternalAuthorizerServiceInterface $externalAuthorizerService,
        Transaction $transaction,
        TransferValidateData $transferValidateData
    )
    {
        $this->userRepository                 = $userRepository;
        $this->transactionFailedLogRepository = $transactionFailedLogRepository;
        $this->externalAuthorizerService      = $externalAuthorizerService;
        $this->transaction                    = $transaction;
        $this->transferValidateData           = $transferValidateData;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function handle(array $data): array
    {
        try {
            $this->transferValidateData->validate($data);

            $payer = $this->userRepository->findByAttribute('document', $data['payer_document']);
            $payee = $this->userRepository->findByAttribute('document', $data['payee_document']);

            $this->transaction
                ->setOperation(TransactionModel::OPERATION_TRANSFER)
                ->setPayerWallet($payer->wallet)
                ->setPayeeWallet($payee->wallet)
                ->setValue($data['value'])
                ->requested();

            DB::beginTransaction();

            $this->transaction->withdrawWalletPayer();
            $this->transaction->depositWalletPayee();

            $this->externalAuthorizerService->isAuthorized();

            DB::commit();

            $this->transaction->completed();

            $this->dispatch($this->transaction->get());

            $data = new TransferResource($this->transaction->get());

            return [
                'code'    => HttpStatusConstant::OK,
                'message' => trans('messages.transfer_successfully'),
                'data'    => $data,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            $this->transactionFailed($data, $e, $this->transaction);

            switch (get_class($e)) {
                case TransferValidateDataException::class || ExternalAuthorizerException::class:
                    return ['code' => $e->getCode(), 'message' => $e->getMessage()];
                default:
                    return [
                        'code'    => HttpStatusConstant::INTERNAL_SERVER_ERROR,
                        'message' => trans('messages.error_transfer'),
                    ];
            }
        }
    }

    /**
     * @param array $data
     * @param Exception $e
     * @param Transaction $transaction
     *
     * @return void
     */
    private function transactionFailed(array $data, Exception $e, Transaction $transaction): void
    {
        $transactionId = null;

        if (!is_null($transaction)) {
            $transactionId = $transaction->get()->id;

            $transaction->chargeback();
        }

        $this->transactionFailedLogRepository->create([
            'transaction_id'    => $transactionId,
            'payer_document'    => $data['payer_document'],
            'payee_document'    => $data['payee_document'],
            'value'             => $data['value'],
            'operation'         => TransactionFailedLog::OPERATION_TRANSFER,
            'exception_message' => $e->getMessage(),
            'exception_trace'   => $e->getTraceAsString(),
        ]);
    }

    /**
     * @param TransactionModel $transaction
     *
     * @return void
     */
    private function dispatch(TransactionModel $transaction): void
    {
        $job = new TransferNotificationJob($transaction);

        if (config('app.app_env') == EnvironmentConstant::LOCAL) {
            $job->delay(Carbon::now()->addSeconds(10));
        }

        dispatch($job);
    }
}
