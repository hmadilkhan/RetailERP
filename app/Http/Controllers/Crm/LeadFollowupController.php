<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\StoreLeadFollowupRequest;
use App\Models\Crm\Lead;
use App\Services\Crm\LeadActivityLogger;
use App\Services\Crm\LeadNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LeadFollowupController extends Controller
{
    public function __construct(
        private readonly LeadActivityLogger $activityLogger,
        private readonly LeadNotificationService $notificationService
    )
    {
    }

    public function store(StoreLeadFollowupRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('addFollowup', $lead);

        DB::transaction(function () use ($request, $lead): void {
            $followup = $lead->followups()->create($request->validated() + [
                'created_by' => auth()->id(),
            ]);

            $lastContactDate = $lead->last_contact_date;
            if ($lastContactDate === null || $followup->followup_date->gt($lastContactDate)) {
                $lead->last_contact_date = $followup->followup_date;
            }

            if ($followup->next_followup_date) {
                $lead->next_followup_date = $followup->next_followup_date;
            }

            $lead->updated_by = auth()->id();
            $lead->save();

            $this->activityLogger->logFollowupAdded($lead, $followup, auth()->user());
            $this->notificationService->notifyFollowupAdded($lead->load(['status', 'assignedUser']), $followup, auth()->user());
        });

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('crm_success', 'Follow-up saved successfully.');
    }
}
