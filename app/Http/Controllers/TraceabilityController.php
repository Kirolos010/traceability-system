<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\TraceabilityService;
use Illuminate\Http\JsonResponse;

class TraceabilityController extends Controller
{
    protected TraceabilityService $traceabilityService;

    public function __construct(TraceabilityService $traceabilityService)
    {
        $this->traceabilityService = $traceabilityService;
    }

    /**
     * Trace backward from a batch
     * 
     * GET /api/trace/backward/{batchId}
     */
    public function traceBackward(int $batchId): JsonResponse
    {
        try {
            $trace = $this->traceabilityService->traceBackward($batchId);
            return ResponseHelper::success($trace, 'Backward trace completed');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 500);
        }
    }

    /**
     * Trace forward from a batch
     * 
     * GET /api/trace/forward/{batchId}
     */
    public function traceForward(int $batchId): JsonResponse
    {
        try {
            $trace = $this->traceabilityService->traceForward($batchId);
            return ResponseHelper::success($trace, 'Forward trace completed');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 500);
        }
    }

    /**
     * Full trace (both backward and forward)
     * 
     * GET /api/trace/full/{batchId}
     */
    public function fullTrace(int $batchId): JsonResponse
    {
        try {
            $trace = $this->traceabilityService->fullTrace($batchId);
            return ResponseHelper::success($trace, 'Full trace completed');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 500);
        }
    }

    /**
     * Trace from a sale
     * 
     * GET /api/trace/sale/{saleId}
     */
    public function traceFromSale(int $saleId): JsonResponse
    {
        try {
            $trace = $this->traceabilityService->traceFromSale($saleId);
            return ResponseHelper::success($trace, 'Sale trace completed');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), null, 500);
        }
    }
}
