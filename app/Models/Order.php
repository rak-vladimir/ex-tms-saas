<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * @property int                                           $id
 * @property int                                           $tenant_id
 * @property string|null                                   $external_id
 * @property string                                        $customer_name
 * @property string                                        $phone
 * @property string                                        $pickup_address
 * @property string                                        $delivery_address
 * @property string                                        $delivery_date
 * @property OrderStatus                                   $status
 * @property \Illuminate\Support\Carbon|null               $created_at
 *
 * @property-read \App\Models\Assignment|null              $assignment
 * @property-read \App\Models\Courier|null                 $courier
 * @property-read \Illuminate\Database\Eloquent\Collection $statusHistories
 */
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\CourierFactory> */
    use HasFactory;
    use HasTenant;

    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'delivery_date' => 'date',
        'status'        => OrderStatus::class,
    ];

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function courier(): HasOneThrough
    {
        return $this->hasOneThrough(
            Courier::class,
            Assignment::class,
            'order_id',
            'id',
            'id',
            'courier_id'
        );
    }
}
