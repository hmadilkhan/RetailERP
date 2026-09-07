{{-- Department page sliders: create form, grouped slide grid and the edit modal. --}}
<div class="space-y-6 p-5">

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Add Department Slide</h2>
            <p class="mt-1 text-sm text-erp-mute">These slides appear on a department page and can promote a hand-picked set of products.</p>
        </div>

        <form id="departmentSliderForm" action="{{ route('sliderStore') }}" method="post" enctype="multipart/form-data"
            class="grid gap-6 p-5 lg:grid-cols-12">
            @csrf
            <input type="hidden" name="slider_type" value="{{ Hash::make('department') }}">

            <div class="space-y-4 lg:col-span-7">
                <div class="grid gap-4 sm:grid-cols-5">
                    <div class="sm:col-span-3">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Desktop Slide <span class="text-rose-600">*</span></span>
                            <span class="text-[11px] font-bold text-erp-mute">1520 &times; 460</span>
                        </div>
                        <div class="overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="flex aspect-[16/7] items-center justify-center">
                                <img id="previewImg_deptslide" src="{{ asset('storage/images/placeholder.jpg') }}" alt="Desktop slide preview"
                                    class="h-full w-full object-contain">
                                <video id="videoPreview_deptslide" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Choose desktop file
                            <input type="file" name="desktop_slide_dept" id="desktop_slide_dept" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewImg_deptslide', 'videoPreview_deptslide', 'desktopDeptSlideName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="desktopDeptSlideName"></p>
                        @error('desktop_slide_dept')
                            <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 hidden text-xs font-bold text-rose-600" id="desktop_slide_dept_alert"></p>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mobile Slide</span>
                            <span class="text-[11px] font-bold text-erp-mute">Optional</span>
                        </div>
                        <div class="overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="flex aspect-[3/4] items-center justify-center">
                                <img id="previewMobileSlide_deptslide" src="{{ asset('storage/images/placeholder.jpg') }}" alt="Mobile slide preview"
                                    class="h-full w-full object-contain">
                                <video id="videoMobilePreview_deptslide" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Choose mobile file
                            <input type="file" name="mobile_slide_dept" id="mobile_slide_dept" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewMobileSlide_deptslide', 'videoMobilePreview_deptslide', 'mobileDeptSlideName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="mobileDeptSlideName"></p>
                        @error('mobile_slide_dept')
                            <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    <i class="icofont icofont-info-circle mt-0.5 text-base"></i>
                    <span>Images (jpg, jpeg, png, webp) and videos (mp4, webm, ogg) are accepted, up to <strong>1MB</strong> per file.</span>
                </div>
            </div>

            <div class="space-y-4 lg:col-span-5">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Slider Name</span>
                    <input type="text" name="slider_name" id="slider_name" value="{{ old('slider_name') }}" placeholder="Summer Sale"
                        class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span class="mt-1 block text-xs text-erp-mute">Used to build the slider slug.</span>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website <span class="text-rose-600">*</span></span>
                    <select name="website_dept_slide" id="website_dept_slide" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}" {{ old('website_dept_slide') == $val->id ? 'selected' : '' }}>{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_dept_alert">@error('website_dept_slide'){{ $message }}@enderror</span>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department <span class="text-rose-600">*</span></span>
                    <select name="department_dpt_slide" id="department_dpt_slide" data-placeholder="Search department..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Department</option>
                        @foreach ($departmentCollection as $val)
                            <option value="{{ $val->department_id }}" {{ old('department_dpt_slide') == $val->department_id ? 'selected' : '' }}>{{ $val->department_name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs text-erp-mute">The department page this slide is displayed on.</span>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="department_dept_alert">@error('department_dpt_slide'){{ $message }}@enderror</span>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Featured Products</span>
                    <select name="product_dpt_slide[]" id="product_dpt_slide" data-placeholder="Search products..." multiple disabled
                        class="v2-select2 mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></select>
                    <span class="mt-1 block text-xs text-erp-mute">Pick a website first. You can select more than one product.</span>
                </label>

                <button type="submit" id="btn_create_dept_slide"
                    class="h-10 w-full rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                    Add Department Slide
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Department Slides</h2>
                <p class="mt-1 text-sm text-erp-mute">Grouped by website and department. Click a slide to replace its media or change the featured products.</p>
            </div>
            <input type="search" id="departmentSliderFilter" placeholder="Filter by website or department..."
                class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-96">
        </div>

        <div class="space-y-5 p-5">
            @forelse ($departmentGroups as $groupKey => $slides)
                @php
                    [$groupWebsiteId, $groupDepartmentId] = explode('|', $groupKey);
                    $website = $websiteMap[$groupWebsiteId] ?? null;
                    $websiteName = $website->name ?? ('Website #' . $groupWebsiteId);
                    $websiteActive = ($website->status ?? 1) == 1;
                    $departmentName = $slides->first()->department_slider_name ?: ('Department #' . $groupDepartmentId);
                    $formId = 'DestroyFormDept' . $groupWebsiteId . '_' . $groupDepartmentId;
                    $modeInputId = 'mode_dept_' . $groupWebsiteId . '_' . $groupDepartmentId;
                    $searchTerms = strtolower($websiteName . ' ' . $departmentName);
                @endphp

                <article class="department-slider-group overflow-hidden rounded-lg border border-erp-line" data-search="{{ $searchTerms }}">
                    <header class="flex flex-col gap-3 border-b border-erp-line bg-erp-soft px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-sm font-black text-erp-ink">{{ $websiteName }}</h3>
                            <span class="text-erp-mute">/</span>
                            <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-bold text-indigo-700 ring-1 ring-indigo-200">
                                {{ $departmentName }}
                            </span>
                            <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-erp-mute ring-1 ring-erp-line">
                                {{ $slides->count() }} {{ $slides->count() === 1 ? 'slide' : 'slides' }}
                            </span>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $websiteActive ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-300' }}">
                                {{ $websiteActive ? 'Active' : 'In-Active' }}
                            </span>
                        </div>
                        <button type="button"
                            onclick="confirmRemoveAllDepartment(@js($formId), @js($modeInputId), @js($websiteName), @js($departmentName))"
                            class="self-start rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                            Remove All
                        </button>
                    </header>

                    <form id="{{ $formId }}" action="{{ route('destroySliderImage', [$groupWebsiteId]) }}" method="post" class="hidden">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="mode{{ $groupWebsiteId }}" id="{{ $modeInputId }}">
                        <input type="hidden" name="id" value="{{ $groupWebsiteId }}">
                        <input type="hidden" name="depart" value="{{ $groupDepartmentId }}">
                    </form>

                    <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($slides as $val)
                            @php
                                $slideUrl = $mediaBase . '/' . $val->website_id . '/' . $val->slide;
                                $mobileUrl = $val->mobile_slide ? $mediaBase . '/' . $val->website_id . '/' . $val->mobile_slide : '';
                                $slideIsVideo = $isVideoFile($val->slide);
                                $mobileIsVideo = $isVideoFile($val->mobile_slide);
                                $boundProducts = $bindProducts->where('slider_id', $val->id)->pluck('product_id')->values();

                                $payload = [
                                    'id' => $val->id,
                                    'websiteId' => $val->website_id,
                                    'websiteName' => $websiteName,
                                    'departmentId' => $val->department_slider,
                                    'departmentName' => $departmentName,
                                    'slideUrl' => $slideUrl,
                                    'mobileUrl' => $mobileUrl,
                                    'slideIsVideo' => $slideIsVideo,
                                    'mobileIsVideo' => $mobileIsVideo,
                                    'products' => $boundProducts,
                                    'formId' => $formId,
                                    'modeInputId' => $modeInputId,
                                ];
                            @endphp

                            <div class="group overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm transition hover:border-erp hover:shadow-panel">
                                <button type="button" onclick="openDepartmentSlideEdit(@js($payload))"
                                    class="relative block aspect-[16/7] w-full overflow-hidden bg-erp-soft">
                                    @if ($slideIsVideo)
                                        <video src="{{ $slideUrl }}" class="h-full w-full object-cover" muted preload="metadata"></video>
                                        <span class="absolute inset-0 flex items-center justify-center bg-slate-900/25 text-3xl text-white">
                                            <i class="icofont icofont-play-alt-1"></i>
                                        </span>
                                    @else
                                        <img src="{{ $slideUrl }}" alt="{{ $val->slide }}" class="h-full w-full object-cover">
                                    @endif
                                    @if ($mobileUrl)
                                        <span class="absolute right-2 top-2 rounded-full bg-white/90 px-2 py-1 text-[10px] font-black uppercase tracking-wide text-erp-dark ring-1 ring-erp-line">
                                            + Mobile
                                        </span>
                                    @endif
                                </button>

                                <div class="space-y-3 p-4">
                                    <div>
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.12em] text-indigo-700 ring-1 ring-indigo-200">
                                            Department
                                        </span>
                                        <p class="mt-2 truncate text-sm font-bold text-erp-ink" title="{{ $departmentName }}">{{ $departmentName }}</p>
                                        <p class="mt-0.5 text-xs font-bold text-erp-mute">
                                            {{ $boundProducts->count() }} featured {{ $boundProducts->count() === 1 ? 'product' : 'products' }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="openDepartmentSlideEdit(@js($payload))"
                                            class="flex-1 rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                            Edit
                                        </button>
                                        <button type="button"
                                            onclick="confirmRemoveDepartmentSlide(@js($formId), @js($modeInputId), @js($val->id), @js($websiteName), @js($departmentName))"
                                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-erp-line px-5 py-14 text-center">
                    <p class="text-sm font-bold text-erp-ink">No department slides yet</p>
                    <p class="mt-1 text-sm text-erp-mute">Upload your first department banner using the form above.</p>
                </div>
            @endforelse

            <div id="departmentNoMatch" class="hidden rounded-lg border border-dashed border-erp-line px-5 py-14 text-center text-sm text-erp-mute">
                No slides match your search.
            </div>
        </div>
    </section>
</div>

{{-- Edit department slide --}}
<div id="departmentSlideEdit_Modal" class="fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/60 px-4 py-8"
    onclick="if (event.target === this) closeModal('departmentSlideEdit_Modal')">
    <div class="w-full max-w-3xl rounded-lg bg-white shadow-menu">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-erp-ink">Edit Department Slide</h3>
                <p class="mt-1 text-sm text-erp-mute">
                    <span id="webname_label_deptmd" class="font-bold text-erp-dark"></span>
                    <span class="text-erp-mute"> / </span>
                    <span id="department_label_deptmd" class="font-bold text-erp-dark"></span>
                </p>
            </div>
            <button type="button" onclick="closeModal('departmentSlideEdit_Modal')"
                class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute transition hover:bg-slate-100">&times;</button>
        </div>

        <form id="editSlideForm_deptmd" action="{{ route('updateSliderImage') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="webname_deptmd" name="webName">
            <input type="hidden" id="webid_deptmd" name="webId">
            <input type="hidden" id="departSlider_deptmd" name="department_slider">
            <input type="hidden" id="id_deptmd" name="id">

            <div class="slider-modal-body grid gap-6 px-5 py-5 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Desktop Slide</span>
                        <div class="mt-2 overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="flex aspect-[16/7] items-center justify-center">
                                <img id="previewslide_deptEdtmd" src="{{ asset('storage/images/no-image.png') }}" alt="Desktop slide" class="h-full w-full object-contain">
                                <video id="slideVd_deptEdtmd" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Replace desktop file
                            <input type="file" name="desktop_slide" id="desktopslide_deptEdtmd" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewslide_deptEdtmd', 'slideVd_deptEdtmd', 'deptSlideMdName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="deptSlideMdName"></p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mobile Slide</span>
                        <div class="mt-2 overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="mx-auto flex aspect-[3/4] max-w-[180px] items-center justify-center">
                                <img id="previewMobileSlide_deptEdtmd" src="{{ asset('storage/images/no-image.png') }}" alt="Mobile slide" class="h-full w-full object-contain">
                                <video id="previewMobileSlideVd_deptEdtmd" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Replace mobile file
                            <input type="file" name="mobile_slide" id="mobile_slide_deptEdtmd" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewMobileSlide_deptEdtmd', 'previewMobileSlideVd_deptEdtmd', 'deptMobileSlideMdName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="deptMobileSlideMdName"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                        <i class="icofont icofont-info-circle mt-0.5 text-base"></i>
                        <span>Leave a file empty to keep the current media. Maximum size is <strong>1MB</strong>.</span>
                    </div>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Featured Products</span>
                        <select name="product_dpt_slide[]" id="product_dpt_slide_deptEdtmd" data-placeholder="Search products..." multiple
                            class="v2-select2 mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></select>
                        <span class="mt-1 block text-xs text-erp-mute">Saving replaces the whole list of featured products.</span>
                    </label>
                </div>
            </div>
        </form>

        <div class="flex flex-col gap-2 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <button type="button" id="btn_remove_deptslidmd"
                class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Remove Slide</button>
            <div class="flex gap-2 sm:justify-end">
                <button type="button" onclick="closeModal('departmentSlideEdit_Modal')"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Close</button>
                <button type="button" id="btn_modify_deptslidmd"
                    class="rounded-lg bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        var currentDepartmentSlide = null;

        function openDepartmentSlideEdit(slide) {
            currentDepartmentSlide = slide;

            // Reset first -- it also clears the hidden inputs that are filled in below.
            document.getElementById('editSlideForm_deptmd').reset();
            document.getElementById('deptSlideMdName').textContent = '';
            document.getElementById('deptMobileSlideMdName').textContent = '';

            document.getElementById('webname_label_deptmd').textContent = slide.websiteName;
            document.getElementById('department_label_deptmd').textContent = slide.departmentName;
            document.getElementById('webname_deptmd').value = slide.websiteName;
            document.getElementById('webid_deptmd').value = slide.websiteId;
            document.getElementById('departSlider_deptmd').value = slide.departmentId;
            document.getElementById('id_deptmd').value = slide.id;

            showMedia('previewslide_deptEdtmd', 'slideVd_deptEdtmd', slide.slideUrl, slide.slideIsVideo);
            showMedia('previewMobileSlide_deptEdtmd', 'previewMobileSlideVd_deptEdtmd', slide.mobileUrl, slide.mobileIsVideo);

            // The whole website catalogue is offered here (not just this department), so a
            // slide that already features a cross-department product keeps that binding.
            resetSelect('product_dpt_slide_deptEdtmd', 'Select Product');
            loadProducts(slide.websiteId, 'product_dpt_slide_deptEdtmd', '', '', slide.products);

            openModal('departmentSlideEdit_Modal');
        }

        function confirmRemoveDepartmentSlide(formId, modeInputId, slideId, websiteName, departmentName) {
            openConfirm({
                formId: formId,
                modeInputId: modeInputId,
                modeValue: slideId,
                title: 'Remove Department Slide',
                message: 'Remove this ' + departmentName + ' slide from the ' + websiteName + ' website?'
            });
        }

        function confirmRemoveAllDepartment(formId, modeInputId, websiteName, departmentName) {
            openConfirm({
                formId: formId,
                modeInputId: modeInputId,
                modeValue: '',
                title: 'Remove All Department Slides',
                message: 'Remove every ' + departmentName + ' slide from the ' + websiteName + ' website? This cannot be undone.'
            });
        }

        jQuery(function () {
            // Featured products come from the whole website catalogue, so a department
            // banner can also promote an item that sits outside that department.
            jQuery('#website_dept_slide').on('change', function () {
                var websiteId = jQuery(this).val();

                if (!websiteId) {
                    resetSelect('product_dpt_slide', 'Select Product');
                    return;
                }

                loadProducts(websiteId, 'product_dpt_slide', '', '', []);
            });

            document.getElementById('departmentSliderForm').addEventListener('submit', function (event) {
                var websiteAlert = document.getElementById('website_dept_alert');
                var departmentAlert = document.getElementById('department_dept_alert');
                var slideAlert = document.getElementById('desktop_slide_dept_alert');
                var invalid = false;

                websiteAlert.textContent = '';
                departmentAlert.textContent = '';
                slideAlert.classList.add('hidden');

                if (!jQuery('#website_dept_slide').val()) {
                    websiteAlert.textContent = 'Please select a website.';
                    invalid = true;
                }

                if (!jQuery('#department_dpt_slide').val()) {
                    departmentAlert.textContent = 'Please select a department.';
                    invalid = true;
                }

                if (!document.getElementById('desktop_slide_dept').files.length) {
                    slideAlert.textContent = 'Please choose a desktop slide.';
                    slideAlert.classList.remove('hidden');
                    invalid = true;
                }

                if (invalid) {
                    event.preventDefault();
                    return;
                }

                var button = document.getElementById('btn_create_dept_slide');
                button.disabled = true;
                button.textContent = 'Please wait...';
            });

            document.getElementById('btn_modify_deptslidmd').addEventListener('click', function () {
                this.disabled = true;
                this.textContent = 'Saving...';
                document.getElementById('editSlideForm_deptmd').submit();
            });

            document.getElementById('btn_remove_deptslidmd').addEventListener('click', function () {
                if (!currentDepartmentSlide) {
                    return;
                }
                closeModal('departmentSlideEdit_Modal');
                confirmRemoveDepartmentSlide(
                    currentDepartmentSlide.formId,
                    currentDepartmentSlide.modeInputId,
                    currentDepartmentSlide.id,
                    currentDepartmentSlide.websiteName,
                    currentDepartmentSlide.departmentName
                );
            });
        });
    </script>
@endpush
