<?php

namespace App\Services\ExternalAuthorizer\Contracts;

interface ExternalAuthorizerServiceInterface
{
    /**
     * @return bool
     */
    public function isAuthorized(): bool;
}
