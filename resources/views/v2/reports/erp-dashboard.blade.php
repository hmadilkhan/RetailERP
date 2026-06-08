@extends('layouts.master-tailwind')

@section('title', 'ERP Reports')
@section('page_title', 'ERP Report Workspace')
@section('page_subtitle', 'Search reports, reuse favorites, apply quick filters, and launch existing PDF or Excel reports from the V2 workspace.')
@section('naverpreport', 'active')

@php
    $reportGroups = [
        'Sales' => [
            ['key' => 'sales-declaration', 'title' => 'Sales Declaration', 'description' => 'Daily declaration with branch and terminal filters.', 'accent' => 'emerald', 'endpoint' => url('salesdeclerationreport'), 'excelEndpoint' => url('reports/excel-export-sales-declartion'), 'fields' => ['date', 'branch', 'terminal'], 'excelMode' => 'salesDeclaration', 'tags' => ['sales', 'tax', 'terminal']],
            ['key' => 'item-sale', 'title' => 'Item Sale Database', 'description' => 'Item-wise sold quantity, order mode, status, department, and inventory.', 'accent' => 'blue', 'endpoint' => url('itemsaledatabasepdf'), 'fields' => ['date', 'branch', 'terminal', 'type', 'multipleDepartment', 'subdepartment', 'inventory', 'ordermode', 'status'], 'tags' => ['sales', 'items', 'inventory']],
            ['key' => 'sales-return', 'title' => 'Sales Return', 'description' => 'Return activity by date, branch, terminal, and item code.', 'accent' => 'rose', 'endpoint' => url('salesreturnpdf'), 'excelEndpoint' => url('reports/sales-return-export'), 'fields' => ['date', 'branch', 'terminal', 'itemcode'], 'tags' => ['sales', 'return', 'items']],
            ['key' => 'invoice-details', 'title' => 'Invoice Details', 'description' => 'Invoice report by date, type, branch, and terminal.', 'accent' => 'amber', 'endpoint' => url('invoice-report'), 'excelEndpoint' => url('reports/excel-export-orders-report'), 'fields' => ['date', 'branch', 'terminal', 'type'], 'tags' => ['invoice', 'sales']],
            ['key' => 'sales-invoices', 'title' => 'Sales Invoices', 'description' => 'Sales invoice summary with customer and POS/website category.', 'accent' => 'cyan', 'endpoint' => url('sales-invoices-report'), 'fields' => ['date', 'branch', 'terminal', 'type', 'category', 'customer'], 'tags' => ['invoice', 'customer', 'sales']],
            ['key' => 'customer-sales', 'title' => 'Customer Sales Report', 'description' => 'Customer-wise sales with optional Excel export.', 'accent' => 'indigo', 'endpoint' => url('customer-sales-report'), 'excelEndpoint' => url('reports/excel-export-customer-sales'), 'fields' => ['date', 'branch', 'customer'], 'tags' => ['customer', 'sales']],
            ['key' => 'sales-person', 'title' => 'Sales Person Report', 'description' => 'Sales person performance by branch and order status.', 'accent' => 'violet', 'endpoint' => url('sales-person-report'), 'fields' => ['date', 'branch', 'salesperson', 'status'], 'tags' => ['salesperson', 'performance']],
        ],
        'Finance' => [
            ['key' => 'profit-loss-standard', 'title' => 'Profit & Loss Standard', 'description' => 'Standard profit and loss PDF by date and branch.', 'accent' => 'emerald', 'endpoint' => url('profitLossStandardReport'), 'fields' => ['date', 'branch'], 'tags' => ['profit', 'loss', 'finance']],
            ['key' => 'profit-loss-details', 'title' => 'Profit & Loss Details', 'description' => 'Detailed P&L movement for deeper review.', 'accent' => 'lime', 'endpoint' => url('profitLossDetailsReport'), 'fields' => ['date', 'branch'], 'tags' => ['profit', 'loss', 'details']],
            ['key' => 'expense-sheet', 'title' => 'Expense Sheet', 'description' => 'Expense report with PDF and Excel export.', 'accent' => 'orange', 'endpoint' => url('expense-report-pdf'), 'excelEndpoint' => url('export-expense-report'), 'fields' => ['date'], 'tags' => ['expense', 'excel']],
            ['key' => 'expense-category', 'title' => 'Expense By Category', 'description' => 'Category-wise expense view for a selected period.', 'accent' => 'red', 'endpoint' => url('expense_by_categorypdf'), 'fields' => ['date'], 'tags' => ['expense', 'category']],
            ['key' => 'cash-in-out', 'title' => 'Cash In/Out Report', 'description' => 'Cash movement by branch and terminal.', 'accent' => 'sky', 'endpoint' => url('cash-in-out-report'), 'excelEndpoint' => null, 'fields' => ['date', 'branch', 'terminal'], 'tags' => ['cash', 'terminal']],
            ['key' => 'order-receivable', 'title' => 'Orders Amount Receivable', 'description' => 'Pending receivable amount by date, branch, and terminal.', 'accent' => 'amber', 'endpoint' => url('order-amount-receivable'), 'excelEndpoint' => url('reports/excel-export-orders-receivables'), 'fields' => ['date', 'branch', 'terminal'], 'tags' => ['receivable', 'orders']],
            ['key' => 'customer-aging', 'title' => 'Customer Aging', 'description' => 'Customer receivable aging summary.', 'accent' => 'rose', 'endpoint' => url('customer-aging'), 'fields' => [], 'direct' => true, 'tags' => ['customer', 'aging']],
        ],
        'Inventory' => [
            ['key' => 'stock-report', 'title' => 'Stock Report', 'description' => 'Current stock by branch, department, and sub department.', 'accent' => 'green', 'endpoint' => url('inventoryReport'), 'fields' => ['branch', 'department', 'subdepartment'], 'tags' => ['stock', 'inventory']],
            ['key' => 'inventory-details', 'title' => 'Inventory Details', 'description' => 'Inventory detail sheet by period, branch, and department.', 'accent' => 'teal', 'endpoint' => url('inventory_detailsPDF'), 'fields' => ['date', 'branch', 'department', 'subdepartment'], 'tags' => ['inventory', 'department']],
            ['key' => 'inventory-images', 'title' => 'Inventory General Details', 'description' => 'Inventory details with images by branch and department.', 'accent' => 'cyan', 'endpoint' => url('inventory-image-report'), 'fields' => ['branch', 'department', 'subdepartment'], 'tags' => ['inventory', 'image']],
            ['key' => 'physical-inventory', 'title' => 'Physical Inventory Sheet', 'description' => 'Physical inventory sheet by department.', 'accent' => 'slate', 'endpoint' => url('inventoryReportPhysical'), 'fields' => ['physicalDepartment'], 'tags' => ['physical', 'inventory']],
            ['key' => 'stock-adjustment', 'title' => 'Stock Adjustment', 'description' => 'Stock adjustment report by date and branch.', 'accent' => 'purple', 'endpoint' => url('stockAdjustmentReport'), 'fields' => ['date', 'branch'], 'tags' => ['stock', 'adjustment']],
            ['key' => 'website-items-summary', 'title' => 'Website Items Summary', 'description' => 'Website item sales summary with Excel export.', 'accent' => 'blue', 'endpoint' => url('website-items-summary'), 'excelEndpoint' => url('reports/website-items-summary'), 'fields' => ['date'], 'tags' => ['website', 'items']],
        ],
        'Operations' => [
            ['key' => 'fbr-report', 'title' => 'FBR Report', 'description' => 'FBR report by date, branch, and terminal.', 'accent' => 'emerald', 'endpoint' => url('fbr-report'), 'fields' => ['date', 'branch', 'terminal'], 'tags' => ['fbr', 'tax']],
            ['key' => 'booking-order', 'title' => 'Booking Order Report', 'description' => 'Booking order activity by payment method and mode.', 'accent' => 'indigo', 'endpoint' => url('order-booking-report'), 'fields' => ['date', 'branch', 'paymentmethod', 'mode'], 'tags' => ['booking', 'orders']],
            ['key' => 'order-timing', 'title' => 'Orders Timing Summary', 'description' => 'Order timing summary by date and branch.', 'accent' => 'sky', 'endpoint' => url('order-timings-summary'), 'fields' => ['date', 'branch'], 'tags' => ['orders', 'timing']],
            ['key' => 'booking-delivery', 'title' => 'Booking Delivery Report', 'description' => 'Booking delivery report by date, branch, and terminal.', 'accent' => 'violet', 'endpoint' => url('booking-delivery-report'), 'fields' => ['date', 'branch', 'terminal'], 'tags' => ['booking', 'delivery']],
        ],
    ];

    $reports = collect($reportGroups)->flatMap(fn ($items, $group) => collect($items)->map(fn ($report) => array_merge($report, ['group' => $group])))->values();
    $reportCount = $reports->count();
    $excelCount = $reports->filter(fn ($report) => !empty($report['excelEndpoint']))->count();
@endphp

@section('content')
    <div class="space-y-6" data-erp-reports>
        <section class="overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="grid gap-0 lg:grid-cols-[1.4fr_0.6fr]">
                <div class="p-5 sm:p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-md bg-erp/10 px-2.5 py-1 text-xs font-bold uppercase tracking-[0.18em] text-erp-dark">V2 Reports</span>
                        <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $reportCount }} reports</span>
                        <span class="rounded-md bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">{{ $excelCount }} Excel exports</span>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-erp-ink sm:text-2xl">Find the right report faster</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-erp-mute">Use quick periods, favorites, recent launches, and AI guidance before opening the existing report PDFs and exports.</p>
                    <div class="mt-5 grid gap-3 md:grid-cols-[1fr_auto]">
                        <div class="relative">
                            <input id="reportSearch" type="search" class="h-11 w-full rounded-lg border border-erp-line bg-white px-4 pr-16 text-sm font-semibold text-erp-ink shadow-sm outline-none transition focus:border-erp focus:ring-4 focus:ring-erp/10" placeholder="Search reports by name, group, or tag">
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Search</span>
                        </div>
                        {{-- <a href="{{ url('/report-builder') }}" class="inline-flex h-11 items-center justify-center rounded-lg bg-erp px-4 text-sm font-bold text-white shadow-sm transition hover:bg-erp-dark">Dynamic Builder</a> --}}
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2" id="quickFilters">
                        <button type="button" data-range="today" class="quick-filter rounded-lg border border-erp-line  px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Today</button>
                        <button type="button" data-range="yesterday" class="quick-filter rounded-lg border border-erp-line  px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Yesterday</button>
                        <button type="button" data-range="week" class="quick-filter rounded-lg border border-erp-line  px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Last 7 Days</button>
                        <button type="button" data-range="month" class="quick-filter rounded-lg border border-erp-line  px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">This Month</button>
                    </div>
                </div>
                <aside class="border-t border-erp-line bg-slate-50 p-5 lg:border-l lg:border-t-0">
                    <div class="rounded-lg border border-erp-line bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">AI Summary</span>
                                <h3 class="mt-1 text-base font-bold text-erp-ink">Report helper</h3>
                            </div>
                            <span class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">Preview</span>
                        </div>
                        <p id="aiSummaryText" class="mt-3 text-sm leading-6 text-erp-mute">Select a report to see what AI should summarize after the report is generated.</p>
                        <div class="mt-4 space-y-2 text-sm text-erp-text" id="aiSummaryBullets">
                            <div class="rounded-md bg-slate-50 px-3 py-2">Detect unusual totals and period changes.</div>
                            <div class="rounded-md bg-slate-50 px-3 py-2">Highlight missing filters before export.</div>
                            <div class="rounded-md bg-slate-50 px-3 py-2">Suggest next report for deeper review.</div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-erp-ink">Favorites</h3>
                        <p class="mt-1 text-sm text-erp-mute">Saved in this browser for quick access.</p>
                    </div>
                    <button type="button" id="clearFavorites" class="text-xs font-bold text-rose-700 hover:text-rose-800">Clear</button>
                </div>
                <div id="favoriteReports" class="mt-4 flex flex-wrap gap-2"></div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-erp-ink">Recent Reports</h3>
                        <p class="mt-1 text-sm text-erp-mute">Last reports opened from this workspace.</p>
                    </div>
                    <button type="button" id="clearRecent" class="text-xs font-bold text-rose-700 hover:text-rose-800">Clear</button>
                </div>
                <div id="recentReports" class="mt-4 flex flex-wrap gap-2"></div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-base font-bold text-erp-ink">Report Catalog</h3>
                        <p class="mt-1 text-sm text-erp-mute">Choose a report, confirm filters, then open PDF or Excel.</p>
                    </div>
                    <div class="flex flex-wrap gap-2" id="groupTabs">
                        <button type="button" data-group="all" class="group-tab rounded-lg bg-erp px-3 py-2 text-xs font-bold text-white">All</button>
                        @foreach (array_keys($reportGroups) as $group)
                            <button type="button" data-group="{{ $group }}" class="group-tab rounded-lg border border-erp-line  px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">{{ $group }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3" id="reportGrid">
                @foreach ($reports as $report)
                    <article class="report-card flex min-h-[210px] flex-col rounded-lg border border-erp-line bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-erp hover:shadow-panel"
                        data-report-key="{{ $report['key'] }}"
                        data-title="{{ strtolower($report['title']) }}"
                        data-group="{{ $report['group'] }}"
                        data-tags="{{ strtolower(implode(' ', $report['tags'] ?? [])) }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="rounded-md bg-{{ $report['accent'] }}-50 px-2.5 py-1 text-xs font-bold text-{{ $report['accent'] }}-700">{{ $report['group'] }}</span>
                                <h4 class="mt-3 text-base font-bold text-erp-ink">{{ $report['title'] }}</h4>
                            </div>
                            <button type="button" class="favorite-btn rounded-lg border border-erp-line px-2.5 py-1.5 text-xs font-bold text-slate-500 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700" data-report-key="{{ $report['key'] }}" aria-label="Toggle favorite">Save</button>
                        </div>
                        <p class="mt-3 flex-1 text-sm leading-6 text-erp-mute">{{ $report['description'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (array_slice($report['tags'] ?? [], 0, 3) as $tag)
                                <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $tag }}</span>
                            @endforeach
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="button" class="open-report-btn inline-flex h-10 flex-1 items-center justify-center rounded-lg bg-erp px-3 text-sm font-bold text-white transition hover:bg-erp-dark" data-report-key="{{ $report['key'] }}">Open</button>
                            {{-- @if (!empty($report['excelEndpoint']))
                                <button type="button" class="excel-report-btn inline-flex h-10 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100" data-report-key="{{ $report['key'] }}">Excel</button>
                            @endif --}}
                        </div>
                    </article>
                @endforeach
            </div>
            <div id="emptyReports" class="hidden px-5 pb-6 text-sm font-semibold text-slate-500">No reports match your search.</div>
        </section>

        <div id="reportModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-menu">
                <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-erp-line bg-white px-5 py-4">
                    <div>
                        <span id="modalGroup" class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">Report</span>
                        <h3 id="modalTitle" class="mt-1 text-lg font-bold text-erp-ink">Report Filters</h3>
                        <p id="modalDescription" class="mt-1 text-sm text-erp-mute"></p>
                    </div>
                    <button type="button" id="closeReportModal" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-bold text-slate-600 transition hover:border-rose-300 hover:text-rose-700">Close</button>
                </div>
                <form id="reportForm" class="grid gap-4 p-5 md:grid-cols-2">
                    <div class="filter-field md:col-span-1" data-field="fromdate">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">From Date</label>
                        <input id="fromdate" type="date" class="h-10 w-full rounded-lg border border-erp-line px-3 text-sm font-semibold text-erp-ink focus:border-erp focus:ring-erp/20">
                    </div>
                    <div class="filter-field md:col-span-1" data-field="todate">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">To Date</label>
                        <input id="todate" type="date" class="h-10 w-full rounded-lg border border-erp-line px-3 text-sm font-semibold text-erp-ink focus:border-erp focus:ring-erp/20">
                    </div>
                    <div class="filter-field md:col-span-2" data-field="branch">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Branch</label>
                        <select id="branch" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Branch">
                            <option value="all">All Branches</option>
                            @foreach ($branches ?? [] as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="terminal">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Terminal</label>
                        <select id="terminal" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Terminal">
                            <option value="">Select Terminal</option>
                            <option value="0">All Terminals</option>
                            @foreach ($terminals ?? [] as $terminal)
                                <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="department">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Department</label>
                        <select id="department" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Department">
                            <option value="">Select Department</option>
                            <option value="0">All Department</option>
                            @foreach ($departments ?? [] as $department)
                                <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="multipleDepartment">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Departments</label>
                        <select id="multipledepartment" multiple class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Departments">
                            @foreach ($departments ?? [] as $department)
                                <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="physicalDepartment">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Physical Department</label>
                        <select id="physicalDepartment" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Department">
                            <option value="">Select Department</option>
                            <option value="0">All Department</option>
                            @foreach ($departments ?? [] as $department)
                                <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="subdepartment">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sub Department</label>
                        <select id="subdepartment" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Sub Department">
                            <option value="">Select Sub Department</option>
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="inventory">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Inventory</label>
                        <select id="inventory" multiple class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Select Inventory"></select>
                    </div>
                    <div class="filter-field md:col-span-1" data-field="type">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Type</label>
                        <select id="type" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="declaration">Declaration</option>
                            <option value="datewise">Datewise</option>
                        </select>
                    </div>
                    <div class="filter-field md:col-span-1" data-field="category">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Category</label>
                        <select id="category" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="all">All</option>
                            <option value="0">POS</option>
                            <option value="1">Website</option>
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="customer">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Customer</label>
                        <select id="customer" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm" data-placeholder="Search Customer"></select>
                    </div>
                    <div class="filter-field md:col-span-1" data-field="status">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Status</label>
                        <select id="status" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="all">All</option>
                            @foreach ($statuses ?? [] as $status)
                                <option value="{{ $status->order_status_id }}">{{ $status->order_status_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-1" data-field="ordermode">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Order Mode</label>
                        <select id="ordermode" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="all">All</option>
                            @foreach ($ordermodes ?? [] as $mode)
                                <option value="{{ $mode->order_mode_id }}">{{ $mode->order_mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-1" data-field="paymentmethod">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Payment Method</label>
                        <select id="paymentmethod" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="">Select Payment Method</option>
                            @foreach ($paymentModes ?? [] as $payment)
                                <option value="{{ $payment->payment_id }}">{{ $payment->payment_mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-1" data-field="mode">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Mode</label>
                        <select id="mode" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="all">All</option>
                            <option value="balances">Balances</option>
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="salesperson">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sales Person</label>
                        <select id="salesperson" class="v2-select2 h-10 w-full rounded-lg border border-erp-line text-sm">
                            <option value="all">All</option>
                            @foreach ($salespersons ?? [] as $person)
                                <option value="{{ $person->serviceprovideruser->user_id ?? '' }}">{{ $person->provider_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field md:col-span-2" data-field="itemcode">
                        <label class="mb-1 block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Item Code</label>
                        <input id="itemcode" type="text" class="h-10 w-full rounded-lg border border-erp-line px-3 text-sm font-semibold text-erp-ink focus:border-erp focus:ring-erp/20" placeholder="Enter item code">
                    </div>
                    <div class="md:col-span-2 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm leading-6 text-blue-900" id="modalAiNote"></div>
                    <div class="sticky bottom-0 -mx-5 -mb-5 flex flex-wrap justify-end gap-2 border-t border-erp-line bg-white px-5 py-4 md:col-span-2">
                        <button type="button" id="modalExcelBtn" class="hidden h-10 rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">Open Excel</button>
                        <button type="submit" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Open PDF</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const reports = @json($reports);
            const byKey = Object.fromEntries(reports.map(report => [report.key, report]));
            const searchInput = document.getElementById('reportSearch');
            const cards = Array.from(document.querySelectorAll('.report-card'));
            const emptyReports = document.getElementById('emptyReports');
            const modal = document.getElementById('reportModal');
            const form = document.getElementById('reportForm');
            const modalExcelBtn = document.getElementById('modalExcelBtn');
            const favoriteKey = 'erpReportFavorites';
            const recentKey = 'erpReportRecent';
            let activeGroup = 'all';
            let currentReport = null;
            let currentAction = 'pdf';
            let quickRange = 'today';

            const routeUrls = {
                terminals: "{{ url('getTerminals') }}",
                salesPersons: "{{ route('sp.branch') }}",
                inventory: "{{ route('getInventoryBySubDepartment') }}",
                customers: "{{ route('search-customer-by-names') }}",
            };

            function getStore(key) {
                try { return JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) { return []; }
            }

            function setStore(key, value) {
                localStorage.setItem(key, JSON.stringify(value.slice(0, 8)));
            }

            function isoDate(date) {
                return date.toISOString().slice(0, 10);
            }

            function setRange(range) {
                quickRange = range;
                const today = new Date();
                let from = new Date(today);
                let to = new Date(today);

                if (range === 'yesterday') {
                    from.setDate(today.getDate() - 1);
                    to.setDate(today.getDate() - 1);
                } else if (range === 'week') {
                    from.setDate(today.getDate() - 6);
                } else if (range === 'month') {
                    from = new Date(today.getFullYear(), today.getMonth(), 1);
                }

                document.getElementById('fromdate').value = isoDate(from);
                document.getElementById('todate').value = isoDate(to);
                document.querySelectorAll('.quick-filter').forEach(button => {
                    const active = button.dataset.range === range;
                    button.classList.toggle('bg-erp', active);
                    button.classList.toggle('text-white', active);
                    button.classList.toggle('border-erp', active);
                });
            }

            function renderPills(containerId, keys, emptyText) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';
                if (!keys.length) {
                    container.innerHTML = `<span class="text-sm font-semibold text-slate-500">${emptyText}</span>`;
                    return;
                }
                keys.forEach(key => {
                    const report = byKey[key];
                    if (!report) return;
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'rounded-lg border border-erp-line bg-slate-50 px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark';
                    button.textContent = report.title;
                    button.addEventListener('click', () => openReport(key, 'pdf'));
                    container.appendChild(button);
                });
            }

            function refreshStoredLists() {
                const favorites = getStore(favoriteKey);
                renderPills('favoriteReports', favorites, 'No favorites yet. Use the star on any report.');
                renderPills('recentReports', getStore(recentKey), 'No recent reports yet.');
                document.querySelectorAll('.favorite-btn').forEach(button => {
                    button.textContent = favorites.includes(button.dataset.reportKey) ? 'Saved' : 'Save';
                    button.classList.toggle('text-amber-700', favorites.includes(button.dataset.reportKey));
                    button.classList.toggle('bg-amber-50', favorites.includes(button.dataset.reportKey));
                });
            }

            function filterCards() {
                const term = (searchInput.value || '').trim().toLowerCase();
                let visible = 0;
                cards.forEach(card => {
                    const matchesSearch = !term || `${card.dataset.title} ${card.dataset.group} ${card.dataset.tags}`.includes(term);
                    const matchesGroup = activeGroup === 'all' || card.dataset.group === activeGroup;
                    const show = matchesSearch && matchesGroup;
                    card.classList.toggle('hidden', !show);
                    if (show) visible++;
                });
                emptyReports.classList.toggle('hidden', visible > 0);
            }

            function updateAiSummary(report) {
                document.getElementById('aiSummaryText').textContent = `${report.title}: AI should summarize totals, compare the selected period with the previous period, and call out exceptions before management review.`;
                document.getElementById('aiSummaryBullets').innerHTML = [
                    `Check ${report.group.toLowerCase()} trend changes and unusual values.`,
                    `Verify required filters: ${(report.fields || []).length ? report.fields.join(', ') : 'none'}.`,
                    `Suggest next action after reviewing ${report.title}.`
                ].map(text => `<div class="rounded-md bg-slate-50 px-3 py-2">${text}</div>`).join('');
            }

            function showFields(report) {
                const fields = new Set(report.fields || []);
                document.querySelectorAll('.filter-field').forEach(field => field.classList.add('hidden'));
                if (fields.has('date')) {
                    document.querySelector('[data-field="fromdate"]').classList.remove('hidden');
                    document.querySelector('[data-field="todate"]').classList.remove('hidden');
                }
                fields.forEach(field => {
                    const node = document.querySelector(`[data-field="${field}"]`);
                    if (node) node.classList.remove('hidden');
                });
            }

            function openReport(key, action) {
                const report = byKey[key];
                if (!report) return;
                currentReport = report;
                currentAction = action || 'pdf';
                updateAiSummary(report);

                if (report.direct) {
                    pushRecent(report.key);
                    window.location.href = report.endpoint;
                    return;
                }

                document.getElementById('modalGroup').textContent = report.group;
                document.getElementById('modalTitle').textContent = report.title;
                document.getElementById('modalDescription').textContent = report.description;
                document.getElementById('modalAiNote').textContent = `AI Summary: after opening this report, review ${report.title} for trend changes, missing filters, and high-risk records.`;
                modalExcelBtn.classList.toggle('hidden', !report.excelEndpoint);
                showFields(report);
                setRange(quickRange);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function pushRecent(key) {
                const recent = getStore(recentKey).filter(item => item !== key);
                recent.unshift(key);
                setStore(recentKey, recent);
                refreshStoredLists();
            }

            function appendIf(params, key, value) {
                if (value !== null && value !== undefined && value !== '') params.append(key, value);
            }

            function buildUrl(report, action) {
                const params = new URLSearchParams();
                const fields = new Set(report.fields || []);
                const endpoint = action === 'excel' && report.excelEndpoint ? report.excelEndpoint : report.endpoint;

                if (action === 'excel' && report.key === 'sales-declaration') {
                    return `${endpoint}/${document.getElementById('fromdate').value}/${document.getElementById('todate').value}/${document.getElementById('branch').value || 'all'}/${document.getElementById('terminal').value || 0}`;
                }

                if (action === 'excel' && report.key === 'website-items-summary') {
                    return `${endpoint}/${document.getElementById('fromdate').value}/${document.getElementById('todate').value}`;
                }

                if (fields.has('date')) {
                    if (report.key === 'expense-sheet' && action !== 'pdf') {
                        appendIf(params, 'first', document.getElementById('fromdate').value);
                        appendIf(params, 'second', document.getElementById('todate').value);
                    } else if (report.key === 'expense-sheet') {
                        appendIf(params, 'first', document.getElementById('fromdate').value);
                        appendIf(params, 'second', document.getElementById('todate').value);
                    } else {
                        appendIf(params, 'fromdate', document.getElementById('fromdate').value);
                        appendIf(params, 'todate', document.getElementById('todate').value);
                    }
                }

                if (fields.has('branch')) appendIf(params, action === 'excel' && report.key === 'invoice-details' ? 'branch' : 'branch', document.getElementById('branch').value);
                if (fields.has('terminal')) appendIf(params, report.key === 'invoice-details' && action === 'excel' ? 'terminal_id' : (action === 'excel' && report.key === 'order-receivable' ? 'terminal' : 'terminalid'), document.getElementById('terminal').value);
                if (fields.has('department')) appendIf(params, 'department', document.getElementById('department').value);
                if (fields.has('subdepartment')) appendIf(params, 'subdepartment', document.getElementById('subdepartment').value);
                if (fields.has('physicalDepartment')) appendIf(params, 'departid', document.getElementById('physicalDepartment').value);
                if (fields.has('type')) appendIf(params, 'type', document.getElementById('type').value);
                if (fields.has('category')) appendIf(params, 'category', document.getElementById('category').value);
                if (fields.has('customer')) appendIf(params, 'customer', document.getElementById('customer').value || 'all');
                if (fields.has('status')) appendIf(params, 'status', document.getElementById('status').value);
                if (fields.has('ordermode')) appendIf(params, 'ordermode', document.getElementById('ordermode').value);
                if (fields.has('paymentmethod')) appendIf(params, 'paymentmethod', document.getElementById('paymentmethod').value);
                if (fields.has('mode')) appendIf(params, 'mode', document.getElementById('mode').value);
                if (fields.has('salesperson')) appendIf(params, 'salesperson', document.getElementById('salesperson').value);
                if (fields.has('itemcode')) appendIf(params, 'code', document.getElementById('itemcode').value);

                if (fields.has('multipleDepartment')) {
                    Array.from(document.getElementById('multipledepartment').selectedOptions).forEach(option => params.append('department[]', option.value));
                }
                if (fields.has('inventory')) {
                    Array.from(document.getElementById('inventory').selectedOptions).forEach(option => params.append('inventory[]', option.value));
                }

                return `${endpoint}?${params.toString()}`;
            }

            function submitReport(action) {
                if (!currentReport) return;
                pushRecent(currentReport.key);
                window.location.href = buildUrl(currentReport, action || currentAction);
            }

            document.querySelectorAll('.open-report-btn').forEach(button => button.addEventListener('click', () => openReport(button.dataset.reportKey, 'pdf')));
            document.querySelectorAll('.excel-report-btn').forEach(button => button.addEventListener('click', () => openReport(button.dataset.reportKey, 'excel')));
            document.querySelectorAll('.favorite-btn').forEach(button => {
                button.addEventListener('click', event => {
                    event.stopPropagation();
                    const key = button.dataset.reportKey;
                    const favorites = getStore(favoriteKey);
                    const next = favorites.includes(key) ? favorites.filter(item => item !== key) : [key, ...favorites];
                    setStore(favoriteKey, next);
                    refreshStoredLists();
                });
            });
            document.querySelectorAll('.group-tab').forEach(button => {
                button.addEventListener('click', () => {
                    activeGroup = button.dataset.group;
                    document.querySelectorAll('.group-tab').forEach(tab => {
                        const active = tab === button;
                        tab.classList.toggle('bg-erp', active);
                        tab.classList.toggle('text-white', active);
                        tab.classList.toggle('border', !active);
                    });
                    filterCards();
                });
            });
            document.querySelectorAll('.quick-filter').forEach(button => button.addEventListener('click', () => setRange(button.dataset.range)));
            searchInput.addEventListener('input', filterCards);
            document.getElementById('closeReportModal').addEventListener('click', closeModal);
            modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
            form.addEventListener('submit', event => { event.preventDefault(); submitReport('pdf'); });
            modalExcelBtn.addEventListener('click', () => submitReport('excel'));
            document.getElementById('clearFavorites').addEventListener('click', () => { setStore(favoriteKey, []); refreshStoredLists(); });
            document.getElementById('clearRecent').addEventListener('click', () => { setStore(recentKey, []); refreshStoredLists(); });

            if (window.jQuery && jQuery.fn.select2) {
                jQuery('.v2-select2.select2-hidden-accessible').select2('destroy');

                const initSelect = ($select) => {
                    if ($select.hasClass('select2-hidden-accessible')) return;
                    $select.select2({
                        dropdownCssClass: 'v2-select2-dropdown',
                        placeholder: $select.data('placeholder') || '',
                        width: '100%',
                        dropdownParent: jQuery('#reportModal')
                    });
                };
                jQuery('.v2-select2').each(function () { initSelect(jQuery(this)); });
                jQuery('#customer').select2({
                    ajax: {
                        url: routeUrls.customers,
                        dataType: 'json',
                        delay: 250,
                        data: params => ({ q: params.term, branch: jQuery('#branch').val() || 'all' }),
                        processResults: data => ({ results: (data.items || []).map(item => ({ id: item.id, text: `${item.name} | ${item.branch_name} | ${item.mobile || ''}` })) })
                    },
                    dropdownCssClass: 'v2-select2-dropdown',
                    dropdownParent: jQuery('#reportModal'),
                    minimumInputLength: 1,
                    placeholder: 'Search Customer',
                    width: '100%'
                });

                jQuery('#branch').on('change', function () {
                    const branch = this.value;
                    jQuery.post(routeUrls.terminals, { _token: "{{ csrf_token() }}", branch, status: 1 }).done(function (resp) {
                        const terminal = jQuery('#terminal');
                        terminal.empty().append("<option value=''>Select Terminal</option><option value='0'>All Terminals</option>");
                        (resp || []).forEach(item => terminal.append(`<option value="${item.terminal_id}">${item.terminal_name}</option>`));
                        terminal.trigger('change');
                    });
                    jQuery.post(routeUrls.salesPersons, { _token: "{{ csrf_token() }}", branch }).done(function (resp) {
                        const salesperson = jQuery('#salesperson');
                        salesperson.empty().append("<option value='all'>All</option>");
                        ((resp && resp.providers) || []).forEach(item => {
                            if (item.serviceprovideruser) salesperson.append(`<option value="${item.serviceprovideruser.user_id}">${item.provider_name}</option>`);
                        });
                        salesperson.trigger('change');
                    });
                });

                jQuery('#department, #multipledepartment').on('change', function () {
                    const department = jQuery('#department').val() || (jQuery('#multipledepartment').val() || [])[0];
                    if (!department) return;
                    jQuery.post("{{ url('get_sub_departments') }}", { _token: "{{ csrf_token() }}", id: department }).done(function (resp) {
                        const subdepartment = jQuery('#subdepartment');
                        subdepartment.empty().append("<option value=''>Select Sub Department</option>");
                        (resp || []).forEach(item => subdepartment.append(`<option value="${item.sub_department_id}">${item.sub_depart_name}</option>`));
                        subdepartment.trigger('change');
                    });
                });

                jQuery('#subdepartment').on('change', function () {
                    const department = (jQuery('#multipledepartment').val() || [])[0] || jQuery('#department').val();
                    const subdepartment = this.value;
                    if (!department || !subdepartment) return;
                    jQuery.post(routeUrls.inventory, { _token: "{{ csrf_token() }}", department, subdepartment }).done(function (resp) {
                        const inventory = jQuery('#inventory');
                        inventory.empty();
                        (resp || []).forEach(item => inventory.append(`<option value="${item.id}">${item.product_name}</option>`));
                        inventory.trigger('change');
                    });
                });
            }

            setRange('today');
            refreshStoredLists();
            filterCards();
        });
    </script>
@endpush
