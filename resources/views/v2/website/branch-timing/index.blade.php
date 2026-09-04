@extends('layouts.master-tailwind')

@section('title', 'Branch Timings')
@section('page_title', 'Branch Timings')
@section('page_subtitle', 'Set the weekly opening and closing hours for every branch attached to your websites.')

@section('content')
    @php
        $websiteCollection = collect($websites ?? []);
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
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($websiteCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available website records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</div>
                <div class="mt-4 truncate text-2xl font-black text-erp-ink" id="statBranch">&mdash;</div>
                <p class="mt-2 text-sm text-erp-mute">Currently selected branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Open Days</div>
                <div class="mt-4 text-3xl font-black text-erp-ink"><span id="statOpenDays">0</span><span class="text-lg font-bold text-erp-mute">/7</span></div>
                <p class="mt-2 text-sm text-erp-mute">Days with hours configured</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Weekly Hours</div>
                <div class="mt-4 text-3xl font-black text-erp-ink" id="statWeeklyHours">0h</div>
                <p class="mt-2 text-sm text-erp-mute">Total trading hours per week</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Select Branch</h2>
                <p class="mt-1 text-sm text-erp-mute">Pick a website, then choose the branch whose schedule you want to manage.</p>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-12">
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select name="website" id="website" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert"></span>
                </label>

                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
                    <select name="branch" id="branch" data-placeholder="Search branch..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                        <option value="">Select Branch</option>
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="branch_alert"></span>
                </label>

                <div class="flex items-end md:col-span-2">
                    <button type="button" id="btnReset"
                        class="h-10 w-full rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                        Reset
                    </button>
                </div>
            </div>
        </section>

        <section id="emptyState" class="rounded-lg border border-dashed border-erp-line bg-erp-soft px-6 py-14 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-erp shadow-sm">
                <i class="icofont icofont-clock-time text-2xl"></i>
            </div>
            <h3 class="mt-4 text-base font-bold text-erp-ink">No branch selected</h3>
            <p class="mt-1 text-sm text-erp-mute">Choose a website and a branch above to load its weekly timing sheet.</p>
        </section>

        <section id="loadingState" class="hidden rounded-lg border border-erp-line bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-erp-line border-t-erp"></div>
            <p class="mt-4 text-sm font-bold text-erp-mute">Loading schedule...</p>
        </section>

        <section id="listBox" class="hidden rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Weekly Schedule</h2>
                    <p class="mt-1 text-sm text-erp-mute">Leave both times empty to mark a day as closed.</p>
                </div>
                <span id="branchBadge"
                    class="hidden self-start rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 sm:self-auto"></span>
            </div>
            <div id="tablediv"></div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        #tablediv input[type="time"]::-webkit-calendar-picker-indicator {
            opacity: .45;
            cursor: pointer;
        }

        #tablediv input[type="time"]:focus::-webkit-calendar-picker-indicator {
            opacity: 1;
        }

        #tablediv .day-block:nth-child(even) {
            background-color: #fcfdfe;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            var $ = window.jQuery;
            if (!$) {
                return;
            }

            function initSelect($el) {
                if (!$.fn.select2) {
                    return;
                }
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    dropdownCssClass: 'v2-select2-dropdown',
                    width: '100%',
                    allowClear: true,
                    placeholder: $el.data('placeholder') || 'Select'
                });
            }

            function setAlert(id, message) {
                $('#' + id).text(message || '');
            }

            function showPanel(name) {
                $('#emptyState').toggleClass('hidden', name !== 'empty');
                $('#loadingState').toggleClass('hidden', name !== 'loading');
                $('#listBox').toggleClass('hidden', name !== 'list');
            }

            function resetStats() {
                $('#statBranch').html('&mdash;');
                $('#statOpenDays').text('0');
                $('#statWeeklyHours').text('0h');
                $('#branchBadge').addClass('hidden').text('');
            }

            // Called by the injected schedule partial whenever its rows change.
            window.branchTimingStats = function (openDays, minutes) {
                $('#statOpenDays').text(openDays);
                $('#statWeeklyHours').text((Math.round((minutes / 60) * 10) / 10) + 'h');
            };

            $(function () {
                initSelect($('#website'));
                initSelect($('#branch'));

                $('#website').on('change', function () {
                    var id = $(this).val();
                    var $branch = $('#branch');

                    setAlert('website_alert', '');
                    setAlert('branch_alert', '');
                    resetStats();
                    showPanel('empty');

                    $branch.prop('disabled', true).empty().append('<option value="">Select Branch</option>');
                    initSelect($branch);

                    if (!id) {
                        return;
                    }

                    $.ajax({
                        url: '{{ route('getWebsiteBranches') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            websiteId: id
                        },
                        success: function (resp) {
                            var branches = resp || [];
                            $branch.empty().append('<option value="">Select Branch</option>');
                            $.each(branches, function (i, v) {
                                $branch.append($('<option/>').val(v.branch_id).text(v.branch_name));
                            });
                            $branch.prop('disabled', branches.length === 0);
                            initSelect($branch);

                            if (!branches.length) {
                                setAlert('branch_alert', 'No branches are linked to this website.');
                            }
                        },
                        error: function () {
                            setAlert('branch_alert', 'Unable to load branches. Please try again.');
                        }
                    });
                });

                $('#branch').on('change', function () {
                    var branchId = $(this).val();
                    var websiteId = $('#website').val();

                    setAlert('branch_alert', '');

                    if (!branchId) {
                        resetStats();
                        showPanel('empty');
                        return;
                    }

                    if (!websiteId) {
                        setAlert('website_alert', 'Please select a website first.');
                        return;
                    }

                    var branchName = $(this).find('option:selected').text();
                    $('#statBranch').text(branchName);
                    $('#branchBadge').removeClass('hidden').text(branchName);
                    showPanel('loading');

                    $.ajax({
                        url: '{{ route('getBranchTiming') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            websiteId: websiteId,
                            branchId: branchId
                        },
                        success: function (resp) {
                            $('#tablediv').html(resp);
                            showPanel('list');
                        },
                        error: function () {
                            showPanel('empty');
                            setAlert('branch_alert', 'Unable to load the schedule. Please try again.');
                        }
                    });
                });

                $('#btnReset').on('click', function () {
                    $('#website').val('').trigger('change');
                });
            });
        })();
    </script>
@endpush
