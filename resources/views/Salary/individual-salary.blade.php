@extends('layouts.master-layout')

@section('title','Payroll')

@section('breadcrumtitle','Individual Wise Salary')

@section('navpayroll','active')

@section('navempwise','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Individual Wise Salary</h5>
          <h6 class=""><a href="{{ url('/salary-details') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label class="form-control-label">Select Branch</label>
                    <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" onchange="getemployee()">
                        <option value="">Select Branch</option>
                        @if($branches)
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{$branch->branch_name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
			<div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label class="form-control-label">Select Salary Category</label>
                    <select name="salcat" id="salcat" data-placeholder="Select Salary Category" class="form-control select2" onchange="getemployee()" >
                        <option value="">Select Salary Category</option>
                        @if($salcat)
                            @foreach($salcat as $value)
                                <option value="{{ $value->id }}">{{$value->category}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
                <div class="col-lg-4 col-md-4">
             <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                 
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" >
                    <option value="">Select Employee</option>

                </select>
        </div>
                </div>
                    <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label class="form-control-label">From Date</label>
                      <input class="form-control" type="text"
                       name="salarydatefrom" id="salarydatefrom" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
                 <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label class="form-control-label">To Date</label>
                      <input class="form-control" type="text"
                       name="salarydateto" id="salarydateto" placeholder="DD-MM-YYYY" onchange="matchdate()"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
        <div class="col-lg-1  col-sm-1">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-info waves-effect waves-light m-t-25" onclick="getdata()">
                                  <i class="icofont icofont-search">&nbsp;Filter Data</i>
                            </button>
                    </div>       
                </div> 
           </div>
           <hr>
           <div class="row">
                       <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label>Employee ACC Number</label><br>
                      <label class="form-control-label" id="empacc"></label><br>
                        <label>Branch</label><br>
                      <label class="form-control-label" id="branch"></label>
                  </div>
             </div>
                    <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label>Employee Name</label><br>
                      <label class="form-control-label" id="empname"></label><br>
                        <label>Department</label><br>
                      <label class="form-control-label" id="depart"></label>
                  </div>
             </div>
                      <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label>Contact Number</label><br>
                      <label class="form-control-label" id="empcontact"></label><br>
                       <label>Designation</label><br>
                      <label class="form-control-label" id="desg"></label>
                  </div>
             </div>
                       <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                     <a href="#">
                <img id="empimgs" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                </a>
                  </div>
             </div>

           </div>
           <hr>
           <div class="row">
                        <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label>Present Days</label><br>
                      <label class="form-control-label" id="present"></label><br>
                  </div>
             </div>
                 <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label>Leaves Count</label><br>
                      <label class="form-control-label" id="leaves"></label><br>
                  </div>
             </div>
                    <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label>Absent Days</label><br>
                      <label class="form-control-label" id="absent"></label><br>
                  </div>
             </div>
                     <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label>Late Count</label><br>
                      <label class="form-control-label" id="late"></label><br>
                  </div>
             </div>
                    <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label>Early Count</label><br>
                      <label class="form-control-label" id="early"></label><br>
                  </div>
             </div>
                   <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label>Over Time Duration</label><br>
                      <label class="form-control-label" id="otduration"></label><br>
                  </div>
             </div>
                
             
           </div>
           <div class="row">
    <div id="abcd">
      
    </div>
      <div id="efgh">
      
    </div>         
           </div>

           <div id="hidden">
        <input type="hidden" id="GETallowanceSUM" value="">
        <input type="hidden" id="GETbonusamt" value="">
        <input type="hidden" id="GETotamt" value="">
        <input type="hidden" id="GETbonusID" value="">
        <input type="hidden" id="GETotID" value="">

        <input type="hidden" id="GETadvanceamt" value="">        
        <input type="hidden" id="GETadvanceID" value="">        
        <input type="hidden" id="GETloanamt" value="">
        <input type="hidden" id="GETtaxamt" value="">
        <input type="hidden" id="GETabsentamt" value="">
               <input type="hidden" id="grosstablecalculation" value="">
               <input type="hidden" id="days" value="0">
           </div>

           <div class="row col-md-6 col-lg-6">
               <div class="form-group">
                   <label class="form-control-label">Loan Deduction Amount:</label>
                   <input type="number" class="form-control" placeholder="Enter Amount" id="loandedecutionamt" name="loandedecutionamt" onchange="loan()"  disabled="true" />
               </div>
           </div>
    
           <div class="row col-md-6 col-lg-6 f-right">
              <table id="" class="table table-responsive invoice-table invoice-total">
          <tbody>
         <input type="hidden" name="totalrp" id="totalrp">
           <!--<tr>
                <th><h5>Basic Salary:</h5></th>
                <td><h5 id="basicpay">0.00</h5></td>
             </tr> -->
             <tr>
                <th><h5>Gross Salary:</h5></th>
                <td><h5 id="gross">0.00</h5></td>
             </tr>
              <tr>
                <th><h5>Special Amount:</h5></th>
                <td><h5 id="spamt">0.00</h5></td>
             </tr>
                <tr>
                <th><h5>Deduction:</h5></th>
                <td><h5 id="deductionamt">0.00</h5></td>
             </tr>
                 <tr class="text-info">
                <th><h3>Net Salary:</h3></th>
                <td><h3 id="netsal">0.00</h3></td>
             </tr>

          </tbody>
       </table>
           </div>

           <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="salaryinsert()" > <i class="icofont icofont-money-bag"> </i>
                        Make Salary
                    </button>
                     <button type="button" id="btnmodal" class="btn btn-md btn-default waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-save"> </i>
                        Give Special Allowance
                    </button>
                    </div>       
                </div>  
            </div>  


	
   </div>
</div>
	</section>
  <!-- modals -->
 <div class="modal fade modal-flex" id="special-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Special Allowance</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Enter Amount:</label>
                         <input type="Number" min="1"  name="specialamt" id="specialamt" class="form-control" />
                        </div>
                      </div>
                  </div> 
                     <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Reason:</label>
                         <input type="text"  name="specialreason" id="specialreason" class="form-control" />
                        </div>
                      </div>
                  </div>    
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_special" class="btn btn-success waves-effect waves-light" onClick="addspecialallowance()">Give Special Allowance</button>
             </div>
          </div>
           </div>
        </div> 
	@endsection

@section('scriptcode_three')

<script type="text/javascript">
	$(".select2").select2();



  $('#salarydatefrom, #salarydateto').bootstrapMaterialDatePicker({
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

  function getdata(){

    $('#gross').html(0.00);
    $('#netsal').html(0.00);
    $('#spamt').html(0.00);
    $('#deductionamt').html(0.00);
    $('#basicpay').html(0.00);

    let fromdate = new Date($('#salarydatefrom').val());
      let todate = new Date($('#salarydateto').val());
      let days = (todate.getTime() - fromdate.getTime()) / (1000 * 60 * 60 * 24);
	  console.log("days",days);
      $('#days').val(days);

    if ($('#employee').val() == '') {
	swal({
          title: "Error Message",
          text: "Please Select Branch First!",
          type: "error"
     });
}
else if ($('#salarydatefrom').val() == '') {
  swal({
          title: "Error Message",
          text: "Please Enter Salary From Date!",
          type: "error"
     });
}
  else if ($('#salarydateto').val() == '') {
      swal({
          title: "Error Message",
          text: "Please Enter Salary To Date!",
          type: "error"
     });
  }
  // else if (days != 0 && days < 30) {
  //    swal({
  //         title: "Error Message",
  //         text: "Please Enter Valid Date Range!",
  //         type: "error"
  //    });
  // }
  else{

     $.ajax({
        url:'{{ url("/getempdetails") }}',
        type:"GET",
        data:{_token : "{{csrf_token()}}",
       empid:$('#employee').val(),
       fromdate:$('#salarydatefrom').val(),
       todate:$('#salarydateto').val(),
      },
    success:function(resp){
      $('#empacc').html(resp[0].emp_acc);
      $('#empname').html(resp[0].emp_name +" S/o "+ resp[0].emp_fname);
      $('#empcontact').html(resp[0].emp_contact);
      $('#branch').html(resp[0].branch_name);
      $('#depart').html(resp[0].department_name);
      $('#desg').html(resp[0].designation_name);
      $('#present').html(resp[0].present);
      $('#leaves').html(resp[0].leaves);
      $('#absent').html(resp[0].absent);
      $('#late').html(resp[0].late);
      $('#early').html(resp[0].early);
      $('#otduration').html(resp[0].ot);
      $('#basicpay').html(resp[0].basic_salary);
      $('#empimgs').attr("src","./public/assets/images/employees/images/"+resp[0].emp_picture);

     }
  });  

      $.ajax({
        url:'{{ url("/getdeduction") }}',
        type:"GET",
        data:{_token : "{{csrf_token()}}",
       empid:$('#employee').val(),
       fromdate:$('#salarydatefrom').val(),
       todate:$('#salarydateto').val(),
      },
    success:function(result){
      $('#efgh').html('');
      $('#efgh').html(result);
		
       let deduct = parseFloat($('#deduct').val()).toFixed(2);
	   console.log(deduct)
      $('#deductionamt').html(deduct);

      //SET into hidden fields
      $('#GETadvanceamt').val($('#SETadvanceamt').val());
      $('#GETadvanceID').val($('#SETadvanceID').val());
      $('#GETloanamt').val($('#SETloanamt').val());
      $('#loandedecutionamt').val($('#SETLoandedeuctiondays').val());
      $('#GETabsentamt').val($('#SETabsentamt').val());
      $('#GETtaxamt').val($('#SETtaxamt').val());


      if($('#SETloanamt').val() == ""){
          $('#loandedecutionamt').attr('disabled', true);
      }
      else{
          $('#loandedecutionamt').attr('disabled', false);
          // loan();
		  
      }
     }
  }); 

      $.ajax({
        url:'{{ url("/getgross") }}',
        type:"GET",
        data:{_token : "{{csrf_token()}}",
       empid:$('#employee').val(),
       fromdate:$('#salarydatefrom').val(),
       todate:$('#salarydateto').val(),
      },
    success:function(result){
      $('#abcd').html('');
      $('#abcd').html(result);
      $('#gross').html('');
      let abc = parseFloat($('#grossamt').val()).toFixed(2);
       // let gross = (parseFloat($('#basicpay').html()) + parseFloat(abc)).toFixed(2);
        $('#grosstablecalculation').val(abc);


      // $('#gross').html(abc);

      //set in hidden fields
      $('#GETbonusamt').val($('#setbonusamt').val());
      $('#GETotamt').val($('#setotamt').val());
      $('#GETallowanceSUM').val($('#SETallowanceSUM').val());
      $('#GETbonusID').val($('#bonusid').val());
      $('#GETotID').val($('#overtimeID').val());
    }
    }); 

     $.ajax({
        url:'{{ url("/getsepcialallowance") }}',
        type:"GET",
        data:{_token : "{{csrf_token()}}",
       empid:$('#employee').val(),
       fromdate:$('#salarydatefrom').val(),
       todate:$('#salarydateto').val(),
      },
    success:function(result){
      let spamt = parseFloat(result[0].amount).toFixed(2);
      $('#spamt').html(spamt);
    }
    });

        $.ajax({
            url:'{{ url("/getweekends") }}',
            type:"GET",
            data:{_token : "{{csrf_token()}}",
                empid:$('#employee').val(),
                fromdate:$('#salarydatefrom').val(),
                todate:$('#salarydateto').val(),
            },
            success:function(resp){
                let days = (parseInt($('#days').val()) - resp);
                $('#days').val(days);
            }
        });



    }
    sleep(3000).then(() => { calculateNetAmt() });
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}


function calculateNetAmt(){
    $('#gross').html(0.00);
    $('#netsal').html(0.00);
    let days = parseFloat($('#days').val());
    days++;


if (days != 0){
    if($('#salcat').val() != 1){
        let gross = (( parseFloat($('#grosstablecalculation').val())).toFixed(2)); //parseFloat($('#basicpay').html()) +
        gross = parseFloat(gross).toFixed(2);
        $('#gross').html(gross.toLocaleString());
        let net = ((parseFloat($('#gross').html()) - parseFloat($('#deductionamt').html())) + parseFloat($('#spamt').html()));
        net = parseFloat(net).toFixed(2);
        $('#netsal').html(net.toLocaleString());
    }
    else{
        let gross = (((  parseFloat($('#grosstablecalculation').val())).toFixed(2)) / 30) * (30 - parseFloat($("#absent").html())); //parseFloat($('#basicpay').html()) +
        gross = parseFloat(gross).toFixed(2);
        $('#gross').html(gross.toLocaleString());
        let net = ((parseFloat($('#gross').html()) - parseFloat($('#deductionamt').html())) + parseFloat($('#spamt').html()));
        net = parseFloat(net).toFixed(2);
        $('#netsal').html(net.toLocaleString());
    }

}

}

  $("#btnmodal").on('click',function(){
  $('#desgname').val('');
  $("#special-modal").modal("show");
});

  function addspecialallowance(){
    if ($('#employee').val() == "") {
  swal({
          title: "Error Message",
          text: "Please Select Employee First!",
          type: "error"
     });
  }
   else if ($('#specialamt').val() <= 0) {
  swal({
          title: "Error Message",
          text: "Special Amount can not be Zero or negative!",
          type: "error"
     });
  }
  else if ($('#specialamt').val() == '') {
  swal({
          title: "Error Message",
          text: "Special Amount can not be empty!",
          type: "error"
     });
  }
  else{
     $.ajax({
        url:'{{ url("/insert-specialallowance") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
        empid:$('#employee').val(),
        amount:$('#specialamt').val(),
        date:$('#salarydateto').val(),
        reason:$('#specialreason').val(),
      },
    success:function(resp){
        if(resp == 1){
             swal({
                    title: "Success",
                    text: "Special Allowance Submitted Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                    $("#special-modal").modal("hide");
           let spamt = parseFloat($('#specialamt').val()).toFixed(2);
                  parseFloat($('#spamt').html(spamt)).toFixed(2);
                     sleep(3000).then(() => { calculateNetAmt() });
                   }
               });
          } 
          else{
                   swal({
                    title: "Success",
                    text: "Special Allowance Updated Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                    $("#special-modal").modal("hide");
                    let spamt = parseFloat(resp).toFixed(2);
                  parseFloat($('#spamt').html(spamt)).toFixed(2);
                     sleep(3000).then(() => { calculateNetAmt() });
                   }
               });
          }
     }
  }); 
   }
}

function salaryinsert(){
  if ($('#employee').val() == '') {
  swal({
          title: "Error Message",
          text: "Please Select Branch First!",
          type: "error"
     });
}
else if ($('#salarydatefrom').val() == '') {
  swal({
          title: "Error Message",
          text: "Please Enter Salary From Date!",
          type: "error"
     });
}
  else if ($('#salarydateto').val() == '') {
      swal({
          title: "Error Message",
          text: "Please Enter Salary To Date!",
          type: "error"
     });
  }
  else if ($('#present').html() == 0) {
      swal({
          title: "Error Message",
          text: "You can not make Salary of 0 Present!",
          type: "error"
      });
  }
  else{
    payslip();
    emp_ledger();
   
}
}

function emp_ledger(){
    $.ajax({
        url:'{{ url("/insert-emp-ledger") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
            empid:$('#employee').val(),
            amount:$('#netsal').html(),
            mode:0,
        },
        success:function(resp){
            console.log(resp);
        }
    });
}

      function payslip(){
        $.ajax({
        url:'{{ url("/insert-payslip") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
        empid:$('#employee').val(),

        present:$('#present').html(),
        leaves:$('#leaves').html(),
        absent:$('#absent').html(),
        othours:$('#otduration').html(),
        basicepay:$('#setbasicsalary').val(),

        payslipdate:$('#salarydateto').val(),

        otamount:$('#GETotamt').val(),
        bonusamt:$('#GETbonusamt').val(),
        allowanceamt:$('#GETallowanceSUM').val(),
        pffundamt:$('#setpffund').val(),
        otherallowanceamt:$('#setallowance').val(),
        grossamt:$('#grossamt').val(),

        advanceamt:$('#GETadvanceamt').val(),
        loanamt:$('#loandedecutionamt').val(),
        absentamt:$('#GETabsentamt').val(),
        taxamt:$('#GETtaxamt').val(),
        eobiamt:$('#SETEobi').val(),
        security_deposit:$('#SetSecurityDeposit').val(),
        
        specialamt:$('#spamt').html(),
        gross:$('#gross').html(),
        deduct:$('#deductionamt').html(),
        net:$('#netsal').html(),

        bonusid:$('#GETbonusID').val(),
        otid:$('#GETotID').val(),
        advanceid:$('#GETadvanceID').val(),

        fromdate:$('#salarydatefrom').val(),
        todate:$('#salarydateto').val(),
        salcategory: $('#salcat').val(),
      },
    success:function(resp){
            console.log(resp);
      if (resp > 0) {
                 swal({
                    title: "Success",
                    text: "Payslip Generated Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                    window.location = "{{url('/empwise-view')}}";
                   }
               });
                 }
                 else{
                  swal({
                    title: "warning",
                    text: "Salary Sheet of "+resp+" Already Exsist!",
                    type: "warning"
               });
                 }
     }
  }); 
}

function loan(){

      if (parseFloat(($('#loandedecutionamt').val())) > parseFloat(($('#SETloanamt').val())))
      {
          swal({
              title: "Error Message",
              text: "Please Enter Valid Amount or 0!",
              type: "error"
          });
      }
      else if($('#loandedecutionamt').val() == ""){
          swal({
              title: "Error Message",
              text: "Please Enter Valid Amount or 0!",
              type: "error"
          });
      }
      else{
      let deduction = (parseFloat($('#GETadvanceamt').val()) + parseFloat($('#GETabsentamt').val()) + parseFloat($('#GETtaxamt').val()));


      let loanamt = parseFloat($('#loandedecutionamt').val());
      let newdeduct = parseFloat(loanamt + deduction);
      let deduct = parseFloat(newdeduct).toFixed(2);
      // $('#deductionamt').html(deduct);
	  // sleep(3000).then(() => { calculateNetAmt() });
	  
      }
}

function  getemployee(){
	if($('#salcat').val() != "" && $('#branch').val() != ""){
      $.ajax({
          url:'{{ url("/getemp_sal_category") }}',
          type:"GET",
          data:{_token : "{{csrf_token()}}",
              catid:$('#salcat').val(),
              branch:$('#branch').val(),
          },
          success:function(resp){

              $("#employee").empty();
              for(var count=0; count < resp.length; count++){
                  $("#employee").append("<option value=''>Select Employee</option>");
                  $("#employee").append(
                      "<option value='"+resp[count].empid+"'>"+resp[count].emp_acc+" | "+resp[count].emp_name+" | "+resp[count].department_name+" | "+resp[count].branch_name+" </option>");
              }
          }
      });
	}
}

function matchdate() {
      if($('#salcat').val() == 1){
          var today = new Date();
          var dd = today.getDate();

          var mm = today.getMonth()+1;
          var yyyy = today.getFullYear();
          if(dd<10)
          {
              dd='0'+dd;
          }

          if(mm<10)
          {
              mm='0'+mm;
          }
          today = yyyy+'-'+mm+'-'+dd;

          if($('#salarydateto').val() > today)
          {
              swal({
                  title: "Error Message",
                  text: "You can not make After Date Salary!",
                  type: "error"
              });
              $('#salarydateto').val('');
          }
      }

}

</script>
@endsection
