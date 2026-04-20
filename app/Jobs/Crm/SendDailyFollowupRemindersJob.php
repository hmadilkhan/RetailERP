<?php

namespace App\Jobs\Crm;

use App\Services\Crm\LeadNotificationService;

class SendDailyFollowupRemindersJob
{
    public function handle(LeadNotificationService $notificationService): void
    {
        $notificationService->sendDailyFollowupReminders();
    }
}
