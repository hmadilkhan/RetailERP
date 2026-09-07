{{-- Home page sliders: create form, grouped slide grid and the edit modal. --}}
<div class="space-y-6 p-5">

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="border-b border-erp-line px-5 py-4">
            <h2 class="text-base font-bold text-erp-ink">Add Home Slide</h2>
            <p class="mt-1 text-sm text-erp-mute">Upload a desktop banner, optionally a mobile version, and pick where the slide takes the visitor.</p>
        </div>

        <form id="sliderCreateForm" action="{{ route('sliderStore') }}" method="post" enctype="multipart/form-data"
            class="grid gap-6 p-5 lg:grid-cols-12">
            @csrf

            <div class="space-y-4 lg:col-span-7">
                <div class="grid gap-4 sm:grid-cols-5">
                    <div class="sm:col-span-3">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Desktop Slide <span class="text-rose-600">*</span></span>
                            <span class="text-[11px] font-bold text-erp-mute">1520 &times; 460</span>
                        </div>
                        <div class="overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="flex aspect-[16/7] items-center justify-center">
                                <img id="previewImg" src="{{ asset('storage/images/placeholder.jpg') }}" alt="Desktop slide preview"
                                    class="h-full w-full object-contain">
                                <video id="videoPreview" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Choose desktop file
                            <input type="file" name="desktop_slide" id="desktop_slide" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewImg', 'videoPreview', 'desktopSlideName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="desktopSlideName"></p>
                        @error('desktop_slide')
                            <p class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 hidden text-xs font-bold text-rose-600" id="desktop_slide_alert"></p>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mobile Slide</span>
                            <span class="text-[11px] font-bold text-erp-mute">Optional</span>
                        </div>
                        <div class="overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="flex aspect-[3/4] items-center justify-center">
                                <img id="previewMobileSlide" src="{{ asset('storage/images/placeholder.jpg') }}" alt="Mobile slide preview"
                                    class="h-full w-full object-contain">
                                <video id="videoMobilePreview" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Choose mobile file
                            <input type="file" name="mobile_slide" id="mobile_slide" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewMobileSlide', 'videoMobilePreview', 'mobileSlideName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="mobileSlideName"></p>
                        @error('mobile_slide')
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
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website <span class="text-rose-600">*</span></span>
                    <select name="website" id="website" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}" {{ old('website') == $val->id ? 'selected' : '' }}>{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert">@error('website'){{ $message }}@enderror</span>
                </label>

                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Slide Links To</span>
                    <div class="mt-2 flex gap-2">
                        <label class="flex-1">
                            <input type="radio" name="navigato" value="department" class="peer sr-only">
                            <span class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-erp-line px-3 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp peer-checked:border-erp peer-checked:bg-emerald-50 peer-checked:text-erp-dark">
                                <i class="icofont icofont-listing-box text-base"></i> Department
                            </span>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="navigato" value="product" class="peer sr-only">
                            <span class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-erp-line px-3 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp peer-checked:border-erp peer-checked:bg-emerald-50 peer-checked:text-erp-dark">
                                <i class="icofont icofont-cube text-base"></i> Product
                            </span>
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-erp-mute">Leave both unselected for a slide that does not link anywhere.</p>
                </div>

                <div class="hidden" id="departmentbox">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inventory Department</span>
                        <select name="depart" id="depart" data-placeholder="Search department..."
                            class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">Select Department</option>
                            @foreach ($departmentCollection as $val)
                                <option value="{{ $val->department_id }}" {{ old('depart') == $val->department_id ? 'selected' : '' }}>{{ $val->department_name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="hidden space-y-4" id="productbox">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department</span>
                        <select id="depart_prod" data-placeholder="Search department..."
                            class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                            <option value="">Select Department</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Sub Department</span>
                        <select id="subDepartment_prod" data-placeholder="Search sub department..."
                            class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                            <option value="">Select Sub Department</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Product</span>
                        <select name="product" id="product" data-placeholder="Search product..."
                            class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                            <option value="">Select Product</option>
                        </select>
                    </label>
                </div>

                <button type="submit" id="btn_create"
                    class="h-10 w-full rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60">
                    Add Slide
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-lg border border-erp-line bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-erp-ink">Home Slides</h2>
                <p class="mt-1 text-sm text-erp-mute">Grouped by website. Click a slide to replace its media or change where it links.</p>
            </div>
            <input type="search" id="defaultSliderFilter" placeholder="Filter by website, department or product..."
                class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-96">
        </div>

        <div class="space-y-5 p-5">
            @forelse ($defaultGroups as $websiteId => $slides)
                @php
                    $website = $websiteMap[$websiteId] ?? null;
                    $websiteName = $website->name ?? ('Website #' . $websiteId);
                    $websiteActive = ($website->status ?? 1) == 1;
                    $searchTerms = strtolower(
                        $websiteName . ' ' . $slides->map(fn ($row) => trim(($row->invent_department_name ?? '') . ' ' . ($row->prod_name ?? '')))->implode(' ')
                    );
                @endphp

                <article class="default-slider-group overflow-hidden rounded-lg border border-erp-line" data-search="{{ $searchTerms }}">
                    <header class="flex flex-col gap-3 border-b border-erp-line bg-erp-soft px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-sm font-black text-erp-ink">{{ $websiteName }}</h3>
                            <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-bold text-erp-mute ring-1 ring-erp-line">
                                {{ $slides->count() }} {{ $slides->count() === 1 ? 'slide' : 'slides' }}
                            </span>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $websiteActive ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-300' }}">
                                {{ $websiteActive ? 'Active' : 'In-Active' }}
                            </span>
                        </div>
                        <button type="button"
                            onclick="confirmRemoveAll(@js('DestroyForm' . $websiteId), @js('mode_default_' . $websiteId), @js($websiteName))"
                            class="self-start rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                            Remove All
                        </button>
                    </header>

                    <form id="DestroyForm{{ $websiteId }}" action="{{ route('destroySliderImage', [$websiteId]) }}" method="post" class="hidden">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="mode{{ $websiteId }}" id="mode_default_{{ $websiteId }}">
                        <input type="hidden" name="id" value="{{ $websiteId }}">
                    </form>

                    <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($slides as $val)
                            @php
                                $slideUrl = $mediaBase . '/' . $val->website_id . '/' . $val->slide;
                                $mobileUrl = $val->mobile_slide ? $mediaBase . '/' . $val->website_id . '/' . $val->mobile_slide : '';
                                $slideIsVideo = $isVideoFile($val->slide);
                                $mobileIsVideo = $isVideoFile($val->mobile_slide);

                                if ($val->invent_department_id) {
                                    $targetType = 'Department';
                                    $targetLabel = $val->invent_department_name ?: ('#' . $val->invent_department_id);
                                    $targetChip = 'bg-indigo-50 text-indigo-700 ring-indigo-200';
                                } elseif ($val->prod_id) {
                                    $targetType = 'Product';
                                    $targetLabel = $val->prod_name ?: ('#' . $val->prod_id);
                                    $targetChip = 'bg-amber-50 text-amber-700 ring-amber-200';
                                } else {
                                    $targetType = 'No link';
                                    $targetLabel = 'Slide is not clickable';
                                    $targetChip = 'bg-slate-100 text-slate-600 ring-slate-300';
                                }

                                $payload = [
                                    'id' => $val->id,
                                    'websiteId' => $val->website_id,
                                    'websiteName' => $websiteName,
                                    'slideUrl' => $slideUrl,
                                    'mobileUrl' => $mobileUrl,
                                    'slideIsVideo' => $slideIsVideo,
                                    'mobileIsVideo' => $mobileIsVideo,
                                    'departmentId' => $val->invent_department_id,
                                    'productId' => $val->prod_id,
                                    'productDepartmentId' => $val->prod_dept_id,
                                    'productSubDepartmentId' => $val->prod_subdept_id,
                                ];
                            @endphp

                            <div class="group overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm transition hover:border-erp hover:shadow-panel">
                                <button type="button" onclick="openSlideEdit(@js($payload))"
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
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.12em] ring-1 {{ $targetChip }}">
                                            {{ $targetType }}
                                        </span>
                                        <p class="mt-2 truncate text-sm font-bold text-erp-ink" title="{{ $targetLabel }}">{{ $targetLabel }}</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" onclick="openSlideEdit(@js($payload))"
                                            class="flex-1 rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                            Edit
                                        </button>
                                        <button type="button"
                                            onclick="confirmRemoveSlide(@js('DestroyForm' . $websiteId), @js('mode_default_' . $websiteId), @js($val->id), @js($websiteName))"
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
                    <p class="text-sm font-bold text-erp-ink">No home slides yet</p>
                    <p class="mt-1 text-sm text-erp-mute">Upload your first banner using the form above.</p>
                </div>
            @endforelse

            <div id="defaultNoMatch" class="hidden rounded-lg border border-dashed border-erp-line px-5 py-14 text-center text-sm text-erp-mute">
                No slides match your search.
            </div>
        </div>
    </section>
</div>

{{-- Edit home slide --}}
<div id="slideEdit_Modal" class="fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/60 px-4 py-8"
    onclick="if (event.target === this) closeModal('slideEdit_Modal')">
    <div class="w-full max-w-3xl rounded-lg bg-white shadow-menu">
        <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
            <div>
                <h3 class="text-base font-bold text-erp-ink">Edit Home Slide</h3>
                <p class="mt-1 text-sm text-erp-mute"><span id="webname_label_md" class="font-bold text-erp-dark"></span></p>
            </div>
            <button type="button" onclick="closeModal('slideEdit_Modal')"
                class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute transition hover:bg-slate-100">&times;</button>
        </div>

        <form id="editSlideForm_md" action="{{ route('updateSliderImage') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="webname_md" name="webName">
            <input type="hidden" id="webid_md" name="webId">
            <input type="hidden" id="id_md" name="id">

            <div class="slider-modal-body grid gap-6 px-5 py-5 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Desktop Slide</span>
                        <div class="mt-2 overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="flex aspect-[16/7] items-center justify-center">
                                <img id="slideImgMD" src="{{ asset('storage/images/no-image.png') }}" alt="Desktop slide" class="h-full w-full object-contain">
                                <video id="slideVdMD" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Replace desktop file
                            <input type="file" name="slide_md" id="slide_md" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'slideImgMD', 'slideVdMD', 'slideMdName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="slideMdName"></p>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Mobile Slide</span>
                        <div class="mt-2 overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="mx-auto flex aspect-[3/4] max-w-[180px] items-center justify-center">
                                <img id="previewMobileSlide_md" src="{{ asset('storage/images/no-image.png') }}" alt="Mobile slide" class="h-full w-full object-contain">
                                <video id="previewMobileSlideVd_md" class="hidden h-full w-full object-contain" controls></video>
                            </div>
                        </div>
                        <label class="slider-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Replace mobile file
                            <input type="file" name="mobile_slide" id="mobile_slide_md" class="hidden"
                                accept=".jpg,.jpeg,.png,.webp,.mp4,.webm,.ogg"
                                onchange="previewFile(this, 'previewMobileSlide_md', 'previewMobileSlideVd_md', 'mobileSlideMdName')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="mobileSlideMdName"></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                        <i class="icofont icofont-info-circle mt-0.5 text-base"></i>
                        <span>Leave a file empty to keep the current media. Maximum size is <strong>1MB</strong>.</span>
                    </div>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Slide Links To</span>
                        <div class="mt-2 flex gap-2">
                            <label class="flex-1">
                                <input type="radio" name="navigato_md" id="navigat_depart_md" value="department" class="peer sr-only">
                                <span class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-erp-line px-3 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp peer-checked:border-erp peer-checked:bg-emerald-50 peer-checked:text-erp-dark">
                                    <i class="icofont icofont-listing-box text-base"></i> Department
                                </span>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="navigato_md" id="navigat_prod_md" value="product" class="peer sr-only">
                                <span class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-erp-line px-3 py-2.5 text-sm font-bold text-erp-text transition hover:border-erp peer-checked:border-erp peer-checked:bg-emerald-50 peer-checked:text-erp-dark">
                                    <i class="icofont icofont-cube text-base"></i> Product
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="hidden" id="departmentbox_md">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Inventory Department</span>
                            <select name="depart_md" id="depart_md" data-placeholder="Search department..."
                                class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <option value="">Select Department</option>
                                @foreach ($departmentCollection as $val)
                                    <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="hidden space-y-4" id="productbox_md">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department</span>
                            <select id="depart_editmd" data-placeholder="Search department..."
                                class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <option value="">Select Department</option>
                                @foreach ($departmentCollection as $val)
                                    <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Sub Department</span>
                            <select id="subDepartment_prod_editmd" data-placeholder="Search sub department..."
                                class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                                <option value="">Select Sub Department</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Product</span>
                            <select name="product_md" id="product_editmd" data-placeholder="Search product..."
                                class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                                <option value="">Select Product</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </form>

        <div class="flex flex-col gap-2 border-t border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <button type="button" id="btn_remove_md"
                class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Remove Slide</button>
            <div class="flex gap-2 sm:justify-end">
                <button type="button" onclick="closeModal('slideEdit_Modal')"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Close</button>
                <button type="button" id="btn_update_md"
                    class="rounded-lg bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        var currentSlide = null;

        function toggleDefaultTarget(mode, prefix) {
            var departmentBox = document.getElementById(prefix === 'md' ? 'departmentbox_md' : 'departmentbox');
            var productBox = document.getElementById(prefix === 'md' ? 'productbox_md' : 'productbox');

            departmentBox.classList.toggle('hidden', mode !== 'department');
            productBox.classList.toggle('hidden', mode !== 'product');

            // The box that just appeared may hold select2 controls that were built while
            // it was hidden, which leaves them 0px wide until they are rebuilt.
            refreshSelect2In(jQuery(mode === 'department' ? departmentBox : productBox));
        }

        function openSlideEdit(slide) {
            currentSlide = slide;

            // Reset first -- it also clears the hidden inputs that are filled in below.
            document.getElementById('editSlideForm_md').reset();
            document.getElementById('slideMdName').textContent = '';
            document.getElementById('mobileSlideMdName').textContent = '';

            document.getElementById('webname_label_md').textContent = slide.websiteName;
            document.getElementById('webname_md').value = slide.websiteName;
            document.getElementById('webid_md').value = slide.websiteId;
            document.getElementById('id_md').value = slide.id;

            showMedia('slideImgMD', 'slideVdMD', slide.slideUrl, slide.slideIsVideo);
            showMedia('previewMobileSlide_md', 'previewMobileSlideVd_md', slide.mobileUrl, slide.mobileIsVideo);

            jQuery('#depart_md').val('').trigger('change.select2');
            resetSelect('subDepartment_prod_editmd', 'Select Sub Department');
            resetSelect('product_editmd', 'Select Product');
            jQuery('#depart_editmd').val('').trigger('change.select2');

            if (slide.departmentId) {
                document.getElementById('navigat_depart_md').checked = true;
                toggleDefaultTarget('department', 'md');
                jQuery('#depart_md').val(String(slide.departmentId)).trigger('change.select2');
            } else if (slide.productId) {
                document.getElementById('navigat_prod_md').checked = true;
                toggleDefaultTarget('product', 'md');

                jQuery('#depart_editmd').val(slide.productDepartmentId ? String(slide.productDepartmentId) : '').trigger('change.select2');
                loadSubDepartments(slide.productDepartmentId, slide.websiteId, 'subDepartment_prod_editmd', slide.productSubDepartmentId);
                loadProducts(slide.websiteId, 'product_editmd', slide.productDepartmentId, slide.productSubDepartmentId, slide.productId);
            } else {
                document.getElementById('navigat_depart_md').checked = false;
                document.getElementById('navigat_prod_md').checked = false;
                toggleDefaultTarget('', 'md');
            }

            openModal('slideEdit_Modal');
        }

        function confirmRemoveSlide(formId, modeInputId, slideId, websiteName) {
            openConfirm({
                formId: formId,
                modeInputId: modeInputId,
                modeValue: slideId,
                title: 'Remove Slide',
                message: 'Remove this slide from the ' + websiteName + ' website?'
            });
        }

        function confirmRemoveAll(formId, modeInputId, websiteName) {
            openConfirm({
                formId: formId,
                modeInputId: modeInputId,
                modeValue: '',
                title: 'Remove All Slides',
                message: 'Remove every home slide from the ' + websiteName + ' website? This cannot be undone.'
            });
        }

        jQuery(function () {
            jQuery('input[name="navigato"]').on('change', function () {
                toggleDefaultTarget(this.value, '');

                if (this.value === 'department') {
                    jQuery('#product').val('').trigger('change.select2');
                } else {
                    jQuery('#depart').val('').trigger('change.select2');
                }
            });

            jQuery('input[name="navigato_md"]').on('change', function () {
                toggleDefaultTarget(this.value, 'md');
            });

            // Picking a website narrows both department dropdowns to that storefront.
            jQuery('#website').on('change', function () {
                var websiteId = jQuery(this).val();
                loadDepartments(websiteId, 'depart_prod', '');
                resetSelect('subDepartment_prod', 'Select Sub Department');
                resetSelect('product', 'Select Product');
            });

            jQuery('#depart_prod').on('change', function () {
                loadSubDepartments(jQuery(this).val(), jQuery('#website').val(), 'subDepartment_prod', '');
                resetSelect('product', 'Select Product');
            });

            jQuery('#subDepartment_prod').on('change', function () {
                loadProducts(jQuery('#website').val(), 'product', jQuery('#depart_prod').val(), jQuery(this).val(), '');
            });

            jQuery('#depart_editmd').on('change', function () {
                loadSubDepartments(jQuery(this).val(), jQuery('#webid_md').val(), 'subDepartment_prod_editmd', '');
                resetSelect('product_editmd', 'Select Product');
            });

            jQuery('#subDepartment_prod_editmd').on('change', function () {
                loadProducts(jQuery('#webid_md').val(), 'product_editmd', jQuery('#depart_editmd').val(), jQuery(this).val(), '');
            });

            document.getElementById('sliderCreateForm').addEventListener('submit', function (event) {
                var websiteAlert = document.getElementById('website_alert');
                var slideAlert = document.getElementById('desktop_slide_alert');
                var invalid = false;

                websiteAlert.textContent = '';
                slideAlert.classList.add('hidden');

                if (!jQuery('#website').val()) {
                    websiteAlert.textContent = 'Please select a website.';
                    invalid = true;
                }

                if (!document.getElementById('desktop_slide').files.length) {
                    slideAlert.textContent = 'Please choose a desktop slide.';
                    slideAlert.classList.remove('hidden');
                    invalid = true;
                }

                if (invalid) {
                    event.preventDefault();
                    return;
                }

                var button = document.getElementById('btn_create');
                button.disabled = true;
                button.textContent = 'Please wait...';
            });

            document.getElementById('btn_update_md').addEventListener('click', function () {
                this.disabled = true;
                this.textContent = 'Saving...';
                document.getElementById('editSlideForm_md').submit();
            });

            document.getElementById('btn_remove_md').addEventListener('click', function () {
                if (!currentSlide) {
                    return;
                }
                closeModal('slideEdit_Modal');
                confirmRemoveSlide('DestroyForm' + currentSlide.websiteId, 'mode_default_' + currentSlide.websiteId, currentSlide.id, currentSlide.websiteName);
            });
        });
    </script>
@endpush
