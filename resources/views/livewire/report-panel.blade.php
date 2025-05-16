<div class="row">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Report Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    @foreach ($reportHeads as $head)
                        <li class="nav-item dropdown {{ $activeReport === $head['name'] ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarScrollingDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $head['name'] }}
                            </a>
                            <ul class="dropdown-menu p-2" aria-labelledby="navbarScrollingDropdown">
                                @foreach ($head['reports'] as $report)
                                    <li wire:click="selectReport('{{ $report['key'] }}')"><a class="dropdown-item"
                                            href="#">{{ $report['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>

            </div>
        </div>
    </nav>
    <!-- Dynamic content -->
    <div class="card mt-2">
        <div class="card-body">
            <div class="col-md-12 ">
                @if ($activeReport)
                    @livewire("reports.$activeReport", key($activeReport))
                @else
                    <div class="alert alert-info">Please select a report.</div>
                @endif
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            // Define the initialization function in the global scope
            function initializeSelect2() {
                // Check if Select2 is already initialized
                if ($('#customerId').hasClass('select2-hidden-accessible')) {
                    return; // Exit if already initialized
                }
                let branch = $('#branch').val();
                if (branch == '') {
                    branch = 'all';
                }
                // Initialize Select2
                $('#customerId').select2({
                    ajax: {
                        url: "{{ route('search-customer-by-names') }}",
                        type: 'GET',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                branch: branch,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data, params) {
                            if (data === 0) {
                                return {
                                    results: []
                                };
                            }

                            if (data && data.items) {
                                return {
                                    results: $.map(data.items, function(item) {
                                        return {
                                            id: item.id,
                                            text: item.name + " | " + item.branch_name,
                                            description: item.mobile || 'No description available'
                                        };
                                    })
                                };
                            }

                            return {
                                results: []
                            };
                        },
                        error: function(xhr, status, error) {
                            // Handle error silently
                        },
                        cache: true
                    },
                    placeholder: 'Type to search customers...',
                    minimumInputLength: 1,
                    width: '100%',
                    templateResult: formatProduct,
                    templateSelection: formatProductSelection
                });

                // Format the product display in dropdown
                function formatProduct(product) {
                    if (!product.id) return product.text;
                    return $('<span><strong>' + product.text + '</strong><br><small class="text-muted">' +
                        (product.description || 'No description available') + '</small></span>');
                }

                // Format the selected product
                function formatProductSelection(product) {
                    if (!product.id) return product.text;
                    return product.text;
                }

                // Handle selection
                $('#customerId').on('select2:select', function(e) {
                    let selectedData = e.params.data;
                    // Add to inventory
                    @this.addInventory(selectedData.id);

                    // Clear selection after a short delay
                    setTimeout(() => {
                        $(this).val('').trigger('change');
                    }, 100);
                });
            }

            // Initialize when document is ready
            $(document).ready(function() {
                window.initializeSelect2();
                console.log('ready');
            });

            // Listen for initialization event from parent
            Livewire.on('initializeSelect2', () => {
                window.initializeSelect2();
            });

            // Handle Livewire updates
            Livewire.hook('morph.updating', () => {
                if ($('#customerId').hasClass('select2-hidden-accessible')) {
                    $('#customerId').select2('destroy');
                }
            });

            Livewire.hook('morph.updated', () => {
                // Only reinitialize if the element exists and is visible
                if ($('#customerId').length && $('#customerId').is(':visible')) {
                    window.initializeSelect2();
                }
            });
        </script>
    @endpush
</div>
