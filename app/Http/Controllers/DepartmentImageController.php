<?php

namespace App\Http\Controllers;

use App\Services\DepartmentImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\RateLimitException;

class DepartmentImageController extends Controller
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
     * Ids of every active department in this company that still has no image,
     * used by the "All missing" mode to build its work queue.
     */
    public function missing(DepartmentImageService $service)
    {
        $ids = $service->missingImageIds();

        return response()->json(['ids' => $ids, 'total' => count($ids)]);
    }

    /**
     * Generate images for up to MAX_PER_REQUEST departments and report each one
     * separately, so one failing department does not sink the whole batch.
     */
    public function generate(Request $request, DepartmentImageService $service)
    {
        $ids = collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(self::MAX_PER_REQUEST)
            ->values();

        if ($ids->isEmpty()) {
            return response()->json(['state' => 0, 'msg' => 'No departments selected.'], 422);
        }

        $results = [];

        foreach ($ids as $id) {
            try {
                $fileName = $service->generate($id);
                $results[] = [
                    'id' => $id,
                    'ok' => true,
                    'image' => asset('storage/images/department/' . $fileName),
                ];
            } catch (\Throwable $e) {
                Log::error('Department image generation failed', ['department_id' => $id, 'error' => $e->getMessage()]);
                $results[] = [
                    'id' => $id,
                    'ok' => false,
                    'retry' => $this->isThrottled($e),
                    'msg' => $e->getMessage(),
                ];
            }
        }

        return response()->json(['state' => 1, 'results' => $results]);
    }

    /**
     * A 429 can mean "you are going too fast" or "you are out of credit", and only the
     * first is worth retrying. Both arrive as a rate limit error, so read the message.
     */
    protected function isThrottled(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        if (str_contains($message, 'quota') || str_contains($message, 'credit') || str_contains($message, 'billing')) {
            return false;
        }

        return $e instanceof RateLimitException || str_contains($message, 'rate limit');
    }
}
