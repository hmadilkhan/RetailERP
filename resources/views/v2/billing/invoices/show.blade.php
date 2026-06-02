@extends('layouts.master-tailwind')

@section('title', 'Invoice Detail')
@section('page_title', 'Invoice #' . $invoice->invoice_no)
@section('page_subtitle', 'Detailed invoice breakdown, payments, discounts, adjustments, credit applications, and delivery actions.')

@section('content')
    @php
        $paidDate = $invoice->status === 'paid' ? optional($invoice->payments->sortByDesc('payment_date')->first())->payment_date : null;
        $statusClass = $invoice->status === 'paid'
            ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
            : ($invoice->status === 'partial' ? 'bg-amber-50 text-amber-700 ring-amber-200' : ($invoice->status === 'void' ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-sky-50 text-sky-700 ring-sky-200'));
    @endphp

    <div class="space-y-6">
        @if($errors->has('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800">{{ $errors->first('error') }}</div>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 xl:flex-row xl:items-center xl:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Invoice Actions</h2>
                    <p class="mt-1 text-sm text-erp-mute">Send, download, review history, or return to company invoices.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form method="post" action="{{ route('billing.invoices.whatsapp.send', $invoice->id) }}">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-4 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">Send WhatsApp</button>
                    </form>
                    <a href="{{ route('billing.invoices.pdf', $invoice->id) }}" class="inline-flex h-10 items-center rounded-lg border border-sky-200 bg-sky-50 px-4 text-sm font-bold text-sky-700 transition hover:bg-sky-100">Download PDF</a>
                    <a href="{{ route('billing.delivery-history', ['invoice_no' => $invoice->invoice_no]) }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Delivery History</a>
                    <a href="{{ route('billing.invoices.index', ['company_id' => $invoice->company_id]) }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ optional($invoice->company)->name }}</div>
                <p class="mt-2 text-sm text-erp-mute">Company ID {{ $invoice->company_id }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Status</div>
                <div class="mt-4"><span class="rounded-md px-3 py-2 text-xs font-bold uppercase ring-1 {{ $statusClass }}">{{ $invoice->status }}</span></div>
                <p class="mt-3 text-sm text-erp-mute">{{ $paidDate ? 'Paid on ' . \Carbon\Carbon::parse($paidDate)->format('M d, Y') : 'Payment pending' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Billing Period</div>
                <div class="mt-4 text-lg font-black text-erp-ink">{{ date('M d, Y', strtotime($invoice->period_start)) }}</div>
                <p class="mt-2 text-sm text-erp-mute">to {{ date('M d, Y', strtotime($invoice->period_end)) }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Balance Due</div>
                <div class="mt-4 text-2xl font-black text-rose-700">PKR {{ number_format($invoice->balance_amount + $invoice->previous_due, 2) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Due {{ date('M d, Y', strtotime($invoice->due_date)) }}</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Invoice Lines</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Description</th>
                            <th class="px-5 py-3 text-center font-bold">Qty</th>
                            <th class="px-5 py-3 text-right font-bold">Unit Price</th>
                            <th class="px-5 py-3 text-right font-bold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($invoice->lines as $line)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-erp-ink">{{ $line->description }}</td>
                                <td class="px-5 py-4 text-center">{{ number_format($line->qty, 0) }}</td>
                                <td class="px-5 py-4 text-right">PKR {{ number_format($line->unit_price, 2) }}</td>
                                <td class="px-5 py-4 text-right font-bold">PKR {{ number_format($line->line_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 text-sm">
                        <tr><td colspan="3" class="px-5 py-3 text-right font-bold">Subtotal</td><td class="px-5 py-3 text-right font-bold">PKR {{ number_format($invoice->subtotal, 2) }}</td></tr>
                        @if($invoice->tax_amount > 0)<tr><td colspan="3" class="px-5 py-3 text-right">Tax</td><td class="px-5 py-3 text-right">PKR {{ number_format($invoice->tax_amount, 2) }}</td></tr>@endif
                        @if(($invoice->discount_amount ?? 0) > 0)<tr><td colspan="3" class="px-5 py-3 text-right">Discount</td><td class="px-5 py-3 text-right text-rose-700">- PKR {{ number_format($invoice->discount_amount, 2) }}</td></tr>@endif
                        @if($invoice->previous_due > 0)<tr><td colspan="3" class="px-5 py-3 text-right">Outstanding at Issue</td><td class="px-5 py-3 text-right">PKR {{ number_format($invoice->previous_due, 2) }}</td></tr>@endif
                        <tr><td colspan="3" class="px-5 py-3 text-right font-black text-erp-ink">Current Invoice Total</td><td class="px-5 py-3 text-right font-black text-erp-ink">PKR {{ number_format($invoice->total_amount, 2) }}</td></tr>
                        @if($invoice->paid_amount > 0)<tr><td colspan="3" class="px-5 py-3 text-right">Paid Amount</td><td class="px-5 py-3 text-right text-emerald-700">PKR {{ number_format($invoice->paid_amount, 2) }}</td></tr>@endif
                        @if(($invoice->credit_applied_amount ?? 0) > 0)<tr><td colspan="3" class="px-5 py-3 text-right">Customer Credit Applied</td><td class="px-5 py-3 text-right text-sky-700">PKR {{ number_format($invoice->credit_applied_amount, 2) }}</td></tr>@endif
                        <tr><td colspan="3" class="px-5 py-3 text-right font-black text-rose-700">Current Invoice Balance</td><td class="px-5 py-3 text-right font-black text-rose-700">PKR {{ number_format($invoice->balance_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4"><h2 class="text-base font-bold text-erp-ink">Receive Payment</h2></div>
                <form method="post" action="{{ route('billing.invoices.payments.store', $invoice->id) }}" enctype="multipart/form-data" class="grid gap-4 p-5">
                    @csrf
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <input type="number" name="amount" min="0.01" step="0.01" placeholder="Amount" value="{{ old('amount') }}" required class="h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <select name="payment_mode_id" id="payment_mode_id" class="billing-select2 billing-select2-lg h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select payment mode">
                        <option value="">Select payment mode</option>
                        @foreach($paymentModes as $paymentMode)
                            <option value="{{ $paymentMode->payment_id }}" data-is-cash="{{ strcasecmp(trim((string) $paymentMode->payment_mode), 'cash') === 0 ? '1' : '0' }}" {{ old('payment_mode_id') == $paymentMode->payment_id ? 'selected' : '' }}>{{ $paymentMode->payment_mode }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="reference_no" value="{{ old('reference_no') }}" placeholder="Reference # optional" class="h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <textarea name="narration" rows="2" placeholder="Optional notes" class="rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ old('narration') }}</textarea>
                    <label class="block">
                        <span id="screenshots_label" class="text-sm font-bold text-erp-ink">Payment Screenshots</span>
                        <input type="file" name="screenshots[]" id="payment_screenshots" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple class="mt-2 block w-full rounded-lg border border-erp-line text-sm file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                        <span id="screenshots_help" class="mt-2 block text-xs text-erp-mute">Upload up to 8 screenshots. Required for non-cash modes.</span>
                    </label>
                    <button type="submit" class="h-11 rounded-lg bg-erp text-sm font-bold text-white transition hover:bg-erp-dark">Add Payment</button>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4"><h2 class="text-base font-bold text-erp-ink">Add Adjustment</h2></div>
                    <form method="post" action="{{ route('billing.invoices.adjustments.store', $invoice->id) }}" class="grid gap-4 p-5">
                        @csrf
                        <select name="type" required class="billing-select2 billing-select2-lg h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select adjustment type"><option value="debit">Debit (+)</option><option value="credit">Credit (-)</option></select>
                        <input type="number" name="amount" min="0.01" step="0.01" placeholder="Amount" required class="h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <input type="date" name="adjustment_date" value="{{ date('Y-m-d') }}" required class="h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <input type="text" name="reason" placeholder="Reason for adjustment" required class="h-11 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <button type="submit" class="h-11 rounded-lg border border-amber-200 bg-amber-50 text-sm font-bold text-amber-700 transition hover:bg-amber-100">Add Adjustment</button>
                    </form>
                </div>

                <div class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4"><h2 class="text-base font-bold text-erp-ink">Add Discount / Credit</h2></div>
                    <div class="grid gap-5 p-5 lg:grid-cols-2">
                        <form method="post" action="{{ route('billing.invoices.discounts.store', $invoice->id) }}" class="grid gap-3">
                            @csrf
                            <select name="discount_type" required class="billing-select2 h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select discount type"><option value="percentage">Percentage (%)</option><option value="amount">Fixed Amount</option></select>
                            <input type="number" name="discount_value" min="0.01" step="0.01" placeholder="Value" required class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <input type="date" name="discount_date" value="{{ date('Y-m-d') }}" required class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <input type="text" name="reason" placeholder="Discount reason" required class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <button type="submit" class="h-10 rounded-lg border border-sky-200 bg-sky-50 text-sm font-bold text-sky-700 transition hover:bg-sky-100" {{ $invoice->total_amount <= 0 ? 'disabled' : '' }}>Add Discount</button>
                        </form>
                        <form method="post" action="{{ route('billing.invoices.credits.apply', $invoice->id) }}" class="grid gap-3">
                            @csrf
                            <div class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-erp-text">Available: PKR {{ number_format($customerCreditBalance ?? 0, 2) }}</div>
                            <input type="date" name="application_date" value="{{ date('Y-m-d') }}" required class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <input type="number" name="amount" min="0.01" max="{{ min((float) ($customerCreditBalance ?? 0), (float) $invoice->balance_amount) }}" step="0.01" placeholder="Amount" required class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <input type="text" name="reason" value="Apply available customer credit" required class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <button type="submit" class="h-10 rounded-lg border border-emerald-200 bg-emerald-50 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100" {{ (($customerCreditBalance ?? 0) <= 0 || $invoice->balance_amount <= 0) ? 'disabled' : '' }}>Apply Credit</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        @foreach ([
            'Discounts' => ['rows' => $invoice->discounts, 'cols' => ['Date', 'Type', 'Value', 'Discount', 'Reason']],
            'Payment History' => ['rows' => $invoice->payments, 'cols' => ['Date', 'Amount', 'Voucher', 'Mode', 'Reference', 'Narration', 'Screenshots', 'Actions']],
            'Credit Applications' => ['rows' => $invoice->creditApplications, 'cols' => ['Date', 'Amount', 'Reason']],
            'Adjustments' => ['rows' => $invoice->adjustments, 'cols' => ['Date', 'Type', 'Amount', 'Reason']],
        ] as $sectionTitle => $table)
            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4"><h2 class="text-base font-bold text-erp-ink">{{ $sectionTitle }}</h2></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                            <tr><th class="px-5 py-3 text-left font-bold">#</th>@foreach($table['cols'] as $col)<th class="px-5 py-3 text-left font-bold">{{ $col }}</th>@endforeach</tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($table['rows'] as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">{{ $loop->iteration }}</td>
                                    @if($sectionTitle === 'Discounts')
                                        <td class="px-5 py-4">{{ date('M d, Y', strtotime($row->discount_date)) }}</td><td class="px-5 py-4">{{ $row->discount_type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}</td><td class="px-5 py-4">{{ $row->discount_type === 'percentage' ? number_format($row->discount_value, 2) . '%' : 'PKR ' . number_format($row->discount_value, 2) }}</td><td class="px-5 py-4 font-bold text-rose-700">PKR {{ number_format($row->discount_amount, 2) }}</td><td class="px-5 py-4">{{ $row->reason }}</td>
                                    @elseif($sectionTitle === 'Payment History')
                                        <td class="px-5 py-4">{{ date('M d, Y', strtotime($row->payment_date)) }}</td><td class="px-5 py-4 font-bold text-emerald-700">PKR {{ number_format($row->amount, 2) }}</td><td class="px-5 py-4">{{ optional($row->voucher)->voucher_no ?? '-' }}</td><td class="px-5 py-4">{{ optional($row->paymentMode)->payment_mode ?? 'N/A' }}</td><td class="px-5 py-4">{{ $row->reference_no ?? '-' }}</td><td class="px-5 py-4">{{ $row->narration ?? '-' }}</td><td class="px-5 py-4">{{ $row->screenshots->count() }} file(s)</td><td class="px-5 py-4">@if($row->voucher)<form method="post" action="{{ route('billing.invoices.payments.voucher.send', [$invoice->id, $row->id]) }}">@csrf<button class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700">Send Voucher</button></form>@else - @endif</td>
                                    @elseif($sectionTitle === 'Credit Applications')
                                        <td class="px-5 py-4">{{ date('M d, Y', strtotime($row->application_date)) }}</td><td class="px-5 py-4 font-bold text-sky-700">PKR {{ number_format($row->amount, 2) }}</td><td class="px-5 py-4">{{ $row->reason }}</td>
                                    @else
                                        <td class="px-5 py-4">{{ date('M d, Y', strtotime($row->adjustment_date)) }}</td><td class="px-5 py-4">{{ ucfirst($row->type) }}</td><td class="px-5 py-4 font-bold">PKR {{ number_format($row->amount, 2) }}</td><td class="px-5 py-4">{{ $row->reason }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($table['cols']) + 1 }}" class="px-5 py-10 text-center text-erp-mute">No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const paymentModeSelect = document.getElementById('payment_mode_id');
            const screenshotsInput = document.getElementById('payment_screenshots');
            const screenshotsLabel = document.getElementById('screenshots_label');
            const screenshotsHelp = document.getElementById('screenshots_help');
            if (!paymentModeSelect || !screenshotsInput || !screenshotsLabel || !screenshotsHelp) return;
            function syncScreenshotRequirement() {
                const selectedOption = paymentModeSelect.options[paymentModeSelect.selectedIndex];
                const requiresScreenshot = selectedOption && selectedOption.value !== '' && selectedOption.getAttribute('data-is-cash') !== '1';
                screenshotsInput.required = requiresScreenshot;
                screenshotsLabel.innerHTML = requiresScreenshot ? 'Payment Screenshots <span class="text-rose-600">*</span>' : 'Payment Screenshots';
                screenshotsHelp.textContent = requiresScreenshot ? 'Non-cash payments require at least one screenshot. You can upload up to 8 images, max 5 MB each.' : 'Cash screenshots are optional. You can still upload up to 8 images, max 5 MB each.';
            }
            paymentModeSelect.addEventListener('change', syncScreenshotRequirement);
            if (window.jQuery) {
                jQuery(paymentModeSelect).on('change.select2', syncScreenshotRequirement);
            }
            syncScreenshotRequirement();
        })();
    </script>
@endpush
