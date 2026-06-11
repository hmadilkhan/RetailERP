@extends('layouts.master-tailwind')

@section('title', 'Orders')
@section('page_title', 'Orders Workspace')
@section('page_subtitle', 'Search, filter, review, export, and manage POS and website orders from one focused screen.')

@section('breadcrumtitle', 'Orders Panel')

@section('navbranchoperation', 'active')
@section('navorder', 'active')

@section('content')
    <section class="orders-v2-page">
        <div class="card orders-filter-card">
            <div class="card-header">
                <div class="orders-section-heading">
                    <div>
                        <span class="orders-eyebrow">Advanced Filters</span>
                        <h3>Order Search</h3>
                    </div>
                    <label class="orders-toggle">
                        <input type="checkbox" id="checkbox">
                        <span>Delivery orders</span>
                    </label>
                </div>
                <div class="row orders-filter-grid">
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
                    <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12" style="">
                        <label class="form-control-label">Select Department</label>
                        <i id="btn_depart" class="icofont icofont-eraser mt-2 f-right text-success" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Clear All"
                            onclick="clearControl('department')"></i>
                        <select id="department" name="department" data-placeholder="Select Department"
                            class="f-right select2" multiple>
                            <option selected value="all">All</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->department_id }}">
                                    {{ $department->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row orders-action-row">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 orders-actions">
                        <label class="form-control-label"></label>
                         <button type="button" id="fetch"
                            class="h-10 rounded-lg bg-erp px-4 text-sm font-bold text-white transition hover:bg-erp-dark">
                            <i class="icofont icofont-ui-check"> </i>Fetch
                        </button>
                        
                        <button type="button" id="btnExcel"
                            class="h-10 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white transition hover:bg-emerald-700">
                            <i class="icofont icofont-file-excel"> </i>Excel Export
                        </button>
                        @if (session('roleId') != 20 && session('roleId') != 19)
                            <button type="button" id="btnPdf"
                                class="h-10 rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition hover:bg-rose-700">
                                <i class="icofont icofont-file-pdf"> </i>PDF Export
                            </button>
                        @endif
                        <button type="button" onclick="clearSearchFields()"
                            class="h-10 rounded-lg bg-gray-400 px-4 text-sm font-bold text-white transition hover:bg-gray-800">
                            <i class="icofont icofont-eraser"> </i>Clear All
                        </button>
                       
                    </div>
                </div>
            </div>
        </div>
        <div class="card" id="graphDiv">
            <div class="card-header p-2">
                <div class="col-md-6 py-1">
                    <div class="card orders-chart-card">
                        <div class="card-body">
                            <div class="orders-chart-head">
                                <div>
                                    <span class="orders-chart-kicker">Order activity</span>
                                    <h4>Orders by Hour</h4>
                                    <p>Track how order volume moves across the selected time range.</p>
                                </div>
                                <div class="orders-chart-metrics">
                                    <div class="orders-chart-metric">
                                        <span>Peak Slot</span>
                                        <strong id="ordersVolumePeak">-</strong>
                                    </div>
                                    <div class="orders-chart-metric">
                                        <span>Avg / Slot</span>
                                        <strong id="ordersVolumeAvg">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="loader" style="display:none;">
                                <div class="orders-loading orders-loading-compact">
                                    <div class="orders-loading-ring"></div>
                                    <div class="orders-loading-title">Loading chart</div>
                                </div>
                            </div>
                            <div class="orders-chart-canvas-wrap">
                                <canvas class="graph" id="chLine"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 py-1">
                    <div class="card orders-chart-card">
                        <div class="card-body">
                            <div class="orders-chart-head">
                                <div>
                                    <span class="orders-chart-kicker">Revenue flow</span>
                                    <h4>Sales by Hour</h4>
                                    <p>Compare hourly order value and spot the strongest earning window.</p>
                                </div>
                                <div class="orders-chart-metrics">
                                    <div class="orders-chart-metric">
                                        <span>Total Sales</span>
                                        <strong id="ordersRevenueTotal">-</strong>
                                    </div>
                                    <div class="orders-chart-metric">
                                        <span>Best Hour</span>
                                        <strong id="ordersRevenuePeak">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="loader" style="display:none;">
                                <div class="orders-loading orders-loading-compact">
                                    <div class="orders-loading-ring"></div>
                                    <div class="orders-loading-title">Loading chart</div>
                                </div>
                            </div>
                            <div class="orders-chart-canvas-wrap">
                                <canvas class="graph" id="chBarOne"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card orders-summary-card">
            <div class="card-header p-2">
                <div class="loader" style="display:none;">
                    <div class="orders-loading orders-loading-inline">
                        <div class="orders-loading-ring"></div>
                        <div>
                            <div class="orders-loading-title">Updating summary</div>
                            <div class="orders-loading-copy">Refreshing order totals</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-total">
                        <span>Total Orders</span>
                        <h2 class="dashboard-total-products" id="totalorders">0</h2>
                        <span class="label label-lg label-info fs-5" id="totalamount">Orders</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-pending">
                        <span>Pending Orders</span>
                        <h2 class="dashboard-total-products" id="pendingorders">0</h2>
                        <span class="label label-lg label-danger fs-5" id="totalpendingamount">Pending</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-processing">
                        <span>Processing Orders</span>
                        <h2 class="dashboard-total-products" id="processingorders">0</h2>
                        <span class="label label-lg label-warning fs-5" id="totalprocessingamount">Processing</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-void">
                        <span>Void Orders</span>
                        <h2 class="dashboard-total-products" id="voidorders">0</h2>
                        <span class="label label-lg label-danger fs-5" id="totalvoidamount">Void</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                {{-- @if (session('company_id') == 102) --}}
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-dispatch">
                        <span>Dispatch Orders</span>
                        <h2 class="dashboard-total-products" id="dispatchorders">0</h2>
                        <span class="label label-lg label-info fs-5" id="totaldispatchamount">Dispatch</span>
                        <div class="side-box">
                            <i class="ti-package text-info-color"></i>
                        </div>
                    </div>
                </div>
                {{-- @else --}}
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-return">
                        <span>Sales Return</span>
                        <h2 class="dashboard-total-products" id="salesreturnorders">0</h2>
                        <span class="label label-lg label-danger fs-5" id="totalsalesreturnamount">Sales Return</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-delivered">
                        <span>Delivered Orders</span>
                        <h2 class="dashboard-total-products" id="deliveredorders">0</h2>
                        <span class="label label-lg label-success fs-5" id="totaldeliveredamount">Delivered</span>
                        <div class="side-box">
                            <i class="ti-package text-warning-color"></i>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6 graph">
                    <div class="card dashboard-product orders-widget orders-widget-tax">
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
        <div class="card orders-results-card">
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
                                        class="f-right select2">
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
                            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button> --}}
                            <h4 class="modal-title">Void Order(s)</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Reason :</label>
                                        <textarea id="reason" class="form-control"></textarea>
                                        {{-- <input type="text" name="reason" id="reason" class="form-control" /> --}}
                                        <span id="reason_message" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="btnMarkVoid" class="btn btn-danger waves-effect waves-light"
                                onClick="saveVoid()">Void</button>
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
            <div class="modal fade modal-flex in" id="discount-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <input type="hidden" name="orderDiscountId" id="orderDiscountId" class="form-control" />
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between">
                            <h4 class="modal-title">Discount Order </h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Enter Discount Amount :</label>
                                        <input type="text" name="discountamount" id="discountamount"
                                            class="form-control" />
                                        <span id="discountamount_message" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Reason :</label>
                                        <textarea rows="3" type="text" name="discountreason" id="discountreason" class="form-control"></textarea>
                                        <span id="discountreason_message" class="text-danger"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success waves-effect waves-light"
                                onClick="applyDiscount()">Submit</button>
                            <button type="button" data-dismiss="modal" aria-label="Close"
                                class=" btn btn-danger waves-effect waves-light"
                                onclick="$('#discount-modal').modal('hide');">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css_code')

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css"
        integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        html,
        body {
            height: auto !important;
            min-height: 100% !important;
            overflow-y: auto !important;
        }

        body {
            min-height: 100vh !important;
        }

        .orders-v2-page {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .orders-v2-page .card {
            width: 100%;
            border: 1px solid #d8e1ec !important;
            border-radius: 8px !important;
            background: #fff !important;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06) !important;
        }

        .orders-v2-page .card-header,
        .orders-v2-page .card-block,
        .orders-v2-page .card-body {
            background: transparent !important;
        }

        .orders-filter-card .card-header {
            padding: 1.25rem !important;
        }

        .orders-section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #edf2f7;
        }

        .orders-eyebrow {
            display: block;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .orders-section-heading h3 {
            margin: 0.2rem 0 0;
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0;
        }

        .orders-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
            padding: 0.55rem 0.75rem;
            border: 1px solid #d8e1ec;
            border-radius: 8px;
            background: #f8fafc;
            color: #334155;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
        }

        .orders-toggle input {
            margin: 0;
        }

        .orders-filter-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
            margin: 0 !important;
        }

        .orders-filter-grid > [class*="col-"] {
            width: auto;
            max-width: none;
            float: none;
            padding: 0 !important;
            grid-column: span 3;
        }

        .orders-filter-grid .form-group,
        .orders-filter-grid label {
            margin-bottom: 0.35rem;
        }

        .orders-v2-page .form-control-label {
            color: #475569;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .orders-v2-page .form-control,
        .orders-v2-page select {
            min-height: 2.5rem;
            border-color: #d8e1ec !important;
            border-radius: 8px !important;
            color: #334155;
            font-size: 0.875rem;
        }

        .orders-v2-page .form-control:focus {
            border-color: #4CAF50 !important;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.14) !important;
        }

        .orders-v2-page .select2-container {
            width: 100% !important;
        }

        .orders-v2-page .select2-container .select2-selection--single,
        .orders-v2-page .select2-container .select2-selection--multiple {
            min-height: 2.5rem;
            border: 1px solid #d8e1ec;
            border-radius: 8px;
            display: flex;
            align-items: center;
            background: #fff;
        }

        .orders-v2-page .select2-container--default.select2-container--focus .select2-selection--multiple,
        .orders-v2-page .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.14);
        }

        .orders-v2-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            width: 100%;
            color: #334155;
            line-height: 2.4rem;
            padding-left: 0.75rem;
        }

        .orders-v2-page .select2-container--default .select2-selection--multiple {
            align-items: flex-start;
            padding: 0.25rem 0.35rem;
        }

        .orders-v2-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin: 0.12rem;
            border: 1px solid #d8e1ec;
            border-radius: 6px;
            background: #f8fafc;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .orders-v2-page .icofont-eraser {
            cursor: pointer;
        }

        .orders-action-row {
            margin: 1.1rem 0 0 !important;
        }

        .orders-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 0.9rem !important;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 70%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .orders-actions .orders-action-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin: 0 !important;
            border-radius: 8px !important;
            font-weight: 800 !important;
            min-height: 2.75rem;
            min-width: 8.75rem;
            padding: 0.65rem 1.05rem;
            border: 1px solid transparent !important;
            letter-spacing: 0;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }

        .orders-actions .orders-action-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.7rem;
            height: 1.7rem;
            border-radius: 8px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.22);
        }

        .orders-actions .orders-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.14);
        }

        .orders-actions .orders-action-btn:disabled {
            transform: none;
            box-shadow: none;
            opacity: 0.65;
            cursor: not-allowed;
        }

        .orders-action-fetch {
            color: #fff !important;
            background: linear-gradient(135deg, #16a34a 0%, #4CAF50 55%, #22c55e 100%) !important;
            box-shadow: 0 12px 24px rgba(34, 197, 94, 0.24);
        }

        .orders-action-excel {
            color: #166534 !important;
            border-color: #bbf7d0 !important;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%) !important;
        }

        .orders-action-excel i {
            color: #fff;
            background: #16a34a !important;
        }

        .orders-action-pdf {
            color: #9f1239 !important;
            border-color: #fecdd3 !important;
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%) !important;
        }

        .orders-action-pdf i {
            color: #fff;
            background: #e11d48 !important;
        }

        .orders-action-clear {
            color: #475569 !important;
            border-color: #d8e1ec !important;
            background: #fff !important;
        }

        .orders-action-clear i {
            color: #475569;
            background: #f1f5f9 !important;
        }

        .orders-loading {
            min-height: 260px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.9rem;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(76, 175, 80, 0.08), rgba(15, 23, 42, 0.02)),
                #fff;
            color: #334155;
        }

        .orders-loading-compact {
            min-height: 220px;
            flex-direction: column;
        }

        .orders-loading-inline {
            min-height: 92px;
            justify-content: flex-start;
            padding: 1rem;
        }

        .orders-loading-ring {
            width: 2.5rem;
            height: 2.5rem;
            border: 3px solid #d8e1ec;
            border-top-color: #4CAF50;
            border-radius: 999px;
            animation: orders-spin 0.8s linear infinite;
            flex: 0 0 auto;
        }

        .orders-loading-title {
            color: #0f172a;
            font-size: 0.9rem;
            font-weight: 900;
            letter-spacing: 0.02em;
        }

        .orders-loading-copy {
            margin-top: 0.15rem;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
        }

        @keyframes orders-spin {
            to {
                transform: rotate(360deg);
            }
        }

        #graphDiv .card-header {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            padding: 1rem !important;
        }

        .orders-chart-card {
            overflow: hidden;
        }

        .orders-chart-card .card-body {
            padding: 1rem !important;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
        }

        .orders-chart-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .orders-chart-kicker {
            display: inline-block;
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .orders-chart-head h4 {
            margin: 0.3rem 0 0;
            color: #0f172a;
            font-size: 1.15rem;
            font-weight: 900;
        }

        .orders-chart-head p {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 600;
            line-height: 1.55;
        }

        .orders-chart-metrics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.65rem;
            min-width: min(100%, 15rem);
        }

        .orders-chart-metric {
            padding: 0.75rem 0.85rem;
            border: 1px solid rgba(216, 225, 236, 0.95);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
        }

        .orders-chart-metric span {
            display: block;
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .orders-chart-metric strong {
            display: block;
            margin-top: 0.35rem;
            color: #0f172a;
            font-size: 0.98rem;
            font-weight: 900;
            line-height: 1.2;
        }

        #graphDiv .col-md-6 {
            width: auto;
            max-width: none;
            padding: 0 !important;
        }

        .orders-chart-canvas-wrap {
            min-height: 280px;
            padding: 0.25rem;
            border: 1px solid rgba(216, 225, 236, 0.85);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.92);
        }

        #graphDiv canvas {
            min-height: 260px;
            width: 100% !important;
        }

        .orders-summary-card .card-header {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            padding: 1rem !important;
        }

        .orders-summary-card .card-header > .loader {
            grid-column: 1 / -1;
        }

        .orders-summary-card .graph {
            width: auto;
            max-width: none;
            padding: 0 !important;
        }

        .orders-v2-page .dashboard-product.orders-widget {
            position: relative;
            min-height: 148px;
            padding: 1.05rem;
            border-radius: 8px !important;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgba(216, 225, 236, 0.9) !important;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08) !important;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .orders-v2-page .dashboard-product.orders-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 42px rgba(15, 23, 42, 0.13) !important;
            border-color: rgba(76, 175, 80, 0.35) !important;
        }

        .orders-widget::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -2;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
        }

        .orders-widget::after {
            content: "";
            position: absolute;
            right: -2.5rem;
            top: -2.25rem;
            width: 7.5rem;
            height: 7.5rem;
            z-index: -1;
            border-radius: 999px;
            background: var(--orders-widget-glow, rgba(76, 175, 80, 0.13));
        }

        .orders-widget-total {
            --orders-widget-accent: #2563eb;
            --orders-widget-soft: #dbeafe;
            --orders-widget-glow: rgba(37, 99, 235, 0.14);
        }

        .orders-widget-pending {
            --orders-widget-accent: #e11d48;
            --orders-widget-soft: #ffe4e6;
            --orders-widget-glow: rgba(225, 29, 72, 0.13);
        }

        .orders-widget-processing {
            --orders-widget-accent: #d97706;
            --orders-widget-soft: #fef3c7;
            --orders-widget-glow: rgba(217, 119, 6, 0.14);
        }

        .orders-widget-void,
        .orders-widget-return {
            --orders-widget-accent: #7f1d1d;
            --orders-widget-soft: #fee2e2;
            --orders-widget-glow: rgba(127, 29, 29, 0.12);
        }

        .orders-widget-dispatch {
            --orders-widget-accent: #0891b2;
            --orders-widget-soft: #cffafe;
            --orders-widget-glow: rgba(8, 145, 178, 0.14);
        }

        .orders-widget-delivered {
            --orders-widget-accent: #16a34a;
            --orders-widget-soft: #dcfce7;
            --orders-widget-glow: rgba(22, 163, 74, 0.14);
        }

        .orders-widget-tax {
            --orders-widget-accent: #4f46e5;
            --orders-widget-soft: #e0e7ff;
            --orders-widget-glow: rgba(79, 70, 229, 0.14);
        }

        .orders-v2-page .dashboard-product.orders-widget span:first-child {
            display: block;
            max-width: calc(100% - 3.2rem);
            color: #64748b;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .orders-v2-page .dashboard-product.orders-widget .dashboard-total-products {
            margin: 0.65rem 0 0.55rem;
            color: #0f172a;
            font-size: 2.05rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: 0;
        }

        .orders-v2-page .dashboard-product.orders-widget .label {
            display: inline-flex;
            max-width: 100%;
            min-height: 1.9rem;
            align-items: center;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 999px;
            background: var(--orders-widget-soft);
            color: var(--orders-widget-accent);
            font-size: 0.76rem !important;
            font-weight: 900;
            line-height: 1.1;
            padding: 0.38rem 0.65rem;
            white-space: normal;
        }

        .orders-v2-page .dashboard-product.orders-widget .side-box {
            position: absolute;
            right: 1rem;
            top: 1rem;
            width: 2.55rem;
            height: 2.55rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--orders-widget-soft);
            color: var(--orders-widget-accent);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.65);
        }

        .orders-v2-page .dashboard-product.orders-widget .side-box i {
            margin: 0;
            color: var(--orders-widget-accent) !important;
            font-size: 1.2rem;
        }

        #table_data {
            min-height: 260px;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            background: #fff;
        }

        #table_data .table {
            margin-bottom: 0;
        }

        #table_data .table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f8fafc !important;
            color: #64748b !important;
            font-size: 0.72rem !important;
            font-weight: 900 !important;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .orders-results-card {
            overflow: visible;
        }

        .orders-results-card .row {
            margin: 0 !important;
        }

        .orders-results-card .card-block {
            width: 100%;
            padding: 1rem !important;
            overflow: visible;
        }

        .orders-results-card #table_data {
            overflow-y: visible !important;
        }

        .orders-results-card .table-responsive {
            overflow-y: visible;
        }

        .orders-results-card .dropdown-menu {
            z-index: 1080;
            border: 1px solid #d8e1ec;
            border-radius: 8px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
        }

        .orders-dropdown-portal {
            display: block !important;
            position: fixed !important;
            z-index: 3000 !important;
            max-height: min(420px, calc(100vh - 24px));
            overflow-y: auto;
            min-width: 15rem;
        }

        .orders-results-card .btn-group {
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .orders-results-card .btn-group .btn {
            border-radius: 8px !important;
            border: 1px solid transparent !important;
            font-size: 0.78rem;
            font-weight: 900;
            min-height: 2.25rem;
            padding: 0.45rem 0.75rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.10);
        }

        .orders-results-card .btn-group .btn-danger {
            color: #fff;
            background: linear-gradient(135deg, #be123c, #e11d48) !important;
        }

        .orders-results-card .btn-group .btn-success {
            color: #fff;
            background: linear-gradient(135deg, #15803d, #22c55e) !important;
        }

        .orders-results-card .btn-group .btn:disabled {
            box-shadow: none;
            opacity: 0.5;
        }

        .orders-v2-page .modal-content {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.24);
        }

        .orders-v2-page .modal-header {
            border-bottom: 1px solid #edf2f7;
            background: #f8fafc;
        }

        .orders-v2-page .modal-title {
            color: #0f172a;
            font-weight: 800;
        }

        .bg-success {
            background-color: #4CAF50 !important;
        }

        nav .navbar {
            position: relative;
        }

        .navbar-custom-menu {
            position: absolute;
            right: 0;
        }

        @media (max-width: 1199px) {
            .orders-filter-grid > [class*="col-"] {
                grid-column: span 4;
            }
        }

        @media (max-width: 991px) {
            .orders-filter-grid > [class*="col-"] {
                grid-column: span 6;
            }

            #graphDiv .card-header {
                grid-template-columns: 1fr;
            }

            .orders-chart-head {
                flex-direction: column;
            }

            .orders-chart-metrics {
                width: 100%;
            }

            .orders-summary-card .card-header {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575px) {
            .orders-section-heading,
            .orders-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .orders-filter-grid {
                grid-template-columns: 1fr;
            }

            .orders-filter-grid > [class*="col-"] {
                grid-column: auto;
            }

            .orders-actions .btn {
                width: 100%;
            }

            .orders-chart-metrics {
                grid-template-columns: 1fr;
            }

            .orders-summary-card .card-header {
                grid-template-columns: 1fr;
            }
        }
    </style>

@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();
        getTerminal();

        $("#date").val('{{ date('Y-m-d') }}')
        $("#rpdate").val('{{ date('Y-m-d') }}')

        // Safeguard datepicker initialization in case plugin is not loaded
        if ($.fn && $.fn.bootstrapMaterialDatePicker) {
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
        } else {
            console.warn('bootstrapMaterialDatePicker plugin not found; skipping datepicker init');
        }


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

        function positionOrdersDropdown($toggle, $menu) {
            var rect = $toggle[0].getBoundingClientRect();
            var menuWidth = Math.max($menu.outerWidth(), 240);
            var menuHeight = $menu.outerHeight();
            var viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            var viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            var left = Math.min(Math.max(12, rect.left), viewportWidth - menuWidth - 12);
            var top = rect.bottom + 8;

            if (top + menuHeight > viewportHeight - 12) {
                top = Math.max(12, rect.top - menuHeight - 8);
            }

            $menu.css({
                top: top + 'px',
                left: left + 'px',
                minWidth: menuWidth + 'px'
            });
        }

        $(document).on('show.bs.dropdown', '.orders-results-card .btn-group', function() {
            var $group = $(this);
            var $toggle = $group.find('[data-toggle="dropdown"]').first();
            var $menu = $group.children('.dropdown-menu').first();

            if (!$menu.length || $menu.hasClass('orders-dropdown-portal')) {
                return;
            }

            $menu.data('orders-dropdown-parent', $group);
            $menu.data('orders-dropdown-next', $menu.next());
            $menu.appendTo(document.body).addClass('orders-dropdown-portal');
            positionOrdersDropdown($toggle, $menu);
        });

        $(document).on('shown.bs.dropdown', '.orders-results-card .btn-group', function() {
            var $group = $(this);
            var $toggle = $group.find('[data-toggle="dropdown"]').first();
            var $menu = $('.orders-dropdown-portal').filter(function() {
                return $(this).data('orders-dropdown-parent') && $(this).data('orders-dropdown-parent')[0] === $group[0];
            }).first();

            if ($menu.length) {
                positionOrdersDropdown($toggle, $menu);
            }
        });

        $(document).on('hide.bs.dropdown', '.orders-results-card .btn-group', function() {
            var $group = $(this);
            var $menu = $('.orders-dropdown-portal').filter(function() {
                return $(this).data('orders-dropdown-parent') && $(this).data('orders-dropdown-parent')[0] === $group[0];
            }).first();

            if (!$menu.length) {
                return;
            }

            var $next = $menu.data('orders-dropdown-next');
            $menu.removeClass('orders-dropdown-portal').removeAttr('style');
            if ($next && $next.length) {
                $menu.insertBefore($next);
            } else {
                $group.append($menu);
            }
            $menu.removeData('orders-dropdown-parent orders-dropdown-next');
        });

        function showLoader(divName) {
            $('.orders-dropdown-portal').remove();
            $('#' + divName).empty();
            $('#' + divName).append(
                "<div class='orders-loading'>" +
                "<div class='orders-loading-ring'></div>" +
                "<div>" +
                "<div class='orders-loading-title'>Loading orders</div>" +
                "<div class='orders-loading-copy'>Applying filters and preparing the results table</div>" +
                "</div>" +
                "</div>"
            )
        }
        var count = 0;

        function fetch_data(page) {
            count++;

            showLoader("table_data")
            $(".loader").css("display", "block");
            $(".graph").css("display", "none");
            $(".buttons").prop('disabled', true);
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
                    department: $('#department').val(),
                    terminal: $('#terminal').val(),
                    order_no: $('#order_no').val(),
                    sales_tax: $('#sales_tax').val(),
                    type: $('#type').val(),
                    salesperson: $('#orderserviceprovider').val(),
                    category: $('#category').val(),
                    view_mode: window.ordersCurrentView || 'table',
                    height: (count == 1 ? 100 : 200),
                },
                success: function(data) {
                    $('#table_data').empty();
                    $(".loader").css("display", "none");
                    $(".graph").css("display", "block");
                    $(".buttons").prop('disabled', false);
                    $('#table_data').html(data);
                    if ($.fn.dropdown) {
                        $('#table_data [data-toggle="dropdown"]').dropdown();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $("#table_data").empty();
                    $(".buttons").prop('disabled', false);
                    $(".loader").css("display", "none");
                    $(".graph").css("display", "block");
                }
            });
        }
        fetch_data(1);

        $("#fetch").click(function() {
            fetch_data(1);
        });



        // });

        function clearSearchFields() {
            $(".buttons").prop("disabled", true);
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
            $('#orderstatus').val("").change();
            $(".buttons").prop("disabled", false);
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

        $("#department").change(function() {
            clearAllFromControl($(this).val(), 'department');
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
        }
        $("#btnMarkVoid").click(function() {
            console.log("Btn calling");

        })

        function saveVoid() {
            console.log("jdhfjsdhfj");

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
                                title: "Void",
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

        function discountReceipt(id) {
            $("#orderDiscountId").val(id);
            $("#discount-modal").modal("show");
        }

        function applyDiscount() {
            $("#discountamount_message").html("");
            $("#discountreason_message").html("");
            if ($("#discountamount").val() == "") {
                $("#discountamount_message").html("Please enter discount amount");
            } else if ($("#discountreason").val() == "") {
                $("#discountreason_message").html("Please enter reason.");
            } else {

                $.ajax({
                    url: "{{ url('apply-discount') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $("#orderDiscountId").val(),
                        amount: $("#discountamount").val(),
                        reason: $("#discountreason").val(),
                    },
                    dataType: "json",
                    success: function(resp) {
                        if (resp.status == 200) {
                            swal({
                                title: "Discount Applied",
                                text: resp.message,
                                type: "success"
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    fetch_data(1)
                                    $("#discountamount_message").html("");
                                    $("#discountreason_message").html("");
                                    $("#discount-modal").modal("hide");
                                    $("#orderDiscountId").val("");
                                    $("#discountamount").val("")
                                    $("#discountreason").val("")
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
                    "&report=excel&salesperson=" + $('#orderserviceprovider').val() + "&department=" + $("#department").val() + "&category=" + $(
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
        var xaxis;
        var data;

        // MULTIPLE CHECKBOXED LOGIC
        // Checkbox selection logic
    </script>
@endsection
