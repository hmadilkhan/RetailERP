@extends('layouts.master-tailwind')

@section('title', 'Inventory')
@section('page_title', 'Inventory')
@section('page_subtitle', 'Manage products, prices, stock visibility, website links, vendors, tags, barcodes, and catalogue exports.')

@section('content')
    @php
        $query = request()->query();
        $selectedIds = [];
        $activeCount = $inventories->where('status', 1)->count();
        $inactiveMode = request()->boolean('inactive');
        $nonStockMode = request()->boolean('nonstock');
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Matches</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($inventories->total()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Products matching current filters</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Visible Rows</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($inventories->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Showing on this page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mode</div>
                <div class="mt-4 text-xl font-black text-erp-ink">{{ $inactiveMode ? 'Inactive' : ($nonStockMode ? 'Non-stock' : 'Active') }}</div>
                <p class="mt-2 text-sm text-erp-mute">Current product scope</p>
            </div>
            <a href="{{ route('create-invent') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Inventory</div>
                    <p class="mt-2 text-sm text-white/75">Add a catalogue item</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                    <div>
                        <h2 class="text-base font-bold text-erp-ink">Import and Export</h2>
                        <p class="mt-1 text-sm text-erp-mute">Upload inventory CSV files or export the current price sheet.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ url('get-sample-csv') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Download Sample</a>
                        <a target="_blank" href="{{ url('get-export-csv-for-retail-price') }}" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">Export Excel</a>
                    </div>
                </div>
                <form method="POST" action="{{ url('uploadInventory') }}" enctype="multipart/form-data" class="mt-5 grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto_auto] lg:items-end">
                    @csrf
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Inventory File</span>
                        <input type="file" name="file" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                    </label>
                    <label class="inline-flex h-10 items-center gap-2 rounded-lg border border-erp-line px-3 text-sm font-bold text-erp-text">
                        <input type="checkbox" name="update" value="1" class="rounded border-erp-line text-erp focus:ring-erp">
                        Update retail price
                    </label>
                    <button type="submit" class="h-10 rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Import</button>
                </form>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <form method="GET" action="{{ route('invent-list') }}" id="filterForm" class="space-y-4">
                    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                        <div>
                            <h2 class="text-base font-bold text-erp-ink">Product Catalogue</h2>
                            <p class="mt-1 text-sm text-erp-mute">Search, update, link, deactivate, clone, sync, or open product builders.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <label class="inline-flex h-10 cursor-pointer items-center gap-2 rounded-lg border border-erp-line px-3 text-sm font-bold text-erp-text">
                                <input type="checkbox" name="inactive" value="1" class="rounded border-erp-line text-erp focus:ring-erp" {{ $inactiveMode ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                Inactive
                            </label>
                            <label class="inline-flex h-10 cursor-pointer items-center gap-2 rounded-lg border border-erp-line px-3 text-sm font-bold text-erp-text">
                                <input type="checkbox" name="nonstock" value="1" class="rounded border-erp-line text-erp focus:ring-erp" {{ $nonStockMode ? 'checked' : '' }} onchange="document.getElementById('filterForm').submit()">
                                Non-stock
                            </label>
                            <a href="{{ route('invent-list') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</a>
                            <button type="button" id="bulkMenuButton" class="hidden h-10 rounded-lg border border-erp bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Bulk Actions</button>
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                        <input type="text" name="code" value="{{ request('code') }}" placeholder="Item code" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <input type="text" name="name" value="{{ request('name') }}" placeholder="Product name" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <input type="text" name="rp" value="{{ request('rp') }}" placeholder="Retail price" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <select name="dept" id="deptFilter" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">All departments</option>
                            @foreach ($department as $depart)
                                <option value="{{ $depart->department_id }}" {{ (string) request('dept') === (string) $depart->department_id ? 'selected' : '' }}>{{ $depart->department_name }}</option>
                            @endforeach
                        </select>
                        <select name="sdept" id="subDeptFilter" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">All sub-departments</option>
                        </select>
                        <select name="ref" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">All references</option>
                            @foreach ($references as $reference)
                                <option value="{{ $reference->refrerence }}" {{ request('ref') === $reference->refrerence ? 'selected' : '' }}>{{ $reference->refrerence }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg border border-erp bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Search</button>
                    </div>
                </form>
            </div>

            <div id="bulkPanel" class="hidden border-b border-erp-line bg-slate-50 px-5 py-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span id="selectedCount" class="mr-2 text-sm font-bold text-erp-ink">0 selected</span>
                    <button type="button" data-bulk="website" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Link Website</button>
                    <button type="button" data-bulk="unlinkWebsite" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Unlink Website</button>
                    <button type="button" data-bulk="brand" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Link Brand</button>
                    <button type="button" data-bulk="tags" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Link Tags</button>
                    <button type="button" data-bulk="department" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Department</button>
                    <button type="button" data-bulk="subdepartment" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Sub-Department</button>
                    <button type="button" data-bulk="uom" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">UOM</button>
                    <button type="button" data-bulk="tax" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Tax</button>
                    <button type="button" data-bulk="price" class="bulk-action rounded-lg border border-erp-line bg-white px-3 py-2 text-xs font-bold text-erp-text hover:border-erp">Price</button>
                    <button type="button" data-bulk="sunmi" class="bulk-action rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 hover:bg-sky-100">Sunmi ESL</button>
                    <button type="button" data-bulk="activate" class="bulk-action rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Activate</button>
                    @if (session('roleId') == 2)
                        <button type="button" data-bulk="inactive" class="bulk-action rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">Inactive</button>
                    @endif
                    @if (session('roleId') == 1)
                        <button type="button" data-bulk="delete" class="bulk-action rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100">Delete</button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold"><input type="checkbox" id="selectAll" class="rounded border-erp-line text-erp focus:ring-erp"></th>
                            <th class="px-4 py-3 text-left font-bold">Product</th>
                            <th class="px-4 py-3 text-left font-bold">Department</th>
                            <th class="px-4 py-3 text-right font-bold">Prices</th>
                            <th class="px-4 py-3 text-right font-bold">Stock</th>
                            <th class="px-4 py-3 text-left font-bold">Channels</th>
                            <th class="px-4 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($inventories as $item)
                            @php
                                $imageUrl = Custom_Helper::getProductImageUrl($item);
                                $tags = collect(explode(',', $item->tags ?? ''))->map(fn ($tag) => trim($tag))->filter();
                            @endphp
                            <tr class="group bg-white transition hover:bg-emerald-50/40">
                                <td class="px-4 py-3 align-top">
                                    <input type="checkbox" class="row-check rounded border-erp-line text-erp focus:ring-erp" value="{{ $item->id }}">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex min-w-[17rem] items-center gap-3">
                                        <a href="{{ $imageUrl }}" target="_blank" class="relative shrink-0">
                                            <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200" src="{{ $imageUrl }}" alt="{{ $item->product_name }}">
                                            <span class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white {{ ($item->stock ?? 0) > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        </a>
                                        <div class="min-w-0">
                                            <div class="truncate font-bold text-erp-ink">{{ $item->product_name }}</div>
                                            <div class="mt-1 text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">{{ $item->item_code }}</div>
                                            <div class="mt-1.5 flex flex-wrap gap-1">
                                                @foreach ($tags as $tag)
                                                    <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">{{ $tag }}</span>
                                                @endforeach
                                                @if (!empty($item->website_name))
                                                    <span class="rounded-md bg-sky-50 px-2 py-0.5 text-xs font-bold text-sky-700 ring-1 ring-sky-200">{{ $item->website_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="inline-flex rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-700 ring-1 ring-indigo-200">{{ $item->department_name ?? '-' }}</div>
                                    <div class="mt-1 text-sm font-semibold text-erp-text">{{ $item->sub_depart_name ?? '-' }}</div>
                                    <div class="text-xs text-erp-mute">Priority {{ $item->priority ?? 0 }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="text-base font-black text-erp-ink">{{ number_format((float) $item->retail_price, 2) }}</div>
                                    <div class="text-xs font-semibold text-erp-mute">Retail</div>
                                    <div class="mt-1 grid grid-cols-2 gap-x-2 gap-y-0.5 text-xs text-erp-mute">
                                        <span>Act {{ number_format((float) $item->actual_price, 2) }}</span>
                                        <span>Wh {{ number_format((float) $item->wholesale_price, 2) }}</span>
                                        <span class="col-span-2">On {{ number_format((float) $item->online_price, 2) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex min-w-[5rem] justify-center rounded-md {{ ($item->stock ?? 0) > 0 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-rose-50 text-rose-700 ring-rose-200' }} px-2.5 py-1.5 font-black ring-1">
                                        {{ number_format((float) ($item->stock ?? 0), 2) }}
                                    </div>
                                    <div class="mt-1 text-xs text-erp-mute">{{ $item->name ?? '-' }}</div>
                                    <div class="text-xs text-erp-mute">GST {{ number_format((float) ($item->tax_rate ?? 0), 2) }}%</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <label class="inline-flex cursor-pointer items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-erp-text ring-1 ring-slate-200">
                                            <input type="checkbox" class="status-toggle rounded border-erp-line text-erp focus:ring-erp" data-id="{{ $item->id }}" data-column="pos" {{ ($item->isPos ?? 0) == 1 ? 'checked' : '' }}> POS
                                        </label>
                                        <label class="inline-flex cursor-pointer items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-erp-text ring-1 ring-slate-200">
                                            <input type="checkbox" class="status-toggle rounded border-erp-line text-erp focus:ring-erp" data-id="{{ $item->id }}" data-column="online" {{ ($item->isOnline ?? 0) == 1 ? 'checked' : '' }}> Online
                                        </label>
                                        <label class="inline-flex cursor-pointer items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-erp-text ring-1 ring-slate-200">
                                            <input type="checkbox" class="status-toggle rounded border-erp-line text-erp focus:ring-erp" data-id="{{ $item->id }}" data-column="hide" {{ ($item->isHide ?? 0) == 1 ? 'checked' : '' }}> Hidden
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ url('edit-invent/' . $item->slug) }}" target="_blank" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</a>
                                        <button type="button" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp" data-action-menu="{{ $item->id }}">More</button>
                                    </div>
                                    <div id="menu-{{ $item->id }}" class="action-menu mt-2 hidden min-w-[14rem] rounded-lg border border-erp-line bg-white p-2 text-left shadow-menu">
                                        <a href="{{ url('/inventory/' . $item->id . '/deal-products') }}" class="block rounded-md px-3 py-2 text-xs font-bold text-erp-text hover:bg-slate-50">{{ $item->is_deal == 1 ? 'View Deal' : 'Create Deal' }}</a>
                                        <a href="{{ url('/inventory/' . $item->id . '/variable-products') }}" class="block rounded-md px-3 py-2 text-xs font-bold text-erp-text hover:bg-slate-50">{{ $item->pos_product_count != 0 ? 'View variable products' : 'Create variable products' }}</a>
                                        <a href="{{ url('/inventory/' . $item->id . '/variable-products' . (($item->addon_product ?? 0) != 0 ? '/?#addonTab' : '')) }}" class="block rounded-md px-3 py-2 text-xs font-bold text-erp-text hover:bg-slate-50">{{ ($item->addon_product ?? 0) != 0 ? 'View addon products' : 'Create addon products' }}</a>
                                        <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-erp-text hover:bg-slate-50" onclick="openBarcode(@js($item->item_code), @js($item->product_name), @js($item->retail_price))">Print Barcode</button>
                                        <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-erp-text hover:bg-slate-50" onclick="openVendorAssign('{{ $item->id }}')">Assign Vendors</button>
                                        @if ($item->website_id != '')
                                            <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-erp-text hover:bg-slate-50" onclick="unlinkWebsite('{{ $item->id }}', '{{ $item->website_id }}')">Unlink Website</button>
                                        @endif
                                        <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-erp-text hover:bg-slate-50" onclick="openUnlinkTags('{{ $item->id }}')">Unlink Tags</button>
                                        <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-erp-text hover:bg-slate-50" onclick="cloneProduct('{{ $item->id }}', @js($item->product_name))">Clone Product</button>
                                        <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-erp-text hover:bg-slate-50" onclick="syncShopify('{{ $item->id }}')">Sync Shopify</button>
                                        <button type="button" class="block w-full rounded-md px-3 py-2 text-left text-xs font-bold text-rose-700 hover:bg-rose-50" onclick="deleteProduct('{{ $item->id }}')">Inactive</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="font-bold text-erp-ink">No inventory found</div>
                                    <p class="mt-2 text-sm text-erp-mute">Change filters or create a new inventory item.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-erp-mute">
                    Showing {{ $inventories->firstItem() ?? 0 }} to {{ $inventories->lastItem() ?? 0 }} of {{ $inventories->total() }} products
                </div>
                <div class="flex gap-2">
                    @if ($inventories->onFirstPage())
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Previous</span>
                    @else
                        <a href="{{ $inventories->previousPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Previous</a>
                    @endif

                    @if ($inventories->hasMorePages())
                        <a href="{{ $inventories->nextPageUrl() }}" class="rounded-lg border border-erp-line px-3 py-2 text-sm font-semibold text-erp-text transition hover:border-erp hover:text-erp-dark">Next</a>
                    @else
                        <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-300">Next</span>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <div id="modalBackdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50"></div>
    <div id="genericModal" class="fixed left-1/2 top-1/2 z-50 hidden w-[calc(100%-2rem)] max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-lg border border-erp-line bg-white shadow-menu">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <h3 id="modalTitle" class="text-base font-bold text-erp-ink">Action</h3>
            <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal()">Close</button>
        </div>
        <div id="modalBody" class="space-y-4 px-5 py-4"></div>
        <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
            <button type="button" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text" onclick="closeModal()">Cancel</button>
            <button type="button" id="modalSave" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white">Save</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        const currentUrl = "{{ route('invent-list') }}";
        const departments = @json($department);
        const uoms = @json($uom);
        const websites = @json($websites);
        const brands = @json($brandList);
        const tags = @json($tagsList);
        const vendors = @json($vendors);
        let selectedIds = [];
        let modalSubmit = null;

        function selected() {
            return Array.from(document.querySelectorAll('.row-check:checked')).map(input => input.value);
        }

        function refreshSelection() {
            selectedIds = selected();
            document.getElementById('selectedCount').textContent = selectedIds.length + ' selected';
            document.getElementById('bulkPanel').classList.toggle('hidden', selectedIds.length === 0);
            document.getElementById('bulkMenuButton').classList.toggle('hidden', selectedIds.length === 0);
        }

        function post(url, data) {
            return fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(data)
            });
        }

        function optionList(items, valueKey, labelKey, selectedValue = '') {
            return items.map(item => `<option value="${item[valueKey]}" ${String(item[valueKey]) === String(selectedValue) ? 'selected' : ''}>${item[labelKey]}</option>`).join('');
        }

        function openModal(title, body, onSave) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = body;
            modalSubmit = onSave;
            document.getElementById('modalBackdrop').classList.remove('hidden');
            document.getElementById('genericModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalBackdrop').classList.add('hidden');
            document.getElementById('genericModal').classList.add('hidden');
            modalSubmit = null;
        }

        document.getElementById('modalSave').addEventListener('click', function () {
            if (modalSubmit) modalSubmit();
        });

        document.querySelectorAll('.row-check').forEach(input => input.addEventListener('change', refreshSelection));
        document.getElementById('selectAll').addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(input => input.checked = this.checked);
            refreshSelection();
        });
        document.getElementById('bulkMenuButton').addEventListener('click', () => document.getElementById('bulkPanel').classList.toggle('hidden'));

        document.querySelectorAll('[data-action-menu]').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.action-menu').forEach(menu => {
                    if (menu.id !== 'menu-' + this.dataset.actionMenu) menu.classList.add('hidden');
                });
                document.getElementById('menu-' + this.dataset.actionMenu).classList.toggle('hidden');
            });
        });

        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', function () {
                post("{{ url('/change-inventory-status') }}", {
                    table: 'inventory',
                    id: this.dataset.id,
                    columnname: this.dataset.column,
                    value: this.checked ? 1 : 0
                }).catch(() => {
                    this.checked = !this.checked;
                    alert('Unable to update product channel.');
                });
            });
        });

        document.getElementById('deptFilter').addEventListener('change', function () {
            loadSubDepartments(this.value, 'subDeptFilter');
        });

        function loadSubDepartments(id, targetId, selectedValue = '') {
            const target = document.getElementById(targetId);
            target.innerHTML = '<option value="">All sub-departments</option>';
            if (!id) return;
            post("{{ url('get_sub_departments') }}", { id }).then(r => r.json()).then(rows => {
                if (!Array.isArray(rows)) return;
                target.innerHTML = '<option value="">All sub-departments</option>' + optionList(rows, 'sub_department_id', 'sub_depart_name', selectedValue);
            });
        }
        @if (request('dept'))
            loadSubDepartments("{{ request('dept') }}", 'subDeptFilter', "{{ request('sdept') }}");
        @endif

        document.querySelectorAll('.bulk-action').forEach(button => {
            button.addEventListener('click', function () {
                const ids = selected();
                if (!ids.length) {
                    alert('Select at least one product.');
                    return;
                }
                runBulk(this.dataset.bulk, ids);
            });
        });

        function runBulk(action, ids) {
            if (action === 'website') return selectBulk('Link Website', websites, 'id', 'name', value => post("{{ route('setProductAttribute_update') }}", { inventid: ids, website: value }));
            if (action === 'brand') return selectBulk('Link Brand', brands, 'id', 'name', value => post("{{ route('setProductAttribute_update') }}", { inventid: ids, brand: value }));
            if (action === 'tags') return multiBulk('Link Tags', tags, 'id', 'name', values => post("{{ route('setProductAttribute_update') }}", { inventid: ids, tags: values }));
            if (action === 'department') return selectBulk('Change Department', departments, 'department_id', 'department_name', value => post("{{ url('update_product_department') }}", { inventid: ids, deptId: value }));
            if (action === 'subdepartment') return subDepartmentBulk(ids);
            if (action === 'uom') return selectBulk('Change UOM', uoms, 'uom_id', 'name', value => post("{{ url('update_product_uom') }}", { inventid: ids, uomId: value }));
            if (action === 'tax') return taxBulk();
            if (action === 'price') return priceBulk(ids);
            if (action === 'sunmi') return sunmiCloud(ids);
            if (action === 'activate') return confirmPost('Activate selected products?', "{{ url('/multiple-active-invent') }}", { inventid: ids });
            if (action === 'inactive') return confirmPost('Mark selected products inactive?', "{{ url('/all_invent_remove') }}", { inventid: ids, statusid: 2 });
            if (action === 'delete') return confirmPost('Delete selected products permanently?', "{{ url('/all_invent_delete') }}", { inventid: ids });
            if (action === 'unlinkWebsite') return confirmPost('Unlink website for selected products?', "{{ route('all_product_unlink_website') }}", { product_id: ids });
        }

        function selectBulk(title, rows, valueKey, labelKey, submitter) {
            openModal(title, `<select id="modalValue" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"><option value="">Select</option>${optionList(rows, valueKey, labelKey)}</select>`, () => {
                const value = document.getElementById('modalValue').value;
                if (!value) return alert('Select a value.');
                submitter(value).then(() => window.location.reload());
            });
        }

        function multiBulk(title, rows, valueKey, labelKey, submitter) {
            openModal(title, `<select id="modalValue" multiple class="h-40 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">${optionList(rows, valueKey, labelKey)}</select>`, () => {
                const values = Array.from(document.getElementById('modalValue').selectedOptions).map(option => option.value);
                if (!values.length) return alert('Select at least one value.');
                submitter(values).then(() => window.location.reload());
            });
        }

        function subDepartmentBulk(ids) {
            openModal('Change Sub-Department', `
                <select id="modalDept" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"><option value="">Select department</option>${optionList(departments, 'department_id', 'department_name')}</select>
                <select id="modalSubDept" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"><option value="">Select sub-department</option></select>
            `, () => {
                const deptID = document.getElementById('modalDept').value;
                const subdeptId = document.getElementById('modalSubDept').value;
                if (!deptID || !subdeptId) return alert('Select department and sub-department.');
                post("{{ url('update_product_subdepartment') }}", { inventid: ids, deptID, subdeptId }).then(() => window.location.reload());
            });
            document.getElementById('modalDept').addEventListener('change', function () { loadSubDepartments(this.value, 'modalSubDept'); });
        }

        function taxBulk() {
            openModal('Change Tax', `
                <input id="prevTax" type="number" step="0.01" placeholder="Previous tax rate" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <input id="newTax" type="number" step="0.01" placeholder="New tax rate" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            `, () => {
                post("{{ url('update_product_tax') }}", { prev_tax: document.getElementById('prevTax').value, new_tax: document.getElementById('newTax').value }).then(() => window.location.reload());
            });
        }

        function priceBulk(ids) {
            openModal('Change Price', `
                <select id="priceMode" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"><option value="1">Percentage</option><option value="2">Amount</option></select>
                <input id="retailPercent" type="number" step="0.01" placeholder="Retail change" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <input id="wholesalePercent" type="number" step="0.01" placeholder="Wholesale change" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <input id="discountPercent" type="number" step="0.01" placeholder="Discount change" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <input id="onlinePercent" type="number" step="0.01" placeholder="Online change" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            `, () => {
                post("{{ url('insertnewprice') }}", {
                    productid: ids,
                    pricemode: document.getElementById('priceMode').value,
                    rp: document.getElementById('retailPercent').value,
                    wp: document.getElementById('wholesalePercent').value,
                    dp: document.getElementById('discountPercent').value,
                    op: document.getElementById('onlinePercent').value,
                    departmentId: ''
                }).then(() => window.location.reload());
            });
        }

        function confirmPost(message, url, data) {
            if (!confirm(message)) return;
            post(url, data).then(() => window.location.reload());
        }

        function deleteProduct(id) {
            confirmPost('Mark this product inactive?', "{{ url('delete-invent') }}", { id, status: 2 });
        }

        function unlinkWebsite(product_id, website_id) {
            confirmPost('Unlink this product from website?', "{{ route('website_product_unlink') }}", { product_id, website_id });
        }

        function cloneProduct(productId, productName) {
            confirmPost('Clone this product?', "{{ route('duplicateProductToGeneralInventory') }}", { productId, productName });
        }

        function syncShopify(inventoryId) {
            confirmPost('Sync this product to Shopify?', "{{ route('sync-product-to-shopify') }}", { inventoryId });
        }

        function openVendorAssign(productId) {
            multiBulk('Assign Vendors', vendors, 'id', 'vendor_name', values => post("{{ url('/assign-product-to-vendors') }}", { productId, vendors: values }));
        }

        function openBarcode(code, name, price) {
            openModal('Print Barcode', `
                <select id="labelSize" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="2550">25 x 50</option>
                    <option value="6040">60 x 40</option>
                    <option value="1928">19 x 28</option>
                    <option value="3828">38 x 28</option>
                    <option value="4020">40 x 20</option>
                </select>
                <select id="labelPattern" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="tripple">Triple</option>
                </select>
                <select id="printHeader" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="company">Company header</option>
                    <option value="branch">Branch header</option>
                </select>
                <input id="labelName" value="${String(name).replace(/"/g, '&quot;')}" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <input id="labelCode" value="${String(code).replace(/"/g, '&quot;')}" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <input id="labelPrice" value="${String(price).replace(/"/g, '&quot;')}" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <div class="grid grid-cols-2 gap-3">
                    <input id="nameMargin1" type="number" value="0" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <input id="nameMargin2" type="number" value="0" class="w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>
            `, () => {
                post("{{ url('printBarcode') }}", {
                    url: document.getElementById('labelSize').value + document.getElementById('labelPattern').value,
                    code: document.getElementById('labelCode').value,
                    name: document.getElementById('labelName').value,
                    price: document.getElementById('labelPrice').value,
                    margin1: document.getElementById('nameMargin1').value,
                    margin2: document.getElementById('nameMargin2').value,
                    printheader: document.getElementById('printHeader').value
                }).then(response => response.text()).then(url => window.open(url, '_blank'));
            });
        }

        function openUnlinkTags(product) {
            post("{{ route('getProduct_attribute') }}", { control: 'tag', product }).then(r => r.json()).then(rows => {
                multiBulk('Update Product Tags', Array.isArray(rows) ? rows : [], 'id', 'name', values => post("{{ route('updateProductTags') }}", { product, tags: values }));
            });
        }

        function sunmiCloud(ids) {
            post("{{ url('/sunmi-cloud') }}", { inventory: ids }).then(response => response.text()).then(text => {
                openModal('Sunmi ESL Payload', `<textarea readonly class="h-60 w-full rounded-lg border-erp-line text-xs shadow-sm">${text}</textarea>`, closeModal);
            });
        }
    </script>
@endpush
