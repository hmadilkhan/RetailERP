@extends('layouts.master-tailwind')

@section('title', 'Create Bank Account')
@section('page_title', 'Create Bank Account')
@section('page_subtitle', 'Create a branch bank account and optionally show it on a website.')

@section('content')
    @php
        $bankCollection = collect($getbank ?? []);
        $branchCollection = collect($getbranches ?? []);
        $websiteCollection = collect($website ?? []);
    @endphp

    <div class="space-y-6">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Account Details</h2>
                    <p class="mt-1 text-sm text-erp-mute">Fill the account profile, bank, branch, type, and image.</p>
                </div>
                <a href="{{ url('/view-accounts') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
            </div>

            <form method="post" action="{{ url('createaccount') }}" enctype="multipart/form-data" class="grid gap-5 p-5 lg:grid-cols-12">
                @csrf
                @method('post')

                <label class="block lg:col-span-4">
                    <span class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">
                        Select Bank
                        <button type="button" data-modal-target="bank-modal" class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs text-emerald-700">Add</button>
                    </span>
                    <select class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Bank" id="bank" name="bank">
                        <option value="">Select Bank</option>
                        @foreach($bankCollection as $value)
                            <option value="{{ $value->bank_id }}">{{ $value->bank_name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block lg:col-span-4">
                    <span class="flex items-center justify-between text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">
                        Select Branch
                        <button type="button" data-modal-target="branch-modal" class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs text-emerald-700">Add</button>
                    </span>
                    <select class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Branch" id="branch" name="branch">
                        <option value="">Select Branch</option>
                        @foreach($branchCollection as $value)
                            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block lg:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Account Title</span>
                    <input class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="accountitle" id="accountitle" required>
                </label>

                <label class="block lg:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Account Number</span>
                    <input class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="number" name="accountno" id="accountno" placeholder="0000001123456702" required>
                </label>

                <label class="block lg:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Account Type</span>
                    <input class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" type="text" name="accounttype" id="accounttype" placeholder="Current | Saving" required>
                </label>

                @if($websiteCollection->isNotEmpty())
                    <label class="block lg:col-span-3">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                        <select class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" data-placeholder="Select Website" id="website" name="website">
                            <option value="">Select Website</option>
                            @foreach($websiteCollection as $value)
                                <option value="{{ Crypt::encrypt($value->id) }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <div class="lg:col-span-{{ $websiteCollection->isNotEmpty() ? '2' : '5' }}">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Image</span>
                    <div class="mt-2 flex items-center gap-4">
                        <img id="simg" src="{{ asset('assets/images/placeholder.jpg') }}" class="h-20 w-20 rounded-lg object-cover ring-1 ring-slate-200" alt="Account preview">
                        <input type="file" name="image" id="image" accept="image/*" class="block w-full text-sm text-erp-text file:mr-4 file:rounded-lg file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-erp-dark">
                    </div>
                </div>

                <div class="flex items-end lg:col-span-12">
                    <button type="submit" class="h-10 rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Submit Account Details</button>
                </div>
            </form>
        </section>
    </div>

    @include('v2.accounts.partials.bank-account-modals', ['getbank' => $bankCollection])
@endsection

@push('scripts')
    <script>
        function setSelectOptions(selectId, records, valueKey, textKey, placeholder) {
            const select = document.getElementById(selectId);
            select.innerHTML = '<option value="">' + placeholder + '</option>';
            records.forEach(item => select.add(new Option(item[textKey], item[valueKey])));
            if (window.jQuery) jQuery(select).trigger('change.select2');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => openModal(button.dataset.modalTarget));
        });

        document.getElementById('image').addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = event => document.getElementById('simg').src = event.target.result;
            reader.readAsDataURL(file);
        });

        function addbank() {
            const data = new FormData();
            data.append('_token', "{{ csrf_token() }}");
            data.append('bankname', document.getElementById('bankname').value);
            fetch("{{ url('/submitbankdetails') }}", { method: 'POST', body: data })
                .then(response => response.json())
                .then(result => {
                    if (result !== 0) {
                        setSelectOptions('bank', result, 'bank_id', 'bank_name', 'Select Bank');
                        setSelectOptions('bankmodal', result, 'bank_id', 'bank_name', 'Select Bank');
                        closeModal('bank-modal');
                    } else {
                        alert('Bank already exists.');
                    }
                });
        }

        function addbranch() {
            const data = new FormData();
            data.append('_token', "{{ csrf_token() }}");
            data.append('bankname', document.getElementById('bankmodal').selectedOptions[0]?.text || '');
            data.append('branchname', document.getElementById('branchname').value);
            data.append('bank_id', document.getElementById('bankmodal').value);
            fetch("{{ url('/submitbankdetails') }}", { method: 'POST', body: data })
                .then(response => response.json())
                .then(result => {
                    if (result !== 0) {
                        setSelectOptions('branch', result, 'branch_id', 'branch_name', 'Select Branch');
                        closeModal('branch-modal');
                    } else {
                        alert('Branch already exists.');
                    }
                });
        }
    </script>
@endpush
