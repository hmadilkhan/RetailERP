@php
    $isEdit = ($mode ?? 'create') === 'edit';
    $customer = $customer ?? null;
    $supplier = $supplier ?? collect();
    $field = function ($name, $default = '') use ($customer) {
        return old($name, $customer->{$name} ?? $default);
    };
    $inputClass = 'mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
    $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
@endphp

<div class="space-y-6">
    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Profile Details</h2>
            <p class="mt-1 text-sm text-erp-mute">Keep contact, location, and account information easy to scan.</p>
        </div>

        <div class="grid gap-5 px-5 py-5 lg:grid-cols-12">
            <label class="block lg:col-span-4">
                <span class="{{ $labelClass }}">Customer Name</span>
                <input type="text" id="name" name="name" value="{{ $field('name') }}" class="{{ $inputClass }}" required>
            </label>

            <label class="block lg:col-span-4">
                <span class="{{ $labelClass }}">Mobile</span>
                <input type="text" id="mobile" name="mobile" value="{{ $field('mobile') }}" class="{{ $inputClass }}" onkeypress="return restrictAlphabets(event)" required>
            </label>

            <label class="block lg:col-span-4">
                <span class="{{ $labelClass }}">Contact No</span>
                <input type="text" id="phone" name="phone" value="{{ $field('phone') }}" class="{{ $inputClass }}" onkeypress="return restrictAlphabets(event)">
            </label>

            <label class="block lg:col-span-4">
                <span class="{{ $labelClass }}">CNIC</span>
                <input type="text" id="nic" name="nic" value="{{ $field('nic') }}" class="{{ $inputClass }}" onkeypress="return restrictAlphabets(event)">
            </label>

            <label class="block lg:col-span-4">
                <span class="{{ $labelClass }}">Country</span>
                <select name="country" id="country" class="{{ $inputClass }} v2-select2" data-placeholder="Select Country" required>
                    <option value="">Select Country</option>
                    @foreach($country as $value)
                        <option value="{{ $value->country_id }}" {{ (string) old('country', $customer->country_id ?? '') === (string) $value->country_id || ($isEdit && isset($customer->country_name) && $value->country_name == $customer->country_name) ? 'selected' : '' }}>
                            {{ $value->country_name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block lg:col-span-4">
                <span class="{{ $labelClass }}">City</span>
                <select name="city" id="city" class="{{ $inputClass }} v2-select2" data-placeholder="Select City" required>
                    <option value="">Select City</option>
                    @foreach($city as $value)
                        <option value="{{ $value->city_id }}" {{ (string) old('city', $customer->city_id ?? '') === (string) $value->city_id || ($isEdit && isset($customer->city_name) && $value->city_name == $customer->city_name) ? 'selected' : '' }}>
                            {{ $value->city_name }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block lg:col-span-3">
                <span class="{{ $labelClass }}">Credit Limit</span>
                <input type="text" id="creditlimit" name="creditlimit" value="{{ old('creditlimit', $customer->credit_limit ?? '') }}" class="{{ $inputClass }}" onkeypress="return restrictAlphabets(event)">
            </label>

            <label class="block lg:col-span-3">
                <span class="{{ $labelClass }}">Discount</span>
                <input type="text" id="discount" name="discount" value="{{ $field('discount') }}" class="{{ $inputClass }}" onkeypress="return restrictAlphabets(event)">
            </label>

            <label class="block lg:col-span-3">
                <span class="{{ $labelClass }}">Email</span>
                <input type="email" id="email" name="email" value="{{ $field('email') }}" class="{{ $inputClass }}">
            </label>

            @unless($isEdit)
                <label class="block lg:col-span-3">
                    <span class="{{ $labelClass }}">Opening Balance</span>
                    <input type="text" id="ob" name="ob" value="{{ old('ob', 0) }}" class="{{ $inputClass }}" onkeypress="return restrictAlphabets(event)">
                </label>
            @endunless

            <label class="block lg:col-span-8">
                <span class="{{ $labelClass }}">Address</span>
                <textarea name="address" id="address" rows="5" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" required>{{ $field('address') }}</textarea>
            </label>

            <div class="lg:col-span-4">
                <span class="{{ $labelClass }}">Profile Picture</span>
                <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-erp-soft p-4">
                    <img id="vdpimg" src="{{ $isEdit ? asset('assets/images/customers/'.(!empty($customer->image) ? $customer->image : 'placeholder.jpg')) : asset('assets/images/placeholder.jpg') }}" class="h-32 w-32 rounded-lg object-cover ring-1 ring-erp-line" alt="Customer image">
                    <input type="file" name="vdimg" id="vdimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-4 file:rounded-lg file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Customer Settings</h2>
        </div>

        <div class="grid gap-5 px-5 py-5 md:grid-cols-3">
            <label class="block">
                <span class="{{ $labelClass }}">Customer Type</span>
                <select onchange="CustomerType(this)" name="customer_type" class="{{ $inputClass }}">
                    <option value="">Select</option>
                    <option value="1" {{ (string) $field('customer_type') === '1' ? 'selected' : '' }}>Retailer</option>
                    <option value="2" {{ (string) $field('customer_type') === '2' ? 'selected' : '' }}>Supplier</option>
                </select>
            </label>

            <label class="block">
                <span class="{{ $labelClass }}">Payment Type</span>
                <select name="payment_type" class="{{ $inputClass }}">
                    <option value="">Select</option>
                    <option value="1" {{ (string) $field('payment_type') === '1' ? 'selected' : '' }}>Cash</option>
                    <option value="2" {{ (string) $field('payment_type') === '2' ? 'selected' : '' }}>Credit</option>
                </select>
            </label>

            <label class="block">
                <span class="{{ $labelClass }}">Area</span>
                <input type="text" name="customer_area" value="{{ $field('customer_area') }}" class="{{ $inputClass }}" placeholder="Area">
            </label>
        </div>
    </section>

    <section id="hideSupplierDiv" class="rounded-lg border border-erp-line bg-white shadow-sm {{ (string) $field('customer_type') === '2' ? '' : 'hidden' }}">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Supplier Detail</h2>
                <p class="mt-1 text-sm text-erp-mute">Add delivery or supplier-specific address notes.</p>
            </div>
            <button type="button" onclick="return clone_field()" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Add Row</button>
        </div>

        <div class="space-y-4 px-5 py-5">
            @foreach($supplier as $val)
                <div class="grid gap-4 rounded-lg border border-erp-line bg-erp-soft p-4 md:grid-cols-3">
                    <input type="text" disabled value="{{ $val->area }}" class="{{ $inputClass }}">
                    <input type="text" disabled value="{{ $val->street }}" class="{{ $inputClass }}">
                    <textarea disabled rows="2" class="mt-2 w-full rounded-lg border-erp-line text-sm">{{ $val->comment }}</textarea>
                </div>
            @endforeach
            <div id="inputfieldClone" class="space-y-4"></div>
        </div>
    </section>

    <template id="supplierRowTemplate">
        <div class="supplier-row grid gap-4 rounded-lg border border-erp-line bg-erp-soft p-4 md:grid-cols-[1fr_1fr_1fr_auto]">
            <input type="text" name="area[]" class="{{ $inputClass }}" placeholder="Area" required>
            <input type="text" name="street_address[]" class="{{ $inputClass }}" placeholder="Street Address" required>
            <textarea name="comment[]" rows="2" class="mt-2 w-full rounded-lg border-erp-line text-sm" placeholder="Comment"></textarea>
            <button type="button" class="remove_row self-end rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700">Remove</button>
        </div>
    </template>

    @if($isEdit)
        <input type="hidden" name="created_at" id="created_at" value="{{ $customer->created_at }}">
        <input type="hidden" name="custid" id="custid" value="{{ $customer->id }}">
        <input type="hidden" name="custimage" id="custimage" value="{{ $customer->image }}">
    @else
        <input type="hidden" id="hidd_amt" name="hidd_amt">
        <input type="hidden" id="hidd_id" name="hidd_id">
    @endif

    <div class="flex flex-wrap justify-end gap-3">
        <a href="{{ route('customer.index') }}" class="rounded-lg border border-erp-line bg-white px-5 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
        <button type="submit" class="rounded-lg border border-erp bg-erp px-6 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">{{ $submitLabel }}</button>
    </div>
</div>
