<?php

namespace App\Requests;

class TransferRequest
{
    /**
     * @return array
     */
    public static function rules(): array
    {
        return [
            'payer_document' => 'required|numeric',
            'payee_document' => 'required|numeric',
            'value'          => 'required|numeric|between:0.01,999.99',
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
