<?php

namespace App\Observers;

use App\Models\User;
use App\Repositories\Wallet\Contracts\WalletRepositoryInterface;

class UserObserver
{
    /**
     * @var $walletRepository
     */
    private $walletRepository;

    /**
     * @param WalletRepositoryInterface $walletRepository
     *
     * @return void
     */
    public function __construct(WalletRepositoryInterface $walletRepository)
	{
        $this->walletRepository = $walletRepository;
	}

    /**
     * @param User $user
     *
     * @return void
     */
    public function created(User $user): void
    {
        $this->walletRepository->create([
            'user_id' => $user->id,
            'balance' => 0.00,
        ]);
    }
}
