@extends('layouts.master-tailwind')

@section('title', 'Edit Website')
@section('page_title', 'Edit Website')
@section('page_subtitle', 'Update storefront details, brand assets, and repository deployment settings.')

@section('content')
    <form method="POST" action="{{ route('website.update', $website->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Website Information</h2>
                <p class="mt-1 text-sm text-erp-mute">Editing {{ $website->name }} for {{ $website->company_name }}.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Company</span>
                    <div class="mt-2 rounded-lg border border-erp-line bg-slate-50 px-4 py-2.5 text-sm font-bold text-erp-ink">{{ $website->company_name }}</div>
                </div>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website Type *</span>
                    @php $oldWebType = old('type') ?: $website->type; @endphp
                    <select name="type" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Type</option>
                        @foreach(['restaurant', 'grocery', 'boutique', 'shopify'] as $type)
                            <option value="{{ $type }}" @selected($oldWebType == $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('type') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website Name *</span>
                    <input name="name" type="text" value="{{ old('name') ?: $website->name }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('name') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Domain Name *</span>
                    <input name="url" type="url" value="{{ old('url') ?: $website->url }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('url') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">UAN Number</span>
                    <input name="uan_number" type="text" value="{{ old('uan_number') ?: $website->uan_number }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">WhatsApp Number</span>
                    <input name="whatsapp" type="text" value="{{ old('whatsapp') ?: $website->whatsapp }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Deployment</h2>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                @foreach(['github_token' => 'GitHub Token', 'github_owner' => 'GitHub Owner', 'github_repo' => 'GitHub Repo', 'github_branch' => 'GitHub Branch'] as $field => $label)
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">{{ $label }}</span>
                        <input name="{{ $field }}" type="text" value="{{ old($field) ?: $website->{$field} }}" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Brand Assets</h2>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2">
                <x-v2.website.image-upload name="logo" label="Logo" preview-id="logoPreview" :src="asset('storage/images/' . ($website->logo ? 'website/' . $website->logo : 'placeholder.jpg'))" />
                <x-v2.website.image-upload name="favicon" label="Favicon" preview-id="faviconPreview" :src="asset('storage/images/' . ($website->favicon ? 'website/' . $website->favicon : 'placeholder.jpg'))" />
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('website.index') }}" class="rounded-lg border border-erp-line px-5 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
            <button type="submit" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Update Website</button>
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
