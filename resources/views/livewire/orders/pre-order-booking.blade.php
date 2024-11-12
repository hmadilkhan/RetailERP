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
            <div class="card-header">Order Items</div>
            <div class="card-body">

            </div>
        </div>
    </section>
</div>
@script
    <script>
        $(document).ready(function() {
            $(".select2").select2();

            $('#branch').on('change', function(e) {
                var data = $('#branch').select2("val");
                console.log(data);

                @this.set('branchId', data);
            });

            Livewire.hook('morph.updating', ({
                component,
                cleanup
            }) => {
                $('#customers').select2();
                $('#branch').select2();
                $('#terminals').select2();
                $('#salespersons').select2();
                $('#ordertypes').select2();
            })
        })
    </script>
@endscript
