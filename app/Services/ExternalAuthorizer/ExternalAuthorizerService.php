<?php

namespace App\Services\ExternalAuthorizer;

use Illuminate\Support\Facades\Http;
use App\Exceptions\ExternalAuthorizerException;
use App\Constants\ExternalAuthorizerConstant;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;
use App\Constants\HttpStatusConstant;

class ExternalAuthorizerService implements ExternalAuthorizerServiceInterface
{
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
            $response->json()['message'] !== ExternalAuthorizerConstant::AUTHORIZED
        ) {
            throw new ExternalAuthorizerException(
                trans('messages.external_authenticator_error'),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }

        return true;
    }
}
