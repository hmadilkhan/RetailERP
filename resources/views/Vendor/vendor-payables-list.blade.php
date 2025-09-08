@extends('layouts.master-layout')

@section('title','Vendor Payables')

@section('breadcrumtitle','View Vendor Payables')
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
     <h1 class="">Vendor Payables</h1>
     <h5 class="card-header-text">Search Vendors & Due Dates</h5>
   </div>      
   <div class="card-block">
   <div class="row">
     <div class="col-lg-12 push-md-3">
       <div class="col-lg-3">
        <div class="form-group {{ $errors->has('product') ? 'has-danger' : '' }}">
        <select id="vendor_name" class="js-data-example-ajax form-control select2"></select>
      </div>
      </div>
     <div class="col-lg-2 ">
        <div class="input-group">
          <input type="text" class="form-control float-right"  autocomplete="off" id="from_date" placeholder="From Date" value="">
        </div>
      </div>
        <div class="col-lg-2 ">
        <div class="input-group">
          <input type="text" class="form-control float-right"  autocomplete="off" id="to_date" placeholder="To Date" value="">
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
           <!-- <th>Order#</th> -->
           <th>Generation Date</th>
           <th>PO No</th>
           <th>Vendor</th>
           <th>Branch</th>
           <th>Delivery Date</th>
           <th>Payment Date</th>
           <th>Amount</th>
           <th>Status</th>
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
<script type="text/javascript" src="{{ URL::asset('public/js/purchase-script.js') }} "></script>
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
       { orderable: false, targets: 6 },
       ],
       'ajax': {
         'url':"{{route('get-vendor-payable')}}",
         'data': function(data){
          var vendor_name = $('#vendor_name').val();
          var from_date = $('#from_date').val();
          var to_date = $('#to_date').val();
            data.vendor_name = vendor_name;
            data.from_date = from_date;
            data.to_date = to_date;
        }
      },
      columns: [
     { data: 'order_date' },
      { data: 'po_no' },
      { data: 'Vendor' },
      { data: 'Branch' },
      { data: 'delivery_date' },
      { data: 'payment_date' },
      { data: 'Amount' },
      {
        "render": function (data, type, full, meta)
        { 
          var html = '';
          html = '<span class="tag tag-info">'+full.Status+'</span>';
          return html;
        }
      },
      {
        "render": function (data, type, full, meta)
        { 
          var html = '';
          var viewButton = full.name == 'Draft'?'muted disabled':'primary'; 
          html +=  ' <a  href="{{route('view','/')}}/'+full.purchase_id+' " class="text-'+viewButton+' p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>'; 
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
  $('#from_date,#to_date').bootstrapMaterialDatePicker({
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
    url: '{{route("search-vendor-by-names")}}',
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
  placeholder: 'Search for a Vendor',
  minimumInputLength: 1,
});
  </script>
  @endsection

