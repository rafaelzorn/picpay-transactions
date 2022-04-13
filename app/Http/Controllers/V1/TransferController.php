<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\TransferHandleRequest;
use App\Services\Transfer\Contracts\TransferServiceInterface;

class TransferController extends Controller
{
    /**
     * @var TransferServiceInterface
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
    public function handle(Request $request): JsonResponse
    {
        $this->validate(
            $request,
            TransferHandleRequest::rules(),
            TransferHandleRequest::messages(),
        );

        $response = $this->transferService->handle($request->all());

        return $this->responseAdapter($response);
    }
}
