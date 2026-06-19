@extends('layouts.master-tailwind')

@section('title', 'Departments')
@section('page_title', 'Create Department')
@section('page_subtitle', 'Add a new catalogue department with website visibility, sections, priority, and media.')

@php
    $inputClass = 'mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
    $textareaClass = 'mt-2 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp';
    $labelClass = 'text-xs font-bold uppercase tracking-[0.14em] text-erp-mute';
    $fileClass = 'mt-2 block w-full rounded-lg border border-erp-line bg-white text-sm text-erp-text file:mr-4 file:border-0 file:bg-erp file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-erp focus:ring-erp';
@endphp

@section('content')
    <form method="POST" action="{{ route('invent_dept.store') }}" enctype="multipart/form-data" id="createDepartmentForm">
        @csrf

        <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-erp-line bg-white px-5 py-4 shadow-sm">
            <div>
                <h2 class="text-base font-bold text-erp-ink">New Department</h2>
                <p class="mt-1 text-sm text-erp-mute">Fields marked with an asterisk are required.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('invent_dept.index') }}" class="rounded-lg border border-erp-line px-4 py-2 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Back</a>
                <button type="submit" class="rounded-lg border border-erp bg-erp px-5 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">Create Department</button>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h3 class="text-base font-bold text-erp-ink">Department Details</h3>
                        <p class="mt-1 text-sm text-erp-mute">Core identification for this department.</p>
                    </div>
                    <div class="grid gap-4 p-5 sm:grid-cols-2">
                        <label class="block">
                            <span class="{{ $labelClass }}">Department Code</span>
                            <input type="text" name="department_code" value="{{ old('department_code') }}" placeholder="Department code" class="{{ $inputClass }}">
                            @error('department_code')
                                <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                            @enderror
                        </label>
                        <label class="block">
                            <span class="{{ $labelClass }}">Department Name <span class="text-rose-500">*</span></span>
                            <input type="text" name="department_name" value="{{ old('department_name') }}" placeholder="Department name" required class="{{ $inputClass }}">
                            @error('department_name')
                                <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                </section>

                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h3 class="text-base font-bold text-erp-ink">Website Visibility</h3>
                        <p class="mt-1 text-sm text-erp-mute">Control how this department appears on the storefront.</p>
                    </div>
                    <div class="space-y-4 p-5">
                        <label class="inline-flex items-center gap-2 text-sm font-bold text-erp-text">
                            <input type="checkbox" name="showWebsite" id="showWebsite" value="1" {{ old('showWebsite') ? 'checked' : '' }} class="rounded border-erp-line text-erp focus:ring-erp">
                            Show on Website
                        </label>

                        <div id="websiteFields" class="hidden space-y-4 rounded-lg border border-erp-line bg-slate-50 p-4">
                            <label class="block">
                                <span class="{{ $labelClass }}">Website Department Name</span>
                                <input type="text" name="website_department_name" value="{{ old('website_department_name') }}" placeholder="Website department name" class="{{ $inputClass }}">
                            </label>

                            <div>
                                <span class="{{ $labelClass }}">Sections</span>
                                @php $oldSections = (array) old('sections', []); @endphp
                                <div class="mt-2 grid max-h-40 grid-cols-2 gap-2 overflow-y-auto rounded-lg border border-erp-line bg-white p-3 sm:grid-cols-3">
                                    @forelse ($sections as $val)
                                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-erp-text">
                                            <input type="checkbox" name="sections[]" value="{{ $val->id }}" {{ in_array($val->id, $oldSections) ? 'checked' : '' }} class="rounded border-erp-line text-erp focus:ring-erp">
                                            {{ $val->name }}
                                        </label>
                                    @empty
                                        <span class="text-xs text-erp-mute">No sections available.</span>
                                    @endforelse
                                </div>
                            </div>

                            <label class="block">
                                <span class="{{ $labelClass }}">Priority</span>
                                <input type="number" name="priority" min="0" value="{{ old('priority') }}" class="{{ $inputClass }}">
                            </label>

                            <label class="block">
                                <span class="{{ $labelClass }}">Meta Title</span>
                                <input type="text" name="metatitle" value="{{ old('metatitle') }}" placeholder="Meta title" class="{{ $inputClass }}">
                            </label>

                            <label class="block">
                                <span class="{{ $labelClass }}">Meta Description</span>
                                <textarea name="metadescript" rows="4" placeholder="Meta description" class="{{ $textareaClass }}">{{ old('metadescript') }}</textarea>
                            </label>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-lg border border-erp-line bg-white shadow-sm">
                    <div class="border-b border-erp-line px-5 py-4">
                        <h3 class="text-base font-bold text-erp-ink">Media</h3>
                        <p class="mt-1 text-sm text-erp-mute">Images used on listings and the storefront.</p>
                    </div>
                    <div class="space-y-4 p-5">
                        <label class="block">
                            <span class="{{ $labelClass }}">Department Image</span>
                            <img id="departmentImagePreview" src="{{ asset('storage/images/no-image.png') }}" class="mt-2 h-24 w-24 rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" name="department_image" id="department_image" accept="image/*" class="{{ $fileClass }}">
                            @error('department_image')
                                <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="{{ $labelClass }}">Desktop Banner</span>
                            <img id="bannerImagePreview" src="{{ asset('storage/images/no-image.png') }}" class="mt-2 h-24 w-full rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" name="banner_image" id="banner_image" accept="image/*" class="{{ $fileClass }}">
                            @error('banner_image')
                                <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="{{ $labelClass }}">Mobile Banner</span>
                            <img id="mobileBannerPreview" src="{{ asset('storage/images/no-image.png') }}" class="mt-2 h-24 w-full rounded-lg object-cover ring-1 ring-slate-200">
                            <input type="file" name="mobile_banner" id="mobile_banner" accept="image/*" class="{{ $fileClass }}">
                            @error('mobile_banner')
                                <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>
                </section>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const showWebsiteCheckbox = document.getElementById('showWebsite');
        const websiteFields = document.getElementById('websiteFields');

        function syncWebsiteFields() {
            websiteFields.classList.toggle('hidden', !showWebsiteCheckbox.checked);
        }

        showWebsiteCheckbox.addEventListener('change', syncWebsiteFields);
        syncWebsiteFields();

        function previewImage(inputId, previewId) {
            const input = document.getElementById(inputId);
            input.addEventListener('change', function () {
                if (this.files[0]) {
                    document.getElementById(previewId).src = URL.createObjectURL(this.files[0]);
                }
            });
        }

        previewImage('department_image', 'departmentImagePreview');
        previewImage('banner_image', 'bannerImagePreview');
        previewImage('mobile_banner', 'mobileBannerPreview');
    </script>
@endpush
