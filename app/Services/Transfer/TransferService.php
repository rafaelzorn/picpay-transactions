<?php

namespace App\Services\Transfer;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Repositories\TransactionLog\Contracts\TransactionLogRepositoryInterface;
use App\Services\Transfer\Contracts\TransferServiceInterface;
use App\Services\Transfer\TransferValidate;
use App\Exceptions\TransferValidateException;
use App\Constants\HttpStatusConstant;

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
     * @var $transactionLogRepository
     */
    private $transactionLogRepository;

    /**
     * @param TransferValidate $transferValidate
     * @param UserRepositoryInterface $userRepository
     * @param TransactionLogRepositoryInterface $transactionLogRepository
     *
     * @return void
     */
    public function __construct(
        TransferValidate $transferValidate,
        UserRepositoryInterface $userRepository,
        TransactionLogRepositoryInterface $transactionLogRepository
    )
    {
        $this->transferValidate         = $transferValidate;
        $this->userRepository           = $userRepository;
        $this->transactionLogRepository = $transactionLogRepository;
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

            DB::beginTransaction();

            $payer = $this->userRepository->findByAttribute('document', $data['payer_document']);
            $payee = $this->userRepository->findByAttribute('document', $data['payee_document']);

            dd($payer->wallet);

            DB::commit();

            return [
                'code'    => HttpStatusConstant::OK,
                'message' => trans('messages.transfer_successfully'),
            ];
        } catch (Exception $e) {
            DB::rollBack();

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
}
