<?php

namespace App\Services\Transfer;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Repositories\TransactionErrorLog\Contracts\TransactionErrorLogRepositoryInterface;
use App\Services\Transfer\Contracts\TransferServiceInterface;
use App\Services\Transfer\TransferValidate;
use App\Exceptions\TransferValidateException;
use App\Constants\HttpStatusConstant;
use App\Services\Transfer\Transaction;
use App\Resources\TransferResource;

class TransferService implements TransferServiceInterface
{
    /**
     * @var $transferValidate
     */
    private $transferValidate;

    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * @var $transactionErrorLog
     */
    private $transactionErrorLog;

    /**
     * @var $transaction
     */
    private $transaction;

    /**
     * @param TransferValidate $transferValidate
     * @param UserRepositoryInterface $userRepository
     * @param TransactionErrorLogRepositoryInterface $transactionErrorLog
     * @param Transaction $transaction
     *
     * @return void
     */
    public function __construct(
        TransferValidate $transferValidate,
        UserRepositoryInterface $userRepository,
        TransactionErrorLogRepositoryInterface $transactionErrorLog,
        Transaction $transaction
    )
    {
        $this->transferValidate    = $transferValidate;
        $this->userRepository      = $userRepository;
        $this->transaction         = $transaction;
        $this->transactionErrorLog = $transactionErrorLog;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function handle(array $data): array
    {
        try {
            $this->transferValidate->validate($data);

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

            DB::commit();

            $this->transaction->completed();

            $transaction = new TransferResource($this->transaction);

            return [
                'code'    => HttpStatusConstant::OK,
                'message' => trans('messages.transfer_successfully'),
                'data'    => $transaction,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            $this->failed($data, $e, $this->transaction);

            switch (get_class($e)) {
                case TransferValidateException::class:
                    return ['code' => $e->getCode(), 'message' => $e->getMessage()];
                default:
                    return [
                        'code'    => HttpStatusConstant::INTERNAL_SERVER_ERROR,
                        'message' => trans('messages.error_transfer'),
                    ];
            }
        }
    }

    private function failed(array $data, Exception $e, Transaction $transaction)
    {
        $information = [];

        if (!is_null($transaction->get())) {
            $information = ['transaction_id' => $transaction->get()->id];

            $transaction->failed();
        }

        $information['payer_document'] = $data['payer_document'];
        $information['payee_document'] = $data['payee_document'];
        $information['value']          = $data['value'];

        $this->transactionErrorLog->saveLog($e, $information);
    }
}
