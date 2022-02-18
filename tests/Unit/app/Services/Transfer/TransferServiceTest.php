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

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var TransactionFailedLogRepositoryInterface
     */
    private $transactionFailedLogRepository;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var TransferValidateData
     */
    private $transferValidateData;

    public function setUp(): void
    {
        parent::setUp();

        User::flushEventListeners();

        $this->userRepository                 = $this->app->make(UserRepositoryInterface::class);
        $this->transactionFailedLogRepository = $this->app->make(TransactionFailedLogRepositoryInterface::class);
        $this->transaction                    = $this->app->make(Transaction::class);
        $this->transferValidateData           = $this->app->make(TransferValidateData::class);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_rollback_transaction_when_external_authorization_exception_occurs(): void
    {
        $externalAuthorizerService = $this->createMock(ExternalAuthorizerServiceInterface::class);

        $payer = UserHelper::createUserWithWallet(User::TYPE_USER, $this->faker()->randomFloat(2, 600, 900));
        $payee = UserHelper::createUserWithWallet(User::TYPE_USER, $this->faker()->randomFloat(2, 200, 500));
        $value = $this->faker()->randomFloat(2, 0.01, 350);

        $payerBalance = $payer->wallet->balance;
        $payeeBalance = $payee->wallet->balance;

        $externalAuthorizerService->method('isAuthorized')
                                  ->will($this->throwException(new ExternalAuthorizerException));

        $transferService = new TransferService(
            $this->userRepository,
            $this->transactionFailedLogRepository,
            $externalAuthorizerService,
            $this->transaction,
            $this->transferValidateData,
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
