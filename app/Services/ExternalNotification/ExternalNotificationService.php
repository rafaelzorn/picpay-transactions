<?php

namespace App\Services\ExternalNotification;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ExternalNotificationException;
use App\Constants\ExternalNotificationConstant;
use App\Services\ExternalNotification\Contracts\ExternalNotificationServiceInterface;

class ExternalNotificationService implements ExternalNotificationServiceInterface
{
    /**
     * @return bool
     */
    public function send(): bool
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
