@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','Leave Form')

@section('navleavesdetails','active')

@section('navleaveform','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Leaves Application</h5>
      <h6 class=""><a href="{{ url('/showleaves') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>     

       <div class="card-block">
 <div class="row">
             <div class="col-lg-6 col-md-6">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                 
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" onchange="getleavehead()" >
                    <option value="">Select Employee</option>
                    @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{$value->emp_acc}} | {{$value->emp_name}} | {{$value->department_name}} | {{$value->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>

                 <div class="col-lg-6 col-md-6">
           <div class="form-group">
                <label class="form-control-label">Select Leave Head</label>
                 
                <select name="leavehead" id="leavehead" data-placeholder="Select Leave Head" class="form-control select2" onchange="getbalance()" >
                    <option value="">Select Leave Head</option>
                </select>
        </div>
        </div>
            
            
        
           </div>
           <div class="row">
             <div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">Leave From:</label>
            <input type="text" name="datefrom" id="datefrom" class="form-control" placeholder="23-12-2019" onchange="same()" />
        </div>
           </div>
           <div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">Leave To:</label>
            <input type="text" name="dateto" id="dateto" class="form-control" placeholder="23-12-2019" onchange="caldays()" />
        </div>
           </div>

                  <div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">No. of Days:</label>
            <input type="Number" name="days" id="days" class="form-control" disabled="disabled" />
        </div>
           </div>

                     <div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">Balance:</label>
            <h1 class="text-success f-30" id="balanceleave" style="margin-top: -20px;" >00</h1>
        </div>
           </div>
      
  </div>
  <div class="row">
      <div class="col-lg-12 col-md-12">
           <div class="form-group">
                <label class="form-control-label">Reason:</label>
                <textarea type="Text" name="reason" id="reason" class="form-control"></textarea>
            
        </div>
           </div>
      
  </div>
       <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="submit()"> <i class="icofont icofont-plus"> </i>
                        Submit Leave
                    </button>
                </form>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

   $('#datefrom,#dateto').bootstrapMaterialDatePicker({
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

  function getleavehead(){
  $.ajax({
          url: "{{url('/getleavehead')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          empid:$('#employee').val(),
        },
            success:function(resp){ 
            $("#leavehead").empty();          
                     for(var count=0; count < resp.length; count++){
                      $("#leavehead").append("<option value=''>Select Leave Head</option>");
                      $("#leavehead").append(
                        "<option value='"+resp[count].leave_id+"'>"+resp[count].leave_head+"</option>");
                  }
             }
          }); 
  } 


    function getbalance(){
  $.ajax({
          url: "{{url('/getleavebalance')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          leaveid:$('#leavehead').val(),
          empid:$('#employee').val(),
        },
            success:function(resp){ 
                $('#balanceleave').html(resp[0].balance);
             }
          }); 
  } 

  function same(){
    let samedate = $('#datefrom').val();
    $('#dateto').val(samedate);
    caldays();
  }
  function caldays(){
        let fromdate = new Date($('#datefrom').val());
    let todate = new Date($('#dateto').val());
    
    let days = (todate.getTime() - fromdate.getTime()) / (1000 * 60 * 60 * 24);
    days++;
    if (days <= 0) {
          swal({
            title: "Error Message",
            text: "No of Days can not be 0 or negative!",
            type: "error"
              });
    }
    else{
    $('#days').val(days);
    }
  }

function submit(){
  let days =parseInt($('#days').val());
  let balance = parseInt($('#balanceleave').html());
  if ($('#employee').val() == "") {
       swal({
            title: "Error Message",
            text: "Please Select Employee",
            type: "error"
           });
  }
  else if ($('#leavehead').val() == "") {
       swal({
            title: "Error Message",
            text: "Please Select Leave Head",
            type: "error"
           });
  }
  else if ($('#days').val() == "") {
       swal({
            title: "Error Message",
            text: "Days Can not left blank!",
            type: "error"
           });
  }
   else if (days > balance) {
       swal({
            title: "Error Message",
            text: "Mismatch Error!",
            type: "error"
           });
  }
  else{
      $.ajax({
          url: "{{url('/submitleave')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          leaveid:$('#leavehead').val(),
          empid:$('#employee').val(),
          fromdate:$('#datefrom').val(),
          todate:$('#dateto').val(),
          days:$('#days').val(),
          reason:$('#reason').val(),
        },
             success:function(resp){
        if(resp == 1){
             swal({
                    title: "Success",
                    text: "Leave Application Submitted!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                         window.location= "{{ url('/showleaves') }}";
                   }
               });
          }
          else if (resp == 2) {
      swal({
                    title: "Error",
                    text: "Already Exsists!",
                    type: "error"
           });
          }
          else{
              swal({
                    title: "Error",
                    text: "Enter Days Not Valid!",
                    type: "error"
               });
          }
     }

          }); 
  }
}
</script>
@endsection