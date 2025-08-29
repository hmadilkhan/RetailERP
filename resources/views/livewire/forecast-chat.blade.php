<div class="forecast-chat-container">
<style>
/* Modern Chat UI Styles */
.forecast-chat-container {
    min-height: 100vh;
    background: white; /*linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
    padding: 20px 0;
    position: relative;
    overflow-x: hidden;
}

.forecast-chat-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.chat-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
    position: relative;
    z-index: 1;
}

.gradient-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.gradient-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.header-content {
    position: relative;
    z-index: 2;
}

.header-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.header-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 300;
}

.ai-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 0.9rem;
}

.filters-section {
    background: rgba(248, 250, 252, 0.8);
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.filter-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.filter-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.filter-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.filter-input {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    background: white;
}

.filter-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.chat-window {
    height: 500px;
    overflow-y: auto;
    padding: 1.5rem;
    background: white;
    position: relative;
}

.chat-window::-webkit-scrollbar {
    width: 6px;
}

.chat-window::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.chat-window::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.chat-window::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.message-bubble {
    max-width: 80%;
    border-radius: 18px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    position: relative;
    animation: messageSlide 0.3s ease-out;
}

@keyframes messageSlide {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-user {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 6px;
}

.message-assistant {
    background: #f8fafc;
    color: #1f2937;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 6px;
}

.message-meta {
    font-size: 0.75rem;
    opacity: 0.7;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: #667eea;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
.typing-dot:nth-child(3) { animation-delay: 0s; }

@keyframes typingBounce {
    0%, 80%, 100% { 
        transform: scale(0);
        opacity: 0.5;
    }
    40% { 
        transform: scale(1);
        opacity: 1;
    }
}

.input-section {
    background: white;
    padding: 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
}

.input-container {
    display: flex;
    gap: 1rem;
    align-items: center;
    background: #f8fafc;
    border-radius: 16px;
    padding: 0.5rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.input-container:focus-within {
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.chat-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    outline: none;
    color: #1f2937;
}

.chat-input::placeholder {
    color: #9ca3af;
}

.send-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 100px;
    justify-content: center;
}

.send-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
}

.send-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.send-button:disabled:hover {
    transform: none;
    box-shadow: none;
}

/* Markdown table styling */
.markdown table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.markdown th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem;
    font-weight: 600;
    text-align: left;
}

.markdown td {
    padding: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
    background: white;
}

.markdown tr:nth-child(even) td {
    background: #f9fafb;
}

.markdown h3 {
    color: #1f2937;
    font-size: 1.25rem;
    font-weight: 600;
    margin: 1.5rem 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}

.markdown ul {
    list-style: none;
    padding-left: 0;
}

.markdown ul li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
    padding-left: 1.5rem;
}

.markdown ul li::before {
    content: '•';
    color: #667eea;
    font-weight: bold;
    position: absolute;
    left: 0;
}

/* Responsive design */
@media (max-width: 768px) {
    .forecast-chat-container {
        padding: 10px;
    }
    
    .chat-wrapper {
        border-radius: 16px;
    }
    
    .header-title {
        font-size: 2rem;
    }
    
    .message-bubble {
        max-width: 90%;
    }
    
    .input-container {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .send-button {
        width: 100%;
    }
}

/* Loading animation for the entire page */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.loading-overlay.active {
    opacity: 1;
    visibility: visible;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f4f6;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="chat-wrapper">
    <!-- Header -->
    <div class="gradient-header">
        <div class="header-content d-flex align-items-center justify-content-between">
            <div>
                <div class="header-title">🤖 AI Forecast Assistant</div>
                <div class="header-subtitle">Get intelligent reorder suggestions and demand insights</div>
            </div>
            <div class="ai-badge">
                <i class="mdi mdi-robot me-2"></i>AI Powered
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="filter-card">
                    <label class="filter-label">
                        <i class="mdi mdi-store me-2"></i>Branch
                    </label>
                    <select wire:model.live="branchId" class="form-select filter-input">
                        <option value="all">All Branches</option>
                        @foreach ($branches as $b)
                            <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-card">
                    <label class="filter-label">
                        <i class="mdi mdi-calendar-range me-2"></i>Date Range
                    </label>
                    <select wire:model.live="dateRange" class="form-select filter-input">
                        <option value="7d">Last 7 days</option>
                        <option value="30d">Last 30 days</option>
                        <option value="90d">Last 90 days</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="filter-card">
                    <label class="filter-label">
                        <i class="mdi mdi-chart-line me-2"></i>Top Products
                    </label>
                    <input type="number" min="10" max="200" wire:model.live="topN" 
                           class="form-control filter-input" placeholder="50">
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Window -->
    <div class="chat-window" id="chatWindow">
        @if(empty($messages))
            <div class="text-center text-muted py-5">
                <i class="mdi mdi-chat-outline" style="font-size: 3rem; color: #cbd5e1;"></i>
                <div class="mt-3">
                    <h5>Start a conversation</h5>
                    <p>Ask me about inventory forecasts, reorder suggestions, or demand insights!</p>
                </div>
            </div>
        @else
            @foreach ($messages as $m)
                <div class="d-flex {{ $m['role'] === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="message-bubble markdown {{ $m['role'] === 'user' ? 'message-user' : 'message-assistant' }}">
                        {!! $m['content'] !!}
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Live typing / loading indicator -->
        @if($isProcessing)
        <div class="d-flex justify-content-start">
            <div class="message-bubble message-assistant">
                <div class="message-meta">AI is analyzing your data...</div>
                <div class="typing-indicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="ms-2 small text-muted">Fetching sales data and generating insights...</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Input Section -->
    <div class="input-section">
        <form wire:submit.prevent="send">
            <div class="input-container">
                <input type="text" wire:model="input" autocomplete="off"
                    placeholder="Ask about forecast… (e.g., Which items should I reorder for next week?)"
                    class="chat-input"
                    {{ $isProcessing ? 'disabled' : '' }}>
                <button class="send-button" type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="send"
                    {{ $isProcessing ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="send">
                        <i class="mdi mdi-send me-1"></i>Send
                    </span>
                    <span wire:loading wire:target="send">
                        <div class="loading-spinner" style="width: 16px; height: 16px; border-width: 2px;"></div>
                        Sending...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
// Auto-scroll to bottom when new messages arrive
document.addEventListener('livewire:updated', function () {
    const chatWindow = document.getElementById('chatWindow');
    if (chatWindow) {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }
});

// Show loading overlay when processing starts
document.addEventListener('livewire:loading', function () {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('active');
    }
});

// Hide loading overlay when processing ends
document.addEventListener('livewire:loaded', function () {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.remove('active');
    }
});

// Smooth scroll to bottom on page load
window.addEventListener('load', function () {
    const chatWindow = document.getElementById('chatWindow');
    if (chatWindow) {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }
});
</script>
</div>