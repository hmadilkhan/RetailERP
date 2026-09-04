<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WebsiteScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:website-schedule-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Open/close website branches according to their weekly schedule';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $currentTime = $now->format("H:i");
        $today = $now->format("D");
        $yesterday = $now->copy()->subDay()->format("D");

        $openBranchIds = $this->branchesOpeningAt($currentTime, $today);
        $closeBranchIds = $this->branchesClosingAt($currentTime, $today, $yesterday);

        if ($openBranchIds->isEmpty() && $closeBranchIds->isEmpty()) {
            return self::SUCCESS;
        }

        if ($openBranchIds->isNotEmpty()) {
            DB::table("website_branches")
                ->whereIn("branch_id", $openBranchIds)
                ->where("status", 1)
                ->update(["is_open" => 1]);

            $this->info("[{$currentTime}] Opened branches: " . $openBranchIds->implode(", "));
        }

        if ($closeBranchIds->isNotEmpty()) {
            DB::table("website_branches")
                ->whereIn("branch_id", $closeBranchIds)
                ->where("status", 1)
                ->update(["is_open" => 0]);

            $this->info("[{$currentTime}] Closed branches: " . $closeBranchIds->implode(", "));
        }

        $this->syncWebsiteStatus($openBranchIds->merge($closeBranchIds)->unique());

        return self::SUCCESS;
    }

    /**
     * Branch ids whose schedule for today starts at the given time.
     */
    private function branchesOpeningAt(string $currentTime, string $today)
    {
        return DB::table("website_branches_schedule")
            ->where("status", 1)
            ->where("day", $today)
            ->where("opening_time", $currentTime)
            ->distinct()
            ->pluck("branch_id");
    }

    /**
     * Branch ids whose schedule ends at the given time.
     *
     * A schedule whose closing_time is not after its opening_time runs past
     * midnight, so that closing belongs to the following calendar day and has
     * to be matched against yesterday's row.
     */
    private function branchesClosingAt(string $currentTime, string $today, string $yesterday)
    {
        return DB::table("website_branches_schedule")
            ->where("status", 1)
            ->where("closing_time", $currentTime)
            ->where(function ($query) use ($today, $yesterday) {
                $query->where(function ($sameDay) use ($today) {
                    $sameDay->where("day", $today)
                        ->whereColumn("closing_time", ">", "opening_time");
                })->orWhere(function ($overnight) use ($yesterday) {
                    $overnight->where("day", $yesterday)
                        ->whereColumn("closing_time", "<=", "opening_time");
                });
            })
            ->distinct()
            ->pluck("branch_id");
    }

    /**
     * A website is open while at least one of its active branches is open.
     * Only the websites touched by this run are recalculated.
     */
    private function syncWebsiteStatus(Collection $branchIds)
    {
        if ($branchIds->isEmpty()) {
            return;
        }

        $websiteIds = DB::table("website_branches")
            ->whereIn("branch_id", $branchIds)
            ->where("status", 1)
            ->distinct()
            ->pluck("website_id");

        if ($websiteIds->isEmpty()) {
            return;
        }

        $openWebsiteIds = DB::table("website_branches")
            ->whereIn("website_id", $websiteIds)
            ->where("status", 1)
            ->where("is_open", 1)
            ->distinct()
            ->pluck("website_id");

        $closedWebsiteIds = $websiteIds->diff($openWebsiteIds);

        if ($openWebsiteIds->isNotEmpty()) {
            DB::table("website_details")
                ->whereIn("id", $openWebsiteIds)
                ->update(["is_open" => 1]);
        }

        if ($closedWebsiteIds->isNotEmpty()) {
            DB::table("website_details")
                ->whereIn("id", $closedWebsiteIds)
                ->update(["is_open" => 0]);
        }
    }
}
