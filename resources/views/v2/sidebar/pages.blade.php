@extends('layouts.master-tailwind')

@section('title', 'Pages')
@section('page_title', 'Sidebar Pages')
@section('page_subtitle', 'Manage ERP sidebar entries, hierarchy, page URLs, icons, and menu modes.')

@section('content')
    @php
        $pageCollection = collect($details ?? []);
        $parentPages = $pageCollection->where('page_mode', 'Parent');
        $labelPages = $pageCollection->where('page_mode', 'Label');
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Pages</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($pageCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Registered sidebar entries</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Parents</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($parentPages->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Expandable navigation groups</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Labels</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($labelPages->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Sidebar separators</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Child Pages</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($pageCollection->where('parent_id', '!=', 0)->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Nested menu items</p>
            </div>
        </section>

        @if(session()->has('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
                {{ session()->get('success') }}
            </div>
        @endif

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Create Sidebar Page</h2>
                <p class="mt-1 text-sm text-erp-mute">Add a page, label, parent, child, or deeper sidebar entry.</p>
            </div>
            <form method="POST" action="{{ url('/insert-page') }}" class="p-5">
                @csrf
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page Name</span>
                        <input type="text" name="pagename" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="Inventory">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page URL</span>
                        <input type="text" name="pageurl" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="inventory">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Nav Class</span>
                        <input type="text" name="navclass" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="navinventory">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Icofont Class</span>
                        <input type="text" name="icofont" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp" placeholder="icofont-box">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Parent</span>
                        <select name="parent" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="0">No Parent</option>
                            @foreach($details as $value)
                                <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page Mode</span>
                        <select name="pagemode" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                            <option value="">Select Page Mode</option>
                            <option value="Parent">Parent</option>
                            <option value="Child">Child</option>
                            <option value="Grand Child">Grand Child</option>
                            <option value="Grand Grand Child">Grand Grand Child</option>
                            <option value="Label">Label</option>
                        </select>
                    </label>
                </div>
                <label class="mt-4 inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" name="iconarrow" class="rounded border-erp-line text-erp focus:ring-erp">
                    Show expand arrow
                </label>
                <div class="mt-5 flex justify-end">
                    <button type="submit" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Submit</button>
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Pages Detail</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review and maintain sidebar hierarchy.</p>
                </div>
                <input type="search" id="pageFilter" class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp lg:w-80" placeholder="Filter pages...">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Page</th>
                            <th class="px-5 py-3 text-left font-bold">URL</th>
                            <th class="px-5 py-3 text-left font-bold">Icon</th>
                            <th class="px-5 py-3 text-left font-bold">Parent</th>
                            <th class="px-5 py-3 text-left font-bold">Mode</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pagesTableBody" class="divide-y divide-slate-100">
                        @foreach($details as $value)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $value->page_name }}</td>
                                <td class="px-5 py-4 font-mono text-xs text-erp-mute">{{ $value->page_url }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->icofont }}</td>
                                <td class="px-5 py-4 text-erp-text">{{ $value->parent_id }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-slate-700 ring-1 ring-slate-200">{{ $value->page_mode }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button"
                                            data-id="{{ $value->id }}"
                                            data-page-name="{{ e($value->page_name) }}"
                                            data-page-url="{{ e($value->page_url) }}"
                                            data-nav-class="{{ e($value->navclass) }}"
                                            data-icofont="{{ e($value->icofont) }}"
                                            data-parent-id="{{ $value->parent_id }}"
                                            data-page-mode="{{ e($value->page_mode) }}"
                                            data-arrow="{{ $value->icofont_arrow }}"
                                            class="edit-page rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                        <button type="button" onclick="deletePage({{ $value->id }})" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div id="editPageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <form method="POST" action="{{ url('/update-page') }}" class="w-full max-w-4xl rounded-lg bg-white shadow-menu">
            @csrf
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Update Sidebar Page</h3>
                <button type="button" onclick="closeEditPageModal()" class="rounded-lg px-2 py-1 text-xl leading-none text-erp-mute hover:bg-slate-100">x</button>
            </div>
            <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                <input type="hidden" name="pageid" id="pageid">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page Name</span>
                    <input type="text" name="updatepagename" id="updatepagename" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page URL</span>
                    <input type="text" name="updatepageurl" id="updatepageurl" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Nav Class</span>
                    <input type="text" name="updatenavclass" id="updatenavclass" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Icofont Class</span>
                    <input type="text" name="updateicofont" id="updateicofont" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Parent</span>
                    <select name="updateparent" id="updateparent" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="0">No Parent</option>
                        @foreach($details as $value)
                            <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Page Mode</span>
                    <select name="updatepagemode" id="updatepagemode" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Page Mode</option>
                        <option value="Parent">Parent</option>
                        <option value="Child">Child</option>
                        <option value="Grand Child">Grand Child</option>
                        <option value="Grand Grand Child">Grand Grand Child</option>
                        <option value="Label">Label</option>
                    </select>
                </label>
            </div>
            <div class="flex items-center justify-between border-t border-erp-line px-5 py-4">
                <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" name="updateiconarrow" id="updateiconarrow" class="rounded border-erp-line text-erp focus:ring-erp">
                    Show expand arrow
                </label>
                <button type="submit" class="rounded-lg bg-erp px-5 py-2.5 text-sm font-bold text-white transition hover:bg-erp-dark">Update</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('pageFilter')?.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#pagesTableBody tr').forEach(function (row) {
                row.hidden = !row.textContent.toLowerCase().includes(term);
            });
        });

        document.querySelectorAll('.edit-page').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('pageid').value = this.dataset.id;
                document.getElementById('updatepagename').value = this.dataset.pageName || '';
                document.getElementById('updatepageurl').value = this.dataset.pageUrl || '';
                document.getElementById('updatenavclass').value = this.dataset.navClass || '';
                document.getElementById('updateicofont').value = this.dataset.icofont || '';
                document.getElementById('updateparent').value = this.dataset.parentId || '0';
                document.getElementById('updatepagemode').value = this.dataset.pageMode || '';
                document.getElementById('updateiconarrow').checked = this.dataset.arrow !== '0';
                document.getElementById('editPageModal').classList.remove('hidden');
                document.getElementById('editPageModal').classList.add('flex');
            });
        });

        function closeEditPageModal() {
            document.getElementById('editPageModal').classList.add('hidden');
            document.getElementById('editPageModal').classList.remove('flex');
        }

        function deletePage(id) {
            if (!confirm('Delete this page?')) {
                return;
            }

            fetch("{{ url('/remove-page') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({ id: id })
            }).then(response => response.text()).then(function (response) {
                if (response.trim() === '1') {
                    window.location = "{{ url('/pages') }}";
                } else {
                    alert('Unable to delete page.');
                }
            }).catch(function () {
                alert('Unable to delete page.');
            });
        }
    </script>
@endpush
