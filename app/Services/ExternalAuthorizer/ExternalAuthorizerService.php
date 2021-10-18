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
        $externalAuthorizerUrl = getenv('EXTERNAL_AUTHORIZER_URL');
        $response              = Http::get($externalAuthorizerUrl);

        if ($response->serverError() || $response->failed() || $response->clientError()) {
            return false;
        }

        $isNotAuthorized = $response->json()['message'] !== ExternalAuthorizerConstant::AUTHORIZED;

        if ($isNotAuthorized) {
            throw new ExternalAuthorizerException(
                trans('messages.external_authenticator_error'),
                HttpStatusConstant::UNAUTHORIZED,
            );
        }

        return true;
    }
}
