@extends('layouts.master-tailwind')

@section('title', 'Rooms')
@section('page_title', 'Rooms')
@section('page_subtitle', 'Create and manage hotel or dining rooms mapped to branch floors.')

@section('content')
    @php
        $roomCollection = collect($rooms ?? []);
        $floorCollection = collect($floors ?? []);
    @endphp

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Total Rooms</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($roomCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Active rooms in current branch</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Floors</div>
                <div class="mt-4 text-3xl font-black text-erp-ink">{{ number_format($floorCollection->count()) }}</div>
                <p class="mt-2 text-sm text-erp-mute">Available floor assignments</p>
            </div>
            <div class="rounded-lg border border-erp-line bg-white p-5 shadow-sm sm:col-span-2">
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Current Mode</div>
                <div id="formModeLabel" class="mt-4 text-xl font-black text-erp-ink">Create Room</div>
                <p class="mt-2 text-sm text-erp-mute">Edit mode is activated from a room row.</p>
            </div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="border-b border-erp-line px-5 py-4">
                <h2 class="text-base font-bold text-erp-ink">Room Details</h2>
                <p class="mt-1 text-sm text-erp-mute">Assign each room to a floor.</p>
            </div>
            <form id="roomForm" class="grid gap-4 p-5 md:grid-cols-12">
                @csrf
                <input type="hidden" name="room_id" id="room_id">
                <label class="block md:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Floor</span>
                    <select name="floor_id" id="floor_id" data-placeholder="Select Floor" class="v2-select2 mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                        <option value="">Select Floor</option>
                        @foreach($floorCollection as $floor)
                            <option value="{{ $floor->floor_id }}">{{ $floor->floor_name }}</option>
                        @endforeach
                    </select>
                    <span id="floor_alert" class="mt-1 hidden text-xs font-semibold text-rose-600"></span>
                </label>
                <label class="block md:col-span-3">
                    <span class="text-xs font-bold uppercase tracking-[0.16em] text-erp-mute">Room Name</span>
                    <input type="text" name="room_no" id="room_no" class="mt-2 h-10 w-full rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp">
                    <span id="room_no_alert" class="mt-1 hidden text-xs font-semibold text-rose-600"></span>
                </label>
                <div class="flex items-end gap-2 md:col-span-4">
                    <button type="button" id="btn_save" class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">Save</button>
                    <button type="button" id="btn_update" class="hidden h-10 rounded-lg bg-sky-600 px-4 text-sm font-bold text-white transition hover:bg-sky-700">Update</button>
                    <button type="button" id="btn_clear" class="h-10 rounded-lg border border-erp-line px-4 text-sm font-bold text-erp-text transition hover:border-erp hover:text-erp-dark">Clear</button>
                </div>
            </form>
            <div id="roomStatus" class="border-t border-erp-line px-5 py-3 text-sm font-semibold text-erp-mute"></div>
        </section>

        <section class="rounded-lg border border-erp-line bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-erp-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-bold text-erp-ink">Room List</h2>
                    <p class="mt-1 text-sm text-erp-mute">Review, edit, or delete room records.</p>
                </div>
                <input type="search" id="roomFilter" placeholder="Filter rooms..." class="h-10 rounded-lg border-erp-line text-sm shadow-sm focus:border-erp focus:ring-erp sm:w-80">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.14em] text-erp-mute">
                        <tr>
                            <th class="px-5 py-3 text-left font-bold">Floor</th>
                            <th class="px-5 py-3 text-left font-bold">Room</th>
                            <th class="px-5 py-3 text-right font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="roomRows" class="divide-y divide-slate-100">
                        @forelse($roomCollection as $room)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-semibold text-erp-text">{{ $room->floors->floor_name ?? '-' }}</td>
                                <td class="px-5 py-4 font-bold text-erp-ink">{{ $room->room_no }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button type="button" data-id="{{ $room->id }}" data-floor-id="{{ $room->floor_id }}" data-room-no="{{ e($room->room_no) }}" class="edit-room rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-100">Edit</button>
                                        <button type="button" data-id="{{ $room->id }}" class="delete-room rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-12 text-center text-erp-mute">No rooms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const roomForm = document.getElementById('roomForm');
        const roomStatus = document.getElementById('roomStatus');

        function setRoomStatus(message, success = true) {
            roomStatus.textContent = message;
            roomStatus.className = 'border-t border-erp-line px-5 py-3 text-sm font-semibold ' + (success ? 'text-emerald-700' : 'text-rose-700');
        }

        function resetRoomForm() {
            roomForm.reset();
            document.getElementById('room_id').value = '';
            if (window.jQuery) {
                jQuery('#floor_id').val('').trigger('change.select2');
            }
            document.getElementById('btn_save').classList.remove('hidden');
            document.getElementById('btn_update').classList.add('hidden');
            document.getElementById('formModeLabel').textContent = 'Create Room';
            document.getElementById('room_no_alert').classList.add('hidden');
            document.getElementById('floor_alert').classList.add('hidden');
        }

        function validateRoomForm() {
            let valid = true;
            document.getElementById('floor_alert').classList.add('hidden');
            document.getElementById('room_no_alert').classList.add('hidden');

            if (!document.getElementById('floor_id').value) {
                document.getElementById('floor_alert').textContent = 'Floor is required.';
                document.getElementById('floor_alert').classList.remove('hidden');
                valid = false;
            }

            if (!document.getElementById('room_no').value.trim()) {
                document.getElementById('room_no_alert').textContent = 'Room name is required.';
                document.getElementById('room_no_alert').classList.remove('hidden');
                valid = false;
            }

            return valid;
        }

        function submitRoom(url) {
            if (!validateRoomForm()) {
                return;
            }

            fetch(url, { method: 'POST', body: new FormData(roomForm) })
                .then(response => response.json())
                .then(function (result) {
                    const state = Number(result.state);
                    if (state === 0 || state === 1 && url.includes('update-rooms')) {
                        setRoomStatus('Saved successfully. Refreshing...');
                        window.setTimeout(() => window.location = "{{ url('/rooms') }}", 350);
                    } else {
                        setRoomStatus(result.msg || 'Unable to save room.', false);
                    }
                })
                .catch(() => setRoomStatus('Unable to save room.', false));
        }

        document.getElementById('btn_save').addEventListener('click', () => submitRoom("{{ url('create-rooms') }}"));
        document.getElementById('btn_update').addEventListener('click', () => submitRoom("{{ url('update-rooms') }}"));
        document.getElementById('btn_clear').addEventListener('click', resetRoomForm);

        document.querySelectorAll('.edit-room').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('room_id').value = this.dataset.id;
                document.getElementById('floor_id').value = this.dataset.floorId;
                if (window.jQuery) {
                    jQuery('#floor_id').val(this.dataset.floorId).trigger('change.select2');
                }
                document.getElementById('room_no').value = this.dataset.roomNo;
                document.getElementById('btn_save').classList.add('hidden');
                document.getElementById('btn_update').classList.remove('hidden');
                document.getElementById('formModeLabel').textContent = 'Edit Room';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });

        document.querySelectorAll('.delete-room').forEach(function (button) {
            button.addEventListener('click', function () {
                if (!confirm('Delete this room?')) {
                    return;
                }

                const data = new FormData();
                data.append('_token', "{{ csrf_token() }}");
                data.append('id', this.dataset.id);

                fetch("{{ url('/delete-rooms') }}", { method: 'POST', body: data })
                    .then(response => response.text())
                    .then(function (response) {
                        if (response.trim() === '1') {
                            window.location = "{{ url('/rooms') }}";
                        } else {
                            setRoomStatus('Unable to delete room.', false);
                        }
                    })
                    .catch(() => setRoomStatus('Unable to delete room.', false));
            });
        });

        document.getElementById('roomFilter').addEventListener('input', function () {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#roomRows tr').forEach(row => row.hidden = !row.textContent.toLowerCase().includes(term));
        });
    </script>
@endpush
