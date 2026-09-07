@extends('layouts.master-tailwind')

@section('title', 'Testimonials')
@section('page_title', 'Testimonials')
@section('page_subtitle', 'Customer reviews shown on your storefronts.')

@section('content')
    @php
        $websiteCollection = collect($websites ?? []);
        $testimonialCollection = collect($testimonials ?? []);
        $selectedWebsite = $websiteId ?? null;

        $websiteMap = $websiteCollection->keyBy('id');

        // MediaTrait writes these through the "public" disk, so ask that disk whether the
        // file is really there before pointing an <img> at it.
        $avatarUrl = function ($image) {
            if (!empty($image) && \Illuminate\Support\Facades\Storage::disk('public')->exists('/images/testimonials/' . $image)) {
                return asset('storage/images/testimonials/' . $image);
            }

            return null;
        };

        $ratedTestimonials = $testimonialCollection->filter(fn ($row) => is_numeric($row->rating));
        $averageRating = $ratedTestimonials->count() ? round($ratedTestimonials->avg('rating'), 1) : null;
        $withPhoto = $testimonialCollection->filter(fn ($row) => $avatarUrl($row->image) !== null)->count();
        $coveredWebsites = $testimonialCollection->pluck('website_id')->unique()->count();
    @endphp

    <div class="space-y-6">
        @if (Session::has('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-bold text-emerald-700">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-bold text-rose-700">
                {{ Session::get('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-sm font-bold text-rose-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Testimonials</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($testimonialCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">{{ $selectedWebsite ? 'On the selected website' : 'Across every website' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites Covered</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">
                    <span>{{ number_format($coveredWebsites) }}</span><span class="text-lg font-bold text-erp-mute">/{{ number_format($websiteCollection->count()) }}</span>
                </div>
                <p class="mt-2 text-sm text-erp-mute">Websites with at least one review</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Average Rating</div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-erp-ink">{{ $averageRating !== null ? number_format($averageRating, 1) : '—' }}</span>
                    @if ($averageRating !== null)
                        <span class="text-lg font-bold text-amber-500">&#9733;</span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-erp-mute">Out of 5 across {{ number_format($ratedTestimonials->count()) }} rated {{ $ratedTestimonials->count() === 1 ? 'review' : 'reviews' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">With Photo</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($withPhoto) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Reviews that show a customer picture</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-end lg:justify-between">
                <label class="block lg:w-80">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select id="website" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">All Websites</option>
                        @foreach ($websiteCollection as $value)
                            <option value="{{ $value->id }}" {{ (string) $selectedWebsite === (string) $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input type="search" id="testimonialFilter" placeholder="Search customer or content..."
                        class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                    <a href="{{ route('testimonials.create') }}"
                        class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">
                        <i class="icofont icofont-plus text-base"></i> Create Testimonial
                    </a>
                </div>
            </div>

            <div class="p-5">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($testimonialCollection as $value)
                        @php
                            $website = $websiteMap[$value->website_id] ?? null;
                            $websiteName = $website->name ?? ('Website #' . $value->website_id);
                            $image = $avatarUrl($value->image);
                            $stars = is_numeric($value->rating) ? max(0, min(5, (int) round($value->rating))) : 0;
                            $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($value->customer_name), 0, 1)) ?: '?';
                        @endphp

                        <article class="testimonial-card flex flex-col rounded-lg border border-erp-line bg-white shadow-sm transition hover:border-erp hover:shadow-panel"
                            data-search="{{ strtolower($value->customer_name . ' ' . $value->content . ' ' . $websiteName) }}">
                            <div class="flex items-start gap-3 border-b border-erp-line px-5 py-4">
                                @if ($image)
                                    <button type="button" onclick="openLightbox(@js($image), @js($value->customer_name))"
                                        class="h-12 w-12 shrink-0 overflow-hidden rounded-full ring-1 ring-erp-line">
                                        <img src="{{ $image }}" alt="{{ $value->customer_name }}" class="h-full w-full object-cover">
                                    </button>
                                @else
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-base font-black text-erp-dark ring-1 ring-emerald-200">
                                        {{ $initials }}
                                    </span>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-black text-erp-ink" title="{{ $value->customer_name }}">{{ $value->customer_name }}</p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="text-sm leading-none text-amber-500" aria-hidden="true">
                                            @for ($i = 1; $i <= 5; $i++){{ $i <= $stars ? '★' : '☆' }}@endfor
                                        </span>
                                        <span class="text-xs font-bold text-erp-mute">{{ is_numeric($value->rating) ? $value->rating : '—' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-1 px-5 py-4">
                                <p class="text-sm leading-relaxed text-erp-text">{{ $value->content }}</p>
                            </div>

                            <div class="flex items-center justify-between gap-3 border-t border-erp-line px-5 py-3">
                                <span class="truncate rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-erp-mute ring-1 ring-erp-line" title="{{ $websiteName }}">
                                    {{ $websiteName }}
                                </span>
                                <div class="flex shrink-0 gap-2">
                                    <a href="{{ route('testimonials.edit', $value->id) }}"
                                        class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Edit</a>
                                    <button type="button" onclick="confirmRemove(@js('removeForm' . $value->id), @js($value->customer_name), @js($websiteName))"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                </div>
                            </div>

                            <form action="{{ route('testimonials.destroy', $value->id) }}" method="post" id="removeForm{{ $value->id }}" class="hidden">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="websiteId" value="{{ Crypt::encrypt($value->website_id) }}">
                            </form>
                        </article>
                    @empty
                        <div class="col-span-full rounded-lg border border-dashed border-erp-line px-5 py-14 text-center">
                            <p class="text-sm font-bold text-erp-ink">
                                {{ $selectedWebsite ? 'No testimonials for this website yet' : 'No testimonials yet' }}
                            </p>
                            <p class="mt-1 text-sm text-erp-mute">Add your first customer review to show it on the storefront.</p>
                            <a href="{{ route('testimonials.create') }}"
                                class="mt-4 inline-flex h-10 items-center justify-center rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">
                                Create Testimonial
                            </a>
                        </div>
                    @endforelse

                    <div id="testimonialNoMatch" class="col-span-full hidden rounded-lg border border-dashed border-erp-line px-5 py-14 text-center text-sm text-erp-mute">
                        No testimonials match your search.
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Image lightbox --}}
    <div id="lightbox" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/80 px-4"
        onclick="closeLightbox()">
        <figure class="max-h-full">
            <img id="lightboxImage" src="" alt="" class="mx-auto max-h-[75vh] max-w-full rounded-lg object-contain shadow-menu">
            <figcaption id="lightboxCaption" class="mt-3 text-center text-sm font-bold text-white"></figcaption>
        </figure>
    </div>

    {{-- Delete confirmation --}}
    <div id="confirmModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeConfirm()">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Remove Testimonial</h3>
            </div>
            <div class="px-5 py-5 text-sm text-erp-text" id="confirmMessage"></div>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" onclick="closeConfirm()"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</button>
                <button type="button" id="btnConfirmYes"
                    class="rounded-lg bg-rose-600 px-5 py-2 text-sm font-bold text-white transition hover:bg-rose-700">Remove</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var FILTER_URL_TEMPLATE = @js(route('filterTestimonial', ['id' => '__ID__']));
        var ALL_URL = @js(route('testimonials.index'));

        var confirmFormId = null;

        function confirmRemove(formId, customerName, websiteName) {
            confirmFormId = formId;
            document.getElementById('confirmMessage').textContent =
                'Remove the testimonial from ' + customerName + ' on the ' + websiteName + ' website? This cannot be undone.';

            var modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirm() {
            confirmFormId = null;
            var modal = document.getElementById('confirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openLightbox(url, caption) {
            document.getElementById('lightboxImage').setAttribute('src', url);
            document.getElementById('lightboxCaption').textContent = caption || '';

            var modal = document.getElementById('lightbox');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeLightbox() {
            var modal = document.getElementById('lightbox');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        jQuery(function () {
            // The website filter is server-side: picking one reloads the scoped URL.
            jQuery('#website').on('change', function () {
                var value = jQuery(this).val();
                window.location = value ? FILTER_URL_TEMPLATE.replace('__ID__', encodeURIComponent(value)) : ALL_URL;
            });

            document.getElementById('btnConfirmYes').addEventListener('click', function () {
                if (!confirmFormId) {
                    return;
                }
                this.disabled = true;
                this.classList.add('opacity-60');
                document.getElementById(confirmFormId).submit();
            });

            document.getElementById('testimonialFilter').addEventListener('input', function () {
                var term = this.value.trim().toLowerCase();
                var visible = 0;

                document.querySelectorAll('.testimonial-card').forEach(function (card) {
                    var match = card.getAttribute('data-search').indexOf(term) !== -1;
                    card.classList.toggle('hidden', !match);
                    if (match) {
                        visible += 1;
                    }
                });

                document.getElementById('testimonialNoMatch').classList.toggle('hidden', visible > 0);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') {
                    return;
                }
                closeConfirm();
                closeLightbox();
            });
        });
    </script>
@endpush
