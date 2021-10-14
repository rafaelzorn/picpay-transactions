<?php

namespace App\Services\ExternalAuthorizer;

use Illuminate\Support\Facades\Http;
use App\Constants\ExternalAuthorizerConstant;
use App\Services\ExternalAuthorizer\Contracts\ExternalAuthorizerServiceInterface;

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
            return false;
        }

        return true;
    }
}
