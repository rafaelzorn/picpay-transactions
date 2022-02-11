<?php

namespace App\Services\ExternalNotification;

use Illuminate\Support\Facades\Http;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;
use App\Exceptions\ExternalNotification\ExternalNotificationException;

class ExternalNotificationService implements ExternalNotificationServiceInterface
{
    const SENT = 'Enviado';

    /**
     * @param string $to
     * @param string $message
     *
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        $externalNotificationEndpoint = config('services.notification.endpoint');
        $response                     = Http::get($externalNotificationEndpoint);

        if (
            $response->serverError() ||
            $response->failed() ||
            $response->clientError() ||
            $response->json()['message'] !== self::SENT
        ) {
            throw new ExternalNotificationException(trans('messages.external_notification_error'));
        }

        return true;
    }
}
