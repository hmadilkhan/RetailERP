@extends('layouts.master-tailwind')

@section('title', 'Stock Adjustment')
@section('page_title', 'Stock Adjustment')
@section('page_subtitle', 'Add found stock or write off damaged, spoiled, and wasted stock for a product at a branch.')

@section('content')
    @php
        $canSelectBranch = session('roleId') == 17 || session('roleId') == 2;
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
    @endphp

    <div class="space-y-6">
        <div id="adjustmentAlert" class="hidden rounded-lg border px-4 py-3 text-sm font-semibold"></div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">New Adjustment</h2>
                        <p class="mt-1 text-sm text-erp-mute">Select a product, enter the quantity, and give a reason.</p>
                    </div>
                    <a href="{{ url('/view-adjustments') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">View Adjustments</a>
                </div>

                <div class="grid gap-4 p-5 sm:grid-cols-2">
                    @if ($canSelectBranch)
                        <label class="block">
                            <span class="{{ $labelClass }}">Branch</span>
                            <select id="branch" name="branch" data-placeholder="Select Branch" class="v2-select2 {{ $inputClass }}">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </label>
                    @endif

                    <label class="block {{ $canSelectBranch ? '' : 'sm:col-span-2' }}">
                        <span class="{{ $labelClass }}">Product</span>
                        <select id="product" class="v2-select2 {{ $inputClass }}"></select>
                        <input type="hidden" id="hiddenamount" value="0">
                    </label>

                    <div class="block">
                        <label for="qty" class="{{ $labelClass }}">Adjustment Qty</label>
                        <div class="mt-2 flex gap-2">
                            <input type="number" step="any" name="qty" id="qty" class="h-10 min-w-0 flex-1 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="e.g. 5 or -2">
                            <select id="qtyUom" class="hidden h-10 w-36 shrink-0 rounded-lg border-erp-line bg-slate-50 text-sm font-semibold text-erp-text shadow-sm focus:border-erp focus:ring-erp" aria-label="Unit of measure"></select>
                        </div>
                        <span id="qtyConversion" class="mt-1 block text-xs text-erp-mute">Positive adds stock, negative removes it.</span>
                    </div>

                    <label class="block">
                        <span id="costLabel" class="{{ $labelClass }}">Cost Price</span>
                        <input type="number" step="any" name="amount" id="amount" class="{{ $inputClass }}" placeholder="0.00">
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="{{ $labelClass }}">Reason</span>
                        <textarea name="reason" id="reason" rows="3" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="e.g. Damaged in transit, found during stock count"></textarea>
                    </label>
                </div>

                <div class="flex justify-end border-t border-erp-line px-5 py-4">
                    <button type="button" id="btngrn" class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:cursor-not-allowed disabled:opacity-60">
                        Add Stock
                    </button>
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Stock In Hand</div>
                    <div id="stockText" class="mt-4 text-3xl font-black text-erp-ink">—</div>
                    <div id="stockBreakdown" class="mt-1 text-sm font-semibold text-erp-text"></div>
                    <p id="stockHint" class="mt-2 text-sm text-erp-mute">Select a product to see its current stock.</p>
                    <input type="hidden" id="stock" value="">
                </section>

                <section class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-erp-ink">How it works</h3>
                    <ul class="mt-3 space-y-2 text-sm text-erp-text">
                        <li class="flex gap-2"><span class="font-black text-emerald-600">+</span><span>Positive quantity adds stock as a new GRN at the cost price.</span></li>
                        <li class="flex gap-2"><span class="font-black text-rose-600">−</span><span>Negative quantity removes damaged, spoiled, or wasted stock from the GRN lots you select.</span></li>
                    </ul>
                </section>
            </aside>
        </div>

        <section id="dvgrn" class="hidden rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Select GRN Lots</h2>
                <p class="mt-1 text-sm text-erp-mute">Choose the lots to remove stock from. Lots are used in the order shown.</p>
            </div>
            <div class="overflow-x-auto">
                <table id="tblgrn" class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="w-12 px-5 py-3 text-left">
                                <input type="checkbox" id="checkAllGrn" class="rounded border-erp-line text-erp focus:ring-erp">
                            </th>
                            <th class="px-5 py-3 text-left font-bold">GRN No.</th>
                            <th class="px-5 py-3 text-left font-bold">Product</th>
                            <th class="px-5 py-3 text-right font-bold">Balance</th>
                            <th class="px-5 py-3 text-left font-bold">Date</th>
                            <th class="px-5 py-3 text-left font-bold">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div class="flex justify-end border-t border-erp-line px-5 py-4">
                <button type="button" id="btnsubmit" class="h-10 rounded-lg border border-rose-600 bg-rose-600 px-5 text-sm font-bold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60">
                    Remove Stock
                </button>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function ($) {
            const canSelectBranch = @json($canSelectBranch);
            let selectedStockIds = [];

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, char => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                }[char]));
            }

            function notify(type, text) {
                const styles = {
                    error: 'border-rose-200 bg-rose-50 text-rose-700',
                    success: 'border-emerald-200 bg-emerald-50 text-emerald-700'
                };
                $('#adjustmentAlert')
                    .attr('class', 'rounded-lg border px-4 py-3 text-sm font-semibold ' + styles[type])
                    .text(text);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function clearNotify() {
                $('#adjustmentAlert').attr('class', 'hidden');
            }

            function branchMissing() {
                return canSelectBranch && !$('#branch').val();
            }

            $('#product').select2({
                ajax: {
                    url: '{{ route('search-inventory') }}',
                    dataType: 'json',
                    processResults: function (data) {
                        return {
                            results: $.map(data.items, function (item) {
                                return { text: item.product_name + ' | ' + item.item_code, id: item.id };
                            })
                        };
                    }
                },
                placeholder: 'Search for a product',
                minimumInputLength: 1,
                dropdownCssClass: 'v2-select2-dropdown',
                width: '100%'
            });

            function resetGrns() {
                selectedStockIds = [];
                $('#checkAllGrn').prop('checked', false);
                $('#tblgrn tbody').empty();
            }

            function showMode(isRemoval) {
                $('#btngrn').toggleClass('hidden', isRemoval);
                $('#dvgrn').toggleClass('hidden', !isRemoval);
            }

            // UOM conversion: stock primary me hota hai; factor = 1 primary me is UOM ki kitni qty
            let uomLevels = [];
            const trimQty = value => String(Number(Number(value || 0).toFixed(4)));
            const hasUomLevel = (conversion, fromUom, toUom) => Number(conversion || 0) > 1 && !!toUom && fromUom !== toUom;

            function buildUomLevels(row) {
                uomLevels = [{ name: row.uom_name || 'Primary', factor: 1 }];
                if (hasUomLevel(row.weight_qty, row.uom_name, row.cuom_name)) {
                    uomLevels.push({ name: row.cuom_name, factor: Number(row.weight_qty) });
                    if (hasUomLevel(row.weight_qty2, row.cuom_name, row.cuom2_name)) {
                        uomLevels.push({ name: row.cuom2_name, factor: Number(row.weight_qty) * Number(row.weight_qty2) });
                    }
                }

                $('#qtyUom').html(uomLevels.map((level, index) => '<option value="' + index + '">' + escapeHtml(level.name) + '</option>').join(''))
                    .toggleClass('hidden', uomLevels.length < 2);
            }

            function selectedUom() {
                return uomLevels[Number($('#qtyUom').val() || 0)] || { name: '', factor: 1 };
            }

            // User ne jo qty chuni hui UOM me daali, wo primary me
            function primaryQty() {
                const raw = parseFloat($('#qty').val());
                return isNaN(raw) ? NaN : raw / selectedUom().factor;
            }

            function stockBreakdown(stock) {
                if (uomLevels.length < 2) {
                    return '';
                }
                return uomLevels.map(level => trimQty(stock * level.factor) + ' ' + level.name).join(' / ');
            }

            function updateConversion() {
                const uom = selectedUom();
                const primary = uomLevels[0] ? uomLevels[0].name : '';
                const qty = primaryQty();

                $('#costLabel').text(uomLevels.length > 1 ? 'Cost per ' + uom.name : 'Cost Price');

                if (uom.factor === 1) {
                    $('#qtyConversion').text('Positive adds stock, negative removes it.');
                    return;
                }
                const rate = '1 ' + primary + ' = ' + trimQty(uom.factor) + ' ' + uom.name;
                if (isNaN(qty)) {
                    $('#qtyConversion').text(rate);
                    return;
                }
                $('#qtyConversion').html('<span class="font-bold text-erp-ink">= ' + escapeHtml(trimQty(qty) + ' ' + primary) + '</span> (' + escapeHtml(rate) + ')');
            }

            // Cost field chuni hui UOM ka; hiddenamount hamesha primary ka
            function fillCostForUom() {
                if (parseFloat($('#qty').val()) < 0) {
                    return;
                }
                const primaryCost = Number($('#hiddenamount').val() || 0);
                $('#amount').val(primaryCost ? trimQty(primaryCost / selectedUom().factor) : '');
            }

            function getstock(id) {
                if (!id) {
                    return;
                }
                if (branchMissing()) {
                    notify('error', 'Please select a branch first.');
                    $('#product').val(null).trigger('change.select2');
                    return;
                }

                $.get('{{ url('/getstock_value') }}', { productid: id, branch: $('#branch').val() }, function (resp) {
                    const row = resp && resp[0] ? resp[0] : {};
                    const stock = Number(row.stock || 0);
                    const previousUom = $('#qtyUom').val();
                    buildUomLevels(row);
                    if (previousUom && uomLevels[Number(previousUom)]) {
                        $('#qtyUom').val(previousUom);
                    }
                    $('#hiddenamount').val(row.cost_price || 0);
                    fillCostForUom();
                    updateConversion();
                    $('#stock').val(stock);
                    $('#stockText').text(trimQty(stock) + ' ' + (row.uom_name || ''));
                    $('#stockBreakdown').text(stockBreakdown(stock));
                    $('#stockHint').text(stock > 0 ? 'Active balance across open GRN lots.' : 'No active stock for this product.');
                });
            }

            function getgrns(id) {
                $.get('{{ url('/getgrns') }}', { productid: id, branch: $('#branch').val() }, function (result) {
                    resetGrns();
                    if (!result.length) {
                        $('#tblgrn tbody').html('<tr><td colspan="6" class="px-5 py-10 text-center text-sm text-erp-mute">No GRN lots with stock in this branch.</td></tr>');
                        return;
                    }
                    result.forEach(function (row) {
                        $('#tblgrn tbody').append(`
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3"><input type="checkbox" value="${escapeHtml(row.stock_id)}" class="grn-check rounded border-erp-line text-erp focus:ring-erp"></td>
                                <td class="px-5 py-3 font-bold text-erp-ink">${escapeHtml(row.grn_id)}</td>
                                <td class="px-5 py-3 text-erp-text">${escapeHtml(row.product_name)}</td>
                                <td class="px-5 py-3 text-right font-black text-erp-ink">${escapeHtml(row.balance)}</td>
                                <td class="px-5 py-3 text-erp-text">${escapeHtml(row.date)}</td>
                                <td class="px-5 py-3 text-erp-mute">${escapeHtml(row.time)}</td>
                            </tr>
                        `);
                    });
                });
            }

            function qtychanger() {
                const raw = $('#qty').val();
                if (raw === '') {
                    return;
                }
                const qty = parseFloat(raw);
                clearNotify();

                if (qty === 0) {
                    notify('error', 'Please enter a valid quantity, either positive or negative.');
                    $('#qty').val('');
                    return;
                }
                if (!$('#product').val()) {
                    notify('error', 'Please select a product first.');
                    return;
                }

                if (qty < 0) {
                    $('#amount').val('0.00');
                    showMode(true);
                    getgrns($('#product').val());
                } else {
                    fillCostForUom();
                    resetGrns();
                    showMode(false);
                }
            }

            function validateCommon() {
                if (branchMissing()) {
                    notify('error', 'Please select a branch.');
                    return false;
                }
                if (!$('#product').val()) {
                    notify('error', 'Please select a product first.');
                    return false;
                }
                if (!$('#reason').val().trim()) {
                    notify('error', 'Please enter a reason first.');
                    return false;
                }
                return true;
            }

            function onSaved(resp) {
                if (resp == 1) {
                    notify('success', 'Stock adjusted successfully.');
                    setTimeout(function () {
                        window.location = '{{ url('/stockadjustment') }}';
                    }, 900);
                } else {
                    notify('error', 'Stock could not be adjusted. Please try again.');
                    $('#btngrn, #btnsubmit').prop('disabled', false);
                }
            }

            function onFailed(xhr) {
                const message = xhr && xhr.responseJSON && xhr.responseJSON.message;
                notify('error', message || 'Something went wrong. Please try again.');
                $('#btngrn, #btnsubmit').prop('disabled', false);
            }

            // Positive qty: new GRN
            function creategrn() {
                clearNotify();
                if (!validateCommon()) {
                    return;
                }
                const qty = primaryQty();
                if (!(qty > 0)) {
                    notify('error', 'Please enter a valid quantity.');
                    return;
                }
                // Cost bhi primary UOM ka bhejo
                const primaryCost = Number($('#amount').val() || 0) * selectedUom().factor;

                $('#btngrn').prop('disabled', true);
                $.post('{{ url('/creategrnadjustmnet') }}', {
                    _token: '{{ csrf_token() }}',
                    productid: $('#product').val(),
                    qty: Number(qty.toFixed(6)),
                    cp: $('#hiddenamount').val(),
                    amount: Number(primaryCost.toFixed(4)),
                    narration: $('#reason').val(),
                    branch: $('#branch').val()
                }).done(onSaved).fail(onFailed);
            }

            // Negative qty: deduct from selected GRN lots
            function adjuststock() {
                clearNotify();
                if (!validateCommon()) {
                    return;
                }
                if (!selectedStockIds.length) {
                    notify('error', 'Please select at least one GRN lot.');
                    return;
                }

                $('#btnsubmit').prop('disabled', true);
                $.post('{{ url('/updatestockadjustment') }}', {
                    _token: '{{ csrf_token() }}',
                    stockid: selectedStockIds,
                    qty: Number(primaryQty().toFixed(6)),
                    amount: $('#amount').val(),
                    narration: $('#reason').val(),
                    branch: $('#branch').val()
                }).done(onSaved).fail(onFailed);
            }

            $('#product').on('change', function () {
                getstock($(this).val());
                if (parseFloat($('#qty').val()) < 0) {
                    getgrns($(this).val());
                }
            });
            $('#branch').on('change', function () {
                clearNotify();
                getstock($('#product').val());
                if ($('#product').val() && parseFloat($('#qty').val()) < 0) {
                    getgrns($('#product').val());
                }
            });
            $('#qty').on('change', qtychanger);
            $('#qty').on('input', function () {
                updateConversion();
                const qty = parseFloat($(this).val());
                const removing = qty < 0;
                // Sign badla (plus <-> minus) to foran sahi button/section dikhao
                if (!isNaN(qty) && qty !== 0 && removing !== !$('#dvgrn').hasClass('hidden') && $('#product').val()) {
                    qtychanger();
                }
            });
            $('#qtyUom').on('change', function () {
                updateConversion();
                fillCostForUom();
            });
            $('#btngrn').on('click', creategrn);
            $('#btnsubmit').on('click', adjuststock);

            $('#checkAllGrn').on('change', function () {
                const checked = $(this).is(':checked');
                $('.grn-check').prop('checked', checked);
                selectedStockIds = checked ? $('.grn-check').map(function () { return this.value; }).get() : [];
            });

            $('#tblgrn').on('change', '.grn-check', function () {
                selectedStockIds = $('.grn-check:checked').map(function () { return this.value; }).get();
                $('#checkAllGrn').prop('checked', selectedStockIds.length === $('.grn-check').length);
            });
        })(jQuery);
    </script>
@endpush
