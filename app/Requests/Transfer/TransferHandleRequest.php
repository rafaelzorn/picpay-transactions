<?php

namespace App\Requests\Transfer;

use App\Rules\DocumentRule;
use App\Rules\ShopkeeperDoesNotTransferRule;
use App\Constants\DocumentTypeConstant;

class TransferHandleRequest
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'payer_document' => [
                'bail',
                'required',
                'numeric',
                new ShopkeeperDoesNotTransferRule(),
                new DocumentRule(DocumentTypeConstant::CPF),
            ],
            'payee_document' => [
                'bail',
                'required',
                'numeric',
                new DocumentRule(DocumentTypeConstant::CPF, DocumentTypeConstant::CNPJ)
            ],
            'value' => 'bail|required|numeric|between:0.01,1000.00',
        ];
    }

    /**
     * @return array
     */
    public static function messages(): array
    {
        return [
            'payer_document.required' => trans('validation.payer_document_required'),
            'payee_document.required' => trans('validation.payee_document_required'),
            'payer_document.numeric'  => trans('validation.payer_document_numeric'),
            'payee_document.numeric'  => trans('validation.payee_document_numeric'),
            'value.required'          => trans('validation.value_required'),
            'value.numeric'           => trans('validation.value_numeric'),
            'value.between'           => trans('validation.value_between'),
        ];
    }
}
