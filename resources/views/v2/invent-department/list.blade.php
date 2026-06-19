@extends('layouts.master-tailwind')

@section('title', 'Departments')
@section('page_title', 'Departments')
@section('page_subtitle', 'Organize the catalogue into departments and sub-departments, control website visibility, and manage banners from one workspace.')

@section('content')
    @php
        $totalDepartments = $depart->count();
        $totalSubDepartments = count($sdepart);
        $linkedCount = $depart->filter(function ($d) {
            return $d->websiteProducts->pluck('website_id')->filter()->isNotEmpty();
        })->count();
        $subDeptsByDept = collect($sdepart)->groupBy('department_id');
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Departments</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($totalDepartments) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active records in current scope</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Sub-Departments</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($totalSubDepartments) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Across all departments</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Linked to Website</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($linkedCount) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Departments visible online</p>
            </div>
            <a href="{{ route('departmentCreate') }}" class="flex rounded-lg border border-erp bg-erp p-5 text-white shadow-sm transition hover:bg-erp-dark">
                <div class="self-end">
                    <div class="text-xs font-bold uppercase tracking-[0.16em] text-white/70">Action</div>
                    <div class="mt-4 text-xl font-black">Create Department</div>
                    <p class="mt-2 text-sm text-white/75">Add a new catalogue department</p>
                </div>
            </a>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-erp-line px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Department Directory</h2>
                    <p class="mt-1 text-sm text-erp-mute">Search, edit, manage sub-departments, link to website, or remove departments.</p>
                </div>
                <input type="text" id="departmentSearch" autocomplete="off" placeholder="Search by name or code..."
                    class="h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Department</th>
                            <th class="px-5 py-3 text-left font-bold">Website Name</th>
                            <th class="px-5 py-3 text-left font-bold">Sub-Departments</th>
                            <th class="px-5 py-3 text-center font-bold">Priority</th>
                            <th class="px-5 py-3 text-left font-bold">Website</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="departmentTableBody">
                        @forelse ($depart as $d)
                            @php
                                $sectionIds = $d->inventoryDepartmentSection->pluck('section_id')->values();
                                $isLinked = $d->websiteProducts->pluck('website_id')->filter()->isNotEmpty();
                                $subDepts = $subDeptsByDept->get($d->department_id, collect());
                                $departmentPayload = [
                                    'id' => $d->department_id,
                                    'code' => $d->code,
                                    'name' => $d->department_name,
                                    'webName' => $d->website_department_name,
                                    'image' => $d->image,
                                    'banner' => $d->banner,
                                    'mobileBanner' => $d->mobile_banner,
                                    'websiteMode' => (int) $d->website_mode,
                                    'priority' => $d->priority,
                                    'metaTitle' => $d->meta_title,
                                    'metaDescription' => $d->meta_description,
                                    'sections' => $sectionIds,
                                ];
                            @endphp
                            <tr class="department-row hover:bg-slate-50" data-search="{{ strtolower($d->department_name . ' ' . $d->code) }}">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                                            src="{{ !empty($d->image) ? asset('storage/images/department/' . $d->image) : asset('storage/images/no-image.png') }}"
                                            alt="{{ $d->department_name }}">
                                        <div class="min-w-0">
                                            <div class="truncate font-bold text-erp-ink">{{ $d->department_name }}</div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-erp-mute">
                                                {{ $d->code ?? 'No code' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-erp-text">{{ $d->website_department_name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($subDepts->take(3) as $sd)
                                            <span class="rounded-md bg-sky-50 px-2 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">{{ $sd->sub_depart_name }}</span>
                                        @empty
                                            <span class="text-erp-mute">-</span>
                                        @endforelse
                                        @if ($subDepts->count() > 3)
                                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-erp-mute">+{{ $subDepts->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center font-semibold text-erp-ink">{{ $d->priority ?? 0 }}</td>
                                <td class="px-5 py-4">
                                    @if ($isLinked)
                                        <span class="inline-flex rounded-md bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">Linked</span>
                                    @else
                                        <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-xs font-bold text-erp-mute ring-1 ring-slate-200">Not linked</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <button type="button" onclick='openManageSubDepartmentsModal({{ $d->department_id }}, @js($d->department_name), @js($d->code))'
                                            class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">
                                            Sub-Depts
                                        </button>
                                        <button type="button" onclick="openAddSubDepartmentModal({{ $d->department_id }})"
                                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                            Add Sub-Dept
                                        </button>
                                        <button type="button" onclick='openEditDepartmentModal(@json($departmentPayload))'
                                            class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">
                                            Edit
                                        </button>
                                        @if ($isLinked)
                                            <button type="button" onclick="unlinkWebsite({{ $d->department_id }})"
                                                class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                                Unlink Website
                                            </button>
                                        @else
                                            <button type="button" onclick="openWebsiteLinkModal({{ $d->department_id }})"
                                                class="rounded-lg border border-erp-line px-3 py-2 text-xs font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">
                                                Link Website
                                            </button>
                                        @endif
                                        <button type="button" onclick="deleteDepartment({{ $d->department_id }})"
                                            class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="text-base font-bold text-erp-ink">No departments found</div>
                                    <p class="mt-2 text-sm text-erp-mute">Create your first department to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Edit Department Modal --}}
    <div id="editDepartmentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-2xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Edit Department</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('editDepartmentModal')">Close</button>
            </div>
            <form id="editDepartmentForm" enctype="multipart/form-data" class="space-y-4 px-5 py-5">
                <input type="hidden" id="editDeptId" value="">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Code</span>
                        <input type="text" id="editDeptCode" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Department Name <span class="text-rose-500">*</span></span>
                        <input type="text" id="editDeptName" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <span id="editDeptName_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                    </label>
                </div>

                <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" id="editShowWebsite" class="rounded border-erp-line text-erp focus:ring-erp">
                    Show on Website
                </label>

                <div id="editWebsiteFields" class="hidden space-y-4 rounded-lg border border-erp-line bg-slate-50 p-4">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Website Department Name</span>
                        <input type="text" id="editWebDeptName" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>

                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sections</span>
                        <div class="mt-2 grid max-h-32 grid-cols-2 gap-2 overflow-y-auto rounded-lg border border-erp-line bg-white p-3 sm:grid-cols-3" id="editSectionsBox">
                            @forelse ($sections as $val)
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-erp-text">
                                    <input type="checkbox" class="edit-section-checkbox rounded border-erp-line text-erp focus:ring-erp" value="{{ $val->id }}">
                                    {{ $val->name }}
                                </label>
                            @empty
                                <span class="text-xs text-erp-mute">No sections available.</span>
                            @endforelse
                        </div>
                    </div>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Priority</span>
                        <input type="number" min="0" id="editPriority" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Meta Title</span>
                        <input type="text" id="editMetaTitle" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>

                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Meta Description</span>
                        <textarea id="editMetaDescription" rows="3" class="mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp"></textarea>
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Desktop Banner</span>
                            <img id="editBannerPreview" src="{{ asset('storage/images/no-image.png') }}" class="mt-2 h-20 w-full rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" id="editBannerImage" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-xs text-erp-text file:mr-3 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                        </label>
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Mobile Banner</span>
                            <img id="editMobileBannerPreview" src="{{ asset('storage/images/no-image.png') }}" class="mt-2 h-20 w-full rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" id="editMobileBannerImage" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-xs text-erp-text file:mr-3 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                        </label>
                    </div>
                </div>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Department Image</span>
                    <img id="editImagePreview" src="{{ asset('storage/images/no-image.png') }}" class="mt-2 h-20 w-20 rounded-lg object-cover ring-1 ring-slate-200">
                    <input type="file" id="editDeptImage" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-xs text-erp-text file:mr-3 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                </label>
            </form>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text" onclick="closeModal('editDepartmentModal')">Cancel</button>
                <button type="button" id="editDepartmentSubmit" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white hover:bg-erp-dark">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Add Sub-Department Modal --}}
    <div id="addSubDepartmentModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-lg overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Add Sub-Department</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('addSubDepartmentModal')">Close</button>
            </div>
            <div class="space-y-4 px-5 py-5">
                <input type="hidden" id="addSubDeptDeptId" value="">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Code</span>
                    <input type="text" id="addSubDeptCode" placeholder="Sub-department code" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                </label>
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sub-Department Name <span class="text-rose-500">*</span></span>
                    <input type="text" id="addSubDeptName" placeholder="Sub-department name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span id="addSubDeptName_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                </label>

                <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                    <input type="checkbox" id="addSubDeptShowWebsite" class="rounded border-erp-line text-erp focus:ring-erp">
                    Show on Website
                </label>

                <div id="addSubDeptWebsiteFields" class="hidden space-y-4 rounded-lg border border-erp-line bg-slate-50 p-4">
                    <label class="block">
                        <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Website Sub-Department Name</span>
                        <input type="text" id="addSubDeptWebName" placeholder="Website sub-department name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </label>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Desktop Banner</span>
                            <input type="file" id="addSubDeptBanner" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-xs text-erp-text file:mr-3 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                        </label>
                        <label class="block">
                            <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Mobile Banner</span>
                            <input type="file" id="addSubDeptMobileBanner" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-xs text-erp-text file:mr-3 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                        </label>
                    </div>
                </div>

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Sub-Department Image</span>
                    <input type="file" id="addSubDeptImage" accept="image/*" class="mt-2 block w-full rounded-lg border border-erp-line bg-white text-xs text-erp-text file:mr-3 file:border-0 file:bg-erp file:px-3 file:py-2 file:text-xs file:font-bold file:text-white">
                </label>
            </div>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text" onclick="closeModal('addSubDepartmentModal')">Cancel</button>
                <button type="button" id="addSubDepartmentSubmit" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white hover:bg-erp-dark">Add Sub-Department</button>
            </div>
        </div>
    </div>

    {{-- Manage Sub-Departments Modal --}}
    <div id="manageSubDepartmentsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="max-h-full w-full max-w-4xl overflow-y-auto rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <div>
                    <h3 class="text-base font-bold text-erp-ink">Manage Sub-Departments</h3>
                    <p id="manageSubDeptName" class="mt-1 text-sm text-erp-mute"></p>
                </div>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('manageSubDepartmentsModal')">Close</button>
            </div>
            <input type="hidden" id="manageSubDeptDeptId" value="">
            <div class="overflow-x-auto px-5 py-5">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-3 py-2 text-left font-bold">Code</th>
                            <th class="px-3 py-2 text-left font-bold">Sub-Department</th>
                            <th class="px-3 py-2 text-left font-bold">Website Name</th>
                            <th class="px-3 py-2 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="manageSubDeptTableBody" class="divide-y divide-slate-100">
                        <tr><td colspan="4" class="px-3 py-6 text-center text-erp-mute">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white hover:bg-erp-dark" onclick="closeModal('manageSubDepartmentsModal'); window.location.reload();">Done</button>
            </div>
        </div>
    </div>

    {{-- Website Link Modal --}}
    <div id="websiteLinkModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-6">
        <div class="w-full max-w-md rounded-lg bg-white shadow-menu">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h3 class="text-base font-bold text-erp-ink">Link to Website</h3>
                <button type="button" class="text-erp-mute hover:text-erp-ink" onclick="closeModal('websiteLinkModal')">Close</button>
            </div>
            <div class="space-y-4 px-5 py-5">
                <input type="hidden" id="websiteLinkDeptId" value="">
                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Website <span class="text-rose-500">*</span></span>
                    <select id="websiteLinkSelect" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Website</option>
                        @foreach ($websites as $val)
                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            <div class="flex justify-end gap-2 border-t border-erp-line px-5 py-4">
                <button type="button" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text" onclick="closeModal('websiteLinkModal')">Cancel</button>
                <button type="button" id="websiteLinkSubmit" class="rounded-lg border border-erp bg-erp px-4 py-2 text-sm font-bold text-white hover:bg-erp-dark">Save Changes</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = "{{ csrf_token() }}";
        const noImage = "{{ asset('storage/images/no-image.png') }}";
        const storageBase = "{{ asset('storage/images/department') }}/";

        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        document.getElementById('departmentSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.department-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        /* ---------- Edit Department ---------- */
        function toggleWebsiteFields(checkboxId, boxId) {
            const checkbox = document.getElementById(checkboxId);
            const box = document.getElementById(boxId);
            const sync = () => box.classList.toggle('hidden', !checkbox.checked);
            checkbox.addEventListener('change', sync);
            return sync;
        }
        const syncEditWebsiteFields = toggleWebsiteFields('editShowWebsite', 'editWebsiteFields');
        const syncAddSubDeptWebsiteFields = toggleWebsiteFields('addSubDeptShowWebsite', 'addSubDeptWebsiteFields');

        function openEditDepartmentModal(dept) {
            document.getElementById('editDeptId').value = dept.id;
            document.getElementById('editDeptCode').value = dept.code ?? '';
            document.getElementById('editDeptName').value = dept.name ?? '';
            document.getElementById('editDeptName_alert').textContent = '';
            document.getElementById('editWebDeptName').value = dept.webName ?? '';
            document.getElementById('editPriority').value = dept.priority ?? '';
            document.getElementById('editMetaTitle').value = dept.metaTitle ?? '';
            document.getElementById('editMetaDescription').value = dept.metaDescription ?? '';
            document.getElementById('editDeptImage').value = '';
            document.getElementById('editBannerImage').value = '';
            document.getElementById('editMobileBannerImage').value = '';

            document.getElementById('editImagePreview').src = dept.image ? storageBase + dept.image : noImage;
            document.getElementById('editBannerPreview').src = dept.banner ? storageBase + dept.banner : noImage;
            document.getElementById('editMobileBannerPreview').src = dept.mobileBanner ? storageBase + dept.mobileBanner : noImage;

            document.querySelectorAll('.edit-section-checkbox').forEach(cb => {
                cb.checked = (dept.sections || []).map(String).includes(cb.value);
            });

            document.getElementById('editShowWebsite').checked = Number(dept.websiteMode) === 1;
            syncEditWebsiteFields();

            openModal('editDepartmentModal');
        }

        document.getElementById('editDeptImage').addEventListener('change', function () {
            if (this.files[0]) document.getElementById('editImagePreview').src = URL.createObjectURL(this.files[0]);
        });
        document.getElementById('editBannerImage').addEventListener('change', function () {
            if (this.files[0]) document.getElementById('editBannerPreview').src = URL.createObjectURL(this.files[0]);
        });
        document.getElementById('editMobileBannerImage').addEventListener('change', function () {
            if (this.files[0]) document.getElementById('editMobileBannerPreview').src = URL.createObjectURL(this.files[0]);
        });

        document.getElementById('editDepartmentSubmit').addEventListener('click', function () {
            const name = document.getElementById('editDeptName').value.trim();
            if (!name) {
                document.getElementById('editDeptName_alert').textContent = 'Department name is required.';
                return;
            }

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('departid', document.getElementById('editDeptId').value);
            formData.append('editcode', document.getElementById('editDeptCode').value);
            formData.append('departname', name);
            formData.append('priority', document.getElementById('editPriority').value);

            if (document.getElementById('editShowWebsite').checked) {
                formData.append('showWebsite', '1');
                formData.append('webdeptname', document.getElementById('editWebDeptName').value);
                formData.append('metatitle', document.getElementById('editMetaTitle').value);
                formData.append('metadescript', document.getElementById('editMetaDescription').value);
                document.querySelectorAll('.edit-section-checkbox:checked').forEach(cb => {
                    formData.append('sections[]', cb.value);
                });
            }

            if (document.getElementById('editDeptImage').files[0]) {
                formData.append('departImage', document.getElementById('editDeptImage').files[0]);
            }
            if (document.getElementById('editBannerImage').files[0]) {
                formData.append('bannerImage', document.getElementById('editBannerImage').files[0]);
            }
            if (document.getElementById('editMobileBannerImage').files[0]) {
                formData.append('mobile_banner', document.getElementById('editMobileBannerImage').files[0]);
            }

            fetch("{{ url('/updatedepart') }}", { method: 'POST', body: formData })
                .then(r => r.json())
                .then(resp => {
                    if (resp.state === 1) {
                        if (resp.contrl === 'deptname') {
                            document.getElementById('editDeptName_alert').textContent = resp.msg;
                        } else {
                            alert(resp.msg);
                        }
                        return;
                    }
                    window.location.reload();
                })
                .catch(() => alert('Unable to save department changes.'));
        });

        /* ---------- Add Sub-Department ---------- */
        function openAddSubDepartmentModal(departId) {
            document.getElementById('addSubDeptDeptId').value = departId;
            document.getElementById('addSubDeptCode').value = '';
            document.getElementById('addSubDeptName').value = '';
            document.getElementById('addSubDeptName_alert').textContent = '';
            document.getElementById('addSubDeptWebName').value = '';
            document.getElementById('addSubDeptShowWebsite').checked = false;
            document.getElementById('addSubDeptImage').value = '';
            document.getElementById('addSubDeptBanner').value = '';
            document.getElementById('addSubDeptMobileBanner').value = '';
            syncAddSubDeptWebsiteFields();
            openModal('addSubDepartmentModal');
        }

        document.getElementById('addSubDepartmentSubmit').addEventListener('click', function () {
            const name = document.getElementById('addSubDeptName').value.trim();
            if (!name) {
                document.getElementById('addSubDeptName_alert').textContent = 'Sub-department name is required.';
                return;
            }

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('departid', document.getElementById('addSubDeptDeptId').value);
            formData.append('code', document.getElementById('addSubDeptCode').value);
            formData.append('subdepart', name);
            formData.append('websubdepart', document.getElementById('addSubDeptWebName').value);

            if (document.getElementById('addSubDeptShowWebsite').checked) {
                formData.append('showWebsite', '1');
            }

            if (document.getElementById('addSubDeptImage').files[0]) {
                formData.append('subdepartImage', document.getElementById('addSubDeptImage').files[0]);
            }
            if (document.getElementById('addSubDeptBanner').files[0]) {
                formData.append('subdepartBanner', document.getElementById('addSubDeptBanner').files[0]);
            }
            if (document.getElementById('addSubDeptMobileBanner').files[0]) {
                formData.append('mobile_banner_sbdepart', document.getElementById('addSubDeptMobileBanner').files[0]);
            }

            fetch("{{ url('/addsubdepart') }}", { method: 'POST', body: formData })
                .then(r => r.json())
                .then(resp => {
                    if (resp === 0 || resp === false) {
                        document.getElementById('addSubDeptName_alert').textContent = 'This sub-department already exists.';
                        return;
                    }
                    window.location.reload();
                })
                .catch(() => alert('Unable to add sub-department.'));
        });

        /* ---------- Manage Sub-Departments ---------- */
        function openManageSubDepartmentsModal(departId, departName, departCode) {
            document.getElementById('manageSubDeptDeptId').value = departId;
            document.getElementById('manageSubDeptName').textContent = departName + (departCode ? ' (' + departCode + ')' : '');
            document.getElementById('manageSubDeptTableBody').innerHTML =
                '<tr><td colspan="4" class="px-3 py-6 text-center text-erp-mute">Loading...</td></tr>';
            openModal('manageSubDepartmentsModal');

            fetch("{{ url('/getsubdepart') }}?departid=" + departId)
                .then(r => r.json())
                .then(rows => renderSubDepartmentRows(rows, departId, departCode))
                .catch(() => {
                    document.getElementById('manageSubDeptTableBody').innerHTML =
                        '<tr><td colspan="4" class="px-3 py-6 text-center text-rose-600">Unable to load sub-departments.</td></tr>';
                });
        }

        function renderSubDepartmentRows(rows, departId, departCode) {
            const body = document.getElementById('manageSubDeptTableBody');
            if (!Array.isArray(rows) || rows.length === 0) {
                body.innerHTML = '<tr><td colspan="4" class="px-3 py-6 text-center text-erp-mute">No sub-departments yet.</td></tr>';
                return;
            }

            body.innerHTML = rows.map(row => `
                <tr id="subDeptRow-${row.sub_department_id}">
                    <td class="px-3 py-2">
                        <input type="text" id="subDeptCode-${row.sub_department_id}" value="${escapeAttr(row.code ?? '')}" placeholder="Code"
                            class="h-9 w-24 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" id="subDeptName-${row.sub_department_id}" value="${escapeAttr(row.sub_depart_name ?? '')}" placeholder="Sub-department name"
                            class="h-9 w-full min-w-[10rem] rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <span id="subDeptName-${row.sub_department_id}_alert" class="mt-1 block text-xs font-semibold text-rose-600"></span>
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" id="subDeptWebName-${row.sub_department_id}" value="${escapeAttr(row.website_sub_department_name ?? '')}" placeholder="Website name"
                            class="h-9 w-full min-w-[10rem] rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    </td>
                    <td class="px-3 py-2 text-right">
                        <div class="flex justify-end gap-2">
                            <button type="button" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100"
                                onclick="updateSubDepartment(${row.sub_department_id}, ${departId})">Update</button>
                            <button type="button" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100"
                                onclick="deleteSubDepartment(${row.sub_department_id})">Remove</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function escapeAttr(value) {
            return String(value).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function updateSubDepartment(id, departId) {
            const name = document.getElementById('subDeptName-' + id).value.trim();
            const alertEl = document.getElementById('subDeptName-' + id + '_alert');
            if (!name) {
                alertEl.textContent = 'Sub-department name is required.';
                return;
            }
            alertEl.textContent = '';

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('id', id);
            formData.append('dept', departId);
            formData.append('code', document.getElementById('subDeptCode-' + id).value);
            formData.append('sdepart', name);
            formData.append('website_department_name', document.getElementById('subDeptWebName-' + id).value);

            fetch("{{ route('invent_sb_deptup') }}", { method: 'POST', body: formData })
                .then(r => r.json())
                .then(resp => {
                    if (resp.state === 1) {
                        alertEl.textContent = resp.msg;
                        return;
                    }
                    alert(resp.msg || 'Saved.');
                })
                .catch(() => alert('Unable to update sub-department.'));
        }

        function deleteSubDepartment(id) {
            if (!confirm('Remove this sub-department?')) return;

            fetch("{{ url('/delete-subdepartment') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id })
            })
                .then(response => {
                    if (response.ok) {
                        document.getElementById('subDeptRow-' + id)?.remove();
                    } else {
                        alert('Unable to remove sub-department.');
                    }
                })
                .catch(() => alert('Unable to remove sub-department.'));
        }

        /* ---------- Website Link / Unlink ---------- */
        function openWebsiteLinkModal(departId) {
            document.getElementById('websiteLinkDeptId').value = departId;
            document.getElementById('websiteLinkSelect').value = '';
            openModal('websiteLinkModal');
        }

        document.getElementById('websiteLinkSubmit').addEventListener('click', function () {
            const websiteId = document.getElementById('websiteLinkSelect').value;
            if (!websiteId) {
                alert('Select a website first.');
                return;
            }
            websiteConnection(document.getElementById('websiteLinkDeptId').value, websiteId, 'link');
        });

        function unlinkWebsite(departId) {
            if (!confirm('Unlink this department from the website?')) return;
            websiteConnection(departId, '', 'unlink');
        }

        function websiteConnection(departId, websiteId, statusCode) {
            fetch("{{ route('department_website_connect') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ department: departId, website_id: websiteId, status_code: statusCode })
            })
                .then(response => response.json().then(msg => ({ ok: response.ok, msg })))
                .then(({ ok, msg }) => {
                    if (ok) {
                        window.location.reload();
                    } else {
                        alert(typeof msg === 'string' ? msg : 'Unable to update website link.');
                    }
                })
                .catch(() => alert('Unable to update website link.'));
        }

        /* ---------- Delete Department ---------- */
        function deleteDepartment(id) {
            if (!confirm('Department and its related sub-departments will also be removed. Continue?')) return;

            fetch("{{ url('/deletedepartment') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ id })
            })
                .then(r => r.json())
                .then(resp => {
                    if (resp.status === 200) {
                        window.location.reload();
                    } else {
                        alert(resp.message || 'Unable to delete department.');
                    }
                })
                .catch(() => alert('Unable to delete department.'));
        }
    </script>
@endpush
