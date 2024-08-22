@extends('layouts.master-layout')

@section('title','Customer Receivable')

@section('breadcrumtitle','Customer Payable')
@section('navaccountsoperation','active')
@section('navreports','active')
@section('navaccount_rec','active')
@section('nav_customer_rec','active')



@section('content')
<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h1 class="card-header-text">Customer Receivable</h1>
			<label class="card-header-text f-right  label-lg">Total Balance<span id="totalbalance" class="text-danger"></span></label>
            <hr>
            <h5 class="card-header-text">Filter</h5>
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="form-group">
                        <label class="form-control-label">Select Customer</label>
                        <select name="customer" id="customer" data-placeholder="Select Customer" class="form-control select2">
                            <option value="">Select Customer</option>
								@if($master)
									@foreach($master as $value)
										<option value="{{ $value->id }}">{{ $value->name }}</option>
									@endforeach
								@endif
                        </select>
                    </div>
                </div>
				
				<div class="col-lg-3 col-md-3">
                    <div class="form-group">
                        <label class="form-control-label">Select Payment Type</label>
                        <select name="payment_type" id="payment_type" data-placeholder="Select Payment Type" class="form-control select2">
							<option value = "">All</option>
							<option value = "0">Walk In Customers</option>
							<option value = "1">Cash</option>
							<option value = "2">Credit</option>
                        </select>
                    </div>
                </div>
				
                <div class="col-lg-2  col-sm-2">
                    <div class="form-group">
                        <button type="button" id="btnSubmit" class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="getdata()">
                            <i class="icofont icofont-search"></i>&nbsp;Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block">
            <?php $total = 0;
            $no = 0; ?>
            <table id="tblcustomers" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">

                <thead>
                    <tr>
                        <th>Sr.</th>
                        <th>Customer Name</th>
                        <th>Contact</th>
						<th>Payment Type</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
				<!--
				@foreach($details as $key => $value)
					<tr>
						<td>{{$key+1}}</td>
						<td>{{$value->name}}</td>
						<td>{{$value->mobile}}</td>
						<td>{{($value->payment_type == 1 ? "Cash" : ($value->payment_type == 2 ? "Credit" : "Walk In"))}}</td>
						<td>{{$value->balance}}</td>
					</tr>
				@endforeach
				-->
				</tbody>

            </table>
            <br>
            <div class="button-group ">
                <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="generate_excel()"><i class="icofont icofont-file-excel"> </i>
                    Export to Excel Sheet
                </button>
                <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                    Print Pdf
                </button>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
    $(".select2").select2();

	$('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: true,
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'

        }

    });

    getdata();

    function getdata() {

        $.ajax({
            url: '{{ url("/customer-report-filter") }}',
            type: "POST",
            data: {
                _token: "{{csrf_token()}}",
                customer: $('#customer').val(),
				paymentType:$('#payment_type').val(),
            },
            success: function(result) {

                if (result) {
                    var balance = 0;
                    $("#tblcustomers tbody").empty();
                    for (var count = 0; count < result.length; count++) {
                        if (parseInt(result[count].balance) > 0) {
                            balance += parseInt(result[count].balance);
                        
                        $("#tblcustomers tbody").append(
                            "<tr>" +
                            "<td>" + (count + 1) + "</td>" +
                            "<td>" + result[count].name + "</td>" +
                            "<td>" + result[count].mobile + "</td>" +
							"<td>" + (result[count].payment_type == 1 ? "Cash" : (result[count].payment_type == 2 ? "Credit" : "Walk In")) + "</td>" +
                            "<td>" + (result[count].balance) * (1).toLocaleString() + "</td>" +
                            "</tr>"
                        );
						}
                    }
					$("#totalbalance").empty();
					$("#totalbalance").html(" :"+(balance).toLocaleString());
                    // $("#tblcustomers tbody").append(
                        // "<tr>" +
                        // "<td>" + (count + 1) + "</td>" +
                        // "<td></td>" +
                        // "<td class='f-24'>Total Balance</td>" +
                        // "<td class='f-24'>" + (balance).toLocaleString() + "</td>" +
                        // "</tr>"
                    // );

                }
            }
        });
    }

    function generate_pdf() {
        window.location = "{{url('receivable')}}?customer=" + $('#customer').val() + "&first=" + $('#from').val() + "&second=" + $('#to').val();
    }

    function generate_excel() {
        window.location = "{{url('export-customer-ledger')}}?customer=" + $('#customer').val() + "&first=" + $('#from').val() + "&second=" + $('#to').val();
    }
</script>
@endsection