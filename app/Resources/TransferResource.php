<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        ];
    }
}
