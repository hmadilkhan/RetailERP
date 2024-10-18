<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Customer List</h5>
        </div>
        <div class="card-block">
            <div wire:loading.class="d-flex flex-column" wire:loading>
                <div
                    class='position-relative w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>
                    <div class='spinner-border text-dark' role='status'>
                        <span class='visually-hidden'>Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block" wire:loading.remove>
            <div class="row">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-end">
                    <input type="text" class="form-control"  wire:model.defer="name"  wire:keydown.debounce.500ms="applyFilter()" placeholder="Search Customer"/>
                </div>
            </div>
            <table class="table dt-responsive table-striped table-bordered nowrap mt-3" width="100%">
                <thead>
                    <tr>
                        <th>
                            <div class="rkmd-checkbox checkbox-rotate">
                                <label class="input-checkbox checkbox-primary">
                                    <input type="checkbox" id="checkbox32" class="mainchk">
                                    <span class="checkbox"></span>
                                </label>
                                <div class="captions"></div>
                            </div>
                        </th>
                        <th>Image</th>
                        <th>Customer Name</th>
                        <th>Balance</th>
                        <th>Mobile</th>
                        <th>CNIC</th>
                        @if (session('roleId') == 2)
                            <th>Branch Name</th>
                        @endif
                        {{-- <th>Mobile App Status</th> --}}
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($customers)
                        @foreach ($customers as $value)
                            <tr>
                                <td>
                                    <div class="rkmd-checkbox checkbox-rotate">
                                        <label class="input-checkbox checkbox-primary">
                                            <input type="checkbox" id="checkbox32" class="chkbx"
                                                data-id="{{ $value->id }}">
                                            <span class="checkbox"></span>
                                        </label>
                                        <div class="captions"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <img width="42" height="42"
                                        src="{{ asset('storage/images/customers/' . (!empty($value->image) ? $value->image : 'placeholder.jpg') . '') }}"
                                        class="d-inline-block img-circle "
                                        alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                </td>
                                <td>{{ $value->name }}</td>
                                <td>{{ number_format($value->balance, 2) }}</td>
                                <td>{{ $value->mobile }}</td>
                                <td>{{ $value->nic }}</td>
                                @if (session('roleId') == 2)
                                    <td>{{ $value->userauthorization->branch->branch_name }}</td>
                                @endif
                                {{-- <td>
                                    <div class="checkbox text-center">
                                        <label>
                                            <input id="changeCheckbox{{ $value->id }}"
                                                onchange="changeCheckbox('changeCheckbox{{ $value->id }}','{{ $value->id }}')"
                                                type="checkbox" {{ $value->is_mobile_app_user == 1 ? 'checked' : '' }}
                                                data-toggle="toggle" data-size="mini" data-width="20" data-height="20">
                                        </label>
                                    </div>
                                </td> --}}
                                <!-- <td>{{ $value->status_name }}</td> -->
                                <td class="action-icon">

                                    <a href="{{ url('/discount-panel') }}/{{ $value->slug }}"
                                        class="p-r-10 f-18 text-success" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Discount"><i
                                            class="icofont icofont-sale-discount"></i></a>

                                    <a href="{{ url('/ledgerDetails') }}/{{ $value->slug }}"
                                        class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Ledger"><i
                                            class="icofont icofont-list"></i></a>

                                    <a href="{{ url('/editcustomers') }}/{{ $value->slug }}"
                                        class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Edit"><i
                                            class="icofont icofont-ui-edit"></i></a>

                                    <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm"
                                        data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Delete"></i>

                                    <a href="{{ url('get-customer-receipts', $value->slug) }}"
                                        class="p-r-10 f-18 text-warning " data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Customer Invoices"><i
                                            class="icofont icofont-list"></i></a>

                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            {{$customers->links()}}
        </div>
    </div>
</div>
