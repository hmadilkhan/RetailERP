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

/* New modern styling */
.chat-wrapper {
    max-width: 1000px;
    min-height: 90vh;
    display: flex;
    flex-direction: column;
}

.glass-card {
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.gradient-header {
    background: linear-gradient(135deg, #111827, #1f2937, #0ea5e9);
    color: #fff;
    border-radius: .75rem;
}

.message-bubble {
    max-width: 85%;
    border-radius: 12px;
}

.message-user {
    background: #111827;
    color: #fff;
}

.message-assistant {
    background: #f8f9fa;
}

.message-meta {
    font-size: 12px;
    opacity: .7;
}

.typing-dot {
    width: 6px;
    height: 6px;
    margin: 0 2px;
    display: inline-block;
    background: #0ea5e9;
    border-radius: 50%;
    animation: bounce 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes bounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

.sticky-input {
    position: sticky;
    bottom: 0;
    z-index: 5;
    background: #ffffff;
}

.send-btn:disabled {
    opacity: 0.7;
}
</style>
<div class="container my-4 chat-wrapper">
    <!-- Header -->
    <div class="gradient-header p-4 mb-3 d-flex align-items-center justify-content-between">
        <div>
            <div class="h5 mb-1">Inventory Forecast Assistant</div>
            <div class="small text-white-50">Ask for reorder suggestions and demand insights</div>
        </div>
        <div class="d-none d-md-flex align-items-center gap-2">
            <span class="badge bg-light text-dark">AI</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Branch</label>
            <select wire:model.live="branchId" class="form-select">
                <option value="all">All</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
                @endforeach
            </select>
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
    <div class="glass-card border rounded p-3 flex-grow-1 mb-2" style="overflow-y: auto;">
        @foreach ($messages as $m)
            <div class="d-flex {{ $m['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                <div class="p-2 px-3 message-bubble markdown {{ $m['role'] === 'user' ? 'message-user' : 'message-assistant' }}">
                    {!! $m['content'] !!}
                </div>
            </div>
        @endforeach

        <!-- Live typing / loading indicator -->
        <div wire:loading wire:target="send" class="d-flex justify-content-start my-2">
            <div class="p-2 px-3 message-bubble message-assistant">
                <div class="message-meta mb-1">OpenAI is thinking…</div>
                <div class="d-flex align-items-center">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="ms-2 small text-muted">Fetching sales summary, stock levels, and generating forecast…</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Footer Input -->
    <div class="sticky-input border-top p-3 bg-white">
        <form wire:submit.prevent="send" class="d-flex gap-2">
            <input type="text" wire:model="input" autocomplete="off"
                placeholder="Ask about forecast… (e.g., Which items to reorder for next week?)"
                class="form-control flex-grow-1">
            <button class="btn btn-dark px-4 send-btn" type="submit" wire:loading.attr="disabled" wire:target="send">
                <span wire:loading.remove wire:target="send">Send</span>
                <span wire:loading wire:target="send">Sending…</span>
            </button>
        </form>
    </div>
</div>
</div>