{{--
    Shared create/edit form.
    Expects: $formAction, $formMethod ('POST'|'PUT'), $submitLabel, $cancelUrl,
             $websites and an optional $testimonial.
--}}
@php
    $record = $testimonial ?? null;

    $currentWebsite = old('website_id', $record->website_id ?? '');
    $currentName = old('customer_name', $record->customer_name ?? '');
    $currentRating = old('rating', $record->rating ?? '');
    $currentContent = old('content', $record->content ?? '');

    // A stored rating can be any numeric value, so highlight the rounded star count but
    // resubmit the original value untouched unless the user actually picks a new one.
    $starCount = is_numeric($currentRating) ? max(0, min(5, (int) round($currentRating))) : 0;

    $currentImage = null;
    if (!empty($record->image) && \Illuminate\Support\Facades\Storage::disk('public')->exists('/images/testimonials/' . $record->image)) {
        $currentImage = asset('storage/images/testimonials/' . $record->image);
    }
@endphp

<div class="space-y-6">
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

    <form id="testimonialForm" method="POST" action="{{ $formAction }}" enctype="multipart/form-data"
        class="rounded-lg border border-erp-line bg-white shadow-sm">
        @csrf
        @if ($formMethod === 'PUT')
            @method('PUT')
        @endif

        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">{{ $submitLabel }}</h2>
            <p class="mt-1 text-sm text-erp-mute">The customer name, rating and review text are shown on the storefront.</p>
        </div>

        <div class="grid gap-6 p-5 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-7">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website <span class="text-rose-600">*</span></span>
                    <select name="website_id" id="website_id" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach (collect($websites ?? []) as $website)
                            <option value="{{ $website->id }}" {{ (string) $currentWebsite === (string) $website->id ? 'selected' : '' }}>{{ $website->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert">@error('website_id'){{ $message }}@enderror</span>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customer Name <span class="text-rose-600">*</span></span>
                    <input type="text" name="customer_name" id="customer_name" value="{{ $currentName }}" maxlength="255" placeholder="e.g. Ayesha Khan"
                        class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span class="mt-1 block text-xs text-erp-mute">Each customer name can only be used once.</span>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="customer_name_alert">@error('customer_name'){{ $message }}@enderror</span>
                </label>

                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Rating <span class="text-rose-600">*</span></span>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="flex gap-1" id="ratingStars">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" data-value="{{ $i }}"
                                    class="rating-star text-2xl leading-none transition {{ $i <= $starCount ? 'text-amber-500' : 'text-slate-300' }}"
                                    aria-label="{{ $i }} star{{ $i === 1 ? '' : 's' }}">&#9733;</button>
                            @endfor
                        </div>
                        <span class="text-sm font-bold text-erp-mute" id="ratingLabel">{{ $currentRating !== '' && $currentRating !== null ? $currentRating . ' / 5' : 'Not rated' }}</span>
                    </div>
                    <input type="hidden" name="rating" id="rating" value="{{ $currentRating }}">
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="rating_alert">@error('rating'){{ $message }}@enderror</span>
                </div>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Content <span class="text-rose-600">*</span></span>
                    <textarea name="content" id="content" rows="6" maxlength="2000" placeholder="What did the customer say?"
                        class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ $currentContent }}</textarea>
                    <span class="mt-1 block text-xs text-erp-mute"><span id="contentCount">0</span> / 2000 characters</span>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="content_alert">@error('content'){{ $message }}@enderror</span>
                </label>
            </div>

            <div class="lg:col-span-5">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Customer Photo</span>
                <div class="mt-2 overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                    <div class="mx-auto flex aspect-square max-w-[220px] items-center justify-center p-4">
                        <img id="preview" src="{{ $currentImage ?: asset('storage/images/no-image.png') }}" alt="Customer photo"
                            class="h-full w-full rounded-full object-cover">
                    </div>
                </div>
                <label class="testimonial-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                    <i class="icofont icofont-upload-alt text-base"></i>
                    Choose photo
                    <input type="file" name="image" id="image" class="hidden" accept=".jpg,.jpeg,.png,.webp">
                </label>
                <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="imageName"></p>
                <p class="mt-1 text-xs font-bold text-rose-600" id="image_alert">@error('image'){{ $message }}@enderror</p>

                <div class="mt-3 flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    <i class="icofont icofont-info-circle mt-0.5 text-base"></i>
                    <span>Optional. JPG, PNG or WEBP up to <strong>1MB</strong>. A square picture looks best.</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-2 border-t border-erp-line px-5 py-4 sm:flex-row sm:justify-end">
            <a href="{{ $cancelUrl }}"
                class="inline-flex h-10 items-center justify-center rounded-lg border border-erp-line px-5 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</a>
            <button type="submit" id="btn_submit"
                class="inline-flex h-10 items-center justify-center rounded-lg bg-erp px-8 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .testimonial-drop {
            transition: border-color .15s ease, background-color .15s ease;
        }

        .testimonial-drop:hover {
            border-color: #4CAF50;
            background-color: #f0fdf4;
        }
    </style>
@endpush

@push('scripts')
    <script>
        var PLACEHOLDER_IMAGE = @js(asset('storage/images/no-image.png'));

        function paintStars(value) {
            var rounded = Math.round(Number(value) || 0);

            document.querySelectorAll('#ratingStars .rating-star').forEach(function (star) {
                var on = Number(star.getAttribute('data-value')) <= rounded;
                star.classList.toggle('text-amber-500', on);
                star.classList.toggle('text-slate-300', !on);
            });
        }

        jQuery(function () {
            var ratingInput = document.getElementById('rating');
            var ratingLabel = document.getElementById('ratingLabel');
            var contentBox = document.getElementById('content');
            var contentCount = document.getElementById('contentCount');

            document.querySelectorAll('#ratingStars .rating-star').forEach(function (star) {
                star.addEventListener('click', function () {
                    ratingInput.value = this.getAttribute('data-value');
                    ratingLabel.textContent = ratingInput.value + ' / 5';
                    document.getElementById('rating_alert').textContent = '';
                    paintStars(ratingInput.value);
                });

                star.addEventListener('mouseenter', function () {
                    paintStars(this.getAttribute('data-value'));
                });
            });

            document.getElementById('ratingStars').addEventListener('mouseleave', function () {
                paintStars(ratingInput.value);
            });

            contentCount.textContent = contentBox.value.length;
            contentBox.addEventListener('input', function () {
                contentCount.textContent = this.value.length;
            });

            document.getElementById('image').addEventListener('change', function () {
                var allowed = /\.(jpg|jpeg|png|webp)$/i;
                var alertBox = document.getElementById('image_alert');

                alertBox.textContent = '';
                document.getElementById('imageName').textContent = '';

                if (!this.files || !this.files[0]) {
                    return;
                }

                var file = this.files[0];

                if (!allowed.test(file.name)) {
                    alertBox.textContent = 'Only JPG, PNG and WEBP images are accepted.';
                    this.value = '';
                    return;
                }

                // The server caps uploads at 1MB, so catch it before the round trip.
                if (file.size > 1024 * 1024) {
                    alertBox.textContent = 'This image is larger than 1MB.';
                    this.value = '';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (event) {
                    document.getElementById('preview').setAttribute('src', event.target.result);
                };
                reader.readAsDataURL(file);

                document.getElementById('imageName').textContent = file.name;
            });

            document.getElementById('testimonialForm').addEventListener('submit', function (event) {
                var invalid = null;

                ['website_alert', 'customer_name_alert', 'rating_alert', 'content_alert'].forEach(function (id) {
                    document.getElementById(id).textContent = '';
                });

                if (!contentBox.value.trim()) {
                    document.getElementById('content_alert').textContent = 'Please write the review content.';
                    invalid = 'content';
                }
                if (!ratingInput.value) {
                    document.getElementById('rating_alert').textContent = 'Please pick a rating.';
                    invalid = 'rating';
                }
                if (!document.getElementById('customer_name').value.trim()) {
                    document.getElementById('customer_name_alert').textContent = 'Please enter the customer name.';
                    invalid = 'customer_name';
                }
                if (!jQuery('#website_id').val()) {
                    document.getElementById('website_alert').textContent = 'Please select a website.';
                    invalid = 'website_id';
                }

                if (invalid) {
                    event.preventDefault();
                    if (invalid === 'website_id') {
                        jQuery('#website_id').select2('open');
                    } else if (invalid !== 'rating') {
                        document.getElementById(invalid).focus();
                    }
                    return;
                }

                var button = document.getElementById('btn_submit');
                button.disabled = true;
                button.textContent = 'Please wait...';
            });
        });
    </script>
@endpush
