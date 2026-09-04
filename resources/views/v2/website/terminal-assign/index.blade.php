@extends('layouts.master-tailwind')

@section('title', 'Terminal Assign')
@section('page_title', 'Terminal Assign')
@section('page_subtitle', 'Bind a POS terminal to each website branch and control which branches are open for online orders.')

@section('content')
    @php
        $websiteCollection = collect($websites ?? []);
        $branchCollection = collect($branches ?? []);
        $assignCollection = collect($terminalAssigns ?? []);
        $openCount = $assignCollection->where('is_open', 1)->count();
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
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Assignments</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($assignCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active terminal bindings</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Open Branches</div>
                <div class="mt-4 text-3xl font-black text-erp-ink"><span id="statOpen">{{ number_format($openCount) }}</span><span class="text-lg font-bold text-erp-mute">/{{ number_format($assignCollection->count()) }}</span></div>
                <p class="mt-2 text-sm text-erp-mute">Currently accepting online orders</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Websites</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($websiteCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available website records</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branches</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($branchCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active company branches</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Assign Terminal</h2>
                <p class="mt-1 text-sm text-erp-mute">Choose a branch to load the terminals registered against it.</p>
            </div>
            <form id="terminalAssignForm" method="post" action="{{ route('terminalAssignStore') }}"
                class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select name="website" id="website" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}" {{ old('website') == $val->id ? 'selected' : '' }}>{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="website_alert"></span>
                </label>

                <label class="block md:col-span-4">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
                    <select name="branch" id="branch" data-placeholder="Search branch..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Branch</option>
                        @foreach ($branchCollection as $val)
                            <option value="{{ $val->branch_id }}" {{ old('branch') == $val->branch_id ? 'selected' : '' }}>{{ $val->branch_name }}</option>
                        @endforeach
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="branch_alert"></span>
                </label>

                <label class="block md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Terminal</span>
                    <select name="terminal" id="terminal" data-placeholder="Search terminal..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                        <option value="">Select Terminal</option>
                    </select>
                    <span class="mt-1 block text-xs font-bold text-rose-600" id="terminal_alert"></span>
                </label>

                <div class="md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Is Open Branch</span>
                    <label class="mt-2 flex h-10 cursor-pointer items-center gap-3 rounded-lg border border-erp-line px-3 shadow-sm">
                        <span class="relative inline-flex h-6 w-11 items-center">
                            <input type="checkbox" id="is_open" name="is_open" class="peer sr-only" {{ old('is_open') ? 'checked' : '' }}>
                            <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                            <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                        </span>
                        <span class="text-sm font-bold text-erp-text">Open</span>
                    </label>
                </div>

                <div class="flex md:col-span-12 md:justify-end">
                    <button type="submit"
                        class="h-10 rounded-lg bg-erp px-6 text-sm font-bold text-white transition hover:bg-erp-dark">Create Assignment</button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Assignments</h2>
                    <p class="mt-1 text-sm text-erp-mute">Toggle a row to open or close that branch for online orders.</p>
                </div>
                <input type="search" id="assignFilter" placeholder="Filter by website, branch or terminal..."
                    class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-96">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Website</th>
                            <th class="px-5 py-3 text-left font-bold">Branch</th>
                            <th class="px-5 py-3 text-left font-bold">Terminal</th>
                            <th class="px-5 py-3 text-left font-bold">Is Open</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="assignRows" class="divide-y divide-slate-100">
                        @forelse ($assignCollection as $assign)
                            <tr class="assign-row hover:bg-slate-50" data-id="{{ $assign->id }}"
                                data-website="{{ $assign->website_id }}" data-branch="{{ $assign->branch_id }}"
                                data-terminal="{{ $assign->terminal_id }}" data-open="{{ $assign->is_open }}">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $assign->name }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $assign->branch_name }}</td>
                                <td class="px-5 py-4 text-erp-text">
                                    {{ $assign->terminal_name }}
                                    <span class="ml-1 rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">#{{ $assign->terminal_id }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <label class="relative inline-flex h-6 w-11 cursor-pointer items-center">
                                            <input type="checkbox" class="peer sr-only" id="branchStatus-{{ $assign->id }}"
                                                onchange="branchIsOpen({{ $assign->id }}, this)" {{ $assign->is_open == 1 ? 'checked' : '' }}>
                                            <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                                            <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                        </label>
                                        <span data-open-label
                                            class="rounded-md px-2 py-1 text-xs font-bold ring-1 {{ $assign->is_open == 1 ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-slate-200' }}">{{ $assign->is_open == 1 ? 'Open' : 'Closed' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button type="button" onclick="openEditModal({{ $assign->id }})"
                                            class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-dark transition hover:border-erp hover:bg-emerald-50">Edit</button>
                                        <button type="button" onclick="openDeleteModal({{ $assign->id }}, @js($assign->name . ' / ' . $assign->branch_name)) "
                                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="assignEmptyRow">
                                <td colspan="5" class="px-5 py-12 text-center text-erp-mute">No terminal assignments yet.</td>
                            </tr>
                        @endforelse
                        <tr id="assignNoMatchRow" class="hidden">
                            <td colspan="5" class="px-5 py-12 text-center text-erp-mute">No assignments match your search.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Edit assignment --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden items-start justify-center overflow-y-auto bg-slate-950/60 px-4 py-10"
        onclick="if (event.target === this) closeEditModal()">
        <div class="relative w-full max-w-xl rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Edit Assign Terminal</h3>
                    <p class="mt-1 text-sm text-erp-mute">Re-bind this branch to a different website or terminal.</p>
                </div>
                <button type="button" onclick="closeEditModal()"
                    class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute transition hover:bg-slate-100">&times;</button>
            </div>

            <form id="editFormTerminalBind" method="post" class="space-y-4 px-5 py-5">
                @csrf
                <input type="hidden" id="id_md" name="id">
                <input type="hidden" name="mode" value="0">

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Website</span>
                    <select name="website_md" id="website_md" data-placeholder="Search website..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websiteCollection as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Branch</span>
                    <select name="branch_md" id="branch_md" data-placeholder="Search branch..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Branch</option>
                        @foreach ($branchCollection as $val)
                            <option value="{{ $val->branch_id }}">{{ $val->branch_name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Terminal</span>
                    <select name="terminal_md" id="terminal_md" data-placeholder="Search terminal..."
                        class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" disabled>
                        <option value="">Select Terminal</option>
                    </select>
                </label>

                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Is Open Branch</span>
                    <label class="mt-2 flex h-10 w-fit cursor-pointer items-center gap-3 rounded-lg border border-erp-line px-3 shadow-sm">
                        <span class="relative inline-flex h-6 w-11 items-center">
                            <input type="checkbox" id="is_open_md" name="is_open_md" class="peer sr-only">
                            <span class="absolute inset-0 rounded-full bg-slate-300 transition peer-checked:bg-erp"></span>
                            <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                        </span>
                        <span class="text-sm font-bold text-erp-text">Open</span>
                    </label>
                </div>
            </form>

            <div class="flex items-center justify-between gap-3 border-t border-erp-line px-5 py-4">
                <span id="editModalStatus" class="text-sm font-bold text-rose-600"></span>
                <div class="flex gap-2">
                    <button type="button" onclick="closeEditModal()"
                        class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</button>
                    <button type="button" id="btn_update"
                        class="rounded-lg bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete confirmation --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4"
        onclick="if (event.target === this) closeDeleteModal()">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Delete Assignment</h3>
            </div>
            <div class="px-5 py-5 text-sm text-erp-text">
                Are you sure you want to delete <span id="deleteTargetName" class="font-bold text-erp-ink"></span>?
                This branch will stop accepting online orders through that terminal.
            </div>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" onclick="closeDeleteModal()"
                    class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Cancel</button>
                <button type="button" id="btnConfirmDelete"
                    class="rounded-lg bg-rose-600 px-5 py-2 text-sm font-bold text-white transition hover:bg-rose-700">Delete</button>
            </div>
        </div>
    </div>

    <div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-[60] hidden rounded-lg px-5 py-3 text-sm font-bold shadow-menu"></div>
@endsection

@push('scripts')
    <script>
        var TERMINAL_ROUTES = {
            terminals: "{{ route('getTerminalBranches') }}",
            update: "{{ route('terminalAssignUpdate') }}",
            destroy: "{{ route('deleteWebsiteTerminal') }}"
        };
        var CSRF = "{{ csrf_token() }}";

        function showToast(message, success) {
            var toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'pointer-events-none fixed bottom-6 right-6 z-[60] rounded-lg px-5 py-3 text-sm font-bold shadow-menu ' +
                (success === false ? 'bg-rose-600 text-white' : 'bg-erp-dark text-white');
            clearTimeout(window.__toastTimer);
            window.__toastTimer = setTimeout(function () {
                toast.classList.add('hidden');
            }, 3200);
        }

        function initSelect($el, parent) {
            if (!window.jQuery || !jQuery.fn.select2) {
                return;
            }
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            var options = {
                dropdownCssClass: 'v2-select2-dropdown',
                width: '100%',
                allowClear: true,
                placeholder: $el.data('placeholder') || 'Select'
            };
            if (parent) {
                options.dropdownParent = parent;
            }
            $el.select2(options);
        }

        // Loads the terminals of a branch into a select, optionally preselecting one.
        function loadTerminals(branchId, elementId, selectedValue, parent) {
            var $select = jQuery('#' + elementId);

            $select.prop('disabled', true).empty().append(jQuery('<option>').val('').text('Loading...'));
            initSelect($select, parent);

            if (!branchId) {
                $select.empty().append(jQuery('<option>').val('').text('Select Terminal'));
                initSelect($select, parent);
                return jQuery.Deferred().resolve().promise();
            }

            return jQuery.ajax({
                url: TERMINAL_ROUTES.terminals,
                type: 'POST',
                data: { _token: CSRF, branchId: branchId }
            }).done(function (resp) {
                var terminals = resp || [];
                $select.empty().append(jQuery('<option>').val('').text('Select Terminal'));
                jQuery.each(terminals, function (i, v) {
                    $select.append(jQuery('<option>').val(v.terminal_id).text(v.terminal_name + ' (#' + v.terminal_id + ')'));
                });
                $select.prop('disabled', terminals.length === 0);
                if (selectedValue) {
                    $select.val(String(selectedValue));
                }
                initSelect($select, parent);
            }).fail(function () {
                $select.empty().append(jQuery('<option>').val('').text('Select Terminal')).prop('disabled', false);
                initSelect($select, parent);
                showToast('Unable to load terminals for this branch.', false);
            });
        }

        function openEditModal(id) {
            var row = document.querySelector('.assign-row[data-id="' + id + '"]');
            if (!row) {
                return;
            }

            document.getElementById('id_md').value = id;
            document.getElementById('editModalStatus').textContent = '';
            document.getElementById('is_open_md').checked = row.getAttribute('data-open') === '1';

            jQuery('#website_md').val(row.getAttribute('data-website')).trigger('change.select2');
            jQuery('#branch_md').val(row.getAttribute('data-branch')).trigger('change.select2');
            loadTerminals(row.getAttribute('data-branch'), 'terminal_md', row.getAttribute('data-terminal'), jQuery('#editModal > div'));

            var modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeEditModal() {
            var modal = document.getElementById('editModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        var deleteTargetId = null;

        function openDeleteModal(id, label) {
            deleteTargetId = id;
            document.getElementById('deleteTargetName').textContent = label;
            var modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            deleteTargetId = null;
            var modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function refreshOpenStat() {
            var open = document.querySelectorAll('.assign-row[data-open="1"]').length;
            document.getElementById('statOpen').textContent = open;
        }

        function branchIsOpen(id, input) {
            var value = input.checked ? 1 : 0;
            var row = document.querySelector('.assign-row[data-id="' + id + '"]');

            jQuery.ajax({
                url: TERMINAL_ROUTES.update,
                type: 'POST',
                data: { _token: CSRF, is_open: value, id: id, mode: 1 },
                success: function (resp) {
                    if (resp.status == 200) {
                        var label = row.querySelector('[data-open-label]');
                        row.setAttribute('data-open', value);
                        label.textContent = value ? 'Open' : 'Closed';
                        label.className = 'rounded-md px-2 py-1 text-xs font-bold ring-1 ' + (value
                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                            : 'bg-slate-100 text-slate-600 ring-slate-200');
                        refreshOpenStat();
                        showToast(value ? 'Branch is now open.' : 'Branch is now closed.');
                    } else {
                        input.checked = !input.checked;
                        showToast('Unable to update this branch.', false);
                    }
                },
                error: function () {
                    input.checked = !input.checked;
                    showToast('Unable to update this branch.', false);
                }
            });
        }

        jQuery(function () {
            initSelect(jQuery('#website'));
            initSelect(jQuery('#branch'));
            initSelect(jQuery('#terminal'));

            var $modalPanel = jQuery('#editModal > div');
            initSelect(jQuery('#website_md'), $modalPanel);
            initSelect(jQuery('#branch_md'), $modalPanel);
            initSelect(jQuery('#terminal_md'), $modalPanel);

            @if (old('branch'))
                loadTerminals("{{ old('branch') }}", 'terminal', "{{ old('terminal') }}");
            @endif

            jQuery('#branch').on('change', function () {
                jQuery('#branch_alert').text('');
                loadTerminals(jQuery(this).val(), 'terminal', '');
            });

            jQuery('#branch_md').on('change', function () {
                loadTerminals(jQuery(this).val(), 'terminal_md', '', $modalPanel);
            });

            document.getElementById('terminalAssignForm').addEventListener('submit', function (event) {
                var missing = null;

                jQuery('#website_alert, #branch_alert, #terminal_alert').text('');

                if (!jQuery('#terminal').val()) {
                    jQuery('#terminal_alert').text('Please select a terminal.');
                    missing = '#terminal';
                }
                if (!jQuery('#branch').val()) {
                    jQuery('#branch_alert').text('Please select a branch.');
                    missing = '#branch';
                }
                if (!jQuery('#website').val()) {
                    jQuery('#website_alert').text('Please select a website.');
                    missing = '#website';
                }

                if (missing) {
                    event.preventDefault();
                    jQuery(missing).select2('open');
                }
            });

            document.getElementById('btn_update').addEventListener('click', function () {
                var status = document.getElementById('editModalStatus');
                status.textContent = '';

                if (!jQuery('#website_md').val() || !jQuery('#branch_md').val() || !jQuery('#terminal_md').val()) {
                    status.textContent = 'Website, branch and terminal are all required.';
                    return;
                }

                var button = this;
                button.disabled = true;
                button.classList.add('opacity-60');

                jQuery.ajax({
                    url: TERMINAL_ROUTES.update,
                    type: 'POST',
                    data: jQuery('#editFormTerminalBind').serialize(),
                    success: function (resp) {
                        if (resp.status == 200) {
                            showToast('Assignment updated.');
                            location.reload();
                        } else {
                            status.textContent = resp.msg || 'Unable to update this assignment.';
                            button.disabled = false;
                            button.classList.remove('opacity-60');
                        }
                    },
                    error: function () {
                        status.textContent = 'Unable to update this assignment.';
                        button.disabled = false;
                        button.classList.remove('opacity-60');
                    }
                });
            });

            document.getElementById('btnConfirmDelete').addEventListener('click', function () {
                if (!deleteTargetId) {
                    return;
                }

                var button = this;
                button.disabled = true;
                button.classList.add('opacity-60');

                jQuery.ajax({
                    url: TERMINAL_ROUTES.destroy,
                    type: 'POST',
                    data: { _token: CSRF, id: deleteTargetId },
                    success: function (resp) {
                        if (resp.status == 200) {
                            location.reload();
                        } else {
                            showToast(resp.message || 'Unable to delete this assignment.', false);
                            button.disabled = false;
                            button.classList.remove('opacity-60');
                            closeDeleteModal();
                        }
                    },
                    error: function () {
                        showToast('Unable to delete this assignment.', false);
                        button.disabled = false;
                        button.classList.remove('opacity-60');
                        closeDeleteModal();
                    }
                });
            });

            document.getElementById('assignFilter').addEventListener('input', function () {
                var term = this.value.toLowerCase();
                var visible = 0;

                document.querySelectorAll('#assignRows tr.assign-row').forEach(function (row) {
                    var match = row.textContent.toLowerCase().indexOf(term) !== -1;
                    row.hidden = !match;
                    if (match) {
                        visible += 1;
                    }
                });

                var emptyRow = document.getElementById('assignEmptyRow');
                document.getElementById('assignNoMatchRow').classList.toggle('hidden', visible > 0 || !!emptyRow);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeEditModal();
                    closeDeleteModal();
                }
            });
        });
    </script>
@endpush
