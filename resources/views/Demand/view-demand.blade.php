@extends('layouts.master-layout')

@section('title','Demand')

@section('breadcrumtitle','View Demand')

@section('navbranchoperation','active')
@section('navdemand','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Demand List</h5>
         <a href="{{ url('create-demand') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Demand Order
              </a>
         <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>

         </div>      
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>
                  <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                                                <input type="checkbox" id="checkbox32" class="mainchk">
                                                <span class="checkbox"></span>
                                            </label>
                     <div class="captions"></div>
                  </div>
               </th>
               <th>DO No</th>
               <th>Branch</th>
               <th>Generation Date</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
           @foreach($demands as $value)
                 <tr>
                   <td>
                      <div class="rkmd-checkbox checkbox-rotate">
                         <label class="input-checkbox checkbox-primary">
                                                    <input type="checkbox" id="checkbox32" class="chkbx" data-id="{{$value->demand_id}}">
                                                    <span class="checkbox"></span>
                                                </label>
                         <div class="captions"></div>
                      </div>
                   </td>
                   <td>DO-{{$value->demand_id}}</td>
                   <td >{{$value->branch_name}}</td>
                   <td >{{$value->date}}</td>
                   <td >
                    @if($value->name == "Draft")
                    <span class="tag tag-default">  {{$value->name }}</span>
                  @elseif($value->name == "Pending")
                    <span class="tag tag-success">  {{$value->name }}</span>
                  @elseif($value->name == "Approved")
                     <span class="tag tag-info">  {{$value->name }}</span>
                  @elseif($value->name == "Cancel")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                       @elseif($value->name == "Delivered")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                     @elseif($value->name == "In-Process")
                    <span class="tag tag-primary">  {{$value->name }}</span>
                     @elseif($value->name == "Completed")
                    <span class="tag tag-success">  {{$value->name }}</span>
                  @endif
                  </td>
                 <td class="action-icon">
                    
                     <a href="{{ url('/demand-details') }}/{{ Crypt::encrypt($value->demandid) }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt text-primary f-18" ></i> </a>

                     <a href="{{ url('/edit-demand') }}/{{ Crypt::encrypt($value->demandid) }}" class="{{ $value->name != 'Draft' ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $value->name != 'Draft' ? 'Disabled' : 'Edit' }}"><i class="icofont icofont-ui-edit text-{{ $value->name != 'Draft' ? 'muted' : 'warning' }} f-18"></i> </a>

                    <a class="{{ $value->name != 'Draft' ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $value->name != 'Draft' ? 'Disabled' : 'Delete' }}"><i class="icofont icofont-ui-delete text-{{ $value->name != 'Draft' ? 'muted' :'danger'}} f-18"  <?php echo ($value->name != "Draft" ?  '' : ' onclick="btn_remove('.$value->demandid.')" '); ?>   ></i></a>
                 </td>          
                       
                 </tr>
                  @endforeach
          
                
           
         </tbody>
        
      
     </table>
  </div>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  var rem_id = [];
   $('#demandtb').DataTable( {

        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Demand',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );


function btn_remove(id){

    swal({
          title: "Delete",
          text: "Do you want to Delete?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "YES",
          cancelButtonText: "NO",
          closeOnConfirm: false,
          closeOnCancel: false
        },function(isConfirm){
                    if(isConfirm){
                             $.ajax({
                              url: "{{url('/removedemand')}}",
                              type: "PUT",
                              data: {_token:"{{csrf_token()}}",demandid:id,statusid:6},
                              success:function(id){
                                  if (id == 1) {
                                        swal({
                                              title: "Success!",
                                              text: "Demand Deleted Successfully :)",
                                              type: "success"
                                         },function(isConfirm){
                                             if(isConfirm){
                                              window.location="{{url('/demand')}}";
                                             }
                                         });

                                   }else{
                                          swal("Alert!", "Demand not Deleted:)", "error");                       
                                   }

                              }

                             });         
                                 
                    }else {
                         swal("Cancel!", "Your item is safe:)", "error");
                    }
       });
}

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



  $("#btn_removeall").on('click',function(){

      
      swal({
          title: "Delete",
          text: "Do you want to Delete all demand?",
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
                              url: "{{url('/all-demand-remove')}}",
                              type: "PUT",
                              data: {_token:"{{csrf_token()}}",demandid:rem_id,statusid:6},
                              success:function(resp){
                    
                                  if (resp == 1) {
                                        swal({
                                              title: "Success!",
                                              text: "All demand deleted Successfully :)",
                                              type: "success"
                                         },function(isConfirm){
                                             if(isConfirm){
                                              window.location="{{url('/demand')}}";
                                             }
                                         });

                                   }else{
                                          swal("Alert!", "Demand not Deleted:)", "error");                       
                                   }

                              }

                             });
                 }
                 
              }else{
                swal("Cancel!", "Your all demand is safe:)", "error");
              }

        });          


  });


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

@section('scriptcode_two')



@endsection
