<?php

use App\Helpers\ValidationHelper;

class ValidationHelperTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function should_return_valid_cpf(): void
    {
        $validDocument = $this->faker()->cpf(false);

        $this->assertTrue(ValidationHelper::isValidCpf($validDocument));
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_invalid_cpf(): void
    {
        $invalidDocuments = [
            '00000000000',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
            '66666666666',
            '77777777777',
            '88888888888',
            '99999999999',
            '608.248.510-68',
            '922.415.190-07',
            '045.063.090-34',
            '045.023.010-30',
        ];

        foreach ($invalidDocuments as $invalidDocument) {
            $this->assertFalse(ValidationHelper::isValidCpf($invalidDocument));
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_valid_cnpj(): void
    {
        $validDocument = $this->faker()->cnpj(false);

        $this->assertTrue(ValidationHelper::isValidCnpj($validDocument));
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_invalid_cpnj(): void
    {
        $invalidDocuments = [
            '00000000000000',
            '11111111111111',
            '22222222222222',
            '33333333333333',
            '44444444444444',
            '55555555555555',
            '66666666666666',
            '77777777777777',
            '88888888888888',
            '999999999999999',
            '74.831.692/0001-07',
            '21.174.646/0001-45',
            '33.671.691/0001-30',
            '31.641.621/0001-20',
        ];

        foreach ($invalidDocuments as $invalidDocument) {
            $this->assertFalse(ValidationHelper::isValidCnpj($invalidDocument));
        }
    }
}
