<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Stock Report</h5>
        </div>
        <div class="card-block">
            <form wire:submit="applyFilters">
                <section class="panels-wells">
                    <div class="row">
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> From</label>
                                <input class="form-control" type="text" name="from" id="from"
                                    wire:model="from" placeholder="DD-MM-YYYY" />
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> To</label>
                                <input class="form-control" type="text" name="to" id="to" wire:model="to"
                                    placeholder="DD-MM-YYYY" />
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12" style="">
                            <label class="form-control-label">Select Branch</label>
                            <select id="branch" name="branch" data-placeholder="Select Branch"
                                class="f-right select2">
                                <option selected value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-control-label"></label>
                            <button id="submit-button" type="button" data-placement="bottom"
                                class="btn btn-success  waves-effect waves-light mt-4">Search</button>
                            <button type="button" data-placement="bottom"
                                class="btn btn-warning  waves-effect waves-light mt-4 text-white"
                                wire:click="clear()">Clear</button>

                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-block">
            <div wire:loading.class="d-flex flex-column" wire:loading>
                <div
                    class='position-relative w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>
                    <div class='spinner-border text-dark' role='status'>
                        <span class='visually-hidden'>Loading...</span>
                    </div>
                </div>
            </div>
            <div class="project-table" wire:loading.remove>
                <table class="table table-striped nowrap dt-responsive m-t-10 dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>Product Id</th>
                            <th>Item Code</th>
                            <th>Product Name</th>
                            <th>Opening Date</th>
                            <th>Opening Stock</th>
                            <th>Sales</th>
                            <th>Closing Stock</th>
                            <th>Closing Date</th>
                            {{-- <th>Stock</th> --}}
                            {{-- <th>Narration</th> --}}
                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($stocks))
                            @foreach ($stocks as $stock)
                                @php
                                    // Find corresponding sales for this product_id
                                    $sales = $stocks['sales']->firstWhere('product_id', $stock->product_id);
                                    $salesAmount = $sales ? $sales->sales : 0; // Default to 0 if no sales found
                                    $closing = $stocks['closing']->firstWhere('product_id', $stock->product_id);
                                @endphp
                                <tr>
                                    <td>{{ $stock->product_id }}</td>
                                    <td>{{ $stock->item_code }}</td>
                                    <td>{{ $stock->product_name }}</td>
                                    <td>{{ $stock->opening_date }}</td>
                                    <td>{{ $stock->opening_stock }}</td>
                                    <td>{{ $stock->sales }}</td>
                                    <td>{{ $stock->closing_stock }}</td>
                                    <td>{{ $stock->closing_date }}</td>
                                    {{-- <td>{{ $salesAmount }}</td>
                                    <td>{{ !empty($closing) ? $closing->closing_date : '-' }}</td>
                                    <td>{{ $stock->->closing_stock : '-' }}</td> --}}
                                    {{-- <td>{{ $stock->product_id }}</td>
                                    <td>{{ $stock->opening_date }}</td>
                                    <td>{{ $stock->opening_stock }}</td> --}}
                                    {{-- <td>{{ $stock->sales }}</td>
                                    <td>{{ $stock->closing_stock }}</td>
                                    <td>{{ $stock->closing_date }}</td> --}}

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center">No record found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            {{-- @if (!empty($stocks))
                {{ $stocks->links() }}
            @endif --}}
        </div>
    </div>
</section>
@script
    <script>
        $(document).ready(function() {
            $(".select2").select2();

            $('#from,#to').bootstrapMaterialDatePicker({
                format: 'YYYY-MM-DD',
                time: false,
                clearButton: true,

                icons: {
                    date: "icofont icofont-ui-calendar",
                    up: "icofont icofont-rounded-up",
                    down: "icofont icofont-rounded-down",
                    next: "icofont icofont-rounded-right",
                    previous: "icofont icofont-rounded-left"
                }
            });

            $('#submit-button').on('click', function(e) {
                e.preventDefault();

                let from = $('#from').val();
                let to = $('#to').val();
                // let code = $('#code').val(); 
                // let name = $('#name').val(); 
                let branch = $('#branch').val();

                // Call Livewire component method on form submission
                @this.call('submitForm', from, to, branch);
            });
            Livewire.hook('morph.updating', ({
                component,
                cleanup
            }) => {
                $('#branch').select2();
            })
        });
    </script>
@endscript
