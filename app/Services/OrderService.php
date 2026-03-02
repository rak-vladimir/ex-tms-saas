<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Assignment;
use App\Models\Courier;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(array $data): Order
    {
        $tenant = app(Tenant::class);

        return DB::transaction(function () use ($data, $tenant) {
            if (! empty($data['external_id'])) {
                $order = Order::firstWhere([
                    'tenant_id'   => $tenant->id,
                    'external_id' => $data['external_id'],
                ]);
                if ($order) {
                    return $order;
                }
            }

            $order = Order::create([
                ...$data,
                'tenant_id' => $tenant->id,
                'status'    => OrderStatus::NEW,
            ]);

            $this->addStatusHistory($order, OrderStatus::NEW);

            return $order;
        });
    }

    public function assignCourier(Order $order, int $courierId): Assignment
    {
        $courier = Courier::findOrFail($courierId);

        if ($courier->tenant_id !== $order->tenant_id) {
            throw ValidationException::withMessages([
                'courier_id' => 'Courier does not belong to tenant'
            ]);
        }

        if ($order->assignment) {
            throw ValidationException::withMessages([
                'order' => 'Order already assigned'
            ]);
        }

        return DB::transaction(function () use ($order, $courier) {
            $assignment = Assignment::create([
                'tenant_id'  => $order->tenant_id,
                'order_id'   => $order->id,
                'courier_id' => $courier->id,
            ]);

            $this->changeStatus($order, OrderStatus::ASSIGNED);

            return $assignment;
        });
    }

    public function changeStatus(Order $order, OrderStatus $newStatus, array $meta = []): Order
    {
        $this->validateTransition($order->status, $newStatus);

        return DB::transaction(function () use ($order, $newStatus, $meta) {
            $order->update([
                'status' => $newStatus,
            ]);

            $this->addStatusHistory($order, $newStatus, $meta);

            return $order;
        });
    }

    private function addStatusHistory(Order $order, OrderStatus $status, array $meta = []): void
    {
        $order->statusHistories()->create([
            'tenant_id' => $order->tenant_id,
            'status'    => $status,
            'meta'      => $meta,
        ]);
    }

    private function validateTransition(OrderStatus $from, OrderStatus $to): void
    {
        if (! $from->canTransitionTo($to)) {
            throw ValidationException::withMessages([
                'status' => "Invalid transition from {$from->value} to {$to->value}"
            ]);
        }
    }
}
