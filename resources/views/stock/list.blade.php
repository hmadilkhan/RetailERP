@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','View Inventory')

@section('navbranchoperation','active')
@section('navstock','active')

@section('content')
 <section class="panels-wells">
  
    
    <div class="col-lg-12 grid-item">
                      <div class="card">
                        <div class="card-header">
                           <h5 class="card-header-text">Stock Details</h5>
                      </div>
                      <div class="card-block">
                        <div class="project-table">
                                 <table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
                                    <thead>
                                       <tr>
                                          <th>Image</th>
                                          <th>Item Code</th>
                                          <th>Product Name</th>
                                          <th>Unit of measure</th>
                                          <th>Department</th>
                                          <th>Amount</th>
                                          <th>Qty</th>
                                          <th>Stock</th>
                                          <th>Action</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                     @if($stock)
                                        @foreach ($stock as $value)
                                         @if($value->mode == 3)
                                           <tr>
                                              <td class="img-pro">
                                                 <img src="{{ asset('public/assets/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="img-fluid d-inline-block" alt="tbl">
                                              </td>
                                              <td>{{$value->item_code}}</td>
                                              <td class="pro-name">
                                                 <h6>{{$value->product_name}}</h6>
                                               <!--   <span class="text-muted f-12">{{$value->product_description}}</span> -->
                                              </td>
                                              <td >{{$value->name}}</td>
                                              <td>{{$value->department_name}}</td>
                                              <td>{{number_format($value->amount,2)}}</td>
                                              <td>{{$value->qty}}</td>
                                              <td>
                                                <label class="{{($value->qty > 0 && $value->qty > $value->reminder_qty) ? 'text-success' : (($value->qty < $value->reminder_qty) ? 'text-warning' : 'text-danger')}}">{{($value->qty > 0 && $value->qty > $value->reminder_qty) ? 'In Stock' : (($value->qty < $value->reminder_qty) ? 'Low Stock' : 'Out Of Stock')}}</label>
                                                 
                                              </td>
                                              <td class="action-icon">
                                                  <a href="{{url('stock-details',$value->id)}}" class="p-r-10 text-primary f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>
                                              </td>
                                           </tr>
                                           @endif
                                          @endforeach
                                        @endif
                                       
                                    </tbody>
                                 </table>
                              </div>
                     </div>
                  </div>

</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  var rem_id = [];
      $('.table').DataTable( {

        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Product',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );


  $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");
      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this imaginary file!",
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
                        url: "{{ url('delete-invent')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",id:id},
                        success:function(resp){
                          console.log(resp);
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove vendor.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('inventory-list') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your vendor is safe :)", "error");
           }
        });
  });

  $(".mainchk").on('click',function(){

      if($(this).is(":checked")){
         $("#btn_removeall").removeClass('invisible');

            $(".chkbx").each(function( index ) {
              $(this).attr("checked",true);
            });

      }else {
         $("#btn_removeall").addClass('invisible');
            $(".chkbx").each(function( index ) {
              $(this).attr("checked",false);
            });
      }    
     
  });

  $(".chkbx").on('click',function(){
        if($(this).is(":checked")){
          $("#btn_removeall").removeClass('invisible');

        }else {
          $("#btn_removeall").addClass('invisible');
        }

  });

</script>

@endsection
