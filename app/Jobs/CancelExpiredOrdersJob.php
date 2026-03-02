<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Scopes\TenantScope;
use App\Services\OrderService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrdersJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(OrderService $service): void
    {
        Order::withoutGlobalScope(TenantScope::class)
            ->where('status', OrderStatus::NEW)
            ->where('created_at', '<=', now()->subMinutes(30))
            ->select(['id', 'status', 'tenant_id'])
            ->chunkById(200, function ($orders) use ($service) {
                foreach ($orders as $order) {
                    try {
                        $service->cancelByTimeout($order->id, $order->tenant_id);
                    } catch (\Throwable $e) {
                        Log::error("System auto-cancel failed", [
                            'order_id'  => $order->id,
                            'tenant_id' => $order->tenant_id,
                            'error'     => $e->getMessage()
                        ]);
                    }
                }
            });
    }
}
