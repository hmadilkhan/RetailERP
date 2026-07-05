@php
    $product = $isEdit ? ($data[0] ?? null) : null;
    $price = $isEdit ? ($prices[0] ?? null) : null;
    $selectedWebsites = collect($selectedWebsites ?? []);
    $selectedTags = collect($inventoryTags ?? []);
    $selectedVendors = collect($selectedVendors ?? []);
    $selectedBrand = old('brand', $product->brand_id ?? collect($inventoryBrand ?? [])->first());
    $selectedWebsite = old('website', $selectedWebsites->first());
    $websiteEnabled = old('showProductWebsite') || $selectedWebsites->isNotEmpty();
    $imageName = $product->image ?? null;
    $imageUrl = $imageName ? asset('storage/images/products/' . $imageName) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22480%22 viewBox=%220 0 640 480%22%3E%3Crect width=%22640%22 height=%22480%22 fill=%22%23f8fafc%22/%3E%3Cpath d=%22M160 328l92-98 66 70 48-52 114 122H160z%22 fill=%22%23dbe4ee%22/%3E%3Ccircle cx=%22442%22 cy=%22154%22 r=%2242%22 fill=%22%23cbd5e1%22/%3E%3Ctext x=%22320%22 y=%22420%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2228%22 fill=%22%2364758b%22%3EProduct Image%3C/text%3E%3C/svg%3E';
    $referenceValue = old('reference', $isEdit ? ($references ?? '') : '');
    $selectedInventoryType = old('inventory_type', $isEdit ? ($product->inventory_type_id ?? '') : 1);
    $websiteIsRestaurant = ($websiteType ?? null) === 'restaurant';

    $field = function ($name, $fallback = '') use ($product, $price) {
        $map = [
            'pctcode' => $product->pct_code ?? '',
            'code' => $product->item_code ?? '',
            'name' => $product->product_name ?? '',
            'weight' => $product->weight_qty ?? '',
            'reminder' => $product->reminder_qty ?? '',
            'description' => $product->product_description ?? '',
            'inventory_type' => $product->inventory_type_id ?? '',
            'priority' => $product->priority ?? '',
            'sdescription' => html_entity_decode($product->short_description ?? ''),
            'details' => html_entity_decode($product->details ?? ''),
            'product_description_resturant_website' => $product->product_description_resturant_website ?? '',
            'meta_title' => $product->meta_title ?? '',
            'meta_description' => $product->meta_description ?? '',
            'slug' => $product->slug ?? '',
            'cost_price' => $price->cost_price ?? '',
            'ap' => $price->actual_price ?? '',
            'taxrate' => $price->tax_rate ?? '',
            'taxamount' => $price->tax_amount ?? '',
            'rp' => $price->retail_price ?? '',
            'wp' => $price->wholesale_price ?? '',
            'dp' => $price->discount_price ?? '',
            'op' => $price->online_price ?? '',
        ];

        return old($name, $map[$name] ?? $fallback);
    };

    $inputClass = 'mt-2 h-11 w-full rounded-lg border border-erp-line bg-white px-3 text-sm text-erp-ink outline-none transition placeholder:text-slate-400 focus:border-erp focus:ring-2 focus:ring-erp/10';
    $textareaClass = 'mt-2 w-full rounded-lg border border-erp-line bg-white px-3 py-2 text-sm text-erp-ink outline-none transition placeholder:text-slate-400 focus:border-erp focus:ring-2 focus:ring-erp/10';
    $selectClass = 'v2-select2 v2-select2-lg mt-2 w-full';
    $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
@endphp

<form id="{{ $isEdit ? 'inventoryupdate' : 'inventCreateForm' }}" method="POST" enctype="multipart/form-data" action="{{ $formAction }}" class="space-y-6">
    @csrf

    @if ($isEdit)
        <input type="hidden" id="id" name="id" value="{{ $product->id }}">
        <input type="hidden" id="previmage" name="previmage" value="{{ $product->image }}">
        <input type="hidden" id="reminder_id" name="reminder_id" value="{{ $product->reminder_id }}">
        <input type="hidden" id="oldGalleryImage" name="galleryImage">
        <input type="hidden" id="oldurlGalleryImage" name="urlGalleryImage">
        <input type="hidden" id="oldvideo" name="oldvideo">
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800">
            Please fix the highlighted fields and submit again.
        </div>
    @endif

    <div class="flex flex-col gap-3 rounded-lg border border-erp-line bg-white p-5 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="text-xs font-bold uppercase tracking-[0.18em] text-erp-mute">Product Setup</div>
            <h2 class="mt-2 text-xl font-bold text-erp-ink">{{ $isEdit ? ($product->product_name ?? 'Update Product') : 'New Catalogue Product' }}</h2>
            <p class="mt-1 text-sm text-erp-mute">Required product details, prices, website settings, and media are grouped below.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('invent-list') }}" class="inline-flex h-10 items-center rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back to List</a>
            <button id="btn_submit_save_changes" type="submit" class="inline-flex h-10 items-center rounded-lg border border-erp bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">{{ $submitLabel }}</button>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h3 class="text-base font-bold text-erp-ink">Identity</h3>
                    <p class="mt-1 text-sm text-erp-mute">Code, department, mode, references, and searchable naming.</p>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-2">
                    <label class="block">
                        <span class="{{ $labelClass }}">Department <span class="text-rose-600">*</span></span>
                        <select id="depart" name="depart" required data-placeholder="Select Department" class="{{ $selectClass }}">
                            <option value="">Select Department</option>
                            @foreach ($department ?? [] as $val)
                                <option value="{{ $val->department_id }}" @selected(old('depart', $product->department_id ?? '') == $val->department_id)>{{ $val->department_name }}</option>
                            @endforeach
                        </select>
                        @error('depart') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Sub Department <span class="text-rose-600">*</span></span>
                        <select id="subDepart" name="subDepart" required data-placeholder="Select Sub Department" class="{{ $selectClass }}">
                            <option value="">Select Sub Department</option>
                            @foreach ($subdepartment ?? [] as $val)
                                <option value="{{ $val->sub_department_id }}" @selected(old('subDepart', $product->sub_department_id ?? '') == $val->sub_department_id)>{{ $val->sub_depart_name }}</option>
                            @endforeach
                        </select>
                        @error('subDepart') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">PCT Code</span>
                        <input type="text" name="pctcode" id="pctcode" value="{{ $field('pctcode') }}" class="{{ $inputClass }}" placeholder="Enter PCT code">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Product Code <span class="text-rose-600">*</span></span>
                        <div class="mt-2 flex gap-2">
                            <input type="text" name="code" id="code" value="{{ $field('code') }}" required class="h-11 min-w-0 flex-1 rounded-lg border border-erp-line bg-white px-3 text-sm text-erp-ink outline-none transition placeholder:text-slate-400 focus:border-erp focus:ring-2 focus:ring-erp/10" placeholder="Enter product code">
                            <button type="button" id="btngen" class="h-11 shrink-0 rounded-lg border border-erp-line px-3 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Auto</button>
                        </div>
                        <span id="code_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                        @error('code') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    <label class="block md:col-span-2">
                        <span class="{{ $labelClass }}">Product Name <span class="text-rose-600">*</span></span>
                        <input type="text" name="name" id="name" value="{{ $field('name') }}" required class="{{ $inputClass }}" placeholder="Enter product name">
                        <span id="product_name_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                        @error('name') <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span> @enderror
                    </label>
                    @if ($isEdit)
                        <label class="block md:col-span-2">
                            <span class="{{ $labelClass }}">Slug</span>
                            <input type="text" name="slug" id="slug" value="{{ $field('slug') }}" class="{{ $inputClass }}" placeholder="website-product-url">
                        </label>
                    @endif
                    <label class="block">
                        <span class="{{ $labelClass }}">Product Mode <span class="text-rose-600">*</span></span>
                        <select id="product_mode" name="product_mode" required data-placeholder="Select Product Mode" class="{{ $selectClass }}">
                            <option value="">Select Product Mode</option>
                            @foreach ($mode ?? [] as $val)
                                <option value="{{ $val->product_mode_id }}" @selected(old('product_mode', $product->product_mode ?? '') == $val->product_mode_id)>{{ $val->product_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Inventory Type</span>
                        <select id="inventory_type" name="inventory_type" data-placeholder="Select Inventory Type" class="{{ $selectClass }}">
                            <option value="">Select Inventory Type</option>
                            @foreach ($types ?? [] as $type)
                                <option value="{{ $type->id }}" @selected($selectedInventoryType == $type->id)>{{ data_get($type, 'name', data_get($type, 'type_name', data_get($type, 'title', 'Type ' . $type->id))) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block md:col-span-2">
                        <span class="{{ $labelClass }}">References</span>
                        <input type="text" id="reference" name="reference" value="{{ $referenceValue }}" class="{{ $inputClass }}" placeholder="Comma separated supplier or barcode references">
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h3 class="text-base font-bold text-erp-ink">Units and Pricing</h3>
                    <p class="mt-1 text-sm text-erp-mute">UOM conversion, reminders, tax, and channel prices.</p>
                </div>
                <div class="grid gap-4 p-5 md:grid-cols-3">
                    <label class="block">
                        <span class="{{ $labelClass }}">Unit Measure <span class="text-rose-600">*</span></span>
                        <select id="uom" name="uom" required data-placeholder="Select UOM" class="{{ $selectClass }}">
                            <option value="">Select UOM</option>
                            @foreach ($uom ?? [] as $val)
                                <option value="{{ $val->uom_id }}" @selected(old('uom', $product->uom_id ?? '') == $val->uom_id)>{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Weight | Qty</span>
                        <input type="number" step="0.01" name="weight" id="weight" value="{{ $field('weight') }}" class="{{ $inputClass }}" placeholder="0">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Conversion UOM <span class="text-rose-600">*</span></span>
                        <select id="cuom" name="cuom" required data-placeholder="Select Conversion UOM" class="{{ $selectClass }}">
                            <option value="">Select Conversion UOM</option>
                            @foreach ($uom ?? [] as $val)
                                <option value="{{ $val->uom_id }}" @selected(old('cuom', $product->cuom ?? '') == $val->uom_id)>{{ $val->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Qty Reminder <span class="text-rose-600">*</span></span>
                        <input type="number" min="0" name="reminder" id="reminder" value="{{ $field('reminder') }}" required class="{{ $inputClass }}" placeholder="0">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Cost Price</span>
                        <input type="number" step="0.01" min="0" name="cost_price" id="cost_price" value="{{ $field('cost_price') }}" class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Actual Price <span class="text-rose-600">*</span></span>
                        <input type="number" step="0.01" min="0" name="ap" id="ap" value="{{ $field('ap') }}" required class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tax Rate</span>
                        <input type="number" step="0.01" min="0" name="taxrate" id="taxrate" value="{{ $field('taxrate') }}" class="{{ $inputClass }}" placeholder="0">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tax Amount</span>
                        <input type="number" step="0.01" min="0" name="taxamount" id="taxamount" value="{{ $field('taxamount') }}" class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Retail Price <span class="text-rose-600">*</span></span>
                        <input type="number" step="0.01" min="0" name="rp" id="rp" value="{{ $field('rp') }}" required class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Wholesale Price</span>
                        <input type="number" step="0.01" min="0" name="wp" id="wp" value="{{ $field('wp') }}" class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Discount Price</span>
                        <input type="number" step="0.01" min="0" name="dp" id="dp" value="{{ $field('dp') }}" class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Online Price</span>
                        <input type="number" step="0.01" min="0" name="op" id="op" value="{{ $field('op') }}" class="{{ $inputClass }}" placeholder="0.00">
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h3 class="text-base font-bold text-erp-ink">Catalogue Content</h3>
                    <p class="mt-1 text-sm text-erp-mute">Description, web copy, and search metadata.</p>
                </div>
                <div class="grid gap-4 p-5">
                    <label class="block">
                        <span class="{{ $labelClass }}">Product Description</span>
                        <textarea name="description" id="description" rows="4" class="{{ $textareaClass }}" placeholder="Internal product description">{{ $field('description') }}</textarea>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Short Website Description</span>
                        <textarea name="sdescription" id="summary-ckeditor" rows="4" class="{{ $textareaClass }}" placeholder="Short product summary">{{ $field('sdescription') }}</textarea>
                    </label>
                    <label id="editorDiv" class="{{ $websiteIsRestaurant ? 'hidden' : '' }} block">
                        <span class="{{ $labelClass }}">Website Details</span>
                        <textarea name="details" id="details" rows="6" class="{{ $textareaClass }}" placeholder="Detailed website description">{{ $field('details') }}</textarea>
                    </label>
                    <label id="nonEditorDiv" class="{{ $websiteIsRestaurant ? '' : 'hidden' }} block">
                        <span class="{{ $labelClass }}">Restaurant Website Details</span>
                        <textarea name="product_description_resturant_website" id="product_description_resturant_website" rows="5" class="{{ $textareaClass }}" placeholder="Restaurant website description">{{ $field('product_description_resturant_website') }}</textarea>
                    </label>
                    <div class="grid gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="{{ $labelClass }}">Meta Title</span>
                            <input type="text" name="meta_title" id="meta_title" value="{{ $field('meta_title') }}" class="{{ $inputClass }}" placeholder="SEO title">
                        </label>
                        <label class="block">
                            <span class="{{ $labelClass }}">Meta Description</span>
                            <textarea name="meta_description" id="meta_description" rows="2" class="{{ $textareaClass }}" placeholder="SEO description">{{ $field('meta_description') }}</textarea>
                        </label>
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h3 class="text-base font-bold text-erp-ink">Visibility</h3>
                    <p class="mt-1 text-sm text-erp-mute">Website, brand, tags, vendors, and POS availability.</p>
                </div>
                <div class="space-y-4 p-5">
                    <label class="block">
                        <span class="{{ $labelClass }}">Brand</span>
                        <select id="brand" name="brand" data-placeholder="Select Brand" class="{{ $selectClass }}">
                            <option value="">Select Brand</option>
                            @foreach ($brandList ?? [] as $brand)
                                <option value="{{ $brand->id }}" @selected($selectedBrand == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Tags</span>
                        <select id="tags" name="tags[]" multiple data-placeholder="Select Tags" class="{{ $selectClass }}">
                            @foreach ($tagsList ?? [] as $tag)
                                <option value="{{ $tag->id }}" @selected(collect(old('tags', $selectedTags->all()))->contains($tag->id))>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Vendors</span>
                        <select id="vendor" name="vendor[]" multiple data-placeholder="Select Vendors" class="{{ $selectClass }}">
                            @foreach ($vendors ?? [] as $vendor)
                                <option value="{{ $vendor->id }}" @selected(collect(old('vendor', $selectedVendors->all()))->contains($vendor->id))>{{ $vendor->vendor_name ?? $vendor->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-erp-line px-4 py-3 text-sm font-bold text-erp-text">
                        <input type="checkbox" id="showProductWebsite" name="showProductWebsite" class="rounded border-erp-line text-erp focus:ring-erp" {{ $websiteEnabled ? 'checked' : '' }}>
                        Show product on website
                    </label>
                    <div id="website-module" class="{{ $websiteEnabled ? '' : 'hidden' }} space-y-4">
                        <label class="block">
                            <span class="{{ $labelClass }}">Website</span>
                            <select id="website" name="website" data-placeholder="Select Website" class="{{ $selectClass }}">
                                <option value="">Select Website</option>
                                @foreach ($websites ?? [] as $website)
                                    <option value="{{ $website->id }}" @selected($selectedWebsite == $website->id)>{{ $website->name ?? $website->website_name ?? $website->domain ?? 'Website ' . $website->id }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="{{ $labelClass }}">Product Priority</span>
                            <input type="number" name="priority" id="priority" value="{{ $field('priority') }}" class="{{ $inputClass }}" placeholder="0">
                        </label>
                    </div>
                    @if (!$isEdit)
                        <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                            <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-erp-text">
                                <input type="checkbox" id="chkstock" name="chkstock" class="rounded border-erp-line text-erp focus:ring-erp" {{ old('chkstock') ? 'checked' : '' }}>
                                Add opening stock
                            </label>
                            <div id="stockOpeningFields" class="{{ old('chkstock') ? '' : 'hidden' }} mt-4 grid gap-3">
                                <input type="number" step="0.01" min="0" name="stock_cost" id="stock_cost" value="{{ old('stock_cost') }}" class="{{ $inputClass }}" placeholder="Opening cost">
                                <input type="number" step="0.01" min="0" name="stock_qty" id="stock_qty" value="{{ old('stock_qty') }}" class="{{ $inputClass }}" placeholder="Opening quantity">
                            </div>
                        </div>
                    @endif
                    <div class="rounded-lg border border-erp-line bg-slate-50 p-4">
                        <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-erp-text">
                            <input type="checkbox" id="chkactive" name="chkactive" class="rounded border-erp-line text-erp focus:ring-erp" {{ old('chkactive') ? 'checked' : '' }}>
                            Create POS product
                        </label>
                        <div id="posFields" class="{{ old('chkactive') ? '' : 'hidden' }} mt-4 grid gap-3">
                            <input type="text" name="poscode" id="poscode" value="{{ old('poscode') }}" class="{{ $inputClass }}" placeholder="POS code">
                            <input type="text" name="posname" id="posname" value="{{ old('posname') }}" class="{{ $inputClass }}" placeholder="POS name">
                            <input type="text" name="posuom" id="posuom" value="{{ old('posuom') }}" class="{{ $inputClass }}" placeholder="POS UOM">
                            <input type="number" step="0.01" min="0" name="posprice" id="posprice" value="{{ old('posprice') }}" class="{{ $inputClass }}" placeholder="POS price">
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="border-b border-erp-line px-5 py-4">
                    <h3 class="text-base font-bold text-erp-ink">Media</h3>
                    <p class="mt-1 text-sm text-erp-mute">Primary image, gallery, and video assets.</p>
                </div>
                <div class="space-y-4 p-5">
                    <img id="simg" src="{{ $imageUrl }}" class="h-48 w-full rounded-lg border border-erp-line object-cover" alt="Product image preview">
                    <label class="block">
                        <span class="{{ $labelClass }}">Primary Image</span>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-erp-text">
                        <input type="checkbox" name="actual_image_size" class="rounded border-erp-line text-erp focus:ring-erp" {{ old('actual_image_size', $product->actual_image_size ?? false) ? 'checked' : '' }}>
                        Keep actual image size
                    </label>
                    <label class="block">
                        <span class="{{ $labelClass }}">Gallery Images</span>
                        <input type="file" name="prodgallery[]" id="prodgallery" multiple accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                    </label>
                    @if ($isEdit && !empty($images))
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($images as $gallery)
                                <div id="gallery-{{ $gallery->id }}" class="relative overflow-hidden rounded-lg border border-erp-line">
                                    <img src="{{ asset('storage/images/products/' . $gallery->image) }}" class="h-20 w-full object-cover" alt="Gallery image">
                                    <button type="button" data-gallery-id="{{ $gallery->id }}" data-gallery-image="{{ $gallery->image }}" class="remove-gallery absolute right-1 top-1 rounded bg-rose-600 px-2 py-1 text-xs font-bold text-white">X</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <label class="block">
                        <span class="{{ $labelClass }}">Product Video</span>
                        <input type="file" name="prodvideo" id="productvideo" accept="video/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white">
                    </label>
                    @if ($isEdit && !empty($inventoryVideo))
                        <div id="videoPreviewBox" class="rounded-lg border border-erp-line p-3 text-sm text-erp-text">
                            <div class="flex items-center justify-between gap-3">
                                <span class="truncate">{{ $inventoryVideo->file }}</span>
                                <button type="button" id="removeVideo" data-video="{{ $inventoryVideo->file }}" class="rounded bg-rose-600 px-3 py-1 text-xs font-bold text-white">Remove</button>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</form>

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var $ = window.jQuery;
            if (!$) {
                return;
            }

            var isEdit = @json($isEdit);
            var selectedSubDepart = @json(old('subDepart', $product->sub_department_id ?? ''));
            var selectedWebsite = @json($selectedWebsite);

            function notifyError(message) {
                if (window.swal) {
                    swal('Error!', message, 'error');
                    return;
                }
                alert(message);
            }

            function refreshSelect2($select) {
                if ($.fn.select2 && $select.hasClass('select2-hidden-accessible')) {
                    $select.trigger('change.select2');
                }
            }

            function loadSubDepartments(selected) {
                var departmentId = $('#depart').val();
                $('#subDepart').empty().append($('<option>').val('').text('Select Sub Department'));

                if (!departmentId) {
                    refreshSelect2($('#subDepart'));
                    return;
                }

                $.ajax({
                    url: '{{ url('/getSubdepartBydepartID') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: departmentId
                    },
                    success: function (result) {
                        $.each(result || [], function (_, value) {
                            $('#subDepart').append(
                                $('<option>')
                                    .val(value.sub_department_id)
                                    .text(value.sub_depart_name)
                                    .prop('selected', String(selected || '') === String(value.sub_department_id))
                            );
                        });
                        refreshSelect2($('#subDepart'));
                    }
                });
            }

            $('#depart').on('change', function () {
                loadSubDepartments('');
            });

            if ($('#depart').val()) {
                loadSubDepartments(selectedSubDepart);
            }

            $('#btngen').on('click', function () {
                $.ajax({
                    url: '{{ url('/get-product-code') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        departmentId: $('#depart').val(),
                        subdepartmentId: $('#subDepart').val()
                    },
                    success: function (response) {
                        if (response && response.status === 200) {
                            $('#code').val(response.code).trigger('change');
                            $('#code_alert').text('');
                        } else {
                            $('#code_alert').text('Select department and sub department first.');
                        }
                    }
                });
            });

            $('#code').on('change', function () {
                var code = $(this).val();
                if (!code) {
                    return;
                }
                $.ajax({
                    url: '{{ url('/chk-itemcode') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        itemcode: code
                    },
                    success: function (count) {
                        if (!isEdit && Number(count) > 0) {
                            $('#code_alert').text('This product code already exists.');
                        } else {
                            $('#code_alert').text('');
                        }
                    }
                });
            });

            function calculateTaxFromRate() {
                var ap = parseFloat($('#ap').val()) || 0;
                var rate = parseFloat($('#taxrate').val()) || 0;
                var taxAmount = ap * (rate / 100);
                $('#taxamount').val(Math.round(taxAmount));
                $('#rp').val(Math.round(ap + taxAmount));
            }

            $('#ap, #taxrate').on('change input', calculateTaxFromRate);
            $('#taxamount').on('change input', function () {
                var ap = parseFloat($('#ap').val()) || 0;
                var taxAmount = parseFloat($('#taxamount').val()) || 0;
                $('#taxrate').val(ap ? ((taxAmount / ap) * 100).toFixed(2) : 0);
                $('#rp').val(Math.round(ap + taxAmount));
            });

            $('#showProductWebsite').on('change', function () {
                $('#website-module').toggleClass('hidden', !this.checked);
            }).trigger('change');

            $('#chkstock').on('change', function () {
                $('#stockOpeningFields').toggleClass('hidden', !this.checked);
            });

            $('#chkactive').on('change', function () {
                $('#posFields').toggleClass('hidden', !this.checked);
            });

            $('#website').on('change', function () {
                var website = $(this).val();
                if (!website) {
                    $('#editorDiv').removeClass('hidden');
                    $('#nonEditorDiv').addClass('hidden');
                    return;
                }
                $.ajax({
                    url: '{{ route('getWebsiteType') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        code: website
                    },
                    success: function (response) {
                        var restaurant = response === 'restaurant';
                        $('#editorDiv').toggleClass('hidden', restaurant);
                        $('#nonEditorDiv').toggleClass('hidden', !restaurant);
                    }
                });
            });

            if (selectedWebsite) {
                $('#website').val(selectedWebsite).trigger('change');
            }

            $('#name').on('change input', function () {
                var regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;
                var value = $(this).val();
                $('#product_name_alert').text(value && !regex.test(value) ? 'Special characters are not allowed.' : '');
            });

            $('#image').on('change', function () {
                var file = this.files && this.files[0];
                if (!file) {
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    notifyError('File size must be less than 5MB.');
                    this.value = '';
                    return;
                }
                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    notifyError('Only JPG, PNG, and WEBP files are allowed.');
                    this.value = '';
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (event) {
                    $('#simg').attr('src', event.target.result);
                };
                reader.readAsDataURL(file);
            });

            $('.remove-gallery').on('click', function () {
                var image = $(this).data('gallery-image');
                var id = $(this).data('gallery-id');
                var $input = $('#oldGalleryImage');
                var current = $input.val() ? $input.val().split(',') : [];
                current.push(image);
                $input.val(current.join(','));
                $('#gallery-' + id).remove();
            });

            $('#removeVideo').on('click', function () {
                $('#oldvideo').val($(this).data('video'));
                $('#videoPreviewBox').remove();
            });

            if (window.CKEDITOR && document.getElementById('summary-ckeditor')) {
                CKEDITOR.replace('summary-ckeditor');
            }

            $('#inventoryupdate').on('submit', function (event) {
                event.preventDefault();
                var regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;
                if (!regex.test($('#name').val())) {
                    notifyError('Special characters are not allowed.');
                    return;
                }
                if (window.CKEDITOR && CKEDITOR.instances['summary-ckeditor']) {
                    $('#summary-ckeditor').val(CKEDITOR.instances['summary-ckeditor'].getData());
                }
                var $button = $('#btn_submit_save_changes');
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function () {
                        $button.prop('disabled', true).text('Please wait...');
                    },
                    success: function () {
                        window.location.reload();
                    },
                    error: function (xhr) {
                        $button.prop('disabled', false).text(@json($submitLabel));
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error) {
                            notifyError(xhr.responseJSON.error);
                        } else {
                            notifyError('Unable to update inventory. Please check required fields.');
                        }
                    }
                });
            });

            $('#inventCreateForm').on('submit', function (event) {
                var regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;
                if (!regex.test($('#name').val())) {
                    event.preventDefault();
                    notifyError('Special characters are not allowed.');
                    return;
                }
                if (window.CKEDITOR && CKEDITOR.instances['summary-ckeditor']) {
                    $('#summary-ckeditor').val(CKEDITOR.instances['summary-ckeditor'].getData());
                }
            });
        });
    </script>
@endpush
