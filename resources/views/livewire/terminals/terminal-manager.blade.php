<div class="terminal-manager-page">
    <style>
        .terminal-manager-page .select2-container {
            width: 100% !important;
        }
        .terminal-manager-page .select2-container .select2-selection--single {
            height: 35px !important;
            border: 1px solid #ccc;
            border-radius: 2px;
        }
        .terminal-manager-page .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 33px !important;
            padding-left: 12px;
            color: #555;
        }
        .terminal-manager-page .select2-container .select2-selection--single .select2-selection__arrow {
            height: 33px !important;
        }
        .terminal-manager-page .terminal-badge {
            display: inline-flex;
            align-items: center;
            min-width: 74px;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        .terminal-manager-page .terminal-badge-active,
        .terminal-manager-page .terminal-badge-online {
            background: #e6f6ec;
            color: #157347;
        }
        .terminal-manager-page .terminal-badge-inactive,
        .terminal-manager-page .terminal-badge-offline {
            background: #fdeaea;
            color: #b02a37;
        }
        .terminal-manager-page .terminal-badge-unknown {
            background: #fff4db;
            color: #9a6700;
        }
        .terminal-manager-page .terminal-badge-muted {
            background: #eef1f4;
            color: #5c6773;
        }
        .terminal-manager-page .terminal-action-menu .dropdown-menu {
            min-width: 190px;
            padding: 6px 0;
        }
        .terminal-manager-page .terminal-action-menu .dropdown-item,
        .terminal-manager-page .terminal-action-menu button.dropdown-item {
            width: 100%;
            border: 0;
            background: transparent;
            text-align: left;
            padding: 7px 14px;
            color: #333;
            cursor: pointer;
        }
        .terminal-manager-page .terminal-action-menu .dropdown-item:hover,
        .terminal-manager-page .terminal-action-menu button.dropdown-item:hover {
            background: #f5f5f5;
        }
        .terminal-manager-page .terminal-table-shell {
            position: relative;
            min-height: 180px;
        }
        .terminal-manager-page .terminal-loader {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(1px);
        }
        .terminal-manager-page .terminal-loader-box {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1px solid #e3e6ea;
            border-radius: 6px;
            background: #fff;
            color: #333;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            font-weight: 600;
        }
        .terminal-manager-page .terminal-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #cfd7df;
            border-top-color: #0d6efd;
            border-radius: 50%;
            animation: terminal-spin 0.75s linear infinite;
        }
        @keyframes terminal-spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Terminal Manager</h5>
            </div>
            <div class="card-block">
                @if (session()->has('terminal_message'))
                    <div class="alert alert-success">{{ session('terminal_message') }}</div>
                @endif
                @if (session()->has('terminal_error'))
                    <div class="alert alert-danger">{{ session('terminal_error') }}</div>
                @endif

                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Company:</label>
                            <select id="terminal-filter-company" class="form-control terminal-select2" data-property="filterCompanyId">
                                <option value="">All Companies</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->company_id }}" @selected((string) $filterCompanyId === (string) $company->company_id)>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Branch:</label>
                            <select id="terminal-filter-branch" class="form-control terminal-select2" data-property="filterBranchId" {{ $filterCompanyId === '' ? 'disabled' : '' }}>
                                <option value="">{{ $filterCompanyId === '' ? 'Select company first' : 'All Branches' }}</option>
                                @foreach ($filterBranches as $branch)
                                    <option value="{{ $branch->branch_id }}" @selected((string) $filterBranchId === (string) $branch->branch_id)>{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Status:</label>
                            <select class="form-control" wire:model.live="statusId">
                                <option value="">All</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Lock:</label>
                            <select class="form-control" wire:model.live="lockStatus">
                                <option value="">All</option>
                                <option value="1">Locked</option>
                                <option value="0">Unlocked</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Search:</label>
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Terminal, MAC, serial or model">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">{{ $terminalId ? 'Update Terminal' : 'Create New Terminal' }}</h5>
            </div>
            <div class="card-block">
                <form wire:submit.prevent="saveTerminal">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Company:</label>
                                <select id="terminal-form-company" class="form-control terminal-select2" data-property="formCompanyId">
                                    <option value="">Select Company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}" @selected((string) $formCompanyId === (string) $company->company_id)>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('formCompanyId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Branch:</label>
                                <select id="terminal-form-branch" class="form-control terminal-select2" data-property="formBranchId" {{ $formCompanyId === '' ? 'disabled' : '' }}>
                                    <option value="">Select Branch</option>
                                    @foreach ($formBranches as $branch)
                                        <option value="{{ $branch->branch_id }}" @selected((string) $formBranchId === (string) $branch->branch_id)>{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('formBranchId') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Terminal Name:</label>
                                <input type="text" class="form-control" wire:model.defer="terminalName">
                                @error('terminalName') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">MAC Address:</label>
                                <input type="text" class="form-control" wire:model.defer="macAddress">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Device Serial Number:</label>
                                <input type="text" class="form-control" wire:model.defer="serialNo">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Model No:</label>
                                <input type="text" class="form-control" wire:model.defer="modelNo">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-md btn-success waves-effect waves-light">
                        <i class="icofont icofont-save"></i>
                        {{ $terminalId ? 'Update Terminal' : 'Add Terminal' }}
                    </button>
                    @if ($terminalId)
                        <button type="button" class="btn btn-md btn-default waves-effect m-l-10" wire:click="resetTerminalForm">
                            Cancel Edit
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Terminal Detail</h5>
            </div>
            <div class="card-block">
                <div class="table-responsive terminal-table-shell">
                    <div class="terminal-loader" wire:loading.flex wire:target="filterCompanyId,filterBranchId,statusId,lockStatus,search,lockTerminal,unlockTerminal,checkDeviceStatus,revealLockPassword,inactiveTerminal,reactiveTerminal,editTerminal,saveTerminal">
                        <div class="terminal-loader-box">
                            <span class="terminal-spinner"></span>
                            <span>Loading terminals...</span>
                        </div>
                    </div>

                    <table class="table table-striped nowrap" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Terminal id</th>
                                <th>Company</th>
                                <th>Branch</th>
                                <th>Terminal Name</th>
                                <th>MAC Address</th>
                                <th>Serial Number</th>
                                <th>Model No</th>
                                <th>Status</th>
                                <th>Device Status</th>
                                <th>Lock Password</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($terminals as $terminal)
                                <tr wire:key="terminal-row-{{ $terminal->terminal_id }}">
                                    <td>{{ $terminal->terminal_id }}</td>
                                    <td>{{ $terminal->company_name }}</td>
                                    <td>{{ $terminal->branch_name }}</td>
                                    <td>{{ $terminal->terminal_name }}</td>
                                    <td>{{ $terminal->mac_address }}</td>
                                    <td>{{ $terminal->serial_no }}</td>
                                    <td>{{ $terminal->model_no }}</td>
                                    <td>
                                        @if ((int) $terminal->status_id === 1)
                                            <span class="terminal-badge terminal-badge-active">Active</span>
                                        @else
                                            <span class="terminal-badge terminal-badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php($deviceStatus = $deviceStatuses[$terminal->terminal_id] ?? null)
                                        @if ($deviceStatus)
                                            <span class="terminal-badge {{ $deviceStatus['label'] === 'Online' ? 'terminal-badge-online' : ($deviceStatus['label'] === 'Offline' ? 'terminal-badge-offline' : 'terminal-badge-unknown') }}">
                                                {{ $deviceStatus['label'] }}
                                            </span>
                                        @else
                                            <span class="terminal-badge terminal-badge-muted">Not Checked</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($terminal->lock_password))
                                            <span class="badge {{ isset($revealedPasswords[$terminal->terminal_id]) ? 'badge-warning' : 'badge-inverse' }}">
                                                {{ $revealedPasswords[$terminal->terminal_id] ?? '********' }}
                                            </span>
                                            <button type="button" class="btn btn-link btn-sm p-0 m-l-5" wire:click="revealLockPassword({{ $terminal->terminal_id }})" title="View Lock Password">
                                                <i class="icofont icofont-eye text-info f-18"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown terminal-action-menu">
                                            <button class="btn btn-sm btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <button type="button" class="dropdown-item" wire:click="editTerminal({{ $terminal->terminal_id }})">
                                                    <i class="icofont icofont-ui-edit text-primary"></i> Edit
                                                </button>

                                                @if ((int) $terminal->status_id === 1)
                                                    <button type="button" class="dropdown-item" wire:click="inactiveTerminal({{ $terminal->terminal_id }})" onclick="return confirm('Inactive this terminal?')">
                                                        <i class="icofont icofont-ui-delete text-danger"></i> Inactive
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item" wire:click="reactiveTerminal({{ $terminal->terminal_id }})">
                                                        <i class="icofont icofont-check-circled text-primary"></i> Reactive
                                                    </button>
                                                @endif

                                                <a href="{{ url('/permission') }}/{{ $this->encrypted($terminal->terminal_id) }}" class="dropdown-item">
                                                    <i class="icofont icofont-layout text-info"></i> Permission
                                                </a>
                                                <a href="{{ url('/printing-details') }}/{{ $this->encrypted($terminal->terminal_id) }}" class="dropdown-item">
                                                    <i class="icofont icofont-print text-success"></i> Print Details
                                                </a>
                                                <a href="{{ url('/bind-terminals') }}/{{ $this->encrypted($terminal->terminal_id) }}/{{ $this->encrypted($terminal->branch_id) }}" class="dropdown-item">
                                                    <i class="icofont icofont-at text-success"></i> Bind Terminal
                                                </a>

                                                @if ((int) ($terminal->is_locked ?? 0) === 0)
                                                    <button type="button" class="dropdown-item" wire:click="lockTerminal({{ $terminal->terminal_id }})">
                                                        <i class="icofont icofont-lock text-warning"></i> Lock Device
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item" wire:click="unlockTerminal({{ $terminal->terminal_id }})">
                                                        <i class="icofont icofont-unlocked text-success"></i> Unlock Device
                                                    </button>
                                                @endif

                                                <button type="button" class="dropdown-item" wire:click="checkDeviceStatus({{ $terminal->terminal_id }})">
                                                    <i class="icofont icofont-verification-check text-info"></i> Device Status
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center text-muted">No terminals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="m-t-15">
                    {{ $terminals->links() }}
                </div>
            </div>
        </div>
    </section>

    @script
    <script>
            const initTerminalSelect2 = function () {
                $('.terminal-manager-page .terminal-select2').each(function () {
                    const $el = $(this);
                    const property = $el.data('property');
                    const currentValue = $wire.get(property) || '';

                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.off('change.terminal-manager');
                        $el.select2('destroy');
                    }

                    $el.val(currentValue);
                    $el.select2({
                        width: '100%',
                        dropdownAutoWidth: false
                    });

                    $el.on('change.terminal-manager', function () {
                        $wire.set(property, $(this).val() || '');
                    });
                });
            };

            initTerminalSelect2();

            Livewire.hook('morph.updating', function () {
                $('.terminal-manager-page .terminal-select2.select2-hidden-accessible').each(function () {
                    $(this).off('change.terminal-manager');
                    $(this).select2('destroy');
                });
            });

            Livewire.hook('morph.updated', function () {
                window.requestAnimationFrame(initTerminalSelect2);
            });
    </script>
    @endscript
</div>
