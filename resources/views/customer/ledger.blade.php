@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Add Expense')

@section('navcustomer','active')

@section('content')
<section class="panels-wells">

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Add Manual Adjustments </h5>
			<a class="f-right" onclick="toggleManual()">
                    Collapse
            </a>

            <h5 class=""><a href="{{ url('customer') }}"><i class="text-success text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>

        </div>
        <div class="card-block" id="manual-card">
            <form method="GET" action="{{url('adjustment-customer')}}">
                <input type="hidden" name="customer" value="{{$customerID}}" />
                <div class="row">
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Date</label>
                            <input class="form-control" type="text" name="date" id="date" placeholder="DD-MM-YYYY" value="{{ old('date') }}" />
                            @error('date')
                            <div class="form-control-feedback">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group {{ $errors->has('debit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Debit</label>
                            <input class="form-control" onkeypress="return isNumberKey(event)" type="text" name="debit" id="debit" required value="0" value="{{ old('debit') }}" />
                            @error('debit')
                            <div class="form-control-feedback">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Credit</label>
                            <input class="form-control" onkeypress="return isNumberKey(event)" type="text" name="credit" id="credit" required value="0" value="{{ old('credit') }}" />
                            @error('credit')
                            <div class="form-control-feedback">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Narration</label>
                            <input class="form-control" type="text" name="narration" id="narration" value="{{ old('narration') }}" />
                            @error('narration')
                            <div class="form-control-feedback">{{$message}}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="form-group">
                            <label class="form-control-label"></label>
                            <button type="submit" class="btn btn-md btn-success waves-effect waves-light m-t-20">
                                Deposit Amount
                            </button>
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="panels-wells " style="margin-top:-30px;">
    
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Filter Ledger Details</h5>
				<a class="f-right" onclick="toggle()">
                    Collapse
                </a>
			</div>
			<div class="card-block" id="filter-card">
				<div class="row">
					<div class="col-lg-4 col-md-4">
						<div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
							<label class="form-control-label">From</label>
							<input class="form-control" type="text"
								   name="from" id="fromdate" placeholder="YYYY-MM-DD"/>
							@error('from')
								<div class="form-control-feedback">{{$message}}</div>
							@enderror
						</div>
					</div>
					<div class="col-lg-4 col-md-4">
						<div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
							<label class="form-control-label">To</label>
							<input class="form-control" type="text"
								   name="to" id="todate" placeholder="YYYY-MM-DD"/>
							@error('to')
								<div class="form-control-feedback">{{$message}}</div>
							@enderror
						</div>
					</div>
					<div class="col-lg-4 col-md-4">
						<label class="form-control-label"></label>
						<div class="button-group ">
							<button type="button" id="btndraft" onclick="pdfgenerate()" class="btn btn-md btn-danger waves-effect waves-light f-left m-t-10"> <i class="icofont icofont-file-pdf"> </i>
							   Print Pdf
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<section class="panels-wells" style="margin-top:-30px;">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Customer Ledger Details - {{$customername}}</h5>
             <a href="{{ url('create-customer-payment',$customerID) }}" class="btn btn-success waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Make Payment </a> 
            <h5 class=""><a href="{{ url('customer') }}"><i class="text-success text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            {{-- <h5 class=""><a href="{{ url('customer-ledger-report',$customerID) }}"><i class="btn btn-danger text-center icofont icofont-file-pdf p-r-20 f-18 f-right m-r-3" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back"> Print PDF</i></a></h5>--}}


        </div>
        <div class="card-block">
            <div class="project-table">
                <table id="ledgerTable" class="table table-striped nowrap dt-responsive" width="100%">
                    <thead>
                        <tr>

                            <th>S.No</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Receipt No</th>
                            <th>Mode / Narration</th>
                            <th>Total Amount</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Total Balance</th>
                            <th>Action</th>


                        </tr>
                    </thead>
                    <tbody>

                        @if($details)
                        @php
                        $receipt_balance = 0;
                        $total_amount = 0;
                        $credit = 0;
                        $debit = 0;
                        @endphp
                        @foreach ($details as $value)
                        <?php
                            $creditGreater = false;
                            if($credit > $total_amount){
                                $creditGreater = true;
                            } 
                            if ($value->total_amount > 0) {
                                $total_amount += $value->total_amount;
                            } else {
                                $total_amount += $value->debit;
                            }
                            $credit += $value->credit;
                            $debit += $value->debit;
                            $receipt_balance = Custom_Helper::getLedgerCal($value,$receipt_balance,$total_amount,$credit, $debit,$creditGreater);
                        //?>
                        <tr {{($value->received == 1 ? "style=background-color:#FFDAB5;" : '')}} class="">
                            <td>{{ $value->cust_account_id }}</td>
                            <!-- $loop->index +1<td>{{$value->cust_account_id}}</td> -->
                            <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                            <td>{{date("h:i A",strtotime($value->created_at))}}</td>
                            <td>{{($value->receipt_no == "" ? 'Manual Adjustment' : $value->receipt_no)}}</td>
                            <td>{{ $value->payment_mode .' / '. $value->narration }}</td>
                            <td>{{number_format($value->total_amount,2)}}</td>
                            <td class="{{((float)$value->credit > 0) ? 'text-success' : ''}}">{{number_format($value->credit,2)}}</td>
                            <td class="{{((float)$value->debit > 0) ? 'text-danger' : ''}}">{{number_format($value->debit,2)}}</td>
                            <td class="{{ $total_amount > $credit ?'text-danger':'text-success'}}">{{number_format(($receipt_balance),2)}}</td>
                            <td>
                                @if($value->receipt_no != 0)
                                <a href="{{url('print',$value->receipt_no)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-printer"></i></a>
                                @else

                                <a href="javascript:void(0)" class="text-primary" onclick="showEditManualPayment('{{$value->cust_id}}','{{$value->cust_account_id}}','{{$value->debit}}','{{$value->credit}}','{{$value->narration}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-pen"></i></a>
                                @endif
                            </td>

                        </tr>
                        @endforeach
                        @endif

                    </tbody>
                </table>
                <br>
                <div class="button-group ">
                    <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="generateExcel()"><i class="icofont icofont-file-excel"> </i>
                        Export to Excel Sheet
                    </button>
                    <button type="button" id="btndraft" onclick="pdfgenerate()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                        Print Pdf
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL START--}}
    <div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="mod-title" class="modal-title">Receipt Details</h4>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Receipt No :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="receiptno" class="">1234564897978</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Date :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="date" class="">2012-02-12</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Customer Name:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="name" class="">Muhammad Adil Khan</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Contact :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="mobile" class="">0311-1234567</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Order Type:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="type" class="">Take Away</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Status :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="status" class="">Pending</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="tablemodal" class="table " width="100%" cellspacing="0">
                                <thead>
                                    <th width="80%">Product Name</th>
                                    <th>Qty</th>
                                    <th>Amount</th>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>

                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Total Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="tamount" class="f-right">10000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Advance :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="receive" class="f-right">1000</label>
                        </div>
                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Bal. Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="bal" class="f-right">10000</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--           <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button> -->
                </div>
            </div>
        </div>
    </div>
    {{-- MODAL END--}}
</section>

{{-- Model --}}
<div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">EDIT MANUAL ADJUSTMENTS</h4>
            </div>
            <form action="" method="post" id="editManualPayment">
                {{csrf_field()}}
                <input class="form-control" type="hidden" name="cust_account_id" id="model-accountId" value="" />
                <input class="form-control" type="hidden" name="cust_id" id="model-custId" value="" />
                <div class="row m-r-0 m-l-0 m-t-10">
                    <div class="messages"></div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group {{ $errors->has('debit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Debit</label>
                            <input class="form-control" type="number" name="debit" id="model-debit" required value="0" value="{{ old('debit') }}" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Credit</label>
                            <input class="form-control" type="number" name="credit" id="model-credit" required value="0" value="{{ old('credit') }}" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Narration</label>
                            <input class="form-control" type="text" name="narration" id="model-narration" value="{{ old('narration') }}" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-success waves-effect waves-light f-right">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>


</script>
@section('scriptcode_three')

<script type="text/javascript">
    $('#ledgerTable').DataTable({
        bLengthChange: true,
        displayLength: 50,
        info: true,
		"order": [ 0, "desc" ],
        language: {
            search: '',
            searchPlaceholder: 'Search Customer',
            lengthMenu: '<span></span> _MENU_'

        }

    });
	
	
    $('#date,#fromdate,#todate').bootstrapMaterialDatePicker({
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

    function getBill(receipt) {
      
        $.ajax({
            url: "{{url('/get-order-general')}}",
            type: 'POST',
            dataType: "json",
            async: false,
            data: {
                _token: "{{ csrf_token() }}",
                receiptID: receipt,
            },
            beforeSend: function() {
                // console.log("Data is loading");
            },
            success: function(result) {
                $('#product-modal').modal("show");
                $('#receiptno').html(result[0].receipt_no);
                $('#date').html(result[0].date);
                $('#name').html(result[0].name);
                $('#mobile').html(result[0].mobile);
                $('#type').html(result[0].order_mode);
                $('#status').html(result[0].order_status_name);

                if (type == "Take Away") {
                    $('#tamount').html("Rs. " + result[0].total_amount.toLocaleString());
                    $('#receive').html('0');
                    var balance = parseInt(result[0].total_amount) - parseInt(result[0].receive_amount);
                    $('#bal').html('0');
                } else {
                    $('#tamount').html("Rs. " + result[0].total_amount.toLocaleString());
                    $('#receive').html("Rs. " + result[0].receive_amount.toLocaleString());
                    var balance = parseInt(result[0].total_amount) - parseInt(result[0].receive_amount);
                    $('#bal').html("Rs. " + balance.toLocaleString());
                }

                $.ajax({
                    url: "{{url('/get-items-by-receipt')}}",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: result[0].id,
                    },
                    beforeSend: function() {
                        // console.log("Data is loading");
                    },
                    success: function(result) {
                        $("#tablemodal tbody").empty();
                        for (var count = 0; count < result.length; count++) {
                            $("#tablemodal tbody").append(
                                "<tr>" +
                                "<td >" + result[count].product_name + "</td>" +
                                "<td >" + result[count].total_qty + "</td>" +
                                "<td '>" + parseInt(result[count].total_amount).toLocaleString() + "</td>" +
                                "</tr>"
                            )
                        }
                    }
                });
            }
        });
    }


    function showEditManualPayment(custID, accountID, debit, credit, narration) {
        $('#model-credit').val(parseFloat(credit));
        $('#model-debit').val(parseFloat(debit));
        $('#model-narration').val(narration);
        $('#model-accountId').val(accountID);
        $('#model-custId').val(custID);
        $('#details-modal').modal('show');
    }

    // Edit Manual Payment
    $('#editManualPayment').on('submit', function(e) {
        e.preventDefault();
        var $form = $('#editManualPayment');
        // check if the input is valid using a 'valid' property
        var formStatus = $('#editManualPayment')[0].checkValidity();
        $.ajax({
            async: false,
            type: "POST",
            url: "<?php echo URL::to('edit-adjustment-customer') ?>",
            data: $form.serialize(),
            success: function(response) {
                // var obj = $.parseJSON(response);
                var obj = response;
                if (obj.status == 'true') {
                    $('.messages').html('<div class="alert alert-success p-r-20 p-l-10" style="background-color:#dff0d8;color:#3c763d;border-color:d0e9c6" >' + obj.message + '</div>').fadeIn().delay(3000).fadeOut();
                    window.setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    var message = '';
                    $.each(response.message, function(key, value) {
                        message += value + '<br>';
                    });
                    $('.messages').html('<div class="alert alert-danger">' + message + '</div>').fadeIn().delay(3000).fadeOut();
                }
            }

        });
        return false; //mark-2

    });
	
	function pdfgenerate() {
        window.location = "{{url('customer-ledger-report',$slug)}}" + "/" + $("#fromdate").val() + "/" + $("#todate").val();
    }
	
	function generateExcel(){
		alert("Work IN progress")
	}
	
	$('#filter-card').toggle();
	$('#manual-card').toggle();
	function toggle(){
		$('#filter-card').toggle();
	}
	
	function toggleManual(){
		$('#manual-card').toggle();
	}
</script>



@endsection