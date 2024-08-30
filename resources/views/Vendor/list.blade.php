@extends('layouts.master-layout')

@section('title','Vendor')

@section('breadcrumtitle','View Vendor')
@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Vendors List</h5>
         <a href="{{ route('vendors.create') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Vendor" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE VENDOR
              </a>
             
         </div>      
       <div class="card-block">
           <ul class="nav nav-tabs md-tabs " role="tablist">
               <li class="nav-item">
                   <a class="nav-link active" id="act" data-toggle="tab" href="#home7" role="tab">Active</a>
                   <div class="slide"></div>
               </li>
               <li class="nav-item">
                   <a class="nav-link" id="pro" data-toggle="tab" href="#profile7" role="tab">InActive</a>
                   <div class="slide"></div>
               </li>
           </ul>
           <div class="tab-content">
               <div class="tab-pane active" id="home7" role="tabpanel">
                   <div class="project-table m-t-20">

                        <table class="table table-striped nowrap dt-responsive m-t-10" width="100%">
                       <thead>
                       <tr>

                           <th>Preview</th>
                           <th>Vendor Name | Balance</th>
                           <th>Contact</th>
{{--                           <th>Email</th>--}}
                           <th>Payment Terms</th>
                           <th>Action</th>

                       </tr>
                       </thead>
                       <tbody>
                       @if($vendor)
                           @foreach($vendor as $value)
                               @if($value->status_id == 1)
                                <tr>

                                   <td class="text-center">
                                       <img src="{{ asset('storage/images/vendors/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                   </td>
                                   <td class="pro-name">
                                       {{$value->vendor_name."   | ".number_format($value->balance,2)}}
                                       <br/>
                                       <span class="text-muted f-12">{{$value->company_name}}</span><br/>
{{--                                       <span class="text-muted f-12">{{number_format($value->balance,2)}}</span>--}}
                                   </td>
                                   <td>{{$value->vendor_contact}}</td>
{{--                                   <td>{{$value->vendor_email }} </td>--}}
                                    <td>{{$value->payment_terms }} Days</td>
                                  <!-- <td class="action-icon">
                                       <a href="{{ url('/ledgerlist', $value->slug) }}" class="p-r-10 text-info f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ledger"><i class="icofont icofont-money-bag"></i></a>

                                        <a href="{{ route('vendors.edit', $value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>

                                       <a class="icofont icofont-ui-delete text-danger f-18 " onclick="deletevendor('{{ $value->id }}','\'{{$value->vendor_name}}\'','\'{{$value->company_name}}\'')" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></a>

                                       <a href="{{ url('add-vendor-product', $value->slug) }}" class="p-r-20 f-18 text-info m-l-1 " data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Vendor Product"><i class="icofont icofont-ui-add"></i></a>

                                       <a href="{{ url('vendor-po', $value->slug) }}" class="p-r-10 f-18 text-pink " data-toggle="tooltip" data-placement="top" title="" data-original-title="Vendor PO's"><i class="icofont icofont-list"></i></a>

                                   </td> -->
								    <td class="p-relative">
										 <a class="dropdown-toggle addon-btn" data-toggle="dropdown" aria-expanded="true">
											<i class="icofont icofont-ui-settings"></i>
										 </a>
										 <div class="dropdown-menu dropdown-menu-right">
											<a class="dropdown-item" href="{{ route('vendors.edit', $value->id) }}"><i class="icofont icofont-ui-edit text-warning m-r-1"></i>Edit</a>
											<a class="dropdown-item" href="{{ url('/ledgerlist', $value->slug) }}"><i class="icofont icofont-money-bag text-info m-r-1"></i>Ledger</a>
											<a class="dropdown-item" href="{{ url('add-vendor-product', $value->slug) }}"><i class="icofont icofont-ui-add text-info m-r-1"></i>Add Vendor Product</a>
											<a class="dropdown-item" href="{{ url('vendor-po', $value->slug) }}"><i class="icofont icofont-list text-pink m-r-1"></i>Vendor Purchases</a>
											<a class="dropdown-item" href="{{ url('advance-payment-view', $value->id) }}"><i class="icofont icofont-money text-purple m-r-1"></i>Vendor Advances</a>
											
											<div role="separator" class="dropdown-divider"></div>
											<a class="dropdown-item" onclick="deletevendor('{{ $value->id }}','\'{{$value->vendor_name}}\'','\'{{$value->company_name}}\'')" data-id="{{ $value->id }}"><i class="icofont icofont-ui-delete text-danger m-r-1"></i>Delete</a>
										 </div>
                                    </td>
                               </tr>
                               @endif
                           @endforeach
                       @endif
                       </tbody>
                   </table>
                   </div>
               </div>
               <div class="tab-pane" id="profile7" role="tabpanel">
                   <div class="project-table m-t-20">
                       <table class="table table-striped nowrap dt-responsive m-t-10" width="100%">
                           <thead>
                           <tr>

                               <th>Preview</th>
                               <th>Vendor Name</th>
                               <th>Vendor Contact</th>
{{--                               <th>Email</th>--}}
                               <th>Payment Terms</th>
                               <th>Action</th>

                           </tr>
                           </thead>
                           <tbody>
                           @if($vendor)
                               @foreach($vendor as $value)
                                   @if($value->status_id == 2)
                                       <tr>

                                           <td class="text-center">
                                               <img src="{{ asset('storage/images/vendors/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                           </td>
                                           <td class="pro-name">
                                               {{$value->vendor_name}}
                                               <br/>
                                               <span class="text-muted f-12">{{$value->company_name}}</span>
                                           </td>
                                           <td>{{$value->vendor_contact}}</td>
{{--                                           <td>{{$value->vendor_email }} </td>--}}
                                           <td>{{$value->payment_terms }} Days</td>
                                           <td class="action-icon">
                                               <i  onclick="item_inactive('{{$value->id}}')" class="icofont icofont-ui-check text-success f-18" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="RE-ACTIVE"></i>
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


        </div>
    </div>
   </div>
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
var rem_id = [];

   $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: true,
        language: {
          search:'', 
          searchPlaceholder: 'Search Vendor',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

   function deletevendor(id,name,company_name)
   {  
      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover vendor "+name+" from "+company_name+" company again!",
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
                        url: "{{ url('vendors')}}"+"/"+id,
                        type: 'DELETE',
                        data:{_token:"{{ csrf_token() }}"},
                        dataType:"json",
                        success:function(resp){
                            if(resp.result == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove vendor.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ route('vendors.index') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your vendor is safe :)", "error");
           }
        });
   }

function item_inactive(id){

    swal({
            title: "Are you sure?",
            text: "This vendor will be active again !!!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Active it!",
            cancelButtonText: "cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if(isConfirm){
                $.ajax({
                    url: "{{ url('active-vendor')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",id:id},
                    success:function(resp){

                        if(resp == 1){
                            swal({
                                title: "Activated",
                                text: "Vendor activated Successfully .",
                                type: "success"
                            },function(isConfirm){
                                if(isConfirm){
                                    window.location="{{ url('vendors') }}";
                                }
                            });
                        }
                    }

                });

            }else {
                swal("Cancelled", "Your Item is safe :)", "error");
            }
        });
}


  //Alert confirm
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
                        url: "{{ url('vendors')}}"+"/"+id,
                        type: 'DELETE',
                        data:{_token:"{{ csrf_token() }}"},
                        dataType:"json",
                        success:function(resp){
                            if(resp.result == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove vendor.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ route('vendors.index') }}";
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


$("#btn_removeall").on('click',function(){

      
      swal({
          title: "Delete",
          text: "Do you want to remove all vendor?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "YES",
          cancelButtonText: "NO",
          closeOnConfirm: false,
          closeOnCancel: false
        },function(isConfirm){
              if(isConfirm){
                 
                 $(".chkbx").each(function( index ) {

                    if($(this).is(":checked")){
                       if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                           rem_id.push($(this).data('id'));
                        
                       }
                    }
                     
                  });

                 if(rem_id.length > 0){

                          $.ajax({
                              url: "{{url('/all-vendors-remove')}}",
                              type: "PUT",
                              data: {_token:"{{csrf_token()}}",id:rem_id},
                              success:function(resp){
                           
                                  if (resp == 1) {
                                        swal({
                                              title: "Success!",
                                              text: "All vendor remove Successfully :)",
                                              type: "success"
                                         },function(isConfirm){
                                             if(isConfirm){
                                              window.location="{{route('vendors.index')}}";
                                             }
                                         });

                                   }else{
                                          swal("Alert!", "Vendor not removed:)", "error");                       
                                   }

                              }

                             });
                 }
                 
              }else{
                swal("Cancel!", "Your all vendor is safe:)", "error");
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
