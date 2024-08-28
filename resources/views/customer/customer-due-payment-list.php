@extends('layouts.master-layout')

@section('title','Customer Due Payment')

@section('breadcrumtitle','View Customer Due Payment')
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
     <h5 class="card-header-text">Customer Due Payment</h5>
   </div>      
   <div class="card-block">
   <div class="row">
     <div class="col-lg-12 push-md-4">
      <div class="col-lg-3">
        <div class="input-group">
          <input type="text" class="form-control float-right" autocomplete="off" id="customer_name" placeholder="Search Customer Name" value="">
        </div>
      </div>
      <div class="col-lg-3 ">
        <div class="input-group">
          <input type="text" class="form-control float-right" onkeypress="return isNumberKey(event)" autocomplete="off" id="no_of_day" placeholder="Search Days" value="">
        </div>
      </div>
      <div class="col-lg-3">
        <button class="btn btn-success btnSubmit ml-1">Submit</button>
        <button class="btn btn-info resetBtn ml-1">Reset</button>
      </div>
    </div>
   </div>
   <div class="clearfix"></div>
     <table  id="empTable" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">
       <thead>
         <tr>
           <th>Order#</th>
           <th>Customer Name</th>
           <th>Due Date</th>
           <th>Date</th>
           <th>Time</th>
           <th>Branch</th>
           <th>Terminal</th>
           <th>Receipt No</th>
           <th>OrderType</th>
           <th>Payment Type</th>
           <th>Total Amount</th>
           <th>Action</th>
         </tr>
       </thead>
     </table>
   </div>
 </div>
</section>
@endsection


@section('scriptcode_three')
@include('../partials._datatable')
<script type="text/javascript" src="{{ URL::asset('js/purchase-script.js') }} "></script>
<script type="text/javascript">
  var oTable;    
  $(document).ready(function(){
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
       { orderable: false, targets: 7 },
       ],
       'ajax': {
         'url':"{{route('get-customer-due-payment')}}",
         'data': function(data){
          var customer_name = $('#customer_name').val();
          var no_of_day = $('#no_of_day').val();
            data.customer_name = customer_name;
            data.no_of_day = no_of_day;
        }
      },
      columns: [
      { data: 'Order' },
      { data: 'Customer Name' },
      { data: 'Due Date' },
      { data: 'Date' },
      { data: 'Time' },
      { data: 'Branch' },
      { data: 'Terminal' },
      { data: 'Receipt No' },
      { data: 'OrderType' },
      {data:'Payment Type'},
      {data: 'Total Amount'},
      {
        "render": function (data, type, full, meta)
        { 
             console.log(full.receipt_no);
          var html = '';
          html += '<a  href="{{url('print')}}/'+full.receipt_no+'" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-print"></i></a>';
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
        $('#customer_name').val('');
        $('#no_of_day').val('');
        oTable.draw();
     });

    });
  function changeTab(arg,type){
        $('#type').val(type);
        $(".nav-link").removeClass('active');
        $(arg).addClass('active');
         $('input[type=search]').val('');
         $('.drp-selected').text('');
         oTable.search('').draw();
  }


  
  </script>
  @endsection

