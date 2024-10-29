@extends('layouts.master-layout')

@section('title', 'Orders')

@section('breadcrumtitle', 'Orders Panel')

@section('navbranchoperation', 'active')
@section('navorder', 'active')

@section('content')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h3 class="">Order Details</h3>
                <hr />
                <div class="checkbox">
                    <label><input type="checkbox" id="checkbox"> Search Delivery Orders</label>
                </div>
                <div class="row ">
                    <div id="customernumber" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Mobile No.</label>
                            <input type='text' class="form-control" id="customer_number" name="customer_number"
                                placeholder="Mobile No" />
                            <span class="help-block text-danger" id="customernumber"></span>
                        </div>
                    </div>
                    <div id="machineorder#" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Machine Order No.</label>
                            <input type='text' class="form-control" id="machine_order_no" name="machine_order_no"
                                placeholder="Machine Order No" />
                            <span class="help-block text-danger" id="machineorder#"></span>
                        </div>
                    </div>
                    <div id="order#" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Order#</label>
                            <input type='text' class="form-control" id="order_no" name="order_no"
                                placeholder="Order No" />
                            <span class="help-block text-danger" id="order#"></span>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Receipt No</label>
                            <input type='text' class="form-control" id="receipt" name="receipt"
                                placeholder="Receipt No" />
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>

                    <div id="from" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="rpdate" name="rpdate"
                                placeholder="DD-MM-YYYY" />
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>

                    <div id="to" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">To Date</label>
                            <input type='text' class="form-control" id="date" name="date"
                                placeholder="DD-MM-YYYY" />
                            <span class="help-block text-danger" id="dbox"></span>
                        </div>
                    </div>

                    <div id="deliveryfrom" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Delivery From Date</label>
                            <input type='text' class="form-control" id="del_from" name="rpdate"
                                placeholder="DD-MM-YYYY" />
                            <span class="help-block text-danger" id="rpbox"></span>
                        </div>
                    </div>
                    <div id="deliveryto" class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Delivery To Date</label>
                            <input type='text' class="form-control" id="del_to" name="date"
                                placeholder="DD-MM-YYYY" />
                            <span class="help-block text-danger" id="dbox"></span>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <label class="form-control-label">Select Payment Mode</label>
                        <select id="paymentmode" name="paymentmode" class="f-right select2"
                            data-placeholder="Select Payment Mode">
                            <option value="">Select Payment Mode</option>
                            @foreach ($paymentMode as $value)
                                <option value="{{ $value->payment_id }}">{{ $value->payment_mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <label class="form-control-label">Select Mode</label>
                        <select id="ordermode" name="ordermode" data-placeholder="Select Mode" class="f-right select2">
                            <option value="">Select Mode</option>
                            @foreach ($mode as $value)
                                <option value="{{ $value->order_mode_id }}">{{ $value->order_mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <label class="form-control-label">Select Status</label>
                        <i id="btn_depart" class="icofont icofont-eraser mt-2 f-right text-success" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Clear All"
                            onclick="clearControl('orderstatus')"></i>
                        <select id="orderstatus" name="orderstatus" data-placeholder="Select Status"
                            class="f-right select2" multiple>
                            <option value="">Select Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->order_status_id }}">{{ $status->order_status_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12" style="">
                        <label class="form-control-label">Select Branch</label>
                        <i id="btn_depart" class="icofont icofont-eraser mt-2 f-right text-success" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Clear All"
                            onclick="clearControl('branch')"></i>
                        <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2"
                            multiple>
                            {{-- @if (session('roleId') == 2 or session('roleId') == 17 or session('roleId') == 19) --}}
                            <option selected value="all">All</option>
                            {{-- @endif --}}
                            @foreach ($branch as $value)
                                <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <label class="form-control-label">Select Terminal</label>
                        <i id="btn_depart" class="icofont icofont-eraser mt-2 f-right text-success" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Clear All"
                            onclick="clearControl('terminal')"></i>
                        <select id="terminal" name="terminal" data-placeholder="Select Terminal"
                            class="f-right select2" multiple>
                            <option selected value="all">All</option>
                        </select>
                    </div>
                    @if (session('roleId') != 20 && session('roleId') != 19)
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-control-label">Select Customer</label>
                            <select id="customer" name="customer" data-placeholder="Select Customer"
                                class="f-right select2">
                                <option value="">Select Customer</option>
                                @foreach ($customer as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-control-label">Select Sales Tax</label>
                            <select id="sales_tax" name="sales_tax" data-placeholder="Select Sales Tax"
                                class="f-right select2">
                                <option value="">Select Sales Tax</option>
                                <option value="fbr">FBR</option>
                                <option value="srb">SRB</option>
                            </select>
                        </div>

                        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                            <label class="form-control-label">Select Sales Person</label>
                            <select id="orderserviceprovider" name="orderserviceprovider"
                                data-placeholder="Select Sales Person" class="f-right select2">
                                <option value="all">All</option>
                                @foreach ($serviceproviders as $provider)
                                    <option value="{{ $provider->serviceprovideruser->user_id }}">
                                        {{ $provider->provider_name }}</option>
                                @endforeach
                            </select>
                        </div>

                    @endif
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <label class="form-control-label">Select Type</label>
                        <select id="type" name="type" data-placeholder="Select Type"
                            {{ in_array(session('roleId'), [19, 20]) ? 'disabled' : '' }} class="f-right select2">
                            <option value="declaration">Declaration</option>
                            <option {{ in_array(session('roleId'), [19, 20]) ? 'selected' : '' }} value="datewise">
                                Datewise</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
                        <label class="form-control-label">Select Category</label>
                        <select id="category" name="category" data-placeholder="Select Category"
                            class="f-right select2">
                            <option value="all">All</option>
                            <option value="0">POS</option>
                            <option value="1">Website</option>
                        </select>
                    </div>
                </div>

                <div class="row">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 f-right">
                        <label class="form-control-label"></label>
                        <button type="button" onclick="clearSearchFields()"
                            class="btn btn-info waves-effect waves-light m-t-25 m-r-10 f-right">
                            <i class="icofont icofont-file-pdf"> </i>Clear All
                        </button>

                        <button type="button" id="btnExcel"
                            class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right">
                            <i class="icofont icofont-file-excel"> </i>Excel Export
                        </button>
                        @if (session('roleId') != 20 && session('roleId') != 19)
                            <button type="button" id="btnPdf"
                                class="btn btn-danger waves-effect waves-light m-t-25 m-r-10 f-right">
                                <i class="icofont icofont-file-pdf"> </i>PDF Export
                            </button>
                        @endif
                        <button type="button" id="fetch"
                            class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right">
                            <i class="icofont icofont-ui-check"> </i>Fetch
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header p-2">
                <div class="col-md-6 py-1">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="chLine"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 py-1">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="chBarOne"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header p-2">
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Total Orders</span>
                        <h2 class="dashboard-total-products" id="totalorders">0</h2>
                        <span class="label label-lg label-info fs-5" id="totalamount">Orders</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Pending Orders</span>
                        <h2 class="dashboard-total-products" id="pendingorders">0</h2>
                        <span class="label label-lg label-danger fs-5" id="totalpendingamount">Pending</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Processing Orders</span>
                        <h2 class="dashboard-total-products" id="processingorders">0</h2>
                        <span class="label label-lg label-warning fs-5" id="totalprocessingamount">Processing</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Void Orders</span>
                        <h2 class="dashboard-total-products" id="voidorders">0</h2>
                        <span class="label label-lg label-danger fs-5" id="totalvoidamount">Void</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                {{-- @if (session('company_id') == 102) --}}
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Dispatch Orders</span>
                        <h2 class="dashboard-total-products" id="dispatchorders">0</h2>
                        <span class="label label-lg label-info fs-5" id="totaldispatchamount">Dispatch</span>
                        <div class="side-box">
                            <i class="ti-package text-info-color"></i>
                        </div>
                    </div>
                </div>
                {{-- @else --}}
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Sales Return</span>
                        <h2 class="dashboard-total-products" id="salesreturnorders">0</h2>
                        <span class="label label-lg label-danger fs-5" id="totalsalesreturnamount">Sales Return</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Delivered Orders</span>
                        <h2 class="dashboard-total-products" id="deliveredorders">0</h2>
                        <span class="label label-lg label-success fs-5" id="totaldeliveredamount">Delivered</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span id="labeltaxname">FBR/SRB Orders</span>
                        <h2 class="dashboard-total-products" id="totaltaxorders">0</h2>
                        <span class="label label-lg label-primary fs-5" id="totaltaxamount">FBR/SRB Amount</span>
                        <div class="side-box">
                            <i class="ti-package text-primary-color"></i>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="card dashboard-product">
                        <span>Cancel Orders</span>
                        <h2 class="dashboard-total-products" id="cancelorders">0</h2>
                        <span class="label label-danger">Cancel</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="card-block">
                    <div id="table_data" class="overflow-x-auto" style="overflow-y:hidden;">
                        {{-- @include('partials.orders_table') --}}
                    </div>
                </div>
            </div>

            <div class="modal fade modal-flex in" id="sp-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 id="mod-title" class="modal-title">Select Service Provider</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="orderidforsp" name="orderidforsp" />
                                <div class="col-md-12">
                                    <label class="form-control-label">Select Service Provider</label>
                                    <select id="serviceprovider" name="serviceprovider"
                                        data-placeholder="Select Service Provider" class="f-right select2">
                                        <option value="">Select Service Provider</option>
                                        @foreach ($serviceproviders as $provider)
                                            <option value="{{ $provider->id }}">{{ $provider->provider_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 m-t-5">
                                    <label class="form-control-label">Enter Narration</label>
                                    <textarea id="narration" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_assign"
                                class="btn btn-success waves-effect waves-light">Assign</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade modal-flex in" id="sales-person-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 id="mod-title" class="modal-title">Select Sales Person</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="orderid" name="orderid" />
                                <div class="col-md-12">
                                    <label class="form-control-label">Select Sales Person</label>
                                    <select id="salespersonmodal" name="salespersonmodal"
                                        data-placeholder="Select Sales Person" class="f-right select2">
                                        <option value="">Select Sales Person</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_assign_sp"
                                class="btn btn-success waves-effect waves-light">Assign</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade modal-flex in" id="assign-modal" tabindex="-1" role="dialog"
                style="overflow-y:auto;">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 id="mod-title" class="modal-title">Pickup Person Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" id="orderidforpicker" name="orderidforpicker" />
                                <div class="col-md-12">
                                    <label class="form-control-label">Select Branch</label>
                                    <select id="branchmodal" name="branchmodal" data-placeholder="Select Branch"
                                        class="f-right">
                                        @foreach ($branch as $value)
                                            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 m-t-2">
                                    <label class="form-control-label">Enter Name</label>
                                    <input type="text" name="namemodal" id="namemodal" class="form-control" />
                                </div>
                                <div class="col-md-12 m-t-2">
                                    <label class="form-control-label">Enter Mobile</label>
                                    <input type="text" name="mobilemodal" id="mobilemodal" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_submit"
                                class="btn btn-success waves-effect waves-light">Assign</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade modal-flex in" id="void-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <input type="hidden" name="voidId" id="voidId" class="form-control" />
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Void Receipt</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Reason :</label>
                                        <input type="text" name="reason" id="reason" class="form-control" />
                                        <span id="reason_message" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success waves-effect waves-light"
                                onClick="saveVoid()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade modal-flex in" id="delivered-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <input type="hidden" name="orderId" id="orderId" class="form-control" />
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Mark as Delivered</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Delivery Date :</label>
                                        <input type="date" name="delivery_date" id="delivery_date"
                                            class="form-control" />
                                        <span id="delivery_mesasge" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light"
                                onClick="saveDelivery()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();
        getTerminal();

        $("#date").val('{{ date('Y-m-d') }}')
        $("#rpdate").val('{{ date('Y-m-d') }}')

        $('#date,#rpdate,#del_from,#del_to').bootstrapMaterialDatePicker({
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



        $('#checkbox').change(function() {
            if ($('#checkbox').is(":checked")) {
                $('#from').css("display", "none");
                $('#to').css("display", "none");
                $('#deliveryfrom').css("display", "block");
                $('#deliveryto').css("display", "block");
                $('#rpdate').val('');
                $('#date').val('');
                $("#type").val("datewise").change();
            } else {
                $('#from').css("display", "block");
                $('#to').css("display", "block");
                $('#deliveryfrom').css("display", "none");
                $('#deliveryto').css("display", "none");
                $("#type").val("declaration").change();
            }
        });
        $('#from').css("display", "block");
        $('#to').css("display", "block");
        $('#deliveryfrom').css("display", "none");
        $('#deliveryto').css("display", "none");


        // $(document).ready(function(){

        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetch_data(page)

        });

        function showLoader(divName) {
            $('#' + divName).empty();
            $('#' + divName).append(
                "<div class='position-relative w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>" +
                "<div class='spinner-border text-dark' role='status'>" +
                "<span class='visually-hidden'>Loading...</span></div></div>"
            )
        }
        var count = 0;
        function fetch_data(page) {
            count++;
            
            showLoader("table_data")
            $.ajax({
                url: "{{ url('get-pos-orders-new') }}" + "?page=" + page,
                type: 'GET',
                data: {
                    machineOrderNo: $('#machine_order_no').val(),
                    customerNo: $('#customer_number').val(),
                    payMode: $('#paymentmode').val(),
                    first: $('#rpdate').val(),
                    second: $('#date').val(),
                    customer: $('#customer').val(),
                    receipt: $('#receipt').val(),
                    mode: $('#ordermode').val(),
                    status: $('#orderstatus').val(),
                    deli_from: $('#del_from').val(),
                    deli_to: $('#del_to').val(),
                    branch: $('#branch').val(),
                    terminal: $('#terminal').val(),
                    order_no: $('#order_no').val(),
                    sales_tax: $('#sales_tax').val(),
                    type: $('#type').val(),
                    salesperson: $('#orderserviceprovider').val(),
                    category: $('#category').val(),
                    height: (count == 1 ? 100 : 200),
                },
                success: function(data) {
                    $('#table_data').empty();
                    $('#table_data').html(data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#table_data").empty();
                }
            });
        }
        fetch_data(1);

        $("#fetch").click(function() {
            fetch_data(1);
        });



        // });

        function clearSearchFields() {
            $('#paymentmode').val("").change()
            $('#rpdate').val("")
            $('#date').val("")
            $('#customer').val("").change()
            $('#receipt').val("")
            $('#ordermode').val("").change()
            $('#del_from').val("")
            $('#del_to').val("")
            $('#branch').val("").change()
            $('#terminal').val("").change()
            $('#order_no').val("")
            $('#sales_tax').val("").change();
            $('#orderstatus').val("").change()
        }

        function assignToServiceProviderModal(receiptId) {
            $('#sp-modal').modal("show");
            $("#serviceprovider").select2({
                dropdownParent: $('#sp-modal')
            });
            $("#orderidforsp").val(receiptId);
        }

        function assignSalesPerson(receiptId, branch) {
            $('#sales-person-modal').modal("show");
            $("#salespersonmodal").select2({
                dropdownParent: $('#sales-person-modal')
            });
            $("#orderid").val(receiptId);
            getServiceProviderByBranch(branch, "#salespersonmodal");
        }



        function assignToBranchModal(receiptId) {
            $('#assign-modal').modal("show");
            $("#branchmodal").select2({
                dropdownParent: $('#assign-modal')
            });
            $("#orderidforpicker").val(receiptId);
        }

        $("#btn_assign").click(function() {
            let sp = $("#serviceprovider").val();
            let receiptId = $("#orderidforsp").val();
            if (sp == "") {
                alert("Please Select Service Provider")
            } else {
                $.ajax({
                    url: "{{ url('/assign-service-provider') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        receiptId: receiptId,
                        sp: sp,
                        narration: $("#narration").val()
                    },
                    dataType: 'json',
                    success: function(result) {
                        // console.log(result);
                        if (result.status == 200) {
                            $('#sp-modal').modal("hide");
                            location.reload();
                        }
                    }
                });
            }
        })


        $("#btn_assign_sp").click(function() {
            let sp = $("#salespersonmodal").val();
            let receiptId = $("#orderid").val();
            if (sp == "") {
                alert("Please Select Sales Person")
            } else {
                $.ajax({
                    url: "{{ url('/assign-sales-person') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        receiptId: receiptId,
                        sp: sp,
                    },
                    dataType: 'json',
                    success: function(result) {
                        console.log(result);
                        if (result.status == 200) {
                            $('#sales-person-modal').modal("hide");
                            fetch_data(1);
                        }
                    }
                });
            }
        })

        $("#btn_submit").click(function() {
            let name = $("#namemodal").val();
            let mobile = $("#mobilemodal").val();
            let receiptId = $("#orderidforpicker").val();
            let branch = $("#branchmodal").val();
            if (name == "") {
                alert("Please enter name")
            } else if (mobile == "") {
                alert("Please enter mobile")
            } else if (branch == "") {
                alert("Please enter branch")
            } else {
                $.ajax({
                    url: "{{ url('/change-order-status-with-logs') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        receipt: receiptId,
                        status: 10,
                        name: name,
                        mobile: mobile,
                        comments: "NULL",
                        branch: branch
                    },
                    dataType: 'json',
                    success: function(result) {
                        console.log(result);
                        if (result.status == 200) {
                            $('#assign-modal').modal("hide");
                            fetch_data(1);
                        }
                    }
                });
            }
        })

        function showReceipt(ReceiptNo) {
            orderSeen(ReceiptNo);
        }

        function showOrderDetails(ReceiptId) {
            window.open("{{ url('order-detail') }}/" + ReceiptId)
        }

        async function orderSeen(ReceiptNo) {
            $.ajax({
                url: "{{ url('/order-seen') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    receiptNo: ReceiptNo
                },
                dataType: 'json',
                success: function(result) {
                    if (result != "") {
                        openReceipt(ReceiptNo)
                    }
                }
            });
            openReceipt(ReceiptNo)
        }

        function openReceipt(ReceiptNo) {
            window.open("{{ url('print') }}" + "/" + ReceiptNo)
        }

        $("#branch").change(function() {
            getTerminal();
            getServiceProviderByBranch($("#branch").val(), "#orderserviceprovider");
            clearAllFromControl($(this).val(), 'branch');
        })

        $("#terminal").change(function() {
            clearAllFromControl($(this).val(), 'terminal');
        })
        $("#orderstatus").change(function() {
            clearAllFromControl($(this).val(), 'orderstatus');
        })



        function clearAllFromControl(values, controlId) {
            var selectedValues = values;
            // If "All" is selected, unselect other options
            if (selectedValues.includes('all')) {
                // Deselect other options
                $('#' + controlId + ' option[value="all"]').prop('selected', false);
                // $('#branch option').not('[value="all"]').prop('selected', false);
            } else {
                // If any other option is selected, unselect "All"
                $('#' + controlId + ' option[value="all"]').prop('selected', false);
            }

            if (!selectedValues || selectedValues.length === 0) {
                // $('#orderserviceprovider option[value="all"]').prop('selected', true);
                $("#" + controlId).val('all').trigger('change.select2')
            }
            // Update the select input manually to trigger the change
            $('#' + controlId).trigger('change.select2');

        }

        function getTerminal() {
            $.ajax({
                url: "{{ route('getTerminals') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: $("#branch").val(),
                },
                beforeSend: function() {
                    $('#loader').removeClass('hidden')
                },
                success: function(result) {
                    // console.log("Fetching Results",result);
                    $('#loader').addClass('hidden')
                    if (result != 0) {
                        $("#terminal").empty();
                        $("#terminal").append('<option selected value="all">All</option>');
                        $.each(result.terminal, function() {
                            $("#terminal").append('<option value="' + this.terminal_id + '"+>' + this
                                .terminal_name + '</option>');
                        });
                    }
                },
                complete: function() {
                    $('#loader').addClass('hidden')
                },
                error: function(error) {
                    $('#loader').addClass('hidden')
                    $("#btn_search_report").attr("disabled", false);
                    console.log("Error", error);
                },

            });
        }

        function getServiceProviderByBranch(branch, controlId) {
            // let branch = (branch != "" ? $("#branch").val() : branch);
            $.ajax({
                url: "{{ route('sp.branch') }}",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    branch: branch,
                },
                success: function(result) {
                    console.log("Fetching Providers", result);
                    if (result != 0) {
                        $(controlId).empty();
                        $(controlId).append('<option value="all">All</option>');
                        $.each(result.providers, function() {
                            $(controlId).append('<option value="' + this.serviceprovideruser.user_id +
                                '"+>' + this
                                .provider_name + '</option>');
                        });
                    }
                },
                error: function(error) {
                    console.log("Error", error);
                },

            });
        }

        function voidReceipt(id) {
            $("#voidId").val(id);
            $("#void-modal").modal("show");
            // $('.alert-confirm').on('click',function(){

            // swal({
            // title: "Are you sure?",
            // text: "Receipt will be void ??!",
            // type: "warning",
            // showCancelButton: true,
            // confirmButtonClass: "btn-danger",
            // confirmButtonText: "delete it!",
            // cancelButtonText: "cancel plx!",
            // closeOnConfirm: false,
            // closeOnCancel: false
            // },
            // function(isConfirm){
            // if(isConfirm){
            // $.ajax({
            // url: "{{ url('make-receipt-void') }}",
            // type: 'POST',
            // data:{_token:"{{ csrf_token() }}",id:id},
            // dataType:"json",
            // success:function(resp){
            // if(resp.status == 200){
            // swal({
            // title: "Deleted",
            // text: resp.message,
            // type: "success"
            // },function(isConfirm){
            // if(isConfirm){
            // fetch_data(1)
            // window.location="{{ route('vendors.index') }}";
            // }
            // });
            // }
            // }

            // });
            // }else {
            // swal("Cancelled", "Your receipt is safe :)", "error");
            // }
            // });
            // });
        }

        function saveVoid() {
            $("#reason_mesasge").html("");
            if ($("#reason").val() == "") {
                $("#reason_message").html("Please select reason");
            } else {
                $.ajax({
                    url: "{{ url('make-receipt-void') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $("#voidId").val(),
                        reason: $("#reason").val()
                    },
                    dataType: "json",
                    success: function(resp) {
                        if (resp.status == 200) {
                            swal({
                                title: "Deleted",
                                text: resp.message,
                                type: "success"
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    fetch_data(1)
                                    // window.location="{{ route('vendors.index') }}";
                                    $("#reason_mesasge").html("");
                                    $("#void-modal").modal("hide");
                                    $("#voidId").val("");
                                    $("#reason").val("")
                                }
                            });
                            $("#voidId").val("");
                            $("#reason").val("")
                        }
                    }

                });
            }
        }

        function deliveredReceipt(id) {
            $("#orderId").val(id);
            $("#delivered-modal").modal("show");
        }

        function saveDelivery() {
            $("#delivery_mesasge").html("");
            if ($("#delivery_date").val() == "") {
                $("#delivery_mesasge").html("Please select Delivery Date");
            } else {
                $.ajax({
                    url: "{{ url('make-receipt-delivered') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $("#orderId").val(),
                        reason: $("#delivery_date").val()
                    },
                    dataType: "json",
                    success: function(resp) {
                        if (resp.status == 200) {
                            swal({
                                title: "Delivered",
                                text: resp.message,
                                type: "success"
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    fetch_data(1)
                                    // window.location="{{ route('vendors.index') }}";
                                    $("#delivery_mesasge").html("");
                                    $("#delivered-modal").modal("hide");
                                    $("#orderId").val("")
                                }
                            });
                        }
                    }

                });
            }
        }

        $("#btnExcel").click(function() {
            if ($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == '') {
                $('input[name="fromdate"]').focus();
                $("#alert_fromdate").text('Please select the date');
            } else {
                window.open("{{ url('reports/excel-export-orders-report') }}" + "?fromdate=" + $("#rpdate").val() +
                    "&todate=" + $("#date").val() + "&branch=" + $("#branch").val() + "&terminal=" + $(
                        "#terminal").val() + "&customer=" + $("#customer").val() + "&paymentmode=" + $(
                        "#paymentmode").val() + "&ordermode=" + $("#ordermode").val() + "&type=" + $("#type")
                    .val() + "&status=" + $("#orderstatus").val() + "&receipt=" + $("#receipt").val() +
                    "&machineOrderNo=" + $("#machine_order_no").val() + "&order_no=" + $("#order_no").val() +
                    "&report=excel&salesperson=" + $('#orderserviceprovider').val() + "&category=" + $(
                        '#category').val());
            }
        })

        $("#btnPdf").click(function() {
            if ($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == '') {
                $('input[name="fromdate"]').focus();
                $("#alert_fromdate").text('Please select the date');
            } else {
                window.open("{{ url('reports/excel-export-orders-report') }}" + "?fromdate=" + $("#rpdate").val() +
                    "&todate=" + $("#date").val() + "&branch=" + $("#branch").val() + "&terminal=" + $(
                        "#terminal").val() + "&customer=" + $("#customer").val() + "&paymentmode=" + $(
                        "#paymentmode").val() + "&ordermode=" + $("#ordermode").val() + "&type=" + $("#type")
                    .val() + "&status=" + $("#orderstatus").val() + "&receipt=" + $("#receipt").val() +
                    "&machineOrderNo=" + $("#machine_order_no").val() + "&order_no=" + $("#order_no").val() +
                    "&report=pdf&salesperson=" + $('#orderserviceprovider').val() + "&category=" + $(
                        '#category').val());
            }

        })

        function clearControl(controlId) {
            $("#" + controlId).val('all').trigger('change.select2')
        }

        $("#category").change(function() {
            if ($(this).val() == 1) {
                $("#type").val("datewise").change();
            } else {
                $("#type").val("declaration").change();
            }
        })

        var colors = ['#007bff', '#28a745', '#333333', '#c3e6cb', '#dc3545', '#6c757d'];
        var xaxis ;
        var data ;

    </script>
@endsection
