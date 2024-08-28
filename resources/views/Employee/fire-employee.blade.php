@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','Fire Employee')

@section('navemployees','active')

@section('navfire','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
      <h1 >Fire Employee Panel</h1>
         <h5 class="card-header-text">Employee List</h5>
         </div>     

       <div class="card-block">

     <table id="tblemp" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

          <thead>
            <tr>
              <th>Image</th>
               <th>Emp Code</th>
               <th>Name</th>
               <th>Contact</th>
               <th>Designation</th>
               <th>Department</th>
               <th>Branch</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
                 @foreach($getemp as $value)
                 <tr>
                     <td class="text-center">
                    <img width="42" height="42" src="{{ asset('assets/images/employees/images/'.(!empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg' }}"/>
                             </td>
                   <td >{{$value->emp_acc}}</td>
                   <td >{{$value->emp_name}}</td>
                   <td >{{$value->emp_contact}}</td>
                   <td >{{$value->designation_name}}</td>
                   <td >{{$value->department_name}}</td>
                   <td >{{$value->branch_name}}</td>
                    <td >{{$value->status_name}}</td>
            
                 <td class="action-icon">

                   <a href="{{ url('/details-employee') }}/{{ $value->empid }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt text-primary f-18" ></i> </a>
                    
                       <i class="icofont icofont-exit text-danger f-18"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Fire Employee"  onclick="modalopen('{{ $value->empid}}','{{$value->id}}')"></i>

                 </td>          
                       
                 </tr>
                  @endforeach
     
                
           
         </tbody>
        
      
     </table>
  </div>
</div>
</section>

<!-- modals -->
 <div class="modal fade modal-flex" id="fire-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Fire Employee</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Enter Reason:</label>
                         <input type="text"  name="reason" id="reason" class="form-control" />
                         <input type="hidden" name="empid" id="empid" >
                         <input type="hidden" name="fireid" id="fireid" >
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light" onClick="fire()">Fire Employee</button>
             </div>
          </div>
           </div>
        </div> 


@endsection

@section('scriptcode_three')

<script type="text/javascript">

function modalopen(id, fireid){
  
  $('#reason').val('');
  $('#empid').val(id);
  $('#fireid').val(fireid);
  $("#fire-modal").modal("show");
}
  
  $(".select2").select2();

      $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Employee',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

 function fire(){
  swal({
          title: "Are you sure?",
          text: "Do you wan to FIre Employee!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Fire Employee!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/remove-employee')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        empid:$('#empid').val(),
                        fireid:$('#fireid').val(),
                        statusid:2,
                        reason:$('#reason').val(),
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Employee Fire Successfully!!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        $("#fire-modal").modal("hide");
                                        window.location="{{ url('/fire-emp-show') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Employee is safe :)", "error");
              $("#fire-modal").modal("hide");
           }
        });

 }

 </script>

@endsection
