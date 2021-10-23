<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Models\User;
use App\Resources\TransferResource;
use App\Constants\HttpStatusConstant;

class TransferControllerTest extends TestCase
{
    use DatabaseMigrations;

    private const POST               = 'POST';
    private const ENDPOINT_TRANSFER  = '/api/v1/transactions/transfer';

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
    public function should_return_transfer_successful_from_user_to_user(): void
    {
        // Arrange
        $payer = UserHelper::createUserWithWallet(User::TYPE_USER, 100.00);
        $payee = UserHelper::createUserWithWallet(User::TYPE_USER, 100.00);

        $data  = [
            'payer_document' => $payer->document,
            'payee_document' => $payee->document,
            'value'          => 1.00,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::OK);

        /* TODO
        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::OK,
            'message' => trans('messages.transfer_successfully'),
        ]);
        */

        $this->seeInDatabase('wallets', ['id' => $payer->wallet->id, 'balance' => 99]);
        $this->seeInDatabase('wallets', ['id' => $payee->wallet->id, 'balance' => 101]);
    }
}
