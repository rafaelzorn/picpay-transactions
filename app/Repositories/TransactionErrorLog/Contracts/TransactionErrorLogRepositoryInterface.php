<?php

namespace App\Repositories\TransactionErrorLog\Contracts;

use Exception;
use App\Repositories\Base\Contracts\BaseRepositoryInterface;

interface TransactionErrorLogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array $information
     * @param Exception $e
     *
     * @return void
     */
    public function saveLog(Exception $e, array $information = []): void;
}
