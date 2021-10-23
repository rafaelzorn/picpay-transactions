<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Wallet;

class Transaction extends Model
{
    /**
     * Operations
     */
    const OPERATION_TRANSFER = 'transfer';

    /**
     * Status
     */
    const STATUS_REQUESTED  = 'requested';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CHARGEBACK = 'chargeback';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payer_wallet_id',
        'payee_wallet_id',
        'value',
        'operation',
        'status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'payer_wallet_id',
        'payee_wallet_id',
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
        'payer_wallet_id' => 'integer',
        'payee_wallet_id' => 'integer',
        'value'           => 'decimal:2',
        'operation'       => 'string',
        'status'          => 'string',
        'created_at'      => 'datetime:Y-m-d H:i:s',
        'updated_at'      => 'datetime:Y-m-d H:i:s',
        'deleted_at'      => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return BelongsTo
     */
    public function payerWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class,  'payer_wallet_id');
    }

    /**
     * @return BelongsTo
     */
    public function payeeWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class,  'payee_wallet_id');
    }
}
