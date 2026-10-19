<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Branch Availability: product kis branch ke POS me jaye.
 * product_branch_availability ki row = us branch me BAND; row na ho = chalu (default, purana behaviour).
 * Sirf company ki active branches (status_id 1) pe kaam; inactive branch ki purani rows nahi chheri jaati.
 * POS pe asar webservice/function.php (getInventory / getUpdatedInventory) ke filter se.
 */
class BranchAvailabilityService
{
    public const MODE_ONLY = 'only';       // sirf in branches me chalu, baaqi active branches me band
    public const MODE_DISABLE = 'disable'; // in branches me band
    public const MODE_ENABLE = 'enable';   // in branches me chalu

    private const TABLE = 'product_branch_availability';

    private static ?bool $ready = null;

    /** migration chali ho to hi feature; warna sab chalu (purana behaviour) */
    public static function ready(): bool
    {
        return self::$ready ??= Schema::hasTable(self::TABLE);
    }

    /** company ki active branches [branch_id => branch_name] */
    public function branches(int $companyId): array
    {
        return DB::table('branch')
            ->where('company_id', $companyId)
            ->where('status_id', 1)
            ->orderBy('branch_name')
            ->pluck('branch_name', 'branch_id')
            ->all();
    }

    /** product jin active branches me band hai */
    public function disabledBranches(int $companyId, int $productId): array
    {
        if (!self::ready()) {
            return [];
        }

        return DB::table(self::TABLE)
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->whereIn('branch_id', array_keys($this->branches($companyId)))
            ->pluck('branch_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /** list ke liye: [product_id => band active branches ki ginti] — sirf jin ki koi branch band */
    public function disabledCounts(int $companyId, array $productIds): array
    {
        if (!self::ready() || empty($productIds)) {
            return [];
        }

        return DB::table(self::TABLE)
            ->where('company_id', $companyId)
            ->whereIn('product_id', $productIds)
            ->whereIn('branch_id', array_keys($this->branches($companyId)))
            ->groupBy('product_id')
            ->selectRaw('product_id, COUNT(*) as n')
            ->pluck('n', 'product_id')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    public function isEnabled(int $productId, int $branchId): bool
    {
        if (!self::ready()) {
            return true;
        }

        return !DB::table(self::TABLE)->where('product_id', $productId)->where('branch_id', $branchId)->exists();
    }

    /** product form: $enabledBranchIds chalu, baaqi active branches band */
    public function setForProduct(int $companyId, int $productId, array $enabledBranchIds, $userId = null): array
    {
        return $this->bulk($companyId, [$productId], self::MODE_ONLY, $enabledBranchIds, $userId);
    }

    /**
     * Kai products ek saath. Return: ['disabled' => rows bani, 'enabled' => rows hati].
     * MODE_ONLY me khali $branchIds = saari branches me band (POS se poora hata) — ye bhi allowed.
     */
    public function bulk(int $companyId, array $productIds, string $mode, array $branchIds, $userId = null): array
    {
        if (!self::ready()) {
            throw new RuntimeException('Branch availability is not set up yet (database update pending).');
        }
        if (!in_array($mode, [self::MODE_ONLY, self::MODE_DISABLE, self::MODE_ENABLE], true)) {
            throw new RuntimeException('Invalid option.');
        }

        $active = array_map('intval', array_keys($this->branches($companyId)));
        $branchIds = array_values(array_unique(array_map('intval', $branchIds)));
        $foreign = array_diff($branchIds, $active);
        if ($foreign) {
            throw new RuntimeException('Selected branch is not an active branch of this company.');
        }
        if ($mode !== self::MODE_ONLY && empty($branchIds)) {
            throw new RuntimeException('Select at least one branch.');
        }

        $productIds = array_values(array_unique(array_map('intval', $productIds)));
        $valid = DB::table('inventory_general')
            ->where('company_id', $companyId)
            ->whereIn('id', $productIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        if (empty($valid) || count($valid) !== count($productIds)) {
            throw new RuntimeException('Product not found.');
        }

        [$disable, $enable] = match ($mode) {
            self::MODE_ONLY => [array_values(array_diff($active, $branchIds)), $branchIds],
            self::MODE_DISABLE => [$branchIds, []],
            self::MODE_ENABLE => [[], $branchIds],
        };

        $result = ['disabled' => 0, 'enabled' => 0];
        DB::transaction(function () use ($companyId, $valid, $disable, $enable, $userId, &$result) {
            $now = now();
            foreach (array_chunk($valid, 500) as $chunk) {
                if ($disable) {
                    $rows = [];
                    foreach ($chunk as $productId) {
                        foreach ($disable as $branchId) {
                            $rows[] = [
                                'company_id' => $companyId,
                                'product_id' => $productId,
                                'branch_id' => $branchId,
                                'created_by' => $userId,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                    foreach (array_chunk($rows, 1000) as $part) {
                        $result['disabled'] += DB::table(self::TABLE)->insertOrIgnore($part);
                    }
                }

                if ($enable) {
                    $reopened = DB::table(self::TABLE)
                        ->whereIn('product_id', $chunk)
                        ->whereIn('branch_id', $enable)
                        ->get(['product_id', 'branch_id']);
                    if ($reopened->isNotEmpty()) {
                        DB::table(self::TABLE)->whereIn('product_id', $chunk)->whereIn('branch_id', $enable)->delete();
                        $result['enabled'] += $reopened->count();
                        // dobara chalu product terminals pe wapas aaye (getUpdatedInventory)
                        foreach ($reopened->groupBy('branch_id') as $branchId => $rows) {
                            $this->queueDownload((int) $branchId, $rows->pluck('product_id')->map(fn ($id) => (int) $id)->all());
                        }
                    }
                }
            }
        });

        return $result;
    }

    /**
     * BA.6: band branch me stock aaya — rokna nahi, sirf batana. Warning session me rakhi jaati hai aur
     * master-tailwind layout ka partial (partials/branch-availability-alert) agle poore page pe dikha kar hata deta hai,
     * taake har screen (redirect / JSON / Livewire) ki JS na chhedni pade.
     */
    public function warnIfDisabled(int $branchId, array $productIds): ?string
    {
        $productIds = array_values(array_unique(array_filter(array_map('intval', $productIds))));
        if (!self::ready() || $branchId <= 0 || empty($productIds)) {
            return null;
        }
        $names = DB::table(self::TABLE . ' as pba')
            ->join('inventory_general as ig', 'ig.id', '=', 'pba.product_id')
            ->where('pba.branch_id', $branchId)
            ->whereIn('pba.product_id', $productIds)
            ->orderBy('ig.product_name')
            ->pluck('ig.product_name')
            ->all();
        if (empty($names)) {
            return null;
        }
        $branch = DB::table('branch')->where('branch_id', $branchId)->value('branch_name') ?? ('#' . $branchId);
        $list = implode(', ', array_slice($names, 0, 5)) . (count($names) > 5 ? ' and ' . (count($names) - 5) . ' more' : '');
        $message = "Stock saved. Note: {$list} " . (count($names) > 1 ? 'are' : 'is') . " turned off for POS in {$branch}, so it will not sell there until turned on (Inventory → Branch Availability).";

        $pending = (array) session('ba_warnings', []);
        $pending[] = $message;
        session(['ba_warnings' => array_slice(array_values(array_unique($pending)), -5)]);

        return $message;
    }

    /** branch ke active terminals ke liye product update (inventory_download_status, jaise product edit karta hai) */
    private function queueDownload(int $branchId, array $productIds): void
    {
        $terminals = DB::table('terminal_details')->where('branch_id', $branchId)->where('status_id', 1)->pluck('terminal_id');
        if ($terminals->isEmpty() || empty($productIds)) {
            return;
        }
        $rows = [];
        $date = now()->toDateString();
        $time = now()->toTimeString();
        foreach ($terminals as $terminalId) {
            foreach ($productIds as $productId) {
                $rows[] = [
                    'branchId' => $branchId,
                    'terminalId' => $terminalId,
                    'productId' => $productId,
                    'status' => 1,
                    'date' => $date,
                    'time' => $time,
                ];
            }
        }
        foreach (array_chunk($rows, 1000) as $part) {
            DB::table('inventory_download_status')->insert($part);
        }
    }
}
