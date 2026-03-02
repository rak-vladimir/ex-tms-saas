<?php

namespace App\Models\Scopes;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Do that because helper tenant() don't work in queue
        $tenant = app(CurrentTenant::class);

        if ($tenantId = $tenant->id()) {
            $builder->where(
                $model->getTable() . '.tenant_id',
                $tenantId
            );
        }
    }
}
