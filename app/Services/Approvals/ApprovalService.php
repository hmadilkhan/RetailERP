<?php

namespace App\Services\Approvals;

use App\Models\VendorBill;
use App\Services\Inventory\StockAdjustmentApplier;
use App\Services\Purchases\VendorBillService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Phase 2.4: approvals. Rule (company + module + branch / saari + kam se kam amount + approver role) match ho aur
 * request karne wala approver na ho → kaam ruk kar approval_requests me "pending". Koi active rule nahi = pehle jaisa.
 * Admin (1) / Sub-Admin (2) ka apna kaam nahi rukta aur wo har request approve kar sakte hain.
 * Pending kaam asli tables me nahi jata (posting, valuation, reports, POS unhe seedha padhte hain) — approve pe chalta hai:
 *   stock_adjustment : payload se StockAdjustmentApplier (negative me lots kam pad jayein to usi branch ke baaqi lots)
 *   transfer         : transfer_without_demand status 5 Pending → 2 Approved (reject = 3); banane wala phir "Place Order"
 *   vendor_bill      : bill draft rehta hai; approve pe VendorBillService::post
 *   expense          : POS expense rok nahi sakte (paisa nikal chuka, terminal offline) — sirf baad me review, koi asar nahi
 */
class ApprovalService
{
    const MODULES = [
        'stock_adjustment' => 'Stock Adjustment',
        'transfer' => 'Branch Transfer',
        'vendor_bill' => 'Vendor Bill',
        'expense' => 'POS Expense (review)',
    ];
    const ALWAYS = [1, 2]; // Admin, Sub-Admin

    private static ?bool $enabled = null;

    /** tables bani hain? (migration se pehle deploy pe sab pehle jaisa) */
    public static function enabled(): bool
    {
        return self::$enabled ??= \Illuminate\Support\Facades\Schema::hasTable('approval_rules');
    }

    /** Approval chahiye? null = nahi, warna approver role id */
    public function approverFor(int $companyId, string $module, ?int $branchId, float $amount, int $roleId): ?int
    {
        if (!self::enabled() || in_array($roleId, self::ALWAYS, true)) {
            return null;
        }
        $rules = DB::table('approval_rules')->where('company_id', $companyId)->where('module', $module)->where('is_active', 1)
            ->where(fn ($q) => $q->whereNull('branch_id')->orWhere('branch_id', $branchId))
            ->where('min_amount', '<=', round(abs($amount), 2))
            ->orderByDesc('min_amount')->get();
        if ($rules->isEmpty() || $rules->pluck('approver_role_id')->contains($roleId)) {
            return null;
        }
        return (int) $rules->first()->approver_role_id;
    }

    public function request(int $companyId, string $module, ?int $branchId, float $amount, int $approverRole, ?int $subjectId,
                            string $reference, ?array $payload, $userId): int
    {
        Cache::forget('approvals_pending:' . $companyId);
        return DB::table('approval_requests')->insertGetId([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'module' => $module,
            'subject_id' => $subjectId,
            'reference' => mb_substr($reference, 0, 255),
            'amount' => round(abs($amount), 2),
            'payload' => $payload ? json_encode($payload) : null,
            'status' => 'pending',
            'approver_role_id' => $approverRole,
            'requested_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function pendingFor(string $module, int $subjectId)
    {
        return DB::table('approval_requests')->where('module', $module)->where('subject_id', $subjectId)->where('status', 'pending')->first();
    }

    public function approvedFor(string $module, int $subjectId): bool
    {
        return DB::table('approval_requests')->where('module', $module)->where('subject_id', $subjectId)->where('status', 'approved')->exists();
    }

    /** ye user is request pe faisla kar sakta hai? */
    public function canDecide(object $req, int $companyId, int $roleId, $userId, $branchId): bool
    {
        if ((int) $req->company_id !== $companyId || $req->status !== 'pending') {
            return false;
        }
        if (in_array($roleId, self::ALWAYS, true)) {
            return true;
        }
        if ($roleId !== (int) $req->approver_role_id || (int) $req->requested_by === (int) $userId) {
            return false;
        }
        if ($req->branch_id === null) {
            return true;
        }
        if ($roleId === 16) { // Regional Manager — user_branches
            return DB::table('user_branches')->where('user_id', $userId)->where('branch_id', $req->branch_id)->exists();
        }
        return (int) $req->branch_id === (int) $branchId;
    }

    public function approve(int $id, int $companyId, int $roleId, $userId, $branchId, ?string $remarks = null): string
    {
        return DB::transaction(function () use ($id, $companyId, $roleId, $userId, $branchId, $remarks) {
            $req = DB::table('approval_requests')->where('id', $id)->lockForUpdate()->first();
            if (!$req || !$this->canDecide($req, $companyId, $roleId, $userId, $branchId)) {
                throw new RuntimeException('You cannot approve this request.');
            }
            $result = $this->execute($req, $companyId, $userId);
            DB::table('approval_requests')->where('id', $id)->update([
                'status' => 'approved', 'decided_by' => $userId, 'decided_at' => now(), 'remarks' => $remarks ?: null,
                'result' => $result, 'updated_at' => now(),
            ]);
            Cache::forget('approvals_pending:' . $companyId);
            return $result;
        });
    }

    public function reject(int $id, int $companyId, int $roleId, $userId, $branchId, ?string $remarks = null): void
    {
        DB::transaction(function () use ($id, $companyId, $roleId, $userId, $branchId, $remarks) {
            $req = DB::table('approval_requests')->where('id', $id)->lockForUpdate()->first();
            if (!$req || !$this->canDecide($req, $companyId, $roleId, $userId, $branchId)) {
                throw new RuntimeException('You cannot reject this request.');
            }
            if ($req->module === 'transfer' && $req->subject_id) {
                $this->transferStatus((int) $req->subject_id, 3); // Reject
            }
            DB::table('approval_requests')->where('id', $id)->update([
                'status' => 'rejected', 'decided_by' => $userId, 'decided_at' => now(), 'remarks' => $remarks ?: null, 'updated_at' => now(),
            ]);
        });
        Cache::forget('approvals_pending:' . $companyId);
    }

    /** request karne wala apni pending request wapas le */
    public function cancel(int $id, int $companyId, $userId): void
    {
        $req = DB::table('approval_requests')->where('id', $id)->where('company_id', $companyId)->first();
        if (!$req || $req->status !== 'pending' || (int) $req->requested_by !== (int) $userId) {
            throw new RuntimeException('Only your own pending request can be withdrawn.');
        }
        if ($req->module === 'transfer' && $req->subject_id) {
            $this->transferStatus((int) $req->subject_id, 1); // wapas Draft
        }
        DB::table('approval_requests')->where('id', $id)->update(['status' => 'cancelled', 'updated_at' => now()]);
        Cache::forget('approvals_pending:' . $companyId);
    }

    private function execute(object $req, int $companyId, $userId): string
    {
        $payload = $req->payload ? json_decode($req->payload, true) : [];
        switch ($req->module) {
            case 'stock_adjustment':
                $applier = app(StockAdjustmentApplier::class);
                if (($payload['sign'] ?? '+') === '+') {
                    $stockId = $applier->positive($payload);
                    return 'Stock added (lot #' . $stockId . ').';
                }
                $applier->negative($payload, true);
                return 'Stock removed.';
            case 'transfer':
                $this->transferStatus((int) $req->subject_id, 2); // Approved — banane wala Place Order dabaye
                return 'Transfer approved. The sender can now place the order.';
            case 'vendor_bill':
                $bill = VendorBill::where('company_id', $companyId)->find($req->subject_id);
                if (!$bill || $bill->status !== 'draft') {
                    throw new RuntimeException('This bill is no longer a draft.');
                }
                app(VendorBillService::class)->post($companyId, $bill->id, $req->requested_by ?: $userId);
                return 'Bill ' . $bill->doc_no . ' posted.';
            default:
                return 'Reviewed.';
        }
    }

    private function transferStatus(int $transferId, int $status): void
    {
        DB::table('transfer_without_demand')->where('transfer_id', $transferId)->update(['status_id' => $status]);
        DB::table('transfer_item_details')->where('transfer_id', $transferId)->where('transfer_type', 'without_demand')->update(['status_id' => $status]);
    }

    /** POS expense review: rule ke baad wale, had se upar wale POS expenses ki pending requests (ek baar) */
    public function syncExpenseReviews(int $companyId): int
    {
        if (!self::enabled()) {
            return 0;
        }
        $rules = DB::table('approval_rules')->where('company_id', $companyId)->where('module', 'expense')->where('is_active', 1)->get();
        if ($rules->isEmpty()) {
            return 0;
        }
        $since = substr((string) $rules->min('created_at'), 0, 10);
        $rows = DB::table('expenses as e')->join('branch as b', 'b.branch_id', '=', 'e.branch_id')
            ->leftJoin('expense_categories as c', 'c.exp_cat_id', '=', 'e.exp_cat_id')
            ->where('b.company_id', $companyId)->whereNull('e.payment_mode')->where('e.date', '>=', $since)
            ->where('e.net_amount', '>=', (float) $rules->min('min_amount'))
            ->whereNotExists(fn ($q) => $q->from('approval_requests as a')->whereColumn('a.subject_id', 'e.exp_id')->where('a.module', 'expense'))
            ->limit(500)->get(['e.exp_id', 'e.branch_id', 'e.net_amount', 'e.date', 'e.expense_details', 'c.expense_category']);

        $n = 0;
        foreach ($rows as $e) {
            $rule = $rules->filter(fn ($r) => ($r->branch_id === null || (int) $r->branch_id === (int) $e->branch_id) && (float) $e->net_amount >= (float) $r->min_amount)
                ->sortByDesc('min_amount')->first();
            if (!$rule) {
                continue;
            }
            $this->request($companyId, 'expense', (int) $e->branch_id, (float) $e->net_amount, (int) $rule->approver_role_id, (int) $e->exp_id,
                'Expense #' . $e->exp_id . ' — ' . ($e->expense_category ?: 'Expense') . ' — ' . $e->date . ($e->expense_details ? ' — ' . $e->expense_details : ''), null, null);
            $n++;
        }
        return $n;
    }

    /** banner ke liye — is user ke faisle ke liye kitni pending (1 min cache company-wise list) */
    public function pendingCountFor(int $companyId, int $roleId, $userId, $branchId): int
    {
        try {
            if (!self::enabled()) {
                return 0;
            }
            $rows = Cache::remember('approvals_pending:' . $companyId, 60, fn () => DB::table('approval_requests')
                ->where('company_id', $companyId)->where('status', 'pending')
                ->get(['id', 'company_id', 'branch_id', 'status', 'approver_role_id', 'requested_by'])->all());
            return collect($rows)->filter(fn ($r) => $this->canDecide($r, $companyId, $roleId, $userId, $branchId))->count();
        } catch (Throwable $e) {
            return 0;
        }
    }
}
