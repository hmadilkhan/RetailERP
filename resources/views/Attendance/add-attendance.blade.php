@extends('layouts.master-layout')

@section('title','Manually Attendance')

@section('breadcrumtitle','Manually Attendance')

@section('navattendance','active')

@section('navaddattendance','active')

@section('content')
<section class="panels-wells"> 
  <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Manually Attendance</h5>
            <div class="new-users-more text-left p-t-10">
        <a href="{{ url('/dailyattendance-view') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to Panel</h6></a>
        </div>
         </div>      
       <div class="card-block">
        <form method="post" action="{{url('/submitattendance')}}" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}

         <div class="row">
           <div class="col-lg-4 col-md-4">
                           <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Branch:</label>
                                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" onchange="getemp()" >
                                    <option value="">Select Branch</option>
                                    @if($getbranch)
                                      @foreach($getbranch as $value)
                                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                    @if ($errors->has('branch'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                            </div>
                        </div>

                 <div class="col-lg-4 col-md-4">
                           <div class="form-group {{ $errors->has('employee') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Employee:</label>
                                <select name="employee" id="employee" data-placeholder="Select Branch" class="form-control select2" >
                                </select>
                    @if ($errors->has('employee'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                            </div>
                        </div>
                                  <div class="col-lg-3 col-md-3">
       <div class="form-group {{ $errors->has('attendancedate') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Attendance Date:</label>
                  <input class="form-control" type="text" 
                   name="attendancedate" id="attendancedate" value="{{ old('attendancedate') }}"   />
                    @if ($errors->has('attendancedate'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>

                    <div class="col-lg-1  col-sm-12">
                    <div class="form-group">
                           <button type="button" id="btndetails"  class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="getdetails()">
                                  <i class="icofont icofont-search"> </i>
                            </button>
                    </div>       
                </div> 


      
        </div>
         <div class="row">
         
           <div class="col-lg-3 col-md-3">
       <div class="form-group {{ $errors->has('clockin') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Clock In:</label>
                  <input class="form-control" type="time" 
                   name="clockin" id="clockin" value="{{ old('clockin') }}" onchange="latecount()"  />
                    @if ($errors->has('clockin'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
                  <div class="col-lg-3 col-md-3">
       <div class="form-group {{ $errors->has('clockout') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Clock Out:</label>
                  <input class="form-control" type="time" 
                   name="clockout" id="clockout" value="{{ old('clockout') }}" onchange="earlycount()"  />
                    @if ($errors->has('clockout'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
                        <div class="col-lg-2 col-md-2">
       <div class="form-group {{ $errors->has('late') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Late Count:</label>
                  <input class="form-control" type="Number"
                   name="late" readonly="readonly" id="late" value="{{ old('late') }}"  />
                    @if ($errors->has('late'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
                        <div class="col-lg-2 col-md-2">
       <div class="form-group {{ $errors->has('early') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Early Count:</label>
                  <input class="form-control" type="Number" 
                   name="early" id="early" readonly="readonly" value="{{ old('early') }}"  />
                    @if ($errors->has('early'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>

                <div class="col-lg-2 col-md-2">
       <div class="form-group {{ $errors->has('ot') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">OT Count:</label>
                  <input class="form-control" type="Number" 
                   name="ot" id="ot" readonly="readonly" value="{{ old('ot') }}"  />
                   <input type="hidden" name="attendanceid" id="attendanceid" >
                    @if ($errors->has('ot'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
         
                </div>
                   <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-finger-print"> </i>
                        Mark as Present
                    </button>
                     <button type="button" id="btnabsent" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20" onclick="absent()"><i class="icofont icofont-close"> </i>
                        Mark as Absent
                    </button>
                    </div>       
                </div>  
            </div> 

      <!-- <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >  </button> -->
               </form>  
           </div> 

           
 </div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
$(".select2").select2();

  $('#attendancedate').bootstrapMaterialDatePicker({
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

function getemp(){
        $.ajax({
            url: "{{url('/getemployees')}}",
            type: 'GET',
            data:{_token:"{{ csrf_token() }}",
            branchid: $('#branch').val(),
          },
            success:function(resp){              
                if(resp){
                $("#employee").empty();
                 $("#employee").append("<option value=''>Select Employee</option>");
                     for(var count=0; count < resp.length; count++){
                      $("#employee").append(
                      "<option value='"+resp[count].empid+"'>"+resp[count].emp_name+"</option>");
                     }
                    }
           }
          }); 
}

function getdetails(){
  if ($('#branch').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Branch First!",
          type: "error"
     });
}
else if ($('#employee').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Employee First!",
          type: "error"
     });
}
else if ($('#attendancedate').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Date First!",
          type: "error"
     });
}
else{
        $.ajax({
            url: "{{url('/getattendancedetails')}}",
            type: 'GET',
            data:{_token:"{{ csrf_token() }}",
            branchid: $('#branch').val(),
            empid:$('#employee').val(),
            date:$('#attendancedate').val(),
          },
            success:function(resp){
                console.log(resp);
              if (resp == 1) {
                     // swal({
                     //        title: "Message",
                     //        text: "Absent Data Found!",
                     //        type: "warning"
                     //   },function(isConfirm){
                     //       if(isConfirm){
                    
                     //       }
                     //   });
                     absent_delete();
                    
              }
              else if (resp == 2) {
                         swal({
                            title: "Message",
                            text: "Date Marked as a Weekend!",
                            type: "warning"
                       },function(isConfirm){
                           if(isConfirm){
                             $('#btnsubmit').attr('disabled','disabled');
                            $('#btnabsent').attr('disabled','disabled');
                           }
                       });
              }
              else if (resp == 3) {
                         swal({
                            title: "Message",
                            text: "Date Marked as a Holiday!",
                            type: "warning"
                       },function(isConfirm){
                           if(isConfirm){
                          $('#btnsubmit').attr('disabled','disabled');
                          $('#btnabsent').attr('disabled','disabled');
                           }
                       });
              }
               else if(resp != 0){
                  $('#clockin').val(resp[0].clock_in);
                  $('#clockout').val(resp[0].clock_out);
                  $('#late').val(resp[0].late);
                  $('#early').val(resp[0].early);
                  $('#ot').val(resp[0].OT_time);
                  $('#attendanceid').val(resp[0].attendance_id);
                  $('#btnsubmit').attr('disabled','disabled');
                  $('#btnabsent').attr('disabled','disabled');
                  }
                  else{
                       swal({
                            title:"Message",
                            text: "Data Not found",
                            type: "warning"
                       });
                        $('#btnsubmit').attr('disabled',false);
                        $('#btnabsent').attr('disabled',false);

                  $('#clockin').val('');
                  $('#clockout').val('');
                  $('#late').val('');
                  $('#early').val('');
                  $('#ot').val('');
                  }
           }
          }); 
      }
}

function latecount(){
 
        $.ajax({
            url: "{{url('/getgracetime')}}",
            type: 'GET',
            data:{_token:"{{ csrf_token() }}",
            empid:$('#employee').val(),
          },
            success:function(resp){    
                if(resp){
                  let start = resp[0].shift_start;
                  let end = $('#clockin').val();
                  let timeStart = new Date("01/01/2007 " + start);
                  let timeEnd = new Date("01/01/2007 " + end);
                  let difference = ((timeEnd - timeStart) / 60 / 1000);
                  if (difference > resp[0].grace_time_in ) {
                    let late = difference - resp[0].grace_time_in;
                     $('#late').val(late);
                  }
                  else if (difference < resp[0].grace_time_in) {
                     $('#late').val(0);
                  }
                  else{
                   $('#late').val(0);

                  }
                  }
           }
          }); 
}

function earlycount(){

        $.ajax({
            url: "{{url('/getgracetime')}}",
            type: 'GET',
            data:{_token:"{{ csrf_token() }}",
            empid:$('#employee').val(),
          },
            success:function(resp){  
                if(resp){
                  // let end = '{{date('h:i',strtotime('+resp[0].shift_end+'))}}';
                  let start = $('#clockout').val();
                  let timeStart = new Date("01/01/2007 " + start);
                  let timeEnd = new Date("01/01/2007 " + resp[0].shift_end);
                  let difference = ((timeEnd - timeStart) / 60 / 1000);
                  if (difference > resp[0].grace_time_out ) {
                    let early = difference - resp[0].grace_time_out;
                     $('#early').val(early);
                  }
                  else if (difference < resp[0].grace_time_out) {
                     $('#early').val(0);
                     let early = difference - resp[0].grace_time_out;
                     early = early * (-1);
                     if (early == resp[0].grace_time_out) {
                        $('#ot').val(0);
                     }
                     else if(early > resp[0].grace_time_out){
                       $('#ot').val(early);
                     }
                     else{
                      $('#ot').val(0);
                     }
                  }
                  else{
                   $('#early').val(0);
                  }
                  }
           }
          }); 
}

function absent_delete(){
  swal({
          title: "Absent Data Found!!",
          text: "Do You want to Present this Employee!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "yes plx!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/absent_delete')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        empid:$('#employee').val(),
                        date:$('#attendancedate').val(),
                        },
                        success:function(resp){
                          $('#btnsubmit').attr('disabled',false);
                         $('#btnabsent').attr('disabled',false);
                             swal({
                            title:"Message",
                            text: "Please Enter Clock In and Clock Out!",
                            type: "success"
                       });
                        }
                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled:)", "error");
           }
        });
}


function absent(){
    if ($('#branch').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Branch First!",
          type: "error"
     });
}
else if ($('#employee').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Employee First!",
          type: "error"
     });
}
else if ($('#attendancedate').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Date First!",
          type: "error"
     });
}
else{

   $.ajax({
            url: "{{url('/absent_insert')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            employee:$('#employee').val(),
            attendancedate:$('#attendancedate').val(),
          },
            success:function(resp){ 
            if(resp == 1){
                         swal({
                            title: "success",
                            text: "Employee Marked as Absent!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                            window.location="{{ url('/manuallyattendance') }}";
                           }
                       });
           }
           else{
              swal({
          title: "Already Exsist!",
          text: "Employee already marked as Absent!",
          type: "error"
     });
           }  
          
    }
}); 



  
    }
}


</script>
@endsection  