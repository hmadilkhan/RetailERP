<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Inventory feed for weighing scale devices.
 *
 * GET /api/scale/inventory/{company_id}
 */
class ScaleInventoryController extends Controller
{
    public function index(Request $request, $company_id = null): JsonResponse
    {
        $companyId = $company_id ?? $request->query('company_id');

        $validator = validator([
            'company_id'    => $companyId,
            'updated_since' => $request->query('updated_since'),
            'only_weighed'  => $request->query('only_weighed'),
            'min_grams'     => $request->query('min_grams'),
            'max_grams'     => $request->query('max_grams'),
            'limit'         => $request->query('limit'),
            'offset'        => $request->query('offset'),
        ], [
            'company_id'    => 'required|integer|min:1',
            'updated_since' => 'nullable|numeric|min:0',
            'only_weighed'  => 'nullable|boolean',
            'min_grams'     => 'nullable|integer|min:1',
            'max_grams'     => 'nullable|integer|min:1',
            'limit'         => 'nullable|integer|min:1|max:5000',
            'offset'        => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $columns = [
            'a.item_code',
            'a.product_name',
            'a.image',
            'a.updated_at',
            'p.retail_price',
            'p.date as price_updated_at',
        ];

        $query = DB::table('inventory_general as a')
            ->join('inventory_price as p', function ($join) {
                $join->on('p.product_id', '=', 'a.id')->where('p.status_id', 1);
            })
            ->where('a.company_id', (int) $companyId)
            ->where('a.status', 1)
            ->whereNull('a.deleted_at')
            ->select($columns)
            ->orderBy('a.id');

        if ($request->boolean('only_weighed')) {
            $query->whereIn('a.uom_id', config('scale.weighed_uom_ids'));
        }

        // Incremental sync: epoch milliseconds, same unit as the updated_at we emit.
        if ($request->filled('updated_since')) {
            $since = date('Y-m-d H:i:s', (int) ((int) $request->query('updated_since') / 1000));

            $query->where(function ($q) use ($since) {
                $q->where('a.updated_at', '>=', $since)
                    ->orWhere('p.date', '>=', $since);
            });
        }

        if ($request->filled('limit')) {
            $query->limit((int) $request->query('limit'))->offset((int) $request->query('offset', 0));
        }

        $imageBaseUrl = rtrim(config('scale.image_base_url'), '/') . '/';
        $minGrams     = (int) ($request->query('min_grams') ?: config('scale.default_min_grams'));
        $maxGrams     = (int) ($request->query('max_grams') ?: config('scale.default_max_grams'));

        $items = $query->get()->map(function ($row) use ($imageBaseUrl, $minGrams, $maxGrams) {
            return [
                'plu'          => (string) $row->item_code,
                'name'         => $row->product_name,
                // No separate urdu column in the database; product_name already
                // holds the urdu label for the scale companies.
                'name_urdu'    => $row->product_name,
                'price_per_kg' => round((float) $row->retail_price, 2),
                'image_url'    => $row->image ? $imageBaseUrl . $row->image : null,
                'min_grams'    => $minGrams,
                'max_grams'    => $maxGrams,
                'updated_at'   => $this->epochMilliseconds($row),
            ];
        })->values();

        return response()->json($items);
    }

    /**
     * Latest change across the product and its price, in epoch milliseconds so the
     * device can pass it straight back as updated_since.
     */
    private function epochMilliseconds(object $row): int
    {
        $latest = 0;

        foreach (array_filter([$row->updated_at, $row->price_updated_at]) as $timestamp) {
            $latest = max($latest, (int) strtotime($timestamp));
        }

        return $latest * 1000;
    }
}
