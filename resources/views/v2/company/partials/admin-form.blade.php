@php
    $isEdit = $mode === 'edit';
    $logo = $isEdit && !empty($record->logo) ? asset('storage/images/company/' . $record->logo) : asset('storage/images/placeholder.jpg');
    $posBg = $isEdit && !empty($record->pos_background) ? asset('storage/images/pos-background/' . $record->pos_background) : asset('storage/images/placeholder.jpg');
    $orderDisplay = $isEdit && !empty($record->order_calling_display_image) ? asset('storage/images/order-calling/' . $record->order_calling_display_image) : asset('storage/images/placeholder.jpg');
@endphp

<section class="rounded-lg border border-erp-line bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-bold text-erp-ink">Company Details</h2>
            <p class="mt-1 text-sm text-erp-mute">Required fields are marked by validation and match the existing controller.</p>
        </div>
        <a href="{{ route('company.index') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back to List</a>
    </div>

    <div class="grid gap-5 p-5 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="companyname">Company Name</label>
            <input class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="companyname" id="companyname" value="{{ old('companyname', $isEdit ? $record->name : '') }}">
            @error('companyname')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="country">Country</label>
            <select name="country" id="country" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Country</option>
                @foreach (($country ?? []) as $value)
                    <option value="{{ $value->country_id }}" {{ (string) old('country', $isEdit ? $record->country_id : '') === (string) $value->country_id ? 'selected' : '' }}>{{ $value->country_name }}</option>
                @endforeach
            </select>
            @error('country')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="city">City</label>
            <select name="city" id="city" {{ old('country', $isEdit ? $record->country_id : '') ? '' : 'disabled' }} class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp disabled:bg-slate-100">
                <option value="">Select City</option>
                @foreach (($city ?? []) as $value)
                    <option value="{{ $value->city_id }}" {{ (string) old('city', $isEdit ? $record->city_id : '') === (string) $value->city_id ? 'selected' : '' }}>{{ $value->city_name }}</option>
                @endforeach
            </select>
            @error('city')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-3">
            <label class="text-sm font-bold text-erp-ink" for="company_email">Company Email</label>
            <input class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="company_email" id="company_email" value="{{ old('company_email', $isEdit ? $record->email : '') }}">
            @error('company_email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="lg:col-span-3">
            <label class="text-sm font-bold text-erp-ink" for="company_mobile">Mobile Number</label>
            <input class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="company_mobile" id="company_mobile" value="{{ old('company_mobile', $isEdit ? $record->mobile_contact : '') }}">
            @error('company_mobile')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-3">
            <label class="text-sm font-bold text-erp-ink" for="whatsapp_number">WhatsApp Number</label>
            <input class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $isEdit ? ($record->whatsapp_number ?? '') : '') }}">
            @error('whatsapp_number')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="lg:col-span-3">
            <label class="text-sm font-bold text-erp-ink" for="company_ptcl">PTCL Number</label>
            <input class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="company_ptcl" id="company_ptcl" value="{{ old('company_ptcl', $isEdit ? $record->ptcl_contact : '') }}">
            @error('company_ptcl')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="currency">Currency</label>
            <select name="currency" id="currency" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Currency</option>
                @foreach (($currencies ?? []) as $currency)
                    <option value="{{ $currency->name }}" {{ old('currency', $currencyname ?? '') == $currency->name ? 'selected' : '' }}>{{ $currency->name }}</option>
                @endforeach
            </select>
            @error('currency')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="package">Package</label>
            <select name="package" id="package" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                <option value="">Select Package</option>
                @foreach (($packages ?? []) as $package)
                    <option value="{{ $package->id }}" {{ (string) old('package', $isEdit ? $record->package_id : '') === (string) $package->id ? 'selected' : '' }}>{{ $package->name }}</option>
                @endforeach
            </select>
            @error('package')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        @if ($isEdit)
            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="permanent_close">Permanent Close</label>
                <select name="permanent_close" id="permanent_close" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="0" {{ (string) old('permanent_close', $record->permanent_close ?? 0) === '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ (string) old('permanent_close', $record->permanent_close ?? 0) === '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
        @endif

        <div class="lg:col-span-12">
            <label class="text-sm font-bold text-erp-ink" for="company_address">Company Address</label>
            <textarea name="company_address" id="company_address" rows="5" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ old('company_address', $isEdit ? $record->address : '') }}</textarea>
            @error('company_address')<p class="mt-1 text-xs text-rose-600">Required field can not be blank.</p>@enderror
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="vdimg">Company Logo</label>
            <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                <img id="vdpimg" src="{{ $logo }}" class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="Company logo preview">
                <input type="file" name="vdimg" id="vdimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
            </div>
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="posbgimg">POS Background</label>
            <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                <img id="posbimg" src="{{ $posBg }}" class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="POS background preview">
                <input type="file" name="posbgimg" id="posbgimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
            </div>
        </div>

        <div class="lg:col-span-4">
            <label class="text-sm font-bold text-erp-ink" for="ordercallingbgimg">Order Calling Display</label>
            <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                <img id="previewordercallingbgimg" src="{{ $orderDisplay }}" class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="Order calling display preview">
                <input type="file" name="ordercallingbgimg" id="ordercallingbgimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:justify-end">
        <a href="{{ route('company.index') }}" class="rounded-lg border border-erp-line px-4 py-2 text-center text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
        <button type="submit" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">{{ $submitLabel }}</button>
    </div>
</section>
