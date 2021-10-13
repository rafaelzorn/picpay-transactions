<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Requests\TransferRequest;
use App\Services\Transfer\Contracts\TransferServiceInterface;

class TransactionsController extends Controller
{
    /**
     * @var $transferService
     */
    private $transferService;

    /**
     * @param TransferServiceInterface $transferService
     *
     * @return void
     */
    public function __construct(TransferServiceInterface $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function transfer(Request $request): JsonResponse
    {
        $this->validate(
            $request,
            TransferRequest::rules(),
            TransferRequest::messages(),
        );

        $response = $this->transferService->handle($request->all());

        return $this->responseAdapter($response);
    }
}
