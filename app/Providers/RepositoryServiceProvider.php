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
use App\Repositories\TransactionFailedLog\TransactionFailedLogRepository;
use App\Repositories\TransactionFailedLog\Contracts\TransactionFailedLogRepositoryInterface;
use App\Repositories\TransactionNotificationLog\TransactionNotificationLogRepository;
use App\Repositories\TransactionNotificationLog\Contracts\TransactionNotificationLogRepositoryInterface;

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
        $this->app->bind(TransactionFailedLogRepositoryInterface::class, TransactionFailedLogRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(
            TransactionNotificationLogRepositoryInterface::class,
            TransactionNotificationLogRepository::class,
        );
    }
}
