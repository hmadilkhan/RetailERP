@extends('layouts.master-tailwind')

@section('title', 'Website Sliders')
@section('page_title', 'Website Sliders')
@section('page_subtitle', 'Upload and manage the hero slides shown on your storefront home page and department pages.')

@section('content')
    @php
        $companyId = session('company_id');

        $websiteCollection = collect($websites ?? []);
        $departmentCollection = collect($departments ?? []);
        $sliderRows = collect($websiteSliderList ?? []);
        $bindProducts = collect($slider_bin_products ?? []);

        $websiteMap = $websiteCollection->keyBy('id');

        $defaultSliders = $sliderRows->where('slider_type', 'default')->values();
        $departmentSliders = $sliderRows->where('slider_type', 'department')->values();

        // Home slides are grouped per website; the "Remove All" action on a group header
        // clears every slide of that website, exactly like the legacy screen did.
        $defaultGroups = $defaultSliders->groupBy('website_id');

        // Department slides group on website + department, because the delete endpoint
        // keys them on both values.
        $departmentGroups = $departmentSliders->groupBy(fn ($row) => $row->website_id . '|' . $row->department_slider);

        $mediaBase = asset('storage/images/website/sliders/' . $companyId);
        $videoExtensions = ['mp4', 'webm', 'ogg'];

        $isVideoFile = fn ($file) => $file && in_array(strtolower(pathinfo((string) $file, PATHINFO_EXTENSION)), $videoExtensions, true);

        $coveredWebsites = $sliderRows->pluck('website_id')->unique()->count();
    @endphp

    <div class="space-y-6" id="sliderPage">
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
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Slides</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($sliderRows->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active slides across all websites</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Home Slides</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($defaultSliders->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Shown on the storefront home page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department Slides</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($departmentSliders->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Across {{ number_format($departmentGroups->count()) }} department {{ $departmentGroups->count() === 1 ? 'page' : 'pages' }}</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites Covered</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">
                    <span>{{ number_format($coveredWebsites) }}</span><span class="text-lg font-bold text-erp-mute">/{{ number_format($websiteCollection->count()) }}</span>
                </div>
                <p class="mt-2 text-sm text-erp-mute">Websites with at least one slide</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="overflow-x-auto border-b border-erp-line px-5 pt-4">
                <nav class="flex min-w-max gap-1 pb-px" id="sliderTabs">
                    <button type="button" data-tab="default" id="sliderTabNav"
                        class="slider-tab rounded-t-lg border-b-2 border-transparent px-4 py-3 text-sm font-bold text-erp-mute transition hover:text-erp-dark">
                        Home Sliders
                        <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-black text-erp-mute">{{ $defaultSliders->count() }}</span>
                    </button>
                    <button type="button" data-tab="department" id="departmentSliderNav"
                        class="slider-tab rounded-t-lg border-b-2 border-transparent px-4 py-3 text-sm font-bold text-erp-mute transition hover:text-erp-dark">
                        Department Sliders
                        <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-black text-erp-mute">{{ $departmentSliders->count() }}</span>
                    </button>
                </nav>
            </div>

            <div class="slider-panel hidden" data-panel="default">
                @include('v2.website.sliders.partials.default')
            </div>
            <div class="slider-panel hidden" data-panel="department">
                @include('v2.website.sliders.partials.department')
            </div>
        </section>
    </div>

    {{-- Shared delete confirmation --}}
    <div id="confirmModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeConfirm()">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink" id="confirmTitle">Remove Slide</h3>
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

@push('styles')
    <style>
        .slider-tab.is-active {
            border-color: #4CAF50;
            color: #2E7D32;
        }

        .slider-drop {
            transition: border-color .15s ease, background-color .15s ease;
        }

        .slider-drop:hover {
            border-color: #4CAF50;
            background-color: #f0fdf4;
        }
    </style>
@endpush

@push('scripts')
    <script>
        var SLIDER_ROUTES = {
            products: @js(route('getWebsiteProd')),
            departments: @js(route('getDepart_n_subDepart_wb'))
        };
        var CSRF_TOKEN = @js(csrf_token());
        var PLACEHOLDER_IMAGE = @js(asset('storage/images/no-image.png'));

        var confirmState = null;

        /* ----------------------------------------------------------------- tabs */
        function activateTab(key) {
            document.querySelectorAll('.slider-panel').forEach(function (panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-panel') !== key);
            });
            document.querySelectorAll('.slider-tab').forEach(function (tab) {
                tab.classList.toggle('is-active', tab.getAttribute('data-tab') === key);
            });

            // Select2 sizes itself at init time, so anything built while its panel was
            // hidden comes out 0px wide -- rebuild the panel that just became visible.
            refreshSelect2In(jQuery('.slider-panel[data-panel="' + key + '"]'));
        }

        // Rebuilds every on-screen select2 inside the given container. A select sitting in
        // a display:none ancestor reports as hidden, so it is left for its own reveal.
        function refreshSelect2In($container) {
            $container.find('.v2-select2:visible').each(function () {
                initSelect2(jQuery(this));
            });
        }

        /* --------------------------------------------------------------- modals */
        function openModal(id) {
            var modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Select2 measures its width at init time, and inside a hidden modal that is
            // 0px -- so every modal dropdown is rebuilt once the modal is on screen.
            refreshSelect2In(jQuery('#' + id));
        }

        function closeModal(id) {
            var modal = document.getElementById(id);
            if (!modal) {
                return;
            }
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function initSelect2($select) {
            if (!window.jQuery || !jQuery.fn.select2) {
                return;
            }
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                allowClear: !$select.prop('multiple') && $select.find('option[value=""]').length > 0,
                dropdownCssClass: 'v2-select2-dropdown',
                placeholder: $select.data('placeholder') || 'Select'
            });
        }

        /* -------------------------------------------------------------- confirm */
        function openConfirm(options) {
            confirmState = options;
            document.getElementById('confirmTitle').textContent = options.title || 'Remove Slide';
            document.getElementById('confirmMessage').textContent = options.message;

            var modal = document.getElementById('confirmModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirm() {
            confirmState = null;
            var modal = document.getElementById('confirmModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        /* -------------------------------------------------------- media preview */
        function showMedia(imageId, videoId, url, isVideo) {
            var image = document.getElementById(imageId);
            var video = document.getElementById(videoId);

            if (isVideo) {
                video.setAttribute('src', url || '');
                video.classList.remove('hidden');
                image.classList.add('hidden');
            } else {
                video.removeAttribute('src');
                video.classList.add('hidden');
                image.setAttribute('src', url || PLACEHOLDER_IMAGE);
                image.classList.remove('hidden');
            }
        }

        // Live preview for a freshly picked file, plus the file name next to the button.
        function previewFile(input, imageId, videoId, nameId) {
            var imagePattern = /\.(jpg|jpeg|png|webp)$/i;
            var videoPattern = /\.(mp4|webm|ogg)$/i;

            if (nameId) {
                document.getElementById(nameId).textContent = '';
            }

            if (!input.files || !input.files[0]) {
                return;
            }

            var file = input.files[0];

            if (!imagePattern.test(file.name) && !videoPattern.test(file.name)) {
                alert('Invalid file type. Please choose an image (jpg, jpeg, png, webp) or a video (mp4, webm, ogg).');
                input.value = '';
                return;
            }

            // The server rejects anything over 1MB, so catch it before the upload starts.
            if (file.size > 1024 * 1024) {
                alert('This file is larger than 1MB. Please choose a smaller file.');
                input.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                showMedia(imageId, videoId, event.target.result, videoPattern.test(file.name));
            };
            reader.readAsDataURL(file);

            if (nameId) {
                document.getElementById(nameId).textContent = file.name;
            }
        }

        /* ------------------------------------------------------------ ajax load */
        function fillSelect(elementId, rows, valueKey, labelKey, placeholder, selected) {
            var $select = jQuery('#' + elementId);
            var multiple = !!$select.prop('multiple');
            var selectedList = (selected === null || selected === undefined || selected === '')
                ? []
                : (Array.isArray(selected) ? selected.map(String) : [String(selected)]);

            $select.empty().prop('disabled', false);

            if (!multiple) {
                $select.append(new Option(placeholder, '', false, false));
            }

            (rows || []).forEach(function (row) {
                var value = String(row[valueKey]);
                $select.append(new Option(row[labelKey], value, false, selectedList.indexOf(value) !== -1));
            });

            $select.val(multiple ? selectedList : (selectedList[0] || '')).trigger('change.select2');
        }

        function resetSelect(elementId, placeholder) {
            var $select = jQuery('#' + elementId);
            $select.empty().prop('disabled', true);
            if (!$select.prop('multiple')) {
                $select.append(new Option(placeholder, '', false, false));
            }
            $select.val($select.prop('multiple') ? [] : '').trigger('change.select2');
        }

        function loadDepartments(websiteId, elementId, selected) {
            if (!websiteId) {
                resetSelect(elementId, 'Select Department');
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.post(SLIDER_ROUTES.departments, { _token: CSRF_TOKEN, website: websiteId, mode: 'depart' })
                .done(function (response) {
                    fillSelect(elementId, response === 0 ? [] : response, 'department_id', 'department_name', 'Select Department', selected);
                });
        }

        function loadSubDepartments(departmentId, websiteId, elementId, selected) {
            if (!departmentId) {
                resetSelect(elementId, 'Select Sub Department');
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.post(SLIDER_ROUTES.departments, { _token: CSRF_TOKEN, depart: departmentId, website: websiteId, mode: 'subdepart' })
                .done(function (response) {
                    fillSelect(elementId, response === 0 ? [] : response, 'sub_department_id', 'sub_depart_name', 'Select Sub Department', selected);
                });
        }

        function loadProducts(websiteId, elementId, departmentId, subDepartmentId, selected) {
            if (!websiteId) {
                resetSelect(elementId, 'Select Product');
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.post(SLIDER_ROUTES.products, {
                _token: CSRF_TOKEN,
                id: websiteId,
                department: departmentId || '',
                subDepart: subDepartmentId || ''
            }).done(function (response) {
                fillSelect(elementId, response === 0 ? [] : response, 'id', 'product_name', 'Select Product', selected);
            });
        }

        // Filters a card grid by the text typed in its search box.
        function bindGridFilter(inputId, gridSelector, emptySelector) {
            var input = document.getElementById(inputId);
            if (!input) {
                return;
            }

            input.addEventListener('input', function () {
                var term = this.value.trim().toLowerCase();
                var visible = 0;

                document.querySelectorAll(gridSelector).forEach(function (group) {
                    var match = group.getAttribute('data-search').indexOf(term) !== -1;
                    group.classList.toggle('hidden', !match);
                    if (match) {
                        visible += 1;
                    }
                });

                var empty = document.querySelector(emptySelector);
                if (empty) {
                    empty.classList.toggle('hidden', visible > 0);
                }
            });
        }

        jQuery(function () {
            activateTab(String(window.location.hash || '').toLowerCase().indexOf('department') !== -1 ? 'department' : 'default');

            document.querySelectorAll('.slider-tab').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var key = this.getAttribute('data-tab');
                    activateTab(key);
                    history.replaceState(null, '', key === 'department' ? '#departmentSliderNav' : '#sliderTab');
                });
            });

            document.getElementById('btnConfirmYes').addEventListener('click', function () {
                if (!confirmState) {
                    return;
                }

                // Blank mode = remove every slide in the group, an id = remove just that slide.
                if (confirmState.modeInputId) {
                    document.getElementById(confirmState.modeInputId).value = confirmState.modeValue || '';
                }

                this.disabled = true;
                this.classList.add('opacity-60');
                document.getElementById(confirmState.formId).submit();
            });

            bindGridFilter('defaultSliderFilter', '.default-slider-group', '#defaultNoMatch');
            bindGridFilter('departmentSliderFilter', '.department-slider-group', '#departmentNoMatch');

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') {
                    return;
                }
                closeConfirm();
                closeModal('slideEdit_Modal');
                closeModal('departmentSlideEdit_Modal');
            });
        });
    </script>
@endpush
