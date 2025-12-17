<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreBatchRequest;
use App\Http\Resources\BatchResource;
use App\Models\Batch;
use Illuminate\Http\JsonResponse;

class BatchController extends Controller
{
    public function index(): JsonResponse
    {
        $batches = Batch::with(['product', 'supplier'])
            ->when(request('product_id'), function ($query) {
                $query->where('product_id', request('product_id'));
            })
            ->when(request('supplier_id'), function ($query) {
                $query->where('supplier_id', request('supplier_id'));
            })
            ->when(request('search'), function ($query) {
                $query->where('batch_number', 'like', '%' . request('search') . '%')
                    ->orWhere('lot_number', 'like', '%' . request('search') . '%');
            })
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(BatchResource::collection($batches), 'Batches retrieved successfully');
    }

    public function store(StoreBatchRequest $request): JsonResponse
    {
        $batch = Batch::create($request->validated());
        $batch->load(['product', 'supplier']);
        return ResponseHelper::success(new BatchResource($batch), 'Batch created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $batch = Batch::with(['product', 'supplier', 'stockLevels.location'])->find($id);

        if (!$batch) {
            return ResponseHelper::notFound('Batch not found');
        }

        return ResponseHelper::success(new BatchResource($batch), 'Batch retrieved successfully');
    }

    public function update(StoreBatchRequest $request, int $id): JsonResponse
    {
        $batch = Batch::find($id);

        if (!$batch) {
            return ResponseHelper::notFound('Batch not found');
        }

        $batch->update($request->validated());
        $batch->load(['product', 'supplier']);
        return ResponseHelper::success(new BatchResource($batch), 'Batch updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $batch = Batch::find($id);

        if (!$batch) {
            return ResponseHelper::notFound('Batch not found');
        }

        $batch->delete();
        return ResponseHelper::success(null, 'Batch deleted successfully');
    }
}
