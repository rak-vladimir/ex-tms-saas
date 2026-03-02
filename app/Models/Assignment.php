<?php

namespace App\Models;

use App\Models\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                        $id
 * @property int                        $tenant_id
 * @property int                        $order_id
 * @property int                        $courier_id
 * @property \Illuminate\Support\Carbon $assigned_at
 */
class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;
    use HasTenant;

    public const CREATED_AT = 'assigned_at';
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = [
        'assigned_at' => 'datetime'
    ];
}
