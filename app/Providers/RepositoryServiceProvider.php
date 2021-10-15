<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Base\Contracts\BaseRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use App\Repositories\Wallet\WalletRepository;
use App\Repositories\Wallet\Contracts\WalletRepositoryInterface;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Transaction\Contracts\TransactionRepositoryInterface;
use App\Repositories\TransactionErrorLog\TransactionErrorLogRepository;
use App\Repositories\TransactionErrorLog\Contracts\TransactionErrorLogRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(
            TransactionErrorLogRepositoryInterface::class,
            TransactionErrorLogRepository::class,
        );
    }
}
