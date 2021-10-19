<?php

namespace App\Services\Transfer;

use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exceptions\TransferValidateDataException;
use App\Exceptions\ExternalAuthorizerException;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Services\Transfer\Contracts\TransferServiceInterface;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;
use App\Repositories\TransactionLog\Contracts\TransactionLogRepositoryInterface;
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
     * @var $transferValidateData
     */
    private $transferValidateData;

    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * @var $transaction
     */
    private $transaction;

    /**
     * @var $externalAuthorizerService
     */
    private $externalAuthorizerService;

    /**
     * @var $transactionLogRepository
     */
    private $transactionLogRepository;

    /**
     * @param TransferValidateData $transferValidateData
     * @param UserRepositoryInterface $userRepository
     * @param Transaction $transaction
     * @param ExternalAuthorizerServiceInterface $externalAuthorizerService
     * @param TransactionLogRepositoryInterface $transactionLogRepository
     *
     * @return void
     */
    public function __construct(
        TransferValidateData $transferValidateData,
        UserRepositoryInterface $userRepository,
        Transaction $transaction,
        ExternalAuthorizerServiceInterface $externalAuthorizerService,
        TransactionLogRepositoryInterface $transactionLogRepository,
    )
    {
        $this->transferValidateData      = $transferValidateData;
        $this->userRepository            = $userRepository;
        $this->transaction               = $transaction;
        $this->externalAuthorizerService = $externalAuthorizerService;
        $this->transactionLogRepository  = $transactionLogRepository;
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

            $transaction->failed();
        }

        $this->transactionLogRepository->create([
            'transaction_id' => $transactionId,
            'payer_document' => $data['payer_document'],
            'payee_document' => $data['payee_document'],
            'value'          => $data['value'],
            'message'        => $e->getMessage(),
            'trace'          => $e->getTraceAsString(),
        ]);
    }

    /**
     * @param Transaction $transaction
     *
     * @return void
     */
    private function dispatch(Transaction $transaction)
    {
        $job = new TransferNotificationJob($transaction->get());

        if (getenv('APP_ENV') == EnvironmentConstant::LOCAL) {
            $job->delay(Carbon::now()->addSeconds(10));
        }

        dispatch($job);
    }
}
