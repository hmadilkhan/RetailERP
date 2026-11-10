<?php

namespace App\Console\Commands;

use App\Services\Website\BranchScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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

    /** Last minute this command handled. */
    private const LAST_RUN_KEY = 'website-schedule:last-run';

    /** Never replay more than this many minutes (server down for hours). */
    private const MAX_CATCH_UP_MINUTES = 60;

    /**
     * Execute the console command.
     *
     * Every opening / closing time since the last run is applied, not only the
     * current minute: `schedule:run` runs its events one after another, so at
     * :00 / :15 / :30 / :45 this command starts after `accounting:post` etc.
     * and the exact minute (e.g. 15:00) has already passed. A cron tick that
     * was skipped is caught up the same way.
     */
    public function handle(BranchScheduleService $schedule)
    {
        $now = now()->startOfMinute();

        $lastRun = Cache::get(self::LAST_RUN_KEY);
        $from = $lastRun ? Carbon::parse($lastRun) : $now->copy()->subMinute();

        if ($from->lt($now->copy()->subMinutes(self::MAX_CATCH_UP_MINUTES))) {
            $from = $now->copy()->subMinutes(self::MAX_CATCH_UP_MINUTES);
        }

        $states = $from->lt($now) ? $schedule->transitions($from, $now) : [];

        Cache::forever(self::LAST_RUN_KEY, $now->toDateTimeString());

        if (empty($states)) {
            return self::SUCCESS;
        }

        $result = $schedule->apply($states);
        $time = $now->format('H:i');

        if ($result['opened']) {
            $this->info("[{$time}] Opened branches: " . implode(', ', $result['opened']));
        }
        if ($result['closed']) {
            $this->info("[{$time}] Closed branches: " . implode(', ', $result['closed']));
        }

        return self::SUCCESS;
    }
}
