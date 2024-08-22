@extends('layouts.master-layout')

@section('title','Loan')

@section('breadcrumtitle','Issue Loan')

@section('navloan','active')

@section('navdeduct','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Issue Loan</h5>
          <h6 class=""><a href="{{ url('/loandetails') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <div class="row">
       
                 <div class="col-lg-6 col-md-6">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                 
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" onchange="getdetails()" >
                    <option value="">Select Employee</option>
                    @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{$value->emp_acc}} | {{$value->emp_name}} | {{$value->department_name}} | {{$value->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>

        
            <div class="col-lg-3 col-md-3">
           <div class="form-group">
            <label class="form-control-label">Date</label>
             <span id="lastdate" class="f-right text-info"></span>
            <input type="text" name="loandate" id="loandate" class="form-control" placeholder="23-12-2019"/>
        </div>
        </div>
                 <div class="col-lg-3 col-md-3">
           <div class="form-group">
            <label class="form-control-label">Enter Amount:</label>
            <input type="number" min="1" name="amount" id="amount" class="form-control" />
              <div class="form-control-feedback">
                <label id="previous" class="form-control-label text-info f-24"></label>
              </div>
        </div>
        </div>

              </div>
              <div class="row">

                  <div class="col-lg-4 col-md-4">
                      <div class="form-group">
                          <label class="form-control-label">Deduction Days | Amount</label>
                          <input type="Number" class="form-control" min="1" name="loandays" id="loandays" placeholder="Enter Days" onchange="calamt()"><br>
                          <input type="Number" class="form-control" name="loanamount" id="loanamount" min="1" placeholder="Enter Amount" onchange="caldays()"><br>
                      </div>
                  </div>
 
             <div class="col-lg-8 col-md-8">
           <div class="form-group">
            <label class="form-control-label">Reason:</label>
            <textarea class="form-control" name="reason" id="reason"></textarea>
        </div>
        </div>
              </div>

    <button type="button" id="btnsubmit" class="btn btn-md btn-info waves-effect waves-light f-right" onclick="issueloan()" > <i class="icofont icofont-plus"> </i> Issue Loan</button>
   </div>
</div>
<!-- modals -->
 <div class="modal fade modal-flex" id="deduct-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Add Deduction Method</h4>
             </div>
             <div class="modal-body">
                <div class="form-group row ">
                <div class="col-lg-12 col-md-12">
                  <label class="form-control-label">Loan Deduction Rule Value</label>
                  <label class="sr-only" for="alighaddon2">Align addon</label></div>
                <div class="col-lg-12 col-md-12">
                   <div class="input-group">
                      <input type="number" id="alighaddon2" class="form-control"  aria-describedby="basic-addon2" min="1" name="loan"  value="{{ old('loan') }}">
                      <span class="input-group-addon" id="basic-addon2">months</span>
               
                   </div>
                </div>
             </div>
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light" onClick="addeduct()">Submit</button>
             </div>
          </div>
           </div>
        </div> 
	</section>
	@endsection

@section('scriptcode_three')

<script type="text/javascript">
	 $(".select2").select2();

  $('#loandate').bootstrapMaterialDatePicker({
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

  // function getemp(){
  //    $.ajax({
  //           url: "{{url('/get-employee')}}",
  //           type: 'POST',
  //         data:{_token:"{{ csrf_token() }}",
  //         dataType:"json",
  //         branchid:$('#branch').val(),
  //       },
  //           success:function(resp){  
  //          $("#employee").empty();
  //              for(var count=0; count < resp.length; count++){
  //               $("#employee").append("<option value=''>Select Employee</option>");
  //               $("#employee").append(
  //                 "<option value='"+resp[count].empid+"'>"+resp[count].emp_name+"</option>");
  //              }           
  //            }

  //         }); 
  // }

  function issueloan(){
    if ($('#branch').val() == '') {
  swal({
      title: "Error Message",
      text: "Branch Can not left blank!",
      type: "error"
      });
    }
    else if ($('#employee').val() == '') {
      swal({
      title: "Error Message",
      text: "Employee Can not left blank!",
      type: "error"
      });
    }
      else if ($('#amount').val() == '') {
      swal({
      title: "Error Message",
      text: "Amount Can not left blank!",
      type: "error"
      });
    }
      else if ($('#loandays').val() == 0) {
      swal({
      title: "Error Message",
      text: "Deduction Days Can not be 0!",
      type: "error"
      });
    }
       else if ($('#loandays').val() == '') {
      swal({
      title: "Error Message",
      text: "Deduction Days Can not left blank!",
      type: "error"
      });
    }
      else if ($('#amount').val() == 0) {
      swal({
      title: "Error Message",
      text: "Amount Can not be 0!",
      type: "error"
      });
    }
      else{

      $.ajax({
          url: "{{url('/issueloan')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          empid:$('#employee').val(),
          amount:$('#amount').val(),
          deduct:$('#loandays').val(),
          loandate:$('#loandate').val(),
          reason:$('#reason').val(),
          deductionamount:$('#loanamount').val(),
        },
            success:function(resp){ 
              console.log(resp);
                if(resp == 1){
                     swal({
                      title: "Success",
                      text: "Loan generated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                      window.location= "{{ url('/loandetails') }}";
                      }
                       });
                  }
                else if(resp == 3)
                {
                    swal({
                        title: "Error Message",
                        text: "Cash Ledger does not have sufficient amount for this transaction!",
                        type: "error"
                    });
                }

             }

          }); 
      }
  }

  $("#btn_deduct").on('click',function(){
  $('#alighaddon2').val('');
  $("#deduct-modal").modal("show");
});

  function addeduct(){
    $.ajax({
          url: "{{url('/insert-loandeduct-modal')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
            dataType:"json",
          loan:$('#alighaddon2').val(),
        },
            success:function(resp){ 
                if(resp != 0){
                     swal({
                      title: "Success",
                      text: "Method submitted Successfully!",
                      type: "success"});

                          $("#deduct-modal").modal("hide");

                      $("#deduct").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#deduct").append("<option value=''>Select Deduction</option>");
                      $("#deduct").append(
                        "<option value='"+resp[count].Loan_Deduct_Type_Id+"'>"+resp[count].Loan_Deduct_type+" Months</option>");
                     }
                  }
                  else{
                      swal({
                      title: "Already exsit",
                      text: "Deduction Method Already Exsit!",
                      type: "error"
                    });
                        $("#deduct-modal").modal("hide");
                  }

             }

          }); 
  }

  function getdetails(){
 $.ajax({
          url: "{{url('/previousdata')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",
            dataType:"json",
          empid:$('#employee').val(),
        },
            success:function(resp){ 
                if(resp != 0){
                  $('#previous').html("Previous balance: "+resp[0].balance);
                  $('#lastdate').html(resp[0].date);
                  }
             }

          }); 
  }
function calamt(){
      let amount = $('#amount').val();
      let days = $('#loandays').val();
      installment = parseInt(amount) / parseInt(days);
      $('#loanamount').val(installment);

}
function  caldays(){
    let amount = $('#amount').val();
    let dedcutamt = $('#loanamount').val();
    days = parseInt(amount) / parseInt(dedcutamt);
    $('#loandays').val(days);
}

</script>
@endsection