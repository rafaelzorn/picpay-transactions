<?php

namespace App\Repositories\Wallet;

use App\Repositories\Base\BaseRepository;
use App\Repositories\Wallet\Contracts\WalletRepositoryInterface;
use App\Models\Wallet;

class WalletRepository extends BaseRepository implements WalletRepositoryInterface
{
    /**
     * @param Wallet $wallet
     *
     * @return void
     */
    public function __construct(Wallet $wallet)
    {
        $this->model = $wallet;
    }
}
