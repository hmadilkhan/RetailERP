@extends('layouts.master-tailwind')

@section('title', 'POS Products')
@section('page_title', 'POS Products')
@section('page_subtitle', 'Create POS sale items from finished goods, set pricing, and manage product variations.')

@section('content')
    @php
        $variationsByProduct = collect($inventoryVariations)->groupBy('product_id');
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Active POS Products</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(collect($details)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Currently sellable on POS</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Variation Categories</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(collect($totalvariation)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available to attach to products</p>
            </div>
            <a href="#createProductCard" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create POS Product</div>
                    <p class="mt-2 text-sm text-white/75">Jump to the create form</p>
                </div>
            </a>
        </section>

        <section id="createProductCard" class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Create POS Product</h2>
                    <p class="mt-1 text-sm text-erp-mute">Pick a finished good, set the usage quantity and pricing.</p>
                </div>
                <button type="button" id="toggleCreateCard" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Collapse</button>
            </div>

            <form method="POST" id="upload_form" enctype="multipart/form-data" class="space-y-4 p-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="block">
                        <span class="{{ $labelClass }}">Select Product</span>
                        <select name="finishgood" id="finishgood" class="{{ $inputClass }}">
                            <option value="">Select Product</option>
                            @if($getfinishgood)
                                @foreach($getfinishgood as $value)
                                    <option value="{{ $value->id }}">{{ $value->product_name }} | {{ $value->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('finishgood'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="{{ $labelClass }}">Item Code</span>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" class="{{ $inputClass }}">
                        @if ($errors->has('code'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="{{ $labelClass }}">Select Unit Of Measure</span>
                        <select name="uom" id="uom" class="{{ $inputClass }}">
                            <option value="">Select Unit Of Measure</option>
                            @if($uoms)
                                @foreach($uoms as $uom)
                                    <option value="{{ $uom->uom_id }}">{{ $uom->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <label class="block">
                        <span class="{{ $labelClass }}">Item Name</span>
                        <input type="text" name="itemname" id="itemname" value="{{ old('itemname') }}" class="{{ $inputClass }}">
                        @if ($errors->has('itemname'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @else
                            <span class="mt-1 block text-xs text-erp-mute">Enter POS product name</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="{{ $labelClass }}">Qty. Usage <button type="button" id="btn_help" class="ml-1 text-erp-dark underline">help</button></span>
                        <input type="text" name="qty" id="qty" value="{{ old('qty') }}" class="{{ $inputClass }}">
                        @if ($errors->has('qty'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @else
                            <span class="mt-1 block text-xs text-erp-mute">Usage qty according to unit of measure</span>
                        @endif
                    </label>

                    <label class="block">
                        <span class="{{ $labelClass }}">Product Image</span>
                        <div class="mt-2 flex items-center gap-3">
                            <img id="productimages" src="{{ asset('storage/images/placeholder.jpg') }}" class="h-16 w-16 rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" name="productimage" id="productimage" accept="image/*" class="block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                        </div>
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <label class="block">
                        <span class="{{ $labelClass }}">Actual Price <span class="text-rose-500">*</span></span>
                        <input type="number" min="0" name="ap" id="ap" value="{{ old('ap') }}" placeholder="0" class="{{ $inputClass }}">
                        @if ($errors->has('ap'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tax Rate</span>
                        <input type="number" step=".01" name="taxrate" id="taxrate" value="{{ old('taxrate') }}" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tax Amount</span>
                        <input type="number" min="0" name="taxamount" id="taxamount" value="{{ old('taxamount') }}" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Retail Price</span>
                        <input type="number" min="0" name="rp" id="rp" value="0" class="{{ $inputClass }}">
                        @if ($errors->has('rp'))
                            <span class="mt-1 block text-xs font-semibold text-rose-600">Required field can not be blank.</span>
                        @endif
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Wholesale Price</span>
                        <input type="number" min="0" name="wp" id="wp" value="0" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Discount Price</span>
                        <input type="number" min="0" name="dp" id="dp" value="0" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Online Price</span>
                        <input type="number" min="0" name="op" id="op" value="0" class="{{ $inputClass }}">
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" id="btnsubmit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Product</button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">POS Products List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Search, edit, attach variations, or deactivate POS products.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input type="search" id="posSearch" placeholder="Search code, name, or product..." class="h-10 w-64 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <label class="inline-flex h-10 cursor-pointer items-center gap-2 rounded-lg border border-erp-line px-3 text-sm font-bold text-erp-text">
                        <input type="checkbox" id="chkactive" class="rounded border-erp-line text-erp focus:ring-erp">
                        Show Inactive
                    </label>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="tblposproducts" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Image</th>
                            <th class="px-5 py-3">Item Code | Name</th>
                            <th class="px-5 py-3">UOM</th>
                            <th class="px-5 py-3">Ref. Product</th>
                            <th class="px-5 py-3">Variations</th>
                            <th class="px-5 py-3">Retail Price</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="posProductsBody" class="divide-y divide-erp-line">
                        @forelse($details as $value)
                            <tr class="pos-row" data-search="{{ strtolower($value->item_code.' '.$value->item_name.' '.$value->product_name) }}">
                                <td class="px-5 py-3">
                                    <img width="42" height="42" src="{{ asset('storage/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200" alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                </td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $value->item_code }} | {{ $value->item_name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->uomname }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->product_name }}</td>
                                <td class="px-5 py-3" id="cell-5-{{ $value->pos_item_id }}">
                                    @foreach($variationsByProduct->get($value->pos_item_id, []) as $variation)
                                        <button type="button" id="lable-variation-{{ $variation->id }}" onclick="variationValue({{ $variation->id }}, {{ $variation->variation_id }}, {{ $variation->product_id }})" class="mb-1 mr-1 inline-flex rounded-full bg-erp-light/40 px-2.5 py-1 text-xs font-bold text-erp-dark">{{ $variation->name }}</button>
                                    @endforeach
                                </td>
                                <td class="px-5 py-3 text-erp-text">{{ $value->retail_price }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $value->status_name === 'Active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $value->status_name }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <button type="button" title="Add Variation" onclick="createVariation({{ $value->pos_item_id }})" class="text-erp-dark hover:text-erp">+ Variation</button>
                                        <button type="button" title="Edit" onclick="edit('{{ $value->pos_item_id }}','{{ $value->item_code }}','{{ $value->item_name }}','{{ $value->actual_price }}','{{ $value->tax_rate }}','{{ $value->tax_amount }}','{{ $value->retail_price }}','{{ $value->wholesale_price }}','{{ $value->online_price }}','{{ $value->discount_price }}','{{ $value->quantity }}','{{ $value->uom_id }}','{{ asset('assets/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}','{{ $value->image }}')" class="font-bold text-erp-dark hover:text-erp">Edit</button>
                                        <button type="button" title="Delete" onclick="remove('{{ $value->item_name }}', {{ $value->pos_item_id }})" class="font-bold text-rose-600 hover:text-rose-700">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-6 text-center text-sm text-erp-mute">No POS products yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="help-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-md overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Conversion Details</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('help-modal')">Close</button>
            </div>
            <div class="px-5 py-5">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs font-bold uppercase text-erp-mute"><th class="py-2">Gram</th><th class="py-2">Kilo Gram</th></tr></thead>
                    <tbody class="divide-y divide-erp-line">
                        <tr><td class="py-2">250 gram</td><td class="py-2">0.25 kg</td></tr>
                        <tr><td class="py-2">500 gram</td><td class="py-2">0.5 kg</td></tr>
                        <tr><td class="py-2">750 gram</td><td class="py-2">0.75 kg</td></tr>
                        <tr><td class="py-2">1000 gram</td><td class="py-2">1 kg</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="update-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-3xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Update Product</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('update-modal')">Close</button>
            </div>
            <form id="update-pos-product" enctype="multipart/form-data" class="space-y-4 px-5 py-5">
                <input type="hidden" name="itemid" id="itemid">
                <input type="hidden" name="prevImageName" id="prevImageName">

                <div class="grid gap-4 sm:grid-cols-3">
                    <label class="block">
                        <span class="{{ $labelClass }}">Item Code</span>
                        <input type="text" name="itemmodalcode" id="itemmodalcode" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Item Name</span>
                        <input type="text" name="itemnamemodal" id="itemnamemodal" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Qty. Usage</span>
                        <input type="number" min="0" name="qtymodal" id="qtymodal" class="{{ $inputClass }}">
                    </label>
                </div>

                <label class="block">
                    <span class="{{ $labelClass }}">Select Unit Of Measure</span>
                    <select name="uommodal" id="uommodal" class="{{ $inputClass }}">
                        <option value="">Select Unit Of Measure</option>
                        @if($uoms)
                            @foreach($uoms as $uom)
                                <option value="{{ $uom->uom_id }}">{{ $uom->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <label class="block">
                        <span class="{{ $labelClass }}">Actual Price <span class="text-rose-500">*</span></span>
                        <input type="number" min="0" name="apmodal" id="apmodal" placeholder="0" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tax Rate</span>
                        <input type="number" step=".01" name="modaltaxrate" id="modaltaxrate" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tax Amount</span>
                        <input type="number" min="0" name="modaltaxamount" id="modaltaxamount" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Retail Price</span>
                        <input type="number" min="0" name="rpmodal" id="rpmodal" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Wholesale Price</span>
                        <input type="number" min="0" name="wpmodal" id="wpmodal" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Discount Price</span>
                        <input type="number" min="0" name="dpmodal" id="dpmodal" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Online Price</span>
                        <input type="number" min="0" name="opmodal" id="opmodal" class="{{ $inputClass }}">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Product Image</span>
                        <div class="mt-2 flex items-center gap-3">
                            <img id="updateproductimage" src="{{ asset('assets/images/placeholder.jpg') }}" class="h-16 w-16 rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" name="updateproduct" id="updateproduct" accept="image/*" class="block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                        </div>
                    </label>
                </div>

                <div class="flex justify-end border-t border-erp-line pt-4">
                    <button type="submit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="variation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Create Variation</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('variation-modal')">Close</button>
            </div>
            <form id="variationForm" class="space-y-4 px-5 py-5">
                <input type="hidden" name="itemId" id="itemId_md">
                <div id="createVariationModal_alert" class="text-sm font-semibold"></div>

                <label class="block">
                    <span class="{{ $labelClass }}">Select Variations Of The Product (If Any)</span>
                    <select class="{{ $inputClass }}" id="variations" name="variations">
                        <option value="">Select Variations</option>
                        @if($totalvariation)
                            @foreach($totalvariation as $variation)
                                <option value="{{ $variation->id }}">{{ $variation->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span class="mt-1 block text-xs font-semibold text-rose-600" id="variations_alert_md"></span>
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Select Products</span>
                    <select name="products[]" id="products" multiple disabled class="mt-2 min-h-[120px] w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></select>
                    <span class="mt-1 block text-xs font-semibold text-rose-600" id="products_alert_md"></span>
                </label>

                <div class="flex justify-end border-t border-erp-line pt-4">
                    <button type="button" id="btnaddVariation" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-variation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Edit Variation Values</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('edit-variation-modal')">Close</button>
            </div>
            <form id="variationEditForm" class="space-y-4 px-5 py-5">
                <input type="hidden" name="itemId_edit_md" id="itemId_edit_md">
                <input type="hidden" name="id" id="inventory_variation_unid_edit_md">
                <input type="hidden" name="variationid" id="variationId">
                <div id="editVariationModal_alert" class="text-sm font-semibold"></div>
                <h3 id="edit_md_variationName" class="text-sm font-bold text-erp-ink"></h3>

                <label class="block">
                    <span class="{{ $labelClass }}">Select Products</span>
                    <select name="products[]" id="products_edit_md" multiple disabled class="mt-2 min-h-[120px] w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></select>
                    <span class="mt-1 block text-xs font-semibold text-rose-600" id="products_alert_edit_md"></span>
                </label>

                <div class="flex justify-between border-t border-erp-line pt-4">
                    <button type="button" id="btnRemoveVariation" onclick="remove_variation_cmd()" class="rounded-lg border border-rose-200 bg-rose-50 px-6 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Remove</button>
                    <button type="button" id="btnupdateVariation" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        document.getElementById('toggleCreateCard').addEventListener('click', function () {
            const card = document.getElementById('upload_form');
            card.classList.toggle('hidden');
            this.textContent = card.classList.contains('hidden') ? 'Expand' : 'Collapse';
        });

        document.getElementById('btn_help').addEventListener('click', function () {
            openModal('help-modal');
        });

        document.getElementById('posSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.pos-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function previewImage(inputId, previewId) {
            document.getElementById(inputId).addEventListener('change', function () {
                if (this.files[0]) {
                    document.getElementById(previewId).src = URL.createObjectURL(this.files[0]);
                }
            });
        }
        previewImage('productimage', 'productimages');
        previewImage('updateproduct', 'updateproductimage');

        /* ---------- Create Product ---------- */
        document.getElementById('upload_form').addEventListener('submit', function (event) {
            event.preventDefault();

            if (!document.getElementById('finishgood').value) { alert('Please Select Product!'); return; }
            if (!document.getElementById('itemname').value) { alert('Item name can not left blank!'); return; }
            if (!document.getElementById('qty').value || document.getElementById('qty').value === '0') { alert('Qty can not left blank!'); return; }
            if (!document.getElementById('code').value) { alert('Please Enter Item Code!'); return; }

            fetch("{{ url('/insert-posproducts') }}", {
                method: 'POST',
                body: new FormData(this),
            })
                .then(res => res.json().catch(() => res.text()))
                .then(resp => {
                    if (resp == 1) {
                        alert('POS Product Added Successfully!');
                        window.location = "{{ url('/posproducts') }}";
                    } else {
                        alert('Same Name POS Product Already Exists!');
                    }
                })
                .catch(() => alert('Unable to create POS product.'));
        });

        /* ---------- Verify Code ---------- */
        document.getElementById('code').addEventListener('change', function () {
            const code = this.value;
            if (!code) return;
            fetch("{{ url('/verifycode') }}?code=" + encodeURIComponent(code))
                .then(res => res.text())
                .then(resp => {
                    if (resp != 0) {
                        alert('Item Code Already Exists, Please try different!');
                        document.getElementById('code').value = '';
                    }
                });
        });

        /* ---------- Delete / Reactivate ---------- */
        function remove(name, id) {
            if (!confirm('Do you want to Delete ' + name + '?')) return;
            fetch("{{ url('/inactive-posproducts') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ subid: id })
            })
                .then(res => res.text())
                .then(resp => {
                    if (resp == 1) {
                        alert('POS Product Deleted Successfully!');
                        window.location = "{{ url('/posproducts') }}";
                    }
                })
                .catch(() => alert('Unable to delete POS product.'));
        }

        function reactive(id) {
            if (!confirm('You want to Re-Active this POS Product!')) return;
            fetch("{{ url('/reactive-posproducts') }}", {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ subid: id })
            })
                .then(res => res.text())
                .then(resp => {
                    if (resp == 1) {
                        alert('POS Product Re-Activated Successfully!');
                        window.location = "{{ url('/posproducts') }}";
                    }
                })
                .catch(() => alert('Unable to reactivate POS product.'));
        }

        document.getElementById('chkactive').addEventListener('change', function () {
            if (!this.checked) {
                window.location = "{{ url('/posproducts') }}";
                return;
            }

            fetch("{{ url('/inactive-posproducts') }}")
                .then(res => res.json())
                .then(result => {
                    const tbody = document.getElementById('posProductsBody');
                    tbody.innerHTML = '';
                    if (!result || !result.length) {
                        tbody.innerHTML = '<tr><td colspan="8" class="px-5 py-6 text-center text-sm text-erp-mute">No inactive POS products.</td></tr>';
                        return;
                    }
                    result.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.className = 'pos-row';
                        tr.innerHTML = `
                            <td class="px-5 py-3"><img width="42" height="42" src="{{ asset('storage/images/products/') }}/${row.image ? row.image : 'placeholder.jpg'}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200"></td>
                            <td class="px-5 py-3 font-semibold text-erp-ink">${row.item_name}</td>
                            <td class="px-5 py-3 text-erp-text">-</td>
                            <td class="px-5 py-3 text-erp-text">${row.department_name ?? ''}</td>
                            <td class="px-5 py-3">-</td>
                            <td class="px-5 py-3 text-erp-text">${row.price ?? ''}</td>
                            <td class="px-5 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">${row.status_name}</span></td>
                            <td class="px-5 py-3 text-right"><button type="button" class="font-bold text-erp-dark hover:text-erp" onclick="reactive(${row.sub_id})">Reactivate</button></td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        });

        /* ---------- Edit Product ---------- */
        function edit(id, code, name, ap, taxrate, taxamount, rp, wp, op, dp, qty, uomId, src, image) {
            openModal('update-modal');
            document.getElementById('itemid').value = id;
            document.getElementById('itemmodalcode').value = code;
            document.getElementById('itemnamemodal').value = name;
            document.getElementById('apmodal').value = ap;
            document.getElementById('modaltaxrate').value = taxrate;
            document.getElementById('modaltaxamount').value = taxamount;
            document.getElementById('rpmodal').value = rp;
            document.getElementById('wpmodal').value = wp;
            document.getElementById('opmodal').value = op;
            document.getElementById('dpmodal').value = dp;
            document.getElementById('qtymodal').value = qty;
            document.getElementById('uommodal').value = uomId;
            document.getElementById('updateproductimage').src = src;
            document.getElementById('prevImageName').value = image;
        }

        document.getElementById('update-pos-product').addEventListener('submit', function (event) {
            event.preventDefault();
            fetch("{{ url('/update-posproducts') }}", {
                method: 'PUT',
                body: new FormData(this),
            })
                .then(res => res.text())
                .then(resp => {
                    if (resp != 2) {
                        alert('Updated Successfully!');
                        window.location = "{{ url('/posproducts') }}";
                    } else {
                        alert('Same Name POS Product Already Exists!');
                    }
                })
                .catch(() => alert('Unable to update POS product.'));
        });

        /* ---------- Price calculations ---------- */
        function wireTaxCalc(apId, rateId, amountId, rpId) {
            const ap = document.getElementById(apId);
            const rate = document.getElementById(rateId);
            const amount = document.getElementById(amountId);
            const rp = document.getElementById(rpId);

            ap.addEventListener('change', () => {
                if (rate.value !== '') {
                    const taxAmount = ap.value * (rate.value / 100);
                    amount.value = Math.round(taxAmount);
                    rp.value = Math.round(parseInt(ap.value || 0) + Math.round(taxAmount));
                }
            });
            rate.addEventListener('change', () => {
                const taxAmount = ap.value * (rate.value / 100);
                amount.value = Math.round(taxAmount);
                rp.value = Math.round(parseInt(ap.value || 0) + Math.round(taxAmount));
            });
            amount.addEventListener('change', () => {
                rate.value = (amount.value / (ap.value || 1)) * 100;
                rp.value = Math.round(parseInt(ap.value || 0) + parseInt(amount.value || 0));
            });
        }
        wireTaxCalc('ap', 'taxrate', 'taxamount', 'rp');
        wireTaxCalc('apmodal', 'modaltaxrate', 'modaltaxamount', 'rpmodal');

        /* ---------- Variations ---------- */
        function createVariation(id) {
            document.getElementById('itemId_md').value = id;
            openModal('variation-modal');
        }

        document.getElementById('variations').addEventListener('change', function () {
            callVariation(this.value, 'products');
        });

        function callVariation(vid, elementId) {
            const select = document.getElementById(elementId);
            if (!vid) {
                select.innerHTML = '';
                select.disabled = true;
                return;
            }
            fetch('{{ route("getVariation_posproduct") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: vid })
            })
                .then(res => res.json())
                .then(resp => {
                    if (!resp || !resp.length) return;
                    select.innerHTML = '';
                    resp.forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id;
                        opt.textContent = v.name;
                        opt.selected = true;
                        select.appendChild(opt);
                    });
                    select.disabled = false;
                });
        }

        function modalControl_clear() {
            ['variations_alert_md', 'products_alert_md', 'products_alert_edit_md'].forEach(id => {
                document.getElementById(id).textContent = '';
            });
            document.getElementById('createVariationModal_alert').textContent = '';
            document.getElementById('createVariationModal_alert').className = 'text-sm font-semibold';
            document.getElementById('editVariationModal_alert').textContent = '';
            document.getElementById('editVariationModal_alert').className = 'text-sm font-semibold';
        }

        document.getElementById('btnaddVariation').addEventListener('click', function () {
            modalControl_clear();
            const variations = document.getElementById('variations');
            const products = document.getElementById('products');
            const selected = Array.from(products.selectedOptions).map(o => o.value);

            if (!variations.value) { document.getElementById('variations_alert_md').textContent = 'Select variation field is required!'; return; }
            if (!products.disabled && selected.length === 0) { document.getElementById('products_alert_md').textContent = 'Select variation field is required!'; return; }

            const params = new URLSearchParams();
            params.append('itemId', document.getElementById('itemId_md').value);
            params.append('variations', variations.value);
            selected.forEach(v => params.append('products[]', v));

            fetch('{{ route("storeVariation") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: params.toString()
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status === 200) {
                        document.getElementById('createVariationModal_alert').textContent = 'Success!';
                        document.getElementById('createVariationModal_alert').className = 'text-sm font-semibold text-emerald-600';
                        setTimeout(() => window.location.reload(), 600);
                    } else if (resp.status === 409) {
                        document.getElementById('variations_alert_md').textContent = resp.msg;
                    } else {
                        document.getElementById('createVariationModal_alert').textContent = resp.msg;
                        document.getElementById('createVariationModal_alert').className = 'text-sm font-semibold text-rose-600';
                    }
                });
        });

        document.getElementById('btnupdateVariation').addEventListener('click', function () {
            modalControl_clear();
            const products = document.getElementById('products_edit_md');
            const selected = Array.from(products.selectedOptions).map(o => o.value);

            if (selected.length === 0) { document.getElementById('products_alert_edit_md').textContent = 'Select variation field is required!'; return; }

            const params = new URLSearchParams();
            params.append('id', document.getElementById('inventory_variation_unid_edit_md').value);
            selected.forEach(v => params.append('products[]', v));

            fetch('{{ route("updateVariation") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: params.toString()
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status === 200) {
                        document.getElementById('editVariationModal_alert').textContent = 'Success!';
                        document.getElementById('editVariationModal_alert').className = 'text-sm font-semibold text-emerald-600';
                        setTimeout(() => window.location.reload(), 600);
                    } else {
                        document.getElementById('editVariationModal_alert').textContent = resp.msg;
                        document.getElementById('editVariationModal_alert').className = 'text-sm font-semibold text-rose-600';
                    }
                });
        });

        function variationValue(unid, variation, itemId) {
            openModal('edit-variation-modal');
            document.getElementById('itemId_edit_md').value = itemId;
            document.getElementById('inventory_variation_unid_edit_md').value = unid;
            document.getElementById('variationId').value = variation;
            document.getElementById('edit_md_variationName').textContent = document.getElementById('lable-variation-' + unid).textContent;

            callVariation(variation, 'products_edit_md');

            fetch('{{ route("getVariationProduct_values") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: unid })
            })
                .then(res => res.json())
                .then(resp => {
                    if (!resp) return;
                    const ids = Array.isArray(resp) ? resp.map(String) : [String(resp)];
                    setTimeout(() => {
                        Array.from(document.getElementById('products_edit_md').options).forEach(opt => {
                            opt.selected = ids.includes(opt.value);
                        });
                    }, 150);
                });
        }

        function remove_variation_cmd() {
            const name = document.getElementById('edit_md_variationName').textContent;
            if (!confirm('You want remove this ' + name + ' variation!')) return;

            fetch('{{ route("removeVariation_posproduct") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: document.getElementById('inventory_variation_unid_edit_md').value })
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.status === 200) {
                        alert('Variation Removed Successfully!');
                        window.location = "{{ route('posProducts') }}";
                    } else {
                        alert(resp.msg);
                    }
                });
        }
    </script>
@endpush
