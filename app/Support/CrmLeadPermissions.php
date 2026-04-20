<?php

namespace App\Support;

use App\User;
use Illuminate\Support\Facades\DB;

class CrmLeadPermissions
{
    public const ADMIN = 'admin';
    public const SALES_MANAGER = 'sales_manager';
    public const SALES_EXECUTIVE = 'sales_executive';

    public static function resolveRole(?User $user = null): string
    {
        $user ??= auth()->user();

        if (!$user) {
            return self::SALES_EXECUTIVE;
        }

        $roleId = (int) session('roleId', 0);
        $roleName = self::roleName($user, $roleId);

        if ($roleId === 1 || str_contains($roleName, 'admin')) {
            return self::ADMIN;
        }

        if (str_contains($roleName, 'sales manager')) {
            return self::SALES_MANAGER;
        }

        if (str_contains($roleName, 'sales executive')) {
            return self::SALES_EXECUTIVE;
        }

        return self::SALES_EXECUTIVE;
    }

    public static function roleName(?User $user = null, ?int $fallbackRoleId = null): string
    {
        $user ??= auth()->user();

        if (!$user) {
            return '';
        }

        $roleId = DB::table('user_authorization')
            ->where('user_id', $user->id)
            ->where('status_id', 1)
            ->value('role_id');

        $roleId = $roleId ?: $fallbackRoleId;

        if (!$roleId) {
            return '';
        }

        return strtolower((string) DB::table('user_roles')->where('role_id', $roleId)->value('role'));
    }

    public static function roleLabel(?User $user = null): string
    {
        return match (self::resolveRole($user)) {
            self::ADMIN => 'Admin',
            self::SALES_MANAGER => 'Sales Manager',
            default => 'Sales Executive',
        };
    }

    public static function canAssign(?User $user = null): bool
    {
        return in_array(self::resolveRole($user), [self::ADMIN, self::SALES_MANAGER], true);
    }

    public static function canViewAll(?User $user = null): bool
    {
        return in_array(self::resolveRole($user), [self::ADMIN, self::SALES_MANAGER], true);
    }
}
