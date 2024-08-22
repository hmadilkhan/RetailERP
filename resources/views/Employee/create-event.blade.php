@extends('layouts.master-layout')

@section('title','Company Events')

@section('breadcrumtitle','Company Events')

@section('navmanage','active')

@section('navevents','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Set Company Events</h5>
          <!-- <h6 class=""><a href="{{ url('/view-employee') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6> -->
         </div>      
       <div class="card-block">
        <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}

           <div class="row">
          <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Branch</label>
                 
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
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
           	  <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Event Name</label>
                 <input type="text" name="event" id="event" class="form-control"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
                  <div class="col-lg-4 col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Date of Event</label>
            <input type="text" name="doe" id="doe" class="form-control" placeholder="14-08-2020"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
      
            </div>
             <div class="col-lg-12 col-sm-12">
                <div class="button-group ">
                      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-plus"> </i>Submit</button>
                    </div>       
                </div>  
            
               </form> 

        
           </div> 
 </div>
 <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Events Details</h5>
         </div>      
       <div class="card-block">
         <table id="tblevents" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Branch</th>
               <th>Event Name</th>
               <th>Event Date</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            @foreach($details as $value)
                 <tr>
                   <td >{{$value->branch_name}}</td>
                    <td >{{$value->event_name}}</td>
                    <td >{{$value->event_date}}</td>
                 <td class="action-icon">
                     <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="changefun('{{$value->event_id}}','{{$value->event_name}}','{{$value->event_date}}','{{$value->branch_id}}')"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                    <i class="icofont icofont-ui-delete text-danger f-18" onclick="remove('{{$value->event_id}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
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
                     <div class="col-md-6">
                      <div class="form-group"> 
                        <input type="hidden" name="eventid" id="eventid" value="">
                        <input type="hidden" name="branchmodal" id="branchmodal" value="">
                <label class="form-control-label">Event Name</label>
                 <input type="text" name="eventmodal" id="eventmodal" class="form-control"/>
                 </div>
                 </div>

                  <div class="col-md-6">
                <div class="form-group">
                  <label class="form-control-label">Date of Event</label>
            <input type="text" name="doemodal" id="doemodal" class="form-control" placeholder="14-08-2020"/>
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

   $('#doe,#doemodal').bootstrapMaterialDatePicker({
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
	else if ($('#event').val() == "") {
		 swal({
            title: "Error Message",
            text: "Please Enter Event Name!",
            type: "error"
              });
	}
	else if ($('#doe').val() == "") {
		swal({
            title: "Error Message",
            text: "Please Select Date!",
            type: "error"
             });
	}
	else{
    $.ajax({
    	 url: "{{url('/insert-events')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(result){
        if(result != 0){
             swal({
                    title: "Success",
                    text: "Event Addedd Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                        $("#tblevents tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblevents tbody").append(
                          "<tr>" +
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].event_name+"</td>" +  
                            "<td>"+result[count].event_date+"</td>" +  
                            "<td class='action-icon'><i onclick='changefun("+result[count].event_id+")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i><i onclick='remove("+result[count].event_id+")'class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>"
                            +
                          "</tr>" 
                         );
                    }
                   }
               });
          }
           else{
                    swal({
                            title: "Already exsit",
                            text: "Event Already Assign!",
                            type: "warning"
                       });
                  }
     }

  });        
}
});

function changefun(eventid,name,date,branchid){
   $('#eventid').val(eventid);
   $('#eventmodal').val(name);
   $('#doemodal').val(date);
   $('#branchmodal').val(branchid);
  $("#update-modal").modal("show");
}

function update(){
$.ajax({
          url: "{{url('/update-events')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          eventid: $('#eventid').val(),
          eventmodal:$('#eventmodal').val(),
          doemodal:$('#doemodal').val(),
          branchmodal:$('#branchmodal').val(),
        },
         success:function(result){
        if(result != 0){
             swal({
                    title: "Success",
                    text: "Event Updated Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                        $("#tblevents tbody").empty();
                        $("#update-modal").modal("hide");
                    for(var count =0;count < result.length; count++){
                        $("#tblevents tbody").append(
                          "<tr>" +
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].event_name+"</td>" +  
                            "<td>"+result[count].event_date+"</td>" +  
                            "<td class='action-icon'><i onclick='changefun("+result[count].event_id+")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i><i onclick='remove("+result[count].event_id+")'class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>"+
                          "</tr>" 
                         );
                    }
                   }
               });
          }
           else{
                    swal({
                            title: "Already exsit",
                            text: "Event Already Assign!",
                            type: "warning"
                       });
                  }
     }
          }); 
}


function remove(id){
      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this!",
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
                        url: "{{url('/delete-events')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Event Deleted Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/showevent') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Event is safe :)", "error");
           }
        });
  }


 </script>

@endsection


