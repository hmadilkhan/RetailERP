@extends('layouts.master-layout')

@section('title','Customer Receivables')

@section('breadcrumtitle','View Customer Receivables')
@section('navVendorPO','active')
@section('navpurchase','active')
@section('nav_viewpurchase','active')
@section('css_code')
<style>
.text-size{
 font-weight:bold;
}
</style>
@stop

@section('content')
<section class="panels-wells">
  <div class="card">
   <div class="card-header">
     <h1 class="">Customer Receivables</h1>
     <h5 class="card-header-text">Customer Receipt Details</h5>
   </div>      
   <div class="card-block">
    <ul class="nav nav-tabs md-tabs m-b-20">
      <input type="hidden" name="type" id="type" value="all" />
           <li class="nav-item">
               <a class="nav-link active text-size" id="draft" onclick="changeTab(this,'all')">All Receipt</a>
               <div class="slide"></div>
           </li>
            <li class="nav-item">
               <a class="nav-link text-size" id="placed" onclick="changeTab(this,'today')">Today Receipt</a>
               <div class="slide"></div>
           </li>
           <li class="nav-item">
               <a class="nav-link text-size" id="received" onclick="changeTab(this,'clear')">Clear Receipt</a>
               <div class="slide"></div>
           </li>
           </ul>
		   
   <div class="row ">
    <div class="col-lg-12 col-md-12 ">
       <div class="col-lg-3">
        <div class="form-group {{ $errors->has('product') ? 'has-danger' : '' }}">
          <select id="customer_name" class="js-data-example-ajax form-control select2"></select>
        </div>
      </div>
	 <div class="col-lg-3 ">
        <div class="form-group {{ $errors->has('product') ? 'has-danger' : '' }}">
          <select id="payment_type" class="select2">
			<option value = "">Select Payment Type</option>
			<option value = "1">Cash</option>
			<option value = "2">Credit</option>
		  </select>
        </div>
      </div>
      <div class="col-lg-3 fromDate">
        <div class="input-group">
          <input type="text" class="form-control float-right"  autocomplete="off" id="from_date" placeholder="From Date" value="">
        </div>
      </div>
      <div class="col-lg-3 fromDate">
        <div class="input-group">
          <input type="text" class="form-control float-right"  autocomplete="off" id="to_date" placeholder="To Date" value="">
        </div>
      </div>

    </div>
   </div>
   <div class="row ">
        <div class="col-lg-12  ">
			<button class="btn btn-success btnSubmit m-l-1 f-right">Submit</button>
			<button class="btn btn-info resetBtn m-l-1 f-right">Reset</button>
      </div>
   </div>
   <br/>
   <div class="clearfix"></div>
     <table  id="empTable" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">
       <thead>
         <tr>
           <!-- <th>Order#</th> -->
           <th>Date</th>
           <th>Time</th>
           <th>Terminal</th>
           <th>Receipt No</th>
           <th>Customer</th>
           <th>Address</th>
           <th>Due Date</th>
           <th>Amount</th>
           <th>Balance</th>
		   <th>Payment Type</th>
           <th>Action</th>
         </tr>
       </thead>
     </table>
   </div>
 </div>
</section>
{{-- Model --}}
<div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">EDIT Due Date</h4>
            </div>
            <form action="" method="post" id="editDueDate">
                {{csrf_field()}}
                <input class="form-control" type="hidden" name="cust_receipt_id" id="model-receipt_id" value="" />
                <div class="row m-r-0 m-l-0 m-t-10">
                    <div class="messages"></div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group {{ $errors->has('debit') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Due Date</label>
                            <input class="form-control" type="text" name="due_date" id="model_due_date" required value="0" value="{{ old('debit') }}" />
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
{{-- Payment Model --}}
<div class="modal fade modal-flex" id="payment-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Payment History</h4>
            </div>
            <form action="" method="post" id="paymentHistory">
                {{csrf_field()}}
                <input class="form-control" type="hidden" name="payment_history" id="payment_receipt_id" value="" />
                <div class="row m-r-0 m-l-0 m-t-10">
                    <div class="messages"></div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group showData">
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('scriptcode_three')
@include('../partials._datatable')
<script type="text/javascript" src="{{ URL::asset('js/purchase-script.js') }} "></script>
<script type="text/javascript">
  var oTable;    
  $(document).ready(function(){
	  $('#payment_type').select2(); 
      // DataTable
       oTable =  $('#empTable').DataTable({
       processing: true,
       serverSide: true,
       searching: false,
       responsive: true,
       order: [0, 'desc'],
       columnDefs: [
       {className: 'text-center', orderable: false, targets: 0 },
       {className: 'text-center', orderable: false, targets: 1 },
       {className: 'text-center', orderable: false, targets: 2 },    
       {className: 'text-center', orderable: false, targets: 4 },
       {className: 'text-center', orderable: false, targets: 5 },
       {className: 'text-center', orderable: false, targets: 6 },
       {className: 'text-center', orderable: false, targets: 7 },
           {className: 'text-center', orderable: false, targets: 8 },
          {className: 'text-center', orderable: false, targets: 9 },     
		  {className: 'text-center', orderable: false, targets: 10 },     
       { orderable: false, targets: 7 },
       ],
       'ajax': {
         'url':"{{route('get-customer-due-payment')}}",
         'data': function(data){
          var customer_name = $('#customer_name').val();
          var from_date = $('#from_date').val();
          var to_date = $('#to_date').val();
          var type = $('#type').val();
		  var payment_type = $('#payment_type').val();
            data.type = type;
            data.customer_name = customer_name;
            data.from_date = from_date;
            data.to_date = to_date;
			data.payment_type = payment_type
        }
		
      },
      columns: [
      // { data: 'Order' },
      { data: 'Date' },
      { data: 'Time' },
      { data: 'Terminal' },
      { data: 'Receipt No' },
      { data: 'Customer Name' },
      { data: 'address' },
      {  "render": function (data, type, full, meta)
        { 
          var html = '';
          html += '<a onclick="showDueDateModel(\''+full.due_date+'\','+full.Order+')"   class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View">'+full.due_date+'</a>';
          return html;
        } },
      {data: 'Total Amount'},
      { 
        "render": function (data, type, full, meta)
        {
          return '<p style="color:red">'+full.balance+'</p>';
        }
       },
	    { 
        "render": function (data, type, full, meta)
        {
          return '<p style="color:red">'+(full.Payment == 1 ? 'Cash' : (full.Payment == 2 ? 'Credit' : 'WalkIn'))+'</p>';
        }
       },
      {
        "render": function (data, type, full, meta)
        { 
          var html = '';
          html += '<a  href="{{url('print')}}/'+full.receipt_no+'" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-print"></i></a>';
           html += '<a  onclick="showPaymentHistory('+full.Order+')" class="text-primary p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>';
          return html;
        }
      },
      ]
    });
      $(".btnSubmit").click(function() {
        minDateFilter = new Date(this.value).getTime();
        // $('#date').val(1);
        oTable.draw();
      });
      $(".resetBtn").click(function() {
        $(".js-data-example-ajax").val('').trigger('change');
        $('#from_date').val('');
        $('#to_date').val('');
		$('#filter_type').val('');
        oTable.draw();
     });

    });
  function changeTab(arg,type){
        $(".js-data-example-ajax").val('').trigger('change');
        $('#from_date').val('');
        $('#to_date').val('');
		$('#filter_type').val('');
        $('#type').val(type);
        $(".nav-link").removeClass('active');
        $(arg).addClass('active');
         $('input[type=search]').val('');
         $('.drp-selected').text('');
         if($('#type').val() == 'today'){
            $('.mainDiv').addClass('push-md-7');
            $('.mainDiv').removeClass('push-md-3');
            $('.fromDate').hide();

         }else{
            $('.mainDiv').addClass('push-md-3');
            $('.mainDiv').removeClass('push-md-7');
            $('.fromDate').show();
         }

         oTable.search('').draw();
  }


$('#from_date,#to_date,#model_due_date').bootstrapMaterialDatePicker({
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
$('.js-data-example-ajax').select2({
  ajax: {
    url: '{{route("search-customer-by-names")}}',
    dataType: 'json',
    processResults: function (data) {
      // Transforms the top-level key of the response object from 'items' to 'results'
      return {
            results: $.map(data.items, function (item) {
                return {
                    text: item.name,
                    id: item.name
                }
            })
        };
    }
    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
  },
  placeholder: 'Search for a Customer',
  minimumInputLength: 1,
});

function showPaymentHistory(Order){
      $('#payment_receipt_id').val(Order);
      $('#payment-modal').modal('show');
      $.ajax({
            async: false,
            type: "POST",
            url: "<?php echo URL::to('customer-payment-log') ?>",
            data: {receipt_no:Order,_token:"{{ csrf_token() }}"},
            success: function(response) {
                // var obj = $.parseJSON(response);
              $('.showData').html(response);
            }

        });
}

 function showDueDateModel(dueDate,Order) {
      $('#model_due_date').val(dueDate);
      $('#model-receipt_id').val(Order);
      $('#details-modal').modal('show');
    }
    // Edit Manual Payment
    $('#editDueDate').on('submit', function(e) {
        e.preventDefault();
        var $form = $('#editDueDate');
        // check if the input is valid using a 'valid' property
        var formStatus = $('#editDueDate')[0].checkValidity();
        $.ajax({
            async: false,
            type: "POST",
            url: "<?php echo URL::to('customer-due-date') ?>",
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
  </script>
  @endsection

