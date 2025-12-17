<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Supplier::query()
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('code', 'like', '%' . request('search') . '%');
            })
            ->when(request('is_active') !== null, function ($query) {
                $query->where('is_active', request('is_active'));
            })
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(SupplierResource::collection($suppliers), 'Suppliers retrieved successfully');
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Supplier::create($request->validated());
        return ResponseHelper::success(new SupplierResource($supplier), 'Supplier created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $supplier = Supplier::with('batches.product')->find($id);

        if (!$supplier) {
            return ResponseHelper::notFound('Supplier not found');
        }

        return ResponseHelper::success(new SupplierResource($supplier), 'Supplier retrieved successfully');
    }

    public function update(StoreSupplierRequest $request, int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return ResponseHelper::notFound('Supplier not found');
        }

        $supplier->update($request->validated());
        return ResponseHelper::success(new SupplierResource($supplier), 'Supplier updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return ResponseHelper::notFound('Supplier not found');
        }

        $supplier->delete();
        return ResponseHelper::success(null, 'Supplier deleted successfully');
    }
}
