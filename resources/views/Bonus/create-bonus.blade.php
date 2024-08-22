@extends('layouts.master-layout')

@section('title','Employee Bonus')

@section('breadcrumtitle','Bonus Details')

@section('navbonus','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Issue Bonus</h5>
          <h6 class=""><a href="{{ url('/showbonus') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>     

       <div class="card-block">
<div class="row">
             <div class="col-lg-5 col-md-5">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" onchange="getbasicsal()" >
                    <option value="">Select Employee</option>
                    @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{$value->emp_acc}} | {{$value->emp_name}} | {{$value->department_name}} | {{$value->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>

        <div class="col-lg-2 col-md-2">
         <div class="form-group">
      <label class="form-control-label">Basic Salary</label>
     <h1 class="text-success f-30" id="basicsalary" style="margin-top: -20px;" >00</h1>
        </div>
        </div>

        <div class="col-lg-3 col-md-3">
         <div class="form-group">
      <label class="form-control-label">Bonus Amount | Percentage</label>
     <input type="Number" class="form-control" name="percen" id="percen" placeholder="Enter Percentage" onchange="calper()"><br>
      <input type="Number" class="form-control" name="amt" id="amt" placeholder="Enter Amount" onchange="calamt()"><br>
        </div>
</div>
  

               <div class="col-lg-2 col-md-2">
         <div class="form-group">
      <label class="form-control-label">New Basic Salary</label>
     <h1 class="text-info f-30" id="finalsalary" style="margin-top: -20px;" >00</h1>
        </div>
        </div>
            
        
           </div>
           <div class="row">
      <div class="col-lg-12 col-md-12">
         <div class="form-group">
      <label class="form-control-label">Enter Reason</label>
    <textarea id="reason" name="reason" class="form-control"></textarea>
        </div>
        </div>
           </div>
<div class="button-group ">
                      <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="submitincrement()"> <i class="icofont icofont-tick-mark"> </i>
                        Give Bonus
                    </button>
                    </div>    
        
  
  </div>
</div>

</section>

 

@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();



function getbasicsal(){
  
   $.ajax({
          url: "{{url('/getbasicsalary')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          empid:$('#employee').val(),
          },
          success:function(resp){
            $('#basicsalary').html(resp[0].basic_pay);
          }
      });

}



function calper(){
  let per = $('#percen').val();
  let basic = $('#basicsalary').html();
  let amt = (parseInt(basic) * parseInt(per))/100;
  let newsal = (parseInt(amt) + parseInt(basic));
  $('#amt').val(amt);
  $('#finalsalary').html(newsal);
  

}


function calamt(){
   let amt = $('#amt').val();
  let basic = $('#basicsalary').html();

  let per = (parseInt(amt) / parseInt(basic))*100;
  let newsal = (parseInt(amt) + parseInt(basic));
  $('#percen').val(per);
  $('#finalsalary').html(newsal);
  
}


function submitincrement(){
    if ($('#employee').val() == "") {
    swal({
            title: "Error Message",
            text: "Please Select Employee First!",
            type: "error"
            });
  }
  else if ($('#finalsalary').html() == "00") {
     swal({
            title: "Error Message",
            text: "Operation Failed!",
            type: "error"
            });
  }
 
  else{
      $.ajax({
            url: "{{url('/store_bonus')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            employee:$('#employee').val(),
            bonusamt:$('#amt').val(),
            bonusper:$('#percen').val(),
            reason:$('#reason').val(),
            },
            success:function(resp){
              console.log(resp);
                if(resp == 1){
                     swal({
                            title: "Success",
                            text: "Bonus Generated Successfully!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                window.location= "{{ url('/showbonus') }}";
                           }
                       });
                 }
                 else if (resp == 0) {
                   swal({
                        title: "Already Exsist!",
                        text: "Bonus Already exsist!!",
                        type: "error"
                       });
                 }
            }

        });
    }
  }

 </script>


@endsection
