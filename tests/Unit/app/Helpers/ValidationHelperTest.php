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
        $validDocuments = [
            '831.865.860-46',
            '74233744073',
            '518.340.260-40',
            '62613527048',
            '200.889.220-49',
        ];

        foreach ($validDocuments as $validDocument) {
            $this->assertTrue(ValidationHelper::isValidCpf($validDocument));
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_invalid_cpf(): void
    {
        $validDocuments = [
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
        ];

        foreach ($validDocuments as $validDocument) {
            $this->assertFalse(ValidationHelper::isValidCpf($validDocument));
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_valid_cnpj(): void
    {
        $validDocuments = [
            '53.703.060/0001-37',
            '19003880000188',
            '40.114.315/0001-84',
            '45097544000151',
            '70.447.289/0001-77',
        ];

        foreach ($validDocuments as $validDocument) {
            $this->assertTrue(ValidationHelper::isValidCnpj($validDocument));
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_invalid_cpnj(): void
    {
        $validDocuments = [
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
        ];

        foreach ($validDocuments as $validDocument) {
            $this->assertFalse(ValidationHelper::isValidCnpj($validDocument));
        }
    }
}
