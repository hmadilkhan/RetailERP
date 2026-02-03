<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">{{ $editId ? 'Edit' : 'Add' }} Booking Slot</h5>
        </div>
        <div class="card-block">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Mode</label>
                            <select wire:model="mode" class="form-control" required>
                                <option value="">Select Mode</option>
                                <option value="normal">Normal</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            @error('mode') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Type</label>
                            <select wire:model="type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="pickup">Pickup</option>
                                <option value="drop">Drop</option>
                            </select>
                            @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" wire:model="start_time" class="form-control" required>
                            @error('start_time') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" wire:model="end_time" class="form-control" required>
                            @error('end_time') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">{{ $editId ? 'Update' : 'Add' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-header-text">Booking Slots List</h5>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mode</th>
                            <th>Type</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($slots as $slot)
                            <tr>
                                <td>{{ $slot->id }}</td>
                                <td><span class="badge badge-{{ $slot->mode == 'urgent' ? 'danger' : 'info' }}">{{ ucfirst($slot->mode) }}</span></td>
                                <td><span class="badge badge-{{ $slot->type == 'pickup' ? 'success' : 'warning' }}">{{ ucfirst($slot->type) }}</span></td>
                                <td>{{ date('h:i A', strtotime($slot->start_time)) }}</td>
                                <td>{{ date('h:i A', strtotime($slot->end_time)) }}</td>
                                <td>{{ date('Y-m-d H:i', strtotime($slot->created_at)) }}</td>
                                <td>
                                    <button wire:click="edit({{ $slot->id }})" class="btn btn-sm btn-warning">
                                        <i class="icofont icofont-edit"></i> Edit
                                    </button>
                                    <button wire:click="delete({{ $slot->id }})" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="icofont icofont-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No slots found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
