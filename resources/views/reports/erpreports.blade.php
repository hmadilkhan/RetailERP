@extends('layouts.master-layout')

@section('title','ERP Reports')

@section('breadcrumtitle','ERP Reports')

@section('naverpreport','active')


@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h1 class="card-header-text">ERP Report Dashboard</h1>
            </div>
            <div class="card-block">
                <div class="row">
                    <div id="dvprofitstandard" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Profit & Loss Standard">
                            <h4 class="text-sm-center">Profit & Loss Standard</h4>
                        </div>
                    </div>
                    <div id="dvprofitdetails" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Profit & Loss Details">
                            <h4 class="text-sm-center">Profit & Loss Details</h4>
                        </div>
                    </div>
                    <div id="dvshowreport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Inventory Valuation">
                            <h4 class="text-sm-center">Inventory Valuation</h4>
                        </div>
                    </div>

                </div>
                <br>
                <div class="row">
                    <div id="dvinventory" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Inventory Details">
                            <h4 class="text-sm-center">Inventory Details</h4>
                        </div>
                    </div>
                    <div id="dvexpensesheet" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Expense Sheet">
                            <h4 class="text-sm-center">Expense Sheet</h4>
                        </div>
                    </div>
                    <div id="dvexpensecat" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Expense By Category">
                            <h4 class="text-sm-center">Expense By Category</h4>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div id="dvsaledecleration" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Sales Decleration">
                            <h4 class="text-sm-center">Sales Declaration</h4>
                        </div>
                    </div>
                    <div id="dvitemsale" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Item Sale Database">
                            <h4 class="text-sm-center">Item Sale Database</h4>
                        </div>
                    </div>
                    <div id="dvsalereturn" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Sales Return">
                            <h4 class="text-sm-center">Sales Return</h4>
                        </div>
                    </div>


                </div>
                <br>
                <div class="row">
                    <div id="dvphysical" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Physical Inventory Sheet">
                            <h4 class="text-sm-center">Physical Inventory Sheet</h4>
                        </div>
                    </div>
                    <div id="dvstockadjustment" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Stock Adjustment">
                            <h4 class="text-sm-center">Stock Adjustment</h4>
                        </div>
                    </div>
					<div id="dvcustomeraging" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Stock Adjustment">
                            <h4 class="text-sm-center">Customer Aging</h4>
                        </div>
                    </div>
					
					
                </div>
				<br>
				<div class="row">
					<div id="dvfbrreport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="FBR Report">
                            <h4 class="text-sm-center">FBR Report</h4>
                        </div>
                    </div>
					
					<div id="dvinvoicereport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Invoice Details">
                            <h4 class="text-sm-center">Invoice Details</h4>
                        </div>
                    </div>
					
					<div id="dvsalesinvoicereport" class="col-lg-4" style="cursor: pointer;">
                        <div class="p-20 z-depth-top-0 waves-effect" data-toggle="tooltip" data-placement="top" title="Invoice Details">
                            <h4 class="text-sm-center">Sales Invoices</h4>
                        </div>
                    </div>
				</div>

            </div>
        </div>

    </section>

{{--        modals--}}
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
                                <select name="depart" id="depart" data-placeholder="Select Department" class="form-control select2"  >
                                    <option value="">Select Department</option>
                                    <option value="0">All Department</option>
                                    @if($departments)
                                        @foreach($departments as $value)
                                            <option value="{{ $value->department_id }}">{{ $value->department_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" onClick="getreport()"><i class="icofont icofont-file-pdf"> </i>Get Report</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="filter-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Filter Box
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </h4>

                </div>

                <div class="modal-body">
{{--                    hidden fields--}}
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




                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">From Date</label>
                                <input class="form-control" type="text"
                                       name="datefrom" id="datefrom" placeholder="DD-MM-YYYY" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">To Date</label>
                                <input class="form-control" type="text"
                                       name="dateto" id="dateto" placeholder="DD-MM-YYYY"/>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
					
					<div class="row" id="dvtype" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Type</label>
                                <select name="type" id="type" data-placeholder="Select Type" class="form-control select2"  >
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
                                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2"  >
                                    <option value="">Select Branch</option>
                                    @if($branches)
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
	
                    <div class="row" id="dvterminal" style="display: none;">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Select Terminal</label>
                                <select name="terminal" id="terminal" data-placeholder="Select Terminal" class="form-control select2"  >
                                    <option value="">Select Terminal</option>
                                    <option value="0">All Terminals</option>
                                    @if($terminals)
                                        @foreach($terminals as $value)
                                            <option value="{{ $value->terminal_id }}">{{ $value->terminal_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
					
                </div>
                <div class="modal-footer">
                    <button id="ExcelButton" style="display:none;" type="button" class="btn btn-success waves-effect waves-light f-right m-l-2" onClick="getExcelData()"><i class="icofont icofont-file-excel"> </i>Excel Report</button>
					<button type="button" class="btn btn-danger waves-effect waves-light" onClick="getdata()"><i class="icofont icofont-file-pdf"> </i>Get Report</button>
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
	
	
        $('#dvprofitstandard').on('click', function (){
            $('#txtprofitstandard').val(1);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
            showbranch();
            showterminal();
        });
        $('#dvprofitdetails').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtinventory').val(0);
            $('#txtprofitdetails').val(1);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
			showbranch();
            showterminal();
        });
        $('#dvinventory').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(1);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
            showterminal();
        });
        $('#dvexpensesheet').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(1);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
            showterminal();
        });
        $('#dvexpensecat').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(1);
            $('#txtsaledec').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
            showterminal();
        });
        $('#dvsaledecleration').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(1);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
            $('#filter-modal').modal('show');
			$('#ExcelButton').css('display','none');
            showterminal();
        });
        $('#dvitemsale').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(1);
            $('#txtitemsale').val(1);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','block');
			
            $('#filter-modal').modal('show');
            showterminal();
            showType();
        });
        $('#dvsalereturn').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtsalereturn').val(1);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
            showterminal();
        });
        $('#dvstockadjustment').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(0);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(1);
            $('#txtphysical').val(0);
			$('#ExcelButton').css('display','none');
            $('#filter-modal').modal('show');
            showterminal();
        });

        $('#dvphysical').on('click', function (){
            $('#txtphysical').val(1);
			$('#ExcelButton').css('display','none');
            $('#depart-modal').modal('show');
        });
		
		$('#dvfbrreport').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(1);
			$('#txttype').val(0);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
            $('#txtfbrreport').val(1);
            $('#txtinvoicereport').val(0);
            $('#filter-modal').modal('show');
			$('#ExcelButton').css('display','block');
            showterminal();
        });
		
		$('#dvinvoicereport').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(1);
			$('#txttype').val(1);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
            $('#txtfbrreport').val(0);
            $('#txtinvoicereport').val(1);
            $('#filter-modal').modal('show');
			$('#ExcelButton').css('display','block');
            showterminal();
			showType();
        });
		
		
		$('#dvsalesinvoicereport').on('click', function (){
            $('#txtprofitstandard').val(0);
            $('#txtprofitdetails').val(0);
            $('#txtinventory').val(0);
            $('#txtexpensesheet').val(0);
            $('#txtexpensecat').val(0);
            $('#txtsaledec').val(1);
            $('#txttype').val(1);
            $('#txtitemsale').val(0);
            $('#txtstockadjustment').val(0);
            $('#txtphysical').val(0);
            $('#txtfbrreport').val(0);
            $('#txtinvoicereport').val(0);
            $('#txtsalesinvoicesreport').val(1);
            $('#filter-modal').modal('show');
			$('#ExcelButton').css('display','block');
            showterminal();
			showType();
        });

        function copydate(){
            let date = $('#datefrom').val();
            $('#dateto').val(date);
        }

        

        function  getdata(){
            let date = $('#datefrom').val();
            let todate = $('#dateto').val();
            let terminalid= $('#terminal').val();
            let branch= $('#branch').val();
            let type= $('#type').val();

            if ($('#txtprofitstandard').val() == 1){
                window.location = "{{url('profitLossStandardReport')}}?fromdate="+date+"&todate="+todate+"&branch="+branch;
            }
            if ($('#txtprofitdetails').val() == 1){
                window.location = "{{url('profitLossDetailsReport')}}?fromdate="+date+"&todate="+todate+"&branch="+branch;
            }
            if($('#txtinventory').val() == 1)
            {
                window.location = "{{url('inventory_detailsPDF')}}?fromdate="+date+"&todate="+todate+"&branch="+branch;
            }
            if($('#txtexpensesheet').val() == 1)
            {
                window.location = "{{url('expense-report-pdf')}}?first="+date+"&second="+todate;
            }
            if($('#txtexpensecat').val() == 1)
            {
                window.location = "{{url('expense_by_categorypdf')}}?fromdate="+date+"&todate="+todate;
            }
            if ($('#txtsaledec').val() == 1){
                window.location = "{{url('salesdeclerationreport')}}?fromdate="+date+"&todate="+todate+"&terminalid="+terminalid;
            }
            if ($('#txtitemsale').val() == 1){
                window.location = "{{url('itemsaledatabasepdf')}}?fromdate="+date+"&todate="+todate+"&terminalid="+terminalid+"&type="+$("#type").val();
            }
            if ($('#txtsalereturn').val() == 1){
                window.location = "{{url('salesreturnpdf')}}?fromdate="+date+"&todate="+todate+"&terminalid="+terminalid;
            }
            // if ($('#txtitemsale').val() == 1){
                // window.location = "{{url('itemsaledatabasepdf')}}?fromdate="+date+"&todate="+todate+"&terminalid="+terminalid;
            // }
            if ($('#txtstockadjustment').val() == 1){
                window.location = "{{url('stockAdjustmentReport')}}?fromdate="+date+"&todate="+todate;
            }
			
			if ($('#txtfbrreport').val() == 1){
                window.location = "{{url('fbr-report')}}?fromdate="+date+"&todate="+todate;
            }
			
			if ($('#txtinvoicereport').val() == 1){
                window.location = "{{url('invoice-report')}}?fromdate="+date+"&todate="+todate+"&terminalid="+terminalid+"&type="+type;
            }
			
			if ($('#txtsalesinvoicesreport').val() == 1){
                window.location = "{{url('sales-invoices-report')}}?fromdate="+date+"&todate="+todate+"&terminalid="+terminalid+"&type="+type;
            }
        }
		
		function getExcelData()
		{
			console.log("Inside Function"+$('#txtitemsale').val())
			let from = $('#datefrom').val();
            let to = $('#dateto').val();
            let terminal = $('#terminal').val();
		
			if ($('#txtitemsale').val() == 1){
                window.location = "{{url('export-isdb')}}?from="+from+"&to="+to+"&terminal="+terminal;
            }
			
			if ($('#txtfbrreport').val() == 1){
                window.location = "{{url('export-fbr')}}?from="+from+"&to="+to+"&terminal="+terminal;
            }
		}


        $('#dvshowreport').on('click', function (){
            window.location = "{{url('inventoryReport')}}";
        });
		
		$('#dvcustomeraging').on('click', function (){
            window.open("{{url('customer-aging')}}");
        });

        {{--$('#dvphysical').on('click', function (){--}}
        {{--    window.location = "{{url('inventoryReportPhysical')}}";--}}
        {{--});--}}


        function showType() {
            if($('#txttype').val() == 1){
                $('#dvtype').css("display", "block");
            }
            else{
                $('#dvtype').css("display", "none");
            }
        }
		
		function showterminal() {
            if($('#txtsaledec').val() == 1 || $('#txtitemsale').val() == 1 || $('#txtsalereturn').val() == 1){
                $('#dvterminal').css("display", "block");
            }
            else{
                $('#dvterminal').css("display", "none");
            }
        }
		
		function showbranch() {
            if($('#txtprofitstandard').val() == 1 || $('#txtprofitdetails').val() == 1 || $('#txtinventory').val() == 1){
                $('#dvbranch').css("display", "block");
            }
            else{
                $('#dvbranch').css("display", "none");
            }
        }
        
        function getreport()
        {
            if($('#depart').val() == ""){
                swal({
                    title: "Error Message",
                    text: "Please Select Department!",
                    type: "error"
                });
            }
            else{
                window.location = "{{url('inventoryReportPhysical')}}?departid="+$('#depart').val();
            }
        }



    </script>
@endsection
