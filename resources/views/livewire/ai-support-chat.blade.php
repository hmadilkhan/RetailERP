<div class="p-4 bg-gray-100 rounded shadow-md h-[500px] overflow-y-auto">
    <div class="space-y-2">
        @foreach($messages as $msg)
            <div class="@if($msg['sender'] === 'user') text-right @else text-left @endif">
                <span class="inline-block p-2 rounded-lg @if($msg['sender'] === 'user') bg-blue-500 text-white @else bg-gray-300 @endif">
                    {{ $msg['text'] }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="mt-4 flex">
        <input type="text" class="flex-1 p-2 border rounded" wire:model.defer="userInput" wire:keydown.enter="sendMessage" placeholder="Type your message...">
        <button wire:click="sendMessage" class="ml-2 px-4 py-2 bg-green-600 text-white rounded">Send</button>
    </div>
</div>
