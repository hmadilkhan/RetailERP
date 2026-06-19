@extends('layouts.master-tailwind')

@section('title', 'Sections')
@section('page_title', isset($id) ? 'Edit Section' : 'Sections')
@section('page_subtitle', 'Manage catalogue sections used to group departments.')

@section('content')
    @php
        $route = route('sections.store');
        if (isset($id)) {
            $route = route('sections.update', $id);
        }
        $sectionName = old('name');
        if (isset($id)) {
            $sectionName = old('name') ? old('name') : $edit->name;
        }
    @endphp

    <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">{{ isset($id) ? 'Edit' : 'Create' }} Section</h2>
                @if (isset($id))
                    <a href="{{ route('sections.index') }}" class="text-sm font-bold text-erp-dark hover:text-erp">Back to list</a>
                @endif
            </div>
            <form method="POST" action="{{ $route }}" class="space-y-4 p-5">
                @csrf
                @if (isset($id))
                    @method('PATCH')
                @endif

                <label class="block">
                    <span class="text-xs font-bold uppercase tracking-[0.14em] text-erp-mute">Section Name</span>
                    <input type="text" name="name" id="name" value="{{ $sectionName }}" placeholder="Section name" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    @error('name')
                        <span class="mt-1 block text-xs font-semibold text-rose-600">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="rounded-lg border border-erp bg-erp px-6 py-2 text-sm font-bold text-white transition hover:bg-erp-dark">{{ isset($id) ? 'Save Changes' : 'Submit' }}</button>
            </form>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Sections List</h2>
                <input type="search" id="sectionSearch" placeholder="Search section..." class="h-10 w-56 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-erp-line text-sm">
                    <thead class="bg-erp-soft">
                        <tr class="text-left text-xs font-bold uppercase tracking-[0.12em] text-erp-mute">
                            <th class="px-5 py-3">Name</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-erp-line">
                        @forelse($lists as $section)
                            <tr class="section-row" data-search="{{ strtolower($section->name) }}">
                                <td class="px-5 py-3 font-semibold text-erp-ink">{{ $section->name }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('sections.edit', $section->id) }}" class="font-bold text-erp-dark hover:text-erp">Edit</a>
                                        <button type="button" onclick="removeSection({{ $section->id }}, '{{ $section->name }}')" class="font-bold text-rose-600 hover:text-rose-700">Delete</button>
                                    </div>
                                    <form action="{{ route('sections.destroy', $section->id) }}" class="hidden" id="removeForm{{ $section->id }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-5 py-6 text-center text-sm text-erp-mute">No sections yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('sectionSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.section-row').forEach(row => {
                row.classList.toggle('hidden', term !== '' && !row.dataset.search.includes(term));
            });
        });

        function removeSection(id, name) {
            if (!confirm('Do you want to delete section ' + name + '?')) return;
            document.getElementById('removeForm' + id).submit();
        }
    </script>
@endpush
