<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Events\LeaveImpersonation;
use Lab404\Impersonate\Events\TakeImpersonation;

class SyncImpersonationSession
{
    public function handle($event): void
    {
        if ($event instanceof TakeImpersonation) {
            $this->syncForUser($event->impersonated);
            return;
        }

        if ($event instanceof LeaveImpersonation) {
            $this->syncForUser($event->impersonator);
        }
    }

    private function syncForUser($user): void
    {
        $authorization = DB::table('user_authorization')
            ->where('user_id', $user->id)
            ->where('status_id', 1)
            ->first();

        if (!$authorization) {
            session()->forget(['userid', 'company_id', 'branch', 'roleId', 'image']);
            return;
        }

        session([
            'userid' => $user->id,
            'company_id' => $authorization->company_id,
            'branch' => $authorization->branch_id,
            'roleId' => $authorization->role_id,
            'image' => $user->image,
        ]);
    }
}
