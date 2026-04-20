<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Services\Crm\LeadNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly LeadNotificationService $notificationService)
    {
    }

    public function open(Request $request, string $notification): RedirectResponse
    {
        $record = $this->notificationService->markAsRead($request->user(), $notification);

        if (!$record) {
            return redirect()
                ->route('crm.dashboard')
                ->with('crm_duplicate_warning', 'Notification could not be found.');
        }

        $targetUrl = data_get($record->data, 'action_url', route('crm.dashboard'));

        return redirect($targetUrl);
    }

    public function readAll(Request $request): RedirectResponse
    {
        $this->notificationService->markAllAsRead($request->user());

        return back()->with('crm_success', 'All CRM notifications marked as read.');
    }
}
