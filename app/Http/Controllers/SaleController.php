<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(): JsonResponse
    {
        $sales = Sale::with(['batch.product', 'location', 'customerLocation'])
            ->when(request('batch_id'), function ($query) {
                $query->where('batch_id', request('batch_id'));
            })
            ->when(request('location_id'), function ($query) {
                $query->where('location_id', request('location_id'));
            })
            ->orderBy('sale_date', 'desc')
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(SaleResource::collection($sales), 'Sales retrieved successfully');
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                // Generate sale number if not provided
                if (!isset($data['sale_number'])) {
                    $data['sale_number'] = 'SALE-' . date('Ymd') . '-' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT);
                }

                // Check available stock
                $available = $this->stockService->getAvailableStock($data['batch_id'], $data['location_id']);
                if ($available < $data['quantity']) {
                    return ResponseHelper::error("Insufficient stock. Available: {$available}, Requested: {$data['quantity']}", null, 400);
                }

                // Calculate total if not provided
                if (!isset($data['total_amount']) && isset($data['unit_price'])) {
                    $data['total_amount'] = $data['quantity'] * $data['unit_price'];
                }

                $sale = Sale::create($data);

                // Record stock movement (out)
                $this->stockService->recordMovement([
                    'batch_id' => $data['batch_id'],
                    'location_id' => $data['location_id'],
                    'type' => 'out',
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'quantity' => $data['quantity'],
                    'unit_cost' => $data['unit_price'] ?? null,
                    'movement_date' => $data['sale_date'],
                ]);

                $sale->load(['batch.product', 'location']);
                return ResponseHelper::success(new SaleResource($sale), 'Sale created successfully', 201);
            });
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $sale = Sale::with(['batch.product', 'location', 'customerLocation'])->find($id);

        if (!$sale) {
            return ResponseHelper::notFound('Sale not found');
        }

        return ResponseHelper::success(new SaleResource($sale), 'Sale retrieved successfully');
    }
}
