<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('assignment.courier');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('delivery_date')) {
            $query->whereDate('delivery_date', $request->date('delivery_date'));
        }

        $perPage = min(max($request->integer('per_page', 10), 1), 100);

        return response()->json(
            $query->paginate($perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, OrderService $service): JsonResponse
    {
        $validated = $request->validate([
            'external_id'      => 'nullable|string',
            'customer_name'    => 'required|string',
            'phone'            => 'required|string|max:50',
            'pickup_address'   => 'required|string',
            'delivery_address' => 'required|string',
            'delivery_date'    => 'required|date',
        ]);

        $order = $service->create($validated);

        return response()->json(
            $order->load('assignment.courier'),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        return response()->json(
            $order->load(['assignment.courier', 'statusHistories'])
        );
    }

    /**
     * Assign a courier to the order.
     */
    public function assign(Order $order, Request $request, OrderService $service): JsonResponse
    {
        $validated = $request->validate([
            'courier_id' => ['required', 'integer', 'exists:couriers,id'],
        ]);

        DB::transaction(function () use ($order, $validated, $service) {
            $service->assignCourier($order, $validated['courier_id']);
        });

        return response()->json(
            $order->fresh(['assignment.courier', 'statusHistories'])
        );
    }

    /**
     * Update the status of the order.
     */
    public function updateStatus(Order $order, Request $request, OrderService $service): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(OrderStatus::values())],
            'meta'   => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($order, $validated, $service) {
            $service->changeStatus(
                $order,
                OrderStatus::from($validated['status']),
                $validated['meta'] ?? []
            );
        });

        return response()->json(
            $order->fresh(['assignment.courier', 'statusHistories'])
        );
    }

}
