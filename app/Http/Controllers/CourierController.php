<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Courier::query();

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $perPage = min(max($request->integer('per_page', 10), 1), 100);

        return response()->json(
            $query->paginate($perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string',
            'phone'        => 'required|string|max:50',
            'vehicle_type' => 'required|string',
            'active'       => 'boolean',
        ]);

        return response()->json(
            Courier::create($validated),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Courier $courier): JsonResponse
    {
        return response()->json($courier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier): JsonResponse
    {
        if ($courier->assignments()->exists()) {
            throw ValidationException::withMessages([
                'courier' => 'Courier has assigned orders and cannot be deleted',
            ]);
        }

        $courier->delete();

        return response()->json(['message' => 'Courier deleted successfully']);
    }
}
