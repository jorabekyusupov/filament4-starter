<?php

namespace Modules\Order\Models;


use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Order\Policies\OrderPolicy;

#[UsePolicy(OrderPolicy::class)]
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'deleted_at',
        'created_by',
        'updated_by',
        'status',
    ];

    protected $casts = [
                'status' => 'boolean'
    ];


    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class);
    }

}