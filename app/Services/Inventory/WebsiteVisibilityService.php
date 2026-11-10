<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Website pe linked product ko link tode baghair chhupana / wapas dikhana.
 *
 * website_products.status: 1 = linked + website pe dikhta, 0 = unlink, 2 = purane inactive products (kisi ne haath se),
 * 3 = linked magar website pe chhupa (HIDDEN). Website sirf status 1 padhti hai, isliye 3 = website pe nahi.
 * Hide = us product ki status 1 rows → 3; Show = 3 → 1 (wahi rows, wahi website — jaisa pehle tha).
 * Unlink (single / bulk / department) 3 ko bhi 0 kar deta hai.
 */
class WebsiteVisibilityService
{
    public const LINKED = 1;
    public const HIDDEN = 3;

    /**
     * @return array{visible: bool, website_id: ?int, website_name: ?string}
     */
    public function set(int $productId, bool $visible, int $companyId): array
    {
        $owned = DB::table('inventory_general')->where('id', $productId)->where('company_id', $companyId)->exists();
        if (!$owned) {
            throw new RuntimeException('Product not found.');
        }

        DB::transaction(function () use ($productId, $visible) {
            $now = date('Y-m-d H:i:s');
            $rows = fn () => DB::table('website_products')->where('inventory_id', $productId);
            $hasLinked = $rows()->where('status', self::LINKED)->lockForUpdate()->exists();
            $hasHidden = $rows()->where('status', self::HIDDEN)->lockForUpdate()->exists();

            if (!$visible) {
                if (!$hasLinked) {
                    if ($hasHidden) {
                        return; // pehle se chhupa
                    }
                    throw new RuntimeException('This product is not linked to any website.');
                }
                // purani chhupi rows (baad me dobara link ho gaya tha) bekaar — warna Show pe wo bhi wapas aa jatin
                $rows()->where('status', self::HIDDEN)->update(['status' => 0, 'updated_at' => $now]);
                $rows()->where('status', self::LINKED)->update(['status' => self::HIDDEN, 'updated_at' => $now]);
                return;
            }

            if ($hasLinked) {
                // pehle se dikh raha (baad me link hua) — purani chhupi rows saaf
                $rows()->where('status', self::HIDDEN)->update(['status' => 0, 'updated_at' => $now]);
                return;
            }
            if (!$hasHidden) {
                throw new RuntimeException('This product is not linked to any website.');
            }
            $rows()->where('status', self::HIDDEN)->update(['status' => self::LINKED, 'updated_at' => $now]);
        });

        return $this->state($productId);
    }

    /** @return array{visible: bool, website_id: ?int, website_name: ?string} */
    public function state(int $productId): array
    {
        foreach ([self::LINKED, self::HIDDEN] as $status) {
            $row = DB::table('website_products as wp')
                ->leftJoin('website_details as wd', 'wd.id', '=', 'wp.website_id')
                ->where('wp.inventory_id', $productId)
                ->where('wp.status', $status)
                ->orderByDesc('wp.id')
                ->select('wp.website_id', 'wd.name')
                ->first();
            if ($row) {
                return ['visible' => $status === self::LINKED, 'website_id' => (int) $row->website_id, 'website_name' => $row->name];
            }
        }

        return ['visible' => false, 'website_id' => null, 'website_name' => null];
    }

    /** Product form: linked websites; koi dikhti link na ho to chhupi wali (taake edit save link na tode) */
    public static function formWebsites(int $productId)
    {
        $linked = DB::table('website_products')->where('inventory_id', $productId)->where('status', self::LINKED)->pluck('website_id');

        return $linked->isNotEmpty()
            ? $linked
            : DB::table('website_products')->where('inventory_id', $productId)->where('status', self::HIDDEN)->pluck('website_id');
    }

    /** Wo websites jin pe product sirf chhupa hai (koi dikhti link nahi) — edit save pe chhupa hi rahe */
    public static function hiddenWebsiteIds(int $productId): array
    {
        $rows = DB::table('website_products')->where('inventory_id', $productId);
        if ((clone $rows)->where('status', self::LINKED)->exists()) {
            return [];
        }

        return (clone $rows)->where('status', self::HIDDEN)->pluck('website_id')->map(fn ($id) => (int) $id)->all();
    }

    /** Nayi link ke baad purani chhupi rows saaf (optional: sirf ek website ki) */
    public static function clearHidden(array $productIds, ?int $websiteId = null): void
    {
        if (empty($productIds)) {
            return;
        }
        $query = DB::table('website_products')->whereIn('inventory_id', $productIds)->where('status', self::HIDDEN);
        if ($websiteId !== null) {
            $query->where('website_id', $websiteId);
        }
        $query->update(['status' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
