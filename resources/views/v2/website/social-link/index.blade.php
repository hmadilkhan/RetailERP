@extends('layouts.master-tailwind')

@section('title', 'Social Links')
@section('page_title', 'Social Links')
@section('page_subtitle', 'Connect each website to its social profiles shown across the storefront.')

@section('content')
    @php
        $websiteCollection = collect($websites ?? []);
        $listCollection = collect($lists ?? []);
        $subCollection = collect($sublists ?? []);

        // Brand look-up: label comes from the controller, the icon falls back when a row has none.
        $platforms = [
            'fb'        => ['icon' => 'icofont icofont-social-facebook', 'chip' => 'bg-blue-50 text-blue-700 ring-blue-200'],
            'insta'     => ['icon' => 'icofont icofont-social-instagram', 'chip' => 'bg-pink-50 text-pink-700 ring-pink-200'],
            'linkedin'  => ['icon' => 'fa-brands fa-linkedin', 'chip' => 'bg-sky-50 text-sky-700 ring-sky-200'],
            'twite'     => ['icon' => 'fa-brands fa-twitter', 'chip' => 'bg-slate-100 text-slate-700 ring-slate-300'],
            'youtube'   => ['icon' => 'icofont icofont-social-youtube', 'chip' => 'bg-rose-50 text-rose-700 ring-rose-200'],
            'tiktok'    => ['icon' => 'fa-brands fa-tiktok', 'chip' => 'bg-slate-100 text-slate-800 ring-slate-300'],
            'pinterest' => ['icon' => 'icofont icofont-social-pinterest', 'chip' => 'bg-red-50 text-red-700 ring-red-200'],
            'snapchat'  => ['icon' => 'icofont icofont-social-snapchat', 'chip' => 'bg-amber-50 text-amber-700 ring-amber-200'],
        ];

        $usedByWebsite = $subCollection->groupBy('website_id')->map(fn($rows) => $rows->pluck('social_type')->values());
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
        @if (Session::has('socialType'))
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-bold text-amber-700">
                {{ Session::get('socialType') }}
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
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Social Links</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($subCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Profiles connected in total</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Connected Websites</div>
                <div class="mt-4 text-3xl font-black text-erp-ink"><span>{{ number_format($listCollection->count()) }}</span><span class="text-lg font-bold text-erp-mute">/{{ number_format($websiteCollection->count()) }}</span></div>
                <p class="mt-2 text-sm text-erp-mute">Websites with at least one link</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Platforms</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format(count($socialFullName)) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available social platforms</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Most Used</div>
                <div class="mt-4 truncate text-2xl font-black text-erp-ink">
                    {{ $subCollection->count() ? ($socialFullName[$subCollection->groupBy('social_type')->sortByDesc(fn($r) => $r->count())->keys()->first()] ?? '—') : '—' }}
                </div>
                <p class="mt-2 text-sm text-erp-mute">Most connected platform</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Create Social Link</h2>
                <p class="mt-1 text-sm text-erp-mute">Each website can hold one link per platform.</p>
            </div>
            <form method="post" action="{{ route('socialinkStore') }}" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select name="website" id="website" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}" {{ old('website') == $val->id ? 'selected' : '' }}>{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert">@error('website'){{ $message }}@enderror</span>
                </label>

                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Social Type</span>
                    <select name="socialType" id="socialType" data-placeholder="Search platform..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Platform</option>
                        @foreach ($socialFullName as $type => $label)
                            <option value="{{ $type }}" {{ old('socialType') == $type ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="socialType_alert">@error('socialType'){{ $message }}@enderror</span>
                </label>

                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">URL</span>
                    <input type="text" name="url" id="url" value="{{ old('url') }}" placeholder="https://facebook.com/your-page"
                        class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="url_alert">@error('url'){{ $message }}@enderror</span>
                </label>

                <div class="flex items-start md:col-span-2 md:pt-6">
                    <button type="submit" id="btn_create"
                        class="h-10 w-full rounded-lg bg-erp px-5 text-sm font-bold text-white transition hover:bg-erp-dark">Add Link</button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Websites Social Links</h2>
                    <p class="mt-1 text-sm text-erp-mute">Click a platform chip to edit or remove that link.</p>
                </div>
                <input type="search" id="socialFilter" placeholder="Filter by website or platform..."
                    class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Website</th>
                            <th class="px-5 py-3 text-left font-bold">Social Links</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="socialRows" class="divide-y divide-slate-100">
                        @forelse ($listCollection as $value)
                            @php
                                $links = $subCollection->where('website_id', $value->website_id);
                            @endphp
                            <tr class="social-row hover:bg-slate-50">
                                <td class="px-5 py-4 align-top font-bold text-erp-ink">
                                    {{ $value->name }}
                                    <div class="mt-1 text-xs font-bold text-erp-mute">{{ $links->count() }} {{ $links->count() === 1 ? 'link' : 'links' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex max-w-3xl flex-wrap gap-2">
                                        @foreach ($links as $link)
                                            @php
                                                $meta = $platforms[$link->social_type] ?? ['icon' => 'icofont icofont-link', 'chip' => 'bg-slate-100 text-slate-700 ring-slate-200'];
                                                $label = $socialFullName[$link->social_type] ?? $link->social_type;
                                            @endphp
                                            <button type="button"
                                                onclick="openEditModal(@js($link->id), @js($label), @js($link->url))"
                                                title="{{ $link->url }}"
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-bold ring-1 transition hover:opacity-80 {{ $meta['chip'] }}">
                                                <i class="{{ $link->icon ?: $meta['icon'] }} text-base"></i>
                                                {{ $label }}
                                            </button>

                                            <form id="DestroyFormValue{{ $link->id }}" action="{{ route('socialinkDestroy', [$link->id]) }}" method="post" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="mode" value="v">
                                            </form>
                                            <form id="UpdateFormValue{{ $link->id }}" action="{{ route('socialinkUpdate', $link->id) }}" method="post" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="id" value="{{ $link->id }}">
                                                <input type="hidden" name="type" value="{{ $link->social_type }}">
                                                <input type="hidden" name="value" id="upformUrl{{ $link->id }}" value="{{ $link->url }}">
                                            </form>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <div class="flex justify-end">
                                        <button type="button" onclick="openConfirm('DestroyForm{{ $value->website_id }}', @js('Remove all social links from ' . $value->name . '?'))"
                                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Remove All</button>
                                    </div>
                                    <form id="DestroyForm{{ $value->website_id }}" action="{{ route('socialinkDestroy', [$value->website_id]) }}" method="post" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="socialEmptyRow">
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No social links added yet.</td>
                            </tr>
                        @endforelse
                        <tr id="socialNoMatchRow" class="hidden">
                            <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No websites match your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Edit link --}}
    <div id="edit_Modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeEditModal()">
        <div class="w-full max-w-lg rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Edit Social Link</h3>
                    <p class="mt-1 text-sm text-erp-mute"><span id="label_md" class="font-bold text-erp-dark"></span></p>
                </div>
                <button type="button" onclick="closeEditModal()"
                    class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute transition hover:bg-slate-100">&times;</button>
            </div>
            <div class="px-5 py-5">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">URL</span>
                    <input type="text" id="text_md" placeholder="https://..."
                        class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="alert_md"></span>
                </label>
            </div>
            <div class="flex items-center justify-between gap-3 border-t border-erp-line px-5 py-4">
                <button type="button" id="btn_remove_md"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">Remove</button>
                <div class="flex gap-2">
                    <button type="button" onclick="closeEditModal()"
                        class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Close</button>
                    <button type="button" id="btn_update_md"
                        class="rounded-lg bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete confirmation --}}
    <div id="confirmModal" class="fixed inset-0 z-[55] hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeConfirm()">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Remove Social Link</h3>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
@endpush

@push('scripts')
    <script>
        // Platforms already connected per website, so the form can flag duplicates before submit.
        var USED_TYPES = @js($usedByWebsite);
        var editId = null;
        var confirmFormId = null;

        function openEditModal(id, label, url) {
            editId = id;
            document.getElementById('label_md').textContent = label;
            document.getElementById('text_md').value = url || '';
            document.getElementById('alert_md').textContent = '';

            var modal = document.getElementById('edit_Modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('text_md').focus();
        }

        function closeEditModal() {
            var modal = document.getElementById('edit_Modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

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

        jQuery(function () {
            jQuery('#website, #socialType').each(function () {
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

            function checkDuplicate() {
                var website = jQuery('#website').val();
                var type = jQuery('#socialType').val();
                var alertBox = document.getElementById('socialType_alert');

                if (website && type && (USED_TYPES[website] || []).indexOf(type) !== -1) {
                    alertBox.textContent = 'This website already has a link for that platform.';
                    return true;
                }

                alertBox.textContent = '';
                return false;
            }

            jQuery('#website, #socialType').on('change', checkDuplicate);

            document.getElementById('btn_create').addEventListener('click', function (event) {
                var missing = null;

                document.getElementById('website_alert').textContent = '';
                document.getElementById('url_alert').textContent = '';

                if (!document.getElementById('url').value.trim()) {
                    document.getElementById('url_alert').textContent = 'URL is required.';
                    missing = '#url';
                }
                if (!jQuery('#socialType').val()) {
                    document.getElementById('socialType_alert').textContent = 'Please select a platform.';
                    missing = '#socialType';
                }
                if (!jQuery('#website').val()) {
                    document.getElementById('website_alert').textContent = 'Please select a website.';
                    missing = '#website';
                }

                if (missing || checkDuplicate()) {
                    event.preventDefault();
                    if (missing === '#url') {
                        document.getElementById('url').focus();
                    } else if (missing) {
                        jQuery(missing).select2('open');
                    }
                }
            });

            document.getElementById('btn_update_md').addEventListener('click', function () {
                var input = document.getElementById('text_md');
                var value = (input.value || '').trim();

                if (value === '') {
                    document.getElementById('alert_md').textContent = 'Field is required.';
                    input.focus();
                    return;
                }

                document.getElementById('upformUrl' + editId).value = value;
                document.getElementById('UpdateFormValue' + editId).submit();
            });

            document.getElementById('btn_remove_md').addEventListener('click', function () {
                var label = document.getElementById('label_md').textContent;
                closeEditModal();
                openConfirm('DestroyFormValue' + editId, 'Are you sure you want to remove the ' + label + ' link?');
            });

            document.getElementById('btnConfirmYes').addEventListener('click', function () {
                if (!confirmFormId) {
                    return;
                }
                this.disabled = true;
                this.classList.add('opacity-60');
                document.getElementById(confirmFormId).submit();
            });

            document.getElementById('text_md').addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    document.getElementById('btn_update_md').click();
                }
            });

            document.getElementById('socialFilter').addEventListener('input', function () {
                var term = this.value.toLowerCase();
                var visible = 0;

                document.querySelectorAll('#socialRows tr.social-row').forEach(function (row) {
                    var match = row.textContent.toLowerCase().indexOf(term) !== -1;
                    row.hidden = !match;
                    if (match) {
                        visible += 1;
                    }
                });

                var emptyRow = document.getElementById('socialEmptyRow');
                document.getElementById('socialNoMatchRow').classList.toggle('hidden', visible > 0 || !!emptyRow);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeConfirm();
                    closeEditModal();
                }
            });
        });
    </script>
@endpush
