@extends('layouts.master-tailwind')

@section('title', 'Brands')
@section('page_title', isset($id) ? 'Edit Brand' : 'Brands')
@section('page_subtitle', 'Manage catalogue brands with logo, banner, and parent grouping.')

@section('content')
    @php
        $route = route('brands.store');
        if (isset($id)) {
            $route = route('brands.update', $id);
        }
        $brandName = old('name');
        $brandSlug = old('slug');
        $parent = old('parent');
        $priority = old('priority') ? old('priority') : 0;
        $brandImage = asset('storage/images/placeholder.jpg');
        $brandBanner = asset('storage/images/placeholder.jpg');
        if (isset($id)) {
            $brandName = old('name') ? old('name') : $edit->name;
            $brandSlug = old('slug') ? old('slug') : $edit->slug;
            $parent = $edit->parent;
            $priority = $edit->priority ? $edit->priority : 0;
            if (!empty($edit->image) && Storage::disk('public')->exists('images/brands/'.session('company_id').'/'.$edit->image)) {
                $brandImage = asset('storage/images/brands/'.session('company_id').'/'.$edit->image);
            }
            if (!empty($edit->banner) && Storage::disk('public')->exists('images/brands/'.session('company_id').'/'.$edit->banner)) {
                $brandBanner = asset('storage/images/brands/'.session('company_id').'/'.$edit->banner);
            }
        }
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
    @endphp

    <div class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ isset($id) ? 'Edit' : 'Create' }} Brand</h2>
                @if (isset($id))
                    <a href="{{ route('brands.index') }}" class="text-sm font-bold text-erp-dark hover:text-erp">Back to list</a>
                @endif
            </div>
            <form method="POST" action="{{ $route }}" enctype="multipart/form-data" class="space-y-4 p-5">
                @csrf
                @if (isset($id))
                    @method('PATCH')
                @endif

                <label class="block">
                    <span class="{{ $labelClass }}">Brand Name</span>
                    <input type="text" name="name" id="name" value="{{ $brandName }}" placeholder="Brand name" class="{{ $inputClass }}">
                    @error('name')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Slug</span>
                    <input type="text" name="slug" id="slug" value="{{ $brandSlug }}" placeholder="Slug" class="{{ $inputClass }}">
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Parent</span>
                    <select name="parent" id="parent" class="{{ $inputClass }}">
                        <option value="">Select</option>
                        @if($lists)
                            @foreach($lists as $val)
                                @if($val->parent == null)
                                    <option {{ $parent == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Priority</span>
                    <input type="number" min="0" value="{{ $priority }}" name="priority" id="priority" placeholder="Priority" class="{{ $inputClass }}">
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Brand Logo</span>
                    <img id="showImage" src="{{ $brandImage }}" class="mt-2 h-24 w-24 rounded-lg object-cover ring-1 ring-slate-200" alt="{{ isset($id) ? $edit->image : 'placeholder.jpg' }}">
                    <input type="file" id="image" name="image" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                    @error('image')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Banner</span>
                    <img id="showBanner" src="{{ $brandBanner }}" class="mt-2 h-24 w-full rounded-lg object-cover ring-1 ring-slate-200" alt="{{ isset($id) ? $edit->banner : 'placeholder.jpg' }}">
                    <input type="file" id="banner" name="banner" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                    @error('banner')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">{{ isset($id) ? 'Save Changes' : 'Submit' }}</button>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Brands List</h2>
                <input type="search" id="brandSearch" placeholder="Search brand..." class="h-10 w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Image</th>
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">Slug</th>
                            <th class="px-5 py-3">Parent</th>
                            <th class="px-5 py-3">Priority</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($lists as $brand)
                            @php
                                $parentTable = $lists->where('id', $brand->parent)->first();
                                $image = asset('storage/images/placeholder.jpg');
                                if ($brand->image != null) {
                                    $path = 'storage/images/brands/'.session('company_id').'/'.$brand->image;
                                    $image = File::exists($path) ? asset($path) : asset('storage/images/placeholder.jpg');
                                }
                            @endphp
                            <tr class="brand-row" data-search="{{ strtolower($brand->name.' '.$brand->slug) }}">
                                <td class="px-5 py-3">
                                    <img src="{{ $image }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200" alt="{{ $brand->image ?: 'placeholder.jpg' }}">
                                </td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $brand->name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $brand->slug }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $parentTable->name ?? '' }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $brand->priority }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('brands.edit', $brand->id) }}" class="font-bold text-erp-dark hover:text-erp">Edit</a>
                                        <button type="button" onclick="removeBrand({{ $brand->id }}, '{{ $brand->name }}')" class="font-bold text-rose-600 hover:text-rose-700">Delete</button>
                                    </div>
                                    <form action="{{ route('brands.destroy', $brand->id) }}" class="hidden" id="removeForm{{ $brand->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-6 text-center text-sm text-erp-mute">No brands yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('brandSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.brand-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function removeBrand(id, name) {
            if (!confirm('Do you want to delete brand ' + name + '?')) return;
            document.getElementById('removeForm' + id).submit();
        }

        function previewImage(inputId, previewId) {
            document.getElementById(inputId).addEventListener('change', function () {
                if (this.files[0]) {
                    document.getElementById(previewId).src = URL.createObjectURL(this.files[0]);
                }
            });
        }
        previewImage('image', 'showImage');
        previewImage('banner', 'showBanner');
    </script>
@endpush
