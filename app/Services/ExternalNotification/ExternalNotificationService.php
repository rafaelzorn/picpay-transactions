<?php

namespace App\Services\ExternalNotification;

use Illuminate\Support\Facades\Http;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;
use App\Constants\ExternalNotificationConstant;
use App\Exceptions\ExternalNotificationException;

class ExternalNotificationService implements ExternalNotificationServiceInterface
{
    /**
     * @param string $to
     * @param string $message
     *
     * @return bool
     */
    public function send(string $to, string $message): bool
    {
        $externalNotificationEndpoint = getenv('EXTERNAL_NOTIFICATION_ENDPOINT');
        $response                     = Http::get($externalNotificationEndpoint);

        if (
            $response->serverError() ||
            $response->failed() ||
            $response->clientError() ||
            $response->json()['message'] !== ExternalNotificationConstant::SENT
        ) {
            throw new ExternalNotificationException(trans('messages.external_notification_error'));
        }

        return true;
    }
}
