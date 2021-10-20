<?php

namespace App\Resources;

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
        $transaction = $this->get();

        return [
            'payer_document' => $transaction->payerWallet->user->document,
            'payee_document' => $transaction->payeeWallet->user->document,
            'value'          => $transaction->value,
            'status'         => trans('transaction-status.' . $transaction->status),
            'created_at'     => FormatHelper::formatMysqlDateTime($transaction->created_at),
        ];
    }
}
