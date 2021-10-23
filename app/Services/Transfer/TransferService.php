<?php

namespace App\Services\Transfer;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Transaction as TransactionModel;
use App\Models\TransactionFailedLog;
use App\Exceptions\TransferValidateDataException;
use App\Exceptions\ExternalAuthorizerException;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Services\Transfer\Contracts\TransferServiceInterface;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;
use App\Repositories\TransactionFailedLog\Contracts\TransactionFailedLogRepositoryInterface;
use App\Services\Transfer\TransferValidateData;
use App\Constants\HttpStatusConstant;
use App\Constants\EnvironmentConstant;
use App\Services\Transfer\Transaction;
use App\Resources\TransferResource;
use App\Helpers\FormatHelper;
use App\Jobs\TransferNotificationJob;


class TransferService implements TransferServiceInterface
{
    /**
     * @var TransferValidateData
     */
    private $transferValidateData;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var ExternalAuthorizerServiceInterface
     */
    private $externalAuthorizerService;

    /**
     * @var TransactionFailedLogRepositoryInterface
     */
    private $transactionFailedLogRepository;

    /**
     * @param TransferValidateData $transferValidateData
     * @param UserRepositoryInterface $userRepository
     * @param Transaction $transaction
     * @param ExternalAuthorizerServiceInterface $externalAuthorizerService
     * @param TransactionFailedLogRepositoryInterface $transactionFailedLogRepository
     *
     * @return void
     */
    public function __construct(
        TransferValidateData $transferValidateData,
        UserRepositoryInterface $userRepository,
        Transaction $transaction,
        ExternalAuthorizerServiceInterface $externalAuthorizerService,
        TransactionFailedLogRepositoryInterface $transactionFailedLogRepository
    )
    {
        $this->transferValidateData           = $transferValidateData;
        $this->userRepository                 = $userRepository;
        $this->transaction                    = $transaction;
        $this->externalAuthorizerService      = $externalAuthorizerService;
        $this->transactionFailedLogRepository = $transactionFailedLogRepository;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function handle(array $data): array
    {
        try {
            $data['payer_document'] = FormatHelper::onlyNumbers($data['payer_document']);
            $data['payee_document'] = FormatHelper::onlyNumbers($data['payee_document']);

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

            $this->dispatch($this->transaction);

            $data = new TransferResource($this->transaction);

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

        if (!is_null($transaction->get())) {
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
     * @param Transaction $transaction
     *
     * @return void
     */
    private function dispatch(Transaction $transaction): void
    {
        $job = new TransferNotificationJob($transaction->get());

        if (config('app.app_env') == EnvironmentConstant::LOCAL) {
            $job->delay(Carbon::now()->addSeconds(10));
        }

        dispatch($job);
    }
}
