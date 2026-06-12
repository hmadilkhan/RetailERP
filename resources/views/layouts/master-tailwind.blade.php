<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $title ?? 'Admin') | ERP</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        erp: {
                            ink: '#0f172a',
                            panel: '#2f3437',
                            panelSoft: '#3b4145',
                            text: '#334155',
                            mute: '#64748b',
                            line: '#d8e1ec',
                            soft: '#f8fafc',
                            DEFAULT: '#4CAF50',
                            light: '#86efac',
                            dark: '#2E7D32'
                        }
                    },
                    boxShadow: {
                        panel: '0 18px 45px rgba(15, 23, 42, 0.10)',
                        menu: '0 20px 55px rgba(15, 23, 42, 0.20)'
                    }
                }
            }
        }
    </script>

    @php
        $tailwindOrdersAssets = request()->is('orders-view*');

        $tailwindLegacyAssets = request()->is('website/advertisement*')
            || request()->is('website/slider*')
            || request()->is('website/social-link*')
            || request()->is('website/terminal-assign*')
            || request()->is('website/branch-timings*')
            || request()->is('website/theme-setting*')
            || request()->is('website/testimonials*')
            || request()->is('website/customer-reviews*')
            || request()->is('website/booking-slots*')
            || request()->is('create-demand')
            || request()->is('edit-demand*')
            || request()->is('demand-details*')
            || request()->is('received-demandpanel*')
            || request()->is('view-transfer*')
            || request()->is('createdeliverychallan*')
            || request()->is('generate-po*')
            || request()->is('showtransferdetails*')
            || request()->is('createGRN*')
            || request()->is('edit_trf_details*');

        $tailwindPurchaseAssets = request()->is('add-purchase') || request()->is('edit/*');
        $tailwindDashboardAssets = request()->is('dashboard');
    @endphp

    @if ($tailwindLegacyAssets)
        @include('partials.html-libs')
    @endif
    @php
        $tailwindSelect2Assets = request()->is('billing*')
            || request()->is('invoice-setup*')
            || request()->is('rooms')
            || request()->is('view-floors')
            || request()->is('view-kitchen-departments')
            || request()->is('view-accounts')
            || request()->is('view-cheque')
            || request()->is('cash-deposit')
            || request()->is('view-bank-discount')
            || request()->is('get-banks')
            || request()->is('create-bank')
            || request()->is('bankaccounts-details')
            || request()->is('expense-report')
            || request()->is('vendor-report')
            || request()->is('customer-report')
            || request()->is('erpreportdashboard')
            || request()->is('reports/item-sale-report')
            || request()->is('reports/consolidated-item-sale-report')
            || request()->is('vendors')
            || request()->is('BusinessPolicy')
            || request()->is('view-purchases')
            || request()->is('vendor-payment-view')
            || request()->is('delivery/lists')
            || request()->is('service-provider')
            || request()->is('drivers')
            || request()->is('vehicles')
            || request()->is('orders-view*');
    @endphp
    @if (($tailwindSelect2Assets || $tailwindPurchaseAssets) && !$tailwindLegacyAssets)
        <link rel="stylesheet" href="{{ asset('components/select2/dist/css/select2.min.css') }}" />
    @endif
    @if ($tailwindOrdersAssets && !$tailwindLegacyAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/themify-icons/themify-icons.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('components/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/sweetalert/css/sweetalert.css') }}">
    @endif
    @if ($tailwindPurchaseAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('components/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('components/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/sweetalert/css/sweetalert.css') }}">
    @endif
    @if ($tailwindDashboardAssets)
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/icon/icofont/css/icofont.css') }}">
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('js/raphael-min.js') }}"></script>
        <script src="{{ asset('components/morris.js/morris.js') }}"></script>
    @endif
    @yield('css_code')
    @yield('scriptcode_one')
    @stack('styles')
    <style>
        @font-face {
            font-family: 'yaro';
            src: url('{{ asset('assets/fonts/YaroRg-Bold.woff2') }}') format('woff2'),
                 url('{{ asset('assets/fonts/YaroRg-Bold.woff') }}') format('woff');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        [x-cloak] { display: none !important; }
        @if ($tailwindSelect2Assets || $tailwindPurchaseAssets)
            .billing-select2.select2-hidden-accessible + .select2-container,
            .v2-select2.select2-hidden-accessible + .select2-container {
                width: 100% !important;
                min-width: 0;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple {
                min-height: 2.5rem;
                border: 1px solid #d8e1ec;
                border-radius: 0.5rem;
                background: #fff;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
                display: flex;
                align-items: center;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple {
                align-items: flex-start;
                padding: 0.25rem 0.375rem;
            }
            .billing-select2-lg.select2-hidden-accessible + .select2-container .select2-selection--single,
            .v2-select2-lg.select2-hidden-accessible + .select2-container .select2-selection--single {
                min-height: 2.75rem;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection__rendered,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection__rendered {
                color: #334155;
                font-size: 0.875rem;
                line-height: 1.25rem;
                padding-left: 0.75rem;
                padding-right: 2rem;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple .select2-selection__rendered {
                padding: 0;
                display: flex;
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple .select2-selection__choice {
                margin: 0;
                border: 1px solid #d8e1ec;
                border-radius: 0.375rem;
                background: #f8fafc;
                color: #334155;
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0.125rem 0.5rem 0.125rem 1.25rem;
            }
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection--multiple .select2-search__field {
                margin: 0.125rem 0;
                min-height: 1.75rem;
                font-size: 0.875rem;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection__placeholder,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection__placeholder {
                color: #64748b;
            }
            .billing-select2.select2-hidden-accessible + .select2-container .select2-selection__arrow,
            .v2-select2.select2-hidden-accessible + .select2-container .select2-selection__arrow {
                height: 100%;
                right: 0.45rem;
            }
            .billing-select2.select2-hidden-accessible + .select2-container.select2-container--focus .select2-selection--single,
            .billing-select2.select2-hidden-accessible + .select2-container.select2-container--open .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--focus .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--open .select2-selection--single,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--focus .select2-selection--multiple,
            .v2-select2.select2-hidden-accessible + .select2-container.select2-container--open .select2-selection--multiple {
                border-color: #4CAF50;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
            }
            .select2-container--default .billing-select2-dropdown.select2-dropdown,
            .select2-container--default .v2-select2-dropdown.select2-dropdown {
                border-color: #d8e1ec;
                border-radius: 0.5rem;
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
                overflow: hidden;
            }
            .billing-select2-dropdown .select2-search--dropdown,
            .v2-select2-dropdown .select2-search--dropdown {
                padding: 0.5rem;
            }
            .billing-select2-dropdown .select2-search__field,
            .v2-select2-dropdown .select2-search__field {
                border: 1px solid #d8e1ec !important;
                border-radius: 0.5rem;
                min-height: 2.25rem;
                font-size: 0.875rem;
                outline: none;
            }
            .billing-select2-dropdown .select2-results__option,
            .v2-select2-dropdown .select2-results__option {
                font-size: 0.875rem;
                padding: 0.5rem 0.75rem;
            }
            .billing-select2-dropdown .select2-results__option--highlighted[aria-selected],
            .v2-select2-dropdown .select2-results__option--highlighted[aria-selected] {
                background: #4CAF50;
            }
        @endif
        @if ($tailwindLegacyAssets)
            .content-wrapper,
            .container-fluid {
                background: transparent !important;
            }
            .panels-wells,
            .container-fluid {
                padding: 0 !important;
            }
            .card {
                border: 1px solid #d8e1ec !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06) !important;
                overflow: hidden;
            }
            .card-header {
                background: #f8fafc !important;
                border-bottom: 1px solid #d8e1ec !important;
                padding: 1rem 1.25rem !important;
            }
            .card-header-text,
            .card-header h4,
            .card-header h5 {
                color: #0f172a !important;
                font-weight: 800 !important;
                letter-spacing: 0 !important;
                margin: 0 !important;
            }
            .card-block,
            .card-body {
                padding: 1.25rem !important;
            }
            .form-control {
                border-color: #d8e1ec !important;
                border-radius: 0.5rem !important;
                box-shadow: none !important;
            }
            .form-control:focus {
                border-color: #4CAF50 !important;
                box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.12) !important;
            }
            .btn {
                border-radius: 0.5rem !important;
                font-weight: 700 !important;
            }
            .table thead th {
                background: #f8fafc !important;
                color: #64748b !important;
                font-size: 0.75rem !important;
                font-weight: 800 !important;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                border-bottom: 1px solid #d8e1ec !important;
            }
            .table tbody td {
                color: #334155;
                vertical-align: middle !important;
                border-color: #edf2f7 !important;
            }
        @endif
    </style>
    @livewireStyles
</head>

<body class="min-h-screen bg-[#f3f6f9] text-erp-text antialiased">
    @php
        $currentUrl = request()->path();
        $sidebarRoleId = session('roleId');

        if (auth()->check() && app()->bound('impersonate') && app('impersonate')->isImpersonating()) {
            $sidebarRoleId = \Illuminate\Support\Facades\DB::table('user_authorization')
                ->where('user_id', auth()->id())
                ->value('role_id') ?? $sidebarRoleId;
        }

        $sidebarPages = collect();
        if ($sidebarRoleId) {
            $companyPackage = \Illuminate\Support\Facades\DB::table('company')
                ->where('company_id', session('company_id'))
                ->whereNotNull('package_id')
                ->first();

            if ($companyPackage) {
                $sidebarPageIds = \Illuminate\Support\Facades\DB::select(
                    'SELECT page_id from role_settings WHERE role_id = ? and page_id IN (SELECT page_id FROM package_module_permissions where package_id = ?) ORDER BY page_id',
                    [$sidebarRoleId, $companyPackage->package_id]
                );
            } else {
                $sidebarPageIds = \Illuminate\Support\Facades\DB::select(
                    'SELECT page_id from role_settings WHERE role_id = ? ORDER BY page_id',
                    [$sidebarRoleId]
                );
            }

            $sidebarPageIds = collect($sidebarPageIds)->pluck('page_id')->all();

            if (!empty($sidebarPageIds)) {
                $sidebarPages = \Illuminate\Support\Facades\DB::table('pages_details')
                    ->whereIn('id', $sidebarPageIds)
                    ->get();
            }
        }

        $sidebarLabel = function ($page) {
            return __('sidebar.' . \Illuminate\Support\Str::snake(strtolower($page->page_name)));
        };

        $sidebarUrlMatches = function ($pageUrl) use ($currentUrl) {
            $pageUrl = trim((string) $pageUrl, '/');

            if ($pageUrl === $currentUrl) {
                return true;
            }

            $activeAliases = [
                'branches' => ['createbranch', 'branch-edit/*', 'branch-emails/*'],
                'usersDetails' => ['userdetails', 'create-user', 'user-edit/*'],
                'userdetails' => ['usersDetails', 'create-user', 'user-edit/*'],
                'companies' => ['createcompany'],
                'company' => ['company/create', 'company/*/edit', 'company-edit/*'],
                'terminal-manager' => ['permission/*', 'printing-details/*', 'bind-terminals/*/*'],
                'terminals' => ['terminal-manager', 'permission/*', 'printing-details/*', 'bind-terminals/*/*'],
                'roles' => ['roles'],
                'pages' => ['pages'],
                'modules-permissions' => ['modules-permissions'],
                'website' => ['website/*', 'delivery/lists'],
                'website/advertisement/lists' => ['website/advertisement/*'],
                'website/slider/lists' => ['website/slider/*'],
                'website/social-link/lists' => ['website/social-link/*'],
                'delivery/lists' => ['delivery/*'],
                'website/terminal-assign/view' => ['website/terminal-assign/*'],
                'website/branch-timings/view' => ['website/branch-timings/*'],
                'website/theme-setting' => ['website/theme-setting/*'],
                'website/testimonials' => ['website/testimonials/*'],
                'website/customer-reviews/lists' => ['website/customer-reviews/*'],
                'invoice-setup' => ['invoice-setup/*'],
                'billing/summary' => ['billing/summary'],
                'billing/invoices' => ['billing/invoices/*'],
                'billing/delivery-history' => ['billing/delivery-history'],
            ];

            foreach (($activeAliases[$pageUrl] ?? []) as $pattern) {
                if (\Illuminate\Support\Str::is($pattern, $currentUrl)) {
                    return true;
                }
            }

            return false;
        };

        $sidebarIsActive = function ($page) use ($sidebarUrlMatches) {
            return $sidebarUrlMatches($page->page_url);
        };

        $sidebarHasActiveDescendant = function ($page) use (&$sidebarHasActiveDescendant, $sidebarPages, $sidebarUrlMatches) {
            return $sidebarPages->where('parent_id', $page->id)->contains(function ($child) use (&$sidebarHasActiveDescendant, $sidebarUrlMatches) {
                return $sidebarUrlMatches($child->page_url) || $sidebarHasActiveDescendant($child);
            });
        };

        $sidebarTopPages = $sidebarPages->filter(function ($page) {
            return (int) $page->parent_id === 0 || $page->page_mode === 'Label' || $page->page_mode === 'Parent';
        });
    @endphp

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" x-cloak
            class="fixed inset-y-0 left-0 z-40 flex w-72 transform flex-col bg-erp-panel text-white shadow-menu transition-transform duration-300 lg:translate-x-0">
            <div class="border-b border-white/10 px-5 py-6">
                <a href="{{ url('/dashboard') }}"
                    class="group relative block overflow-hidden rounded-lg px-3 py-4 text-center transition hover:bg-white/[0.04]">
                    <span class="pointer-events-none absolute inset-x-8 top-4 h-8 rounded-full bg-[#2faa4f]/18 blur-xl transition group-hover:bg-[#2faa4f]/25"></span>
                    <span class="relative flex items-center justify-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-[#2faa4f] shadow-[0_0_16px_rgba(47,170,79,0.85)]"></span>
                        <span class="h-px w-8 bg-gradient-to-r from-transparent to-[#2faa4f]/70"></span>
                        <span class="text-[42px] font-bold leading-none text-[#2faa4f]"
                            style="font-family: yaro, Arial, sans-serif; letter-spacing: 0.02em;">
                            sabify
                        </span>
                        <span class="h-px w-8 bg-gradient-to-l from-transparent to-[#2faa4f]/70"></span>
                        <span class="h-2 w-2 rounded-full bg-[#2faa4f] shadow-[0_0_16px_rgba(47,170,79,0.85)]"></span>
                    </span>
                    <span class="relative mt-2 block text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">
                        Retail ERP
                    </span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5">
                <ul class="space-y-1">
                    @forelse ($sidebarTopPages as $pages)
                        @if ($pages->page_mode == 'Label')
                            <li class="px-3 pb-1 pt-5 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">
                                {{ $sidebarLabel($pages) }}
                            </li>
                        @elseif ($pages->page_mode == 'Parent')
                            @php
                                $children = $sidebarPages->where('parent_id', $pages->id);
                                $hasChildren = $children->isNotEmpty() && (int) $pages->icofont_arrow !== 0;
                                $isActive = $sidebarIsActive($pages);
                                $hasActiveChild = $sidebarHasActiveDescendant($pages)
                                    || (\Illuminate\Support\Str::snake(strtolower($pages->page_name)) === 'billing_section'
                                        && (\Illuminate\Support\Str::is('billing/*', $currentUrl) || \Illuminate\Support\Str::is('invoice-setup*', $currentUrl)));
                            @endphp

                            @if (!$hasChildren)
                                <li>
                                    <a href="{{ url('/' . $pages->page_url) }}" @click="sidebarOpen = false"
                                        class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $isActive ? 'bg-white text-erp-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                        <i class="{{ $pages->icofont }} w-5 shrink-0 text-center {{ $isActive ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                        <span class="min-w-0 flex-1 truncate">{{ $sidebarLabel($pages) }}</span>
                                    </a>
                                </li>
                            @else
                                <li x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }">
                                    <button type="button" @click="open = !open"
                                        class="group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-semibold transition {{ $hasActiveChild ? 'bg-white text-erp-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                        <i class="{{ $pages->icofont }} w-5 shrink-0 text-center {{ $hasActiveChild ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                        <span class="min-w-0 flex-1 truncate">{{ $sidebarLabel($pages) }}</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 shrink-0 text-slate-500 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <ul x-show="open" x-cloak class="mt-1 space-y-1 border-l border-white/10 pl-3">
                                        @foreach ($children as $childs)
                                            @php
                                                $grandchildren = $sidebarPages->where('parent_id', $childs->id);
                                                $childHasChildren = $grandchildren->isNotEmpty() && (int) $childs->icofont_arrow !== 0;
                                                $childActive = $sidebarIsActive($childs);
                                                $hasActiveGrandchild = $sidebarHasActiveDescendant($childs);
                                            @endphp

                                            @if (!$childHasChildren)
                                                <li>
                                                    <a href="{{ url('/' . $childs->page_url) }}" @click="sidebarOpen = false"
                                                        class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $childActive ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                        <i class="{{ $childs->icofont }} w-5 shrink-0 text-center {{ $childActive ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                        <span class="min-w-0 flex-1 truncate">{{ $sidebarLabel($childs) }}</span>
                                                    </a>
                                                </li>
                                            @else
                                                <li x-data="{ open: {{ $hasActiveGrandchild ? 'true' : 'false' }} }">
                                                    <button type="button" @click="open = !open"
                                                        class="group flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm font-semibold transition {{ $hasActiveGrandchild ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                        <i class="{{ $childs->icofont }} w-5 shrink-0 text-center {{ $hasActiveGrandchild ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                        <span class="min-w-0 flex-1 truncate">{{ $sidebarLabel($childs) }}</span>
                                                        <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 shrink-0 text-slate-500 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>

                                                    <ul x-show="open" x-cloak class="mt-1 space-y-1 border-l border-white/10 pl-3">
                                                        @foreach ($grandchildren as $grandchild)
                                                            @php
                                                                $greatGrandchildren = $sidebarPages->where('parent_id', $grandchild->id);
                                                                $grandchildActive = $sidebarIsActive($grandchild);
                                                                $hasActiveGreatGrandchild = $sidebarHasActiveDescendant($grandchild);
                                                            @endphp
                                                            <li>
                                                                <a href="{{ url('/' . $grandchild->page_url) }}" @click="sidebarOpen = false"
                                                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $grandchildActive || $hasActiveGreatGrandchild ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                                    <i class="{{ $grandchild->icofont }} w-5 shrink-0 text-center {{ $grandchildActive || $hasActiveGreatGrandchild ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                                    <span class="min-w-0 flex-1 truncate">{{ $sidebarLabel($grandchild) }}</span>
                                                                </a>

                                                                @if ($greatGrandchildren->isNotEmpty())
                                                                    <ul class="mt-1 space-y-1 border-l border-white/10 pl-3">
                                                                        @foreach ($greatGrandchildren as $grandgrandchild)
                                                                            @php
                                                                                $greatGrandchildActive = $sidebarIsActive($grandgrandchild);
                                                                            @endphp
                                                                            <li>
                                                                                <a href="{{ url('/' . $grandgrandchild->page_url) }}" @click="sidebarOpen = false"
                                                                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $greatGrandchildActive ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                                                    <i class="{{ $grandgrandchild->icofont }} w-5 shrink-0 text-center {{ $greatGrandchildActive ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                                                    <span class="min-w-0 flex-1 truncate">{{ $sidebarLabel($grandgrandchild) }}</span>
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach

                                        @if (\Illuminate\Support\Str::snake(strtolower($pages->page_name)) === 'billing_section')
                                            <li>
                                                <a href="{{ route('billing.delivery-history') }}" @click="sidebarOpen = false"
                                                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold transition {{ $currentUrl == 'billing/delivery-history' ? 'bg-white text-erp-ink' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                                                    <i class="icofont icofont-history w-5 shrink-0 text-center {{ $currentUrl == 'billing/delivery-history' ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                                    <span class="min-w-0 flex-1 truncate">{{ __('sidebar.delivery_history') }}</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif
                        @endif
                    @empty
                        <li class="px-3 py-3 text-sm font-semibold text-slate-400">No menu items available.</li>
                    @endforelse

                    @if (session('roleId') == 1)
                        <li class="px-3 pb-1 pt-5 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">
                            Admin Tools
                        </li>
                        <li>
                            <a href="{{ route('whatsapp.access.manager') }}" @click="sidebarOpen = false"
                                class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ $currentUrl == 'whatsapp-access-manager' ? 'bg-white text-erp-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                                <i class="icofont icofont-ui-social-link w-5 shrink-0 text-center {{ $currentUrl == 'whatsapp-access-manager' ? 'text-erp' : 'text-slate-500 group-hover:text-erp-light' }}"></i>
                                <span class="min-w-0 flex-1 truncate">WhatsApp Access</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="rounded-lg bg-white/10 p-3">
                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500">Signed in</div>
                    <div class="mt-2 truncate text-sm font-semibold text-white">
                        {{ auth()->user()->fullname ?? auth()->user()->username ?? 'Admin' }}
                    </div>

                    @if (app()->bound('impersonate') && app('impersonate')->isImpersonating())
                        <a href="{{ route('impersonate.leave') }}"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-white/10 bg-white/10 px-3 py-2 text-sm font-bold text-white transition hover:bg-white/15">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                            Return to Admin
                        </a>
                    @else
                        <button type="button"
                            onclick="event.preventDefault(); document.getElementById('tailwind-logout-form').submit();"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-lg border border-rose-300/20 bg-rose-500/10 px-3 py-2 text-sm font-bold text-rose-100 transition hover:bg-rose-500/20">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h10" />
                            </svg>
                            Logout
                        </button>
                    @endif
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-erp-line bg-white/90 backdrop-blur-xl">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-erp-line bg-white text-slate-600 transition hover:border-erp hover:text-erp-dark lg:hidden"
                            aria-label="Open ERP menu">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-erp-mute">@yield('title', $title ?? 'Admin')</div>
                            <div class="truncate text-sm font-semibold text-erp-ink">Retail operations center</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        
                        @php
                            $avatarName = trim(auth()->user()->fullname ?? auth()->user()->username ?? 'User');
                            $avatarInitials = collect(preg_split('/\s+/', $avatarName))
                                ->filter()
                                ->take(3)
                                ->map(fn ($part) => substr($part, 0, 1))
                                ->implode('');
                            $avatarImage = session('image');
                        @endphp
                        <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-erp-panel text-sm font-bold text-white ring-1 ring-slate-200">
                            @if (!empty($avatarImage))
                                <img class="h-full w-full object-cover"
                                    src="{{ asset('storage/images/users/' . $avatarImage) }}"
                                    alt="{{ $avatarName }}">
                            @else
                                {{ strtoupper($avatarInitials ?: substr($avatarName, 0, 3)) }}
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            <main class="w-full px-4 py-6 sm:px-6 lg:px-8">
                <div class="mb-6 rounded-lg border border-erp-line bg-white px-5 py-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">
                                <a href="{{ url('/dashboard') }}" class="transition hover:text-erp-dark">ERP Dashboard</a>
                                <span class="text-slate-300">/</span>
                                <span class="text-erp-dark">@yield('title', $title ?? 'Admin')</span>
                            </div>
                            <h1 class="mt-3 text-2xl font-bold tracking-tight text-erp-ink sm:text-3xl">
                                @hasSection('page_title')
                                    @yield('page_title')
                                @else
                                    {{ $title ?? 'Admin Dashboard' }}
                                @endif
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-erp-mute">
                                @yield('page_subtitle', 'Manage retail operations, orders, products, customers, and daily performance from one focused workspace.')
                            </p>
                        </div>

                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-sm">
                        <p class="font-bold">Please review the highlighted form fields.</p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>
        </div>
    </div>

    <form id="tailwind-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if ($tailwindLegacyAssets)
        @include('partials.js-libs')
    @endif
    @if ($tailwindOrdersAssets && !$tailwindLegacyAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/tether/dist/js/tether.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/datepicker/js/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert/js/sweetalert.js') }}"></script>
        <script src="{{ asset('components/chart.js/dist/Chart.js') }}"></script>
        <script>
            window.bootstrap = window.bootstrap || {};
            if (!window.bootstrap.Modal && window.jQuery && jQuery.fn.modal) {
                window.bootstrap.Modal = function (element) {
                    this.element = element;
                };
                window.bootstrap.Modal.prototype.show = function () {
                    jQuery(this.element).modal('show');
                };
                window.bootstrap.Modal.prototype.hide = function () {
                    jQuery(this.element).modal('hide');
                };
                window.bootstrap.Modal.getInstance = function (element) {
                    return new window.bootstrap.Modal(element);
                };
                jQuery(document).on('click', '[data-bs-dismiss="modal"]', function () {
                    jQuery(this).closest('.modal').modal('hide');
                });
            }
        </script>
    @endif
    @if ($tailwindPurchaseAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/tether/dist/js/tether.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/notification/js/bootstrap-growl.min.js') }}"></script>
        <script src="{{ asset('js/notification.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/plugins/datepicker/js/moment-with-locales.min.js') }}"></script>
        <script src="{{ asset('components/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/sweetalert/js/sweetalert.js') }}"></script>
    @endif
    @if ($tailwindSelect2Assets && !$tailwindLegacyAssets && !$tailwindPurchaseAssets && !$tailwindDashboardAssets && !$tailwindOrdersAssets)
        <script src="{{ asset('components/Jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('components/select2/dist/js/select2.full.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.jQuery || !jQuery.fn.select2) {
                    return;
                }

                jQuery('.billing-select2:not(.select2-hidden-accessible), .v2-select2:not(.select2-hidden-accessible)').each(function () {
                    var $select = jQuery(this);
                    var isV2Select = $select.hasClass('v2-select2');
                    $select.select2({
                        allowClear: !$select.prop('required') && $select.find('option[value=""]').length > 0,
                        dropdownCssClass: isV2Select ? 'v2-select2-dropdown' : 'billing-select2-dropdown',
                        placeholder: $select.data('placeholder') || $select.find('option[value=""]').first().text() || '',
                        width: '100%'
                    });
                });
            });
        </script>
    @endif
    @if ($tailwindPurchaseAssets || $tailwindDashboardAssets || $tailwindOrdersAssets)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.documentElement.style.overflowY = 'auto';
                document.documentElement.style.height = 'auto';
                document.documentElement.style.minHeight = '100%';
                document.body.style.overflowY = 'auto';
                document.body.style.height = 'auto';
                document.body.style.minHeight = '100vh';
            });
        </script>
    @endif
    @stack('scripts')
    @yield('scriptcode_three')
    @livewireScripts
</body>

</html>
