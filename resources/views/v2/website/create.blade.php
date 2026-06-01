@extends('layouts.master-tailwind')

@section('title', 'Create Website')
@section('page_title', 'Create Website')
@section('page_subtitle', 'Set up a storefront, domain, brand assets, and deployment repository configuration.')

@section('content')
    <form method="POST" action="{{ route('website.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Website Information</h2>
                <p class="mt-1 text-sm text-erp-mute">Basic company and storefront details.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company *</span>
                    <select name="company_id" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option {{ old('company_id') == $company->company_id ? 'selected' : '' }} value="{{ $company->company_id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website Type *</span>
                    <select name="type" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Type</option>
                        @foreach(['restaurant', 'grocery', 'boutique', 'shopify'] as $type)
                            <option value="{{ $type }}" @selected(old('type') == $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website Name *</span>
                    <input name="name" type="text" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="Sabify Store">
                    @error('name') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Domain Name *</span>
                    <input name="url" type="url" value="{{ old('url') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="https://example.com">
                    @error('url') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">UAN Number</span>
                    <input name="uan_number" type="text" value="{{ old('uan_number') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">WhatsApp Number</span>
                    <input name="whatsapp" type="text" value="{{ old('whatsapp') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Deployment</h2>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">GitHub Token</span>
                    <input name="github_token" type="text" value="{{ old('github_token') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">GitHub Owner</span>
                    <input name="github_owner" type="text" value="{{ old('github_owner') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">GitHub Repo</span>
                    <input name="github_repo" type="text" value="{{ old('github_repo') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">GitHub Branch</span>
                    <input name="github_branch" type="text" value="{{ old('github_branch') }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Brand Assets</h2>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <x-v2.website.image-upload name="logo" label="Logo" preview-id="logoPreview" />
                <x-v2.website.image-upload name="favicon" label="Favicon" preview-id="faviconPreview" />
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('website.index') }}" class="rounded-lg border border-erp-line px-5 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
            <button type="submit" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Create Website</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        function bindPreview(inputName, previewId) {
            document.querySelector(`input[name="${inputName}"]`)?.addEventListener('change', function () {
                if (!this.files || !this.files[0]) return;
                const reader = new FileReader();
                reader.onload = event => document.getElementById(previewId).src = event.target.result;
                reader.readAsDataURL(this.files[0]);
            });
        }

        bindPreview('logo', 'logoPreview');
        bindPreview('favicon', 'faviconPreview');
    </script>
@endpush
