{{-- <div>
    <pre>{{ json_encode($options) }}</pre>
    <select id="select2-dropdown" class="select2" wire:model="selectedOption" style="width: 100%;">
        <option value="">Select an option</option>
        
        @if (!empty($options) && is_array($options))
            @foreach ($options as $option)
                <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
            @endforeach
        @endif
    </select>
</div> --}}

{{-- @push('scripts')
    <script>
         function initializeChildSelect2() {
                console.log("Initiliazing");
            
                const select2Element = $('#select2-dropdown');

                // Destroy any previous Select2 instance
                if (select2Element.hasClass("select2-hidden-accessible")) {
                    select2Element.select2('destroy');
                }

                // Reinitialize Select2
                select2Element.select2({
                    placeholder: 'Search and select',
                    minimumInputLength: 1,
                    ajax: {
                        delay: 250,
                        transport: function(params, success, failure) {
                            Livewire.dispatch('fetchSelect2Options', params.data
                            .q); // Emit query to Livewire
                            success
                        (); // Resolve the transport (no real fetch here since Livewire handles it)
                        }
                    }
                });

                // Sync value with Livewire
                select2Element.on('change', function() {
                    Livewire.dispatch('select2Updated', $(this).val());
                });
            }
            initializeChildSelect2();
            $('#select2-dropdown').change(function(){
                console.log("works");
                
            })
        document.addEventListener('livewire:load', function() {
            console.log("works");
            
            

            // Reinitialize Select2 after Livewire updates the DOM
            Livewire.hook('message.processed', (message, component) => {
                initializeChildSelect2();
            });

            initializeChildSelect2(); // Initial load
        });
    </script>
@endpush --}}
