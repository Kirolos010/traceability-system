<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreInventoryMovementRequest;
use App\Http\Resources\InventoryMovementResource;
use App\Models\InventoryMovement;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;

class InventoryMovementController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(): JsonResponse
    {
        $movements = InventoryMovement::with(['batch.product', 'location', 'user'])
            ->when(request('batch_id'), function ($query) {
                $query->where('batch_id', request('batch_id'));
            })
            ->when(request('location_id'), function ($query) {
                $query->where('location_id', request('location_id'));
            })
            ->when(request('type'), function ($query) {
                $query->where('type', request('type'));
            })
            ->orderBy('movement_date', 'desc')
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(InventoryMovementResource::collection($movements), 'Inventory movements retrieved successfully');
    }

    public function store(StoreInventoryMovementRequest $request): JsonResponse
    {
        try {
            $movement = $this->stockService->recordMovement($request->validated());
            $movement->load(['batch.product', 'location']);
            return ResponseHelper::success(new InventoryMovementResource($movement), 'Inventory movement created successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $movement = InventoryMovement::with(['batch.product', 'location', 'user'])->find($id);

        if (!$movement) {
            return ResponseHelper::notFound('Inventory movement not found');
        }

        return ResponseHelper::success(new InventoryMovementResource($movement), 'Inventory movement retrieved successfully');
    }
}
