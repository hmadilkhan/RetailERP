@extends('layouts.master-layout')

@section('title', 'ERP Reports')

@section('breadcrumtitle', 'ERP Reports')

@section('naverpreport', 'active')


@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h1 class="card-header-text">ERP Report Dashboard</h1>
            </div>
            <div class="card-block">
                <div class="row">
                    <div id="dvprofitstandard" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Profit & Loss Standard">
                            <h4 class="text-sm-center">Profit & Loss Standard</h4>
                        </div>
                    </div>
                    <div id="dvprofitdetails" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Profit & Loss Details">
                            <h4 class="text-sm-center">Profit & Loss Details</h4>
                        </div>
                    </div>
                    <div id="dvshowreport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Stock Report">
                            <h4 class="text-sm-center">Stock Report</h4>
                        </div>
                    </div>

                </div>
                <br>
                <div class="row">
                    <div id="dvinventory" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Inventory Details">
                            <h4 class="text-sm-center">Inventory Details</h4>
                        </div>
                    </div>
                    <div id="dvexpensesheet" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Expense Sheet">
                            <h4 class="text-sm-center">Expense Sheet</h4>
                        </div>
                    </div>
                    <div id="dvexpensecat" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Expense By Category">
                            <h4 class="text-sm-center">Expense By Category</h4>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div id="dvsaledecleration" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Sales Decleration">
                            <h4 class="text-sm-center">Sales Declaration</h4>
                        </div>
                    </div>
                    <div id="dvitemsale" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Item Sale Database">
                            <h4 class="text-sm-center">Item Sale Database</h4>
                        </div>
                    </div>
                    <div id="dvsalereturn" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Sales Return">
                            <h4 class="text-sm-center">Sales Return</h4>
                        </div>
                    </div>


                </div>
                <br>
                <div class="row">
                    <div id="dvphysical" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Physical Inventory Sheet">
                            <h4 class="text-sm-center">Physical Inventory Sheet</h4>
                        </div>
                    </div>
                    <div id="dvstockadjustment" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Stock Adjustment">
                            <h4 class="text-sm-center">Stock Adjustment</h4>
                        </div>
                    </div>
                    <div id="dvcustomeraging" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Stock Adjustment">
                            <h4 class="text-sm-center">Customer Aging</h4>
                        </div>
                    </div>


                </div>
                <br>
                <div class="row">
                    <div id="dvfbrreport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="FBR Report">
                            <h4 class="text-sm-center">FBR Report</h4>
                        </div>
                    </div>

                    <div id="dvinvoicereport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Invoice Details">
                            <h4 class="text-sm-center">Invoice Details</h4>
                        </div>
                    </div>

                    <div id="dvsalesinvoicereport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Invoice Details">
                            <h4 class="text-sm-center">Sales Invoices</h4>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div id="dvinventorygeneral" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Inventory General Details">
                            <h4 class="text-sm-center">Inventory General Details</h4>
                        </div>
                    </div>

                    <div id="dvbookingorderreport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Booking Order Report">
                            <h4 class="text-sm-center">Booking Order Report</h4>
                        </div>
                    </div>

                    <div id="dvsalespersonreport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top"
                            title="Sales Person Report">
                            <h4 class="text-sm-center">Sales Person Report</h4>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </section>

    {{--        modals --}}
    <div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Filter Box
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" value="0" id="txtphysical" />
                    <div class="row" id="dvdepart">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Department</label>
                                <select name="depart" id="depart" data-placeholder="Select Department"
                                    class="form-control select2">
                                    <option value="">Select Department</option>
                                    <option value="0">All Department</option>
                                    @if ($departments)
                                        @foreach ($departments as $value)
                                            <option value="{{ $value->department_id }}">{{ $value->department_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" onClick="getreport()"><i
                            class="icofont icofont-file-pdf"> </i>Get Report</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="filter-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="filterbox"> Filter Box
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </h4>

                </div>

                <div class="modal-body">
                    {{--                    hidden fields --}}
                    <input type="hidden" value="0" id="txtprofitstandard" />
                    <input type="hidden" value="0" id="txtprofitdetails" />
                    <input type="hidden" value="0" id="txtinventory" />
                    <input type="hidden" value="0" id="txtexpensesheet" />
                    <input type="hidden" value="0" id="txtexpensecat" />
                    <input type="hidden" value="0" id="txtsaledec" />
                    <input type="hidden" value="0" id="txttype" />
                    <input type="hidden" value="0" id="txtsalereturn" />
                    <input type="hidden" value="0" id="txtitemsale" />
                    <input type="hidden" value="0" id="txtstockadjustment" />
                    <input type="hidden" value="0" id="txtfbrreport" />
                    <input type="hidden" value="0" id="txtinvoicereport" />
                    <input type="hidden" value="0" id="txtsalesinvoicesreport" />
                    <input type="hidden" value="0" id="txtstockreport" />
                    <input type="hidden" value="0" id="txtinventorygeneralreport" />
                    <input type="hidden" value="0" id="txtbookingorderreport" />
                    <input type="hidden" value="0" id="txtsalespersonreport" />




                    <div class="row" id="dateFilter">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">From Date</label>
                                <input class="form-control" type="text" name="datefrom" id="datefrom"
                                    placeholder="DD-MM-YYYY" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">To Date</label>
                                <input class="form-control" type="text" name="dateto" id="dateto"
                                    placeholder="DD-MM-YYYY" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvitemcode" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Item Code</label>
                                <input class="form-control" type="text" name="itemcode" id="itemcode"
                                    placeholder="Enter Item Code" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvtype" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Type</label>
                                <select name="type" id="type" data-placeholder="Select Type"
                                    class="form-control select2">
                                    <option value="declaration">Declaration</option>
                                    <option value="datewise">Datewise</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvbranch" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Branch</label>
                                <select name="branch" id="branch" data-placeholder="Select Branch"
                                    class="form-control select2">
                                    <option value="all">All Branches</option>
                                    @if ($branches)
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvdepartments" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Department</label>
                                <select name="department" id="department" data-placeholder="Select Department"
                                    class="form-control select2">
                                    <option value="">Select Department</option>
                                    @if ($departments)
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->department_id }}">
                                                {{ $department->department_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvpaymentmodes" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Payment Method</label>
                                <select name="paymentmethod" id="paymentmethod" data-placeholder="Select Payment Method"
                                    class="form-control select2">
                                    <option value="">Select Payment Method</option>
                                    @if ($paymentModes)
                                        @foreach ($paymentModes as $payment)
                                            <option value="{{ $payment->payment_id }}">{{ $payment->payment_mode }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvsubdepartments" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Sub Department</label>
                                <select name="subdepartment" id="subdepartment" data-placeholder="Select Sub Department"
                                    class="form-control select2">
                                    <option value="">Select Sub Department</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvterminal" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Terminal</label>
                                <select name="terminal" id="terminal" data-placeholder="Select Terminal"
                                    class="form-control select2">
                                    <option value="">Select Terminal</option>
                                    <option value="0">All Terminals</option>
                                    @if ($terminals)
                                        @foreach ($terminals as $value)
                                            <option value="{{ $value->terminal_id }}">{{ $value->terminal_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvsalesperson" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Sales Person</label>
                                <select name="salesperson" id="salesperson" data-placeholder="Select Sales Person"
                                    class="form-control select2">
                                    <option value="all">All</option>
                                    @if ($salespersons)
                                        @foreach ($salespersons as $value)
                                            <option value="{{ $value->serviceprovideruser->user_id }}">
                                                {{ $value->provider_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvmode" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Mode</label>
                                <select name="mode" id="mode" data-placeholder="Select Mode"
                                    class="form-control select2">
                                    <option value="all">All</option>
                                    <option value="balances">Balances</option>
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="dvstatus" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Status</label>
                                <select name="status" id="status" data-placeholder="Select Status"
                                    class="form-control select2">
                                    <option value="all">All</option>
                                    @foreach($statuses as $status)
                                        <option value="{{$status->order_status_id}}">{{$status->order_status_name}}</option>
                                    @endforeach
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="ExcelButton" style="display:none;" type="button"
                        class="btn btn-success waves-effect waves-light f-right m-l-2" onClick="getExcelData()"><i
                            class="icofont icofont-file-excel"> </i>Excel Report</button>
                    <button type="button" class="btn btn-danger waves-effect waves-light" onClick="getdata()"><i
                            class="icofont icofont-file-pdf"> </i>Get Report</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();


        $('#datefrom, #dateto').bootstrapMaterialDatePicker({
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

        // Common function to handle the button clicks
        function handleButtonClick(buttonId, filterText, fieldMappings) {
            // Reset all fields to 0
            load_terminals();
            const fields = [
                '#txtprofitstandard', '#txtprofitdetails', '#txtinventory',
                '#txtexpensesheet', '#txtexpensecat', '#txtsaledec',
                '#txttype', '#txtitemsale', '#txtstockadjustment',
                '#txtphysical', '#txtstockreport', '#txtinventorygeneralreport',
                '#txtfbrreport', '#txtinvoicereport', '#txtsalesinvoicesreport', '#txtbookingorderreport',
                '#txtsalereturn'
            ];

            fields.forEach(field => {
                $(field).val(0);
            });

            const filters = ['#dvbranch', '#dvdepartments', '#dvsubdepartments', '#dvterminal', '#dvtype', '#dvitemcode',
                '#dvpaymentmodes', '#dvsalesperson', '#dvmode','#dvstatus'
            ];
            filters.forEach(field => {
                $(field).css('display', 'none');
            });

            // Set specific fields based on mapping
            // console.log(fieldMappings);
            fieldMappings.forEach(mapping => {
                $(mapping.field).val(mapping.value);
            });

            // Set filter text
            $('#filterbox').text(filterText);

            // Show modal and display relevant elements
            $('#filter-modal').modal('show');
            $('#dateFilter').css('display', fieldMappings.some(mapping => mapping.showDateFilter) ? 'block' : 'none');
            $('#ExcelButton').css('display', fieldMappings.some(mapping => mapping.showExcelButton) ? 'block' : 'none');
            console.log("Print Mappings",fieldMappings);
            console.log("Print Condition",fieldMappings.some(mapping => mapping.showStatus));
            
            // Call specific functions if needed
            if (fieldMappings.some(mapping => mapping.showBranch)) showbranch();
            if (fieldMappings.some(mapping => mapping.showTerminal)) showterminal();
            if (fieldMappings.some(mapping => mapping.showDepartments)) showdepartments();
            if (fieldMappings.some(mapping => mapping.showSubDepartments)) showsubdepartments();
            if (fieldMappings.some(mapping => mapping.showType)) showType();
            if (fieldMappings.some(mapping => mapping.showCode)) showCode();
            if (fieldMappings.some(mapping => mapping.showPaymentMode)) showPaymentMode();
            if (fieldMappings.some(mapping => mapping.showSalesPerson)) showSalesPerson();
            if (fieldMappings.some(mapping => mapping.showMode)) showMode();
            if (fieldMappings.some(mapping => mapping.showStatus)) showStatus();
        }

        $('#dvprofitstandard').on('click', function() {
            handleButtonClick('#dvprofitstandard', 'Profit & Loss', [{
                field: '#txtprofitstandard',
                value: 1,
                showDateFilter: true,
                showBranch: true
            }]);
        });

        $('#dvprofitdetails').on('click', function() {
            handleButtonClick('#dvprofitdetails', 'Profit Details', [{
                field: '#txtprofitdetails',
                value: 1,
                showDateFilter: true,
                showBranch: true
            }]);
        });

        $('#dvshowreport').on('click', function() {
            handleButtonClick('#dvshowreport', 'Stock Report', [{
                field: '#txtstockreport',
                value: 1,
                showDateFilter: false,
                showDepartments: true,
                showsubdepartments: true,
                showBranch: true
            }]);
        });

        $('#dvinventory').on('click', function() {
            handleButtonClick('#dvinventory', 'Inventory Details', [{
                field: '#txtinventory',
                value: 1,
                showDateFilter: true,
                showBranch: true,
                showDepartments: true,
                showsubdepartments: true
            }]);
        });

        $('#dvexpensesheet').on('click', function() {
            handleButtonClick('#dvexpensesheet', 'Expense Sheet', [{
                field: '#txtexpensesheet',
                value: 1,
                showDateFilter: true
            }]);
        });

        $('#dvexpensecat').on('click', function() {
            handleButtonClick('#dvexpensecat', 'Expense Category', [{
                field: '#txtexpensecat',
                value: 1,
                showDateFilter: true
            }]);
        });

        $('#dvsaledecleration').on('click', function() {
            handleButtonClick('#dvsaledecleration', 'Sales Declaration', [{
                field: '#txtsaledec',
                value: 1,
                showDateFilter: true,
                showBranch: true,
                showTerminal: true
            }]);
        });

        $('#dvitemsale').on('click', function() {
            handleButtonClick('#dvitemsale', 'Item Sale Database', [{
                field: '#txtitemsale',
                value: 1,
                showDateFilter: true,
                showType: true,
                showDepartments: true,
                showsubdepartments: true,
                showBranch: true,
                showTerminal: true
            }]);
        });

        $('#dvsalereturn').on('click', function() {
            handleButtonClick('#dvsalereturn', 'Sales Return', [{
                field: '#txtsalereturn',
                value: 1,
                showDateFilter: true,
                showBranch: true,
                showTerminal: true,
                showCode: true
            }]);
        });
        $('#dvphysical').on('click', function() {
            handleButtonClick('#dvphysical', 'Physical Inventory Sheet', [{
                field: '#txtphysical',
                value: 1,
                showDateFilter: false,
                showDepartments: true
            }]);
        });

        $('#dvstockadjustment').on('click', function() {
            handleButtonClick('#dvstockadjustment', 'Stock Adjustment', [{
                field: '#txtstockadjustment',
                value: 1,
                showDateFilter: true,
                showBranch: true
            }]);
        });

        $('#dvfbrreport').on('click', function() {
            handleButtonClick('#dvfbrreport', 'FBR Report', [{
                field: '#txtfbrreport',
                value: 1,
                showDateFilter: true,
                showBranch: true,
                showTerminal: true
            }]);
        });

        $('#dvinvoicereport').on('click', function() {
            handleButtonClick('#dvinvoicereport', 'Invoice Report', [{
                field: '#txtinvoicereport',
                value: 1,
                showDateFilter: true,
                showType: true,
                showBranch: true,
                showTerminal: true
            }]);
        });

        $('#dvsalesinvoicereport').on('click', function() {
            handleButtonClick('#dvsalesinvoicereport', 'Sales Invoices', [{
                field: '#txtsalesinvoicesreport',
                value: 1,
                showDateFilter: true,
                showBranch: true,
                showType: true,
                showTerminal: true
            }]);
        });

        $('#dvinventorygeneral').on('click', function() {
            handleButtonClick('#dvinventorygeneral', 'Inventory Details with Images', [{
                field: '#txtinventorygeneralreport',
                value: 1,
                showBranch: true,
                showDepartments: true,
                showsubdepartments: true
            }]);
        });

        $('#dvbookingorderreport').on('click', function() {
            handleButtonClick('#dvbookingorderreport', 'Booking Order Report', [{
                field: '#txtbookingorderreport',
                value: 1,
                showDateFilter: true,
                showPaymentMode: true,
                showBranch: true,
                showMode: true
            }]);
        });

        $('#dvsalespersonreport').on('click', function() {
            handleButtonClick('#dvsalespersonreport', 'Sales Person Report', [{
                field: '#txtsalespersonreport',
                value: 1,
                showDateFilter: true,
                showBranch: true,
                showSalesPerson: true,
                showStatus :true
            }]);
        });

        function copydate() {
            let date = $('#datefrom').val();
            $('#dateto').val(date);
        }



        function getdata() {
            let date = $('#datefrom').val();
            let todate = $('#dateto').val();
            let terminalid = $('#terminal').val();
            let code = $('#itemcode').val();
            let branch = $('#branch').val();
            let type = $('#type').val();
            let department = $('#department').val();
            let subdepartment = $('#subdepartment').val();
            let paymentmethod = $('#paymentmethod').val();
            let salesperson = $('#salesperson').val();
            let mode = $('#mode').val();
            let status = $('#status').val();

            if ($('#txtprofitstandard').val() == 1) {
                window.location = "{{ url('profitLossStandardReport') }}?fromdate=" + date + "&todate=" + todate +
                    "&branch=" + branch;
            }
            if ($('#txtprofitdetails').val() == 1) {
                window.location = "{{ url('profitLossDetailsReport') }}?fromdate=" + date + "&todate=" + todate +
                    "&branch=" + branch;
            }
            if ($('#txtinventory').val() == 1) {
                window.location = "{{ url('inventory_detailsPDF') }}?fromdate=" + date + "&todate=" + todate + "&branch=" +
                    branch + "&department=" + department + "&subdepartment=" + subdepartment;
            }
            if ($('#txtexpensesheet').val() == 1) {
                window.location = "{{ url('expense-report-pdf') }}?first=" + date + "&second=" + todate;
            }
            if ($('#txtexpensecat').val() == 1) {
                window.location = "{{ url('expense_by_categorypdf') }}?fromdate=" + date + "&todate=" + todate;
            }
            if ($('#txtsaledec').val() == 1) {
                window.location = "{{ url('salesdeclerationreport') }}?fromdate=" + date + "&todate=" + todate +
                    "&branch=" + branch + "&terminalid=" + terminalid;
            }
            if ($('#txtitemsale').val() == 1) {
                window.location = "{{ url('itemsaledatabasepdf') }}?fromdate=" + date + "&todate=" + todate +
                    "&terminalid=" + terminalid + "&type=" + $("#type").val() + "&department=" + department + "&branch=" +
                    branch;
            }
            if ($('#txtsalereturn').val() == 1) {
                window.location = "{{ url('salesreturnpdf') }}?fromdate=" + date + "&todate=" + todate + "&terminalid=" +
                    terminalid + "&code=" + code + "&branch=" + branch;
            }
            if ($('#txtstockadjustment').val() == 1) {
                window.location = "{{ url('stockAdjustmentReport') }}?fromdate=" + date + "&todate=" + todate +
                    "&branch=" + branch;
            }

            if ($('#txtfbrreport').val() == 1) {
                window.location = "{{ url('fbr-report') }}?fromdate=" + date + "&todate=" + todate + "&branch=" + branch;
            }

            if ($('#txtinvoicereport').val() == 1) {
                window.location = "{{ url('invoice-report') }}?fromdate=" + date + "&todate=" + todate + "&terminalid=" +
                    terminalid + "&type=" + type + "&branch=" + branch;
            }

            if ($('#txtsalesinvoicesreport').val() == 1) {
                window.location = "{{ url('sales-invoices-report') }}?fromdate=" + date + "&todate=" + todate +
                    "&terminalid=" + terminalid + "&type=" + type + "&branch=" + branch;
            }

            if ($('#txtstockreport').val() == 1) {
                window.location = "{{ url('inventoryReport') }}?branch=" + branch + "&department=" + department +
                    "&subdepartment=" + subdepartment;
            }
            if ($('#txtinventorygeneralreport').val() == 1) {
                window.location = "{{ url('inventory-image-report') }}?department=" + department + "&subdepartment=" +
                    subdepartment + "&branch=" + branch;
            }
            if ($('#txtbookingorderreport').val() == 1) {
                window.location = "{{ url('order-booking-report') }}?fromdate=" + date + "&todate=" + todate +
                    "&paymentmethod=" + paymentmethod + "&branch=" + branch + "&mode=" + mode ;
            }
            if ($('#txtsalespersonreport').val() == 1) {
                window.location = "{{ url('sales-person-report') }}?fromdate=" + date + "&todate=" + todate + "&branch=" +
                    branch + "&salesperson=" + salesperson+ "&status=" + status ;
            }
        }

        function getExcelData() {
            let from = $('#datefrom').val();
            let to = $('#dateto').val();
            let terminal = $('#terminal').val();

            if ($('#txtitemsale').val() == 1) {
                window.location = "{{ url('export-isdb') }}?from=" + from + "&to=" + to + "&terminal=" + terminal;
            }

            if ($('#txtfbrreport').val() == 1) {
                window.location = "{{ url('export-fbr') }}?from=" + from + "&to=" + to + "&terminal=" + terminal;
            }
        }

        $('#dvcustomeraging').on('click', function() {
            window.open("{{ url('customer-aging') }}");
        });

        function showType() {
            if ($('#txttype').val() == 1) {
                $('#dvtype').css("display", "block");
            } else {
                $('#dvtype').css("display", "none");
            }
        }

        function showCode() {
            $('#dvitemcode').css("display", "block");
        }

        function showPaymentMode() {
            $('#dvpaymentmodes').css("display", "block");
        }

        function showterminal() {
            $('#dvterminal').css("display", "block");
        }

        function showbranch() {
            $('#dvbranch').css("display", "block");
        }

        function showSalesPerson() {
            $('#dvsalesperson').css("display", "block");
        }

        function showMode() {
            $('#dvmode').css("display", "block");
        }
        function showStatus() {
            $('#dvstatus').css("display", "block");
        }
        showdepartments();

        function showdepartments() {
            if ($('#txtinventory').val() == 1 || $('#txtitemsale').val() == 1 || $('#txtinventorygeneralreport').val() ==
                1 || $('#txtstockreport').val() == 1) {
                $('#dvdepartments').css("display", "block");
            } else {
                $('#dvdepartments').css("display", "none");
            }
            if ($('#txtinventory').val() == 1 || $('#txtinventorygeneralreport').val() == 1 || $('#txtitemsale').val() ==
                1 || $('#txtstockreport').val() == 1) {
                $('#dvsubdepartments').css("display", "block");
            } else {
                $('#dvsubdepartments').css("display", "none");
            }
        }

        function showsubdepartments() {
            $('#dvsubdepartments').css("display", "block");
        }

        function getreport() {
            if ($('#depart').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Please Select Department!",
                    type: "error"
                });
            } else {
                window.location = "{{ url('inventoryReportPhysical') }}?departid=" + $('#depart').val();
            }
        }

        $("#department").change(function() {
            if ($(this).val() != "") {
                load_sub_dept($(this).val());
            }
        });


        function load_sub_dept(id) {
            $.ajax({
                url: "{{ url('get_sub_departments') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: $("#department").val()
                },
                success: function(resp) {
                    $('#subdepartment').empty();
                    $("#subdepartment").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function(index, value) {
                        $("#subdepartment").append(
                            "<option value=" + value.sub_department_id + ">" + value
                            .sub_depart_name + "</option>"
                        );
                    });
                }
            });
        }

        function load_terminals() {
            $.ajax({
                url: "{{ url('getTerminals') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: $("#branch").val(),
                    status: 1
                },
                success: function(resp) {
                    $('#terminal').empty();
                    $("#terminal").append("<option value=''>Select Terminal</option>");
                    $.each(resp, function(index, value) {
                        $("#terminal").append(
                            "<option value=" + value.terminal_id + ">" + value
                            .terminal_name + "</option>"
                        );
                    });
                }
            });
        }

        function loadSalesPersons() {
            $.ajax({
                url: "{{ route('sp.branch') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: $("#branch").val()
                },
                success: function(resp) {
                    $('#salesperson').empty();
                    $("#salesperson").append('<option value="all">All</option>');
                    $("#salesperson").append("<option value=''>Select Sales Person</option>");
                    $.each(resp.providers, function(index, value) {
                        $("#salesperson").append(
                            "<option value=" + value.serviceprovideruser.user_id + ">" + value
                            .provider_name + "</option>"
                        );
                    });
                }
            });
        }

        $("#branch").change(function() {
            load_terminals($(this).val())
            loadSalesPersons($(this).val());
        })
    </script>
@endsection
