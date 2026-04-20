<?php

namespace App\Policies\Crm;

use App\Models\Crm\Lead;
use App\Support\CrmLeadPermissions;
use App\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array(
            CrmLeadPermissions::resolveRole($user),
            [CrmLeadPermissions::ADMIN, CrmLeadPermissions::SALES_MANAGER, CrmLeadPermissions::SALES_EXECUTIVE],
            true
        );
    }

    public function view(User $user, Lead $lead): bool
    {
        if (CrmLeadPermissions::canViewAll($user)) {
            return true;
        }

        return (int) $lead->assigned_to === (int) $user->id
            || (int) $lead->created_by === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return in_array(
            CrmLeadPermissions::resolveRole($user),
            [CrmLeadPermissions::ADMIN, CrmLeadPermissions::SALES_MANAGER, CrmLeadPermissions::SALES_EXECUTIVE],
            true
        );
    }

    public function update(User $user, Lead $lead): bool
    {
        if (CrmLeadPermissions::canViewAll($user)) {
            return true;
        }

        return (int) $lead->assigned_to === (int) $user->id
            || (int) $lead->created_by === (int) $user->id;
    }

    public function delete(User $user, Lead $lead): bool
    {
        return CrmLeadPermissions::resolveRole($user) === CrmLeadPermissions::ADMIN;
    }

    public function assign(User $user, Lead $lead): bool
    {
        return CrmLeadPermissions::canAssign($user);
    }

    public function bulkAssign(User $user): bool
    {
        return CrmLeadPermissions::canAssign($user);
    }

    public function addFollowup(User $user, Lead $lead): bool
    {
        return $this->update($user, $lead);
    }

    public function uploadAttachment(User $user, Lead $lead): bool
    {
        return $this->update($user, $lead);
    }

    public function changeStatus(User $user, Lead $lead): bool
    {
        return $this->update($user, $lead);
    }

    public function convert(User $user, Lead $lead): bool
    {
        return $this->update($user, $lead);
    }

    public function export(User $user): bool
    {
        return in_array(
            CrmLeadPermissions::resolveRole($user),
            [CrmLeadPermissions::ADMIN, CrmLeadPermissions::SALES_MANAGER],
            true
        );
    }
}
