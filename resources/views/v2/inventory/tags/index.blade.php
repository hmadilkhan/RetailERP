@extends('layouts.master-tailwind')

@section('title', 'Tags')
@section('page_title', isset($id) ? 'Edit Tag' : 'Tags')
@section('page_subtitle', 'Manage catalogue tags with website banners and metadata.')

@section('content')
    @php
        $route = route('tags.store');
        if (isset($id)) {
            $route = route('tags.update', $id);
        }
        $tagName = old('name');
        $tagSlug = old('slug');
        $metaTitle = old('meta_title');
        $metaDescription = old('meta_description');
        $desktopBanner = asset('storage/images/placeholder.jpg');
        $mobileBanner = asset('storage/images/placeholder.jpg');
        if (isset($id)) {
            $tagName = old('name') ? old('name') : $edit->name;
            $tagSlug = old('slug') ? old('slug') : $edit->slug;
            $metaTitle = old('meta_title') ? old('meta_title') : $edit->meta_title;
            $metaDescription = old('meta_description') ? old('meta_description') : $edit->meta_description;
            $desktopBanner = File::exists('storage/images/tags/'.$edit->desktop_banner) ? asset('storage/images/tags/'.$edit->desktop_banner) : asset('storage/images/placeholder.jpg');
            $mobileBanner = File::exists('storage/images/tags/'.$edit->mobile_banner) ? asset('storage/images/tags/'.$edit->mobile_banner) : asset('storage/images/placeholder.jpg');
        }
        $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
        $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
    @endphp

    <div class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ isset($id) ? 'Edit' : 'Create' }} Tag</h2>
                @if (isset($id))
                    <a href="{{ route('tags.index') }}" class="text-sm font-bold text-erp-dark hover:text-erp">Back to list</a>
                @endif
            </div>
            <form method="POST" action="{{ $route }}" enctype="multipart/form-data" class="space-y-4 p-5">
                @csrf
                @if (isset($id))
                    @method('PATCH')
                @endif

                <label class="block">
                    <span class="{{ $labelClass }}">Tag Name</span>
                    <input type="text" name="name" id="name" value="{{ $tagName }}" placeholder="Tag name" class="{{ $inputClass }}">
                    @error('name')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Slug</span>
                    <input type="text" name="slug" id="slug" value="{{ $tagSlug }}" placeholder="Slug" class="{{ $inputClass }}">
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Priority</span>
                    <input type="number" min="0" value="0" name="priority" id="priority" placeholder="Priority" class="{{ $inputClass }}">
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Meta Title</span>
                    <input type="text" name="meta_title" id="meta_title" value="{{ $metaTitle }}" placeholder="Meta title" class="{{ $inputClass }}">
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Meta Description</span>
                    <textarea rows="4" name="meta_description" id="meta_descript" placeholder="Meta description" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ $metaDescription }}</textarea>
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Desktop Banner</span>
                    <img id="previewdesktopBanner" src="{{ $desktopBanner }}" class="mt-2 h-24 w-full rounded-lg object-cover ring-1 ring-slate-200">
                    <input type="file" name="desktop_banner" id="desktop_banner" accept="image/*" onchange="readURL(this,'previewdesktopBanner')" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                    @error('desktop_banner')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="{{ $labelClass }}">Mobile Banner</span>
                    <img id="previewMobileBanner" src="{{ $mobileBanner }}" class="mt-2 h-24 w-32 rounded-lg object-cover ring-1 ring-slate-200">
                    <input type="file" name="mobile_banner" id="mobile_banner" accept="image/*" onchange="readURL(this,'previewMobileBanner')" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp">
                    @error('mobile_banner')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">{{ isset($id) ? 'Save Changes' : 'Submit' }}</button>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Tags List</h2>
                <input type="search" id="tagSearch" placeholder="Search tag..." class="h-10 w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Image</th>
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3">Slug</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($lists as $tag)
                            @php
                                $image = asset('storage/images/placeholder.jpg');
                                if ($tag->desktop_banner != null) {
                                    $path = 'storage/images/tags/'.$tag->desktop_banner;
                                    $image = File::exists($path) ? asset($path) : asset('storage/images/placeholder.jpg');
                                }
                            @endphp
                            <tr class="tag-row" data-search="{{ strtolower($tag->name.' '.$tag->slug) }}">
                                <td class="px-5 py-3">
                                    <img src="{{ $image }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200" alt="{{ $tag->desktop_banner ?: 'placeholder.jpg' }}">
                                </td>
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $tag->name }}</td>
                                <td class="px-5 py-3 text-erp-text">{{ $tag->slug }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('tags.edit', $tag->id) }}" class="font-bold text-erp-dark hover:text-erp">Edit</a>
                                        <button type="button" onclick="removeTag({{ $tag->id }}, '{{ $tag->name }}')" class="font-bold text-rose-600 hover:text-rose-700">Delete</button>
                                    </div>
                                    <form action="{{ route('tags.destroy', $tag->id) }}" class="hidden" id="removeForm{{ $tag->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-6 text-center text-sm text-erp-mute">No tags yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('tagSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.tag-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function removeTag(id, name) {
            if (!confirm('Do you want to delete tag ' + name + '?')) return;
            document.getElementById('removeForm' + id).submit();
        }

        function readURL(input, id) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                if (file.size > 1 * 1024 * 1024) {
                    alert('File size must be less than 1MB.');
                    return;
                }

                const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                if (!allowedExtensions.exec(file.name)) {
                    alert('Invalid file type. Please select a JPG, PNG, or GIF image.');
                    return;
                }

                document.getElementById(id).src = URL.createObjectURL(file);
            }
        }
    </script>
@endpush
