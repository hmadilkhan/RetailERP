<div>
    <section>
        <div class="card">
            <div class="card-header">Pre-order Booking</div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Customers</label>
                                <select class="select2" id="customers" wire:model="customerId">
                                    <option value="">Select Customer</option>
                                    @if ($customers)
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Order Type</label>
                                <select class="select2" id="ordertypes" wire:model="orderTypeId">
                                    <option value="">Order Type</option>
                                    @if ($orderTypes)
                                        @foreach ($orderTypes as $types)
                                            <option value="{{ $types->order_mode_id }}">
                                                {{ $types->order_mode }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Branches</label>
                                <select class="select2" id="branch" wire:model="branchId">
                                    <option value="">Branches</option>
                                    @if ($branches)
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->branch_id }}">
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Terminals</label>
                                <select class="select2" id="terminals" wire:model="terminalId">
                                    <option value="">Terminals</option>
                                    @if ($terminals)
                                        @foreach ($terminals as $terminal)
                                            <option value="{{ $terminal->terminal_id }}">
                                                {{ $terminal->terminal_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Sales Persons</label>
                                <select class="select2" id="salespersons" wire:model="terminalId">
                                    <option value="">Sales Persons</option>
                                    @if (!empty($salesPersons))
                                        @foreach ($salesPersons as $salesPerson)
                                            @if (!empty($salesPerson->serviceprovideruser))
                                                <option value="{{ $salesPerson->serviceprovideruser->user_id }}">
                                                    {{ $salesPerson->provider_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-3">
                            <input 
                                type="text" 
                                class="form-control" 
                                placeholder="Search..." 
                                wire:model.debounce.100ms="customerText" 
                                aria-label="Search"
                            >
                        
                            @if (!empty($customers))
                                <ul class="list-group mt-2">
                                    @foreach ($customers as $customer)
                                        <li class="list-group-item">{{ $customer->name }}</li> <!-- Adjust to match your data -->
                                    @endforeach
                                </ul>
                            @else
                                @if ($customerText)
                                    <p class="mt-2">No results found for "{{ $customerText }}".</p>
                                @endif
                            @endif
                        </div> --}}

                    </div>
                </form>
            </div>
        </div>
    </section>

    <section>
        <div class="card">
            <div class="card-header">Add Order Items</div>
            <div class="card-body">
                <form wire:submit.prevent="addItems">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Products</label>
                                <select class="select2" id="products" wire:model="productId">
                                    <option value="">Select Products</option>
                                    @if ($products)
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->item_code }} - {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i> Qty</label>
                                <input class="form-control" type="text" id="qty" wire:model="qty"
                                    placeholder="Enter Qty" />
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div id="itemcode" class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>Price</label>
                                <input class="form-control" type="text" wire:model="price" id="price"
                                    placeholder="Enter Price " />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit"  data-placement="bottom"
                                class="btn btn-success  waves-effect waves-light mt-4">Add Item</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <section>
        <div class="card">
            <div class="card-header">Order Items Details</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>S.No.</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @if($orderItems)
                            @foreach($orderItems as $key => $item)
                                <tr>
                                    <td>{{++$key}}</td>
                                    <td>{{$item["productId"]}}</td>
                                    <td>{{$item["qty"]}}</td>
                                    <td>{{$item["price"]}}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 text-end">

                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $(document).ready(function() {
            $(".select2").select2();

            $('#branch').on('change', function(e) {
                var data = $('#branch').select2("val");
                @this.set('branchId', data);
            });
            $('#products').on('change', function(e) {
                var data = $('#products').select2("val");
                @this.set('productId', data);
            });

            // document.getElementById('submit-button').addEventListener('click', function() {
            //     // Manually submit the form with parameters
            //     console.log($("#products").val(), $("#qty").val(), $("#price").val());

            //     // Pass parameters as an object/array
            //     Livewire.dispatch('addItems', {
            //         productId: $("#products").val(),
            //         qty: $("#qty").val(),
            //         price: $("#price").val()
            //     });
                
            // });


            Livewire.hook('morph.updating', ({
                component,
                cleanup
            }) => {
                $('#customers').select2();
                $('#branch').select2();
                $('#terminals').select2();
                $('#salespersons').select2();
                $('#ordertypes').select2();
                $('#products').select2();
            })
        })
    </script>
@endscript
