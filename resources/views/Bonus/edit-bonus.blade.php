@extends('layouts.master-layout')

@section('title','Employee Bonus')

@section('breadcrumtitle','Bonus Details')

@section('navbonus','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Edit Bonus Details</h5>
          <h6 class=""><a href="{{ url('/showbonus') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>     

       <div class="card-block">
<div class="row">
             <div class="col-lg-5 col-md-5">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
<input type="hidden" name="bonusid" id="bonusid" value="{{$details[0]->bonus_id}}">
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
     <input type="Number" class="form-control" name="percen" id="percen" placeholder="Enter Percentage" onchange="calper()" value="{{$details[0]->bonus_percentage}}"><br>
      <input type="Number" class="form-control" name="amt" id="amt" placeholder="Enter Amount" onchange="calamt()" value="{{$details[0]->bonus_amt}}"><br>
        </div>
</div>
  

               <div class="col-lg-2 col-md-2">
         <div class="form-group">
      <label class="form-control-label">New Basic Salary</label>
     <h1 class="text-info f-30" id="finalsalary" style="margin-top: -20px;" >{{$details[0]->net_amt}}</h1>
        </div>
        </div>
            
        
           </div>
           <div class="row">
      <div class="col-lg-12 col-md-12">
         <div class="form-group">
      <label class="form-control-label">Enter Reason</label>
    <textarea id="reason" name="reason" class="form-control">{{$details[0]->reason}}</textarea>
        </div>
        </div>
           </div>
<div class="button-group ">
                      <button type="button" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right" onclick="update()"> <i class="icofont icofont-tick-mark"> </i>
                        Edit Bonus Details
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


function update(){

  if ($('#amt').val() == 0) {
     swal({
            title: "Error Message",
            text: "Operation Failed!",
            type: "error"
            });
  }
  else{
      $.ajax({
            url: "{{url('/update_bonus')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            employee:$('#employee').val(),
            bonusamt:$('#amt').val(),
            bonusper:$('#percen').val(),
            reason:$('#reason').val(),
            bonusid:$('#bonusid').val(),
            },
            success:function(resp){
                if(resp){
                     swal({
                       title: "Success",
                       text: "Bonus Details Updated Successfully!",
                       type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                window.location= "{{ url('/showbonus') }}";
                           }
                       });
                 }
            }

        });
    }
  }

$('#employee').val('{{$details[0]->emp_id}}').change();
  

 </script>


@endsection
