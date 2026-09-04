@extends('layouts.master-tailwind')

@section('title', 'Theme Setting')
@section('page_title', 'Theme Setting')
@section('page_subtitle', 'Control the storefront look, contact details, branding and behaviour of each website.')

@section('content')
    @php
        $websiteCollection = collect($websiteLists ?? []);
        $selectedWebsite = old('website') ?: $webId;

        $tabs = [
            'general'  => 'General',
            'header'   => 'Header',
            'contact'  => 'Contact',
            'branding' => 'Branding',
            'product'  => 'Product',
            'footer'   => 'Footer',
            'settings' => 'Settings',
        ];
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

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="grid gap-4 p-5 md:grid-cols-12 md:items-end">
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select name="website" id="website" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}" {{ $selectedWebsite == $val->id ? 'selected' : '' }}>{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert"></span>
                </label>

                @if ($GetWebsite != null)
                    <div class="md:col-span-7">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                                {{ $GetWebsite->name }}
                            </span>
                            <span class="rounded-md px-3 py-1.5 text-xs font-bold ring-1 {{ $GetWebsite->is_open == 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">
                                {{ $GetWebsite->is_open == 1 ? 'Website Open' : 'Website Closed' }}
                            </span>
                            @if ($GetWebsite->maintenance_mode == 1)
                                <span class="rounded-md bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 ring-1 ring-amber-200">Maintenance Mode</span>
                            @endif
                            <a href="{{ route('rebuild', $GetWebsite->id) }}"
                                class="ml-auto rounded-lg border border-erp-line px-4 py-2 text-xs font-bold text-erp-dark transition hover:border-erp hover:bg-emerald-50">Rebuild Website</a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if ($GetWebsite == null)
            <section class="rounded-lg border border-dashed border-erp-line bg-erp-soft px-6 py-14 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-erp shadow-sm">
                    <i class="icofont icofont-ui-settings text-2xl"></i>
                </div>
                <h3 class="mt-4 text-base font-bold text-erp-ink">No website selected</h3>
                <p class="mt-1 text-sm text-erp-mute">Pick a website above to load its theme settings.</p>
            </section>
        @else
            @php
                $topbarSlideRaw = !empty($GetWebsite->topbar_slide_msg) ? json_decode($GetWebsite->topbar_slide_msg) : null;
                $topbarSlideValue = is_array($topbarSlideRaw)
                    ? implode(',', $topbarSlideRaw)
                    : (is_string($topbarSlideRaw) ? $topbarSlideRaw : '');

                $lightLogo = !empty($GetWebsite->logo) ? 'website/' . $GetWebsite->logo : 'placeholder.jpg';
                $darkLogo = !empty($GetWebsite->dark_logo) ? 'website/' . $GetWebsite->dark_logo : 'placeholder.jpg';
                $favicon = !empty($GetWebsite->favicon) ? 'website/' . $GetWebsite->favicon : 'placeholder.jpg';

                $toggles = [
                    ['id' => 'maintenance_mode', 'mode' => 'website', 'label' => 'Maintenance Mode', 'hint' => 'Shows a maintenance page to all visitors.', 'value' => $GetWebsite->maintenance_mode],
                    ['id' => 'checkout_otp', 'mode' => 'theme', 'label' => 'Checkout OTP', 'hint' => 'Verify the customer number before an order is placed.', 'value' => $GetWebsite->checkout_otp],
                    ['id' => 'otp_whatsapp_msg', 'mode' => 'theme', 'label' => 'OTP via WhatsApp', 'hint' => 'Send the checkout OTP over WhatsApp.', 'value' => $GetWebsite->otp_whatsapp_msg],
                    ['id' => 'otp_msg', 'mode' => 'theme', 'label' => 'OTP via SMS', 'hint' => 'Send the checkout OTP as an SMS.', 'value' => $GetWebsite->otp_msg],
                    ['id' => 'top_contact_box', 'mode' => 'theme', 'label' => 'Contact Show on Top', 'hint' => 'Pin the contact box to the top of the storefront.', 'value' => $GetWebsite->top_contact_box],
                    ['id' => 'back_to_top_btn', 'mode' => 'theme', 'label' => 'Back to Top Button', 'hint' => 'Show a floating scroll-to-top button.', 'value' => $GetWebsite->back_to_top_btn],
                    ['id' => 'advertisement', 'mode' => 'theme', 'label' => 'Advertisement Notification', 'hint' => 'Show promotional notifications on the storefront.', 'value' => $GetWebsite->advertisement],
                ];
            @endphp

            <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                <div class="overflow-x-auto border-b border-erp-line px-5 pt-4">
                    <nav class="flex min-w-max gap-1 pb-px" id="themeTabs">
                        @foreach ($tabs as $key => $label)
                            <button type="button" data-tab="{{ $key }}"
                                class="theme-tab rounded-t-lg border-b-2 border-transparent px-4 py-3 text-sm font-bold text-erp-mute transition hover:text-erp-dark">
                                {{ $label }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- General --}}
                <div class="theme-panel hidden p-5" data-panel="general">
                    <div class="max-w-2xl space-y-5">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page Title</span>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="page_title" data-col="page_title" data-mode="website"
                                    value="{{ stripslashes($GetWebsite->page_title) }}" placeholder="Storefront page title"
                                    class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <button type="button" data-save="page_title"
                                    class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                            </div>
                            <span class="mt-1 block text-xs font-bold text-rose-600" id="page_title_alert"></span>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Meta Title</span>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="meta_title" data-col="meta_title" data-mode="website"
                                    value="{{ $GetWebsite->meta_title }}" placeholder="Title shown in search results"
                                    class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <button type="button" data-save="meta_title"
                                    class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                            </div>
                            <span class="mt-1 block text-xs font-bold text-rose-600" id="meta_title_alert"></span>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Meta Description</span>
                            <textarea rows="4" id="meta_description" data-col="meta_description" data-mode="website"
                                placeholder="Short description shown in search results"
                                class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ $GetWebsite->meta_description }}</textarea>
                            <div class="mt-2 flex items-center gap-3">
                                <button type="button" data-save="meta_description"
                                    class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                                <span class="text-xs font-bold text-rose-600" id="meta_description_alert"></span>
                            </div>
                        </label>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Department Nav Style</span>
                                <select id="depart_nav_layout" data-col="depart_nav_layout" data-mode="theme" data-placeholder="Select style"
                                    class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    <option value="">Select</option>
                                    <option value="1" {{ $GetWebsite->depart_nav_layout == 1 ? 'selected' : '' }}>UnderLine Navigate</option>
                                    <option value="2" {{ $GetWebsite->depart_nav_layout == 2 ? 'selected' : '' }}>Box Navigate</option>
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Font Style</span>
                                <select id="fonts" data-col="fontstyle" data-mode="theme" data-placeholder="Search font..."
                                    class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    <option value="">Select</option>
                                    @foreach (['Poppins', 'Roboto'] as $font)
                                        <option value="{{ $font }}" {{ $GetWebsite->fontstyle == $font ? 'selected' : '' }}>{{ $font }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Cart Style</span>
                                <select id="cart_layout" data-col="cart_layout" data-mode="theme" data-placeholder="Select style"
                                    class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    <option value="">Select</option>
                                    <option value="1" {{ $GetWebsite->cart_layout == '1' ? 'selected' : '' }}>Fixed Cart</option>
                                    <option value="2" {{ $GetWebsite->cart_layout == '2' ? 'selected' : '' }}>Drawer Cart</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Header --}}
                <div class="theme-panel hidden p-5" data-panel="header">
                    <div class="max-w-2xl space-y-5">
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-erp-line px-4 py-3">
                            <div>
                                <div class="text-sm font-bold text-erp-ink">TopBar Header Message</div>
                                <p class="mt-0.5 text-xs text-erp-mute">Show a single fixed message strip above the header.</p>
                            </div>
                            <label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center">
                                <input type="checkbox" id="topbar_mode" class="peer sr-only" data-toggle-mode="website"
                                    {{ !empty($GetWebsite->topbar) ? 'checked' : '' }}>
                                <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                                <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>

                        <label class="block {{ !empty($GetWebsite->topbar) ? '' : 'hidden' }}" id="topbarInput">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">TopBar Message</span>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="topbar" data-col="topbar" data-mode="website"
                                    value="{{ $GetWebsite->topbar }}" placeholder="e.g. Free delivery over Rs. 2000"
                                    class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <button type="button" data-save="topbar"
                                    class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                            </div>
                            <span class="mt-1 block text-xs font-bold text-rose-600" id="topbar_alert"></span>
                        </label>

                        <div class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">TopBar Slide Messages</span>
                            <p class="mt-1 text-xs text-erp-mute">Type a message and press Enter to add it. Up to 10 messages rotate in the strip.</p>
                            <div class="mt-2 flex gap-2">
                                <div class="w-full" id="slideMsgWrap">
                                    <input type="text" id="topbar_slide_msg" data-col="topbar_slide_msg" data-mode="website"
                                        value="{{ $topbarSlideValue }}" placeholder="Add a message and press Enter"
                                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                </div>
                                <button type="button" data-save="topbar_slide_msg"
                                    class="h-10 shrink-0 self-start rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                            </div>
                            <span class="mt-1 block text-xs font-bold text-rose-600" id="topbar_slide_msg_alert"></span>
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div class="theme-panel hidden p-5" data-panel="contact">
                    <div class="max-w-2xl space-y-5">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">WhatsApp Number</span>
                                <div class="mt-2 flex gap-2">
                                    <input type="text" id="whatsapp" data-col="whatsapp" data-mode="website"
                                        value="{{ $GetWebsite->whatsapp }}" placeholder="03001234567"
                                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    <button type="button" data-save="whatsapp"
                                        class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                                </div>
                                <span class="mt-1 block text-xs font-bold text-rose-600" id="whatsapp_alert"></span>
                            </label>

                            <label class="block">
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">UAN Number</span>
                                <div class="mt-2 flex gap-2">
                                    <input type="text" id="uan_number" data-col="uan_number" data-mode="website"
                                        value="{{ $GetWebsite->uan_number }}" placeholder="111 222 333"
                                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    <button type="button" data-save="uan_number"
                                        class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                                </div>
                                <span class="mt-1 block text-xs font-bold text-rose-600" id="uan_number_alert"></span>
                            </label>
                        </div>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Email</span>
                            <div class="mt-2 flex gap-2">
                                <input type="email" id="email" data-col="email" data-mode="website"
                                    value="{{ $GetWebsite->email }}" placeholder="orders@example.com"
                                    class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <button type="button" data-save="email"
                                    class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                            </div>
                            <span class="mt-1 block text-xs font-bold text-rose-600" id="email_alert"></span>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Address</span>
                            <textarea rows="4" id="address" data-col="address" data-mode="website"
                                placeholder="Shown in the storefront footer and contact page"
                                class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">{{ $GetWebsite->address }}</textarea>
                            <div class="mt-2 flex items-center gap-3">
                                <button type="button" data-save="address"
                                    class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                                <span class="text-xs font-bold text-rose-600" id="address_alert"></span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Branding --}}
                <div class="theme-panel hidden p-5" data-panel="branding">
                    <p class="mb-4 text-sm text-erp-mute">JPG, PNG or WEBP up to 1MB. Images upload as soon as you pick them.</p>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ([['logo', 'Light Logo', $lightLogo, 'h-24'], ['dark_logo', 'Dark Logo', $darkLogo, 'h-24'], ['favicon', 'Favicon', $favicon, 'h-12']] as $img)
                            <div class="rounded-lg border border-erp-line p-5 text-center">
                                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">{{ $img[1] }}</div>
                                <div class="mt-4 flex h-32 items-center justify-center rounded-lg bg-erp-soft p-3">
                                    <img id="{{ $img[0] }}images" src="{{ asset('storage/images/' . $img[2]) }}"
                                        class="{{ $img[3] }} max-w-full object-contain" alt="{{ $img[1] }}">
                                </div>
                                <label class="mt-4 block cursor-pointer rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-dark transition hover:border-erp hover:bg-emerald-50">
                                    Choose file
                                    <input type="file" name="{{ $img[0] }}" id="{{ $img[0] }}" accept="image/*"
                                        class="hidden" onchange="readURL(this, '{{ $img[0] }}images')">
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Product --}}
                <div class="theme-panel hidden p-5" data-panel="product">
                    <div class="grid max-w-3xl gap-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Product Navigation</span>
                            <select id="product_view" data-col="product_view" data-mode="theme" data-placeholder="Select view"
                                class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <option value="">Select</option>
                                <option value="modal_view" {{ $GetWebsite->product_view == 'modal_view' ? 'selected' : '' }}>Modal View</option>
                                <option value="page_view" {{ $GetWebsite->product_view == 'page_view' ? 'selected' : '' }}>Page View</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Product List View</span>
                            <select id="product_list" data-col="product_list" data-mode="theme" data-placeholder="Select layout"
                                class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <option value="">Select</option>
                                <option value="1" {{ $GetWebsite->product_list == 1 ? 'selected' : '' }}>Landscape</option>
                                <option value="2" {{ $GetWebsite->product_list == 2 ? 'selected' : '' }}>Vertical</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Location Modal</span>
                            <select id="location_modal" data-col="location_modal" data-mode="theme" data-placeholder="Select timing"
                                class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <option value="">Select</option>
                                <option value="0" {{ $GetWebsite->location_modal == 0 ? 'selected' : '' }}>After</option>
                                <option value="1" {{ $GetWebsite->location_modal == 1 ? 'selected' : '' }}>Start up</option>
                            </select>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="theme-panel hidden p-5" data-panel="footer">
                    <label class="block max-w-sm">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Footer Layout</span>
                        <select id="footer_layout" data-col="footer_layout" data-mode="theme" data-placeholder="Select layout"
                            class="v2-select2 theme-select mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">Select</option>
                            @foreach ([1, 2] as $layout)
                                <option value="{{ $layout }}" {{ $GetWebsite->footer_layout == $layout ? 'selected' : '' }}>Layout {{ $layout }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                {{-- Settings --}}
                <div class="theme-panel hidden p-5" data-panel="settings">
                    <div class="space-y-5">
                        <div class="rounded-lg border border-erp-line p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="text-sm font-bold text-erp-ink">Website Mode</div>
                                    <p class="mt-0.5 text-xs text-erp-mute">The website stays open while at least one branch is open.</p>
                                </div>
                                <label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center">
                                    <input type="checkbox" id="is_open" class="peer sr-only" data-toggle-mode="website"
                                        {{ $GetWebsite->is_open == 1 ? 'checked' : '' }}>
                                    <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                                    <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                </label>
                            </div>

                            <div class="mt-4 {{ $GetWebsite->is_open == 1 ? 'hidden' : '' }}" id="closingInput">
                                <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Closing Message</span>
                                <div class="mt-2 flex gap-2">
                                    <input type="text" id="closing_msg" data-col="closing_msg" data-mode="website"
                                        value="{{ !empty($GetWebsite->closing_msg) ? $GetWebsite->closing_msg : 'Sorry! We are closed right now' }}"
                                        class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                    <button type="button" data-save="closing_msg"
                                        class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                                </div>
                                <span class="mt-1 block text-xs font-bold text-rose-600" id="closing_msg_alert"></span>
                            </div>
                        </div>

                        <div class="grid gap-3 lg:grid-cols-2">
                            @foreach ($toggles as $toggle)
                                <div class="flex items-center justify-between gap-4 rounded-lg border border-erp-line px-4 py-3">
                                    <div>
                                        <div class="text-sm font-bold text-erp-ink">{{ $toggle['label'] }}</div>
                                        <p class="mt-0.5 text-xs text-erp-mute">{{ $toggle['hint'] }}</p>
                                    </div>
                                    <label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center">
                                        <input type="checkbox" id="{{ $toggle['id'] }}" class="peer sr-only"
                                            data-toggle-mode="{{ $toggle['mode'] }}" {{ $toggle['value'] == 1 ? 'checked' : '' }}>
                                        <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                                        <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <label class="block max-w-md">
                            <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Order Estimate Time</span>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="order_estimate_time" data-col="order_estimate_time" data-mode="website"
                                    value="{{ $GetWebsite->order_estimate_time }}" placeholder="e.g. 45 minutes"
                                    class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                                <button type="button" data-save="order_estimate_time"
                                    class="h-10 shrink-0 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
                            </div>
                            <span class="mt-1 block text-xs font-bold text-rose-600" id="order_estimate_time_alert"></span>
                        </label>
                    </div>
                </div>
            </section>

            {{-- Branch open/close --}}
            <div id="branchModal" class="fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/60 px-4 py-10"
                onclick="if (event.target === this) closeBranchModal()">
                <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
                    <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                        <div>
                            <h3 class="text-base font-bold text-erp-ink">Branch Lists</h3>
                            <p class="mt-1 text-sm text-erp-mute">Open at least one branch to keep the website open.</p>
                        </div>
                        <button type="button" onclick="closeBranchModal()"
                            class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute transition hover:bg-slate-100">&times;</button>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                                <tr>
                                    <th class="px-5 py-3 text-left font-bold">Branch Name</th>
                                    <th class="px-5 py-3 text-right font-bold">Is Open</th>
                                </tr>
                            </thead>
                            <tbody id="branchRows" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                    <div class="flex justify-end border-t border-erp-line px-5 py-4">
                        <button type="button" onclick="closeBranchModal()"
                            class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Close</button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-[60] hidden rounded-lg px-5 py-3 text-sm font-bold shadow-menu"></div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}" />
    <style>
        #slideMsgWrap .bootstrap-tagsinput {
            display: block;
            width: 100%;
            min-height: 2.5rem;
            padding: 0.35rem 0.5rem;
            border: 1px solid #d8e1ec;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            background: #fff;
            line-height: 1.4;
        }

        #slideMsgWrap .bootstrap-tagsinput input {
            width: auto !important;
            max-width: 100%;
            margin: 2px 0;
            font-size: 0.875rem;
        }

        #slideMsgWrap .bootstrap-tagsinput .tag {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin: 2px 4px 2px 0;
            padding: 0.15rem 0.45rem;
            border-radius: 0.375rem;
            background: #ecfdf5;
            color: #047857;
            font-size: 0.75rem;
            font-weight: 700;
        }

        #slideMsgWrap .bootstrap-tagsinput .tag [data-role="remove"] {
            margin-left: 0.15rem;
            cursor: pointer;
        }

        #slideMsgWrap .bootstrap-tagsinput .tag [data-role="remove"]:after {
            content: "\00d7";
            padding: 0 2px;
        }

        .theme-tab.is-active {
            border-color: #4CAF50;
            color: #2E7D32;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('components/bootstrap-tagsinput/dist/bootstrap-tagsinput.js') }}"></script>
    <script>
        var WEB_ID = "{{ $webId }}";
        var CSRF = "{{ csrf_token() }}";
        var ROUTES = {
            save: "{{ route('webSetSaveChanges') }}",
            branches: "{{ route('getWebsiteBrancheSchedule') }}",
            branchOpen: "{{ route('websiteBranchesIsOpen') }}",
            websiteOpen: "{{ route('websiteIsOpen') }}"
        };

        function showToast(message, success) {
            var toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'pointer-events-none fixed bottom-6 right-6 z-[60] rounded-lg px-5 py-3 text-sm font-bold shadow-menu ' +
                (success === false ? 'bg-rose-600 text-white' : 'bg-erp-dark text-white');
            clearTimeout(window.__toastTimer);
            window.__toastTimer = setTimeout(function () {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Every setting is stored through the same endpoint: one column at a time.
        function saveField(col, value, mode, onDone) {
            jQuery.ajax({
                url: ROUTES.save,
                type: 'POST',
                data: { _token: CSRF, id: WEB_ID, mode: mode, col: col, val: value },
                dataType: 'json',
                success: function (resp) {
                    if (resp === 'success') {
                        showToast('Saved.');
                    } else {
                        showToast(typeof resp === 'string' ? resp : 'Unable to save this setting.', false);
                    }
                    if (onDone) {
                        onDone(resp === 'success');
                    }
                },
                error: function () {
                    showToast('Unable to save this setting.', false);
                    if (onDone) {
                        onDone(false);
                    }
                }
            });
        }

        function saveText(id) {
            var field = document.getElementById(id);
            var alertBox = document.getElementById(id + '_alert');
            var value = (field.value || '').trim();

            if (value === '') {
                if (alertBox) {
                    alertBox.textContent = 'Field is required.';
                }
                field.focus();
                return;
            }

            if (alertBox) {
                alertBox.textContent = '';
            }
            saveField(field.getAttribute('data-col'), field.value, field.getAttribute('data-mode'));
        }

        function readURL(input, previewId) {
            if (!input.files || !input.files[0]) {
                return;
            }

            var file = input.files[0];

            if (file.size > 1024 * 1024) {
                showToast('File size must be less than 1MB.', false);
                input.value = '';
                return;
            }

            if (!/(\.jpg|\.jpeg|\.png|\.webp)$/i.test(file.name)) {
                showToast('Please select a JPG, PNG or WEBP image.', false);
                input.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(previewId).src = e.target.result;
                uploadImage(input);
            };
            reader.readAsDataURL(file);
        }

        function uploadImage(input) {
            var data = new FormData();
            data.append('value', input.files[0]);
            data.append('col', input.getAttribute('name'));
            data.append('_token', CSRF);
            data.append('id', WEB_ID);
            data.append('mode', 'website');

            jQuery.ajax({
                url: ROUTES.save,
                type: 'POST',
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (resp) {
                    if (resp === 'success') {
                        showToast('Image uploaded.');
                    } else {
                        showToast(typeof resp === 'string' ? resp : 'Unable to upload this image.', false);
                    }
                },
                error: function () {
                    showToast('Unable to upload this image.', false);
                }
            });
        }

        function loadBranches(id) {
            jQuery.ajax({
                url: ROUTES.branches,
                type: 'POST',
                data: { _token: CSRF, id: id },
                dataType: 'json',
                success: function (resp) {
                    var tbody = document.getElementById('branchRows');
                    tbody.innerHTML = '';

                    jQuery.each(resp || [], function (i, v) {
                        var tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-50';

                        var name = document.createElement('td');
                        name.className = 'px-5 py-3 font-bold text-erp-ink';
                        name.textContent = v.branch_name;

                        var action = document.createElement('td');
                        action.className = 'px-5 py-3 text-right';

                        var label = document.createElement('label');
                        label.className = 'relative inline-flex h-6 w-11 cursor-pointer items-center';
                        label.innerHTML = '<input type="checkbox" class="peer sr-only">' +
                            '<span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>' +
                            '<span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>';

                        var checkbox = label.querySelector('input');
                        checkbox.checked = v.is_open == 1;
                        checkbox.addEventListener('change', function () {
                            branchIsOpen(v.id, id, this);
                        });

                        action.appendChild(label);
                        tr.append(name, action);
                        tbody.appendChild(tr);
                    });

                    if (!tbody.children.length) {
                        tbody.innerHTML = '<tr><td colspan="2" class="px-5 py-10 text-center text-erp-mute">No branches are linked to this website.</td></tr>';
                    }
                },
                error: function () {
                    showToast('Unable to load branches.', false);
                }
            });
        }

        function branchIsOpen(id, webId, input) {
            var value = input.checked ? 1 : 0;

            jQuery.ajax({
                url: ROUTES.branchOpen,
                type: 'POST',
                data: { _token: CSRF, id: id, value: value, website: webId },
                success: function () {
                    showToast(value ? 'Branch opened.' : 'Branch closed.');
                },
                error: function () {
                    input.checked = !input.checked;
                    showToast('Unable to update this branch.', false);
                }
            });
        }

        function openBranchModal() {
            var modal = document.getElementById('branchModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // The website is only open while a branch is open, so re-check on close.
        function closeBranchModal() {
            var modal = document.getElementById('branchModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            if (!WEB_ID) {
                return;
            }

            jQuery.ajax({
                url: ROUTES.websiteOpen,
                type: 'POST',
                data: { _token: CSRF, website: WEB_ID },
                dataType: 'json',
                success: function (resp) {
                    if (resp == 0) {
                        var master = document.getElementById('is_open');
                        if (master && master.checked) {
                            master.checked = false;
                            document.getElementById('closingInput').classList.remove('hidden');
                            showToast('No branch is open, so the website stays closed.', false);
                        }
                    }
                }
            });
        }

        function activateTab(key) {
            var panels = document.querySelectorAll('.theme-panel');
            var found = false;

            panels.forEach(function (panel) {
                var match = panel.getAttribute('data-panel') === key;
                panel.classList.toggle('hidden', !match);
                if (match) {
                    found = true;
                }
            });

            document.querySelectorAll('.theme-tab').forEach(function (tab) {
                tab.classList.toggle('is-active', tab.getAttribute('data-tab') === key);
            });

            return found;
        }

        jQuery(function () {
            var $website = jQuery('#website');
            if ($website.hasClass('select2-hidden-accessible')) {
                $website.select2('destroy');
            }
            $website.select2({
                dropdownCssClass: 'v2-select2-dropdown',
                width: '100%',
                allowClear: true,
                placeholder: $website.data('placeholder') || 'Select'
            });

            $website.on('change', function () {
                var id = jQuery(this).val();
                if (id) {
                    window.location = "{{ url('website/theme-setting') }}/" + id;
                } else {
                    document.getElementById('website_alert').textContent = 'Field is required.';
                }
            });

            if (!document.querySelector('.theme-panel')) {
                return;
            }

            jQuery('.theme-select').each(function () {
                var $select = jQuery(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    dropdownCssClass: 'v2-select2-dropdown',
                    width: '100%',
                    allowClear: true,
                    placeholder: $select.data('placeholder') || 'Select'
                });
            });

            jQuery('.theme-select').on('change', function () {
                saveField(jQuery(this).data('col'), jQuery(this).val(), jQuery(this).data('mode'));
            });

            if (jQuery.fn.tagsinput) {
                jQuery('#topbar_slide_msg').tagsinput({ maxTags: 10 });
            }

            document.querySelectorAll('[data-save]').forEach(function (button) {
                button.addEventListener('click', function () {
                    saveText(button.getAttribute('data-save'));
                });
            });

            document.querySelectorAll('input[data-col]').forEach(function (input) {
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        saveText(input.id);
                    }
                });
            });

            // The topbar toggle only reveals the message field; clearing it wipes the column.
            document.getElementById('topbar_mode').addEventListener('change', function () {
                var box = document.getElementById('topbarInput');
                if (this.checked) {
                    box.classList.remove('hidden');
                    document.getElementById('topbar').focus();
                } else {
                    box.classList.add('hidden');
                    saveField('topbar', null, 'website');
                }
            });

            // Opening the website is driven by the branch list, not by this toggle alone.
            document.getElementById('is_open').addEventListener('change', function () {
                var closing = document.getElementById('closingInput');
                if (this.checked) {
                    closing.classList.add('hidden');
                    loadBranches(WEB_ID);
                    openBranchModal();
                } else {
                    closing.classList.remove('hidden');
                    saveField('is_open', 0, 'website');
                }
            });

            document.querySelectorAll('input[data-toggle-mode]').forEach(function (input) {
                if (input.id === 'topbar_mode' || input.id === 'is_open') {
                    return;
                }
                input.addEventListener('change', function () {
                    saveField(input.id, input.checked ? 1 : 0, input.getAttribute('data-toggle-mode'));
                });
            });

            document.querySelectorAll('.theme-tab').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var key = tab.getAttribute('data-tab');
                    activateTab(key);
                    history.replaceState(null, '', '#' + key);
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeBranchModal();
                }
            });

            if (!activateTab((location.hash || '').replace('#', ''))) {
                activateTab('general');
            }
        });
    </script>
@endpush
