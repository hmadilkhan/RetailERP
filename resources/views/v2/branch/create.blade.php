@extends('layouts.master-tailwind')

@section('title', 'Create Branch')
@section('page_title', 'Create Branch')
@section('page_subtitle', 'Create a new branch with company, location, contact, reporting, stock, and logo configuration.')

@section('content')
    <form method="POST" id="branchForm" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Branch Details</h2>
                <p class="mt-1 text-sm text-erp-mute">Required fields are branch name, address, PTCL, mobile, and email.</p>
            </div>

            <div class="grid gap-5 p-5 lg:grid-cols-12">
                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="company">Company</label>
                    <select name="company" id="company" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Company</option>
                        @foreach (($company ?? []) as $value)
                            <option value="{{ $value->company_id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="branchname">Branch Name</label>
                    <input type="text" name="branchname" id="branchname" value="{{ old('branchname') }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <p class="mt-1 text-xs text-rose-600" data-error-for="branchname"></p>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="code">Branch Code</label>
                    <input type="text" name="br_code" id="code" value="{{ old('br_code') }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="country">Country</label>
                    <select name="country" id="country" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Country</option>
                        @foreach (($country ?? []) as $value)
                            <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="city">City</label>
                    <select name="city" id="city" disabled class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp disabled:bg-slate-100">
                        <option value="">Select City</option>
                        @foreach (($city ?? []) as $value)
                            <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="email">Branch Email</label>
                    <input type="text" name="br_email" id="email"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <p class="mt-1 text-xs text-rose-600" data-error-for="br_email"></p>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="mobile">Mobile Number</label>
                    <input type="text" name="br_mobile" id="mobile"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <p class="mt-1 text-xs text-rose-600" data-error-for="br_mobile"></p>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="ptcl">PTCL Number</label>
                    <input type="text" name="br_ptcl" id="ptcl"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <p class="mt-1 text-xs text-rose-600" data-error-for="br_ptcl"></p>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="record_daily_stock">Record Daily Stock?</label>
                    <select name="record_daily_stock" id="record_daily_stock" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="1">Yes</option>
                        <option selected value="0">No</option>
                    </select>
                </div>

                <div class="lg:col-span-4">
                    <label class="text-sm font-bold text-erp-ink" for="report">Reports</label>
                    <select multiple name="report[]" id="report" class="mt-2 min-h-32 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        @foreach (($reports ?? []) as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-erp-mute">Hold Ctrl to select multiple reports.</p>
                </div>

                <div class="lg:col-span-5">
                    <label class="text-sm font-bold text-erp-ink" for="address">Branch Address</label>
                    <textarea name="br_address" id="address" rows="6"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                    <p class="mt-1 text-xs text-rose-600" data-error-for="br_address"></p>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="vdimg">Branch Logo</label>
                    <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                        <img id="vdpimg" src="{{ asset('assets/images/placeholder.jpg') }}" class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="Branch logo preview">
                        <input type="file" name="vdimg" id="vdimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
                        <p class="mt-1 text-xs text-rose-600" data-error-for="vdimg"></p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:justify-end">
                <a href="{{ url('/branches') }}" class="rounded-lg border border-erp-line px-4 py-2 text-center text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
                <button type="submit" id="btnsubmit" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Branch</button>
            </div>
        </section>
    </form>
@endsection

@push('scripts')
    <script>
        const branchForm = document.getElementById('branchForm');

        document.getElementById('country')?.addEventListener('change', function () {
            document.getElementById('city').disabled = this.value === '';
        });

        document.getElementById('vdimg')?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                document.getElementById('vdpimg').src = URL.createObjectURL(this.files[0]);
            }
        });

        branchForm?.addEventListener('submit', function (event) {
            event.preventDefault();
            clearErrors();

            const required = {
                branchname: document.getElementById('branchname'),
                br_address: document.getElementById('address'),
                br_ptcl: document.getElementById('ptcl'),
                br_mobile: document.getElementById('mobile'),
                br_email: document.getElementById('email')
            };

            let hasError = false;
            Object.entries(required).forEach(([field, input]) => {
                if (!input.value.trim()) {
                    hasError = true;
                    setError(field, 'Required field can not be blank.');
                }
            });

            if (hasError) {
                alert('Required fields are mandatory.');
                return;
            }

            submitBranch("{{ url('/submitbranch') }}", 'Branch Created Successfully!');
        });

        function submitBranch(url, successMessage) {
            const button = document.getElementById('btnsubmit');
            button.disabled = true;
            button.textContent = 'Saving...';

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: new FormData(branchForm)
            })
                .then(async response => {
                    if (response.status === 422) {
                        const payload = await response.json();
                        Object.entries(payload.errors || {}).forEach(([field, messages]) => setError(field, messages[0]));
                        throw new Error(payload.message || 'Required fields are mandatory.');
                    }

                    return response.text();
                })
                .then(response => {
                    if (response.trim() === '1') {
                        alert(successMessage);
                        window.location = "{{ url('/branches') }}";
                    } else if (response.trim() === '0') {
                        alert('Particular Branch Already Exist!');
                    } else {
                        alert('Unable to submit branch right now.');
                    }
                })
                .catch(error => alert(error.message || 'Unable to submit branch right now.'))
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'Create Branch';
                });
        }

        function setError(field, message) {
            const target = document.querySelector(`[data-error-for="${field}"]`);
            if (target) target.textContent = message;
        }

        function clearErrors() {
            document.querySelectorAll('[data-error-for]').forEach(item => item.textContent = '');
        }
    </script>
@endpush
