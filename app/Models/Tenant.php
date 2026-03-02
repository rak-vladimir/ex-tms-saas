<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                                           $id
 * @property string                                        $name
 * @property string                                        $api_key
 * @property-read \Illuminate\Database\Eloquent\Collection $orders
 * @property-read \Illuminate\Database\Eloquent\Collection $couriers
 */
#[ScopedBy([TenantScope::class])]
class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\TenantFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = [
        'api_key'
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function couriers(): HasMany
    {
        return $this->hasMany(Courier::class);
    }

    public static function findByApiKey(string $key): ?self
    {
        return static::firstWhere('api_key', $key);
    }
}
