<?php

namespace App\Services\Transfer;

use Exception;
use App\Exceptions\TransferValidateException;
use App\Constants\HttpStatusConstant;
use App\Constants\UserTypeConstant;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;

class TransferValidate
{
    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * @var $externalAuthorizerService
     */
    private $externalAuthorizerService;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param ExternalAuthorizerServiceInterface $externalAuthorizerService
     *
     * @return void
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        ExternalAuthorizerServiceInterface $externalAuthorizerService
    )
    {
        $this->userRepository            = $userRepository;
        $this->externalAuthorizerService = $externalAuthorizerService;
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
        $this->checkExternalAuthorizer();
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
            throw new TransferValidateException(
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
            throw new TransferValidateException(
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
            throw new TransferValidateException(
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

        if ($payer->type == UserTypeConstant::SHOPKEEPER) {
            throw new TransferValidateException(
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
            throw new TransferValidateException(
                trans('messages.insufficient_balance'),
                HttpStatusConstant::UNPROCESSABLE_ENTITY,
            );
        }

        return true;
    }

    /**
     * @return bool
     */
    private function checkExternalAuthorizer(): ?bool
    {
        $isNotAuthorized = !$this->externalAuthorizerService->isAuthorized();

        if ($isNotAuthorized) {
            throw new TransferValidateException(
                trans('messages.external_authenticator_error'),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }

        return true;
    }
}
