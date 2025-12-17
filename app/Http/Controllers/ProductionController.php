<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreProductionRequest;
use App\Http\Resources\ProductionResource;
use App\Models\Production;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(): JsonResponse
    {
        $productions = Production::with(['product', 'outputBatch', 'location', 'materials.batch'])
            ->when(request('product_id'), function ($query) {
                $query->where('product_id', request('product_id'));
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->orderBy('production_date', 'desc')
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(ProductionResource::collection($productions), 'Productions retrieved successfully');
    }

    public function store(StoreProductionRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $materials = $data['materials'];
                unset($data['materials']);

                // Generate production number if not provided
                if (!isset($data['production_number'])) {
                    $data['production_number'] = 'PROD-' . date('Ymd') . '-' . str_pad(Production::count() + 1, 4, '0', STR_PAD_LEFT);
                }

                $production = Production::create($data);

                // Create production materials and consume stock
                foreach ($materials as $material) {
                    $production->materials()->create($material);

                    // Consume material stock (out movement)
                    $this->stockService->recordMovement([
                        'batch_id' => $material['batch_id'],
                        'location_id' => $data['location_id'],
                        'type' => 'out',
                        'reference_type' => 'production',
                        'reference_id' => $production->id,
                        'quantity' => $material['quantity'],
                        'movement_date' => $data['production_date'],
                    ]);
                }

                // If output batch is provided and production is completed, add output stock
                if ($production->output_batch_id && $production->status === 'completed') {
                    $this->stockService->recordMovement([
                        'batch_id' => $production->output_batch_id,
                        'location_id' => $data['location_id'],
                        'type' => 'in',
                        'reference_type' => 'production',
                        'reference_id' => $production->id,
                        'quantity' => $data['quantity'],
                        'movement_date' => $data['production_date'],
                    ]);
                }

                $production->load(['product', 'outputBatch', 'materials.batch']);
                return ResponseHelper::success(new ProductionResource($production), 'Production created successfully', 201);
            });
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $production = Production::with(['product', 'outputBatch', 'location', 'materials.batch.product'])->find($id);

        if (!$production) {
            return ResponseHelper::notFound('Production not found');
        }

        return ResponseHelper::success(new ProductionResource($production), 'Production retrieved successfully');
    }

    public function update(StoreProductionRequest $request, int $id): JsonResponse
    {
        $production = Production::find($id);

        if (!$production) {
            return ResponseHelper::notFound('Production not found');
        }

        try {
            return DB::transaction(function () use ($request, $production) {
                $data = $request->validated();
                $materials = $data['materials'] ?? null;
                unset($data['materials']);

                $production->update($data);

                // Update materials if provided
                if ($materials) {
                    $production->materials()->delete();
                    foreach ($materials as $material) {
                        $production->materials()->create($material);
                    }
                }

                $production->load(['product', 'outputBatch', 'materials.batch']);
                return ResponseHelper::success(new ProductionResource($production), 'Production updated successfully');
            });
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 400);
        }
    }
}
