@extends('layouts.master-layout')

@section('title','Daily Attendance')

@section('breadcrumtitle','Daily Attendance')

@section('navattendance','active')

@section('navdailyattendance','active')

@section('content')
<style>
.outer {
    width: 100%;
    height: 300px;
    white-space: nowrap;
    position: relative;
    overflow-x: scroll;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
}

.outer .inner {
    width: 45%;
    background-color: #eee;
    float: none;
    height: 90%;
    margin: 0 0.25%;
    display: inline-block;
    zoom: 1;
}
</style>


<section class="panels-wells">
 <!--    <button type="button" id="btnsubmit" class="btn btn-md btn-info waves-effect waves-light m-l-20 f-right" onclick="genmonth()"><i class="icofont icofont-refresh"> </i>
         Generate Month Dates
      </button> -->
  <!-- <a href="{{url('dailyattendance-edit')}}">Go to Edit</a> -->
  <!--  <button type="button" id="btnsubmit" class="btn btn-md btn-info waves-effect waves-light m-l-20 f-right" onclick="upload()"><i class="icofont icofont-refresh"> </i>
          Sync and Refresh
      </button> -->
  <!-- <a  onclick="upload()"><i class="icofont icofont-refresh f-40 text-primary"></i></a> -->

  <div class="card-block outer">
                  <div class="row">
                    <div class="wrapper">
                      <div id="draggablePanelList "> 
                        @foreach($details as $value)
                            <div class="col-lg-3 col-md-6 inner">
                              <div class="card">
                                <div class="card-block">
                                    <div class="media-left media-middle">
                                 <a href="#">
                                    <img class="media-object img-circle" src="{{ asset('public/assets/images/branch/'.(!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg').'') }}" width="100" height="100">
                                 </a>
                              </div>
                            <div class="media-body">
                                 <h5 class="media-heading m-b-15">{{$value->branch_name}}</h5>
                                 <h2 class="media-heading m-b-15 text-info">{{$value->employees}}</h2>
                                 <h6 class="f-w-300">Total Employe</h6>
                              </div>
                                   
                                       <div class=" social-card">
                                         <div class="col-xs-4 bg-success" style="cursor: pointer;" onclick="getabsent('{{$value->branch_id}}','Present')">
                                 <div class="social-media">

                                    <i class="icofont icofont-tick-mark"></i>
                                    <span>Present</span>
                                    <h5 class="">{{$value->present}}</h5>

                                 </div>
                              </div>
                               <div class="col-xs-4 bg-danger" style="cursor: pointer;" onclick="getabsent('{{$value->branch_id}}','Absent')">
                                 <div class="social-media">
                                    <i class="icofont icofont-close"></i>
                                    <span>Absent</span>
                                    <h5 class="">{{$value->absent}}</h5>
                                 </div>
                              </div>
                              <div class="col-xs-4 bg-primary" style="cursor: pointer;" onclick="getsheet('{{$value->branch_id}}','')">
                                
                                 <div class="social-media">
                                    <i class="icofont icofont-eye-alt"></i><br>
                                    <span>Attendance</span>
                                    <h5 class=""></h5>
                                 </div>
                              </div>
                                     </div>
                           
                                </div>
                              </div>
                            </div>
                            @endforeach
   
              </div>
          </div>
   </div>
</div>

   
  <!-- @foreach($details as $value)
  
   <div class="col-xl-4  col-lg-6 grid-item">
                     <div class="card social-card">
                        <div class="card-block">
                           <div class="media">
                              <div class="media-left media-middle">
                                 <a href="#">
                                    <img class="media-object img-circle" src="{{ asset('public/assets/images/branch/'.(!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg').'') }}" width="100" height="100">
                                 </a>
                              </div>
                              <div class="media-body">
                                 <h5 class="media-heading m-b-15">{{$value->branch_name}}</h5>
                                 <h2 class="media-heading m-b-15 text-info">{{$value->employees}}</h2>
                                 <h6 class="f-w-300">Total Employe</h6>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12">
                             
                              <div class="col-xs-4 bg-success" style="cursor: pointer;" onclick="getabsent('{{$value->branch_id}}','Present')">
                                 <div class="social-media">

                                    <i class="icofont icofont-tick-mark"></i>
                                    <span>Present</span>
                                    <h5 class="">{{$value->present}}</h5>

                                 </div>
                              </div>
                               <div class="col-xs-4 bg-danger" style="cursor: pointer;" onclick="getabsent('{{$value->branch_id}}','Absent')">
                                 <div class="social-media">
                                    <i class="icofont icofont-close"></i>
                                    <span>Absent</span>
                                    <h5 class="">{{$value->absent}}</h5>
                                 </div>
                              </div>
                              <div class="col-xs-4 bg-primary" style="cursor: pointer;" onclick="getsheet('{{$value->branch_id}}','')">
                                
                                 <div class="social-media">
                                    <i class="icofont icofont-eye-alt"></i>
                                    <span>Attendance</span>
                                    <h5 class=""></h5>
                                 </div>
                              </div>
                        
                           </div>
                        </div>
                     </div>
                  </div>
   
   @endforeach -->
   
    
     <div class="row m-10">
               <div class="col-sm-12">
                  <div class="card">
                     <div class="card-header">
                        <h5 class="card-header-text">List of Present Employee</h5>
                     </div>
                     <div class="card-block">
                        <div class="slider-center bg-white">
                          @foreach($getpresent as $value)
                           <div class="card" >
                              <div class="item" >
                                   <img class="card-img-top" src="{{ asset('public/assets/images/employees/images/'.(!empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg').'') }}" width="50" height="180">
                                 <div class="card-block text-center">
                                    <label class="form-control-label">{{$value->emp_name}}</label>
                                    <label class="form-control-label">Clock In Time {{$value->clock_in}}</label>
                                    <label class="form-control-label">Late: {{$value->late}} mint</label>
                                    <label class="form-control-label">{{$value->branch_name}}</label>
                                    <label class="form-control-label">{{$value->designation_name}}</label> 

                                    <a href="#" class="btn btn-primary waves-effect waves-light" onclick="edit('{{$value->branch_id}}','{{$value->empid}}')">Edit Details</a>
                                 </div>
                                 <!-- end of card-block -->
                              </div>
                              <!-- end of item -->
                           </div>
                           @endforeach
                           <!-- end of card -->
                          
                        </div>
                        <!-- end of slider-center -->
                     </div>
                     <!-- end of card-block -->
                  </div>
                  <!-- end of row -->
               </div>
               <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->

            <div class="row m-10">
               <div class="col-sm-12">
                  <div class="card">
                     <div class="card-header">
                        <h5 class="card-header-text">List of Absent Employee</h5>
                     </div>
                     <div class="card-block">
                        <div class="slider-center bg-white">
                          @foreach($getabsent as $value)
                           <div class="card" >
                              <div class="item" >
                                   <img class="card-img-top" src="{{ asset('public/assets/images/employees/images/'.(!empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg').'') }}" width="50" height="180">
                                 <div class="card-block text-center">
                                    <label class="form-control-label">{{$value->emp_name}}</label>
                                    <label class="form-control-label">{{$value->branch_name}}</label>
                                    <label class="form-control-label">{{$value->designation_name}}</label> 

                                    
                                 </div>
                                 <!-- end of card-block -->
                              </div>
                              <!-- end of item -->
                           </div>
                           @endforeach
                           <!-- end of card -->
                          
                        </div>
                        <!-- end of slider-center -->
                     </div>
                     <!-- end of card-block -->
                  </div>
                  <!-- end of row -->
               </div>
               <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
  

   <!--  <div class="row m-10">
               <div class="col-sm-12">
                  <div class="card">
                     <div class="card-block">
                        <div class="slider-center bg-white">
                        <div id="abc">
                          
                        </div>
                     </div>

                     
                  </div>
                  
               </div>
               
            </div>
            </div> -->
            
  
</section>
<!-- modals -->
 <div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Attendance Sheet | <span id="attendancedate"></span></h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
                      <h5>Branch Name: <span id="branchname"></span></h5>
                      <input type="hidden" id="branchid_sheet">

            <table id="tblsheet" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Employee Name</th>
               <th>ClockIn</th>
               <th>ClockOut</th>
               <th>Late </th>
               <th>Early</th>
               <th>OverTime</th>
               <th>ATT.Hrs</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
     </table>
         <div class="button-group ">
       <button type="button" id="btndraft" class="btn btn-md btn-default waves-effect waves-light f-right m-r-20" onclick="getpdf($('#branchid_sheet').val())"> <i class="icofont icofont-file-pdf"> </i>
          Print Pdf
      </button>
         </div> 
                      </div>
                  </div>   
             </div>

          </div>
           </div>
        </div> 

        <div class="modal fade modal-flex" id="clock-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Update Clock In OR Out</h4>
             </div>
             <div class="modal-body">
              <input type="hidden" name="empid" id="empid" value="">
              <input type="hidden" name="attendanceid" id="attendanceid" value="">
              <input type="hidden" name="branchid" id="branchid" value="">
                    <div class="form-group"> 
                        <center><label class="text-info" style="font-size: x-large;" id="empname"></label></center>
                      </div>
               <div class="row">
                   <div class="col-lg-6 col-md-6">
       <div class="form-group {{ $errors->has('clockin') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Clock In:</label>
                  <input class="form-control" type="time" 
                   name="clockin" id="clockin" value="{{ old('clockin') }}" onchange="latecount()"  />
                    @if ($errors->has('clockin'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
                  <div class="col-lg-6 col-md-6">
       <div class="form-group {{ $errors->has('clockout') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Clock Out:</label>
                  <input class="form-control" type="time" 
                   name="clockout" id="clockout" value="{{ old('clockout') }}" onchange="earlycount()"  />
                    @if ($errors->has('clockout'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
                  </div>   
                  <div class="row">
                    <div class="col-lg-4 col-md-4">
       <div class="form-group {{ $errors->has('late') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Late Count:</label>
                  <input class="form-control" type="Number"
                   name="late" readonly="readonly" id="late" value="{{ old('late') }}"  />
                    @if ($errors->has('late'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>
                        <div class="col-lg-4 col-md-4">
       <div class="form-group {{ $errors->has('early') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Early Count:</label>
                  <input class="form-control" type="Number" 
                   name="early" id="early" readonly="readonly" value="{{ old('early') }}"  />
                    @if ($errors->has('early'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
        </div>
              </div>

                <div class="col-lg-4 col-md-4">
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
             </div>
              <div class="modal-footer">
                <button type="button" id="btn_update" class="btn btn-success waves-effect waves-light" onClick="updateattendance()">Update</button>
             </div>
          </div>
           </div>
        </div> 
@endsection
@section('scriptcode_three')
<script type="text/javascript">

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
   

      function getabsent(branchid, mode){

           $('#abc').html('');
                  $.ajax({
                        url : "{{url('/getdatabybranchid')}}",
                        type : "GET",
                        data : {_token : "{{csrf_token()}}",
                        mode:mode,
                        branchid:branchid,
                      },
                        dataType: 'json',
                        success : function(resp){
                          var count=0;
                          $.each(resp,function(index,value){
                            $('#abc').append('<div class="card"><div class="item"><img class="card-img-top" src="./public/assets/images/employees/images/'+value.emp_picture+'" width="50" height="180"><div class="card-block text-center" ><h4 class="card-title">'+value.emp_name+'</h4><h6 class="card-text">'+value.branch_name+'</h6></div></div>')
                            count++;
                          });
                          }
                        });  
                }

    
function upload(){
                 $.ajax({
                        url : "{{url('/attendanceupload')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}",
                      },
                        dataType: 'json',
                        success : function(resp){
                          window.location = "{{url('dailyattendance-view')}}";
                          }
                        }); 
}

  function getsheet(branchid,empid){
                  $.ajax({
                        url : "{{url('/getsheet')}}",
                        type : "GET",
                        data : {_token : "{{csrf_token()}}",
                        branchid:branchid,
                        empid:empid,
                      },
                        dataType: 'json',
                        success : function(result){
                          $('#branchid_sheet').val(branchid);
                          $('#details-modal').modal('show');
                           $("#tblsheet tbody").empty();
                           $('#branchname').html('');
                           $('#attendancedate').html('');
                           $('#branchname').html(result[0].branch_name);
                           $('#attendancedate').html(result[0].date);
                   for(var count =0;count < result.length; count++){
                        $("#tblsheet tbody").append(
                          "<tr>" +
                            "<td class='pro-name' >"+result[count].emp_name+"</td>" +
                            "<td>"+result[count].clock_in+"</td>" +  
                            "<td>"+result[count].clockout+"</td>" +  
                            "<td>"+result[count].lates+" mint</td>" +  
                            "<td>"+result[count].earlys+" mint</td>" +  
                            "<td>"+result[count].ot+" mint</td>" +  
                            "<td>"+result[count].Atttime+"</td>" +  
                            "<td class='action-icon'><i onclick='changefun("+result[count].attendance_id+","+result[count].branch_id+","+result[count].emp_id+","+"\""+ result[count].emp_name + "\")' class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+
                          "</tr>" 
                         );
                    }
                          }
                        });  
                }


                function changefun(attendanceid,branchid,empid,name){
                  $('#clockin').val('');
                  $('#clockout').val('');
                  $('#late').val('');
                  $('#early').val('');
                  $('#ot').val('');
                  $('#attendanceid').val('');
                  $('#empid').val('');
                  $('#empname').html('');
                  $('#branchid').val('');
                  $('#clock-modal').modal('show');
                  $('#details-modal').modal('hide');
                  $('#attendanceid').val(attendanceid);
                  $('#empid').val(empid);
                  $('#empname').html(name);
                  $('#branchid').val(branchid);
                  
                }

function latecount(){
 
        $.ajax({
            url: "{{url('/getgracetime')}}",
            type: 'GET',
            data:{_token:"{{ csrf_token() }}",
            empid:$('#empid').val(),
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
            empid:$('#empid').val(),
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

function updateattendance(){
   $.ajax({
            url: "{{url('/dailyattendance-update')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            attendanceid:$('#attendanceid').val(),
            clockin:$('#clockin').val(),
            clockout:$('#clockout').val(),
            late:$('#late').val(),
            early:$('#early').val(),
            ot:$('#ot').val(),
            mode:1,
          },
            success:function(resp){
                  if(resp == 1){
                     swal({
                      title: "Operation Performed",
                      text: "Attendance Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                        $('#clock-modal').modal('hide');
                      getsheet($('#branchid').val(),'');
                      }
                       });
                  }
           }
          }); 
}

function edit(branchid,empid){
  $('#details-modal').modal('show');
   getsheet(branchid,empid);
}
           
    function getpdf(branchid){    
   window.location = "{{url('getpdfattendancesheet')}}?branchid="+branchid+"&empid="+'';
}

   function genmonth(){

    // is ko bad main dekhte hain
   var date = new Date();
var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
alert(firstDay);
     // $.ajax({
     //        url: "{{url('/monthdata_insert')}}",
     //        type: 'POST',
     //        data:{_token:"{{ csrf_token() }}",
     //        date:$('#attendanceid').val(),
     //      },
     //        success:function(resp){
     //       }
     //      }); 
   }   
           
</script>
@endsection

