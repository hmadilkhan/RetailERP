@extends('layouts.master-tailwind')

@section('title', 'Mobile App Promotion')
@section('page_title', 'Mobile Promotion')
@section('page_subtitle', 'Upload promotional mobile banners, connect them to products, and keep the app gallery within the five-image limit.')

@section('content')
    @php
        $usedLimit = count($images);
        $remainingLimit = max(0, 5 - $usedLimit);
    @endphp

    <div class="space-y-6">
        @if(session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('status') }}</div>
        @endif

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Uploaded</div>
                <div class="mt-3 text-2xl font-bold text-erp-ink">{{ $usedLimit }}/5</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Remaining Slots</div>
                <div class="mt-3 text-2xl font-bold text-erp-ink">{{ $remainingLimit }}</div>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Products Available</div>
                <div class="mt-3 text-2xl font-bold text-erp-ink">{{ number_format(count($products)) }}</div>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Upload Banner</h2>
                <p class="mt-1 text-sm text-erp-mute">Recommended output is resized to 486 x 216 pixels.</p>
            </div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('insert-mobile-images') }}" class="grid gap-5 px-5 py-5 lg:grid-cols-12 lg:items-end">
                @csrf
                <label class="block lg:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Images</span>
                    <input type="file" name="image[]" id="image" multiple required class="mt-2 block w-full rounded-lg border border-erp-line bg-white p-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                </label>

                <label class="block lg:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Product</span>
                    <select class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp v2-select2" id="product" name="product" data-placeholder="Select Product">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block lg:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Description</span>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" class="mt-2 h-11 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="Enter Description">
                </label>

                <button class="rounded-lg border border-erp bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark lg:col-span-2" type="submit" {{ $remainingLimit === 0 ? 'disabled' : '' }}>Save</button>
            </form>
            <div id="previewGallery" class="grid gap-4 px-5 pb-5 sm:grid-cols-2 lg:grid-cols-4"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Current Gallery</h2>
            </div>
            <div class="grid gap-5 px-5 py-5 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($images as $value)
                    <article class="overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm">
                        <img class="h-40 w-full object-cover" src="{{ asset('assets/images/mobile/'.$value->image) }}" alt="{{ $value->description }}">
                        <div class="space-y-2 p-4">
                            <h3 class="font-bold text-erp-ink">{{ $value->product_name }}</h3>
                            <p class="min-h-10 text-sm text-erp-mute">{{ $value->description ?: 'No description added.' }}</p>
                            <button onclick="deleteImage('{{ $value->id }}','{{ $value->image }}')" class="w-full rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100" type="button">Delete</button>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-lg border border-dashed border-erp-line bg-erp-soft px-5 py-8 text-center text-sm text-erp-mute">No mobile promotion images uploaded yet.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const remainingLimit = {{ $remainingLimit }};
        const csrfToken = '{{ csrf_token() }}';

        document.getElementById('image').addEventListener('change', function () {
            const preview = document.getElementById('previewGallery');
            preview.innerHTML = '';

            if (this.files.length > remainingLimit) {
                alert('You can only upload ' + remainingLimit + (remainingLimit === 1 ? ' image' : ' images'));
                this.value = '';
                return;
            }

            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = event => {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'h-36 w-full rounded-lg border border-erp-line object-cover';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });

        function deleteImage(id, image) {
            if (!confirm('Do you really want to delete this image?')) return;

            fetch('{{ url("/delete-mobile-image") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id: id, image: image })
            }).then(res => res.json()).then(result => {
                alert(result.message);
                if (result.status === 200) location.reload();
            });
        }
    </script>
@endpush
