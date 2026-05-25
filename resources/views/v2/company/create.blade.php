@extends('layouts.master-tailwind')

@section('title', 'Create Company')
@section('page_title', 'Create Company')
@section('page_subtitle', 'New Tailwind version of the existing company creation screen. Submission is kept unchanged because the current module has no active submit route.')

@section('content')
    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Company Details</h2>
                <p class="mt-1 text-sm text-erp-mute">Fill company profile, location, contact information, and logo.</p>
            </div>
            <a href="{{ url('/companies') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back to List</a>
        </div>

        <div class="grid gap-5 p-5 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="companyname">Company Name</label>
                <input type="text" name="companyname" id="companyname" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>

            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="country">Country</label>
                <select name="country" id="country" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <option value="">Select Country</option>
                    @foreach (($country ?? []) as $value)
                        <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="city">City</label>
                <select disabled name="city" id="city" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp disabled:bg-slate-100">
                    <option value="">Select City</option>
                    @foreach (($city ?? []) as $value)
                        <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="email">Company Email</label>
                <input type="text" name="email" id="email" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>

            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="mobile">Mobile Number</label>
                <input type="text" name="mobile" id="mobile" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>

            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="ptcl">PTCL Number</label>
                <input type="text" name="ptcl" id="ptcl" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>

            <div class="lg:col-span-8">
                <label class="text-sm font-bold text-erp-ink" for="address">Company Address</label>
                <textarea name="address" id="address" rows="6" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
            </div>

            <div class="lg:col-span-4">
                <label class="text-sm font-bold text-erp-ink" for="vdimg">Company Logo</label>
                <div class="mt-2 rounded-lg border border-dashed border-erp-line bg-slate-50 p-4">
                    <img id="vdpimg" src="{{ asset('assets/images/placeholder.jpg') }}" class="h-28 w-28 rounded-lg object-cover ring-1 ring-slate-200" alt="Company logo preview">
                    <input type="file" name="vdimg" id="vdimg" class="mt-4 block w-full text-sm text-erp-mute file:mr-3 file:rounded-lg file:border-0 file:bg-erp file:px-3 file:py-2 file:text-sm file:font-bold file:text-white">
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 border-t border-erp-line px-5 py-4 sm:flex-row sm:justify-end">
            <a href="{{ url('/companies') }}" class="rounded-lg border border-erp-line px-4 py-2 text-center text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
            <button type="button" class="rounded-lg bg-slate-300 px-4 py-2 text-sm font-bold text-slate-600" title="The existing CompanyController has no active submit route.">Create Company</button>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('country')?.addEventListener('change', function () {
            document.getElementById('city').disabled = this.value === '';
        });

        document.getElementById('vdimg')?.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                document.getElementById('vdpimg').src = URL.createObjectURL(this.files[0]);
            }
        });
    </script>
@endpush
