<div>
    <select id="select2-dropdown" class="select2" wire:model="selectedOption" style="width: 100%;">
        <option value="">Select an option</option>
        @foreach ($options as $option)
            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
        @endforeach
    </select>
</div>

@script
    <script>
        function initializeChildSelect2() {
            console.log("workds");

            const select2Element = $('#select2-dropdown');

            // Destroy any previous Select2 instance
            // select2Element.select2('destroy');

            // Reinitialize Select2
            select2Element.select2({
                placeholder: 'Search and select',
                minimumInputLength: 1,
                ajax: {
                    delay: 250,
                    transport: function(params, success) {
                        Livewire.dispatch('updatedSearch', params.data.q); // Emit search query to Livewire
                        success();
                    }
                }
            });

            // Sync value with Livewire
            select2Element.on('change', function() {
                Livewire.dispatch('select2Updated', $(this).val());
            });
        }

        // Reinitialize Select2 after Livewire updates the DOM
        document.addEventListener('livewire:load', initializeChildSelect2);
        Livewire.hook('message.processed', (message, component) => {
            initializeChildSelect2();
        });
        Livewire.on('childRendered', () => {
            initializeChildSelect2(); // Initialize Select2 after parent re-renders the child
        });
    </script>
@endscript
