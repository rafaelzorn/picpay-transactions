<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Helpers\ValidationHelper;

class DocumentRule implements Rule
{
    /**
     * @var $cpf
     */
    private $cpf;

    /**
     * @var $cnpj
     */
    private $cnpj;

    public function __construct($cpf = false, $cnpj = false)
    {
        $this->cpf  = $cpf;
        $this->cnpj = $cnpj;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $isValidCpf  = ValidationHelper::isValidCpf($value);
        $isValidCnpj = ValidationHelper::isValidCnpj($value);

        if ($this->cpf && $this->cnpj) {
            return $isValidCpf || $isValidCnpj;
        }

        if ($this->cpf) {
            return $isValidCpf;
        }

        return true;
    }

    /**
     * @return string
     */
    public function message(): ?string
    {
        if ($this->cpf && $this->cnpj) {
            return trans('validation.payee_document_invalid');
        }

        if ($this->cpf) {
            return trans('validation.payer_document_invalid');
        }
    }
}
