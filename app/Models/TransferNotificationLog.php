<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferNotificationLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',
        'to',
        'message',
        'attemps',
        'status',
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
        'transaction_id'  => 'integer',
        'to'              => 'string',
        'message'         => 'string',
        'attemps'         => 'string',
        'status'          => 'string',
        'created_at'      => 'datetime:Y-m-d H:i:s',
        'updated_at'      => 'datetime:Y-m-d H:i:s',
        'deleted_at'      => 'datetime:Y-m-d H:i:s',
    ];
}
