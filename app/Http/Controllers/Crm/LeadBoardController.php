<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Crm\UpdateLeadBoardStatusRequest;
use App\Models\Crm\Lead;
use App\Models\Crm\LeadStatus;
use App\Services\Crm\LeadActivityLogger;
use App\Services\Crm\LeadBoardService;
use App\Services\Crm\LeadNotificationService;
use App\Support\CrmFilterState;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeadBoardController extends Controller
{
    public function __construct(
        private readonly LeadBoardService $boardService,
        private readonly LeadActivityLogger $activityLogger,
        private readonly LeadNotificationService $notificationService
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Lead::class);

        $filterState = CrmFilterState::restore($request, 'crm.board.filters', [
            'assigned_to',
            'lead_source_id',
            'product_type_id',
            'date_from',
            'date_to',
        ]);

        if ($filterState['redirect']) {
            return redirect()->route('crm.board', $filterState['values']);
        }

        $board = $this->boardService->boardData($request->user(), $request);

        return view('crm.leads.board', [
            'columns' => $board['columns'],
            'summary' => $board['summary'],
            'filters' => $filterState['values'],
            'canChangeStatusGlobally' => $request->user()->can('viewAny', Lead::class),
        ] + $this->boardService->filterOptions());
    }

    public function updateStatus(UpdateLeadBoardStatusRequest $request, Lead $lead): JsonResponse
    {
        $this->authorize('changeStatus', $lead);

        $newStatus = LeadStatus::query()->findOrFail($request->integer('status_id'));
        $oldStatusName = $lead->status?->name;
        $original = $lead->only([
            'status_id',
            'assigned_to',
            'is_converted',
            'contact_person_name',
            'company_name',
            'contact_number',
            'email',
            'city',
            'product_type_id',
            'product_id',
            'priority',
            'temperature',
            'expected_deal_value',
            'probability_percent',
            'next_followup_date',
        ]);

        $lead->update([
            'status_id' => $newStatus->id,
            'updated_by' => $request->user()->id,
        ]);

        $lead->load(['status', 'assignedUser']);
        $this->activityLogger->logLeadUpdated($lead, $original, $request->user());

        if ((string) ($original['status_id'] ?? null) !== (string) $lead->status_id) {
            $this->notificationService->notifyLeadStatusChanged($lead, $oldStatusName, $request->user());
        }

        return response()->json([
            'message' => 'Lead status updated successfully.',
            'status' => [
                'id' => $newStatus->id,
                'name' => $newStatus->name,
                'slug' => $newStatus->slug,
                'color' => $newStatus->color,
            ],
        ]);
    }
}
