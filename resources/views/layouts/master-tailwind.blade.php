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

    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
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
            <div class="border-b border-white/10 px-4 py-4">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-1 rounded-lg p-1 transition hover:bg-white/[0.04]">
                    <span class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/sabify-mark.png') }}" alt="Sabify" class="h-full w-full object-contain">
                    </span>
                    <span class="min-w-0 -ml-1 pt-1">
                        <span class="block text-2xl font-black leading-none tracking-tight text-white">Sabify</span>
                        <span class="mt-2 block text-[10px] font-black uppercase leading-4 tracking-[0.18em] text-erp-light">Retail & Restaurant</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.22em] text-slate-400">ERP Provider</span>
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
                                    || (\Illuminate\Support\Str::snake(strtolower($pages->page_name)) === 'billing_section' && $currentUrl == 'billing/delivery-history');
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

            <main class="mx-auto max-w-[92rem] px-4 py-6 sm:px-6 lg:px-8">
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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
    @livewireScripts
</body>

</html>
