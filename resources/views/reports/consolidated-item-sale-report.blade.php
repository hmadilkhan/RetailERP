@extends('layouts.master-layout')

@section('title','Consolidated Item Sale Report')

@section('breadcrumtitle','Consolidated Item Sale Report')

@section('navitemsalereport','active')
@section('navreports','active')

@section('content')

 <section class="panels-wells">
    <div class="card">
		<div class="card-header">
         <h5 class="card-header-text mb-10">Item Sale Report</h5>
     </div>
     <div class="card-block">
     	<form name="searchFormItemSale" action="" method="post">
     		@csrf
         <div class="row">
            <div  class="col-md-3">
              <div class="form-group">
                 <label class="form-control-label">Product Name</label>
                 <select id="product_name" name="product_name" class="f-right select2" data-placeholder="Select Product Name">
                     <option value="">Select Payment Mode</option>
                     @foreach($inventory as $value)
                         <option value="{{ $value->id }}">{{ $value->item_code. " | " .$value->product_name }}</option>
                     @endforeach
                 </select>  
                        <span class="help-block text-danger" id="alert_product-name"></span>  
                    </div>
            </div>
           <div id="from" class="col-md-3">
             <div class="form-group">
                    <label class="form-control-label">From Date</label>
                            <input type='text' class="form-control" id="fromdate" name="fromdate" placeholder="DD-MM-YYYY"/>  
                            <span class="help-block text-danger" id="alert_fromdate"></span>  
                    </div>
           </div>
            <div id="to" class="col-md-3">
              <div class="form-group">
                    <label class="form-control-label">To Date</label>
                        <input type='text' class="form-control" id="todate" name="todate" placeholder="DD-MM-YYYY"/>  
                        <span class="help-block text-danger" id="alert_todate"></span>  
                    </div>
            </div>
			<div class="col-md-3">
                 <label class="form-control-label">Select Customer</label>
                 <select id="customer" name="customer" data-placeholder="Select Customer" class="f-right select2">
                     <option value="">Select Customer</option>
                     @foreach($customer as $value)
                         <option value="{{ $value->id }}">{{ $value->name }}</option>
                     @endforeach
                 </select>
             </div>
         </div>

         <div class="row">
             <div class="col-md-3 ">
                 <label class="form-control-label">Select Departments</label>
                 <select id="department" name="department" class="f-right select2" data-placeholder="Select Departments">
                     <option value="">Select Payment Mode</option>
                     @foreach($departments as $department)
                         <option value="{{ $department->department_id }}">{{ $department->department_name }}</option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-3">
                 <label class="form-control-label">Select Mode</label>
                 <select id="ordermode" name="ordermode" data-placeholder="Select Mode" class="f-right select2">
                     <option value="">Select Mode</option>
                     @foreach($mode as $value)
                         <option value="{{ $value->order_mode_id }}">{{ $value->order_mode }}</option>
                     @endforeach
                 </select>
             </div>
             <div class="col-md-3">
                 <label class="form-control-label">Select Branch</label>
                 <select id="branch" name="branch" data-placeholder="Select Branch" class="f-right select2">
                     <option value="all">All Branch</option>
                     @foreach($branch as $value)
                         <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                     @endforeach
                 </select>
             </div>

             <div class="col-md-3">
                 <label class="form-control-label">Select Terminal</label>
                 <select id="terminal" name="terminal" data-placeholder="Select Terminal" class="f-right select2">
                     <option value="">Select Terminal</option>
                 </select>
             </div>
			 
			 <div class="col-md-3 m-t-1">
                 <label class="form-control-label">Select Payment Mode</label>
                 <select id="paymentmode" name="paymentmode" class="f-right select2" data-placeholder="Select Payment Mode">
                     <option value="">Select Payment Mode</option>
                     @foreach($paymentMode as $value)
                         <option value="{{ $value->payment_id }}">{{ $value->payment_mode }}</option>
                     @endforeach
                 </select>
             </div>
			 
			 <div class="col-md-3 m-t-1">
                 <label class="form-control-label">Report Type</label>
                 <select id="report_type" name="report_type" data-placeholder="Select Report Type" class="f-right select2">
                     <option value="consolidated">Consolidated</option>
                     <option value="seperate">Seperate</option>
                 </select>
             </div>
			 
			 <div class="col-md-3 m-t-1">
                 <label class="form-control-label">Declaration Type</label>
                 <select id="declaration" name="declaration" data-placeholder="Select Report Type" class="f-right select2">
                     <option value="declaration">Declaration</option>
                     <option value="datewise">Datewise</option>
                 </select>
             </div>
              
         </div>

         <div class="row">

             <div class="col-md-6 f-right">
                 <label class="form-control-label"></label>
				 <a href="{{ url('reports/item-sale-report') }}" class="btn btn-info waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-refresh" > </i>Reset
                 </a>
				 <button type="button" id="btnExcel"  class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-file-excel" > </i>Excel Export
                 </button>
				 <button type="button" id="btnPdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-file-pdf" > </i>PDF Export
                 </button>
				 <button type="button" name="btn_search_report"  class="btn btn-success waves-effect waves-light m-t-25 m-r-10 f-right"  >
                     <i class="icofont icofont-ui-check"> </i>Submit
                 </button>
             </div>
         </div>
          </form>
		  <div class="row dashboard-header m-l-2 m-t-2" id="totaldiv" style="display:none">
               <div class="col-lg-3 col-md-6">
                  <div class="card dashboard-product">
                     <span>Total Receipt Counts</span>
                     <h2 class="dashboard-total-products" id="totalreceipts"></h2>
                     <span class="label label-warning">Receipt</span>
                     <div class="side-box">
                        <i class="ti-package text-warning-color"></i>
                     </div>
                  </div>
               </div>
			   <div class="col-lg-3 col-md-6">
                  <div class="card dashboard-product">
                     <span>Total Items</span>
                     <h2 class="dashboard-total-products" id="totalorders"></h2>
                     <span class="label label-warning">Items</span>
                     <div class="side-box">
                        <i class="ti-package text-warning-color"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
	    @include('partials.loader')
	    <div id="itemSalesReport"></div>
	</div>

       
</section>

@endsection

@section('scriptcode_three')
<script type="text/javascript">
  $(".select2").select2();
 
  $('input[name="fromdate"],input[name="todate"]').bootstrapMaterialDatePicker({
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
   
   $("button[name='btn_search_report']").on('click',function(){
         if($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == ''){
             $('input[name="fromdate"]').focus();
             $("#alert_fromdate").text('Please select the date'); 
         }else{
			 $("#itemSalesReport").empty();
			 $("#totaldiv").css("display","none");
			 $("#totalorders").html("0");
			 $("#totalamount").html("0");
			$.ajax({
                url: "{{ route('consolidated.SrchISReport') }}",
                type: 'POST',
				// dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    fromdate: $("#fromdate").val(),
                    todate: $("#todate").val(),
                    product: $("#product_name").val(),
                    branch: $("#branch").val(),
                    terminal: $("#terminal").val(),
                    customer: $("#customer").val(),
                    paymentmode: $("#paymentmode").val(),
                    ordermode: $("#ordermode").val(),
					declaration: $("#declaration").val(),
					department: $("#department").val(),
                },
                beforeSend: function() {
                    $('#loader').removeClass('hidden')
                },
                success: function(result) {
					console.log("Fetching Results",result);
                    $('#loader').addClass('hidden')
                    $("#btn_search_report").attr("disabled",false);
                    if (result != 0) {
                        $("#itemSalesReport").html(result);
                    } else {
                        // showError("Purchase Order Not Found");

                    }
                },
                complete: function() {
                    $('#loader').addClass('hidden')
                    $("#btn_search_report").attr("disabled",false);
                },
                error: function(xhr, status, error) {
                    $('#loader').addClass('hidden');
                    $("#btn_search_report").attr("disabled", false);
                    
                    // Get the complete error response
                    let errorMessage = 'An error occurred while processing your request.';
                    let errorDetails = '';
                    
                    try {
                        // Try to parse the response text
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || 'Server Error';
                        
                        // Add any additional error details
                        if (response.error) {
                            errorDetails = `
                                File: ${response.error.file || 'Unknown'}
                                Line: ${response.error.line || 'Unknown'}
                                Message: ${response.error.message || 'No additional details'}
                            `;
                        }
                    } catch (e) {
                        // If parsing fails, use the raw response
                        errorMessage = xhr.responseText || 'Unknown Error';
                    }
                    
                    // Log the complete error to console
                    console.error("Complete Error Details:", {
                        Status: xhr.status,
                        StatusText: xhr.statusText,
                        Response: xhr.responseText,
                        Error: error,
                        Details: errorDetails
                    });
                    
                    // Show error in a more visible way with details
                    Swal.fire({
                        title: 'Error!',
                        html: `
                            <div class="text-left">
                                <p><strong>Error Message:</strong> ${errorMessage}</p>
                                ${errorDetails ? `<pre class="mt-2 text-danger">${errorDetails}</pre>` : ''}
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                },

            });
		 }

         // if(process){
             // $("form[name='searchFormItemSale']").submit();
         // }
   });
   
   $("#branch").change(function(){
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
					console.log("Fetching Results",result);
                    $('#loader').addClass('hidden')
                    if (result != 0) {
						$("#terminal").empty();
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
                    $("#btn_search_report").attr("disabled",false);
                    console.log("Error", error);
                },

            });
   })
	
	$("#btnExcel").click(function(){
		if($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == ''){
             $('input[name="fromdate"]').focus();
             $("#alert_fromdate").text('Please select the date'); 
         }else{
			window.open("{{url('reports/consolidated-excel-export-item-sale-report')}}"+"?fromdate="+$("#fromdate").val()+"&todate="+$("#todate").val()+"&branch="+$("#branch").val()+"&terminal="+$("#terminal").val()+"&customer="+$("#customer").val()+"&paymentmode="+$("#paymentmode").val()+"&ordermode="+$("#ordermode").val()+"&product="+$("#product_name").val()+"&type="+$("#report_type").val()+"&declaration="+$("#declaration").val()+"&department="+$("#department").val()); 
		 }
	})
	
	$("#btnPdf").click(function(){
		if($('input[name="fromdate"]').val() == '' && $('input[name="todate"]').val() == ''){
             $('input[name="fromdate"]').focus();
             $("#alert_fromdate").text('Please select the date'); 
         }else{
			window.open("{{url('reports/pdf-export-item-sale-report')}}"+"?fromdate="+$("#fromdate").val()+"&todate="+$("#todate").val()+"&branch="+$("#branch").val()+"&terminal="+$("#terminal").val()+"&customer="+$("#customer").val()+"&paymentmode="+$("#paymentmode").val()+"&ordermode="+$("#ordermode").val()+"&product="+$("#product_name").val()+"&type="+$("#report_type").val()+"&declaration="+$("#declaration").val()+"&department="+$("#department").val()); 
		 }
		
	})
</script>
@endsection
