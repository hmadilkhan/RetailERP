<?php

namespace App\Notifications\Crm;

use App\Models\Crm\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CrmLeadNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Lead $lead,
        private readonly string $kind,
        private readonly string $title,
        private readonly string $message,
        private readonly string $actionUrl,
        private readonly string $dedupeKey,
        private readonly array $meta = [],
        private readonly bool $sendMail = false
    ) {
    }

    public function via(object $notifiable): array
    {
        return array_values(array_filter([
            'database',
            $this->sendMail ? 'mail' : null,
        ]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'module' => 'crm',
            'kind' => $this->kind,
            'title' => $this->title,
            'message' => $this->message,
            'lead_id' => $this->lead->id,
            'lead_code' => $this->lead->lead_code,
            'lead_name' => $this->lead->contact_person_name,
            'company_name' => $this->lead->company_name,
            'action_url' => $this->actionUrl,
            'dedupe_key' => $this->dedupeKey,
            'meta' => $this->meta,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->title . ' | CRM')
            ->greeting('Hello ' . ($notifiable->fullname ?? $notifiable->username ?? 'Team') . ',')
            ->line($this->message)
            ->action('Open Lead', url($this->actionUrl))
            ->line('This alert was generated from the CRM workspace inside your ERP system.');
    }
}
