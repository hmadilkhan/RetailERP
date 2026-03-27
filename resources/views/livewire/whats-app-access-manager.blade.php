<div class="wa-access-page">
    <style>
        .wa-access-page {
            --wa-ink: #16324f;
            --wa-subtle: #6b7b8c;
            --wa-line: #d9e3ec;
            --wa-paper: #ffffff;
            --wa-brand: #1f7a5a;
            --wa-brand-deep: #14533d;
            --wa-accent: #d7a84d;
            --wa-soft-shadow: 0 18px 45px rgba(22, 50, 79, 0.08);
            color: var(--wa-ink);
        }

        .wa-shell {
            max-width: 1480px;
            margin: 0 auto;
        }

        .wa-hero {
            background:
                radial-gradient(circle at top right, rgba(215, 168, 77, 0.18), transparent 28%),
                linear-gradient(135deg, #f8fbf9 0%, #eef6f1 48%, #fdf9f0 100%);
            border: 1px solid rgba(31, 122, 90, 0.12);
            border-radius: 24px;
            box-shadow: var(--wa-soft-shadow);
            overflow: hidden;
            position: relative;
        }

        .wa-hero::after {
            content: "";
            position: absolute;
            inset: auto -60px -60px auto;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(31, 122, 90, 0.12), transparent 68%);
        }

        .wa-chip {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: 999px;
            padding: .4rem .85rem;
            font-size: .78rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            background: rgba(31, 122, 90, 0.1);
            color: var(--wa-brand-deep);
            font-weight: 700;
        }

        .wa-stat {
            background: var(--wa-paper);
            border-radius: 18px;
            border: 1px solid rgba(22, 50, 79, 0.08);
            padding: 1rem 1.1rem;
            box-shadow: 0 10px 25px rgba(22, 50, 79, 0.05);
            height: 100%;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .wa-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(22, 50, 79, 0.08);
        }

        .wa-stat-label {
            color: var(--wa-subtle);
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: .35rem;
        }

        .wa-stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1;
        }

        .wa-panel {
            background: var(--wa-paper);
            border: 1px solid rgba(22, 50, 79, 0.08);
            border-radius: 22px;
            box-shadow: var(--wa-soft-shadow);
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .wa-panel:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 52px rgba(22, 50, 79, 0.1);
        }

        .wa-panel-header {
            padding: 1.2rem 1.35rem 0;
        }

        .wa-panel-body {
            padding: 1.35rem;
        }

        .wa-form-panel {
            position: relative;
        }

        .wa-form-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(31, 122, 90, 0.02), transparent 22%);
            pointer-events: none;
        }

        .wa-panel-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .wa-panel-subtitle {
            color: var(--wa-subtle);
            margin-bottom: 0;
        }

        .wa-form-label {
            font-size: .84rem;
            font-weight: 700;
            color: var(--wa-ink);
            margin-bottom: .45rem;
        }

        .wa-control {
            border-radius: 14px;
            border: 1px solid var(--wa-line);
            min-height: 48px;
            box-shadow: none;
        }

        .wa-control:focus {
            border-color: rgba(31, 122, 90, 0.45);
            box-shadow: 0 0 0 0.2rem rgba(31, 122, 90, 0.12);
        }

        .wa-access-page .select2-container {
            width: 100% !important;
        }

        .wa-access-page .select2-container .select2-selection--single {
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid var(--wa-line);
            background: #fff;
            display: flex;
            align-items: center;
            padding: 0 .95rem;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .wa-access-page .select2-container .select2-selection--single .select2-selection__rendered {
            color: var(--wa-ink);
            line-height: 1.2;
            padding-left: 0;
            padding-right: 1.75rem;
        }

        .wa-access-page .select2-container .select2-selection--single .select2-selection__placeholder {
            color: #8b98a8;
        }

        .wa-access-page .select2-container .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 10px;
        }

        .wa-access-page .select2-container .select2-selection--single .select2-selection__arrow b {
            border-color: #6b7b8c transparent transparent transparent;
            border-width: 6px 5px 0 5px;
            margin-left: -5px;
            margin-top: -2px;
        }

        .wa-access-page .select2-container--open .select2-selection--single,
        .wa-access-page .select2-container--focus .select2-selection--single {
            border-color: rgba(31, 122, 90, 0.45);
            box-shadow: 0 0 0 0.2rem rgba(31, 122, 90, 0.12);
        }

        .wa-access-page .select2-container--disabled .select2-selection--single {
            background: #f4f7fa;
            color: #9aa8b5;
            cursor: not-allowed;
        }

        .wa-access-page .select2-dropdown {
            border: 1px solid rgba(22, 50, 79, 0.1);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(22, 50, 79, 0.12);
        }

        .wa-access-page .select2-search--dropdown {
            padding: .7rem;
            background: #f8fbfd;
        }

        .wa-access-page .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--wa-line);
            border-radius: 10px;
            min-height: 40px;
            padding: .45rem .75rem;
        }

        .wa-access-page .select2-results__option {
            padding: .7rem .9rem;
            color: var(--wa-ink);
        }

        .wa-access-page .select2-results__option--highlighted[aria-selected] {
            background: linear-gradient(135deg, var(--wa-brand) 0%, var(--wa-brand-deep) 100%);
            color: #fff;
        }

        .wa-access-page .select2-results__option[aria-selected=true] {
            background: rgba(31, 122, 90, 0.1);
            color: var(--wa-brand-deep);
            font-weight: 700;
        }

        .wa-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }

        .wa-btn {
            border-radius: 14px;
            font-weight: 700;
            padding: .75rem 1rem;
            border: 0;
            transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
        }

        .wa-btn:hover {
            transform: translateY(-1px);
        }

        .wa-btn-primary {
            background: linear-gradient(135deg, var(--wa-brand) 0%, var(--wa-brand-deep) 100%);
            color: #fff;
            box-shadow: 0 12px 24px rgba(20, 83, 61, 0.18);
        }

        .wa-btn-primary:hover {
            color: #fff;
            box-shadow: 0 16px 28px rgba(20, 83, 61, 0.24);
        }

        .wa-btn-soft {
            background: #eef4f8;
            color: var(--wa-ink);
            border: 1px solid rgba(22, 50, 79, 0.08);
            box-shadow: 0 8px 18px rgba(22, 50, 79, 0.05);
        }

        .wa-btn-soft:hover {
            background: #e6eef4;
        }

        .wa-table-wrap {
            border-radius: 18px;
            border: 1px solid rgba(22, 50, 79, 0.08);
            overflow: hidden;
            background: #fff;
        }

        .wa-table {
            margin-bottom: 0;
            min-width: 640px;
        }

        .wa-table thead th {
            background: #f7fafc;
            color: var(--wa-subtle);
            border: 0;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .9rem 1rem;
            white-space: nowrap;
        }

        .wa-table tbody td {
            vertical-align: middle;
            padding: .95rem 1rem;
            border-top: 1px solid #edf2f7;
        }

        .wa-table tbody tr {
            transition: background-color .16s ease;
        }

        .wa-table tbody tr:hover {
            background: linear-gradient(135deg, rgba(31, 122, 90, 0.035) 0%, rgba(215, 168, 77, 0.04) 100%);
        }

        .wa-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            padding: .35rem .7rem;
            letter-spacing: .03em;
            min-width: 86px;
        }

        .wa-pill-success {
            background: linear-gradient(135deg, rgba(31, 122, 90, 0.14) 0%, rgba(20, 83, 61, 0.18) 100%);
            color: var(--wa-brand-deep);
            border: 1px solid rgba(31, 122, 90, 0.12);
        }

        .wa-pill-muted {
            background: linear-gradient(135deg, rgba(107, 123, 140, 0.12) 0%, rgba(107, 123, 140, 0.18) 100%);
            color: #546272;
            border: 1px solid rgba(107, 123, 140, 0.1);
        }

        .wa-search {
            background: linear-gradient(135deg, #ffffff 0%, #f9fbfd 100%);
        }

        .wa-empty {
            padding: 2rem 1rem;
            text-align: center;
            color: var(--wa-subtle);
        }

        .wa-mini-text {
            font-size: .85rem;
            color: var(--wa-subtle);
        }

        .wa-link-btn {
            border: 0;
            background: transparent;
            color: var(--wa-brand-deep);
            font-weight: 700;
            padding: .35rem .6rem;
            border-radius: 10px;
            transition: background-color .16s ease, color .16s ease;
        }

        .wa-link-btn:hover {
            background: rgba(31, 122, 90, 0.08);
            color: var(--wa-brand-deep);
            text-decoration: none;
        }

        .wa-alert {
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(31, 122, 90, 0.1) 0%, rgba(215, 168, 77, 0.12) 100%);
            color: var(--wa-ink);
            border: 1px solid rgba(31, 122, 90, 0.12);
            padding: 1rem 1.15rem;
        }

        .wa-section-gap {
            margin-top: .35rem;
        }

        .wa-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .wa-toolbar-copy {
            max-width: 720px;
        }

        .wa-toolbar-copy h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .wa-toolbar-copy p {
            margin-bottom: 0;
            color: var(--wa-subtle);
        }

        .wa-grid-gap {
            row-gap: .35rem;
        }

        .wa-sticky-desktop {
            position: sticky;
            top: 88px;
        }

        .wa-scroll-shadow {
            position: relative;
        }

        .wa-scroll-shadow::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            width: 28px;
            height: 100%;
            background: linear-gradient(90deg, rgba(255,255,255,0), rgba(255,255,255,0.92));
            pointer-events: none;
        }

        .wa-table-title {
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .wa-table-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--wa-brand) 0%, var(--wa-accent) 100%);
        }

        @media (max-width: 767.98px) {
            .wa-shell {
                max-width: 100%;
            }

            .wa-hero {
                border-radius: 20px;
            }

            .wa-hero h2 {
                font-size: 1.45rem;
                line-height: 1.25;
            }

            .wa-stat {
                padding: .9rem .95rem;
            }

            .wa-stat-value {
                font-size: 1.45rem;
            }

            .wa-panel-body,
            .wa-panel-header {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .wa-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .wa-btn {
                width: 100%;
            }

            .wa-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .wa-table {
                min-width: 560px;
            }

            .wa-scroll-shadow::after {
                width: 20px;
            }
        }

        @media (max-width: 1199.98px) {
            .wa-sticky-desktop {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .wa-access-page {
                --wa-soft-shadow: 0 12px 28px rgba(22, 50, 79, 0.08);
            }

            .wa-panel {
                border-radius: 18px;
            }

            .wa-table {
                min-width: 520px;
            }

            .wa-chip {
                font-size: .72rem;
            }
        }
    </style>

    <div class="wa-shell">
    <div class="wa-hero p-4 p-lg-5 mb-4">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <span class="wa-chip">
                    <i class="icofont icofont-whatsapp"></i>
                    Admin Only
                </span>
                <h2 class="mt-3 mb-2 font-weight-bold">WhatsApp Report Access Manager</h2>
                <p class="mb-0 wa-mini-text">
                    Manage registered WhatsApp users and define whether each one sees full company branches or jumps directly into branch terminals.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="row wa-grid-gap">
                    <div class="col-6 mb-3">
                        <div class="wa-stat">
                            <div class="wa-stat-label">Users</div>
                            <div class="wa-stat-value">{{ $stats['users'] }}</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="wa-stat">
                            <div class="wa-stat-label">Active Users</div>
                            <div class="wa-stat-value">{{ $stats['active_users'] }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="wa-stat">
                            <div class="wa-stat-label">Access Rules</div>
                            <div class="wa-stat-value">{{ $stats['access_rules'] }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="wa-stat">
                            <div class="wa-stat-label">Branch Scope</div>
                            <div class="wa-stat-value">{{ $stats['branch_scope'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('whatsapp_access_message'))
        <div class="alert wa-alert shadow-sm mb-4">
            {{ session('whatsapp_access_message') }}
        </div>
    @endif

    <div class="wa-toolbar">
        <div class="wa-toolbar-copy">
            <h4>Everything in one place</h4>
            <p>Use the top section to register a WhatsApp number and assign company or branch access. The lists below stay searchable and responsive for quick admin work.</p>
        </div>
        <div class="wa-chip">
            <i class="icofont icofont-shield"></i>
            Admin Managed
        </div>
    </div>

    <div class="row">
        <div class="col-xl-5 mb-4">
            <div class="wa-sticky-desktop">
            <div class="wa-panel wa-form-panel h-100">
                <div class="wa-panel-header">
                    <h3 class="wa-panel-title">WhatsApp User</h3>
                    <p class="wa-panel-subtitle">Create or update the mobile numbers that can request reports.</p>
                </div>
                <div class="wa-panel-body">
                    <form wire:submit="saveUser">
                        <div class="form-group">
                            <label class="wa-form-label">Display Name</label>
                            <input type="text" wire:model.defer="user_name" class="form-control wa-control" placeholder="Ali Raza">
                            @error('user_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label class="wa-form-label">Mobile Number</label>
                            <input type="text" wire:model.defer="mobile_number" class="form-control wa-control" placeholder="923001234567">
                            @error('mobile_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label class="wa-form-label">Status</label>
                            <select wire:model.defer="user_status" class="form-control wa-control select2-wa" data-placeholder="Select status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('user_status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="wa-actions mt-4">
                            <button type="submit" class="btn wa-btn wa-btn-primary">
                                {{ $editingUserId ? 'Update User' : 'Create User' }}
                            </button>
                            <button type="button" wire:click="resetUserForm" class="btn wa-btn wa-btn-soft">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>

        <div class="col-xl-7 mb-4">
            <div class="wa-panel wa-form-panel h-100">
                <div class="wa-panel-header">
                    <h3 class="wa-panel-title">Access Rule</h3>
                    <p class="wa-panel-subtitle">Assign company-wide or branch-only access for each registered WhatsApp user.</p>
                </div>
                <div class="wa-panel-body">
                    <form wire:submit="saveAccess">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="wa-form-label">WhatsApp User</label>
                                <select wire:model.defer="whatsapp_user_id" class="form-control wa-control select2-wa" data-placeholder="Select user">
                                    <option value="">Select user</option>
                                    @foreach ($userOptions as $optionUser)
                                        <option value="{{ $optionUser->id }}">
                                            {{ $optionUser->mobile_number }}{{ $optionUser->name ? ' - ' . $optionUser->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('whatsapp_user_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label class="wa-form-label">Company</label>
                                <select wire:model.live="company_id" class="form-control wa-control select2-wa" data-placeholder="Select company">
                                    <option value="">Select company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="wa-form-label">Access Level</label>
                                <select wire:model.live="access_level" class="form-control wa-control select2-wa" data-placeholder="Select access level">
                                    <option value="company">Company</option>
                                    <option value="branch">Branch</option>
                                </select>
                                @error('access_level') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="wa-form-label">Branch</label>
                                <select wire:model.defer="branch_id" class="form-control wa-control select2-wa" data-placeholder="Select branch" {{ $access_level !== 'branch' ? 'disabled' : '' }}>
                                    <option value="">Select branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label class="wa-form-label">Status</label>
                                <select wire:model.defer="access_status" class="form-control wa-control select2-wa" data-placeholder="Select status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('access_status') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="wa-actions mt-4">
                            <button type="submit" class="btn wa-btn wa-btn-primary">
                                {{ $editingAccessId ? 'Update Access' : 'Create Access' }}
                            </button>
                            <button type="button" wire:click="resetAccessForm" class="btn wa-btn wa-btn-soft">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="wa-panel wa-search mb-4">
        <div class="wa-panel-body">
            <div class="row align-items-end">
                <div class="col-lg-5 form-group mb-lg-0">
                    <label class="wa-form-label">Search</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control wa-control" placeholder="Search by mobile, name, company or branch">
                </div>
                <div class="col-lg-4 form-group mb-lg-0">
                    <label class="wa-form-label">Filter Company</label>
                    <select wire:model.live="companyFilter" class="form-control wa-control select2-wa" data-placeholder="All companies">
                        <option value="">All companies</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->company_id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 form-group mb-0">
                    <label class="wa-form-label">Filter Scope</label>
                    <select wire:model.live="scopeFilter" class="form-control wa-control select2-wa" data-placeholder="All scopes">
                        <option value="">All scopes</option>
                        <option value="company">Company</option>
                        <option value="branch">Branch</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-5 mb-4">
            <div class="wa-panel">
                <div class="wa-panel-header">
                    <div class="wa-table-title">
                        <span class="wa-table-dot"></span>
                        <h3 class="wa-panel-title mb-0">Registered Users</h3>
                    </div>
                    <p class="wa-panel-subtitle">Keep the WhatsApp directory clean and active.</p>
                </div>
                <div class="wa-panel-body">
                    <div class="wa-table-wrap wa-scroll-shadow table-responsive">
                        <table class="table wa-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $user->mobile_number }}</div>
                                            <div class="wa-mini-text">{{ $user->name ?? 'No name added' }}</div>
                                        </td>
                                        <td>
                                            <span class="wa-pill {{ (int) ($user->status ?? 1) === 1 ? 'wa-pill-success' : 'wa-pill-muted' }}">
                                                {{ (int) ($user->status ?? 1) === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <button type="button" wire:click="editUser({{ $user->id }})" class="wa-link-btn mr-2 mb-1">Edit</button>
                                            <button type="button" wire:click="toggleUserStatus({{ $user->id }})" class="wa-link-btn mb-1">
                                                {{ (int) ($user->status ?? 1) === 1 ? 'Disable' : 'Enable' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="wa-empty">No WhatsApp users found yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7 mb-4">
            <div class="wa-panel">
                <div class="wa-panel-header">
                    <div class="wa-table-title">
                        <span class="wa-table-dot"></span>
                        <h3 class="wa-panel-title mb-0">Access Rules</h3>
                    </div>
                    <p class="wa-panel-subtitle">Each rule controls whether the report flow shows branches or jumps directly to terminals.</p>
                </div>
                <div class="wa-panel-body">
                    <div class="wa-table-wrap wa-scroll-shadow table-responsive">
                        <table class="table wa-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Scope</th>
                                    <th>Company / Branch</th>
                                    <th>Status</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($accesses as $access)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold">{{ $access->mobile_number }}</div>
                                            <div class="wa-mini-text">{{ $access->user_name ?: 'No name added' }}</div>
                                        </td>
                                        <td>
                                            <span class="wa-pill {{ $access->access_level === 'branch' ? 'wa-pill-success' : 'wa-pill-muted' }}">
                                                {{ ucfirst($access->access_level) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold">{{ $access->company_name }}</div>
                                            <div class="wa-mini-text">
                                                {{ $access->branch_name ?: 'All branches in company' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="wa-pill {{ (int) ($access->status ?? 1) === 1 ? 'wa-pill-success' : 'wa-pill-muted' }}">
                                                {{ (int) ($access->status ?? 1) === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <button type="button" wire:click="editAccess({{ $access->id }})" class="wa-link-btn mr-2 mb-1">Edit</button>
                                            <button type="button" wire:click="toggleAccessStatus({{ $access->id }})" class="wa-link-btn mb-1">
                                                {{ (int) ($access->status ?? 1) === 1 ? 'Disable' : 'Enable' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="wa-empty">No access rules found yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $accesses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        const getWireModel = ($el) => {
            return $el.attr('wire:model.live')
                || $el.attr('wire:model.defer')
                || $el.attr('wire:model');
        };

        const destroySelect2 = () => {
            $('.select2-wa').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
        };

        const initSelect2 = () => {
            $('.select2-wa').each(function() {
                const $el = $(this);
                const model = getWireModel($el);

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.select2({
                    width: '100%',
                    placeholder: $el.data('placeholder') || 'Select an option',
                    allowClear: true
                });

                $el.off('change.whatsapp-access').on('change.whatsapp-access', function() {
                    if (!model) {
                        return;
                    }

                    $wire.set(model, $(this).val());
                });
            });
        };

        initSelect2();

        Livewire.hook('morph.updating', () => {
            destroySelect2();
        });

        Livewire.hook('morph.updated', () => {
            initSelect2();
        });
    });
</script>
@endscript
