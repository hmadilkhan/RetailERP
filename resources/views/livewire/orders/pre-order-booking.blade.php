<div>
    <style>
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
        }

        .btn-close {
            background: none;
            border: none;
            margin-left: 5px;
            cursor: pointer;
            padding: 0;
        }
    </style>

    </style>
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
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                   Customers</label>
                                <input type="text" class="form-control" placeholder="Search..."
                                    wire:model.live.debounce.100ms="customerText" aria-label="Search">

                                <div wire:loading>
                                    <p>Searching...</p>
                                </div>

                                @if (!empty($customers))
                                    <ul class="list-group mt-2"
                                        style="max-height: 200px; overflow-y: auto; z-index: 1000; position: absolute; width: 100%;">
                                        @foreach ($customers as $customer)
                                            <li class="list-group-item" style="cursor: pointer;"
                                                wire:click="selectCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                                                {{ $customer->name }}
                                            </li> <!-- Adjust to match your data -->
                                        @endforeach
                                    </ul>
                                @else
                                    @if ($customerText)
                                        <p class="mt-2">No results found for "{{ $customerText }}".</p>
                                    @endif
                                @endif

                                <!-- Display the selected customer -->
                                @if ($selectedCustomerName)
                                    <p class="mt-2">Selected Customer: {{ $selectedCustomerName }}</p>
                                @endif
                            </div>
                        </div> --}}

                        <div class="col-md-3 position-relative">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Customers</label>
                                <!-- Tag Input Container with Bootstrap classes -->
                                {{-- <div class="input-group flex-wrap"> --}}

                                <!-- Search Input -->
                                <input type="text" class="form-control" placeholder="Search..."
                                    wire:model.live.debounce.100ms="customerText" aria-label="Search" />

                                <div wire:loading>
                                    <p>Searching...</p>
                                </div>

                                <!-- Tag List Container -->
                                {{-- <div class="d-flex flex-wrap gap-2 mb-2">
                                        @foreach ($selectedCustomers as $customer)
                                            <span class="badge bg-secondary d-flex align-items-center">
                                                {{ $customer['name'] }}
                                                <button type="button" class="btn-close ms-2" aria-label="Remove"
                                                    wire:click="removeCustomer({{ $customer['id'] }})"></button>
                                            </span>
                                        @endforeach
                                    </div> --}}
                                {{-- </div> --}}

                                <!-- Search Results -->
                                @if (!empty($customers))
                                    <ul class="list-group mt-2"
                                        style="max-height: 200px; overflow-y: auto; z-index: 1000; position: absolute; width: 100%;">
                                        @foreach ($customers as $customer)
                                            <li class="list-group-item list-group-item-action" style="cursor: pointer;"
                                                wire:click="selectCustomer({{ $customer->id }}, '{{ $customer->name }}')">
                                                {{ $customer->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    @if ($customerText)
                                        <p class="mt-2 text-muted">No results found for "{{ $customerText }}".</p>
                                    @endif
                                @endif

                                @if (!empty($selectedCustomers))
                                    <div class="d-flex flex-wrap gap-2 mb-2 mt-2">
                                        @foreach ($selectedCustomers as $customer)
                                            <span class="badge bg-secondary d-flex align-items-center">
                                                {{ $customer['name'] }}
                                                <button type="button" class="btn-close ms-2" aria-label="Remove"
                                                    wire:click="removeCustomer({{ $customer['id'] }})"></button>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>



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
                                <select class="select2" id="products" wire:model="productId" wire:ignore>
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
                            <button type="button" id="submit-button" data-placement="bottom"
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
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @if ($orderItems)
                            @foreach ($orderItems as $key => $item)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $item['productName'] }}</td>
                                    <td>{{ $item['price'] }}</td>
                                    <td>{{ $item['qty'] }}</td>
                                    <td>{{ $item['amount'] }}</td>
                                    <td>
                                        <i class="icofont icofont-trash text-danger fs-4"
                                            onclick="deleteItem({{ $item['productId'] }})"></i>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center"> No record found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Sub Total -->
                <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 d-flex justify-content-end">
                    <div class="col-8 col-lg-8 col-md-8 col-sm-8"></div>
                    <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                        <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                            <span class="fw-bold ">Sub Total :</span>
                        </div>
                        <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                            <span class="">{{ number_format($subTotal, 0) }}</span>
                        </div>
                    </div>
                </div>
                <!-- Discount -->
                <div class="col-12 col-lg-12 col-md-12 col-sm-12  py-2 d-flex justify-content-end">
                    <div class="col-8 col-lg-8 col-md-8 col-sm-8"></div>
                    <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                        <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                            <span class="fw-bold ">Discount :</span>
                        </div>
                        <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                            <span class="">{{ number_format($discount, 0) }}</span>
                        </div>
                    </div>
                </div>
                <!-- Tax Amount -->
                <div class="col-12 col-lg-12 col-md-12 col-sm-12  py-2 d-flex justify-content-end">
                    <div class="col-8 col-lg-8 col-md-8"></div>
                    <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                        <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                            <span class="fw-bold ">Tax Amount :</span>
                        </div>
                        <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                            <span class="">{{ number_format($taxAmount, 0) }}</span>
                        </div>
                    </div>
                </div>
                <!-- Total Amount -->
                <div class="col-12 col-lg-12 col-md-12 col-sm-12 py-2 d-flex justify-content-end">
                    <div class="ccol-8 col-lg-8 col-md-8"></div>
                    <div class="col-4 col-lg-4 col-md-4 col-sm-4">
                        <div class="col-10 col-lg-10 col-md-10 col-sm-10 text-end">
                            <span class="fw-bold ">Total Amount :</span>
                        </div>
                        <div class="col-2 col-lg-2 col-md-2 col-sm-2 text-end">
                            <span class="">{{ number_format($totalAmount, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 text-end">
                    <div class="col-md-11 py-2">
                        <span class="fw-bold">Sub Total : </span>
                    </div>
                    <div class="col-md-1 py-2">
                       <span class="text-end">{{ number_format($subTotal, 0) }}</span>
                    </div>
                    <div class="col-md-11 py-2">
                        <span class="fw-bold"> Discount : </span>
                    </div>
                    <div class="col-md-1 py-2">
                        <span class="text-end">{{ number_format($discount, 0) }}</span>
                    </div>
                    <div class="col-md-11 py-2">
                        <span class="fw-bold"> Tax Amount : </span>
                    </div>
                    <div class="col-md-1 py-2">
                        <span class="text-end">{{ number_format($taxAmount, 0) }}</span>
                    </div>
                    <div class="col-md-11 py-2">
                        <span class="fw-bold"> Total Amount : </span>
                    </div>
                    <div class="col-md-1 py-2">
                        <span class="text-end">{{ number_format($totalAmount, 0) }}</span>
                    </div>
            </div>
        </div>
    </div>
</div> --}}
    @script
        <script>
            $(document).ready(function() {
                $(".select2").select2();

                $('#branch').on('change', function(e) {
                    var data = $('#branch').select2("val");
                    @this.set('branchId', data);
                });
                // $('#products').on('change', function(e) {
                //     var data = $('#products').select2("val");
                //     @this.set('productId', data);
                // });
                let productname = "";
                document.getElementById('submit-button').addEventListener('click', function() {
                    // Select all controls and buttons to disable
                    let controls = document.querySelectorAll('input, select, button');

                    // Disable controls and buttons
                    controls.forEach(control => control.disabled = true);

                    // Manually submit the form with parameters
                    if ($("#products").val() != "") {
                        productname = $("#products option:selected").text().replace(/\s+/g, " ");
                    }

                    // Pass parameters as an object/array
                    Livewire.dispatch('addItems', {
                        productId: $("#products").val(),
                        productName: productname,
                        qty: $("#qty").val(),
                        price: $("#price").val()
                    });

                    // Re-enable controls after the Livewire request is complete
                    Livewire.on('itemAdded', function() {
                        controls.forEach(control => control.disabled = false);
                    });

                });

                window.addEventListener('resetControls', event => {
                    $("#products").val('').change();
                    $("#qty").val('');
                    $("#price").val('')
                });

                window.deleteItem = function(id) {
                    alert('Item deletion triggered for ID: ' + id);
                    Livewire.dispatch('deleteItem', {
                        productId: id
                    });
                };

                Livewire.hook('morph.updating', ({
                    component,
                    cleanup
                }) => {
                    $('.select2').select2()
                })
            })
        </script>
    @endscript
