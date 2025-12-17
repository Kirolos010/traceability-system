<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->when(request('search'), function ($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('sku', 'like', '%' . request('search') . '%');
            })
            ->when(request('is_active') !== null, function ($query) {
                $query->where('is_active', request('is_active'));
            })
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(ProductResource::collection($products), 'Products retrieved successfully');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return ResponseHelper::success(new ProductResource($product), 'Product created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with('batches')->find($id);

        if (!$product) {
            return ResponseHelper::notFound('Product not found');
        }

        return ResponseHelper::success(new ProductResource($product), 'Product retrieved successfully');
    }

    public function update(StoreProductRequest $request, int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return ResponseHelper::notFound('Product not found');
        }

        $product->update($request->validated());
        return ResponseHelper::success(new ProductResource($product), 'Product updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return ResponseHelper::notFound('Product not found');
        }

        $product->delete();
        return ResponseHelper::success(null, 'Product deleted successfully');
    }
}
