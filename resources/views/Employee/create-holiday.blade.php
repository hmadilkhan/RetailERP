@extends('layouts.master-layout')

@section('title','Holidays')

@section('breadcrumtitle','Company Holidays')

@section('navmanage','active')

@section('navholiday','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Set Holidays</h5>
          <!-- <h6 class=""><a href="{{ url('/view-employee') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6> -->
         </div>      
       <div class="card-block">
        <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}

           <div class="row">
          <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Branch</label>
                 
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" onchange="getemp()" >
                    <option value="">Select Branch</option>
                    @if($getbranch)
                      @foreach($getbranch as $value)
                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                      @endforeach
                    @endif
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
           	<!--   <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Employee</label>
                 
                <select name="emp" id="emp" data-placeholder="Select Employee" class="form-control select2">
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div> -->

        <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Select Day Off</label>
                 
                <select name="holiday" id="holiday" data-placeholder="Select Day off" class="form-control select2" >
                    <option value="">Select Day off</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>                        
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
            </div>

      
             <div class="col-lg-12 col-sm-12">
                <div class="button-group ">
                      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-plus"> </i>
                        Assign Holiday
                    </button>
                    </div>       
                </div>  
            
               </form> 

        
           </div> 
 </div>
 <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Holiday Details</h5>
         </div>      
       <div class="card-block">
         <table id="tblholiday" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Branch</th>
               <th>Holiday</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
                @foreach($details as $value)
                 <tr>
                   <td >{{$value->branch_name}}</td>
                    <td >{{$value->day_off}}</td>
                 <td class="action-icon">
                     <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="changefun('{{$value->id}}')"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>
                 </td>          
                 </tr>
                  @endforeach
      
           </tbody>
     </table>
     </div>      
          </div>  




</section>

 <!-- modals -->
 <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Update Modal</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <input type="hidden" name="empid" id="empid" value="">
                        <label class="form-control-label">Select Day Off:</label>
                     <select name="holidaymodal" id="holidaymodal" data-placeholder="Select Day off" class="form-control select2" >
                    <option value="">Select Day off</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>                        
                </select>
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect waves-light" onClick="update()">Chnage Day off</button>
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

function getemp(){
   $.ajax({
            url: "{{url('/getempmonthly')}}",
            type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          branchid:$('#branch').val(),
        },
            success:function(resp){ 
            console.log(resp);
            $("#emp").empty();          
                     for(var count=0; count < resp.length; count++){
                      $("#emp").append("<option value=''>Select Employee</option>");
                      $("#emp").append(
                        "<option value='"+resp[count].empid+"'>"+resp[count].emp_name+"</option>");
                  }
             }
          }); 
}   
  

$('#upload_form').on('submit', function(event){
event.preventDefault();
	if ($('#branch').val() == "") {
    swal({
            title: "Error Message",
            text: "Please Select Branch!",
            type: "error"
              });
  }
	// else if ($('#emp').val() == "") {
	// 	 swal({
 //            title: "Error Message",
 //            text: "Please Select Employee!",
 //            type: "error"
 //              });
	// }
	else if ($('#holiday').val() == "") {
		swal({
            title: "Error Message",
            text: "Please Select Day Off!",
            type: "error"
             });
	}
	else{

    $.ajax({
    	 url: "{{url('/insert-holiday')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(result){
        if(result != 0){
             swal({
                    title: "Success",
                    text: "Day Off Assign Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                        $("#tblholiday tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblholiday tbody").append(
                          "<tr>" +
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].day_off+"</td>" +  
                            "<td class='action-icon'><i onclick='changefun("+result[count].id+")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+
                          "</tr>" 
                         );
                    }
                   }
               });
          }
           else{
                    swal({
                            title: "Already exsit",
                            text: "Day off Already Assign!",
                            type: "warning"
                       });
                  }
     }

  });        
}
});

function changefun(id){
   $('#empid').val(id);
  $("#update-modal").modal("show");
}

function update(){
$.ajax({
          url: "{{url('/update-holiday')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          id:$('#empid').val(),
          holiday:$('#holidaymodal').val(),
        },
         success:function(result){
          $("#update-modal").modal("hide");
             swal({
                    title: "Success",
                    text: "Day Off Updated Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                        $("#tblholiday tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblholiday tbody").append(
                          "<tr>" +
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].day_off+"</td>" +  
                            "<td class='action-icon'><i onclick='changefun("+result[count].id+")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+
                          "</tr>" 
                         );
                    }
                   }
               });
          
     }
          }); 
}




 </script>

@endsection


