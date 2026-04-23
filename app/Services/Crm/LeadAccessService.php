<?php

namespace App\Services\Crm;

use App\Models\Crm\Lead;
use App\Support\CrmLeadPermissions;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LeadAccessService
{
    public function visibleQuery(User $user): Builder
    {
        $query = Lead::query();

        if (CrmLeadPermissions::canViewAll($user)) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($user): void {
            $builder
                ->where('assigned_to', $user->id)
                ->orWhere('created_by', $user->id);
        });
    }

    public function assignmentUsers(): Collection
    {
        return User::query()
            ->select('user_details.id', 'user_details.fullname')
            ->leftJoin('user_authorization', function ($join): void {
                $join->on('user_authorization.user_id', '=', 'user_details.id')
                    ->where('user_authorization.status_id', 1);
            })
            ->leftJoin('user_roles', 'user_roles.role_id', '=', 'user_authorization.role_id')
            ->where(function ($query): void {
                $query
                    ->where('user_roles.role', 'like', '%admin%')
                    ->orWhere('user_roles.role', 'like', '%sales manager%')
                    ->orWhere('user_roles.role', 'like', '%sales executive%')
                    ->orWhereNull('user_roles.role');
            })
            ->orderBy('user_details.fullname')
            ->distinct()
            ->get();
    }
}
