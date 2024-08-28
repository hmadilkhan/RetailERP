@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','Switch Branch')

@section('navemployees','active')

@section('navswitch','active')

@section('content')


 <section class="panels-wells">
    <div class="card">

     <div class="card-header">
      <h1 >Branch and Shift Switch Panel</h1>
         <h5 class="card-header-text">Employee List</h5>
         </div>     

       <div class="card-block">

     <table id="tblemp" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
              <th>Image</th>
               <th>Code | Name</th>
               <th>Branch</th>
               <th>Department</th>
               <th>Designation</th>
               <th>Office Shift</th>
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
                   <td >{{$value->emp_acc}} | {{$value->emp_name}}</td>
                   <td >{{$value->branch_name}}</td>
                   <td >{{$value->department_name}}</td>
                   <td >{{$value->designation_name}}</td>
                   <td >{{$value->shiftname}}</td>
                    <td >{{$value->status_name}}</td>
            
                 <td class="action-icon">

                   <a href="{{ url('/details-employee') }}/{{ $value->empid }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt text-primary f-18" ></i> </a>


 <a id="modalcall" onclick="modalopen('{{$value->empid}}','{{$value->emp_name}}','{{$value->branch_name}}','{{$value->shiftname}}','{{$value->ATT_time}}')" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Swtich Branch"><i class="icofont icofont icofont-exchange text-primary f-18" ></i> </a>

            
                 </td>          
                       
                 </tr>
                  @endforeach
     
                
           
         </tbody>
        
      
     </table>
  </div>
</div>
</section>
<!-- Modals -->
<div class="modal fade modal-flex" id="swtich-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Switch Branch</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-6">
                      <div class="form-group"> 
                        <input type="hidden" id="empid">
                        <label class="form-control-label">Employee Name:</label>
                         <h4 id="empname"></h4>
                        </div>
                           </div>
                             <div class="col-md-6">
                      <div class="form-group"> 
                        <label class="form-control-label">Current Branch:</label>
                         <h4 id="currentbranch"></h4>
                        </div>
                           </div>
                            <div class="col-md-6">
                      <div class="form-group"> 
                        <label class="form-control-label">Shift Name:</label>
                         <h4 id="shift"></h4>
                        </div>
                           </div>
                             <div class="col-md-6">
                      <div class="form-group"> 
                        <label class="form-control-label">Total Work Hours:</label>
                         <h4 id="att"></h4>
                        </div>
                           </div>
                           <div class="col-md-6">
                        <div class="form-group"> 
                        <label class="form-control-label">Switch Branch:</label>
                     <select name="branch-modal" id="branch-modal" data-placeholder="Select Branch" class="form-control select2" onchange="getshifts()" >
                    <option value="">Select Branch</option>
                    @if($getbranch)
                      @foreach($getbranch as $value)
                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group"> 
                        <label class="form-control-label">Switch Office Shift:</label>
                     <select name="shift-modal" id="shift-modal" data-placeholder="Select Shift" class="form-control select2" >
                  
                </select>
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_swtich" class="btn btn-success waves-effect waves-light" onClick="swtichbranch()"><i class="icofont icofont-exchange"></i> Switch Branch</button>
             </div>
          </div>
           </div>
        </div> 

@endsection

@section('scriptcode_three')

<script type="text/javascript">

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

function modalopen(id,name,branch,shift,atttime){
   $("#swtich-modal").modal("show");
   $('#empid').val(id);
   $('#empname').html(name);
   $('#currentbranch').html(branch);
   $('#shift').html(shift);
     $('#att').html(atttime + " Hours");
}

function swtichbranch(){
  if ($('#branch-modal').val() == "") {
        swal({
            title: "Error Message",
            text: "Please Select Branch!",
            type: "error"
              });
  }
  else if ($('#shift-modal').val() == "") {
        swal({
            title: "Error Message",
            text: "Please Select Shift!",
            type: "error"
              });
  }
  else{
 swal({
          title: "Are you sure?",
          text: "Do you want to Change?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Yes plx!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/emp-branch-change')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        empid:$('#empid').val(),
                        branchid:$('#branch-modal').val(),
                        shiftid:$('#shift-modal').val(),
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Success",
                                        text: "Operation Performed Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/switch-branch') }}";
                                       }
                                   });
                             }
                             else{
                                  swal({
                                        title: "Error",
                                        text: "Operation Can't Performed on Same Branch!",
                                        type: "error"
                                  });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled:)", "error");
           }
        });
 }
}

function getshifts(){
   $.ajax({
            url: "{{url('/getshifts')}}",
            type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          branchid:$('#branch-modal').val(),
        },
            success:function(resp){  
            $("#shift-modal").empty();          
                     for(var count=0; count < resp.length; count++){
                      $("#shift-modal").append("<option value=''>Select Office Shift</option>");
                      $("#shift-modal").append(
                        "<option value='"+resp[count].shift_id+"'>"+resp[count].shiftname+"</option>");
                  }
             }
          }); 
}  
 

 </script>

@endsection
