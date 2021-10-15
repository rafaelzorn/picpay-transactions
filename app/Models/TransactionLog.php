<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'message',
        'trace',
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
        'transaction_id' => 'integer',
        'message'        => 'string',
        'trace'          => 'string',
        'created_at'     => 'datetime:Y-m-d H:i:s',
        'updated_at'     => 'datetime:Y-m-d H:i:s',
        'deleted_at'     => 'datetime:Y-m-d H:i:s',
    ];
}
