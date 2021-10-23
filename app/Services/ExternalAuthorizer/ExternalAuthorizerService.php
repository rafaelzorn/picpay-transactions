<?php

namespace App\Services\ExternalAuthorizer;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ExternalAuthorizerException;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;
use App\Constants\HttpStatusConstant;

class ExternalAuthorizerService implements ExternalAuthorizerServiceInterface
{
    const AUTHORIZED = 'Autorizado';

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        $externalAuthorizerEndpoint = config('services.authorizer.endpoint');
        $response                   = Http::get($externalAuthorizerEndpoint);

        if (
            $response->serverError() ||
            $response->failed() ||
            $response->clientError() ||
            $response->json()['message'] !== self::AUTHORIZED
        ) {
            throw new ExternalAuthorizerException(
                trans('messages.external_authenticator_error'),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }

        return true;
    }
}
