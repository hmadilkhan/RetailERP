<div>
<style>
.markdown table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.markdown th, .markdown td {
    border: 1px solid #ddd;
    padding: 8px;
}

.markdown th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.markdown h3 {
    font-size: 16px;
    margin-top: 15px;
}

.markdown ul {
    list-style-type: disc;
    margin-left: 20px;
}
</style>
<div class="container my-4" style="max-width: 900px; display: flex; flex-direction: column; height: 90vh;">
    <!-- Filters -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <label class="form-label">Branch</label>
            <select wire:model.live="branchId" class="form-select">
                <option value="all">All</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
                @endforeach
            </select>
            {{-- <input type="number" wire:model.live="branchId" placeholder="All" class="form-control"> --}}
        </div>
        <div class="col-md-4">
            <label class="form-label">Date Range</label>
            <select wire:model.live="dateRange" class="form-select">
                <option value="7d">Last 7 days</option>
                <option value="30d">Last 30 days</option>
                <option value="90d">Last 90 days</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Top N Products</label>
            <input type="number" min="10" max="200" wire:model.live="topN" class="form-control">
        </div>
    </div>

    <!-- Chat Window -->
    <div class="border rounded bg-white shadow-sm p-3 flex-grow-1 mb-2" style="overflow-y: auto;">
        @foreach ($messages as $m)
            <div class="d-flex {{ $m['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                <div class="p-2 px-3 rounded markdown"
                    style="max-width: 85%;
             {{ $m['role'] === 'user' ? 'background-color:#212529;color:white;' : 'background-color:#f8f9fa;' }}">
                    {!! $m['content'] !!}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Sticky Footer Input -->
    <div class="sticky-bottom bg-light border-top p-3">
        <form wire:submit.prevent="send" class="d-flex gap-2">
            <input type="text" wire:model="input" autocomplete="off"
                placeholder="Ask about forecast… (e.g., Which items to reorder for next week?)"
                class="form-control flex-grow-1">
            <button class="btn btn-dark px-4" type="submit">Send</button>
        </form>
    </div>
</div>
</div>