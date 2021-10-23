<?php

namespace App\Requests;

use App\Rules\DocumentRule;
use App\Constants\DocumentTypeConstant;

class TransferHandleRequest
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'payer_document' => ['required', new DocumentRule(DocumentTypeConstant::CPF)],
            'payee_document' => [
                'required',
                new DocumentRule(DocumentTypeConstant::CPF, DocumentTypeConstant::CNPJ)
            ],
            'value' => 'required|numeric|between:0.01,999.99',
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
            'value.required'          => trans('validation.value_required'),
            'value.numeric'           => trans('validation.value_numeric'),
            'value.between'           => trans('validation.value_between'),
        ];
    }
}
