<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (! $apiKey) {
            abort(401, 'X-API-Key header is required');
        }

        $tenant = Tenant::findByApiKey($apiKey);

        if (! $tenant) {
            logger()->warning('Invalid API Key attempt', ['api_key' => $apiKey]);
            abort(403, 'Invalid API Key');
        }

        app(CurrentTenant::class)->set($tenant);

        return $next($request);
    }
}
