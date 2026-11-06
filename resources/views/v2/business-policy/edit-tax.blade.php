@extends('layouts.master-tailwind')

@section('title', 'Business Policy')
@section('page_title', 'Edit Tax Rule')
@section('page_subtitle', 'Update the tax head, rate, or where it is available.')

@php
    $tax = $details[0];
    $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
    $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
    $hasOld = count(old()) > 0;
    $purchaseChecked = $hasOld ? old('purchase') : $tax->show_in_purchase == 1;
    $posChecked = $hasOld ? old('pos') : $tax->show_in_pos == 1;
@endphp

@section('content')
    <form method="POST" action="{{ url('/update-tax') }}" id="editTaxForm" novalidate>
        @csrf
        <input type="hidden" name="taxid" value="{{ $tax->id }}">

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-erp-line bg-white px-5 py-4 shadow-sm">
            <div>
                <h2 class="text-base font-bold text-erp-ink">{{ $tax->name }}</h2>
                <p class="mt-1 text-sm text-erp-mute">Rule No {{ $tax->id }} &middot; Fields marked with an asterisk are required.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ url('/BusinessPolicy') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
                <button type="submit" id="btnsubmit" class="rounded-lg border border-erp bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Update Rule</button>
            </div>
        </div>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Tax Details</h3>
                <p class="mt-1 text-sm text-erp-mute">Name and rate of the tax.</p>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <label class="block">
                    <span class="{{ $labelClass }}">Tax Head Name <span class="text-rose-500">*</span></span>
                    <input type="text" name="taxname" id="taxname" value="{{ old('taxname', $tax->name) }}" maxlength="100" class="{{ $inputClass }}">
                    <span id="taxnameError" class="mt-1 block text-xs font-semibold text-rose-600">@error('taxname'){{ $message }}@enderror</span>
                </label>
                <label class="block">
                    <span class="{{ $labelClass }}">Tax Percentage <span class="text-rose-500">*</span></span>
                    <div class="relative">
                        <input type="number" name="taxpercentage" id="taxpercentage" value="{{ old('taxpercentage', $tax->value) }}" min="0" max="100" step="0.01" inputmode="decimal" class="{{ $inputClass }} pr-9">
                        <span class="pointer-events-none absolute inset-y-0 right-3 mt-2 flex h-10 items-center text-sm font-bold text-erp-mute">%</span>
                    </div>
                    <span id="taxpercentageError" class="mt-1 block text-xs font-semibold text-rose-600">@error('taxpercentage'){{ $message }}@enderror</span>
                </label>
            </div>
        </section>

        <section class="mt-6 rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Availability</h3>
                <p class="mt-1 text-sm text-erp-mute">Choose where this tax can be applied.</p>
            </div>
            <div class="flex flex-wrap gap-6 p-5">
                <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" name="purchase" value="on" {{ $purchaseChecked ? 'checked' : '' }} class="rounded border-erp-line text-erp focus:ring-erp">
                    Show In Purchase
                </label>
                <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" name="pos" value="on" {{ $posChecked ? 'checked' : '' }} class="rounded border-erp-line text-erp focus:ring-erp">
                    Show In POS
                </label>
            </div>
        </section>
    </form>
@endsection

@push('scripts')
    <script>
        document.getElementById('editTaxForm').addEventListener('submit', function (event) {
            const name = document.getElementById('taxname');
            const pct = document.getElementById('taxpercentage');
            const nameError = document.getElementById('taxnameError');
            const pctError = document.getElementById('taxpercentageError');
            const rate = parseFloat(pct.value);
            let valid = true;

            nameError.textContent = '';
            pctError.textContent = '';

            if (name.value.trim() === '') {
                nameError.textContent = 'Tax head name is required.';
                valid = false;
            }
            if (pct.value.trim() === '' || isNaN(rate) || rate < 0 || rate > 100) {
                pctError.textContent = 'Enter a percentage between 0 and 100.';
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                return;
            }

            const button = document.getElementById('btnsubmit');
            button.disabled = true;
            button.classList.add('opacity-60', 'cursor-not-allowed');
        });
    </script>
@endpush
