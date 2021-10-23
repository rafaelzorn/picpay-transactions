<?php

namespace App\Services\Transfer;

use Exception;
use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Exceptions\TransferValidateDataException;
use App\Constants\HttpStatusConstant;

class TransferValidateData
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function validate(array $data): void
    {
        $this->payerExists($data['payer_document']);
        $this->payeeExists($data['payee_document']);
        $this->transferIsNotForYourself($data['payer_document'], $data['payee_document']);
        $this->isNotShopkeeper($data['payer_document']);
        $this->payerHasEnoughBalance($data['payer_document'], $data['value']);
    }

    /**
     * @param string $payerDocument
     *
     * @return bool
     */
    private function payerExists(string $payerDocument): ?bool
    {
        $payerDoesNotExists = !$this->userRepository->findByAttribute('document', $payerDocument);

        if ($payerDoesNotExists) {
            throw new TransferValidateDataException(
                trans('messages.payer_not_found'),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        return true;
    }

    /**
     * @param string $payeeDocument
     *
     * @return bool
     */
    private function payeeExists(string $payeeDocument): ?bool
    {
        $payeeDoesNotExists = !$this->userRepository->findByAttribute('document', $payeeDocument);

        if ($payeeDoesNotExists) {
            throw new TransferValidateDataException(
                trans('messages.payee_not_found'),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        return true;
    }

    /**
     * @param string $payerDocument
     * @param string $payeeDocument
     *
     * @return bool
     */
    private function transferIsNotForYourself(string $payerDocument, string $payeeDocument): ?bool
    {
        if ($payerDocument === $payeeDocument) {
            throw new TransferValidateDataException(
                trans('messages.tranfer_for_yourself'),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        return true;
    }

    /**
     * @param string $payerDocument
     *
     * @return bool
     */
    private function isNotShopkeeper(string $payerDocument): ?bool
    {
        $payer = $this->userRepository->findByAttribute('document', $payerDocument);

        if ($payer->type == User::TYPE_SHOPKEEPER) {
            throw new TransferValidateDataException(
                trans('messages.shopkeeper_cannot_transfer'),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        return true;
    }

    /**
     * @param string $payerDocument
     * @param float $value
     *
     * @return bool
     */
    private function payerHasEnoughBalance(string $payerDocument, float $value): ?bool
    {
        $payer = $this->userRepository->findByAttribute('document', $payerDocument);

        if ($payer->wallet->balance < $value) {
            throw new TransferValidateDataException(
                trans('messages.insufficient_balance'),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        return true;
    }
}
