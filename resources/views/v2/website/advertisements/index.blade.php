@extends('layouts.master-tailwind')

@section('title', 'Website Advertisements')
@section('page_title', 'Website Advertisements')
@section('page_subtitle', 'Manage the promotional notification each storefront shows its visitors.')

@section('content')
    @php
        $companyId = session('company_id');

        $websiteCollection = collect($websites ?? []);
        $departmentCollection = collect($departments ?? []);
        $adverts = collect($websiteSlider ?? []);

        $mediaBase = asset('storage/images/website/advertisements/' . $companyId);

        // Only one advertisement is allowed per website, so the create form flags the
        // storefronts that are already taken instead of letting the server reject them.
        $usedWebsiteIds = $adverts->pluck('website_id')->map(fn ($id) => (string) $id)->all();

        $departmentLinked = $adverts->filter(fn ($row) => !empty($row->invent_department_id))->count();
        $productLinked = $adverts->filter(fn ($row) => !empty($row->prod_id))->count();
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
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Advertisements</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($adverts->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Notification posts currently live</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites Covered</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">
                    <span>{{ number_format($adverts->count()) }}</span><span class="text-lg font-bold text-erp-mute">/{{ number_format($websiteCollection->count()) }}</span>
                </div>
                <p class="mt-2 text-sm text-erp-mute">One advertisement is allowed per website</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department Links</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($departmentLinked) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Posts that open a department page</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Product Links</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($productLinked) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Posts that open a product page</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Create Advertisement</h2>
                <p class="mt-1 text-sm text-erp-mute">Upload a square artwork and choose where tapping the notification takes the visitor.</p>
            </div>

            <form id="advertisementForm" action="{{ route('storeAdvertisement') }}" method="post" enctype="multipart/form-data"
                class="grid gap-6 p-5 lg:grid-cols-12">
                @csrf

                <div class="lg:col-span-5">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Artwork <span class="text-rose-600">*</span></span>
                        <span class="text-[11px] font-bold text-erp-mute">576 &times; 576</span>
                    </div>
                    <div class="overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                        <div class="mx-auto flex aspect-square max-w-[280px] items-center justify-center">
                            <img id="previewImg" src="{{ asset('storage/images/placeholder.jpg') }}" alt="Advertisement preview"
                                class="h-full w-full object-contain">
                        </div>
                    </div>
                    <label class="ad-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                        <i class="icofont icofont-upload-alt text-base"></i>
                        Choose image
                        <input type="file" name="image" id="image" class="hidden" accept=".jpg,.jpeg,.png"
                            onchange="previewAdImage(this, 'previewImg', 'imageName', 'image_alert')">
                    </label>
                    <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="imageName"></p>
                    <p class="mt-1 text-xs font-bold text-rose-600" id="image_alert">@error('image'){{ $message }}@enderror</p>

                    <div class="mt-3 flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                        <i class="icofont icofont-info-circle mt-0.5 text-base"></i>
                        <span>JPG or PNG only, exactly <strong>576 &times; 576</strong> pixels, up to <strong>1MB</strong>.</span>
                    </div>
                </div>

                <div class="space-y-4 lg:col-span-7">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website <span class="text-rose-600">*</span></span>
                        <select name="website" id="website" data-placeholder="Search website..."
                            class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">Select Website</option>
                            @foreach ($websiteCollection as $val)
                                @php $taken = in_array((string) $val->id, $usedWebsiteIds, true); @endphp
                                <option value="{{ $val->id }}" {{ $taken ? 'disabled' : '' }} {{ old('website') == $val->id ? 'selected' : '' }}>
                                    {{ $val->name }}{{ $taken ? ' — already has an advertisement' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <span class="mt-1 block text-xs text-erp-mute">A website already holding an advertisement cannot take another one — edit or remove the existing post instead.</span>
                        <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert">@error('website'){{ $message }}@enderror</span>
                    </label>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Notification Opens</span>
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
                        <p class="mt-1 text-xs text-erp-mute">Leave both unselected for a notification that does not link anywhere.</p>
                    </div>

                    <div class="hidden" id="departmentbox">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department</span>
                            <select name="depart" id="depart" data-placeholder="Search department..."
                                class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                                <option value="">Select Department</option>
                            </select>
                            <span class="mt-1 block text-xs text-erp-mute">Pick a website first — only departments that have products on it are listed.</span>
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
                        class="h-10 w-full rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark disabled:opacity-60 sm:w-auto sm:px-8">
                        Add Advertisement
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Live Advertisements</h2>
                    <p class="mt-1 text-sm text-erp-mute">Click a post to replace its artwork or change where it links.</p>
                </div>
                <input type="search" id="advertFilter" placeholder="Filter by website, department or product..."
                    class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-96">
            </div>

            <div class="p-5">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse ($adverts as $value)
                        @php
                            $imageUrl = $mediaBase . '/' . $value->website_id . '/' . $value->image;

                            if ($value->invent_department_id) {
                                $targetType = 'Department';
                                $targetLabel = $value->invent_department_name ?: ('#' . $value->invent_department_id);
                                $targetChip = 'bg-indigo-50 text-indigo-700 ring-indigo-200';
                            } elseif ($value->prod_id) {
                                $targetType = 'Product';
                                $targetLabel = ($value->prod_name ?? null) ?: ('#' . $value->prod_id);
                                $targetChip = 'bg-amber-50 text-amber-700 ring-amber-200';
                            } else {
                                $targetType = 'No link';
                                $targetLabel = 'Notification is not clickable';
                                $targetChip = 'bg-slate-100 text-slate-600 ring-slate-300';
                            }

                            $payload = [
                                'id' => $value->id,
                                'websiteId' => $value->website_id,
                                'websiteName' => $value->name,
                                'imageUrl' => $imageUrl,
                                'departmentId' => $value->invent_department_id,
                                'productId' => $value->prod_id,
                                'productDepartmentId' => $value->prod_depart,
                                'productSubDepartmentId' => $value->prod_sb_depart,
                            ];
                        @endphp

                        <article class="advert-card group overflow-hidden rounded-lg border border-erp-line bg-white shadow-sm transition hover:border-erp hover:shadow-panel"
                            data-search="{{ strtolower($value->name . ' ' . $targetLabel . ' ' . $targetType) }}">
                            <button type="button" onclick="openAdvertEdit(@js($payload))"
                                class="block aspect-square w-full overflow-hidden bg-erp-soft">
                                <img src="{{ $imageUrl }}" alt="{{ $value->image }}" class="h-full w-full object-cover">
                            </button>

                            <div class="space-y-3 p-4">
                                <div>
                                    <p class="truncate text-sm font-black text-erp-ink" title="{{ $value->name }}">{{ $value->name }}</p>
                                    <span class="mt-2 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.12em] ring-1 {{ $targetChip }}">
                                        {{ $targetType }}
                                    </span>
                                    <p class="mt-1 truncate text-xs font-bold text-erp-mute" title="{{ $targetLabel }}">{{ $targetLabel }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="openAdvertEdit(@js($payload))"
                                        class="flex-1 rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                        Edit
                                    </button>
                                    <button type="button" onclick="confirmRemoveAdvert(@js('DestroyForm' . $value->id), @js($value->name))"
                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <form id="DestroyForm{{ $value->id }}" action="{{ route('destroyAdvertisement', [$value->id]) }}" method="post" class="hidden">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="website" value="{{ $value->website_id }}">
                                <input type="hidden" name="id" value="{{ $value->id }}">
                            </form>
                        </article>
                    @empty
                        <div class="col-span-full rounded-lg border border-dashed border-erp-line px-5 py-14 text-center">
                            <p class="text-sm font-bold text-erp-ink">No advertisements yet</p>
                            <p class="mt-1 text-sm text-erp-mute">Create your first notification post using the form above.</p>
                        </div>
                    @endforelse

                    <div id="advertNoMatch" class="col-span-full hidden rounded-lg border border-dashed border-erp-line px-5 py-14 text-center text-sm text-erp-mute">
                        No advertisements match your search.
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Edit advertisement --}}
    <div id="advertEdit_Modal" class="fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/60 px-4 py-8"
        onclick="if (event.target === this) closeModal('advertEdit_Modal')">
        <div class="w-full max-w-3xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Edit Advertisement</h3>
                    <p class="mt-1 text-sm text-erp-mute"><span id="webname_label_md" class="font-bold text-erp-dark"></span></p>
                </div>
                <button type="button" onclick="closeModal('advertEdit_Modal')"
                    class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute transition hover:bg-slate-100">&times;</button>
            </div>

            <form id="editPostForm_md" action="{{ route('updateAdvertisement') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="webname_md" name="webName">
                <input type="hidden" id="webid_md" name="webId">
                <input type="hidden" id="id_md" name="id">

                <div class="grid gap-6 px-5 py-5 md:grid-cols-2">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Artwork</span>
                        <div class="mt-2 overflow-hidden rounded-lg border border-erp-line bg-erp-soft">
                            <div class="mx-auto flex aspect-square max-w-[260px] items-center justify-center">
                                <img id="slideImgMD" src="{{ asset('storage/images/no-image.png') }}" alt="Advertisement" class="h-full w-full object-contain">
                            </div>
                        </div>
                        <label class="ad-drop mt-2 flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-dashed border-erp-line px-4 py-2.5 text-sm font-bold text-erp-dark">
                            <i class="icofont icofont-upload-alt text-base"></i>
                            Replace image
                            <input type="file" name="image_md" id="image_md" class="hidden" accept=".jpg,.jpeg,.png"
                                onchange="previewAdImage(this, 'slideImgMD', 'imageMdName', 'image_md_alert')">
                        </label>
                        <p class="mt-1 truncate text-xs font-bold text-erp-mute" id="imageMdName"></p>
                        <p class="mt-1 text-xs font-bold text-rose-600" id="image_md_alert"></p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                            <i class="icofont icofont-info-circle mt-0.5 text-base"></i>
                            <span>Leave the file empty to keep the current artwork. Replacements must also be <strong>576 &times; 576</strong> and under <strong>1MB</strong>.</span>
                        </div>

                        <div>
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Notification Opens</span>
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
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department</span>
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
                                <select id="sub_depart_editmd" data-placeholder="Search sub department..."
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

            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" onclick="closeModal('advertEdit_Modal')"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Close</button>
                <button type="button" id="btn_update_md"
                    class="rounded-lg bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Delete confirmation --}}
    <div id="confirmModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeConfirm()">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Remove Advertisement</h3>
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
        .ad-drop {
            transition: border-color .15s ease, background-color .15s ease;
        }

        .ad-drop:hover {
            border-color: #4CAF50;
            background-color: #f0fdf4;
        }
    </style>
@endpush

@push('scripts')
    <script>
        var AD_ROUTES = {
            products: @js(route('getWebsiteProd')),
            departments: @js(route('getDepart_n_subDepart_wb'))
        };
        var CSRF_TOKEN = @js(csrf_token());
        var PLACEHOLDER_IMAGE = @js(asset('storage/images/no-image.png'));

        // The server rejects anything that is not exactly this size, so the form checks first.
        var REQUIRED_WIDTH = 576;
        var REQUIRED_HEIGHT = 576;

        var confirmFormId = null;
        var currentAdvert = null;

        /* --------------------------------------------------------------- modals */
        function openModal(id) {
            var modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
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
                allowClear: $select.find('option[value=""]').length > 0,
                dropdownCssClass: 'v2-select2-dropdown',
                placeholder: $select.data('placeholder') || 'Select'
            });
        }

        // Select2 sizes itself at init time, so a control built inside a display:none
        // ancestor comes out 0px wide -- rebuild whatever just became visible.
        function refreshSelect2In($container) {
            $container.find('.v2-select2:visible').each(function () {
                initSelect2(jQuery(this));
            });
        }

        /* -------------------------------------------------------------- confirm */
        function openConfirm(formId, message) {
            confirmFormId = formId;
            document.getElementById('confirmMessage').textContent = message;
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

        function confirmRemoveAdvert(formId, websiteName) {
            openConfirm(formId, 'Remove the advertisement from the ' + websiteName + ' website? This cannot be undone.');
        }

        /* ------------------------------------------------------- image handling */
        function previewAdImage(input, imageId, nameId, alertId) {
            var allowed = /\.(jpg|jpeg|png)$/i;
            var alertBox = document.getElementById(alertId);

            document.getElementById(nameId).textContent = '';
            alertBox.textContent = '';

            if (!input.files || !input.files[0]) {
                return;
            }

            var file = input.files[0];

            if (!allowed.test(file.name)) {
                alertBox.textContent = 'Only JPG and PNG images are accepted.';
                input.value = '';
                return;
            }

            if (file.size > 1024 * 1024) {
                alertBox.textContent = 'This image is larger than 1MB.';
                input.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                var probe = new Image();
                probe.onload = function () {
                    if (probe.naturalWidth !== REQUIRED_WIDTH || probe.naturalHeight !== REQUIRED_HEIGHT) {
                        alertBox.textContent = 'Image must be exactly ' + REQUIRED_WIDTH + ' x ' + REQUIRED_HEIGHT +
                            ' pixels (this one is ' + probe.naturalWidth + ' x ' + probe.naturalHeight + ').';
                        input.value = '';
                        document.getElementById(nameId).textContent = '';
                        document.getElementById(imageId).setAttribute('src', PLACEHOLDER_IMAGE);
                        return;
                    }

                    document.getElementById(imageId).setAttribute('src', event.target.result);
                    document.getElementById(nameId).textContent = file.name;
                };
                probe.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }

        /* ------------------------------------------------------------ ajax load */
        function fillSelect(elementId, rows, valueKey, labelKey, placeholder, selected) {
            var $select = jQuery('#' + elementId);
            var selectedValue = (selected === null || selected === undefined) ? '' : String(selected);

            $select.empty().prop('disabled', false);
            $select.append(new Option(placeholder, '', false, false));

            (rows || []).forEach(function (row) {
                var value = String(row[valueKey]);
                $select.append(new Option(row[labelKey], value, false, value === selectedValue));
            });

            $select.val(selectedValue).trigger('change.select2');
        }

        function resetSelect(elementId, placeholder) {
            var $select = jQuery('#' + elementId);
            $select.empty().prop('disabled', true);
            $select.append(new Option(placeholder, '', false, false));
            $select.val('').trigger('change.select2');
        }

        function loadDepartments(websiteId, elementId, selected) {
            if (!websiteId) {
                resetSelect(elementId, 'Select Department');
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.post(AD_ROUTES.departments, { _token: CSRF_TOKEN, website: websiteId, mode: 'depart' })
                .done(function (response) {
                    fillSelect(elementId, response === 0 ? [] : response, 'department_id', 'department_name', 'Select Department', selected);
                });
        }

        function loadSubDepartments(departmentId, websiteId, elementId, selected) {
            if (!departmentId) {
                resetSelect(elementId, 'Select Sub Department');
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.post(AD_ROUTES.departments, { _token: CSRF_TOKEN, depart: departmentId, website: websiteId, mode: 'subdepart' })
                .done(function (response) {
                    fillSelect(elementId, response === 0 ? [] : response, 'sub_department_id', 'sub_depart_name', 'Select Sub Department', selected);
                });
        }

        function loadProducts(websiteId, elementId, departmentId, subDepartmentId, selected) {
            if (!websiteId) {
                resetSelect(elementId, 'Select Product');
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.post(AD_ROUTES.products, {
                _token: CSRF_TOKEN,
                id: websiteId,
                department: departmentId || '',
                subDepart: subDepartmentId || ''
            }).done(function (response) {
                fillSelect(elementId, response === 0 ? [] : response, 'id', 'product_name', 'Select Product', selected);
            });
        }

        /* ----------------------------------------------------------------- edit */
        function toggleTarget(mode, prefix) {
            var departmentBox = document.getElementById(prefix === 'md' ? 'departmentbox_md' : 'departmentbox');
            var productBox = document.getElementById(prefix === 'md' ? 'productbox_md' : 'productbox');

            departmentBox.classList.toggle('hidden', mode !== 'department');
            productBox.classList.toggle('hidden', mode !== 'product');

            refreshSelect2In(jQuery(mode === 'department' ? departmentBox : productBox));
        }

        function openAdvertEdit(advert) {
            currentAdvert = advert;

            // Reset first -- it also clears the hidden inputs that are filled in below.
            document.getElementById('editPostForm_md').reset();
            document.getElementById('imageMdName').textContent = '';
            document.getElementById('image_md_alert').textContent = '';

            document.getElementById('webname_label_md').textContent = advert.websiteName;
            document.getElementById('webname_md').value = advert.websiteName;
            document.getElementById('webid_md').value = advert.websiteId;
            document.getElementById('id_md').value = advert.id;
            document.getElementById('slideImgMD').setAttribute('src', advert.imageUrl || PLACEHOLDER_IMAGE);

            jQuery('#depart_md').val('').trigger('change.select2');
            jQuery('#depart_editmd').val('').trigger('change.select2');
            resetSelect('sub_depart_editmd', 'Select Sub Department');
            resetSelect('product_editmd', 'Select Product');

            if (advert.departmentId) {
                document.getElementById('navigat_depart_md').checked = true;
                toggleTarget('department', 'md');
                jQuery('#depart_md').val(String(advert.departmentId)).trigger('change.select2');
            } else if (advert.productId) {
                document.getElementById('navigat_prod_md').checked = true;
                toggleTarget('product', 'md');

                jQuery('#depart_editmd').val(advert.productDepartmentId ? String(advert.productDepartmentId) : '').trigger('change.select2');
                loadSubDepartments(advert.productDepartmentId, advert.websiteId, 'sub_depart_editmd', advert.productSubDepartmentId);
                loadProducts(advert.websiteId, 'product_editmd', advert.productDepartmentId, advert.productSubDepartmentId, advert.productId);
            } else {
                document.getElementById('navigat_depart_md').checked = false;
                document.getElementById('navigat_prod_md').checked = false;
                toggleTarget('', 'md');
            }

            openModal('advertEdit_Modal');
        }

        jQuery(function () {
            refreshSelect2In(jQuery(document.body));

            jQuery('input[name="navigato"]').on('change', function () {
                toggleTarget(this.value, '');

                if (this.value === 'department') {
                    jQuery('#product').val('').trigger('change.select2');
                } else {
                    jQuery('#depart').val('').trigger('change.select2');
                }
            });

            jQuery('input[name="navigato_md"]').on('change', function () {
                toggleTarget(this.value, 'md');
            });

            // Picking a website narrows both department dropdowns to that storefront.
            jQuery('#website').on('change', function () {
                var websiteId = jQuery(this).val();
                loadDepartments(websiteId, 'depart', '');
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
                loadSubDepartments(jQuery(this).val(), jQuery('#webid_md').val(), 'sub_depart_editmd', '');
                resetSelect('product_editmd', 'Select Product');
            });

            jQuery('#sub_depart_editmd').on('change', function () {
                loadProducts(jQuery('#webid_md').val(), 'product_editmd', jQuery('#depart_editmd').val(), jQuery(this).val(), '');
            });

            document.getElementById('advertisementForm').addEventListener('submit', function (event) {
                var websiteAlert = document.getElementById('website_alert');
                var imageAlert = document.getElementById('image_alert');
                var invalid = false;

                websiteAlert.textContent = '';

                if (!jQuery('#website').val()) {
                    websiteAlert.textContent = 'Please select a website.';
                    invalid = true;
                }

                if (!document.getElementById('image').files.length) {
                    imageAlert.textContent = 'Please choose an advertisement image.';
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
                document.getElementById('editPostForm_md').submit();
            });

            document.getElementById('btnConfirmYes').addEventListener('click', function () {
                if (!confirmFormId) {
                    return;
                }
                this.disabled = true;
                this.classList.add('opacity-60');
                document.getElementById(confirmFormId).submit();
            });

            document.getElementById('advertFilter').addEventListener('input', function () {
                var term = this.value.trim().toLowerCase();
                var visible = 0;

                document.querySelectorAll('.advert-card').forEach(function (card) {
                    var match = card.getAttribute('data-search').indexOf(term) !== -1;
                    card.classList.toggle('hidden', !match);
                    if (match) {
                        visible += 1;
                    }
                });

                document.getElementById('advertNoMatch').classList.toggle('hidden', visible > 0);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') {
                    return;
                }
                closeConfirm();
                closeModal('advertEdit_Modal');
            });
        });
    </script>
@endpush
