@extends('layouts.master-tailwind')

@section('title', 'Customer Reviews')
@section('page_title', 'Customer Reviews')
@section('page_subtitle', 'Moderate the reviews your customers leave on the storefront.')

@section('content')
    @php
        $websiteCollection = collect($websites ?? []);
        $reviewCollection = collect($reviews ?? []);
        $imageCollection = collect($images ?? []);
        $selectedWebsite = $websiteId ?? null;

        $websiteMap = $websiteCollection->keyBy('id');
        $imagesByReview = $imageCollection->groupBy('review_id');

        // The storefront writes these through the "public" disk, so ask that disk whether
        // the file is really there before pointing an <img> at it.
        $reviewImageUrl = function ($image) {
            if (!empty($image) && \Illuminate\Support\Facades\Storage::disk('public')->exists('/images/customer-reviews/' . $image)) {
                return asset('storage/images/customer-reviews/' . $image);
            }

            return null;
        };

        $activeCount = $reviewCollection->where('status', 1)->count();
        $hiddenCount = $reviewCollection->count() - $activeCount;

        $ratedReviews = $reviewCollection->filter(fn ($row) => is_numeric($row->rating));
        $averageRating = $ratedReviews->count() ? round($ratedReviews->avg('rating'), 1) : null;
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

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Reviews</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($reviewCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">{{ $selectedWebsite ? 'On the selected website' : 'Across every website' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Published</div>
                <div class="mt-4 text-3xl font-black text-emerald-600">{{ number_format($activeCount) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Visible to storefront visitors</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Hidden</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($hiddenCount) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Switched off, not shown publicly</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Average Rating</div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-erp-ink">{{ $averageRating !== null ? number_format($averageRating, 1) : '—' }}</span>
                    @if ($averageRating !== null)
                        <span class="text-lg font-bold text-amber-500">&#9733;</span>
                    @endif
                </div>
                <p class="mt-2 text-sm text-erp-mute">Out of 5 across {{ number_format($ratedReviews->count()) }} rated {{ $ratedReviews->count() === 1 ? 'review' : 'reviews' }}</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 xl:flex-row xl:items-end xl:justify-between">
                <label class="block xl:w-80">
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
                    <div class="flex gap-1 rounded-lg border border-erp-line p-1">
                        @foreach ([['all', 'All', $reviewCollection->count()], ['active', 'Published', $activeCount], ['hidden', 'Hidden', $hiddenCount]] as $chip)
                            <button type="button" data-status="{{ $chip[0] }}"
                                class="status-chip rounded-md px-3 py-1.5 text-xs font-bold text-erp-mute transition hover:text-erp-dark">
                                {{ $chip[1] }}
                                <span class="ml-1 text-[11px] font-black">{{ $chip[2] }}</span>
                            </button>
                        @endforeach
                    </div>
                    <input type="search" id="reviewFilter" placeholder="Search customer, title or review..."
                        class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
                </div>
            </div>

            <div class="p-5">
                <div class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                    @forelse ($reviewCollection as $value)
                        @php
                            $website = $websiteMap[$value->website_id] ?? null;
                            $websiteName = $website->name ?? ('Website #' . $value->website_id);
                            $isActive = $value->status == 1;
                            $stars = is_numeric($value->rating) ? max(0, min(5, (int) round($value->rating))) : 0;
                            $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($value->customer_name), 0, 1)) ?: '?';

                            $reviewImages = collect($imagesByReview[$value->id] ?? [])
                                ->map(fn ($row) => $reviewImageUrl($row->image))
                                ->filter()
                                ->values();

                            $createdAt = $value->created_at ? strtotime($value->created_at) : null;
                        @endphp

                        <article class="review-card flex flex-col rounded-lg border border-erp-line bg-white shadow-sm transition hover:border-erp hover:shadow-panel"
                            data-status="{{ $isActive ? 'active' : 'hidden' }}"
                            data-search="{{ strtolower($value->customer_name . ' ' . $value->customer_email . ' ' . $value->review_title . ' ' . $value->review . ' ' . $websiteName) }}">

                            <div class="flex items-start gap-3 border-b border-erp-line px-5 py-4">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-base font-black text-erp-dark ring-1 ring-emerald-200">
                                    {{ $initials }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-black text-erp-ink" title="{{ $value->customer_name }}">{{ $value->customer_name }}</p>
                                    <p class="truncate text-xs font-bold text-erp-mute" title="{{ $value->customer_email }}">{{ $value->customer_email ?: '—' }}</p>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $isActive ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-300' }}">
                                    {{ $isActive ? 'Published' : 'Hidden' }}
                                </span>
                            </div>

                            <div class="flex-1 space-y-3 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm leading-none text-amber-500" aria-hidden="true">
                                        @for ($i = 1; $i <= 5; $i++){{ $i <= $stars ? '★' : '☆' }}@endfor
                                    </span>
                                    <span class="text-xs font-bold text-erp-mute">{{ is_numeric($value->rating) ? $value->rating : '—' }}</span>
                                </div>

                                @if (!empty($value->review_title))
                                    <p class="text-sm font-bold text-erp-ink">{{ $value->review_title }}</p>
                                @endif

                                @if (!empty($value->review))
                                    <div>
                                        <p class="review-body line-clamp-5 text-sm leading-relaxed text-erp-text">{{ $value->review }}</p>
                                        <button type="button" onclick="toggleReviewBody(this)"
                                            class="mt-1 hidden text-xs font-bold text-erp-dark underline">Show more</button>
                                    </div>
                                @endif

                                @if ($reviewImages->count())
                                    <div class="flex flex-wrap gap-2 pt-1">
                                        @foreach ($reviewImages as $url)
                                            <button type="button" onclick="openLightbox(@js($url), @js($value->customer_name))"
                                                class="h-14 w-14 overflow-hidden rounded-lg ring-1 ring-erp-line transition hover:ring-erp">
                                                <img src="{{ $url }}" alt="Review photo" class="h-full w-full object-cover">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-erp-line px-5 py-3">
                                <div class="min-w-0">
                                    <span class="block truncate text-[11px] font-bold text-erp-mute" title="{{ $websiteName }}">{{ $websiteName }}</span>
                                    <span class="block text-[11px] text-erp-mute">
                                        {{ $createdAt ? date('d M Y', $createdAt) . ' · ' . date('h:i a', $createdAt) : '—' }}
                                    </span>
                                </div>

                                <div class="flex shrink-0 items-center gap-3">
                                    <button type="button" role="switch" aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                        title="{{ $isActive ? 'Hide this review' : 'Publish this review' }}"
                                        onclick="confirmToggle(@js('activeInactiveForm' . $value->id), @js($value->customer_name), @js($isActive))"
                                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition {{ $isActive ? 'bg-erp' : 'bg-slate-300' }}">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition {{ $isActive ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                    </button>

                                    <button type="button" onclick="confirmRemove(@js('removeForm' . $value->id), @js($value->customer_name), @js($websiteName))"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <form action="{{ route('activeInactiveCustomer_review') }}" method="POST" id="activeInactiveForm{{ $value->id }}" class="hidden">
                                @csrf
                                <input type="hidden" name="id" value="{{ Crypt::encrypt($value->id) }}">
                                <input type="hidden" name="website" value="{{ Crypt::encrypt($value->website_id) }}">
                                <input type="hidden" name="stcode" value="{{ Crypt::encrypt($value->status) }}">
                                <input type="hidden" name="websiteName" value="{{ $websiteName }}">
                            </form>

                            <form action="{{ route('destroyCustomer_review', $value->id) }}" method="post" id="removeForm{{ $value->id }}" class="hidden">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ Crypt::encrypt($value->id) }}">
                                <input type="hidden" name="website" value="{{ Crypt::encrypt($value->website_id) }}">
                                <input type="hidden" name="websiteName" value="{{ $websiteName }}">
                            </form>
                        </article>
                    @empty
                        <div class="col-span-full rounded-lg border border-dashed border-erp-line px-5 py-14 text-center">
                            <p class="text-sm font-bold text-erp-ink">
                                {{ $selectedWebsite ? 'No reviews for this website yet' : 'No customer reviews yet' }}
                            </p>
                            <p class="mt-1 text-sm text-erp-mute">Reviews left on the storefront show up here for moderation.</p>
                        </div>
                    @endforelse

                    <div id="reviewNoMatch" class="col-span-full hidden rounded-lg border border-dashed border-erp-line px-5 py-14 text-center text-sm text-erp-mute">
                        No reviews match the current filters.
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Image lightbox --}}
    <div id="lightbox" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/80 px-4" onclick="closeLightbox()">
        <figure class="max-h-full">
            <img id="lightboxImage" src="" alt="" class="mx-auto max-h-[75vh] max-w-full rounded-lg object-contain shadow-menu">
            <figcaption id="lightboxCaption" class="mt-3 text-center text-sm font-bold text-white"></figcaption>
        </figure>
    </div>

    {{-- Confirmation --}}
    <div id="confirmModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeConfirm()">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink" id="confirmTitle">Confirm</h3>
            </div>
            <div class="px-5 py-5 text-sm text-erp-text" id="confirmMessage"></div>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" onclick="closeConfirm()"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</button>
                <button type="button" id="btnConfirmYes"
                    class="rounded-lg px-5 py-2 text-sm font-bold text-white transition">Confirm</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .status-chip.is-active {
            background-color: #f0fdf4;
            color: #2E7D32;
        }
    </style>
@endpush

@push('scripts')
    <script>
        var FILTER_URL_TEMPLATE = @js(route('filterCustomerReviews', ['id' => '__ID__']));
        var ALL_URL = @js(route('customerReviewsList'));

        var confirmFormId = null;
        var statusFilter = 'all';

        function openConfirm(formId, title, message, danger) {
            confirmFormId = formId;
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;

            var yes = document.getElementById('btnConfirmYes');
            yes.className = 'rounded-lg px-5 py-2 text-sm font-bold text-white transition ' +
                (danger ? 'bg-rose-600 hover:bg-rose-700' : 'bg-erp hover:bg-erp-dark');

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

        // The switch is a button, not a checkbox, so a cancelled confirm leaves nothing
        // to roll back -- the card keeps showing the status the server actually has.
        function confirmToggle(formId, customerName, isActive) {
            openConfirm(
                formId,
                isActive ? 'Hide Review' : 'Publish Review',
                isActive
                    ? 'Hide the review from ' + customerName + '? It will no longer be shown on the storefront.'
                    : 'Publish the review from ' + customerName + '? It will become visible on the storefront.',
                isActive
            );
        }

        function confirmRemove(formId, customerName, websiteName) {
            openConfirm(
                formId,
                'Remove Review',
                'Remove the review from ' + customerName + ' on the ' + websiteName + ' website?',
                true
            );
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

        function toggleReviewBody(button) {
            var body = button.previousElementSibling;
            var clamped = body.classList.toggle('line-clamp-5');
            button.textContent = clamped ? 'Show more' : 'Show less';
        }

        function applyFilters() {
            var term = document.getElementById('reviewFilter').value.trim().toLowerCase();
            var visible = 0;

            document.querySelectorAll('.review-card').forEach(function (card) {
                var matchesStatus = statusFilter === 'all' || card.getAttribute('data-status') === statusFilter;
                var matchesTerm = card.getAttribute('data-search').indexOf(term) !== -1;
                var show = matchesStatus && matchesTerm;

                card.classList.toggle('hidden', !show);
                if (show) {
                    visible += 1;
                }
            });

            document.getElementById('reviewNoMatch').classList.toggle('hidden', visible > 0);
        }

        jQuery(function () {
            // The website filter is server-side: picking one reloads the scoped URL.
            jQuery('#website').on('change', function () {
                var value = jQuery(this).val();
                window.location = value ? FILTER_URL_TEMPLATE.replace('__ID__', encodeURIComponent(value)) : ALL_URL;
            });

            document.querySelectorAll('.status-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    statusFilter = this.getAttribute('data-status');

                    document.querySelectorAll('.status-chip').forEach(function (other) {
                        other.classList.toggle('is-active', other === chip);
                    });

                    applyFilters();
                });
            });

            document.querySelector('.status-chip').classList.add('is-active');

            document.getElementById('reviewFilter').addEventListener('input', applyFilters);

            document.getElementById('btnConfirmYes').addEventListener('click', function () {
                if (!confirmFormId) {
                    return;
                }
                this.disabled = true;
                this.classList.add('opacity-60');
                document.getElementById(confirmFormId).submit();
            });

            // Only offer "Show more" on reviews that are actually clipped.
            document.querySelectorAll('.review-body').forEach(function (body) {
                if (body.scrollHeight > body.clientHeight + 1) {
                    body.nextElementSibling.classList.remove('hidden');
                }
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
