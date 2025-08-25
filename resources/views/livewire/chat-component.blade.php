<div class="max-w-6xl mx-auto h-screen flex">
    <aside class="w-72 border-r bg-white flex flex-col">
        <div class="p-4 border-b flex items-center justify-between">
            <button wire:click="createNewChat" class="px-3 py-2 bg-blue-600 text-white rounded">
                New Chat
            </button>
        </div>
        <div class="flex-1 overflow-y-auto">
            @foreach($chats as $c)
                <button wire:click="selectChat({{ $c->id }})" 
                        class="w-full text-left block px-4 py-3 hover:bg-gray-100 {{ $currentChatId == $c->id ? 'bg-blue-50' : '' }}">
                    <div class="text-sm text-gray-900 truncate">{{ $c->title ?? 'Untitled chat' }}</div>
                    <div class="text-xs text-gray-500">{{ $c->created_at->diffForHumans() }}</div>
                </button>
            @endforeach
        </div>
    </aside>

    <main class="flex-1 flex flex-col">
        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages">
            @if($currentChat)
                @foreach($currentChat->messages as $m)
                    <div class="flex {{ $m->role==='user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-3xl px-4 py-3 rounded-lg {{ $m->role==='user' ? 'bg-blue-600 text-white' : 'bg-white border text-gray-900' }}">
                            @if($m->role==='assistant' && $m->error)
                                <div class="text-red-600 font-medium">{{ $m->error }}</div>
                                @if($m->sql)
                                    <div class="mt-2"><span class="text-xs text-gray-500">SQL (failed):</span>
                                        <pre class="mt-1 p-2 bg-gray-100 rounded text-xs overflow-x-auto"><code>{{ $m->sql }}</code></pre>
                                    </div>
                                @endif
                            @elseif($m->role==='assistant' && $m->result)
                                @if($m->sql)
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="text-xs text-gray-500">SQL:</div>
                                        <pre class="mt-1 p-2 bg-gray-100 rounded text-xs overflow-x-auto flex-1" id="sql-{{ $m->id }}"><code>{{ $m->sql }}</code></pre>
                                        <button class="px-2 py-1 text-xs border rounded" onclick="copyToClipboard('#sql-{{ $m->id }}')">Copy</button>
                                    </div>
                                @endif
                                <div class="mt-2 overflow-x-auto">
                                    @php($rows = $m->result)
                                    @if(count($rows))
                                        <table class="min-w-full text-sm border">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    @foreach(array_keys((array)$rows[0]) as $col)
                                                        <th class="px-3 py-2 border-b text-left text-gray-700">{{ $col }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($rows as $r)
                                                    <tr class="odd:bg-white even:bg-gray-50">
                                                        @foreach((array)$r as $v)
                                                            <td class="px-3 py-2 border-b align-top">{{ is_scalar($v) ? $v : json_encode($v) }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-sm text-gray-500">No rows.</div>
                                    @endif
                                </div>
                            @else
                                <div class="whitespace-pre-wrap">{{ $m->content }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-gray-500">Create a new chat to get started.</div>
            @endif
        </div>

        @if($currentChat)
            <div class="border-t bg-white p-4 sticky bottom-0">
                <div class="flex gap-2">
                    <textarea 
                        wire:model="messageContent" 
                        rows="1" 
                        class="flex-1 resize-none border rounded p-3" 
                        placeholder="Ask a question about your data…" 
                        wire:keydown.enter.prevent="sendMessage"
                        {{ $isProcessing ? 'disabled' : '' }}
                    ></textarea>
                    <button 
                        wire:click="sendMessage" 
                        class="px-4 py-2 bg-blue-600 text-white rounded {{ $isProcessing ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $isProcessing ? 'disabled' : '' }}
                    >
                        @if($isProcessing)
                            <span class="inline-block animate-spin mr-2">⏳</span>
                        @endif
                        Send
                    </button>
                </div>
            </div>
        @endif
    </main>
</div>

<script>
    // Listen for Livewire events
    document.addEventListener('livewire:init', () => {
        Livewire.on('scrollToBottom', () => {
            setTimeout(() => {
                const el = document.getElementById('messages');
                if(el) el.scrollTop = el.scrollHeight;
            }, 100);
        });

        Livewire.on('urlChanged', (data) => {
            if (data.url) {
                window.history.pushState({}, '', data.url);
            }
        });
    });

    // Copy function
    function copyToClipboard(selector) {
        const el = document.querySelector(selector);
        if(!el) return;
        navigator.clipboard.writeText(el.innerText);
    }

    // Auto-resize textarea
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.querySelector('textarea');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }
    });
</script> 