<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Upload Customer</h5>
            <a href="{{ route('customer.create') }}" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Create Customer"
                class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                    class="icofont icofont-plus m-r-5"></i> CREATE CUSTOMER</a>

            <button id="downloadsample" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Download Sample"
                class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i
                    class="icofont icofont-plus m-r-5"></i> Download Sample</button>

        </div>
        <div class="card-block">
            <div class="row col-md-12 ">
                <form method='post' action='{{ url('uploadFile') }}' enctype='multipart/form-data'>
                    {{ csrf_field() }}
                    <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                        <label for="vdimg" class="form-control-label">Select File </label>
                        <br />
                        <label for="vdimg" class="custom-file">
                            <input type="file" name="file" id="vdimg" class="custom-file-input">
                            <span class="custom-file-control"></span>
                        </label>
                        <input type='submit' class="btn btn-primary m-l-10 m-t-1" name='submit' value='Import'>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Filter Customer</h5>
        </div>
        <div class="card-block">
            <div class="row">
                <div class="col-md-3 col-lg-3 col-sm-12 col-xl-3">
                    <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                        <label class="form-control-label">Select Branch</label>
                        <select name="branch" id="branch" class="form-control select2">
                            <option value="">Select Branch</option>
                            @if ($branches)
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('branch'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-12 col-xl-3">
                    <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                        <label class="form-control-label">Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Search Customer" />
                        @if ($errors->has('branch'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-12 col-xl-3">
                    <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                        <label class="form-control-label">Contact</label>
                        <input type="text" class="form-control" id="contact" placeholder="Search Contact" />
                        @if ($errors->has('branch'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-12 col-xl-3">
                    <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                        <label class="form-control-label">Membership</label>
                        <input type="text" class="form-control" id="membership" placeholder="Search Membership" />
                        @if ($errors->has('branch'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <button id="searchcustomer" data-toggle="tooltip" data-placement="bottom" title=""
                        data-original-title="Search"
                        class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i
                            class="icofont icofont-plus m-r-5"></i> Search</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{-- <h5 class="card-header-text">Customer List</h5> --}}
            <div class="button-group m-l-1 f-right">
                <a style="color:white;" id="btnPdf" target="_blank" href="{{ URL::to('customers-report-pdf') }}"
                    class="btn btn-md btn-danger waves-effect waves-light f-right"><i
                        class="icofont icofont-file-excel">
                    </i>
                    Export to PDF
                </a>
            </div>
            <div class="button-group f-right">
                <button style="color:white;" id="btnExcel"
                    class="btn btn-md btn-success waves-effect waves-light f-right"><i
                        class="icofont icofont-file-excel">
                    </i>
                    Export to Excel
                </button>
            </div>
            <div class="button-group f-left">
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="statuscheckbox" class="f-left">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions">{{ $checkboxText }}</div>
                </div>
            </div>

        </div>
        <div class="card-block" wire:loading>
            <div wire:loading.class="d-flex flex-column">
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
                <div class="col-md-3">
                    <select id="pageNo" class="form-control" wire:model="pageNo" style="width:50px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-7"></div>
                <div class="col-md-2 text-end">
                    <input type="text" class="form-control" wire:model.defer="name"
                        wire:keydown.debounce.500ms="applyFilter()" placeholder="Search Customer" />
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
                        @if (session('roleId') == 2)
                            <th>Branch Name</th>
                        @endif
                        <th>Balance</th>
                        <th>Mobile</th>
                        <th>CNIC</th>
                        <th>Membership</th>
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
                                @if (session('roleId') == 2)
                                    <td>{{ isset($value->userauthorization->branch->branch_name) ? $value->userauthorization->branch->branch_name : '' }}
                                    </td>
                                @endif
                                <td>{{ number_format($value->balance, 2) }}</td>
                                <td>{{ $value->mobile }}</td>
                                <td>{{ $value->nic }}</td>
                                <td>{{ $value->membership_card_no }}</td>
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
            {{ $customers->links() }}
        </div>
    </div>
</div>
@script
    <script>
        $(".select2").select2();
        $('#downloadsample').click(function() {
            $.ajax({
                url: 'https://sabsoft.com.pk/Retail/assets/samples/sample_customer.csv',
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = 'sample_customer.csv';
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                }
            });
        });
        $("#statuscheckbox").change(function() {
            if ($(this).prop('checked')) {
                @this.set('status', 2);
                @this.set('checkboxText', 'Show Active Items');
            } else {
                @this.set('status', 1);
                @this.set('checkboxText', 'Show In-Active Items');
            }
        })
        $("#pageNo").change(function() {
            @this.set('pageNo', $(this).val());
        });
        $("#searchcustomer").click(function() {
            let branch = $("#branch").val();
            let name = $("#name").val();
            let contact = $("#contact").val();
            let membership = $("#membership").val();

            Livewire.dispatch('searchCustomer', {
                branch: branch,
                name: name,
                contact: contact,
                membership: membership
            });
        })

        $("#btnExcel").click(function(){
            let branch = $("#branch").val();
            let name = $("#name").val();
            let contact = $("#contact").val();
            let membership = $("#membership").val();
            window.open("{{ URL::to('export-customer-balance') }}"+"?branch="+branch+"&name="+name+"&contact="+contact+"&membership="+membership);
        })
        $("#btnPdf").click(function(){
            let branch = $("#branch").val();
            let name = $("#name").val();
            let contact = $("#contact").val();
            let membership = $("#membership").val();
            window.open("{{ URL::to('customers-report-pdf') }}"+"?branch="+branch+"&name="+name+"&contact="+contact+"&membership="+membership);
        })

        Livewire.hook('morph.updating', ({
            component,
            cleanup
        }) => {
            $('.select2').select2();
            // initializeSearchAndSelect();
        })
    </script>
@endscript
