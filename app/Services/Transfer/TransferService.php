<?php

namespace App\Services\Transfer;

use Exception;
use App\Services\Transfer\Contracts\TransferServiceInterface;
use App\Services\Transfer\TransferValidate;
use App\Exceptions\TransferException;
use App\Constants\HttpStatusConstant;

class TransferService implements TransferServiceInterface
{
    /**
     * @var $transferValidate
     */
    private $transferValidate;

    /**
     * @param TransferValidate $transferValidate
     *
     * @return void
     */
    public function __construct(TransferValidate $transferValidate)
    {
        $this->transferValidate = $transferValidate;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function handle(array $data): array
    {
        try {
            $this->transferValidate->validate($data);

            return [
                'code'    => HttpStatusConstant::OK,
                'message' => trans('messages.transfer_successfully'),
            ];
        } catch (Exception $e) {
            switch (get_class($e)) {
                case TransferException::class:
                    return ['code' => $e->getCode(), 'message' => $e->getMessage()];
                default:
                    return [
                        'code'    => HttpStatusConstant::INTERNAL_SERVER_ERROR,
                        'message' => trans('messages.error_transfer'),
                    ];
            }
        }
    }
}
