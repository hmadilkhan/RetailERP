@extends('layouts.master-tailwind')

@section('title', 'Sales Details')

@section('content')
@php
    $branchName = $names[0]->branch_name ?? 'Branch';
    $terminalName = $names[0]->terminal_name ?? 'Terminal';
    $modeTitles = [
        'isdb' => 'Item Sales Database',
        'ci' => 'Cash In',
        'co' => 'Cash Out',
        'sr' => 'Sales Return',
        'ex' => 'Expenses',
        1 => 'Cash Details',
        2 => 'Credit Card Details',
        3 => 'Customer Credit Details',
    ];
    $reportTitle = $modeTitles[$mode] ?? 'Sales Details';
    $recordCount = count($details);
    $totalQty = 0;
    $totalAmount = 0;
    $actualAmount = 0;
    $discountAmount = 0;

    foreach ($details as $row) {
        if ($mode === 'isdb') {
            $totalQty += $row->qty ?? 0;
            $totalAmount += $row->total_amount ?? 0;
        } elseif ($mode === 'sr') {
            $totalQty += $row->qty ?? 0;
            $totalAmount += $row->amount ?? 0;
        } elseif ($mode === 'ex' || $mode === 'ci' || $mode === 'co') {
            $totalAmount += $row->amount ?? 0;
        } else {
            $totalQty += $row->total_item_qty ?? 0;
            $totalAmount += $row->total_amount ?? 0;
            $actualAmount += $row->ActualReceiptAmount ?? 0;
            $discountAmount += $row->discount_amount ?? 0;
        }
    }
@endphp

<div class="space-y-5">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-erp-ink">{{ $reportTitle }}</h1>
            <p class="mt-1 text-sm text-erp-mute">{{ $branchName }} / {{ $terminalName }}</p>
        </div>
        <a href="{{ url('/sales-details') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-erp-line bg-white px-4 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark">
            Back to Sales Details
        </a>
    </header>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Records</div>
            <div class="mt-3 text-2xl font-black text-erp-ink">{{ number_format($recordCount, 0) }}</div>
        </div>
        @if ($mode === 'isdb' || $mode === 'sr' || !in_array($mode, ['ci', 'co', 'ex'], true))
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Quantity</div>
                <div class="mt-3 text-2xl font-black text-erp-ink">{{ number_format($totalQty, 2) }}</div>
            </div>
        @endif
        @if ($mode == 3)
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Discount</div>
                <div class="mt-3 text-2xl font-black text-erp-ink">{{ number_format($discountAmount, 0) }}</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Gross Sales</div>
                <div class="mt-3 text-2xl font-black text-erp-ink">{{ number_format($actualAmount, 0) }}</div>
            </div>
        @endif
        <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
            <div class="text-xs font-black uppercase tracking-[0.16em] text-erp-mute">Net Amount</div>
            <div class="mt-3 text-2xl font-black text-erp-dark">{{ session('currency') }} {{ number_format($totalAmount, 2) }}</div>
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-base font-black text-erp-ink">{{ $reportTitle }}</h2>
                <p class="mt-1 text-sm text-erp-mute">{{ number_format($recordCount, 0) }} records</p>
            </div>
            <input type="search" id="salesReportSearch" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp lg:w-80" placeholder="Search records...">
        </div>

        @if ($recordCount === 0)
            <div class="p-12 text-center text-sm font-bold text-erp-mute">No records found for this selection.</div>
        @else
            <div class="overflow-x-auto">
                @if ($mode === 'isdb')
                    <table class="sales-report-table min-w-full divide-y divide-erp-line text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-black">Item Code</th>
                                <th class="px-5 py-3 text-left font-black">Product Name</th>
                                <th class="px-5 py-3 text-right font-black">Qty</th>
                                <th class="px-5 py-3 text-right font-black">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line bg-white">
                            @foreach ($details as $value)
                                <tr class="report-row">
                                    <td class="px-5 py-3 font-bold text-erp-text">{{ $value->item_code }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->product_name }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-erp-ink">{{ number_format($value->qty, 2) }}</td>
                                    <td class="px-5 py-3 text-right font-black text-erp-ink">{{ number_format($value->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50 font-black text-erp-ink">
                            <tr><td class="px-5 py-3" colspan="2">Total</td><td class="px-5 py-3 text-right">{{ number_format($totalQty, 2) }}</td><td class="px-5 py-3 text-right">{{ number_format($totalAmount, 2) }}</td></tr>
                        </tfoot>
                    </table>
                @elseif ($mode === 'ci' || $mode === 'co')
                    <table class="sales-report-table min-w-full divide-y divide-erp-line text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-right font-black">Amount</th>
                                <th class="px-5 py-3 text-left font-black">Narration</th>
                                <th class="px-5 py-3 text-left font-black">Date</th>
                                <th class="px-5 py-3 text-left font-black">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line bg-white">
                            @foreach ($details as $value)
                                <tr class="report-row">
                                    <td class="px-5 py-3 text-right font-black text-erp-ink">{{ number_format($value->amount, 2) }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->narration }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ date('Y-m-d', strtotime($value->datetime)) }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ date('h:i a', strtotime($value->datetime)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50 font-black text-erp-ink">
                            <tr><td class="px-5 py-3 text-right">{{ number_format($totalAmount, 2) }}</td><td class="px-5 py-3" colspan="3">Total</td></tr>
                        </tfoot>
                    </table>
                @elseif ($mode === 'sr')
                    <table class="sales-report-table min-w-full divide-y divide-erp-line text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-black">Receipt No</th>
                                <th class="px-5 py-3 text-left font-black">Time</th>
                                <th class="px-5 py-3 text-left font-black">Product Name</th>
                                <th class="px-5 py-3 text-right font-black">Qty</th>
                                <th class="px-5 py-3 text-right font-black">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line bg-white">
                            @foreach ($details as $value)
                                <tr class="report-row">
                                    <td class="px-5 py-3 font-bold text-erp-text">{{ $value->receipt_no }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ date('h:i a', strtotime($value->timestamp)) }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->product_name }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-erp-ink">{{ number_format($value->qty, 2) }}</td>
                                    <td class="px-5 py-3 text-right font-black text-erp-ink">{{ number_format($value->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50 font-black text-erp-ink">
                            <tr><td class="px-5 py-3" colspan="3">Total</td><td class="px-5 py-3 text-right">{{ number_format($totalQty, 2) }}</td><td class="px-5 py-3 text-right">{{ number_format($totalAmount, 2) }}</td></tr>
                        </tfoot>
                    </table>
                @elseif ($mode === 'ex')
                    <table class="sales-report-table min-w-full divide-y divide-erp-line text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-black">S.No.</th>
                                <th class="px-5 py-3 text-left font-black">Date</th>
                                <th class="px-5 py-3 text-left font-black">Category</th>
                                <th class="px-5 py-3 text-left font-black">Details</th>
                                <th class="px-5 py-3 text-right font-black">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line bg-white">
                            @foreach ($details as $index => $value)
                                <tr class="report-row">
                                    <td class="px-5 py-3 font-bold text-erp-text">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ date('d F Y', strtotime($value->created_at)) }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->expense_category }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->expense_details }}</td>
                                    <td class="px-5 py-3 text-right font-black text-erp-ink">{{ number_format($value->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50 font-black text-erp-ink">
                            <tr><td class="px-5 py-3" colspan="4">Total</td><td class="px-5 py-3 text-right">{{ number_format($totalAmount, 2) }}</td></tr>
                        </tfoot>
                    </table>
                @else
                    <table class="sales-report-table min-w-full divide-y divide-erp-line text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr>
                                <th class="px-5 py-3 text-left font-black">Receipt</th>
                                <th class="px-5 py-3 text-right font-black">Item Qty</th>
                                @if ($mode == 3)
                                    <th class="px-5 py-3 text-right font-black">Actual Amount</th>
                                    <th class="px-5 py-3 text-right font-black">Discount</th>
                                @endif
                                <th class="px-5 py-3 text-right font-black">Total Amount</th>
                                <th class="px-5 py-3 text-left font-black">Date</th>
                                <th class="px-5 py-3 text-left font-black">Time</th>
                                <th class="px-5 py-3 text-left font-black">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-erp-line bg-white">
                            @foreach ($details as $value)
                                <tr class="report-row">
                                    <td class="px-5 py-3">
                                        <div class="font-black text-erp-ink">{{ $value->customer }}</div>
                                        <div class="mt-1 text-xs font-bold text-erp-mute">{{ $value->receipt_no }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-right font-bold text-erp-ink">{{ number_format($value->total_item_qty, 0) }}</td>
                                    @if ($mode == 3)
                                        <td class="px-5 py-3 text-right font-bold text-erp-ink">{{ number_format($value->ActualReceiptAmount, 0) }}</td>
                                        <td class="px-5 py-3 text-right font-bold text-erp-ink">{{ number_format($value->discount_amount, 0) }}</td>
                                    @endif
                                    <td class="px-5 py-3 text-right font-black text-erp-ink">{{ number_format($value->total_amount, 0) }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ $value->date }}</td>
                                    <td class="px-5 py-3 text-erp-text">{{ date('h:i a', strtotime($value->time)) }}</td>
                                    <td class="px-5 py-3">
                                        <a href="{{ url('print', $value->receipt_no) }}" class="inline-flex h-9 items-center rounded-lg border border-erp-line px-3 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Print</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50 font-black text-erp-ink">
                            <tr>
                                <td class="px-5 py-3">Total</td>
                                <td class="px-5 py-3 text-right">{{ number_format($totalQty, 0) }}</td>
                                @if ($mode == 3)
                                    <td class="px-5 py-3 text-right">{{ number_format($actualAmount, 0) }}</td>
                                    <td class="px-5 py-3 text-right">{{ number_format($discountAmount, 0) }}</td>
                                @endif
                                <td class="px-5 py-3 text-right">{{ number_format($totalAmount, 0) }}</td>
                                <td class="px-5 py-3" colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection

@section('scriptcode_three')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('salesReportSearch');
        if (!input) return;

        input.addEventListener('input', function() {
            var term = input.value.toLowerCase();
            document.querySelectorAll('.report-row').forEach(function(row) {
                row.classList.toggle('hidden', row.textContent.toLowerCase().indexOf(term) === -1);
            });
        });
    });
</script>
@endsection
