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
                                <select id="customers" class="select2" wire:model="selectedOption" style="width: 100%;">
                                    <option value="">Select an option</option>
                                    @if (is_array($options) && !empty($options))
                                        @foreach ($options as $option)
                                            <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No options available</option>
                                    @endif
                                </select>
                                @error('customerId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Order Type</label>
                                <select class="select2" id="ordertypes" wire:model="orderTypeId">
                                    <option value="">Order Type</option>
                                    @if (!empty($orderTypes))
                                        @foreach ($orderTypes as $types)
                                            <option value="{{ $types->order_mode_id }}">
                                                {{ $types->order_mode }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('orderTypeId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Order Payment</label>
                                <select class="select2" id="payments" wire:model="paymentId">
                                    <option value="">Order Payment</option>
                                    @if (!empty($payments))
                                        @foreach ($payments as $payment)
                                            <option value="{{ $payment->payment_id }}">
                                                {{ $payment->payment_mode }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('paymentId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
                                @error('branchId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
                                @error('terminalId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                    Tax</label>
                                <select class="select2" id="taxes" wire:model="taxValue">
                                    <option value="">Select Tax</option>
                                    @if (!empty($taxes))
                                        @foreach ($taxes as $tax)
                                            <option value="{{ $tax->value }}">
                                                {{ $tax->name." ".$tax->value."%" }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('paymentId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
                @error('orderItems')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
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
                                        <i class="icofont icofont-pencil text-warning fs-4"
                                            onclick="editItem('{{ $item['productId'] }}','{{ $item['qty'] }}','{{ $item['price'] }}')"></i>
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

    <section>
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
    </section>

    <section>
        <div class="row">
            <div class="col-md-12 text-end">
                <button type="button" id="place-order" class="btn btn-success text-end">Place Order</button>
            </div>
        </div>
    </section>
    @script
        <script>
            console.log("calling");
            
            $(document).ready(function() {
                $(".select2").select2();
                var select2Data = null ;

                $('#branch').on('change', function(e) {
                    var data = $('#branch').select2("val");
                    @this.set('branchId', data);
                });
                $('#taxes').on('change', function(e) {
                    var data = $('#taxes').select2("val");
                    @this.set('taxValue', data);
                });

                initializeSearchAndSelect();

                function initializeSearchAndSelect() {
                    const select2Element = $('#customers');

                    // Destroy any previous Select2 instance
                    if (select2Element.hasClass("select2-hidden-accessible")) {
                        select2Element.select2('destroy');
                    }

                    // Reinitialize Select2
                    select2Element.select2({
                        placeholder: 'Search and select',
                        minimumInputLength: 1,
                        ajax: {
                            delay: 1000,
                            transport: function(params, success, failure) {
                                // Emit the query to Livewire
                                Livewire.dispatch('fetchSelect2Options', {
                                    search: params.data.q
                                });

                                // Listen for Livewire's response
                                Livewire.on('select2OptionsFetched', (event) => {
                                    const payload = event; // Event detail should contain the array
                                    const options = payload[0]?.options ||
                                []; // Access options safely

                                    select2Data = options.map(option => ({
                                        id: option.id,
                                        text: option.name
                                    }));

                                    success({
                                        results: select2Data
                                    });
                                });
                            }
                        }
                    });
                }

                // Listen for Livewire events to reinitialize Select2
                Livewire.on('reinitializeSelect2', (event) => {
                    console.log('Reinitializing Select2...');
                    initializeSearchAndSelect();
                });

                // Sync value with Livewire
                // select2Element.on('change', function() {
                //     Livewire.dispatch('select2Updated', {
                //         value: $(this).val()
                //     });
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

                document.getElementById('place-order').addEventListener('click', function() {

                    // Select all controls and buttons to disable
                    let controls = document.querySelectorAll('input, select, button');

                    // Disable controls and buttons
                    controls.forEach(control => control.disabled = true);

                    Livewire.dispatch('placeOrder', {
                        customerId: $("#customers").val(),
                        type: $("#ordertypes").val(),
                        branchId: $("#branch").val(),
                        terminalId: $("#terminals").val(),
                        salesPersonId: $("#salespersons").val(),
                        paymentId: $("#payments").val()
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

                window.editItem = function(productId, qty, price) {
                    console.log(productId, qty, price);
                    $("#products").val(productId).change();
                    $("#qty").val(qty);
                    $("#price").val(price)
                };

                Livewire.hook('morph.updating', ({
                    component,
                    cleanup
                }) => {
                    $('.select2').select2();
                    console.log(select2Data);
                    if (select2Data == null) {
                        initializeSearchAndSelect();
                    }
                    // initializeSearchAndSelect();
                })

            })
        </script>
    @endscript
