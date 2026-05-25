<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CRM') | ERP</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        crm: {
                            ink: '#0f172a',
                            panel: '#2f3437',
                            panelSoft: '#3b4145',
                            text: '#1e293b',
                            mute: '#64748b',
                            line: '#d8e1ec',
                            soft: '#f8fafc',
                            green: '#4CAF50',
                            greenLight: '#86efac',
                            greenDark: '#2E7D32',
                            success: '#166534',
                            successSoft: '#dcfce7',
                            warning: '#b45309',
                            warningSoft: '#ffedd5',
                            danger: '#b91c1c',
                            dangerSoft: '#fee2e2',
                        },
                    },
                    boxShadow: {
                        crm: '0 18px 45px rgba(15, 23, 42, 0.10)',
                        'crm-menu': '0 20px 55px rgba(15, 23, 42, 0.20)',
                    },
                },
            },
        };
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('crm_styles')
</head>

<body class="min-h-screen bg-[#f3f6f9] text-crm-text antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
            class="fixed inset-0 z-30 bg-slate-950/50 backdrop-blur-sm lg:hidden"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" x-cloak
            class="fixed inset-y-0 left-0 z-40 flex w-72 transform flex-col bg-crm-panel text-white shadow-crm-menu transition-transform duration-300 lg:translate-x-0">
            <div class="border-b border-white/10 px-5 py-5">
                <a href="{{ route('crm.dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-crm-green text-lg font-black text-white">
                        C
                    </span>
                    <span class="min-w-0">
                        <span class="block text-base font-bold tracking-tight">CRM Command</span>
                        <span class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Sales workspace</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5">
                <div class="px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">Main</div>
                <ul class="mt-3 space-y-1">
                    <li>
                        <a href="{{ route('crm.dashboard') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('crm.dashboard') || request()->routeIs('crm.index') ? 'bg-white text-crm-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('crm.dashboard') || request()->routeIs('crm.index') ? 'text-crm-green' : 'text-slate-500 group-hover:text-crm-greenLight' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-3H4v3Z" />
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('crm.board') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('crm.board') ? 'bg-white text-crm-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('crm.board') ? 'text-crm-green' : 'text-slate-500 group-hover:text-crm-greenLight' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 5h4v14H5V5Zm5 0h4v10h-4V5Zm5 0h4v7h-4V5Z" />
                            </svg>
                            <span>Pipeline</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('crm.leads.index') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('crm.leads.*') ? 'bg-white text-crm-ink' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('crm.leads.*') ? 'text-crm-green' : 'text-slate-500 group-hover:text-crm-greenLight' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 19a4 4 0 0 0-8 0M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6-1 2 2 3-4" />
                            </svg>
                            <span>Leads</span>
                        </a>
                    </li>
                </ul>

                <div class="mt-8 px-3 text-[11px] font-bold uppercase tracking-[0.22em] text-slate-500">Back office</div>
                <ul class="mt-3 space-y-1">
                    <li>
                        <a href="{{ url('/dashboard') }}"
                            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white">
                            <svg class="h-5 w-5 shrink-0 text-slate-500 group-hover:text-crm-greenLight" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H6v12h12v-4M14 4h6v6M20 4l-9 9" />
                            </svg>
                            <span>ERP Dashboard</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="rounded-lg bg-white/6 p-3">
                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-500">Signed in</div>
                    <div class="mt-2 truncate text-sm font-semibold text-white">
                        {{ auth()->user()->fullname ?? auth()->user()->username ?? 'ERP User' }}
                    </div>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-20 border-b border-crm-line bg-white/90 backdrop-blur-xl">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" @click="sidebarOpen = true"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-crm-line bg-white text-slate-600 transition hover:border-crm-green hover:text-crm-greenDark lg:hidden"
                            aria-label="Open CRM menu">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-crm-mute">@yield('title', 'CRM')</div>
                            <div class="truncate text-sm font-semibold text-crm-ink">Sales operations center</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <a href="{{ route('crm.leads.create') }}"
                            class="hidden rounded-lg bg-crm-green px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-crm-greenDark sm:inline-flex">
                            New Lead
                        </a>

                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open"
                                class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-crm-line bg-white text-slate-600 transition hover:border-crm-green hover:text-crm-greenDark">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17H20l-1.405-1.405A2.03 2.03 0 0 1 18 14.158V11a6 6 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9" />
                                </svg>
                                @if (($crmUnreadNotificationsCount ?? 0) > 0)
                                    <span class="absolute -right-1 -top-1 inline-flex min-h-[1.15rem] min-w-[1.15rem] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">
                                        {{ min($crmUnreadNotificationsCount, 99) }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" x-cloak @click.away="open = false"
                                class="absolute right-0 z-50 mt-3 w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-lg border border-crm-line bg-white text-crm-text shadow-crm">
                                <div class="border-b border-crm-line bg-slate-50 px-5 py-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <div class="text-sm font-bold text-crm-ink">Notifications</div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.18em] text-crm-mute">
                                                {{ $crmUnreadNotificationsCount ?? 0 }} unread
                                            </div>
                                        </div>
                                        @if (($crmUnreadNotificationsCount ?? 0) > 0)
                                            <form method="POST" action="{{ route('crm.notifications.read-all') }}">
                                                @csrf
                                                <button type="submit"
                                                    class="text-xs font-bold uppercase tracking-[0.16em] text-crm-greenDark transition hover:text-crm-green">
                                                    Mark all read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <div class="max-h-[28rem] overflow-y-auto">
                                    @forelse (($crmRecentNotifications ?? collect()) as $notification)
                                        @php
                                            $kind = data_get($notification->data, 'kind');
                                            $badgeClasses = [
                                                'lead_assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                                                'status_changed' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
                                                'followup_added' => 'bg-amber-50 text-amber-700 ring-amber-200',
                                                'followup_due_today' => 'bg-green-50 text-green-700 ring-green-200',
                                                'followup_overdue' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                            ];
                                        @endphp
                                        <a href="{{ route('crm.notifications.open', $notification->id) }}"
                                            class="block border-b border-slate-100 px-5 py-4 transition hover:bg-slate-50">
                                            <div class="flex items-start gap-3">
                                                <span class="mt-0.5 inline-flex rounded-md px-2 py-1 text-[11px] font-bold ring-1 {{ $badgeClasses[$kind] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                                    {{ str($kind)->replace('_', ' ')->title() }}
                                                </span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <p class="text-sm font-bold text-crm-ink">{{ data_get($notification->data, 'title', 'CRM Update') }}</p>
                                                        @if (is_null($notification->read_at))
                                                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-crm-green"></span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-2 text-sm leading-6 text-crm-mute">{{ data_get($notification->data, 'message') }}</p>
                                                    <div class="mt-3 flex items-center justify-between gap-3 text-xs font-medium text-crm-mute">
                                                        <span>{{ data_get($notification->data, 'lead_code', 'Lead') }}</span>
                                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-6 py-12 text-center">
                                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.03 2.03 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9" />
                                                </svg>
                                            </div>
                                            <p class="mt-4 text-sm font-bold text-crm-ink">No notifications yet.</p>
                                            <p class="mt-2 text-sm text-crm-mute">CRM activity alerts will appear here.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-crm-panel text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->fullname ?? auth()->user()->username ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[92rem] px-4 py-6 sm:px-6 lg:px-8">
                <div class="mb-6 rounded-lg border border-crm-line bg-white px-5 py-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-crm-mute">
                                <a href="{{ url('/dashboard') }}" class="transition hover:text-crm-greenDark">ERP Dashboard</a>
                                <span class="text-slate-300">/</span>
                                <span class="text-crm-greenDark">@yield('title', 'CRM')</span>
                            </div>
                            <h1 class="mt-3 text-2xl font-bold tracking-tight text-crm-ink sm:text-3xl">
                                @yield('page_title', 'CRM Workspace')
                            </h1>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-crm-mute">
                                @yield('page_subtitle', 'Manage leads, pipeline movement, reminders, and customer conversations from one focused workspace.')
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('crm.dashboard') }}"
                                class="rounded-lg border border-crm-line bg-white px-3 py-2 text-sm font-semibold text-crm-text transition hover:border-crm-green hover:text-crm-greenDark">
                                Reports
                            </a>
                            <a href="{{ route('crm.leads.index') }}"
                                class="rounded-lg border border-crm-line bg-white px-3 py-2 text-sm font-semibold text-crm-text transition hover:border-crm-green hover:text-crm-greenDark">
                                Lead List
                            </a>
                        </div>
                    </div>
                </div>

                @if (session('crm_success'))
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">
                        {{ session('crm_success') }}
                    </div>
                @endif

                @if (session('crm_error'))
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-800 shadow-sm">
                        {{ session('crm_error') }}
                    </div>
                @endif

                @if (session('crm_duplicate_warning'))
                    <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-800 shadow-sm">
                        {{ session('crm_duplicate_warning') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 shadow-sm">
                        <p class="font-bold">Please review the highlighted CRM form fields.</p>
                        <ul class="mt-2 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        (function() {
            document.addEventListener('submit', function(event) {
                const form = event.target;

                if (!(form instanceof HTMLFormElement) || !form.matches('[data-crm-submit]')) {
                    return;
                }

                const buttons = form.querySelectorAll('[type="submit"]');
                buttons.forEach(function(button) {
                    if (!(button instanceof HTMLButtonElement)) {
                        return;
                    }

                    if (!button.dataset.originalLabel) {
                        button.dataset.originalLabel = button.innerHTML;
                    }

                    button.disabled = true;
                    button.classList.add('opacity-70', 'cursor-not-allowed');
                    button.innerHTML = button.dataset.loadingLabel || 'Processing...';
                });
            });
        })();
    </script>
    @stack('crm_scripts')
</body>

</html>
