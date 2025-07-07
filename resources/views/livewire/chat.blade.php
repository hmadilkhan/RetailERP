@php
    // Optionally, you can use Bootstrap 5 CDN in your main layout if not already included
@endphp
<div class="container-fluid min-vh-100 bg-light p-0">
    <div class="card shadow-lg border-0 rounded-0 w-100 h-100" style="height: 100vh;">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-chat-dots me-2"></i>Global Chat</h5>
        </div>
        <div class="card-body d-flex flex-column p-0" style="overflow: hidden; height: calc(100vh - 72px);">
            <!-- Conversation -->
            <div class="flex-grow-1 w-100 h-100 overflow-auto px-4 py-4 d-flex flex-column" style="background: #f8f9fa;" wire:poll.2s="getMessages">
                @forelse($messages as $msg)
                    <div class="d-flex mb-3 {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="d-flex flex-column align-items-{{ $msg->sender_id == auth()->id() ? 'end' : 'start' }}">
                            <div class="p-3 rounded-4 shadow-sm mb-1 {{ $msg->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}" style="max-width: 600px;">
                                {{ $msg->message }}
                            </div>
                            <small class="text-muted" style="font-size: 0.85em;">
                                {{ $msg->created_at->format('H:i') }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="d-flex justify-content-center align-items-center w-100" style="height: 100%;">
                        <span class="text-muted">No messages yet.</span>
                    </div>
                @endforelse
            </div>
            <!-- Loader -->
            @if($loading)
                <div class="text-center my-2">
                    <div class="spinner-border spinner-border-sm text-primary"></div> Sending...
                </div>
            @endif
            <!-- Input Box at Bottom -->
            <form wire:submit.prevent="sendMessage" class="card-footer bg-white border-top p-3 d-flex align-items-center gap-2 w-100">
                <input type="text" class="form-control rounded-pill px-3" placeholder="Type your message..." wire:model.defer="message" autocomplete="off">
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-1" type="submit" @if($loading) disabled @endif>
                    <i class="bi bi-send"></i> <span>Send</span>
                </button>
            </form>
        </div>
    </div>
</div>
