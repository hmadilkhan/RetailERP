@extends('layouts.master-tailwind')

@php
    $editingTransfer = isset($getdetails) && count($getdetails) > 0;
    $transferRecord = $editingTransfer ? $getdetails[0] : null;
    $transferId = $editingTransfer ? $transferRecord->transfer_id : $addtransfer;
    $sourceBranchId = $editingTransfer ? $transferRecord->branch_from : session('branch');
    $destinationBranchId = $editingTransfer ? $transferRecord->branch_to : null;
    $transferDate = $editingTransfer ? date('Y-m-d', strtotime($transferRecord->date)) : date('Y-m-d');
@endphp

@section('title', $editingTransfer ? 'Edit Transfer Order' : 'Create Transfer Order')
@section('page_title', $editingTransfer ? 'Edit Transfer Order' : 'Create Transfer Order')
@section('page_subtitle', 'Move stock between branches and place the transfer order when it is ready.')

@section('content')
    <div class="space-y-6" id="transferOrderPage">
        <div class="flex flex-col gap-4 rounded-xl border border-erp-line bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-bold text-erp-ink">Transfer Order #{{ $transferId }}</h2>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-amber-700">Draft</span>
                </div>
                <p class="mt-1 text-sm text-erp-mute">Choose branches, add products, then save or place the order.</p>
            </div>
            <a href="{{ url('/trf_list') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-erp-line bg-white px-4 text-sm font-bold text-erp-text shadow-sm transition hover:border-erp hover:text-erp-dark">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                Back to list
            </a>
        </div>

        <div id="pageMessage" class="hidden rounded-lg border px-4 py-3 text-sm font-medium" role="status"></div>

        <section class="rounded-xl border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="font-bold text-erp-ink">Transfer Information</h3>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-3">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-erp-text">Transfer from branch</span>
                    <select id="branchfrm" class="v2-select2 v2-select2-lg w-full" data-placeholder="Select branch">
                        <option value="">Select branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->branch_id }}" @selected((int) $branch->branch_id === (int) $sourceBranchId)>{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-erp-text">Destination branch</span>
                    <select id="branchto" class="v2-select2 v2-select2-lg w-full" data-placeholder="Select branch">
                        <option value="">Select branch</option>
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-erp-text">Transfer date</span>
                    <input id="trfdate" type="date" value="{{ $transferDate }}" class="h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
        </section>

        <section class="rounded-xl border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="font-bold text-erp-ink">Add Product</h3>
            </div>
            <div class="grid gap-5 p-5 md:grid-cols-12 md:items-end">
                <label class="block md:col-span-5">
                    <span class="mb-2 block text-sm font-bold text-erp-text">Product</span>
                    <select id="product" class="v2-select2 v2-select2-lg w-full" data-placeholder="Select product" disabled>
                        <option value="">Select source branch first</option>
                    </select>
                </label>

                <label class="block md:col-span-3">
                    <span class="mb-2 flex items-center justify-between gap-2 text-sm font-bold text-erp-text">
                        Available stock
                        <span id="stockStatus" class="text-xs font-bold text-erp-mute"></span>
                    </span>
                    <input id="stock" type="text" value="0" readonly class="h-11 w-full rounded-lg border-erp-line bg-slate-50 text-sm text-erp-text shadow-sm">
                </label>

                <label class="block md:col-span-2">
                    <span class="mb-2 block text-sm font-bold text-erp-text">Quantity</span>
                    <input id="qty" type="number" min="1" step="1" value="1" class="h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>

                <button type="button" id="addProductBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-erp px-4 text-sm font-bold text-white shadow-sm transition hover:bg-erp-dark disabled:cursor-not-allowed disabled:opacity-50 md:col-span-2" disabled>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Add item
                </button>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-erp-ink">Transfer Items</h3>
                    <p id="itemCount" class="mt-1 text-xs text-erp-mute">0 items added</p>
                </div>
                <input id="itemFilter" type="search" placeholder="Filter items..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-72">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-erp-mute">
                        <tr>
                            <th class="px-5 py-3">Product</th>
                            <th class="px-5 py-3">Item code</th>
                            <th class="px-5 py-3 text-right">Quantity</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transferItems" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div id="emptyItems" class="px-5 py-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-erp-mute">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7 9 18l-5-5"/><path d="M9 7h11v11"/></svg>
                </div>
                <p class="mt-3 font-bold text-erp-text">No products added</p>
                <p class="mt-1 text-sm text-erp-mute">Select a product and quantity above to start the transfer.</p>
            </div>
        </section>

        <div class="flex flex-col-reverse gap-3 rounded-xl border border-erp-line bg-white p-5 shadow-sm sm:flex-row sm:justify-end">
            <button type="button" id="saveDraftBtn" class="inline-flex h-11 items-center justify-center rounded-lg border border-erp-line bg-white px-5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Save as draft</button>
            <button type="button" id="placeOrderBtn" class="inline-flex h-11 items-center justify-center rounded-lg bg-erp px-5 text-sm font-bold text-white shadow-sm transition hover:bg-erp-dark">Submit and place</button>
        </div>
    </div>

    <div id="quantityModal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/50 p-4" aria-hidden="true">
        <div class="w-full max-w-md rounded-xl bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="quantityModalTitle">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 id="quantityModalTitle" class="font-bold text-erp-ink">Change transfer quantity</h3>
                <button type="button" id="closeQuantityModal" class="rounded-lg p-2 text-erp-mute hover:bg-slate-100 hover:text-erp-ink" aria-label="Close">&times;</button>
            </div>
            <div class="p-5">
                <input type="hidden" id="editItemId">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-erp-text">Quantity</span>
                    <input type="number" min="1" step="1" id="editQuantity" class="h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
            <div class="flex justify-end gap-3 border-t border-erp-line px-5 py-4">
                <button type="button" id="cancelQuantityModal" class="h-10 rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text">Cancel</button>
                <button type="button" id="updateQuantityBtn" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white hover:bg-erp-dark">Update</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const transferId = @json($transferId);
            const initialDestinationBranchId = @json((string) ($destinationBranchId ?? ''));
            const companyId = Number(@json(session('company_id')));
            const placeholderImage = @json(asset('storage/images/placeholder.jpg'));
            const productImageBase = @json(asset('storage/images/products/')) + '/';
            const urls = {
                products: @json(url('/get_products')),
                branches: @json(url('/get-to-branches')),
                stock: @json(url('/trf_stock')),
                insert: @json(url('/insert_trf')),
                details: @json(url('/trf_details')),
                remove: @json(url('/trf_delete')),
                updateQuantity: @json(url('/qty_update')),
                changeStatus: @json(url('/trf_change_status')),
                createChallan: @json(url('/insert_direct_chalan')),
                list: @json(url('/trf_list'))
            };

            const elements = {
                from: document.getElementById('branchfrm'),
                to: document.getElementById('branchto'),
                date: document.getElementById('trfdate'),
                product: document.getElementById('product'),
                stock: document.getElementById('stock'),
                stockStatus: document.getElementById('stockStatus'),
                quantity: document.getElementById('qty'),
                add: document.getElementById('addProductBtn'),
                rows: document.getElementById('transferItems'),
                empty: document.getElementById('emptyItems'),
                count: document.getElementById('itemCount'),
                filter: document.getElementById('itemFilter'),
                message: document.getElementById('pageMessage'),
                modal: document.getElementById('quantityModal'),
                editId: document.getElementById('editItemId'),
                editQuantity: document.getElementById('editQuantity')
            };

            let items = [];
            let destinationBranchToRestore = initialDestinationBranchId;

            function refreshSelect(select, value) {
                if (value !== undefined) select.value = String(value);
                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(select).trigger('change.select2');
                }
            }

            function formBody(data) {
                const body = new URLSearchParams(data);
                body.set('_token', csrfToken);
                return body;
            }

            async function request(url, options = {}) {
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json', ...(options.headers || {}) },
                    credentials: 'same-origin',
                    ...options
                });
                if (!response.ok) throw new Error('Request failed. Please try again.');
                const contentType = response.headers.get('content-type') || '';
                return contentType.includes('application/json') ? response.json() : response.text();
            }

            function showMessage(message, type = 'error') {
                elements.message.textContent = message;
                elements.message.className = 'rounded-lg border px-4 py-3 text-sm font-medium ' +
                    (type === 'success'
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                        : 'border-rose-200 bg-rose-50 text-rose-800');
                elements.message.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                window.setTimeout(() => elements.message.classList.add('hidden'), 5000);
            }

            function setBusy(button, busy, label) {
                if (!button.dataset.label) button.dataset.label = button.textContent.trim();
                button.disabled = busy;
                button.textContent = busy ? label : button.dataset.label;
            }

            function resetStock() {
                elements.stock.value = '0';
                elements.stockStatus.textContent = '';
                elements.add.disabled = true;
            }

            async function loadSourceData() {
                const branchId = elements.from.value;
                elements.to.innerHTML = '<option value="">Select branch</option>';
                elements.product.innerHTML = '<option value="">Loading products...</option>';
                elements.product.disabled = true;
                refreshSelect(elements.to, '');
                refreshSelect(elements.product, '');
                resetStock();
                if (!branchId) {
                    elements.product.innerHTML = '<option value="">Select source branch first</option>';
                    return;
                }

                try {
                    const [branches, products] = await Promise.all([
                        request(urls.branches, { method: 'POST', body: formBody({ branch: branchId }) }),
                        request(urls.products, { method: 'POST', body: formBody({ branchid: branchId }) })
                    ]);

                    branches.forEach(branch => elements.to.add(new Option(branch.branch_name, branch.branch_id)));
                    elements.product.innerHTML = '<option value="">Select product</option>';
                    products.forEach(product => elements.product.add(new Option(product.item_code + ' | ' + product.product_name, product.id)));
                    elements.product.disabled = false;
                    refreshSelect(elements.to, destinationBranchToRestore);
                    destinationBranchToRestore = '';
                    refreshSelect(elements.product, '');
                } catch (error) {
                    elements.product.innerHTML = '<option value="">Unable to load products</option>';
                    refreshSelect(elements.product, '');
                    showMessage(error.message);
                }
            }

            async function loadStock() {
                resetStock();
                if (!elements.product.value || !elements.from.value) return;
                try {
                    const result = await request(urls.stock, {
                        method: 'POST',
                        body: formBody({ productid: elements.product.value, branchid: elements.from.value })
                    });
                    const stock = Number(result[0]?.stock || 0);
                    const reminder = Number(result[0]?.reminder_qty || 0);
                    elements.stock.value = stock;
                    elements.stockStatus.textContent = stock <= 0 ? 'Out of stock' : (stock < reminder ? 'Low stock' : 'In stock');
                    elements.stockStatus.className = 'text-xs font-bold ' + (stock <= 0 ? 'text-rose-600' : (stock < reminder ? 'text-amber-600' : 'text-emerald-600'));
                    elements.add.disabled = stock <= 0;
                } catch (error) {
                    showMessage(error.message);
                }
            }

            function productImage(item) {
                if ([95, 102, 104].includes(companyId) && item.product_image_url) return item.product_image_url;
                return item.product_image ? productImageBase + item.product_image : placeholderImage;
            }

            function actionButton(label, className, handler) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = className;
                button.addEventListener('click', handler);
                return button;
            }

            function renderItems() {
                const term = elements.filter.value.trim().toLowerCase();
                const visibleItems = items.filter(item => (item.item_code + ' ' + item.product_name).toLowerCase().includes(term));
                elements.rows.replaceChildren();

                const hasItems = items.length > 0;
                elements.from.disabled = hasItems;
                elements.to.disabled = hasItems;
                elements.date.disabled = hasItems;
                refreshSelect(elements.from);
                refreshSelect(elements.to);

                visibleItems.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-slate-50';

                    const productCell = document.createElement('td');
                    productCell.className = 'px-5 py-4';
                    const productWrap = document.createElement('div');
                    productWrap.className = 'flex items-center gap-3';
                    const image = document.createElement('img');
                    image.src = productImage(item);
                    image.alt = '';
                    image.className = 'h-11 w-11 rounded-lg border border-erp-line object-cover';
                    image.addEventListener('error', () => { image.src = placeholderImage; });
                    const name = document.createElement('span');
                    name.className = 'font-bold text-erp-text';
                    name.textContent = item.product_name;
                    productWrap.append(image, name);
                    productCell.append(productWrap);

                    const codeCell = document.createElement('td');
                    codeCell.className = 'px-5 py-4 text-erp-text';
                    codeCell.textContent = item.item_code;
                    const qtyCell = document.createElement('td');
                    qtyCell.className = 'px-5 py-4 text-right font-bold text-erp-ink';
                    qtyCell.textContent = item.Transfer_Qty;
                    const actionsCell = document.createElement('td');
                    actionsCell.className = 'px-5 py-4 text-right';
                    const actions = document.createElement('div');
                    actions.className = 'inline-flex gap-2';
                    actions.append(
                        actionButton('Edit', 'rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text hover:border-erp hover:text-erp-dark', () => openQuantityModal(item)),
                        actionButton('Delete', 'rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50', () => removeItem(item.transfer_item_id))
                    );
                    actionsCell.append(actions);
                    row.append(productCell, codeCell, qtyCell, actionsCell);
                    elements.rows.append(row);
                });

                elements.empty.classList.toggle('hidden', hasItems);
                elements.count.textContent = items.length + (items.length === 1 ? ' item added' : ' items added');
            }

            async function loadItems() {
                try {
                    items = await request(urls.details + '?' + new URLSearchParams({ trfid: transferId }));
                    renderItems();
                } catch (error) {
                    showMessage(error.message);
                }
            }

            async function addItem() {
                const quantity = Number(elements.quantity.value);
                const stock = Number(elements.stock.value);
                if (!elements.from.value || !elements.to.value || !elements.product.value || !elements.date.value) {
                    showMessage('Select source branch, destination branch, date, and product.');
                    return;
                }
                if (elements.from.value === elements.to.value) {
                    showMessage('Source and destination branches must be different.');
                    return;
                }
                if (!Number.isFinite(quantity) || quantity <= 0 || quantity > stock) {
                    showMessage('Enter a valid quantity within the available stock.');
                    return;
                }

                setBusy(elements.add, true, 'Adding...');
                try {
                    const result = await request(urls.insert, {
                        method: 'POST',
                        body: formBody({
                            trfid: transferId,
                            trfdate: elements.date.value,
                            branchfrom: elements.from.value,
                            branchto: elements.to.value,
                            productid: elements.product.value,
                            qty: quantity
                        })
                    });
                    if (Number(result) !== 1) {
                        showMessage('This product is already included in the transfer.');
                        return;
                    }
                    elements.product.value = '';
                    refreshSelect(elements.product, '');
                    elements.quantity.value = '1';
                    resetStock();
                    await loadItems();
                    showMessage('Product added to the transfer.', 'success');
                } catch (error) {
                    showMessage(error.message);
                } finally {
                    setBusy(elements.add, false, 'Adding...');
                    elements.add.disabled = true;
                }
            }

            async function removeItem(id) {
                if (!window.confirm('Delete this product from the transfer?')) return;
                try {
                    const result = await request(urls.remove + '?' + new URLSearchParams({ trfid: id }));
                    if (Number(result) !== 1) throw new Error('Product could not be deleted.');
                    await loadItems();
                    showMessage('Product removed from the transfer.', 'success');
                } catch (error) {
                    showMessage(error.message);
                }
            }

            function openQuantityModal(item) {
                elements.editId.value = item.transfer_item_id;
                elements.editQuantity.value = item.Transfer_Qty;
                elements.modal.classList.remove('hidden');
                elements.modal.classList.add('flex');
                elements.modal.setAttribute('aria-hidden', 'false');
                elements.editQuantity.focus();
            }

            function closeQuantityModal() {
                elements.modal.classList.add('hidden');
                elements.modal.classList.remove('flex');
                elements.modal.setAttribute('aria-hidden', 'true');
            }

            async function updateQuantity() {
                const button = document.getElementById('updateQuantityBtn');
                const quantity = Number(elements.editQuantity.value);
                if (!Number.isFinite(quantity) || quantity <= 0) {
                    showMessage('Enter a quantity greater than zero.');
                    return;
                }
                setBusy(button, true, 'Updating...');
                try {
                    const result = await request(urls.updateQuantity, {
                        method: 'PUT',
                        body: formBody({ id: elements.editId.value, qty: quantity })
                    });
                    if (!Number(result)) throw new Error('Quantity could not be updated.');
                    closeQuantityModal();
                    await loadItems();
                    showMessage('Quantity updated.', 'success');
                } catch (error) {
                    showMessage(error.message);
                } finally {
                    setBusy(button, false, 'Updating...');
                }
            }

            async function changeStatus(statusId, allowUnchanged = false) {
                const result = await request(urls.changeStatus, {
                    method: 'PUT',
                    body: formBody({ id: transferId, statusid: statusId })
                });
                if (!allowUnchanged && !Number(result)) throw new Error('Transfer status could not be updated.');
            }

            async function saveDraft() {
                const button = document.getElementById('saveDraftBtn');
                setBusy(button, true, 'Saving...');
                try {
                    await changeStatus(1, true);
                    window.location.assign(urls.list);
                } catch (error) {
                    showMessage(error.message);
                    setBusy(button, false, 'Saving...');
                }
            }

            async function placeOrder() {
                if (!items.length) {
                    showMessage('Add at least one product before placing the transfer.');
                    return;
                }
                if (!elements.to.value) {
                    showMessage('Select a destination branch before placing the transfer.');
                    return;
                }

                const shipmentInput = window.prompt('Shipment amount (leave blank for 0):', '0');
                if (shipmentInput === null) return;
                const shipmentAmount = shipmentInput.trim() === '' ? 0 : Number(shipmentInput);
                if (!Number.isFinite(shipmentAmount) || shipmentAmount < 0) {
                    showMessage('Shipment amount must be zero or greater.');
                    return;
                }

                const button = document.getElementById('placeOrderBtn');
                setBusy(button, true, 'Placing...');
                try {
                    const challan = await request(urls.createChallan, {
                        method: 'POST',
                        body: formBody({ transferid: transferId, branchto: elements.to.value, shipmentamt: shipmentAmount })
                    });
                    if (!Number(challan)) throw new Error('Delivery challan could not be created.');
                    await changeStatus(8);
                    window.location.assign(urls.list);
                } catch (error) {
                    showMessage(error.message);
                    setBusy(button, false, 'Placing...');
                }
            }

            if (window.jQuery && jQuery.fn.select2) {
                jQuery(elements.from).on('change.transferOrder', loadSourceData);
                jQuery(elements.product).on('change.transferOrder', loadStock);
            } else {
                elements.from.addEventListener('change', loadSourceData);
                elements.product.addEventListener('change', loadStock);
            }
            elements.add.addEventListener('click', addItem);
            elements.quantity.addEventListener('keydown', event => { if (event.key === 'Enter') addItem(); });
            elements.filter.addEventListener('input', renderItems);
            document.getElementById('saveDraftBtn').addEventListener('click', saveDraft);
            document.getElementById('placeOrderBtn').addEventListener('click', placeOrder);
            document.getElementById('updateQuantityBtn').addEventListener('click', updateQuantity);
            document.getElementById('closeQuantityModal').addEventListener('click', closeQuantityModal);
            document.getElementById('cancelQuantityModal').addEventListener('click', closeQuantityModal);
            elements.modal.addEventListener('click', event => { if (event.target === elements.modal) closeQuantityModal(); });
            document.addEventListener('keydown', event => { if (event.key === 'Escape') closeQuantityModal(); });

            loadSourceData();
            loadItems();
        });
    </script>
@endpush
