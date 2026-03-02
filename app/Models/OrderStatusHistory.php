<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                        $id
 * @property int                        $tenant_id
 * @property int                        $order_id
 * @property OrderStatus                $status
 * @property array|null                 $meta
 * @property \Illuminate\Support\Carbon $created_at
 */
class OrderStatusHistory extends Model
{
    /** @use HasFactory<\Database\Factories\OrderStatusHistoryFactory> */
    use HasFactory;
    use HasTenant;

    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'status' => OrderStatus::class,
        'meta'   => 'array'
    ];
}
