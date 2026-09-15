<?php

namespace App\Http\Controllers;

use App\Services\ProductImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductImageController extends Controller
{
    /**
     * Generated one at a time from the browser, so keep each request small enough
     * to finish well inside the PHP execution limit.
     */
    const MAX_PER_REQUEST = 5;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Ids of every active product in this company that still has no image,
     * used by the "All missing" mode to build its work queue.
     */
    public function missing(ProductImageService $service)
    {
        $ids = $service->missingImageIds();

        return response()->json(['ids' => $ids, 'total' => count($ids)]);
    }

    /**
     * Generate images for up to MAX_PER_REQUEST products and report each one
     * separately, so one failing product does not sink the whole batch.
     */
    public function generate(Request $request, ProductImageService $service)
    {
        $ids = collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(self::MAX_PER_REQUEST)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json(['state' => 0, 'msg' => 'No products selected.'], 422);
        }

        $results = [];

        foreach ($ids as $id) {
            try {
                $fileName = $service->generate($id);
                $results[] = [
                    'id' => $id,
                    'ok' => true,
                    'image' => asset('storage/images/products/' . $fileName),
                ];
            } catch (\Throwable $e) {
                Log::error('Product image generation failed', ['product_id' => $id, 'error' => $e->getMessage()]);
                $results[] = [
                    'id' => $id,
                    'ok' => false,
                    'msg' => $e->getMessage(),
                ];
            }
        }

        return response()->json(['state' => 1, 'results' => $results]);
    }
}
