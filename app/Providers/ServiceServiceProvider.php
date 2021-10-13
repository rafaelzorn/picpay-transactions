<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use App\Services\Transfer\TransferService;
use App\Services\Transfer\Contracts\TransferServiceInterface;

class ServiceServiceProvider extends BaseServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(TransferServiceInterface::class, TransferService::class);
    }
}
