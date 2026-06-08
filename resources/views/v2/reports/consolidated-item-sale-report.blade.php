@extends('layouts.master-tailwind')

@section('title', 'Consolidated Item Sale Report')
@section('page_title', 'Consolidated Item Sale Report')
@section('page_subtitle', 'Review consolidated item sales by department, product, branch, terminal, and date.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Filter</h2>
                <p class="mt-1 text-sm text-erp-mute">Select a date range to run the consolidated report.</p>
            </div>
            <form name="searchFormItemSale" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                @include('v2.reports.partials.item-sale-filters', ['includeDepartment' => true])
                <div class="flex flex-wrap items-end gap-2 md:col-span-12">
                    <button type="button" name="btn_search_report" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
                    <button type="button" id="btnPdf" class="h-10 rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition hover:bg-rose-700">PDF Export</button>
                    <button type="button" id="btnExcel" class="h-10 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white transition hover:bg-emerald-700">Excel Export</button>
                    <a href="{{ url('reports/consolidated-item-sale-report') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Reset</a>
                    <span id="alert_fromdate" class="text-sm font-semibold text-rose-700"></span>
                </div>
            </form>
        </section>

        @include('v2.reports.partials.item-sale-summary')

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Consolidated Sales</h2>
                <p id="reportStatus" class="mt-1 text-sm text-erp-mute">Run the report to view matching sales.</p>
            </div>
            @include('partials.loader')
            <div id="itemSalesReport"></div>
        </section>
    </div>
@endsection

@push('scripts')
    @include('v2.reports.partials.item-sale-scripts', [
        'searchRoute' => route('consolidated.SrchISReport'),
        'excelUrl' => url('reports/consolidated-excel-export-item-sale-report'),
        'pdfUrl' => url('reports/pdf-export-item-sale-report'),
        'includeDepartment' => true,
    ])
@endpush
