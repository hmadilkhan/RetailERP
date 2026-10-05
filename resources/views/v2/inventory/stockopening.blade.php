@extends('layouts.master-tailwind')

@section('title', 'Stock Opening')
@section('page_title', 'Stock Opening')
@section('page_subtitle', 'Set the opening stock balance, cost, and unit of measure for a product at a branch.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Upload Inventory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Bulk import stock opening records from a CSV file.{{ session('message') }}</p>
                </div>
                <a href="{{ url('getcsv') }}" id="downloadsample" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Download Sample</a>
            </div>
            <form method="POST" action="{{ url('uploadStockOpening') }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3 p-5">
                @csrf
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Select File</span>
                    <input type="file" name="file" id="vdimg" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                    @if ($errors->has('file'))
                        <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                    @endif
                </label>
                <button type="submit" name="submit" value="Import" class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Import</button>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Inventory Stock Opening</h2>
                <p class="mt-1 text-sm text-erp-mute">Record the opening quantity and cost for a product at a branch.</p>
            </div>

            <form method="POST" enctype="multipart/form-data" action="{{ url('insert-stock-opening') }}" class="p-5">
                @csrf

                @if (Session::get('status') || $status)
                    <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">{{ Session::get('status') }}{{ $status }}</div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Branch</span>
                        <select class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" id="branch" name="branch">
                            <option value="">Select Branch</option>
                            @if($branches)
                                @foreach($branches as $val)
                                    <option {{ old('branch') == $val->branch_id ? 'selected' : '' }} value="{{ $val->branch_id }}">{{ $val->branch_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('branch'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Product</span>
                        <select class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" id="product" name="product">
                            <option value="">Select Product</option>
                            @if($product)
                                @foreach($product as $val)
                                    <option {{ old('product') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->department_name." | ".$val->item_code." | ".$val->product_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('product'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Unit Of Measure</span>
                        <select class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" id="uom" name="uom">
                            <option value="">Select Unit Of Measure</option>
                            @if($uom)
                                @foreach($uom as $val)
                                    <option {{ old('uom') == $val->uom_id ? 'selected' : '' }} value="{{ $val->uom_id }}">{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('uom'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Stock Qty</span>
                        <div class="mt-2 flex gap-2">
                            <input type="text" name="qty" id="qty" class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            {{-- Packing UOM wale product pe: Carton me qty, submit pe primary me convert (name nahi, post nahi hota) --}}
                            <select id="qtyUnit" class="hidden h-10 w-32 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></select>
                        </div>
                        <span id="packHint" class="mt-1 block text-xs font-semibold text-erp-text"></span>
                        @if ($errors->has('qty'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <label class="block">
                        <span id="cpLabel" class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Item Cost Price</span>
                        <input type="text" name="cp" id="cp" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        @if ($errors->has('cp'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <input type="hidden" name="rp" id="rp" value="0">
                    <input type="hidden" name="wp" id="wp" value="0">
                    <input type="hidden" name="dp" id="dp" value="0">

                    <div class="flex items-end">
                        <button type="submit" class="h-10 w-full rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark sm:w-auto">Submit</button>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        // Packing UOM: stock primary (Pcs) me save hota hai; Carton chuna to qty x pack_qty, cost / pack_qty
        let pack = null;
        const qtyUnit = document.getElementById('qtyUnit');
        const trimQty = value => String(Number(Number(value || 0).toFixed(4)));
        const isPackSelected = () => pack && qtyUnit.value === 'pack';

        function setPack(row) {
            pack = row && Number(row.pack_qty) > 1 && row.pack_uom_name
                ? { qty: Number(row.pack_qty), name: row.pack_uom_name, primary: row.uom_name || 'Unit' }
                : null;
            qtyUnit.innerHTML = pack
                ? '<option value="primary"></option><option value="pack"></option>'
                : '';
            if (pack) {
                qtyUnit.options[0].textContent = pack.primary;
                qtyUnit.options[1].textContent = pack.name;
            }
            qtyUnit.classList.toggle('hidden', !pack);
            updatePackHint();
        }

        function updatePackHint() {
            const hint = document.getElementById('packHint');
            document.getElementById('cpLabel').textContent = isPackSelected() ? 'Cost per ' + pack.name : 'Item Cost Price';
            if (!pack) {
                hint.textContent = '';
                return;
            }
            const rate = '1 ' + pack.name + ' = ' + trimQty(pack.qty) + ' ' + pack.primary;
            const qty = parseFloat(document.getElementById('qty').value);
            const cp = parseFloat(document.getElementById('cp').value);
            if (!isPackSelected() || isNaN(qty)) {
                hint.textContent = rate;
                return;
            }
            hint.textContent = '= ' + trimQty(qty * pack.qty) + ' ' + pack.primary
                + (isNaN(cp) ? '' : ' @ ' + trimQty(cp / pack.qty)) + ' (' + rate + ')';
        }

        qtyUnit.addEventListener('change', updatePackHint);
        document.getElementById('qty').addEventListener('input', updatePackHint);
        document.getElementById('cp').addEventListener('input', updatePackHint);

        document.getElementById('qty').closest('form').addEventListener('submit', function () {
            if (!isPackSelected()) {
                return;
            }
            const qtyEl = document.getElementById('qty');
            const cpEl = document.getElementById('cp');
            const qty = parseFloat(qtyEl.value);
            const cp = parseFloat(cpEl.value);
            if (!isNaN(qty)) {
                qtyEl.value = trimQty(qty * pack.qty);
            }
            if (!isNaN(cp)) {
                cpEl.value = trimQty(cp / pack.qty);
            }
        });

        document.getElementById('product').addEventListener('change', function () {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', this.value);

            fetch('{{ url('/get-uom-id') }}', {
                method: 'POST',
                body: formData,
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp && resp[0] && resp[0].uom_id) {
                        document.getElementById('uom').value = resp[0].uom_id;
                    }
                    setPack(resp && resp[0]);
                });
        });
    </script>
@endpush
