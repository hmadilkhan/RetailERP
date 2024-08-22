@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','View Employee')

@section('navemployees','active')

@section('navhire','active')

@section('navallowance','active')

@section('content')

<section class="panels-wells">
<div class="card" >
     <div class="card-header">
         <h5 class="card-header-text">Employee Allowance</h5>
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
                        <label class="form-control-label">Select Allowance Head:</label>

                  <i id="btn_add" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Allowance Head" ></i>
                     <select name="allowancehead" id="allowancehead" data-placeholder="Select Allowance" class="form-control select2" >
                    <option value="">Select Allowance</option>
                       @if($allowance)
                      @foreach($allowance as $value)
                        <option value="{{ $value->allowance_id }}">{{ $value->allowance_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
          	 <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Amount:</label>
            <input type="text" name="amount" id="amount" class="form-control" />
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
     <table id="tblallowances" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0" style="margin-top: -40px;">

         <thead>
            <tr>
              <th>Allowance Head</th>
               <th>Amount</th>
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

           <div class="modal fade modal-flex" id="allowance-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Allowance Modal</h4>
             </div>
             <div class="modal-body">
              <div class="row">
            <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
             <label class="form-control-label">Allowance Head</label>
           <input type="text" name="allowancemodal" id="allowancemodal" class="form-control" value="" />
                        </div>
                      </div>
              </div>
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light" onClick="addallowance()">Add Allowance</button>
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
              <h1 class="f-30 text-primary text-center" id="allowancename"></label>
                      </h1>
             <div class="row"> 
            <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
             <label class="form-control-label">Amount</label>
           <input type="Number" name="amountmodal" id="amountmodal" class="form-control" value="" />
         <input type="hidden" name="allowanceid" id="allowanceid" class="form-control" value="" />
                        </div>
                      </div>
              </div>
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-primary waves-effect waves-light" onClick="update()">Update Allowance</button>
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
	else if ($('#allowancehead').val() == "") {
		 swal({
            title: "Error Message",
            text: "Allowance Head Can not left blank!",
            type: "error"
              });
	}
  else if ($('#amount').val() == "") {
     swal({
            title: "Error Message",
            text: "Amount Can not left blank!",
            type: "error"
              });
  }
	else{
		$.ajax({
    	 url: "{{url('/storeallowancedetails')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(resp){
        if(resp != 0){
             swal({
                    title: "Success",
                    text: "Employee Allowance Added Successfully!",
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
                            text: "Particular Allowance Already exsit!",
                            type: "warning"
                       });
                  }
     }

  });   
	}
}); 


function getdetails(id){

		$.ajax({
    	  url: "{{url('/getallowancesdetails')}}",
	        type: 'GET',
	        data:{_token:"{{ csrf_token() }}",
	        employee:id,
	        },
         
    success:function(result){
    	  			$("#tblallowances tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblallowances tbody").append(
                          "<tr>" +
                            "<td>"+result[count].allowance_name+"</td>" +  
                            "<td>"+result[count].amount+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><i onclick='edit("+result[count].id+","+"\""+ result[count].allowance_name + "\","+"\""+ result[count].amount + "\")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>&nbsp;&nbsp;<i onclick='remove("+result[count].id+","+"\""+ result[count].allowance_name + "\") 'class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>"+
                          "</tr>" 
                         );
                    }
		  }
      });
}

//Alert confirm
function remove(id,allowance){
 
      swal({
          title: "Are you sure?",
          text: "Do you want to Delete this "+allowance+ " ?",
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
                        url: "{{url('/deleteallowance')}}",
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

function edit(id,allowance,amount){
  $('#amountmodal').val(amount);
  $('#allowanceid').val(id);
  $('#allowancename').html(allowance);
  $("#update-modal").modal("show");
}

$("#btn_add").on('click',function(){
  $('#allowancemodal').val('');
  $("#allowance-modal").modal("show");
});



function addallowance(){
    $.ajax({
          url: "{{url('/storeallowance')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          allowance:$('#allowancemodal').val(),
          },
          success:function(resp){
         if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Allowance Head Created Successfully!",
                      type: "success"});
                      $("#allowance-modal").modal("hide");
                      $("#allowancehead").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#allowancehead").append("<option value=''>Select Allowance</option>");
                      $("#allowancehead").append(
                        "<option value='"+resp[count].allowance_id+"'>"+resp[count].allowance_name+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Allowance Head Already exsit!",
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
  if ($('#amountmodal').val() == "" || $('#amountmodal').val() == 0 ) {
    swal({
            title: "Error Message",
            text: "Enter Amount was incorrect!",
            type: "error"
        });
  }
  else{
     $.ajax({
            url: "{{url('/updateallowance')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            id:$('#allowanceid').val(),
            amount:$('#amountmodal').val(),
            },
            success:function(resp){
                if(resp == 1){
                     swal({
                            title: "Success",
                            text: "Amount Updated Successfully!",
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


