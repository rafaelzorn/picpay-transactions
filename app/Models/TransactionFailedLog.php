<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionFailedLog extends Model
{
    /**
     * Operations
     */
    const OPERATION_TRANSFER = 'transfer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'payer_document',
        'payee_document',
        'value',
        'operation',
        'exception_message',
        'exception_trace',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'transaction_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'transaction_id'    => 'integer',
        'payer_document'    => 'string',
        'payee_document'    => 'string',
        'value'             => 'decimal:2',
        'operation'         => 'string',
        'exception_message' => 'string',
        'exception_trace'   => 'string',
        'created_at'        => 'datetime:Y-m-d H:i:s',
        'updated_at'        => 'datetime:Y-m-d H:i:s',
        'deleted_at'        => 'datetime:Y-m-d H:i:s',
    ];
}
