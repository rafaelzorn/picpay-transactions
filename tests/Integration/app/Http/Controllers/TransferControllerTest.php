<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
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
        $type  = User::TYPE_USER;
        $payer = UserHelper::createUserWithWallet($type, $this->faker()->randomFloat(2, 500, 680));
        $payee = UserHelper::createUserWithWallet($type, $this->faker()->randomFloat(2, 100, 350));
        $value = $this->faker()->randomFloat(2, 0.01, 280);

        $payerBalance = $payer->wallet->balance - $value;
        $payeeBalance = $payee->wallet->balance + $value;

        $data  = [
            'payer_document' => $payer->document,
            'payee_document' => $payee->document,
            'value'          => $value,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::OK);

        $this->seeInDatabase('wallets', ['id' => $payer->wallet->id, 'balance' => $payerBalance]);
        $this->seeInDatabase('wallets', ['id' => $payee->wallet->id, 'balance' => $payeeBalance]);

        $data = new TransferResource(Transaction::latest()->first());

        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::OK,
            'message' => trans('messages.transfer_successfully'),
            'data'    => $data,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_transfer_successful_from_user_to_shopkeeper(): void
    {
        // Arrange
        $payer = UserHelper::createUserWithWallet(User::TYPE_USER, $this->faker()->randomFloat(2, 600, 900));
        $payee = UserHelper::createUserWithWallet(User::TYPE_SHOPKEEPER, $this->faker()->randomFloat(2, 200, 500));
        $value = $this->faker()->randomFloat(2, 0.01, 350);

        $payerBalance = $payer->wallet->balance - $value;
        $payeeBalance = $payee->wallet->balance + $value;

        $data  = [
            'payer_document' => $payer->document,
            'payee_document' => $payee->document,
            'value'          => $value,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::OK);

        $this->seeInDatabase('wallets', ['id' => $payer->wallet->id, 'balance' => $payerBalance]);
        $this->seeInDatabase('wallets', ['id' => $payee->wallet->id, 'balance' => $payeeBalance]);

        $data = new TransferResource(Transaction::latest()->first());

        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::OK,
            'message' => trans('messages.transfer_successfully'),
            'data'    => $data,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_validation_that_fields_are_required(): void
    {
        // Arrange
        $validations = [
            'payer_document' => trans('validation.payer_document_required'),
            'payee_document' => trans('validation.payee_document_required'),
            'value'          => trans('validation.value_required'),
        ];

        // Act
        $response = $this->call(self::POST, self::ENDPOINT_TRANSFER);

        // Assert
        $this->assertEquals(HttpStatusConstant::UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals($this->validationMessages($validations), $response->getContent());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_validation_that_document_must_be_numeric(): void
    {
        // Arrange
        $validations = [
            'payer_document' => trans('validation.payer_document_numeric'),
            'payee_document' => trans('validation.payee_document_numeric'),
        ];

        $data  = [
            'payer_document' => $this->faker()->cpf(),
            'payee_document' => $this->faker()->cpf(),
            'value'          => $this->faker()->randomFloat(2, 0.1, 999.99),
        ];

        // Act
        $response = $this->call(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->assertEquals(HttpStatusConstant::UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals($this->validationMessages($validations), $response->getContent());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_validation_that_document_is_invalid(): void
    {
        // Arrange
        $validations = [
            'payer_document' => trans('validation.payer_document_invalid'),
            'payee_document' => trans('validation.payee_document_invalid'),
        ];

        $invalidPayerDocument = '12232856012';
        $invalidPayeeDocument = '52738170000';

        $data  = [
            'payer_document' => $invalidPayerDocument,
            'payee_document' => $invalidPayeeDocument,
            'value'          => $this->faker()->randomFloat(2, 0.1, 999.99),
        ];

        // Act
        $response = $this->call(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->assertEquals(HttpStatusConstant::UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals($this->validationMessages($validations), $response->getContent());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_validation_that_shopkeeper_cannot_transfer(): void
    {
        // Arrange
        $validations = [
            'payer_document' => trans('validation.shopkeeper_cannot_transfer'),
        ];

        $data  = [
            'payer_document' => $this->faker()->cnpj(false),
            'payee_document' => $this->faker()->cpf(false),
            'value'          => $this->faker()->randomFloat(2, 0.1, 999.99),
        ];

        // Act
        $response = $this->call(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->assertEquals(HttpStatusConstant::UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals($this->validationMessages($validations), $response->getContent());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_validation_that_value_must_be_numeric(): void
    {
        // Arrange
        $validations = [
            'value' => trans('validation.value_numeric'),
        ];

        $data  = [
            'payer_document' => $this->faker()->cpf(false),
            'payee_document' => $this->faker()->cpf(false),
            'value'          => 'invalid-value',
        ];

        // Act
        $response = $this->call(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->assertEquals(HttpStatusConstant::UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals($this->validationMessages($validations), $response->getContent());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_validation_that_value_between(): void
    {
        // Arrange
        $validations = [
            'value' => trans('validation.value_between'),
        ];

        $data  = [
            'payer_document' => $this->faker()->cpf(false),
            'payee_document' => $this->faker()->cpf(false),
            'value'          => 1000.00,
        ];

        // Act
        $response = $this->call(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->assertEquals(HttpStatusConstant::UNPROCESSABLE_ENTITY, $response->status());
        $this->assertEquals($this->validationMessages($validations), $response->getContent());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_payer_does_not_exists(): void
    {

        // Arrange
        $type  = User::TYPE_USER;
        $payer = User::factory()->type($type)->create();
        $payee = User::factory()->type($type)->create();

        $data  = [
            'payer_document' => $this->faker()->cpf(false),
            'payee_document' => $payee->document,
            'value'          => 1.00,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::UNPROCESSABLE_ENTITY);

        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::UNPROCESSABLE_ENTITY,
            'message' => trans('messages.payer_not_found'),
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_payee_does_not_exists(): void
    {
        // Arrange
        $type  = User::TYPE_USER;
        $payer = User::factory()->type($type)->create();
        $payee = User::factory()->type($type)->create();

        $data  = [
            'payer_document' => $payer->document,
            'payee_document' => $this->faker()->cpf(false),
            'value'          => 1.00,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::UNPROCESSABLE_ENTITY);

        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::UNPROCESSABLE_ENTITY,
            'message' => trans('messages.payee_not_found'),
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_not_be_able_to_transfer_to_yourself(): void
    {
        // Arrange
        $payer = User::factory()->type(User::TYPE_USER)->create();

        $data  = [
            'payer_document' => $payer->document,
            'payee_document' => $payer->document,
            'value'          => 1.00,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::UNPROCESSABLE_ENTITY);

        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::UNPROCESSABLE_ENTITY,
            'message' => trans('messages.tranfer_for_yourself'),
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_not_transfer_without_enough_balance(): void
    {
        // Arrange
        $type  = User::TYPE_USER;
        $payer = UserHelper::createUserWithWallet($type, $this->faker()->randomFloat(2, 100, 200));
        $payee = UserHelper::createUserWithWallet($type, $this->faker()->randomFloat(2, 100, 200));
        $value = $this->faker()->randomFloat(2, 300, 400);

        $data  = [
            'payer_document' => $payer->document,
            'payee_document' => $payee->document,
            'value'          => $value,
        ];

        // Act
        $this->json(self::POST, self::ENDPOINT_TRANSFER, $data);

        // Assert
        $this->seeStatusCode(HttpStatusConstant::UNPROCESSABLE_ENTITY);

        $this->seeJsonEquals([
            'code'    => HttpStatusConstant::UNPROCESSABLE_ENTITY,
            'message' => trans('messages.insufficient_balance'),
        ]);
    }
}
