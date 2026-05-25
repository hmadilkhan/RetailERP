@extends('layouts.master-tailwind')

@section('title', 'Edit Branch')
@section('page_title', 'Edit Branch')
@section('page_subtitle', 'Update branch profile, contact details, reports, daily stock behavior, and logo.')

@section('content')
    <form method="POST" id="branchForm" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" id="imagename" name="imagename" value="{{ $details[0]->branch_logo }}">
        <input type="hidden" name="br_id" id="br_id" value="{{ $details[0]->branch_id }}">
        <input type="hidden" name="br_old_image" id="br_old_image" value="{{ $details[0]->branch_logo }}">

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">{{ $details[0]->branch_name }}</h2>
                    <p class="mt-1 text-sm text-erp-mute">Branch ID #{{ $details[0]->branch_id }}</p>
                </div>
                <a href="{{ url('/branches') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back to List</a>
            </div>

            <div class="grid gap-5 p-5 lg:grid-cols-12">
                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="company">Company</label>
                    <select name="company" id="company" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Company</option>
                        @foreach (($company ?? []) as $value)
                            <option {{ $value->company_id == $details[0]->company_id ? 'selected' : '' }} value="{{ $value->company_id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="branchname">Branch Name</label>
                    <input type="text" name="branchname" id="branchname" value="{{ $details[0]->branch_name }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="code">Branch Code</label>
                    <input type="text" name="br_code" id="code" value="{{ $details[0]->code }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="country">Country</label>
                    <select name="country" id="country" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        @foreach (($country ?? []) as $value)
                            <option {{ $value->country_name == $details[0]->country_name ? 'selected' : '' }} value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="city">City</label>
                    <select name="city" id="city" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select City</option>
                        @foreach (($city ?? []) as $value)
                            <option {{ $value->city_name == $details[0]->city_name ? 'selected' : '' }} value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="email">Branch Email</label>
                    <input type="text" name="br_email" id="email" value="{{ $details[0]->branch_email }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="mobile">Mobile Number</label>
                    <input type="text" name="br_mobile" id="mobile" value="{{ $details[0]->branch_mobile }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="ptcl">PTCL Number</label>
                    <input type="text" name="br_ptcl" id="ptcl" value="{{ $details[0]->branch_ptcl }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="record_daily_stock">Record Daily Stock?</label>
                    <select name="record_daily_stock" id="record_daily_stock" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option {{ $details[0]->record_daily_stock == 1 ? 'selected' : '' }} value="1">Yes</option>
                        <option {{ $details[0]->record_daily_stock == 0 ? 'selected' : '' }} value="0">No</option>
                    </select>
                </div>

                <div class="lg:col-span-4">
                    <label class="text-sm font-bold text-erp-ink" for="report">Reports</label>
                    <select multiple name="reportlist[]" id="report" class="mt-2 min-h-32 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        @foreach (($reports ?? []) as $value)
                            <option {{ in_array($value->id, $branchreports->toArray()) ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-erp-mute">Hold Ctrl to select multiple reports.</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-bold text-erp-ink" for="report_send_date">Report Send Date</label>
                    <input type="date" name="report_send_date" id="report_send_date" value="{{ $details[0]->report_send_date }}"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="address">Branch Address</label>
                    <textarea name="br_address" id="address" rows="6"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ $details[0]->branch_address }}</textarea>
                </div>

                <div class="lg:col-span-3">
                    <label class="text-sm font-bold text-erp-ink" for="vdimg">Branch Logo</label>
                    <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                        <img id="vdpimg"
                            src="{{ asset('storage/images/branch/' . (!empty($details[0]->branch_logo) ? $details[0]->branch_logo : 'placeholder.jpg')) }}"
                            class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="Branch logo preview">
                        <input type="file" name="vdimg" id="vdimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:justify-end">
                <a href="{{ url('/branches') }}" class="rounded-lg border border-erp-line px-4 py-2 text-center text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
                <button type="submit" id="btnsubmit" class="rounded-lg bg-erp px-4 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Update Branch</button>
            </div>
        </section>
    </form>
@endsection

@push('scripts')
    <script>
        const branchForm = document.getElementById('branchForm');

        document.getElementById('vdimg')?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                document.getElementById('vdpimg').src = URL.createObjectURL(this.files[0]);
            }
        });

        branchForm?.addEventListener('submit', function (event) {
            event.preventDefault();
            const button = document.getElementById('btnsubmit');
            button.disabled = true;
            button.textContent = 'Updating...';

            fetch("{{ url('/updatebranch') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: new FormData(branchForm)
            })
                .then(response => response.text())
                .then(function (response) {
                    if (response.trim() === '1') {
                        alert('Branch Updated Successfully!');
                        window.location = "{{ url('/branches') }}";
                    } else {
                        alert('Unable to update branch right now.');
                    }
                })
                .catch(function () {
                    alert('Unable to update branch right now.');
                })
                .finally(function () {
                    button.disabled = false;
                    button.textContent = 'Update Branch';
                });
        });
    </script>
@endpush
