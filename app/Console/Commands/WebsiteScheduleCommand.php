<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WebsiteScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'website:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentTime = date("H:i");
        $branchIds = DB::table("website_branches_schedule")->where("opening_time", $currentTime)->pluck("branch_id");

        DB::table("website_branches")
            ->whereIn("branch_id", $branchIds)->where('status', 1)
            ->update([
                "is_open" => 1
            ]);

        if (DB::table('website_branches')->where('is_open', '=', 1)->where('status', 1)->count() > 0) {

            $getWebsiteId = DB::table("website_branches")
                ->where('is_open', '=', 1)
                ->where('status', 1)
                ->groupBy('website_id')
                ->pluck('website_id');

            if (count($getWebsiteId) > 0) {
                DB::table("website_details")
                    ->whereIn("id", $getWebsiteId)
                    ->update([
                        "is_open" => 1
                    ]);
            }
        }

        $branchIds = DB::table("website_branches_schedule")->where("closing_time", $currentTime)->pluck("branch_id");

        $websiteIds = DB::table("website_branches")
            ->whereIn("branch_id", $branchIds)
            ->where('status', 1)
            ->update([
                "is_open" => 0
            ]);

        if (DB::table('website_branches')->where('is_open', '=', 0)->count() > 0) {

            $getWebsiteId = DB::table("website_branches")
                ->where('is_open', '=', 0)
                ->where('status', 1)
                ->whereIn('branch_id', DB::table('website_branches_schedule')->groupBy('branch_id')->pluck('branch_id'))
                ->groupBy('website_id')
                ->pluck('website_id');

            if (count($getWebsiteId) > 0) {
                DB::table("website_details")
                    ->whereIn("id", $getWebsiteId)
                    ->update([
                        "is_open" => 0
                    ]);
            }
        }
    }
}
