<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use App\Support\CurrentTenant;

trait HasTenant
{
    public static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (! $model->tenant_id) {
                $model->tenant_id = app(CurrentTenant::class)->id();
            }
        });
    }
}
