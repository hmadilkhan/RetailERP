<?php

namespace App\Services\Website;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Opens / closes website branches from `website_branches_schedule`.
 *
 * Times are compared in PHP after normalising to H:i, so the column type
 * (TIME "15:00:00" or varchar "15:00") does not matter. A shift whose
 * closing_time is not after its opening_time runs past midnight, so its
 * closing belongs to the next calendar day.
 */
class BranchScheduleService
{
    /**
     * Branch states produced by the opening / closing times that fall in the
     * minutes after $from up to and including $to: [branch_id => true|false].
     * Within a minute an opening wins over a closing (back-to-back shifts).
     */
    public function transitions(Carbon $from, Carbon $to): array
    {
        $rows = $this->rows();
        $states = [];

        for ($minute = $from->copy()->startOfMinute()->addMinute(); $minute <= $to; $minute->addMinute()) {
            $time = $minute->format('H:i');
            $today = $minute->format('D');
            $yesterday = $minute->copy()->subDay()->format('D');

            $opening = [];
            foreach ($rows as $row) {
                if ($row->close === $time
                    && (($row->day === $today && $row->close > $row->open)
                        || ($row->day === $yesterday && $row->close <= $row->open))) {
                    $states[$row->branch_id] = false;
                }
                if ($row->day === $today && $row->open === $time) {
                    $opening[] = $row->branch_id;
                }
            }
            foreach ($opening as $branchId) {
                $states[$branchId] = true;
            }
        }

        return $states;
    }

    /**
     * Should the branch be open at $at according to its schedule?
     * null = the branch has no schedule at all.
     */
    public function isOpenAt(int $branchId, Carbon $at): ?bool
    {
        $rows = $this->rows($branchId);
        if ($rows->isEmpty()) {
            return null;
        }

        $time = $at->format('H:i');
        $today = $at->format('D');
        $yesterday = $at->copy()->subDay()->format('D');

        foreach ($rows as $row) {
            $overnight = $row->close <= $row->open;

            if ($row->day === $today && $time >= $row->open && ($overnight || $time < $row->close)) {
                return true;
            }
            if ($row->day === $yesterday && $overnight && $time < $row->close) {
                return true;
            }
        }

        return false;
    }

    /**
     * Writes branch states to `website_branches` and recalculates the open
     * flag of every website those branches belong to.
     */
    public function apply(array $states): array
    {
        $open = collect(array_keys(array_filter($states)));
        $closed = collect(array_keys(array_filter($states, fn ($state) => !$state)));

        if ($open->isNotEmpty()) {
            DB::table('website_branches')->whereIn('branch_id', $open)->where('status', 1)->update(['is_open' => 1]);
        }
        if ($closed->isNotEmpty()) {
            DB::table('website_branches')->whereIn('branch_id', $closed)->where('status', 1)->update(['is_open' => 0]);
        }

        $this->syncWebsiteStatus($open->merge($closed)->unique());

        return ['opened' => $open->all(), 'closed' => $closed->all()];
    }

    /**
     * A website is open while at least one of its active branches is open.
     */
    private function syncWebsiteStatus(Collection $branchIds): void
    {
        if ($branchIds->isEmpty()) {
            return;
        }

        $websiteIds = DB::table('website_branches')
            ->whereIn('branch_id', $branchIds)
            ->where('status', 1)
            ->distinct()
            ->pluck('website_id');

        if ($websiteIds->isEmpty()) {
            return;
        }

        $openWebsiteIds = DB::table('website_branches')
            ->whereIn('website_id', $websiteIds)
            ->where('status', 1)
            ->where('is_open', 1)
            ->distinct()
            ->pluck('website_id');

        $closedWebsiteIds = $websiteIds->diff($openWebsiteIds);

        if ($openWebsiteIds->isNotEmpty()) {
            DB::table('website_details')->whereIn('id', $openWebsiteIds)->update(['is_open' => 1]);
        }
        if ($closedWebsiteIds->isNotEmpty()) {
            DB::table('website_details')->whereIn('id', $closedWebsiteIds)->update(['is_open' => 0]);
        }
    }

    protected function rows(?int $branchId = null): Collection
    {
        return $this->normalise(DB::table('website_branches_schedule')
            ->where('status', 1)
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->get(['branch_id', 'day', 'opening_time', 'closing_time']));
    }

    protected function normalise(Collection $rows): Collection
    {
        return $rows
            ->filter(fn ($row) => $row->opening_time && $row->closing_time)
            ->map(fn ($row) => (object) [
                'branch_id' => (int) $row->branch_id,
                'day' => ucfirst(strtolower(substr(trim($row->day), 0, 3))),
                'open' => date('H:i', strtotime($row->opening_time)),
                'close' => date('H:i', strtotime($row->closing_time)),
            ])
            ->values();
    }
}
