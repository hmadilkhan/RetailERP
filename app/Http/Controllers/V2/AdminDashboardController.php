<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if ((int) session('roleId') !== 1) {
            return redirect()->route('home');
        }

        $cards = [
            [
                'label' => 'Users',
                'value' => $this->countTable('user_details'),
                'caption' => 'Registered staff accounts',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Roles',
                'value' => $this->countTable('user_roles'),
                'caption' => 'Access profiles configured',
                'tone' => 'indigo',
            ],
            [
                'label' => 'Companies',
                'value' => $this->countTable('company'),
                'caption' => 'Business entities onboarded',
                'tone' => 'sky',
            ],
            [
                'label' => 'Branches',
                'value' => $this->countTable('branch'),
                'caption' => 'Operational locations',
                'tone' => 'amber',
            ],
            [
                'label' => 'Terminals',
                'value' => $this->countTable('terminal_details'),
                'caption' => 'POS devices in system',
                'tone' => 'rose',
            ],
            [
                'label' => 'Modules',
                'value' => $this->countTable('pages_details'),
                'caption' => 'Sidebar pages and tools',
                'tone' => 'slate',
            ],
        ];

        return view('v2.admin-dashboard', [
            'cards' => $cards,
            'recentUsers' => $this->recentUsers(),
            'recentCompanies' => $this->recentCompanies(),
            'moduleModes' => $this->moduleModes(),
            'rolePermissionRows' => $this->rolePermissionRows(),
            'branchRows' => $this->branchRows(),
        ]);
    }

    private function countTable(string $table): int
    {
        return $this->safe(function () use ($table) {
            return Schema::hasTable($table) ? DB::table($table)->count() : 0;
        }, 0);
    }

    private function recentUsers()
    {
        return $this->safe(function () {
            if (!Schema::hasTable('user_details')) {
                return collect();
            }

            $query = DB::table('user_details as users')
                ->leftJoin('user_authorization as authz', 'authz.user_id', '=', 'users.id')
                ->leftJoin('user_roles as roles', 'roles.role_id', '=', 'authz.role_id')
                ->leftJoin('branch', 'branch.branch_id', '=', 'authz.branch_id')
                ->select([
                    'users.id',
                    'users.fullname',
                    'users.username',
                    'users.email',
                    'users.created_at',
                    'roles.role',
                    'branch.branch_name',
                ])
                ->orderByDesc('users.id')
                ->limit(6);

            return $query->get();
        }, collect());
    }

    private function recentCompanies()
    {
        return $this->safe(function () {
            if (!Schema::hasTable('company')) {
                return collect();
            }

            return DB::table('company')
                ->orderByDesc('company_id')
                ->limit(5)
                ->get();
        }, collect());
    }

    private function moduleModes()
    {
        return $this->safe(function () {
            if (!Schema::hasTable('pages_details')) {
                return collect();
            }

            return DB::table('pages_details')
                ->select('page_mode', DB::raw('COUNT(*) as total'))
                ->groupBy('page_mode')
                ->orderByDesc('total')
                ->get();
        }, collect());
    }

    private function rolePermissionRows()
    {
        return $this->safe(function () {
            if (!Schema::hasTable('role_settings') || !Schema::hasTable('user_roles') || !Schema::hasTable('pages_details')) {
                return collect();
            }

            return DB::table('role_settings as settings')
                ->join('user_roles as roles', 'roles.role_id', '=', 'settings.role_id')
                ->join('pages_details as pages', 'pages.id', '=', 'settings.page_id')
                ->select('roles.role', DB::raw('COUNT(pages.id) as total_pages'))
                ->groupBy('roles.role')
                ->orderByDesc('total_pages')
                ->limit(6)
                ->get();
        }, collect());
    }

    private function branchRows()
    {
        return $this->safe(function () {
            if (!Schema::hasTable('branch')) {
                return collect();
            }

            $query = DB::table('branch')
                ->leftJoin('company', 'company.company_id', '=', 'branch.company_id')
                ->select([
                    'branch.branch_id',
                    'branch.branch_name',
                    'company.name as company_name',
                ])
                ->orderByDesc('branch.branch_id')
                ->limit(6);

            return $query->get();
        }, collect());
    }

    private function safe(callable $callback, $fallback)
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
            return $fallback;
        }
    }
}
