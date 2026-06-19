@extends('layouts.master-tailwind')

@section('title', 'Order Booking')
@section('page_title', 'Retail Pre-order Booking')
@section('page_subtitle', 'Build an order manually for a customer, add items, then place the order.')

@section('content')
    <form id="placeOrderForm" class="space-y-6">
        <input type="hidden" id="subTotalAmount" name="subTotal">
        <input type="hidden" id="taxAmount" name="taxAmount">
        <input type="hidden" id="discountAmount" name="discountAmount">
        <input type="hidden" id="netAmount" name="totalAmount">

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Retail Pre-order Booking</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-3">
                <div class="relative">
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Customers</label>
                    <input type="hidden" id="customerId" name="customerId">
                    <input type="text" id="customerSearch" autocomplete="off" placeholder="Search for a Customer" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <div id="customerResults" class="absolute z-20 hidden w-full rounded-lg border border-erp-line bg-white shadow-menu"></div>
                    <div id="customerId_message" class="message mt-1 text-xs font-semibold text-rose-600"></div>
                </div>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Order Type</span>
                    <select id="orderTypeId" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Order Type</option>
                        @if (!empty($orderTypes))
                            @foreach ($orderTypes as $types)
                                <option value="{{ $types->order_mode_id }}">{{ $types->order_mode }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div id="orderTypeId_message" class="message mt-1 text-xs font-semibold text-rose-600"></div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Order Payment</span>
                    <select id="paymentId" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Order Payment</option>
                        @if (!empty($payments))
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->payment_id }}">{{ $payment->payment_mode }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div id="paymentId_message" class="message mt-1 text-xs font-semibold text-rose-600"></div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Branches</span>
                    <select id="branchId" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Branches</option>
                        @if ($branches)
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div id="branchId_message" class="message mt-1 text-xs font-semibold text-rose-600"></div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Terminals</span>
                    <select id="terminalId" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Terminals</option>
                    </select>
                    <div id="terminalId_message" class="message mt-1 text-xs font-semibold text-rose-600"></div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sales Persons</span>
                    <select id="salespersonId" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Sales Persons</option>
                    </select>
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Add Order Items</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
                <div class="relative xl:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Products</label>
                    <input type="hidden" id="productId">
                    <input type="text" id="productSearch" autocomplete="off" placeholder="Search for a Product" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <div id="productResults" class="absolute z-20 hidden w-full rounded-lg border border-erp-line bg-white shadow-menu"></div>
                    <div id="productId_message" class="item-message mt-1 text-xs font-semibold text-rose-600"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Qty</label>
                    <input type="number" id="qty" placeholder="Enter Qty" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <div id="qty_message" class="item-message mt-1 text-xs font-semibold text-rose-600"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Price</label>
                    <input type="text" id="price" placeholder="Enter Price" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <div id="price_message" class="item-message mt-1 text-xs font-semibold text-rose-600"></div>
                </div>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Tax</span>
                    <select id="taxValue" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Tax</option>
                        @if (!empty($taxes))
                            @foreach ($taxes as $tax)
                                <option value="{{ $tax->value }}">{{ $tax->name . ' ' . $tax->value . '%' }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <div>
                    <span class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Discount Type</span>
                    <div class="mt-2 flex items-center gap-4">
                        <label class="flex items-center gap-2 text-sm font-semibold text-erp-text">
                            <input type="radio" name="percentage" value="percentage" checked class="text-erp focus:ring-erp"> Percentage
                        </label>
                        <label class="flex items-center gap-2 text-sm font-semibold text-erp-text">
                            <input type="radio" name="percentage" value="amount" class="text-erp focus:ring-erp"> Amount
                        </label>
                    </div>
                    <input type="text" id="discountValue" placeholder="Enter Discount" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Total Price</label>
                    <input type="text" id="totalPrice" placeholder="Enter Price" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Tax Amount</label>
                    <input type="text" id="itemTax" placeholder="Enter Price" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Discount Amount</label>
                    <input type="text" id="itemDiscount" placeholder="Enter Price" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Gross</label>
                    <input type="text" id="itemGross" placeholder="Enter Price" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="flex items-end">
                    <button type="button" id="add-order-item" class="h-10 w-full rounded-lg border border-erp bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Add Item</button>
                </div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Order Items Details</h2>
            </div>
            <div class="overflow-x-auto">
                <table id="itemsTable" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-4 py-3">S.No.</th>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Qty</th>
                            <th class="px-4 py-3">Total Amount</th>
                            <th class="px-4 py-3">Tax Rate</th>
                            <th class="px-4 py-3">Tax Amount</th>
                            <th class="px-4 py-3">Discount Type</th>
                            <th class="px-4 py-3">Discount Amount</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line"></tbody>
                </table>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-2 p-5 sm:items-end">
                <div class="flex w-full max-w-xs justify-between text-sm font-semibold text-erp-text sm:max-w-sm">
                    <span>Sub Total :</span><span id="subTotal">0.00</span>
                </div>
                <div class="flex w-full max-w-xs justify-between text-sm font-semibold text-erp-text sm:max-w-sm">
                    <span>Discount :</span><span id="totalDiscount">0.00</span>
                </div>
                <div class="flex w-full max-w-xs justify-between text-sm font-semibold text-erp-text sm:max-w-sm">
                    <span>Tax Amount :</span><span id="totalTax">0.00</span>
                </div>
                <div class="flex w-full max-w-xs justify-between border-t border-erp-line pt-2 text-base font-bold text-erp-ink sm:max-w-sm">
                    <span>Total Amount :</span><span id="totalAmount">0.00</span>
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="button" id="placeOrder" class="rounded-lg border border-erp bg-erp px-8 py-3 text-sm font-bold text-white transition hover:bg-erp-dark">Place Order</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';

        function debounce(fn, delay) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        }

        function setupAutocomplete(searchInputId, resultsId, hiddenInputId, url, extraParams, renderLabel) {
            const searchInput = document.getElementById(searchInputId);
            const results = document.getElementById(resultsId);
            const hiddenInput = document.getElementById(hiddenInputId);

            const search = debounce(function () {
                const q = searchInput.value.trim();
                if (q.length < 1) {
                    results.classList.add('hidden');
                    return;
                }
                const params = new URLSearchParams(Object.assign({ q }, extraParams()));
                fetch(url + '?' + params.toString())
                    .then(res => res.json())
                    .then(data => {
                        const items = data.items || [];
                        results.innerHTML = '';
                        if (items.length === 0) {
                            results.classList.add('hidden');
                            return;
                        }
                        items.forEach(item => {
                            const row = document.createElement('div');
                            row.className = 'cursor-pointer px-4 py-2 text-sm text-erp-text hover:bg-erp-soft';
                            row.textContent = renderLabel(item);
                            row.addEventListener('click', () => {
                                hiddenInput.value = item.id;
                                searchInput.value = renderLabel(item);
                                results.classList.add('hidden');
                                searchInput.dispatchEvent(new Event('itemselected', { detail: item }));
                            });
                            results.appendChild(row);
                        });
                        results.classList.remove('hidden');
                    });
            }, 300);

            searchInput.addEventListener('input', search);
            document.addEventListener('click', function (e) {
                if (!results.contains(e.target) && e.target !== searchInput) {
                    results.classList.add('hidden');
                }
            });
        }

        setupAutocomplete('customerSearch', 'customerResults', 'customerId', "{{ route('search-customer-by-names') }}", () => ({}), item => item.name);
        setupAutocomplete('productSearch', 'productResults', 'productId', "{{ route('search-inventory') }}", () => ({}), item => item.product_name + ' | ' + item.item_code);

        document.getElementById('branchId').addEventListener('change', function () {
            fetch("{{ url('get-terminals-and-salespersons') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ branchId: this.value })
            })
                .then(res => res.json())
                .then(result => {
                    if (result.status == 200) {
                        const terminalSelect = document.getElementById('terminalId');
                        const salespersonSelect = document.getElementById('salespersonId');
                        terminalSelect.innerHTML = '<option value="">Terminals</option>';
                        salespersonSelect.innerHTML = '<option value="">Sales Persons</option>';
                        result.terminals.forEach(value => {
                            terminalSelect.insertAdjacentHTML('beforeend', `<option value="${value.terminal_id}">${value.terminal_name}</option>`);
                        });
                        result.salesPersons.forEach(value => {
                            salespersonSelect.insertAdjacentHTML('beforeend', `<option value="${value.serviceprovideruser.user_id}">${value.provider_name}</option>`);
                        });
                    }
                });
        });

        document.getElementById('productSearch').addEventListener('itemselected', function () {
            const productId = document.getElementById('productId').value;
            document.getElementById('price').disabled = true;
            fetch("{{ url('get-price-of-product') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: productId })
            })
                .then(res => res.json())
                .then(result => {
                    document.getElementById('price').disabled = false;
                    if (result.status == 200) {
                        document.getElementById('price').value = result.price.retail_price;
                        document.getElementById('qty').value = 1;
                        document.getElementById('totalPrice').value = result.price.retail_price;
                        itemCalculation();
                    }
                });
        });

        function checkExistence(productId) {
            let result = false;
            document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                if (row.dataset.productId === productId) result = true;
            });
            return result;
        }

        document.getElementById('qty').addEventListener('change', function () {
            const price = parseFloat(document.getElementById('price').value) || 0;
            const qty = parseFloat(this.value) || 0;
            document.getElementById('totalPrice').value = price * qty;
            itemCalculation();
        });

        function itemCalculation() {
            const subTotal = document.getElementById('totalPrice').value;
            if (subTotal !== '') {
                const taxAmount = calculateTax(subTotal);
                const discountAmount = calculateDiscount(subTotal);
                const grossAmount = (parseFloat(subTotal) + parseFloat(taxAmount)) - parseFloat(discountAmount);
                document.getElementById('itemTax').value = taxAmount;
                document.getElementById('itemDiscount').value = discountAmount;
                document.getElementById('itemGross').value = Math.round(grossAmount);
            }
        }

        function calculateTax(subTotal) {
            const taxValue = document.getElementById('taxValue').value;
            if (taxValue !== '') {
                return Math.round(subTotal * (taxValue / 100));
            }
            return 0;
        }

        function calculateDiscount(subTotal) {
            const discountType = document.querySelector('input[name="percentage"]:checked').value;
            const discountValue = document.getElementById('discountValue').value;
            if (discountValue !== '') {
                if (discountType === 'percentage') {
                    return Math.round(subTotal * (discountValue / 100));
                }
                return discountValue;
            }
            return 0;
        }

        document.getElementById('taxValue').addEventListener('change', itemCalculation);
        document.getElementById('discountValue').addEventListener('change', itemCalculation);

        function emptyControls() {
            document.getElementById('productId').value = '';
            document.getElementById('productSearch').value = '';
            document.getElementById('qty').value = '';
            document.getElementById('taxValue').value = '';
            document.getElementById('discountValue').value = '';
            document.getElementById('totalPrice').value = '';
            document.getElementById('itemTax').value = '';
            document.getElementById('itemDiscount').value = '';
            document.getElementById('itemGross').value = '';
        }

        let rowCounter = 0;

        document.getElementById('add-order-item').addEventListener('click', function () {
            document.querySelectorAll('.item-message').forEach(el => el.innerHTML = '');

            const productId = document.getElementById('productId').value;
            const productName = document.getElementById('productSearch').value.trim();
            const qty = document.getElementById('qty').value;
            const price = document.getElementById('price').value;

            if (productId === '') {
                document.getElementById('productId_message').innerHTML = 'Please Select Product';
            } else if (qty === '') {
                document.getElementById('qty_message').innerHTML = 'Please Enter Qty';
            } else if (price === '') {
                document.getElementById('price_message').innerHTML = 'Please Enter Price';
            } else if (checkExistence(productId)) {
                alert('already added');
            } else {
                rowCounter++;
                const amount = qty * price;
                const taxRate = document.getElementById('taxValue').value;
                const taxAmount = document.getElementById('itemTax').value;
                const discountType = document.querySelector('input[name="percentage"]:checked').value;
                const discountAmount = document.getElementById('itemDiscount').value;
                const grossAmount = document.getElementById('itemGross').value;

                const tr = document.createElement('tr');
                tr.id = 'row' + rowCounter;
                tr.dataset.productId = productId;
                tr.innerHTML = `
                    <input type="hidden" value="${productId}" name="products[]">
                    <input type="hidden" value="${productName}" name="productnames[]">
                    <input type="hidden" value="${qty}" name="qty[]">
                    <input type="hidden" value="${price}" name="price[]">
                    <input type="hidden" value="${grossAmount}" name="amount[]">
                    <input type="hidden" value="${taxRate}" name="itemTaxRate[]">
                    <input type="hidden" value="${taxAmount}" name="itemTaxAmount[]">
                    <input type="hidden" value="${discountType}" name="itemDiscountType[]">
                    <input type="hidden" value="${discountAmount}" name="itemDiscountAmount[]">
                    <td class="px-4 py-3 text-erp-text">${rowCounter}</td>
                    <td class="px-4 py-3 font-semibold text-erp-ink">${productName}</td>
                    <td class="px-4 py-3 text-erp-text">${price}</td>
                    <td class="px-4 py-3 text-erp-text">${qty}</td>
                    <td class="px-4 py-3 text-erp-text">${amount}</td>
                    <td class="px-4 py-3 text-erp-text">${taxRate}</td>
                    <td class="px-4 py-3 text-erp-text">${taxAmount}</td>
                    <td class="px-4 py-3 text-erp-text">${discountType}</td>
                    <td class="px-4 py-3 text-erp-text">${discountAmount}</td>
                    <td class="px-4 py-3 text-erp-text">${grossAmount}</td>
                    <td class="px-4 py-3 text-right"><button type="button" class="font-bold text-rose-600 hover:text-rose-700" onclick="deleteItem(${rowCounter})">Delete</button></td>
                `;
                document.querySelector('#itemsTable tbody').appendChild(tr);
                calculateTotals();
                emptyControls();
            }
        });

        function calculateTotals() {
            let subTotal = 0, discountAmount = 0, taxAmount = 0, netTotal = 0;
            document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                subTotal += parseFloat(cells[4].textContent) || 0;
                discountAmount += parseFloat(cells[8].textContent) || 0;
                taxAmount += parseFloat(cells[6].textContent) || 0;
                netTotal += parseFloat(cells[9].textContent) || 0;
            });

            document.getElementById('subTotalAmount').value = subTotal;
            document.getElementById('taxAmount').value = taxAmount;
            document.getElementById('discountAmount').value = discountAmount;
            document.getElementById('netAmount').value = netTotal;

            document.getElementById('subTotal').textContent = subTotal.toLocaleString();
            document.getElementById('totalTax').textContent = taxAmount.toLocaleString();
            document.getElementById('totalDiscount').textContent = discountAmount.toLocaleString();
            document.getElementById('totalAmount').textContent = netTotal.toLocaleString();
        }

        function deleteItem(id) {
            document.getElementById('row' + id).remove();
            calculateTotals();
        }

        document.getElementById('placeOrder').addEventListener('click', function () {
            document.querySelectorAll('.message').forEach(el => el.innerHTML = '');

            const formData = new FormData();
            formData.append('customerId', document.getElementById('customerId').value);
            formData.append('orderTypeId', document.getElementById('orderTypeId').value);
            formData.append('paymentId', document.getElementById('paymentId').value);
            formData.append('branchId', document.getElementById('branchId').value);
            formData.append('terminalId', document.getElementById('terminalId').value);
            formData.append('salespersonId', document.getElementById('salespersonId').value);
            formData.append('subTotal', document.getElementById('subTotalAmount').value);
            formData.append('taxAmount', document.getElementById('taxAmount').value);
            formData.append('discountAmount', document.getElementById('discountAmount').value);
            formData.append('totalAmount', document.getElementById('netAmount').value);
            document.querySelectorAll('#itemsTable tbody input[type=hidden]').forEach(input => {
                formData.append(input.name, input.value);
            });

            const controls = document.querySelectorAll('#placeOrderForm input, #placeOrderForm select, #placeOrderForm button');
            controls.forEach(control => control.disabled = true);

            fetch("{{ url('place-order') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body: formData
            })
                .then(res => res.json())
                .then(result => {
                    if (result.status == 200) {
                        if (result.orderId) {
                            window.open("{{ url('order-detail') }}/" + result.orderId);
                        }
                        location.reload();
                    } else {
                        controls.forEach(control => control.disabled = false);
                    }
                })
                .catch(() => {
                    controls.forEach(control => control.disabled = false);
                });
        });
    </script>
@endpush
