<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ActivityLoggerTrait
{
    /**
     * Log activity in a generic and reusable way.
     *
     * @param string $logName
     * @param string $eventType
     * @param \Illuminate\Database\Eloquent\Model|null $subject
     * @param array|null $properties
     * @param string|null $description
     * @return void
     */
    public function logActivity(string $logName, string $eventType, $subject = null, ?array $properties = null, ?string $description = null)
    {
        $activity = activity($logName)
            ->causedBy(Auth::user());

        if ($subject) {
            $activity->performedOn($subject);
        }

        if (!empty($properties)) {
            $activity->withProperties($properties);
        }

        $description = $description ?? Auth::user()->fullname . " performed {$eventType} on {$logName}";

        $activity
            ->setEvent($eventType)
            ->log($description);
    }
}
