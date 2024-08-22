@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','View Purchases')
@section('navVendorPO','active')
@section('navpurchase','active')
@section('nav_viewpurchase','active')

@section('content')
<section class="panels-wells">
  <div class="card">
   <div class="card-header">
     <h5 class="card-header-text">Purchases List</h5>
     <a href="{{ route('add-purchase') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Create Purshase Order
     </a>
   </div>      
   <div class="card-block">
    <ul class="nav nav-tabs md-tabs m-b-20">
      <input type="hidden" name="type" id="type" value="Draft" />
           <li class="nav-item">
               <a class="nav-link active" id="draft" onclick="changeTab(this,'Draft')">DRAFT</a>
               <div class="slide"></div>
           </li>
            <li class="nav-item">
               <a class="nav-link" id="placed" onclick="changeTab(this,'Placed')">Placed</a>
               <div class="slide"></div>
           </li>
           <li class="nav-item">
               <a class="nav-link" id="received" onclick="changeTab(this,'Received')">RECEIVED</a>
               <div class="slide"></div>
           </li>
           <li class="nav-item">
               <a class="nav-link" id="cancelled" onclick="changeTab(this,'Cancelled')">Cancelled</a>
               <div class="slide"></div>
           </li>
           <li class="nav-item">
               <a class="nav-link" id="complete" onclick="changeTab(this,'Complete')">Complete</a>
               <div class="slide"></div>
           </li>
           <li class="nav-item">
               <a class="nav-link" id="partially-received" onclick="changeTab(this,'Partially Received')">Partially Received</a>
               <div class="slide"></div>
           </li>
           </ul>
     <table  id="empTable" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">
       <thead>
         <tr>
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
       { orderable: false, targets: 7 },
       ],
       'ajax': {
         'url':"{{route('get-purchase')}}",
         'data': function(data){
          var type = $('#type').val();
          data.type = type;
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
          var html;
          if(full.Status == 'Draft'){
            html = '<span class="tag tag-default">'+full.Status+'</span>';
          }else if(full.Status == 'Placed'){
            html = '<span class="tag tag-success">'+full.Status+'</span>';
          }else if(full.Status == 'Received'){
            html = '<span class="tag tag-info">'+full.Status+'</span>';
          }else if(full.Status == 'Cancelled'){
            html = '<span class="tag tag-danger">'+full.Status+'</span>';
          }else if(full.Status == 'Partially Received'){
            html = '<span class="tag tag-danger">'+full.Status+'</span>';
          }else if(full.Status == 'Partially Return'){
            html = '<span class="tag tag-danger">'+full.Status+'</span>';
          }else if(full.Status == 'Complete Return'){
            html = '<span class="tag tag-danger">'+full.Status+'</span>';
          }else if(full.Status == 'Complete'){
            html = '<span class="tag tag-success">'+full.Status+'</span>';
          }else if(full.Status == 'Replacement'){
            html = '<span class="tag tag-danger">'+full.Status+'</span>';
          }else{
            html = '<span class="tag tag-danger">'+full.Status+'</span>';
          }
          return html;
           }
      },
      {
        "render": function (data, type, full, meta)

        { 
          var html = '';
          console.log("Status",full.Status);
          if(full.Status == "Received"){
            html += '<a  href="{{url('grn-details')}}/'+full.purchase_id+'" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>';
          }
          var viewButton = full.Status == 'Draft'?'muted disabled':'primary';
          html +=  ' <a  href="{{route('view','/')}}/'+full.purchase_id+' " class="text-'+viewButton+' p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>'; 
          var editButton = (full.Status == 'Draft' ? 'warning' : 'muted disabled');
		  console.log("Draft",full.Status)
          html += '<a  href="{{route('edit','')}}/'+full.purchase_id+'" class="text-'+editButton+'  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>';
          var deleteButton = full.Status == 'Draft'?'danger':'muted disabled';
          html += '<a class="text-'+deleteButton+' p-r-10 f-18 alert-confirm" onclick="alertConfirm('+full.purchase_id+')" data-id="'+full.purchase_id+'" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>';
          return html;
        }
      },
      ]
    });
      $(".applyBtn").click(function() {
        minDateFilter = new Date(this.value).getTime();
        $('#date').val(1);
        oTable.draw();
      });
      $(".resetBtn").click(function() {
       $('#reservation').val('');
       $('#date').val('');
       $('.drp-selected').text('')
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
  // $.fn.dataTable.ext.errMode = 'none';

     //Alert confirm
 function alertConfirm(id){
    var id= id;
      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this customer!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "delete it!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/DeletePO')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){

                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove purchase order.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{url('/view-purchases')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Customer is safe :)", "error");
           }
        });
 }
  
  </script>
  @endsection


  @section('css_code')
  <style type="text/css">
    a.disabled{
      pointer-events:none;
      cursor: default;
    }
  </style>
  @endsection
