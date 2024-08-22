@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','View Employee')

@section('navemployees','active')

@section('navhire','active')

@section('navleaves','active')

@section('content')

<section class="panels-wells">
<div class="card" >
     <div class="card-header">
         <h5 class="card-header-text">Employee Leaves</h5>
            <a class="f-right" onclick="toggle()">
           <i class="icofont icofont-minus"></i>
         </a>
         </div>      
       <div class="card-block" id="insert-card">
       	 <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}
        
           <div class="row">
                  <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Select Employee:</label>
                     <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" >
                    <option value="">Select Employee</option>
                       @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
      <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Select Leave Head:</label>

                  <i id="btn_add" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Leave Head" ></i>
                     <select name="leavehead" id="leavehead" data-placeholder="Select Leave" class="form-control select2" >
                    <option value="">Select Leave</option>
                       @if($leaves)
                      @foreach($leaves as $value)
                        <option value="{{ $value->leave_id }}">{{ $value->leave_head }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
          	 <div class="col-lg-2 col-md-2">
           <div class="form-group">
            <label class="form-control-label">Qty:</label>
            <input type="Number" name="qty" id="qty" class="form-control" />
        </div>
        </div>
               <div class="col-lg-2 col-md-2">
           <div class="form-group">
            <label class="form-control-label">Year:</label>
            <input type="Number" name="year" id="year" class="form-control" />
        </div>
        </div>
        
           </div>
               <button type="Submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > <i class="icofont icofont-plus"> </i>
                        Submit 
                    </button>
                </form>

           </div> 
 </div>


 <div class="card">
     <div class="card-header">
     	
                      <div class="form-group"> 
                        <label class="form-control-label">Select Employee:</label>
                     <select name="employee2" id="employee2" data-placeholder="Select Employee" class="form-control select2" onchange="getdetails($('#employee2').val())" >
                    <option value="">Select Employee</option>
                       @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
         </div>     
       <div class="card-block">
     <table id="tblleaves" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0" style="margin-top: -40px;">

         <thead>
            <tr>
              <th>Leave Head</th>
               <th>Total Qty</th>
               <th>Balance</th>
               <th>Year</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
               
         </tbody>
        
      
     </table>
  </div>
</div>

 
</section>

           <div class="modal fade modal-flex" id="leave-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Leave Modal</h4>
             </div>
             <div class="modal-body">
              <div class="row">
            <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
             <label class="form-control-label">Leave Head</label>
           <input type="text" name="leavemodal" id="leavemodal" class="form-control" value="" />
                        </div>
                      </div>
              </div>
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light" onClick="addleave()">Add Leave Head</button>
             </div>
          </div>
           </div>
        </div> 

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
              <h1 class="f-30 text-primary text-center" id="leaveheadname"></label>
                      </h1>
             <div class="row"> 
            <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
             <label class="form-control-label">Quantity:</label>
           <input type="Number" name="qtymodal" id="qtymodal" class="form-control" value="" />
         <input type="hidden" name="leaveid" id="leaveid" class="form-control" value="" />
                        </div>
                      </div>
              </div>
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-primary waves-effect waves-light" onClick="update()">Update Leave</button>
             </div>
          </div>
           </div>
        </div> 
        
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

     


$('#upload_form').on('submit', function(event){
event.preventDefault();
	if ($('#employee').val() == "") {
		 swal({
            title: "Error Message",
            text: "Please Select Employee!",
            type: "error"
              });
	}
	else if ($('#leavehead').val() == "") {
		 swal({
            title: "Error Message",
            text: "Leave Head Can not left blank!",
            type: "error"
              });
	}
  else if ($('#qty').val() == "" || $('#qty').val() == 0) {
     swal({
            title: "Error Message",
            text: "Quantity Can not left blank!",
            type: "error"
              });
  }
  else if ($('#year').val() == "") {
     swal({
            title: "Error Message",
            text: "Year Can not left blank!",
            type: "error"
              });
  }
	else{
		$.ajax({
    	 url: "{{url('/insert-leavedetails')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(resp){
        if(resp != 0){
             swal({
                    title: "Success",
                    text: "Employee Leaves Added Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                          getdetails($('#employee').val());
                   }
               });
          }
              else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Leave Already exsit!",
                            type: "warning"
                       });
                  }
     }

  });   
	}
}); 


function getdetails(id){

		$.ajax({
    	  url: "{{url('/getleavesdetails')}}",
	        type: 'GET',
	        data:{_token:"{{ csrf_token() }}",
	        employee:id,
	        },
         
    success:function(result){
    	  			$("#tblleaves tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblleaves tbody").append(
                          "<tr>" +
                            "<td>"+result[count].leave_head+"</td>" +  
                            "<td>"+result[count].leave_qty+"</td>" +  
                            "<td>"+result[count].balance+"</td>" +  
                            "<td>"+result[count].year+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><i onclick='edit("+result[count].id+","+"\""+ result[count].leave_head + "\","+"\""+ result[count].leave_qty + "\")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>&nbsp;&nbsp;<i onclick='remove("+result[count].id+","+"\""+ result[count].leave_head + "\") 'class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>"+
                          "</tr>" 
                         );
                    }
		  }
      });
}

//Alert confirm
function remove(id,leave){
 
      swal({
          title: "Are you sure?",
          text: "Do you want to Delete this "+leave+ " ?",
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
                        url: "{{url('/deleteleavesdetails')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Delete Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        getdetails($('#employee2').val());
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled :)", "error");
           }
        });
  }

function edit(id,leave,qty){
  $('#qtymodal').val(qty);
  $('#leaveid').val(id);
  $('#leaveheadname').html(leave);
  $("#update-modal").modal("show");
}

$("#btn_add").on('click',function(){
  $('#leavemodal').val('');
  $("#leave-modal").modal("show");
});



function addleave(){
    $.ajax({
          url: "{{url('/storeleavehead')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          leavehead:$('#leavemodal').val(),
          },
          success:function(resp){
         if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Leave Head Created Successfully!",
                      type: "success"});
                      $("#leave-modal").modal("hide");
                      $("#leavehead").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#leavehead").append("<option value=''>Select Leave</option>");
                      $("#leavehead").append(
                        "<option value='"+resp[count].leave_id+"'>"+resp[count].leave_head+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Leave Head Already exsit!",
                            type: "warning"
                       });
                  }
          }

      });
}  


function toggle(){

  $('#insert-card').toggle();
}

  $('#insert-card').hide();


function update(){
  if ($('#qtymodal').val() == "" || $('#qtymodal').val() == 0 ) {
    swal({
            title: "Error Message",
            text: "Enter Quantity was incorrect!",
            type: "error"
        });
  }
  else{
     $.ajax({
            url: "{{url('/updateleavedetails')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            id:$('#leaveid').val(),
            qty:$('#qtymodal').val(),
            },
            success:function(resp){
                if(resp == 1){
                     swal({
                            title: "Success",
                            text: "Quantity Updated Successfully!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                            $("#update-modal").modal("hide");
                            getdetails($('#employee2').val());
                           }
                       });
                 }
            }

        });
  }
}
 

 </script>


@endsection




