@php
    $recipy = $isEdit ? ($details[0] ?? null) : null;
    $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
    $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
@endphp

<input type="hidden" id="update_id" name="update_id">
<input type="hidden" id="hidd_id" name="hidd_id" value="{{ $isEdit ? $recipy->recipy_id : '' }}">
<input type="hidden" id="productmode" name="productmode">

<div class="space-y-6">
    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Finished Good</h2>
            <p class="mt-1 text-sm text-erp-mute">Choose the recipe's output product.</p>
        </div>
        <div class="p-5">
            <label class="block">
                <span class="{{ $labelClass }}">Select Finished Good</span>
                <select class="{{ $inputClass }}" id="finished" name="finished">
                    <option value="">Select Finished Good</option>
                    @if($products)
                        @foreach($products as $value)
                            <option {{ $isEdit && $value->id == $recipy->product_id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->department_name." | ".$value->item_code." | ".$value->product_name }}</option>
                        @endforeach
                    @endif
                </select>
            </label>
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Select Inventory</h2>
            <p class="mt-1 text-sm text-erp-mute">Add raw materials used to build this recipe.</p>
        </div>
        <div class="space-y-4 p-5">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <label class="block xl:col-span-2">
                    <span class="{{ $labelClass }}">Select Product</span>
                    <select class="{{ $inputClass }}" id="product" name="product" onchange="getuom()">
                        <option value="">Select Product</option>
                        @if($raw)
                            @foreach($raw as $value)
                                <option value="{{ $value->id }}">{{ $value->department_name." | ".$value->item_code." | ".$value->product_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Unit of Measure</span>
                    <input type="text" readonly name="uom" placeholder="kg" id="uom" class="{{ $inputClass }} bg-erp-soft">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Rate</span>
                    <input type="text" {{ $isEdit ? 'readonly' : '' }} name="rate" placeholder="0" id="rate" class="{{ $inputClass }} {{ $isEdit ? 'bg-erp-soft' : '' }}">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Weight | Quantity</span>
                    <input type="text" readonly name="weight" placeholder="0" id="weight" class="{{ $inputClass }} bg-erp-soft">
                </label>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                <label class="block">
                    <span class="{{ $labelClass }}">Used in DineIn</span>
                    <select class="{{ $inputClass }}" id="dinein" name="dinein">
                        <option value="">Select DineIn</option>
                        <option value="1">YES</option>
                        <option {{ !$isEdit ? 'selected' : '' }} value="0">NO</option>
                    </select>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Packet Quantity</span>
                    <input type="number" min="0" placeholder="0" name="itemqty" id="itemqty" class="{{ $inputClass }}" onchange="qty_change()">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Cost</span>
                    <input type="text" readonly placeholder="0" name="cost" id="cost" class="{{ $inputClass }} bg-erp-soft">
                </label>
                <div class="flex items-end">
                    <button type="button" id="btnSubmit" class="h-10 w-full rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Add Item</button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-erp-line">
                <table id="item_table" class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Amount</th>
                            @if($isEdit)
                                <th class="px-4 py-3">Used In DineIn</th>
                            @endif
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line"></tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Costing Calculations</h2>
            <p class="mt-1 text-sm text-erp-mute">Ingredient cost is computed automatically; add packing and infrastructure cost.</p>
        </div>
        <div class="space-y-4 p-5">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <label class="block">
                    <span class="{{ $labelClass }}">Ingredients Cost</span>
                    <input type="number" readonly min="0" value="{{ $isEdit ? $recipy->ingredients_cost : 0 }}" id="ic" name="ic" class="{{ $inputClass }} bg-erp-soft">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Packing Cost</span>
                    <input type="number" min="0" value="{{ $isEdit ? $recipy->material_cost : 0 }}" id="pc" name="pc" class="{{ $inputClass }}" onchange="getInfraCost()">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Infra-Structure Cost</span>
                    <input type="number" min="0" value="{{ $isEdit ? $recipy->infrastructure_cost : 0 }}" id="infra" name="infra" class="{{ $inputClass }}" onchange="getInfraCost()">
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Total Cost</span>
                    <input type="number" readonly min="0" value="{{ $isEdit ? $recipy->total_cost : 0 }}" id="totalCost" name="totalCost" class="{{ $inputClass }} bg-erp-soft font-bold">
                </label>
            </div>

            <div class="flex justify-end border-t border-erp-line pt-4">
                <button type="button" id="btnFinalSubmit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">{{ $isEdit ? 'Update Job Order' : 'Submit Job Order' }}</button>
            </div>
        </div>
    </section>
</div>

<script>
    const csrfToken = '{{ csrf_token() }}';
    const isEditMode = @json($isEdit);
    let mode = 'insert';
    let count = 0;

    function getuom() {
        const productId = document.getElementById('product').value;
        if (!productId) return;
        fetch("{{ url('getunitofmessaure') }}?productid=" + encodeURIComponent(productId))
            .then(res => res.json())
            .then(resp => {
                document.getElementById('uom').value = resp[0].name;
                document.getElementById('rate').value = resp[0].retail_price;
                document.getElementById('weight').value = resp[0].weight_qty;
                document.getElementById('productmode').value = resp[0].product_mode;
            });
    }

    function qty_change() {
        const qty = parseFloat(document.getElementById('weight').value);
        const price = parseFloat(document.getElementById('rate').value);
        const usageqty = parseFloat(document.getElementById('itemqty').value);
        let total = (price / qty) * usageqty;
        total = Math.round(total * 100) / 100;
        document.getElementById('cost').value = total;
    }

    function emptyControls() {
        document.getElementById('update_id').value = '';
        document.getElementById('product').value = '';
        document.getElementById('itemqty').value = '';
        document.getElementById('cost').value = '';
        document.getElementById('productmode').value = '';
        document.getElementById('dinein').value = '';
    }

    @if(!$isEdit)
    document.getElementById('finished').addEventListener('change', function () {
        const finishedId = this.value;
        if (!finishedId) return;

        fetch("{{ url('/chk-recipy-exists') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ id: finishedId })
        })
            .then(res => res.json())
            .then(result => {
                if (result > 0) {
                    if (!confirm('Recipy Already Exists!! Do you want to Make it Again??!')) return;

                    fetch("{{ url('/createagain-joborder') }}?id=" + encodeURIComponent(finishedId))
                        .then(res => res.json())
                        .then(resp => {
                            if (resp && resp[0]) {
                                document.getElementById('hidd_id').value = resp[0].recipy_id;

                                fetch("{{ url('/inactiveoldecipy') }}", {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                                    body: JSON.stringify({ recipyid: document.getElementById('hidd_id').value })
                                })
                                    .then(res => res.json())
                                    .then(resp => {
                                        if (resp) {
                                            alert('Old Recipy Deleted Successfully!!');
                                            document.getElementById('hidd_id').value = '';
                                        }
                                    });
                            }
                        });
                }
            });
    });
    @endif

    document.getElementById('btnSubmit').addEventListener('click', function () {
        count++;
        if (!document.getElementById('finished').value) { alert('Finish Good is required'); return; }
        if (!document.getElementById('product').value) { alert('Product is required'); return; }
        if (document.getElementById('rate').value == 0) { alert('Rate is empty or Zero!!'); return; }
        if (document.getElementById('itemqty').value == 0 || !document.getElementById('itemqty').value) { alert('Packet Quantity is required'); return; }

        if (mode === 'insert') {
            const formData = new URLSearchParams();
            formData.append('jobid', document.getElementById('hidd_id').value);
            formData.append('id', document.getElementById('finished').value);
            formData.append('count', count);

            fetch("{{ url('/add-job') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: formData.toString()
            })
                .then(res => res.json())
                .then(result => {
                    if (result) {
                        document.getElementById('hidd_id').value = result;
                    }

                    const subFormData = new URLSearchParams();
                    subFormData.append('id', document.getElementById('hidd_id').value);
                    subFormData.append('itemid', document.getElementById('product').value);
                    subFormData.append('usage', document.getElementById('itemqty').value);
                    subFormData.append('amount', document.getElementById('cost').value);
                    subFormData.append('dineIn', document.getElementById('dinein').value);
                    subFormData.append('productmode', document.getElementById('productmode').value);

                    fetch("{{ url('/add-sub-job') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                        body: subFormData.toString()
                    })
                        .then(res => res.json())
                        .then(result => {
                            document.getElementById('product').value = '';
                            document.getElementById('uom').value = '';
                            document.getElementById('rate').value = '';
                            document.getElementById('weight').value = '';
                            document.getElementById('itemqty').value = '';

                            if (result == 2) {
                                alert('Product already exists');
                            } else {
                                getDetails();
                                getCosting();
                                if (isEditMode) emptyControls();
                            }
                        });
                });
        } else {
            const formData = new URLSearchParams();
            formData.append('updateid', document.getElementById('update_id').value);
            formData.append('itemid', document.getElementById('product').value);
            formData.append('usage', document.getElementById('itemqty').value);
            formData.append('amount', document.getElementById('cost').value);
            formData.append('dineIn', document.getElementById('dinein').value);
            formData.append('productmode', document.getElementById('productmode').value);

            fetch("{{ url('/item-update') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
                body: formData.toString()
            })
                .then(() => {
                    alert('Updated Successfully!');
                    mode = 'insert';
                    getDetails();
                    getCosting();
                    if (isEditMode) emptyControls();
                });
        }
    });

    function getDetails() {
        fetch("{{ url('/load-job') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
            body: 'jobid=' + encodeURIComponent(document.getElementById('hidd_id').value)
        })
            .then(res => res.json())
            .then(result => {
                const tbody = document.querySelector('#item_table tbody');
                tbody.innerHTML = '';
                result.forEach(value => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-4 py-3 text-erp-text">${value.product_name}</td>
                        <td class="px-4 py-3 text-erp-text">${value.usage_qty}</td>
                        <td class="px-4 py-3 text-erp-text">${value.cost}</td>
                        ${isEditMode ? `<td class="px-4 py-3 text-erp-text">${value.used_in_dinein == 1 ? 'YES' : 'No'}</td>` : ''}
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <button type="button" class="font-bold text-erp-dark hover:text-erp" onclick="updateItem(${value.recipy_details_id}, ${value.item_id}, ${value.mode_id}, ${value.usage_qty}, ${value.cost}, ${value.used_in_dinein})">Edit</button>
                                <button type="button" class="font-bold text-rose-600 hover:text-rose-700" onclick="deleteItem(${value.recipy_details_id}, ${value.recipy_id})">Delete</button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
    }

    function updateItem(id, itemid, productmode, qty, amount, usedIn) {
        mode = 'update';
        document.getElementById('product').value = itemid;
        document.getElementById('update_id').value = id;
        document.getElementById('itemqty').value = qty;
        document.getElementById('cost').value = amount;
        document.getElementById('dinein').value = usedIn;
    }

    function deleteItem(id, recipyid) {
        if (!confirm('Do you want to Delete!')) return;

        fetch("{{ url('/item-delete') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ id, recipyid })
        })
            .then(res => res.text())
            .then(resp => {
                if (resp == 1) {
                    alert('Item Deleted Successfully.');
                    getDetails();
                    getCosting();
                } else {
                    alert('Recipy Deleted Successfully.');
                    window.location = "{{ url('/joborder') }}";
                }
            });
    }

    function getCosting() {
        fetch("{{ url('/calculate-cost') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
            body: 'jobid=' + encodeURIComponent(document.getElementById('hidd_id').value)
        })
            .then(res => res.json())
            .then(result => {
                document.getElementById('ic').value = result;
                getInfraCost();
            });
    }

    function getInfraCost() {
        const total = parseFloat(document.getElementById('ic').value || 0) + parseFloat(document.getElementById('infra').value || 0) + parseFloat(document.getElementById('pc').value || 0);
        document.getElementById('totalCost').value = total;
    }

    document.getElementById('btnFinalSubmit').addEventListener('click', function () {
        const formData = new URLSearchParams();
        @if($isEdit)
            formData.append('recipyid', document.getElementById('hidd_id').value);
            formData.append('ic', document.getElementById('ic').value);
            formData.append('pc', document.getElementById('pc').value);
            formData.append('infra', document.getElementById('infra').value);
            formData.append('total', document.getElementById('totalCost').value);
        @else
            formData.append('jobid', document.getElementById('hidd_id').value);
            formData.append('ic', document.getElementById('ic').value);
            formData.append('pc', document.getElementById('pc').value);
            formData.append('infra', document.getElementById('infra').value);
            formData.append('totalcost', document.getElementById('totalCost').value);
        @endif

        fetch("{{ $isEdit ? url('/account-update') : url('/account-add') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
            body: formData.toString()
        })
            .then(res => res.json())
            .then(result => {
                if (result == 1) {
                    window.location = "{{ url('/joborder') }}";
                }
            });
    });

    @if($isEdit)
    getDetails();
    @endif
</script>
