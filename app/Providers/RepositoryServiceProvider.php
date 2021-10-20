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
use App\Repositories\TransactionLog\TransactionLogRepository;
use App\Repositories\TransactionLog\Contracts\TransactionLogRepositoryInterface;
use App\Repositories\TransferNotificationLog\TransferNotificationLogRepository;
use App\Repositories\TransferNotificationLog\Contracts\TransferNotificationLogRepositoryInterface;

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
        $this->app->bind(TransactionLogRepositoryInterface::class, TransactionLogRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(
            TransferNotificationLogRepositoryInterface::class,
            TransferNotificationLogRepository::class,
        );
    }
}
