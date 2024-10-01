<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Stock Adjustment Details</h5>
            <a href="{{ url('stockadjustment') }}" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Create Inventory"
                class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                    class="icofont icofont-plus m-r-5"></i> CREATE NEW ADJUSTMENT</a>
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
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                    ItemCode</label>
                                <input class="form-control" type="text" name="code" id="code"
                                    wire:model="code" placeholder="Enter Product ItemCode for search" />
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                    Product</label>
                                <input class="form-control" type="text" id="name" wire:model="name"
                                    placeholder="Enter Product Name for search" />
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
                                class="btn btn-success  waves-effect waves-light mt-4"
                                >Search</button>
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
            <div class="project-table">
                <table wire:loading.remove
                    class="table table-striped nowrap dt-responsive m-t-10 dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th>Ref#</th>
                            <th>Date</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Qty</th>
                            {{-- <th>Stock</th> --}}
                            <th>Created By</th>
                            <th>Narration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($stocks))
                            @foreach ($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->productstock->grn_id }}</td>
                                    <td>{{ date('d-m-Y', strtotime($stock->date)) }}</td>
                                    <td>{{ $stock->products->item_code }}</td>
                                    <td>{{ $stock->products->product_name }}</td>
                                    <td>{{ $stock->qty }}</td>
                                    {{-- <td>{{ $stock->stock }}</td> --}}
                                    <td>{{ $stock->productstock->grn->user->fullname }}</td>
                                    <td>{{ $stock->narration }}</td>
                                    <td>
                                        <a target="_blank"
                                            href="{{ route('stock.adjustment.voucher', $stock->productstock->grn_id) }}"
                                            class="text-danger p-r-10 f-18" data-toggle="tooltip" data-placement="top"
                                            title="" data-original-title="View"><i
                                                class="icofont icofont-printer"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">No record found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if (!empty($stocks))
                {{ $stocks->links() }}
            @endif
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
                let code = $('#code').val(); 
                let name = $('#name').val(); 
                let branch = $('#branch').val(); 

                // Call Livewire component method on form submission
                @this.call('submitForm', from, to,code,name,branch);
            });

            // $('#from').on('change', function(e) {
            //     var data = $('#from').val();
            //     @this.set('from', data);
            // });

            // $('#to').on('change', function(e) {
            //     var data = $('#to').val();
            //     @this.set('to', data);
            // });

            // $('#branch').on('change', function(e) {
            //     var data = $('#branch').select2("val");
            //     @this.set('branch', data);
            // });

            Livewire.hook('morph.updating', ({
                component,
                cleanup
            }) => {
                $('#branch').select2();
            })
        });
    </script>
@endscript
