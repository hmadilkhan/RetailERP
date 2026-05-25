<?php

namespace App\Livewire\Terminals;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Terminal;
use App\Services\TerminalLockService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class TerminalManager extends Component
{
    use WithPagination;

    public $filterCompanyId = '';
    public $filterBranchId = '';
    public $statusId = 1;
    public $lockStatus = '';
    public $deviceStatusFilter = '';
    public $search = '';

    public $terminalId = null;
    public $formCompanyId = '';
    public $formBranchId = '';
    public $terminalName = '';
    public $macAddress = '';
    public $serialNo = '';
    public $modelNo = '';

    public array $deviceStatuses = [];
    public array $revealedPasswords = [];

    public function mount(): void
    {
        if ((int) session('roleId') === 2) {
            $this->filterCompanyId = (string) session('company_id');
            $this->formCompanyId = (string) session('company_id');
        }

        if (!in_array((int) session('roleId'), [1, 2], true)) {
            $this->filterCompanyId = (string) session('company_id');
            $this->filterBranchId = (string) session('branch');
            $this->formCompanyId = (string) session('company_id');
            $this->formBranchId = (string) session('branch');
        }
    }

    public function updatedFilterCompanyId(): void
    {
        $this->filterBranchId = '';
        $this->deviceStatuses = [];
        $this->resetPage();
    }

    public function updatedFilterBranchId(): void
    {
        $this->deviceStatuses = [];
        $this->resetPage();
    }

    public function updatedFormCompanyId(): void
    {
        $this->formBranchId = '';
    }

    public function updatedStatusId(): void
    {
        $this->deviceStatuses = [];
        $this->resetPage();
    }

    public function updatedLockStatus(): void
    {
        $this->deviceStatuses = [];
        $this->resetPage();
    }

    public function updatedDeviceStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->deviceStatuses = [];
        $this->resetPage();
    }

    public function saveTerminal(): void
    {
        $this->validate([
            'formCompanyId' => ['required', 'integer', 'exists:company,company_id'],
            'formBranchId' => ['required', 'integer', 'exists:branch,branch_id'],
            'terminalName' => ['required', 'string', 'max:100'],
            'macAddress' => ['nullable', 'string', 'max:100'],
            'serialNo' => ['nullable', 'string', 'max:100'],
            'modelNo' => ['nullable', 'string', 'max:100'],
        ]);

        $duplicate = Terminal::query()
            ->where('branch_id', $this->formBranchId)
            ->where(function ($query) {
                $query->where('terminal_name', $this->terminalName);

                if ($this->macAddress !== '') {
                    $query->orWhere('mac_address', $this->macAddress);
                }
            })
            ->when($this->terminalId, function ($query) {
                $query->where('terminal_id', '!=', $this->terminalId);
            })
            ->exists();

        if ($duplicate) {
            session()->flash('terminal_error', 'Terminal name or MAC address already exists in this branch.');
            return;
        }

        if ($this->terminalId) {
            Terminal::query()->where('terminal_id', $this->terminalId)->update([
                'branch_id' => (int) $this->formBranchId,
                'terminal_name' => $this->terminalName,
                'mac_address' => $this->macAddress,
                'serial_no' => $this->serialNo,
                'model_no' => $this->modelNo,
                'status_id' => 1,
            ]);

            $this->logTerminalActivity((int) $this->terminalId, 'terminal_updated', 'Terminal updated from Livewire terminal manager.');
            session()->flash('terminal_message', 'Terminal updated successfully.');
        } else {
            $terminalId = DB::table('terminal_details')->insertGetId([
                'branch_id' => (int) $this->formBranchId,
                'terminal_name' => $this->terminalName,
                'mac_address' => $this->macAddress,
                'serial_no' => $this->serialNo,
                'model_no' => $this->modelNo,
                'status_id' => 1,
            ]);

            DB::table('users_sales_permission')->insert([
                'user_id' => session('userid'),
                'terminal_id' => $terminalId,
                'ob' => 1,
                'cb' => 1,
                'cash_sale' => 1,
                'card_sale' => 1,
                'customer_credit_sale' => 1,
                'cost' => 1,
                'r_cash' => 1,
                'r_card' => 1,
                'r_cheque' => 1,
                'sale_return' => 1,
                'discount' => 1,
                'cash_in' => 1,
                'cash_out' => 1,
            ]);

            $this->logTerminalActivity((int) $terminalId, 'terminal_created', 'Terminal created from Livewire terminal manager.');
            session()->flash('terminal_message', 'Terminal saved successfully.');
        }

        $this->resetTerminalForm();
        $this->statusId = 1;
    }

    public function editTerminal(int $terminalId): void
    {
        $terminal = Terminal::query()
            ->join('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->where('terminal_details.terminal_id', $terminalId)
            ->select([
                'terminal_details.*',
                'branch.company_id',
            ])
            ->firstOrFail();

        $this->terminalId = (int) $terminal->terminal_id;
        $this->formCompanyId = (string) $terminal->company_id;
        $this->formBranchId = (string) $terminal->branch_id;
        $this->terminalName = (string) $terminal->terminal_name;
        $this->macAddress = (string) $terminal->mac_address;
        $this->serialNo = (string) $terminal->serial_no;
        $this->modelNo = (string) $terminal->model_no;
    }

    public function resetTerminalForm(): void
    {
        $this->terminalId = null;
        if ((int) session('roleId') === 1) {
            $this->formCompanyId = '';
            $this->formBranchId = '';
        } elseif ((int) session('roleId') === 2) {
            $this->formCompanyId = (string) session('company_id');
            $this->formBranchId = '';
        }
        $this->terminalName = '';
        $this->macAddress = '';
        $this->serialNo = '';
        $this->modelNo = '';
    }

    public function inactiveTerminal(int $terminalId): void
    {
        $terminal = $this->terminalDetailsQuery()->where('terminal_details.terminal_id', $terminalId)->first();
        Terminal::query()->where('terminal_id', $terminalId)->update(['status_id' => 2]);
        $this->logTerminalActivity($terminalId, 'terminal_inactivated', 'Terminal marked inactive from Livewire terminal manager.', $terminal);
        session()->flash('terminal_message', 'Terminal inactivated successfully.');
    }

    public function reactiveTerminal(int $terminalId): void
    {
        $terminal = $this->terminalDetailsQuery()->where('terminal_details.terminal_id', $terminalId)->first();
        Terminal::query()->where('terminal_id', $terminalId)->update(['status_id' => 1]);
        $this->logTerminalActivity($terminalId, 'terminal_reactivated', 'Terminal reactivated from Livewire terminal manager.', $terminal);
        session()->flash('terminal_message', 'Terminal reactivated successfully.');
    }

    public function lockTerminal(int $terminalId): void
    {
        $terminal = $this->terminalDetailsQuery()->where('terminal_details.terminal_id', $terminalId)->first();
        $terminalLockService = app(TerminalLockService::class);
        $result = $terminalLockService->lockTerminalById($terminalId);
        $this->logTerminalLockActivity($terminalId, 'terminal_manual_lock', $result, $terminal);
        $this->revealedPasswords = [];
        $this->dispatch('show-lock-result',
            action: 'lock',
            success: $result['success'] ?? false,
            message: $result['message'] ?? 'Unknown response.',
            password: $result['lock_password'] ?? null,
        );
    }

    public function unlockTerminal(int $terminalId): void
    {
        $terminal = $this->terminalDetailsQuery()->where('terminal_details.terminal_id', $terminalId)->first();
        $terminalLockService = app(TerminalLockService::class);
        $result = $terminalLockService->unlockTerminalById($terminalId);
        $this->logTerminalLockActivity($terminalId, 'terminal_manual_unlock', $result, $terminal);
        $this->revealedPasswords = [];
        $this->dispatch('show-lock-result',
            action: 'unlock',
            success: $result['success'] ?? false,
            message: $result['message'] ?? 'Unknown response.',
            password: null,
        );
    }

    public function checkDeviceStatus(int $terminalId): void
    {
        $terminalLockService = app(TerminalLockService::class);
        $result = $terminalLockService->checkTerminalStatusById($terminalId);

        $this->setDeviceStatusFromResult($terminalId, $result);
    }

    public function checkVisibleDeviceStatuses(): void
    {
        $terminalLockService = app(TerminalLockService::class);
        $terminalIds = $this->filteredTerminalsQuery()
            ->forPage($this->getPage(), 15)
            ->pluck('terminal_details.terminal_id');

        foreach ($terminalIds as $terminalId) {
            $result = $terminalLockService->checkTerminalStatusById((int) $terminalId);
            $this->setDeviceStatusFromResult((int) $terminalId, $result);
        }
    }

    private function setDeviceStatusFromResult(int $terminalId, array $result): void
    {
        $list = $result['data']['data']['data']['list'] ?? [];
        $device = $list[0] ?? null;

        $this->deviceStatuses[$terminalId] = [
            'success' => (bool) ($result['success'] ?? false),
            'label' => $device ? ((int) ($device['status'] ?? 0) === 1 ? 'Online' : 'Offline') : 'Unknown',
            'status' => $result['status'] ?? 500,
            'message' => $result['message'] ?? null,
        ];
    }

    public function revealLockPassword(int $terminalId): void
    {
        $terminal = $this->terminalDetailsQuery()
            ->where('terminal_details.terminal_id', $terminalId)
            ->first();

        if (!$terminal || empty($terminal->lock_password)) {
            session()->flash('terminal_error', 'No lock password is saved for this terminal.');
            return;
        }

        $this->revealedPasswords[$terminalId] = $terminal->lock_password;

        activity('terminal_lock_password')
            ->causedBy(auth()->user())
            ->withCompany($terminal->company_id)
            ->withBranch($terminal->branch_id)
            ->withProperties([
                'terminal_id' => (int) $terminal->terminal_id,
                'terminal_name' => $terminal->terminal_name,
                'serial_no' => $terminal->serial_no,
                'is_locked' => (int) ($terminal->is_locked ?? 0),
                'revealed_by_user_id' => auth()->id(),
                'revealed_by_username' => auth()->user()->username ?? null,
            ])
            ->event('password_revealed')
            ->log('Terminal lock password was revealed for ' . $terminal->terminal_name);
    }

    private function terminalDetailsQuery()
    {
        return Terminal::query()
            ->leftJoin('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->leftJoin('company', 'company.company_id', '=', 'branch.company_id')
            ->leftJoin('accessibility_mode', 'accessibility_mode.status_id', '=', 'terminal_details.status_id')
            ->select([
                'terminal_details.terminal_id',
                'terminal_details.branch_id',
                'terminal_details.terminal_name',
                'terminal_details.mac_address',
                'terminal_details.serial_no',
                'terminal_details.model_no',
                'terminal_details.status_id',
                'terminal_details.is_locked',
                'terminal_details.lock_password',
                'branch.branch_name',
                'branch.company_id',
                'company.name as company_name',
                'accessibility_mode.status_name',
            ]);
    }

    private function logTerminalActivity(int $terminalId, string $event, string $message, $terminal = null): void
    {
        $terminal ??= $this->terminalDetailsQuery()
            ->where('terminal_details.terminal_id', $terminalId)
            ->first();

        $logger = activity('terminal')
            ->causedBy(auth()->user())
            ->withProperties([
                'terminal_id' => $terminalId,
                'terminal_name' => $terminal->terminal_name ?? null,
                'serial_no' => $terminal->serial_no ?? null,
                'user_id' => auth()->id(),
                'username' => auth()->user()->username ?? null,
            ]);

        if ($terminal?->company_id) {
            $logger->withCompany($terminal->company_id);
        }

        if ($terminal?->branch_id) {
            $logger->withBranch($terminal->branch_id);
        }

        $logger->event($event)->log($message);
    }

    private function logTerminalLockActivity(int $terminalId, string $event, array $result, $terminal = null): void
    {
        $terminal ??= $this->terminalDetailsQuery()
            ->where('terminal_details.terminal_id', $terminalId)
            ->first();

        $logger = activity('terminal_lock')
            ->causedBy(auth()->user())
            ->withProperties([
                'terminal_id' => $terminalId,
                'terminal_name' => $terminal->terminal_name ?? null,
                'serial_no' => $terminal->serial_no ?? null,
                'is_locked' => $terminal ? (int) ($terminal->is_locked ?? 0) : null,
                'status' => $result['status'] ?? null,
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? null,
                'lock_password' => $result['lock_password'] ?? null,
                'user_id' => auth()->id(),
                'username' => auth()->user()->username ?? null,
            ]);

        if ($terminal?->company_id) {
            $logger->withCompany($terminal->company_id);
        }

        if ($terminal?->branch_id) {
            $logger->withBranch($terminal->branch_id);
        }

        $logger->event($event)->log($result['message'] ?? $event);
    }

    private function filteredTerminalsQuery()
    {
        return $this->terminalDetailsQuery()
            ->when($this->filterCompanyId !== '', function ($query) {
                $query->where('branch.company_id', $this->filterCompanyId);
            })
            ->when($this->filterBranchId !== '', function ($query) {
                $query->where('terminal_details.branch_id', $this->filterBranchId);
            })
            ->when($this->statusId !== '', function ($query) {
                $query->where('terminal_details.status_id', $this->statusId);
            })
            ->when($this->lockStatus !== '', function ($query) {
                $query->where('terminal_details.is_locked', $this->lockStatus);
            })
            ->when($this->search !== '', function ($query) {
                $search = '%' . trim($this->search) . '%';
                $query->where(function ($query) use ($search) {
                    $query->where('terminal_details.terminal_name', 'like', $search)
                        ->orWhere('terminal_details.mac_address', 'like', $search)
                        ->orWhere('terminal_details.serial_no', 'like', $search)
                        ->orWhere('terminal_details.model_no', 'like', $search);
                });
            })
            ->when(!in_array((int) session('roleId'), [1, 2], true), function ($query) {
                $query->where('terminal_details.branch_id', session('branch'));
            })
            ->orderByDesc('terminal_details.terminal_id');
    }

    #[Title('Terminal Manager')]
    #[Layout('layouts.master-tailwind')]
    public function render()
    {
        $companies = Company::query()
            ->select('company_id', 'name')
            ->when((int) session('roleId') === 2, function ($query) {
                $query->where('company_id', session('company_id'));
            })
            ->orderBy('name')
            ->get();

        $filterBranches = Branch::query()
            ->select('branch_id', 'branch_name', 'company_id')
            ->when($this->filterCompanyId !== '', function ($query) {
                $query->where('company_id', $this->filterCompanyId);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->when(!in_array((int) session('roleId'), [1, 2], true), function ($query) {
                $query->where('branch_id', session('branch'));
            })
            ->orderBy('branch_name')
            ->get();

        $formBranches = Branch::query()
            ->select('branch_id', 'branch_name', 'company_id')
            ->when($this->formCompanyId !== '', function ($query) {
                $query->where('company_id', $this->formCompanyId);
            }, function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->when(!in_array((int) session('roleId'), [1, 2], true), function ($query) {
                $query->where('branch_id', session('branch'));
            })
            ->orderBy('branch_name')
            ->get();

        $terminals = $this->filteredTerminalsQuery()->paginate(15);
        $terminalRows = $terminals->getCollection();

        if ($this->deviceStatusFilter !== '' && count($this->deviceStatuses) > 0) {
            $terminalRows = $terminalRows
                ->filter(function ($terminal) {
                    $status = $this->deviceStatuses[$terminal->terminal_id]['label'] ?? null;
                    return $status === $this->deviceStatusFilter;
                })
                ->values();
        }

        return view('livewire.terminals.terminal-manager', [
            'companies' => $companies,
            'filterBranches' => $filterBranches,
            'formBranches' => $formBranches,
            'terminals' => $terminals,
            'terminalRows' => $terminalRows,
        ]);
    }

    public function encrypted(int $id): string
    {
        return Crypt::encrypt($id);
    }
}
