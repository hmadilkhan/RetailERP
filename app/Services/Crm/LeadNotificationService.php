<?php

namespace App\Services\Crm;

use App\Models\Crm\Lead;
use App\Models\Crm\LeadFollowup;
use App\Notifications\Crm\CrmLeadNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class LeadNotificationService
{
    public function __construct(private readonly LeadAccessService $leadAccessService)
    {
    }

    public function notifyLeadAssigned(Lead $lead, ?User $actor = null): void
    {
        $lead->loadMissing(['assignedUser', 'status']);

        $assignedName = $lead->assignedUser?->fullname ?? 'Unassigned';
        $message = sprintf(
            'Lead %s has been assigned to %s. Review the profile and plan the next action.',
            $lead->lead_code,
            $assignedName
        );

        $this->sendToRecipients(
            $this->relevantRecipients($lead, $actor),
            new CrmLeadNotification(
                lead: $lead,
                kind: 'lead_assigned',
                title: 'New CRM Lead Assignment',
                message: $message,
                actionUrl: route('crm.leads.show', $lead, false),
                dedupeKey: 'lead_assigned:' . $lead->id . ':' . ($lead->assigned_to ?? 'none'),
                meta: [
                    'assigned_to' => $lead->assigned_to,
                    'assigned_user_name' => $assignedName,
                ],
                sendMail: $this->mailAvailable()
            ),
            now()->subHours(12)
        );
    }

    public function notifyLeadStatusChanged(Lead $lead, ?string $oldStatusName, ?User $actor = null): void
    {
        $lead->loadMissing(['assignedUser', 'status']);

        $newStatusName = $lead->status?->name ?? 'Updated';
        $message = sprintf(
            'Lead %s status changed from %s to %s.',
            $lead->lead_code,
            $oldStatusName ?: 'N/A',
            $newStatusName
        );

        $this->sendToRecipients(
            $this->relevantRecipients($lead, $actor),
            new CrmLeadNotification(
                lead: $lead,
                kind: 'status_changed',
                title: 'Lead Status Updated',
                message: $message,
                actionUrl: route('crm.leads.show', $lead, false),
                dedupeKey: 'status_changed:' . $lead->id . ':' . ($oldStatusName ?: 'na') . ':' . $newStatusName,
                meta: [
                    'old_status' => $oldStatusName,
                    'new_status' => $newStatusName,
                ],
                sendMail: false
            ),
            now()->subHours(6)
        );
    }

    public function notifyFollowupAdded(Lead $lead, LeadFollowup $followup, ?User $actor = null): void
    {
        $lead->loadMissing(['assignedUser', 'status']);

        $message = $followup->next_followup_date
            ? sprintf(
                '%s follow-up was added for %s and the next follow-up is scheduled for %s.',
                $followup->followup_type,
                $lead->lead_code,
                $followup->next_followup_date->format('d M Y')
            )
            : sprintf(
                '%s follow-up was added for %s.',
                $followup->followup_type,
                $lead->lead_code
            );

        $this->sendToRecipients(
            $this->relevantRecipients($lead, $actor),
            new CrmLeadNotification(
                lead: $lead,
                kind: 'followup_added',
                title: 'Lead Follow-up Logged',
                message: $message,
                actionUrl: route('crm.leads.show', $lead, false),
                dedupeKey: 'followup_added:' . $lead->id . ':' . $followup->id,
                meta: [
                    'followup_id' => $followup->id,
                    'followup_type' => $followup->followup_type,
                    'followup_date' => $followup->followup_date->format('Y-m-d'),
                    'next_followup_date' => optional($followup->next_followup_date)->format('Y-m-d'),
                ],
                sendMail: false
            ),
            now()->subHours(2)
        );
    }

    public function sendDailyFollowupReminders(): int
    {
        $today = now()->startOfDay();
        $sentCount = 0;

        Lead::query()
            ->with(['assignedUser', 'status'])
            ->whereNotNull('next_followup_date')
            ->whereDate('next_followup_date', '<=', $today->toDateString())
            ->whereHas('status', fn (Builder $query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']))
            ->orderBy('next_followup_date')
            ->chunkById(100, function (Collection $leads) use ($today, &$sentCount): void {
                foreach ($leads as $lead) {
                    $kind = $lead->next_followup_date && $lead->next_followup_date->lt($today)
                        ? 'followup_overdue'
                        : 'followup_due_today';

                    $title = $kind === 'followup_overdue'
                        ? 'Overdue CRM Follow-up'
                        : 'Today\'s CRM Follow-up';

                    $message = $kind === 'followup_overdue'
                        ? sprintf(
                            'Lead %s follow-up is overdue since %s. Please review and update the lead.',
                            $lead->lead_code,
                            $lead->next_followup_date?->format('d M Y')
                        )
                        : sprintf(
                            'Lead %s has a follow-up due today. Keep the conversation moving and update the next action.',
                            $lead->lead_code
                        );

                    $notification = new CrmLeadNotification(
                        lead: $lead,
                        kind: $kind,
                        title: $title,
                        message: $message,
                        actionUrl: route('crm.leads.show', $lead, false),
                        dedupeKey: $kind . ':' . $lead->id . ':' . optional($lead->next_followup_date)->format('Y-m-d'),
                        meta: [
                            'next_followup_date' => optional($lead->next_followup_date)->format('Y-m-d'),
                            'priority' => $lead->priority,
                            'assigned_to' => $lead->assigned_to,
                        ],
                        sendMail: $this->mailAvailable()
                    );

                    $before = $sentCount;
                    $this->sendToRecipients($this->relevantRecipients($lead), $notification, now()->startOfDay(), function () use (&$sentCount): void {
                        $sentCount++;
                    });

                    if ($sentCount === $before) {
                        continue;
                    }
                }
            });

        return $sentCount;
    }

    public function layoutData(?User $user): array
    {
        if (!$user) {
            return [
                'crmUnreadNotificationsCount' => 0,
                'crmRecentNotifications' => collect(),
            ];
        }

        return [
            'crmUnreadNotificationsCount' => $user->unreadNotifications()
                ->where('data->module', 'crm')
                ->count(),
            'crmRecentNotifications' => $user->notifications()
                ->where('data->module', 'crm')
                ->latest()
                ->limit(8)
                ->get(),
        ];
    }

    public function dashboardReminderData(User $user): array
    {
        $today = now()->toDateString();
        $baseQuery = $this->visibleLeadsQuery($user)
            ->with(['assignedUser', 'status', 'leadSource'])
            ->whereNotNull('next_followup_date')
            ->whereHas('status', fn (Builder $query) => $query->whereNotIn('slug', ['won', 'lost', 'junk']));

        return [
            'dueToday' => (clone $baseQuery)
                ->whereDate('next_followup_date', $today)
                ->orderBy('next_followup_date')
                ->limit(6)
                ->get(),
            'overdue' => (clone $baseQuery)
                ->whereDate('next_followup_date', '<', $today)
                ->orderBy('next_followup_date')
                ->limit(6)
                ->get(),
            'recentNotifications' => $user->notifications()
                ->where('data->module', 'crm')
                ->latest()
                ->limit(6)
                ->get(),
        ];
    }

    public function markAsRead(User $user, string $notificationId): ?DatabaseNotification
    {
        $notification = $user->notifications()->find($notificationId);

        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }

        return $notification;
    }

    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications()
            ->where('data->module', 'crm')
            ->update(['read_at' => now()]);
    }

    private function relevantRecipients(Lead $lead, ?User $actor = null): Collection
    {
        $managerIds = User::query()
            ->select('user_details.id')
            ->leftJoin('user_authorization', function ($join): void {
                $join->on('user_authorization.user_id', '=', 'user_details.id')
                    ->where('user_authorization.status_id', 1);
            })
            ->leftJoin('user_roles', 'user_roles.role_id', '=', 'user_authorization.role_id')
            ->where(function ($query): void {
                $query
                    ->where('user_roles.role', 'like', '%admin%')
                    ->orWhere('user_roles.role', 'like', '%sales manager%');
            })
            ->pluck('user_details.id');

        $recipientIds = $managerIds
            ->push($lead->assigned_to)
            ->filter()
            ->unique()
            ->reject(fn ($id) => $actor && (int) $id === (int) $actor->id)
            ->values();

        if ($recipientIds->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $recipientIds)
            ->orderBy('fullname')
            ->get();
    }

    private function sendToRecipients(Collection $recipients, CrmLeadNotification $notification, Carbon $since, ?callable $afterSend = null): void
    {
        foreach ($recipients->unique('id') as $recipient) {
            if ($this->notificationExists($recipient, $notification, $since)) {
                continue;
            }

            $recipient->notify($notification);

            if ($afterSend) {
                $afterSend();
            }
        }
    }

    private function notificationExists(User $user, CrmLeadNotification $notification, Carbon $since): bool
    {
        $payload = $notification->toArray($user);

        return $user->notifications()
            ->where('type', $notification::class)
            ->where('created_at', '>=', $since)
            ->where('data->module', 'crm')
            ->where('data->dedupe_key', $payload['dedupe_key'])
            ->exists();
    }

    private function mailAvailable(): bool
    {
        $mailer = (string) config('mail.default');

        return $mailer !== '' && !in_array($mailer, ['array', 'log'], true);
    }

    private function visibleLeadsQuery(User $user): Builder
    {
        return $this->leadAccessService->visibleQuery($user);
    }
}
