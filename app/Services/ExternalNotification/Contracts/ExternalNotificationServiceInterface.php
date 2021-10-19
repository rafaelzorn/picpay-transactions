<?php

namespace App\Services\ExternalNotification\Contracts;

interface ExternalNotificationServiceInterface
{
    /**
     * @param string $to
     * @param string $message
     *
     * @return bool
     */
    public function send(string $to, string $message): bool;
}
