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
                            text: '#1e293b',
                            mute: '#64748b',
                            line: '#dbe5f0',
                            soft: '#f8fbff',
                            blue: '#114a8f',
                            royal: '#1d6fd6',
                            deep: '#0a2d57',
                            glow: '#dbeafe',
                            success: '#166534',
                            successSoft: '#dcfce7',
                            warning: '#b45309',
                            warningSoft: '#ffedd5',
                            danger: '#b91c1c',
                            dangerSoft: '#fee2e2',
                        },
                        erp: {
                            brand: '#114a8f',
                            accent: '#2f80ed',
                        },
                    },
                    boxShadow: {
                        crm: '0 24px 60px rgba(15, 23, 42, 0.10)',
                        'crm-soft': '0 12px 32px rgba(15, 23, 42, 0.08)',
                    },
                    borderRadius: {
                        '4xl': '2rem',
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

<body class="min-h-screen bg-slate-50 text-crm-text antialiased"
    style="background-image:
        radial-gradient(circle at top left, rgba(17, 74, 143, 0.12), transparent 28%),
        radial-gradient(circle at top right, rgba(47, 128, 237, 0.10), transparent 24%),
        linear-gradient(180deg, #eef4fb 0%, #f8fafc 100%);">

    <div class="min-h-screen">
        <header class="relative overflow-hidden border-b border-white/40 bg-gradient-to-br from-crm-deep via-crm-blue to-erp-accent text-white shadow-crm">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.14),transparent_22%)]"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-3xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.28em] text-white/90">
                            <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                            Sales CRM
                        </div>
                        <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">
                            @yield('page_title', 'CRM Workspace')
                        </h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-200 sm:text-base">
                            @yield('page_subtitle', 'Premium sales workspace built inside the ERP environment.')
                        </p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <nav class="flex flex-wrap gap-2 rounded-full border border-white/10 bg-white/10 p-1 backdrop-blur">
                            <a href="{{ route('crm.dashboard') }}"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('crm.dashboard') || request()->routeIs('crm.index') ? 'bg-white text-crm-deep shadow-lg' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('crm.board') }}"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('crm.board') ? 'bg-white text-crm-deep shadow-lg' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                Pipeline
                            </a>
                            <a href="{{ route('crm.leads.index') }}"
                                class="rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('crm.leads.*') ? 'bg-white text-crm-deep shadow-lg' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                                Leads
                            </a>
                        </nav>

                        <div class="flex items-center gap-3">
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open"
                                    class="relative inline-flex h-14 w-14 items-center justify-center rounded-3xl border border-white/10 bg-white/10 text-white backdrop-blur transition hover:bg-white/15">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17H20l-1.405-1.405A2.03 2.03 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5.143m5.714 0a2.857 2.857 0 1 1-5.714 0m5.714 0H9.143" />
                                    </svg>
                                    @if (($crmUnreadNotificationsCount ?? 0) > 0)
                                        <span class="absolute right-3 top-3 inline-flex min-h-[1.4rem] min-w-[1.4rem] items-center justify-center rounded-full bg-rose-500 px-1.5 text-[11px] font-bold text-white">
                                            {{ min($crmUnreadNotificationsCount, 99) }}
                                        </span>
                                    @endif
                                </button>

                                <div x-show="open" x-cloak @click.away="open = false"
                                    class="absolute right-0 z-40 mt-3 w-[24rem] overflow-hidden rounded-[28px] border border-white/10 bg-white text-crm-text shadow-crm">
                                    <div class="border-b border-slate-200 bg-slate-50/90 px-5 py-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-semibold text-crm-ink">CRM Notifications</div>
                                                <div class="mt-1 text-xs uppercase tracking-[0.22em] text-crm-mute">
                                                    {{ $crmUnreadNotificationsCount ?? 0 }} unread
                                                </div>
                                            </div>
                                            @if (($crmUnreadNotificationsCount ?? 0) > 0)
                                                <form method="POST" action="{{ route('crm.notifications.read-all') }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-crm-blue transition hover:text-crm-deep">
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
                                                    'followup_due_today' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                                    'followup_overdue' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                                ];
                                            @endphp
                                            <a href="{{ route('crm.notifications.open', $notification->id) }}"
                                                class="block border-b border-slate-100 px-5 py-4 transition hover:bg-slate-50">
                                                <div class="flex items-start gap-3">
                                                    <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 {{ $badgeClasses[$kind] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                                        {{ str($kind)->replace('_', ' ')->title() }}
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <p class="text-sm font-semibold text-crm-ink">{{ data_get($notification->data, 'title', 'CRM Update') }}</p>
                                                            @if (is_null($notification->read_at))
                                                                <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-crm-blue"></span>
                                                            @endif
                                                        </div>
                                                        <p class="mt-2 text-sm leading-6 text-crm-mute">{{ data_get($notification->data, 'message') }}</p>
                                                        <div class="mt-3 flex items-center justify-between gap-3 text-xs text-crm-mute">
                                                            <span>{{ data_get($notification->data, 'lead_code', 'Lead') }}</span>
                                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-6 py-14 text-center">
                                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.03 2.03 0 0 1 18 14.158V11a6 6 0 1 0-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9" />
                                                    </svg>
                                                </div>
                                                <p class="mt-4 text-sm font-semibold text-crm-ink">No CRM notifications yet.</p>
                                                <p class="mt-2 text-sm text-crm-mute">Assignments, status movement, and reminder alerts will appear here.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-3xl border border-white/10 bg-white/10 px-4 py-3 text-sm backdrop-blur">
                                <div class="text-xs uppercase tracking-[0.22em] text-white/60">Signed In</div>
                                <div class="mt-1 font-semibold text-white">{{ auth()->user()->fullname ?? auth()->user()->username ?? 'ERP User' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <div class="inline-flex items-center gap-2 rounded-full border border-crm-line bg-white/75 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-crm-mute shadow-crm-soft backdrop-blur">
                    <a href="{{ url('/dashboard') }}" class="transition hover:text-crm-blue">ERP Dashboard</a>
                    <span>/</span>
                    <span class="text-crm-blue">@yield('title', 'CRM')</span>
                </div>
            </div>

            @if (session('crm_success'))
                <div class="mb-5 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 shadow-crm-soft">
                    {{ session('crm_success') }}
                </div>
            @endif

            @if (session('crm_duplicate_warning'))
                <div class="mb-5 rounded-3xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 shadow-crm-soft">
                    {{ session('crm_duplicate_warning') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('crm_scripts')
</body>

</html>
