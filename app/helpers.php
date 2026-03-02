<?php

use App\Models\Tenant;
use App\Support\CurrentTenant;

if (! function_exists('tenant')) {
    function tenant(): ?Tenant
    {
        return app(CurrentTenant::class)->get();
    }
}
