@extends('layouts.master-tailwind')

@section('title', 'Order Detail')
@section('page_title', 'Order #' . $order->id)
@section('page_subtitle', 'Items, payment, delivery and status history of this order.')

@section('content')
    @php
        $currency = session('currency');
        $statusName = $order->orderStatus?->order_status_name ?? '-';
        $statusId = $order->orderStatus?->order_status_id ?? 0;
        $roleId = session('roleId');

        // Status ka dikhane wala naam (purane page jaisa)
        $statusLabel = function ($name) {
            return match ($name) {
                'Ready for Delivery' => 'Ready',
                'Dispatch' => 'Dispatch from workshop',
                'Order Picked Up' => 'Picked Up By Branch',
                default => $name,
            };
        };
        $statusTone = function ($name) {
            return match ($name) {
                'Pending', 'Cancel', 'Void', 'Sales Return' => ['badge' => 'bg-rose-50 text-rose-700 ring-rose-200', 'dot' => 'bg-rose-500'],
                'Processing' => ['badge' => 'bg-amber-50 text-amber-700 ring-amber-200', 'dot' => 'bg-amber-500'],
                'Ready for Delivery' => ['badge' => 'bg-sky-50 text-sky-700 ring-sky-200', 'dot' => 'bg-sky-500'],
                'Order Picked Up' => ['badge' => 'bg-cyan-50 text-cyan-700 ring-cyan-200', 'dot' => 'bg-cyan-500'],
                default => ['badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500'],
            };
        };
        $money = function ($value) use ($currency) {
            $value = (float) $value;
            return trim($currency . ' ' . number_format($value, floor($value) == $value ? 0 : 2));
        };

        $taxName = '';
        $taxAmount = 0;
        if (!empty($order->orderAccountSub)) {
            if ($order->orderAccountSub->sales_tax_amount > 0) {
                $taxName = 'FBR';
                $taxAmount = $order->orderAccountSub->sales_tax_amount;
            } elseif ($order->orderAccountSub->srb > 0) {
                $taxName = 'SRB';
                $taxAmount = $order->orderAccountSub->srb;
            }
        }

        $isCredit = $order->payment_id == 3;
        $receivedTotal = (float) ($order->orderAccount?->receive_amount ?? 0) + (float) $received;
        $grandValue = $isCredit ? $order->total_amount - $receivedTotal : $order->total_amount;

        // Purane page jaisa: worker mode (role 19) ya inventory wale items hi dikhte hain
        $visibleItems = $order->orderdetails->filter(
            fn($item) => ($roleId == 19 && $item->mode == 'worker') || !empty($item->inventory),
        );
        $totalQty = $visibleItems->sum(fn($item) => (float) $item->total_qty);

        $btn = 'inline-flex h-10 items-center gap-2 rounded-lg px-4 text-sm font-bold transition';
        $tone = $statusTone($statusName);
    @endphp

    <div class="space-y-6">
        {{-- Header --}}
        <section class="overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-5 px-5 py-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0 space-y-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-2xl font-black text-erp-ink">
                            @if ($order->web == 1)
                                Order ID {{ $order->url_orderid }}
                            @else
                                Receipt # {{ $order->receipt_no }}
                            @endif
                        </h2>
                        <span class="rounded-md px-3 py-1.5 text-xs font-bold uppercase tracking-wide ring-1 {{ $tone['badge'] }}">
                            {{ $statusLabel($statusName) }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-erp-mute">
                        <span class="inline-flex items-center gap-1.5"><i class="icofont icofont-building-alt text-erp"></i>
                            <span class="font-semibold text-erp-text">{{ $order->branchrelation?->branch_name ?? '-' }}</span></span>
                        <span class="inline-flex items-center gap-1.5"><i class="icofont icofont-user-alt-3 text-erp"></i>
                            <span class="font-semibold text-erp-text">{{ ucwords($order->customer?->name ?? 'Walk-in') }}</span></span>
                        <span class="inline-flex items-center gap-1.5"><i class="icofont icofont-calendar text-erp"></i>
                            Booked {{ date('d M Y', strtotime($order->date)) . ' ' . date('h:i A', strtotime($order->time)) }}</span>
                        @if ($order->delivery_date != '')
                            <span class="inline-flex items-center gap-1.5"><i class="icofont icofont-delivery-time text-erp"></i>
                                Delivery {{ date('d M Y', strtotime($order->delivery_date)) }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 xl:justify-end">
                    @if (auth()->user()->id == 929 && $statusName == 'Delivered')
                        <a href="{{ url('change-order-status') }}/{{ $order->id }}/1"
                            class="{{ $btn }} bg-rose-600 text-white hover:bg-rose-700">
                            <i class="icofont icofont-undo"></i> Pending</a>
                    @endif
                    @if ($roleId == 19 or $roleId == 2)
                        @if ($statusName == 'Pending')
                            <a href="{{ url('change-order-status') }}/{{ $order->id }}/2"
                                class="{{ $btn }} bg-amber-500 text-white hover:bg-amber-600">
                                <i class="icofont icofont-spinner-alt-3"></i> Processing</a>
                        @elseif($statusName == 'Processing')
                            <a href="{{ url('change-order-status') }}/{{ $order->id }}/3"
                                class="{{ $btn }} bg-sky-600 text-white hover:bg-sky-700">
                                <i class="icofont icofont-check-circled"></i> Ready</a>
                        @elseif($statusName == 'Ready for Delivery')
                            <a href="{{ url('change-order-status') }}/{{ $order->id }}/6"
                                class="{{ $btn }} bg-erp text-white hover:bg-erp-dark">
                                <i class="icofont icofont-vehicle-delivery-van"></i> Dispatch</a>
                        @endif
                    @endif
                    @if ($statusId >= 6 && ($roleId == 19 or $roleId == 20 or $roleId == 2))
                        <a href="{{ url('voucher') }}/{{ $order->receipt_no }}" target="_blank"
                            class="{{ $btn }} border border-erp-line bg-white text-erp-text hover:border-erp hover:text-erp-dark">
                            <i class="icofont icofont-printer"></i> Print</a>
                    @endif
                    @if ($statusId >= 2 && session('company_id') == 102)
                        <a href="{{ url('factory-operation-report') }}?order={{ $order->id }}" target="_blank"
                            class="{{ $btn }} border border-erp-line bg-white text-erp-text hover:border-erp hover:text-erp-dark">
                            <i class="icofont icofont-file-document"></i> Operation Report</a>
                    @endif
                    <a href="{{ url('orders-view') }}"
                        class="{{ $btn }} border border-erp-line bg-slate-50 text-erp-text hover:border-erp hover:text-erp-dark">
                        <i class="icofont icofont-arrow-left"></i> Back to Orders</a>
                </div>
            </div>
        </section>

        {{-- Quick stats --}}
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">{{ $isCredit ? 'Balance' : 'Order Total' }}</div>
                <div class="mt-3 text-2xl font-black {{ $isCredit && $grandValue > 0 ? 'text-rose-700' : 'text-erp-ink' }}">{{ $money($grandValue) }}</div>
                @if ($isCredit)
                    <p class="mt-1 text-sm text-erp-mute">of {{ $money($order->total_amount) }}</p>
                @endif
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Items</div>
                <div class="mt-3 text-2xl font-black text-erp-ink">{{ $visibleItems->count() }}</div>
                <p class="mt-1 text-sm text-erp-mute">Total qty {{ number_format($totalQty, floor($totalQty) == $totalQty ? 0 : 2) }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Payment Method</div>
                <div class="mt-3 text-lg font-black text-erp-ink">{{ $order->payment?->payment_mode ?? '-' }}</div>
                <p class="mt-1 text-sm text-erp-mute">Sales person: {{ !empty($provider) ? $provider->serviceprovider->provider_name : '-' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Service Type</div>
                <div class="mt-3 text-lg font-black text-erp-ink">{{ $order->service?->serviceType?->name ?? 'N/A' }}</div>
                <p class="mt-1 text-sm text-erp-mute">Order #{{ $order->id }}</p>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Left: items + payments --}}
            <div class="space-y-6 xl:col-span-2">
                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">Order Items</h2>
                        <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-erp-mute">{{ $visibleItems->count() }} items</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($visibleItems as $key => $item)
                            @php
                                $isWorker = $roleId == 19 && $item->mode == 'worker';
                                $price = $item->item_price != '' ? (float) $item->item_price : 0;
                                $lineTotal = $price * (float) $item->total_qty;
                            @endphp
                            <div class="p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                                    <img id="myImg{{ $key }}"
                                        src="{{ Custom_Helper::getProductImage(optional($item->inventory)->url, optional($item->inventory)->image) }}"
                                        alt="{{ $item->note }}"
                                        class="h-20 w-20 shrink-0 cursor-pointer rounded-lg border border-erp-line object-cover transition hover:opacity-80"
                                        onclick="showImage('{{ $key }}')">

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded bg-slate-100 px-2 py-0.5 font-mono text-xs font-bold text-erp-text">{{ optional($item->inventory)->item_code }}</span>
                                                    @if (!$isWorker && optional($item->inventory)->department)
                                                        <span class="rounded bg-sky-50 px-2 py-0.5 text-xs font-bold text-sky-700 ring-1 ring-sky-200">{{ $item->inventory->department->department_name }}</span>
                                                    @endif
                                                    @if (!$isWorker && $item->is_sale_return == 1)
                                                        <span class="rounded bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700 ring-1 ring-rose-200">Sales Return</span>
                                                    @endif
                                                </div>
                                                <h3 class="mt-1.5 text-base font-bold text-erp-ink">{{ $item->item_name }}</h3>
                                                @if ($item->note != '')
                                                    <p class="mt-1 text-sm text-erp-mute"><span class="font-semibold text-erp-text">Comments:</span> {{ $item->note }}</p>
                                                @endif
                                            </div>

                                            {{-- Item actions (purane qaide) --}}
                                            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                                                @if (!$isWorker && $statusId == 1 && $item->mode == '' && in_array(session('company_id'), [104, 7]))
                                                    @php $itemTone = $statusTone($item->itemstatus?->order_status_name); @endphp
                                                    <span class="rounded-md px-2.5 py-1 text-xs font-bold ring-1 {{ $itemTone['badge'] }}">{{ $item->itemstatus?->order_status_name }}</span>
                                                    <a href="{{ url('sent-to-workshop') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}"
                                                        class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-amber-500 px-3 text-xs font-bold text-white transition hover:bg-amber-600">
                                                        <i class="icofont icofont-industries-alt-4"></i> Sent to Workshop</a>
                                                @endif
                                                @if ($isWorker)
                                                    @if ($item->status == 1)
                                                        <a href="{{ url('change-item-status') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}/2"
                                                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-amber-500 px-3 text-xs font-bold text-white transition hover:bg-amber-600">
                                                            <i class="icofont icofont-spinner-alt-3"></i> Processing</a>
                                                    @elseif($item->status == 2)
                                                        <a href="{{ url('change-item-status') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}/3"
                                                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-sky-600 px-3 text-xs font-bold text-white transition hover:bg-sky-700">
                                                            <i class="icofont icofont-check-circled"></i> Ready</a>
                                                    @elseif($item->status == 3)
                                                        <a href="{{ url('change-item-status') }}/{{ $order->id }}/{{ $item->receipt_detail_id }}/6"
                                                            class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-erp px-3 text-xs font-bold text-white transition hover:bg-erp-dark">
                                                            <i class="icofont icofont-vehicle-delivery-van"></i> Dispatch</a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-3 gap-3 rounded-lg bg-slate-50 px-4 py-3 text-sm">
                                            <div>
                                                <div class="text-xs font-semibold uppercase tracking-wide text-erp-mute">Price</div>
                                                <div class="mt-1 font-bold text-erp-ink">{{ $money($price) }}</div>
                                            </div>
                                            <div>
                                                <div class="text-xs font-semibold uppercase tracking-wide text-erp-mute">Qty</div>
                                                <div class="mt-1 font-bold text-erp-ink">{{ number_format((float) $item->total_qty, floor((float) $item->total_qty) == (float) $item->total_qty ? 0 : 2) }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs font-semibold uppercase tracking-wide text-erp-mute">Total</div>
                                                <div class="mt-1 font-black text-erp-dark">{{ $money($lineTotal) }}</div>
                                            </div>
                                        </div>

                                        @if (!$isWorker && $item->statusLogs && $item->statusLogs->isNotEmpty())
                                            <details class="group mt-3">
                                                <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 text-xs font-bold text-erp-dark hover:underline">
                                                    <i class="icofont icofont-history"></i> Item logs ({{ $item->statusLogs->count() }})
                                                    <i class="icofont icofont-simple-down transition group-open:rotate-180"></i>
                                                </summary>
                                                <ol class="mt-2 space-y-1.5 border-l-2 border-erp-line pl-4 text-sm">
                                                    @foreach ($item->statusLogs as $log)
                                                        <li class="text-erp-text">
                                                            <span class="font-semibold text-erp-ink">{{ $log->status?->order_status_name }}</span>
                                                            <span class="text-erp-mute">· {{ date('d M Y', strtotime($log->date)) . ' ' . date('h:i A', strtotime($log->time)) }}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </details>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-10 text-center text-sm text-erp-mute">No items in this order.</div>
                        @endforelse
                    </div>
                </section>

                @if ($isCredit && $roleId == 2)
                    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                        <div class="border-b border-erp-line px-5 py-4">
                            <h2 class="text-base font-bold text-erp-ink">Payment Details</h2>
                            <p class="mt-1 text-xs text-erp-mute">Highlighted rows are received payments.</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="ledgerTable" class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                                    <tr>
                                        <th class="whitespace-nowrap px-4 py-3 text-left font-bold">Date Time</th>
                                        <th class="px-4 py-3 text-left font-bold">Mode / Narration</th>
                                        <th class="px-4 py-3 text-right font-bold">Total Amount</th>
                                        <th class="px-4 py-3 text-right font-bold">Debit</th>
                                        <th class="px-4 py-3 text-right font-bold">Credit</th>
                                        <th class="px-4 py-3 text-right font-bold">Total Balance</th>
                                        <th class="px-4 py-3 text-center font-bold">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @if ($ledgerDetails)
                                        @php
                                            $receipt_balance = 0;
                                            $total_amount = 0;
                                            $credit = 0;
                                            $debit = 0;
                                        @endphp
                                        @foreach ($ledgerDetails as $value)
                                            <?php
                                            $creditGreater = false;
                                            if ($credit > $total_amount) {
                                                $creditGreater = true;
                                            }
                                            if ($value->total_amount > 0) {
                                                $total_amount += $value->total_amount;
                                            } else {
                                                $total_amount += $value->debit;
                                            }
                                            $credit += $value->credit;
                                            $debit += $value->debit;
                                            $receipt_balance = Custom_Helper::getLedgerCal($value, $receipt_balance, $total_amount, $credit, $debit, $creditGreater);
                                            ?>
                                            <tr class="{{ $value->received == 1 ? 'bg-orange-50' : '' }}">
                                                <td class="whitespace-nowrap px-4 py-3 text-erp-text">{{ date('d M Y', strtotime($value->created_at)) . ' ' . date('h:i A', strtotime($value->created_at)) }}</td>
                                                <td class="px-4 py-3 text-erp-text">{{ $value->payment_mode . ' / ' . $value->narration }}</td>
                                                <td class="px-4 py-3 text-right">{{ number_format($value->total_amount, 2) }}</td>
                                                <td class="px-4 py-3 text-right {{ (float) $value->credit > 0 ? 'font-semibold text-emerald-700' : '' }}">{{ number_format($value->credit, 2) }}</td>
                                                <td class="px-4 py-3 text-right {{ (float) $value->debit > 0 ? 'font-semibold text-rose-700' : '' }}">{{ number_format($value->debit, 2) }}</td>
                                                <td class="px-4 py-3 text-right font-bold {{ $total_amount > $credit ? 'text-rose-700' : 'text-emerald-700' }}">{{ number_format($receipt_balance, 2) }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    @if ($value->receipt_no != 0)
                                                        <a href="{{ url('print', $value->receipt_no) }}" title="Print"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-emerald-700 hover:bg-emerald-50"><i class="icofont icofont-printer"></i></a>
                                                    @else
                                                        <a href="javascript:void(0)" title="Edit"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sky-700 hover:bg-sky-50"
                                                            onclick="showEditManualPayment('{{ $value->cust_id }}','{{ $value->cust_account_id }}','{{ $value->debit }}','{{ $value->credit }}','{{ $value->narration }}')"><i class="icofont icofont-pen"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="7" class="px-4 py-8 text-center text-erp-mute">No ledger entries.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            </div>

            {{-- Right: summary, info, delivery, timeline --}}
            <div class="space-y-6">
                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">Order Summary</h2>
                    </div>
                    <dl class="space-y-3 px-5 py-4 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-erp-mute">Sub Total</dt><dd class="font-semibold text-erp-ink">{{ $money($order->actual_amount) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-erp-mute">Discount</dt><dd class="font-semibold text-rose-700">- {{ $money($order->orderAccountSub?->discount_amount) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-erp-mute">Shipping Charge</dt><dd class="font-semibold text-erp-ink">{{ $money($order->orderAccountSub?->delivery_charges_amount) }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-erp-mute">Tax {{ $taxName != '' ? '(' . $taxName . ')' : '' }}</dt><dd class="font-semibold text-erp-ink">{{ $money($taxAmount) }}</dd></div>
                        @if ($isCredit)
                            <div class="flex justify-between gap-4 border-t border-dashed border-erp-line pt-3"><dt class="text-erp-mute">Order Total</dt><dd class="font-semibold text-erp-ink">{{ $money($order->total_amount) }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-erp-mute">Amount Received</dt><dd class="font-semibold text-emerald-700">- {{ $money(!empty($order->orderAccountSub) ? $receivedTotal : 0) }}</dd></div>
                        @endif
                    </dl>
                    <div class="flex items-center justify-between rounded-b-lg border-t border-erp-line bg-slate-50 px-5 py-4">
                        <span class="text-sm font-bold uppercase tracking-wide text-erp-text">{{ $isCredit ? 'Balance' : 'Total' }}</span>
                        <span class="text-xl font-black {{ $isCredit && $grandValue > 0 ? 'text-rose-700' : 'text-erp-dark' }}">{{ $money($grandValue) }}</span>
                    </div>
                </section>

                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">Order Details</h2>
                        <span class="text-sm font-bold text-erp-mute">#{{ $order->id }}</span>
                    </div>
                    @php
                        $infoRows = [
                            'Branch' => $order->branchrelation?->branch_name,
                            'Receipt Number' => $order->receipt_no,
                            'Service Type' => $order->service?->serviceType?->name ?? 'N/A',
                            'Customer' => $order->customer?->name ?? 'Walk-in',
                            'Contact Number' => $order->customer?->mobile,
                            'Membership Number' => $order->customer?->membership_card_no,
                            'Payment Method' => $order->payment?->payment_mode,
                            'Sales Person' => !empty($provider) ? $provider->serviceprovider->provider_name : null,
                        ];
                        if (!empty($wallet)) {
                            $infoRows['Wallet'] = $wallet->serviceprovider->provider_name;
                        }
                        $infoRows['Land Mark'] = !empty($order->address) ? $order->address->landmark : null;
                        $infoRows['Address'] = !empty($order->address) ? $order->address->address : null;
                    @endphp
                    <dl class="divide-y divide-slate-100 text-sm">
                        @foreach ($infoRows as $label => $value)
                            <div class="flex justify-between gap-4 px-5 py-2.5">
                                <dt class="shrink-0 text-erp-mute">{{ $label }}</dt>
                                <dd class="text-right font-semibold text-erp-ink">{{ $value != '' ? $value : '-' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">Delivery Detail</h2>
                    </div>
                    <dl class="divide-y divide-slate-100 text-sm">
                        <div class="flex justify-between gap-4 px-5 py-2.5">
                            <dt class="shrink-0 text-erp-mute">Area Name</dt>
                            <dd class="text-right font-semibold text-erp-ink">{{ $order->delivery_area_name != '' ? $order->delivery_area_name : '-' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 px-5 py-2.5">
                            <dt class="shrink-0 text-erp-mute">Instructions</dt>
                            <dd class="text-right font-semibold text-erp-ink">{{ $order->delivery_instructions != '' ? $order->delivery_instructions : '-' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h2 class="text-base font-bold text-erp-ink">Status History</h2>
                    </div>
                    <div class="px-5 py-4">
                        @if ($order->statusLogs->isEmpty())
                            <p class="py-4 text-center text-sm text-erp-mute">No logs</p>
                        @else
                            <ol class="relative space-y-5 border-l-2 border-erp-line pl-6">
                                @foreach ($order->statusLogs as $log)
                                    @php $logName = $log->status?->order_status_name; @endphp
                                    <li class="relative">
                                        <span class="absolute -left-[31px] top-1 h-3.5 w-3.5 rounded-full ring-4 ring-white {{ $statusTone($logName)['dot'] }}"></span>
                                        <div class="flex flex-wrap items-baseline justify-between gap-x-3">
                                            <span class="text-sm font-bold text-erp-ink">{{ $statusLabel($logName) }}</span>
                                            <span class="text-xs text-erp-mute">{{ date('d M Y', strtotime($log->date)) . ' ' . date('h:i A', strtotime($log->time)) }}</span>
                                        </div>
                                        @if (!empty($log->user))
                                            <div class="mt-0.5 text-xs font-semibold text-erp-dark">{{ $log->user->fullname }}</div>
                                        @endif
                                        @if ($logName == 'Order Picked Up')
                                            <div class="mt-1 text-xs text-erp-text">
                                                {{ (!empty($log->branch) ? $log->branch->branch_name . ' | ' : '') . $log->name . ' | ' . $log->mobile }}
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- Image preview --}}
    <div id="myModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/85 p-4" onclick="closeImage(event)">
        <button type="button" id="myModalClose" class="absolute right-5 top-4 text-4xl font-bold leading-none text-white/80 hover:text-white" onclick="closeImage()">&times;</button>
        <div class="flex max-h-full w-full max-w-3xl flex-col items-center">
            <img id="img01" class="max-h-[75vh] max-w-full rounded-lg bg-white object-contain shadow-menu" alt="">
            <div id="caption" class="mt-4 text-center text-base text-slate-200"></div>
        </div>
    </div>
@endsection

@section('scriptcode_three')
    <script>
        function showImage(id) {
            var img = document.getElementById('myImg' + id);
            var modal = document.getElementById('myModal');
            document.getElementById('img01').src = img.src;
            var caption = document.getElementById('caption');
            caption.textContent = '';
            if (img.alt) {
                var b = document.createElement('b');
                b.textContent = 'Comments: ';
                caption.appendChild(b);
                caption.appendChild(document.createTextNode(img.alt));
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeImage(e) {
            if (e && e.target.id !== 'myModal') return;
            var modal = document.getElementById('myModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeImage();
        });
    </script>
@endsection
