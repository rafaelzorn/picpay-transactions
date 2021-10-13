<?php

namespace App\Services\Transfer\Contracts;

interface TransferServiceInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function handle(array $data): array;
}
