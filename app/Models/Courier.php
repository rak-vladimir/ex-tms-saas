<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                                           $id
 * @property int                                           $tenant_id
 * @property string                                        $name
 * @property string                                        $phone
 * @property string                                        $vehicle_type
 * @property bool                                          $active
 *
 * @property-read \Illuminate\Database\Eloquent\Collection $assignments
 */
class Courier extends Model
{
    /** @use HasFactory<\Database\Factories\CourierFactory> */
    use HasFactory;
    use HasTenant;

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
