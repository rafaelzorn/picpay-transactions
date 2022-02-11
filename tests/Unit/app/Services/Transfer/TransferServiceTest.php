<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\User;
use App\Services\Transfer\TransferService;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Repositories\TransactionFailedLog\Contracts\TransactionFailedLogRepositoryInterface;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;
use App\Services\Transfer\Transaction;
use App\Services\Transfer\TransferValidateData;
use App\Exceptions\ExternalAuthorizer\ExternalAuthorizerException;

class TransferServiceTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        User::flushEventListeners();
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_rollback_transaction_when_external_authorization_exception_occurs(): void
    {
        $userRepository                 = $this->app->make(UserRepositoryInterface::class);
        $transactionFailedLogRepository = $this->app->make(TransactionFailedLogRepositoryInterface::class);
        $externalAuthorizerService      = $this->createMock(ExternalAuthorizerServiceInterface::class);
        $transaction                    = $this->app->make(Transaction::class);
        $transferValidateData           = $this->app->make(TransferValidateData::class);

        $payer = UserHelper::createUserWithWallet(User::TYPE_USER, $this->faker()->randomFloat(2, 600, 900));
        $payee = UserHelper::createUserWithWallet(User::TYPE_USER, $this->faker()->randomFloat(2, 200, 500));
        $value = $this->faker()->randomFloat(2, 0.01, 350);

        $payerBalance = $payer->wallet->balance;
        $payeeBalance = $payee->wallet->balance;

        $externalAuthorizerService->method('isAuthorized')
                                  ->will($this->throwException(new ExternalAuthorizerException));

        $transferService = new TransferService(
            $userRepository,
            $transactionFailedLogRepository,
            $externalAuthorizerService,
            $transaction,
            $transferValidateData,
        );

        $data = [
            'payer_document' => $payer->document,
            'payee_document' => $payee->document,
            'value'          => $value,
        ];

        $transferService->handle($data);

        $this->seeInDatabase('wallets', ['id' => $payer->wallet->id, 'balance' => $payerBalance]);
        $this->seeInDatabase('wallets', ['id' => $payee->wallet->id, 'balance' => $payeeBalance]);
    }
}
