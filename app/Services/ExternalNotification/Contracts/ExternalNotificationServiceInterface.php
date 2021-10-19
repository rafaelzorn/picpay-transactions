<?php

namespace App\Services\ExternalNotification\Contracts;

interface ExternalNotificationServiceInterface
{
    /**
     * @return bool
     */
    public function send(): bool;
}
