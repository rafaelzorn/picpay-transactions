<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Helpers\ValidationHelper;

class ShopkeeperDoesNotTransferRule implements Rule
{
    /**
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return !ValidationHelper::isValidCnpj($value);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return trans('validation.shopkeeper_cannot_transfer');
    }
}
