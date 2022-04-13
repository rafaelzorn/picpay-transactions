<?php

namespace App\Http\Resources\Transfer;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\FormatHelper;

class TransferResource extends JsonResource
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'payer_document' => $this->payerWallet->user->document,
            'payee_document' => $this->payeeWallet->user->document,
            'value'          => $this->value,
            'operation'      => trans('transaction-operation.' . $this->operation),
            'status'         => trans('transaction-status.' . $this->status),
            'created_at'     => FormatHelper::formatMysqlDateTime($this->created_at),
        ];
    }
}
