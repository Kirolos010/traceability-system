<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Resources\TransferResource;
use App\Models\Transfer;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(): JsonResponse
    {
        $transfers = Transfer::with(['batch.product', 'fromLocation', 'toLocation'])
            ->when(request('batch_id'), function ($query) {
                $query->where('batch_id', request('batch_id'));
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->orderBy('transfer_date', 'desc')
            ->paginate(request('per_page', 15));

        return ResponseHelper::success(TransferResource::collection($transfers), 'Transfers retrieved successfully');
    }

    public function store(StoreTransferRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();

                // Generate transfer number if not provided
                if (!isset($data['transfer_number'])) {
                    $data['transfer_number'] = 'TRF-' . date('Ymd') . '-' . str_pad(Transfer::count() + 1, 4, '0', STR_PAD_LEFT);
                }

                // Check available stock
                $available = $this->stockService->getAvailableStock($data['batch_id'], $data['from_location_id']);
                if ($available < $data['quantity']) {
                    return ResponseHelper::error("Insufficient stock. Available: {$available}, Requested: {$data['quantity']}", null, 400);
                }

                // Reserve stock
                $this->stockService->reserveStock($data['batch_id'], $data['from_location_id'], $data['quantity']);

                $transfer = Transfer::create($data);

                // If status is completed, process the transfer immediately
                if ($data['status'] === 'completed') {
                    $this->processTransfer($transfer);
                }

                $transfer->load(['batch.product', 'fromLocation', 'toLocation']);
                return ResponseHelper::success(new TransferResource($transfer), 'Transfer created successfully', 201);
            });
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $transfer = Transfer::with(['batch.product', 'fromLocation', 'toLocation'])->find($id);

        if (!$transfer) {
            return ResponseHelper::notFound('Transfer not found');
        }

        return ResponseHelper::success(new TransferResource($transfer), 'Transfer retrieved successfully');
    }

    public function complete(int $id): JsonResponse
    {
        $transfer = Transfer::find($id);

        if (!$transfer) {
            return ResponseHelper::notFound('Transfer not found');
        }

        if ($transfer->status === 'completed') {
            return ResponseHelper::error('Transfer already completed', null, 400);
        }

        try {
            return DB::transaction(function () use ($transfer) {
                $this->processTransfer($transfer);
                $transfer->update([
                    'status' => 'completed',
                    'received_date' => now(),
                ]);

                $transfer->load(['batch.product', 'fromLocation', 'toLocation']);
                return ResponseHelper::success(new TransferResource($transfer), 'Transfer completed successfully');
            });
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 400);
        }
    }

    protected function processTransfer(Transfer $transfer): void
    {
        // Release reserved stock
        $this->stockService->releaseReservedStock(
            $transfer->batch_id,
            $transfer->from_location_id,
            (float) $transfer->quantity
        );

        // Remove from source location
        $this->stockService->recordMovement([
            'batch_id' => $transfer->batch_id,
            'location_id' => $transfer->from_location_id,
            'type' => 'out',
            'reference_type' => 'transfer',
            'reference_id' => $transfer->id,
            'quantity' => $transfer->quantity,
            'movement_date' => $transfer->transfer_date,
        ]);

        // Add to destination location
        $this->stockService->recordMovement([
            'batch_id' => $transfer->batch_id,
            'location_id' => $transfer->to_location_id,
            'type' => 'in',
            'reference_type' => 'transfer',
            'reference_id' => $transfer->id,
            'quantity' => $transfer->quantity,
            'movement_date' => $transfer->received_date ?? $transfer->transfer_date,
        ]);
    }
}
