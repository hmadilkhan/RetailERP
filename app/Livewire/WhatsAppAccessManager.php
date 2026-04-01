<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\WhatsAppUser;
use App\WhatsAppUserAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class WhatsAppAccessManager extends Component
{
    use WithPagination;

    public $search = '';
    public $companyFilter = '';
    public $scopeFilter = '';

    public $editingUserId = null;
    public $user_name = '';
    public $mobile_number = '';
    public $user_status = 1;

    public $editingAccessId = null;
    public $whatsapp_user_id = '';
    public $company_id = '';
    public $branch_id = '';
    public $access_level = 'company';
    public $access_status = 1;

    protected $paginationTheme = 'bootstrap';
    protected bool $userHasNameColumn = false;
    protected bool $userHasStatusColumn = false;
    protected bool $userHasCreatedAtColumn = false;
    protected bool $userHasUpdatedAtColumn = false;
    protected bool $accessHasStatusColumn = false;
    protected bool $accessHasCreatedAtColumn = false;
    protected bool $accessHasUpdatedAtColumn = false;

    public function mount(): void
    {
        abort_unless(session('roleId') == 1, 403);
    }

    public function boot(): void
    {
        $this->userHasNameColumn = $this->hasColumn('whatsapp_users', 'name');
        $this->userHasStatusColumn = $this->hasColumn('whatsapp_users', 'status');
        $this->userHasCreatedAtColumn = $this->hasColumn('whatsapp_users', 'created_at');
        $this->userHasUpdatedAtColumn = $this->hasColumn('whatsapp_users', 'updated_at');
        $this->accessHasStatusColumn = $this->hasColumn('whatsapp_user_access', 'status');
        $this->accessHasCreatedAtColumn = $this->hasColumn('whatsapp_user_access', 'created_at');
        $this->accessHasUpdatedAtColumn = $this->hasColumn('whatsapp_user_access', 'updated_at');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('usersPage');
        $this->resetPage('accessPage');
    }

    public function updatedCompanyFilter(): void
    {
        $this->resetPage('accessPage');
    }

    public function updatedScopeFilter(): void
    {
        $this->resetPage('accessPage');
    }

    public function updatedCompanyId($value): void
    {
        if (!$value) {
            $this->branch_id = '';
            return;
        }

        if ($this->access_level === 'branch' && !$this->branches->pluck('branch_id')->contains((int) $this->branch_id)) {
            $this->branch_id = '';
        }
    }

    public function updatedAccessLevel($value): void
    {
        if ($value === 'company') {
            $this->branch_id = '';
        }
    }

    public function saveUser(): void
    {
        $rules = [
            'mobile_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('whatsapp_users', 'mobile_number')->ignore($this->editingUserId),
            ],
        ];

        if ($this->userHasNameColumn) {
            $rules['user_name'] = ['nullable', 'string', 'max:150'];
        }

        if ($this->userHasStatusColumn) {
            $rules['user_status'] = ['required', 'integer', 'in:0,1'];
        }

        $validated = $this->validate($rules, [
            'mobile_number.unique' => 'This mobile number is already assigned.',
        ]);

        $payload = [
            'mobile_number' => $this->normalizeMobile($validated['mobile_number']),
        ];

        if ($this->userHasNameColumn) {
            $payload['name'] = trim((string) $this->user_name) ?: null;
        }

        if ($this->userHasStatusColumn) {
            $payload['status'] = (int) $this->user_status;
        }

        if ($this->userHasUpdatedAtColumn) {
            $payload['updated_at'] = now();
        }

        if ($this->editingUserId) {
            WhatsAppUser::whereKey($this->editingUserId)->update($payload);
            $message = 'WhatsApp user updated successfully.';
        } else {
            if ($this->userHasCreatedAtColumn) {
                $payload['created_at'] = now();
            }

            $user = WhatsAppUser::create($payload);
            $this->whatsapp_user_id = (string) $user->id;
            $message = 'WhatsApp user created successfully.';
        }

        $this->resetUserForm();
        session()->flash('whatsapp_access_message', $message);
        $this->resetPage('usersPage');
    }

    public function editUser(int $userId): void
    {
        $user = WhatsAppUser::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->mobile_number = $user->mobile_number;
        $this->user_name = $this->userHasNameColumn ? (string) ($user->name ?? '') : '';
        $this->user_status = $this->userHasStatusColumn ? (int) ($user->status ?? 1) : 1;
    }

    public function resetUserForm(): void
    {
        $this->editingUserId = null;
        $this->user_name = '';
        $this->mobile_number = '';
        $this->user_status = 1;
        $this->resetValidation();
    }

    public function saveAccess(): void
    {
        $validated = $this->validate([
            'whatsapp_user_id' => ['required', 'exists:whatsapp_users,id'],
            'company_id' => ['required', 'exists:company,company_id'],
            'access_level' => ['required', Rule::in(['company', 'branch'])],
        ]);

        if ($this->access_level === 'branch') {
            $this->validate([
                'branch_id' => [
                    'required',
                    Rule::exists('branch', 'branch_id')->where(function ($query) {
                        $query->where('company_id', $this->company_id);
                    }),
                ],
            ], [
                'branch_id.required' => 'Please select a branch for branch-level access.',
            ]);
        }

        $duplicateCheck = WhatsAppUserAccess::query()
            ->where('whatsapp_user_id', $validated['whatsapp_user_id'])
            ->where('company_id', $validated['company_id'])
            ->where('access_level', $validated['access_level'])
            ->when($this->access_level === 'branch', function ($query) {
                $query->where('branch_id', $this->branch_id);
            }, function ($query) {
                $query->whereNull('branch_id');
            })
            ->when($this->editingAccessId, function ($query) {
                $query->where('id', '!=', $this->editingAccessId);
            })
            ->exists();

        if ($duplicateCheck) {
            $this->addError('access_level', 'This access rule already exists for the selected user.');
            return;
        }

        $payload = [
            'whatsapp_user_id' => (int) $validated['whatsapp_user_id'],
            'company_id' => (int) $validated['company_id'],
            'branch_id' => $this->access_level === 'branch' ? (int) $this->branch_id : null,
            'access_level' => $validated['access_level'],
        ];

        if ($this->accessHasStatusColumn) {
            $payload['status'] = (int) $this->access_status;
        }

        if ($this->accessHasUpdatedAtColumn) {
            $payload['updated_at'] = now();
        }

        if ($this->editingAccessId) {
            WhatsAppUserAccess::whereKey($this->editingAccessId)->update($payload);
            $message = 'Access rule updated successfully.';
        } else {
            if ($this->accessHasCreatedAtColumn) {
                $payload['created_at'] = now();
            }

            WhatsAppUserAccess::create($payload);
            $message = 'Access rule created successfully.';
        }

        $this->resetAccessForm();
        session()->flash('whatsapp_access_message', $message);
        $this->resetPage('accessPage');
    }

    public function editAccess(int $accessId): void
    {
        $access = WhatsAppUserAccess::findOrFail($accessId);

        $this->editingAccessId = $access->id;
        $this->whatsapp_user_id = (string) $access->whatsapp_user_id;
        $this->company_id = (string) $access->company_id;
        $this->access_level = $access->access_level;
        $this->branch_id = $access->branch_id ? (string) $access->branch_id : '';
        $this->access_status = $this->accessHasStatusColumn ? (int) ($access->status ?? 1) : 1;
    }

    public function resetAccessForm(): void
    {
        $this->editingAccessId = null;
        $this->whatsapp_user_id = '';
        $this->company_id = '';
        $this->branch_id = '';
        $this->access_level = 'company';
        $this->access_status = 1;
        $this->resetValidation();
    }

    public function toggleUserStatus(int $userId): void
    {
        if (!$this->userHasStatusColumn) {
            return;
        }

        $user = WhatsAppUser::findOrFail($userId);
        $newStatus = (int) !((int) $user->status);

        $payload = ['status' => $newStatus];
        if ($this->userHasUpdatedAtColumn) {
            $payload['updated_at'] = now();
        }

        $user->update($payload);
        session()->flash('whatsapp_access_message', 'User status updated.');
    }

    public function toggleAccessStatus(int $accessId): void
    {
        if (!$this->accessHasStatusColumn) {
            return;
        }

        $access = WhatsAppUserAccess::findOrFail($accessId);
        $newStatus = (int) !((int) $access->status);

        $payload = ['status' => $newStatus];
        if ($this->accessHasUpdatedAtColumn) {
            $payload['updated_at'] = now();
        }

        $access->update($payload);
        session()->flash('whatsapp_access_message', 'Access status updated.');
    }

    public function getCompaniesProperty(): Collection
    {
        return Company::query()
            ->select('company_id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function getBranchesProperty(): Collection
    {
        if (!$this->company_id) {
            return collect();
        }

        return Branch::query()
            ->select('branch_id', 'branch_name', 'company_id')
            ->where('company_id', $this->company_id)
            ->orderBy('branch_name')
            ->get();
    }

    public function getUsersProperty()
    {
        $query = WhatsAppUser::query()
            ->select($this->getUserListingColumns())
            ->when($this->search !== '', function ($query) {
                $query->where(function ($innerQuery) {
                    $innerQuery->where('mobile_number', 'like', '%' . $this->search . '%');

                    if ($this->userHasNameColumn) {
                        $innerQuery->orWhere('name', 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->when($this->userHasStatusColumn, fn ($query) => $query->orderByDesc('status'))
            ->orderByDesc('id');

        return $query->simplePaginate(8, ['*'], 'usersPage');
    }

    public function getAccessesProperty()
    {
        return DB::table('whatsapp_user_access')
            ->join('whatsapp_users', 'whatsapp_users.id', '=', 'whatsapp_user_access.whatsapp_user_id')
            ->join('company', 'company.company_id', '=', 'whatsapp_user_access.company_id')
            ->leftJoin('branch', 'branch.branch_id', '=', 'whatsapp_user_access.branch_id')
            ->select(
                'whatsapp_user_access.id',
                'whatsapp_user_access.access_level',
                'whatsapp_user_access.branch_id',
                'whatsapp_user_access.company_id',
                'whatsapp_user_access.whatsapp_user_id',
                DB::raw($this->accessHasStatusColumn ? 'whatsapp_user_access.status as status' : '1 as status'),
                'whatsapp_users.mobile_number',
                DB::raw($this->userHasNameColumn ? 'whatsapp_users.name as user_name' : 'NULL as user_name'),
                'company.name as company_name',
                'branch.branch_name'
            )
            ->when($this->search !== '', function ($query) {
                $query->where(function ($innerQuery) {
                    $innerQuery->where('whatsapp_users.mobile_number', 'like', '%' . $this->search . '%')
                        ->orWhere('company.name', 'like', '%' . $this->search . '%')
                        ->orWhere('branch.branch_name', 'like', '%' . $this->search . '%');

                    if ($this->userHasNameColumn) {
                        $innerQuery->orWhere('whatsapp_users.name', 'like', '%' . $this->search . '%');
                    }
                });
            })
            ->when($this->companyFilter !== '', fn ($query) => $query->where('whatsapp_user_access.company_id', $this->companyFilter))
            ->when($this->scopeFilter !== '', fn ($query) => $query->where('whatsapp_user_access.access_level', $this->scopeFilter))
            ->orderByDesc('whatsapp_user_access.id')
            ->simplePaginate(10, ['*'], 'accessPage');
    }

    public function getStatsProperty(): array
    {
        $userStats = WhatsAppUser::query()
            ->selectRaw('COUNT(*) as users')
            ->selectRaw(
                $this->userHasStatusColumn
                    ? 'SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_users'
                    : 'COUNT(*) as active_users'
            )
            ->first();

        $accessStats = WhatsAppUserAccess::query()
            ->selectRaw('COUNT(*) as access_rules')
            ->selectRaw("SUM(CASE WHEN access_level = 'branch' THEN 1 ELSE 0 END) as branch_scope")
            ->first();

        return [
            'users' => (int) ($userStats->users ?? 0),
            'active_users' => (int) ($userStats->active_users ?? 0),
            'access_rules' => (int) ($accessStats->access_rules ?? 0),
            'branch_scope' => (int) ($accessStats->branch_scope ?? 0),
        ];
    }

    #[Title('WhatsApp Access Manager')]
    public function render()
    {
        return view('livewire.whats-app-access-manager', [
            'users' => $this->users,
            'accesses' => $this->accesses,
            'companies' => $this->companies,
            'branches' => $this->branches,
            'stats' => $this->stats,
            'userOptions' => WhatsAppUser::query()
                ->select(
                    'id',
                    'mobile_number',
                    DB::raw($this->userHasNameColumn ? 'name' : 'NULL as name')
                )
                ->when($this->userHasStatusColumn, fn ($query) => $query->where('status', 1))
                ->orderBy('mobile_number')
                ->get(),
        ]);
    }

    private function normalizeMobile(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value);
        return $digits ?: trim($value);
    }

    private function hasColumn(string $table, string $column): bool
    {
        static $cache = [];
        $key = $table . '.' . $column;

        if (!array_key_exists($key, $cache)) {
            try {
                $cache[$key] = Schema::hasColumn($table, $column);
            } catch (\Throwable $e) {
                $cache[$key] = false;
            }
        }

        return $cache[$key];
    }

    private function getUserListingColumns(): array
    {
        $columns = ['id', 'mobile_number'];

        if ($this->userHasNameColumn) {
            $columns[] = 'name';
        }

        if ($this->userHasStatusColumn) {
            $columns[] = 'status';
        }

        return $columns;
    }
}
