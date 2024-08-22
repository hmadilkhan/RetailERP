@extends('layouts.master-layout')

@section('title','Advance Salary')

@section('breadcrumtitle','Advance Salary Details')

@section('navpayroll','active')

@section('navadvance','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Issue Advance</h5>
          <h6 class=""><a href="{{ url('/view-advancelist') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <div class="row">
		<div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">Select Branch</label>
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" onchange="getemp()" >
                    <option value="">Select Branch</option>
                    @if($branches)
                      @foreach($branches as $branch)
                        <option value="{{ $branch->branch_id }}"> {{$branch->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>
		<div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">Select Category</label>
                <select name="category" id="category" data-placeholder="Select Category" class="form-control select2" onchange="getemp()" >
                    <option value="">Select Category</option>
                    @if($categories)
                      @foreach($categories as $category)
                        <option value="{{ $category->id }}"> {{$category->category}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>

                 <div class="col-lg-3 col-md-3">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                <select multiple="" name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" onchange="getdetails()" >
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
                    <label class="form-control-label">Basic Salary</label>
                    <h1 class="text-success f-30" id="basicsalary" style="margin-top: -20px;" >00</h1>
                </div>
            </div>
			
              </div>
			  <div class="row">
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
            <input type="number" min="1" name="amount" id="amount" class="form-control" onchange="compareamount()" />
              <div class="form-control-feedback">
                <label id="previous" class="form-control-label text-info f-24"></label>
              </div>
        </div>
        </div>

              </div>
              <div class="row">
       
             <div class="col-lg-12 col-md-12">
           <div class="form-group">
            <label class="form-control-label">Reason:</label>
            <textarea class="form-control" name="reason" id="reason"></textarea>
        </div>
        </div>
              </div>

    <button type="button" id="btnsubmit" class="btn btn-md btn-info waves-effect waves-light f-right" onclick="issueadvance()" > <i class="icofont icofont-plus"> </i> Issue Advance</button>
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
$("#loandate").val('{{date("Y-m-d")}}')
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

  function getemp(){
	 let branch = $('#branch').val();
	 let category = $('#category').val();
	 if(branch != "" && category != ""){
		 $.ajax({
			url: "{{url('/get-employeebybranch')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				dataType:"json",
				branchid:$('#branch').val(),
				category:$('#category').val(),
			},
			success:function(resp){  
			console.log(resp)
				$("#employee").empty();
				for(var count=0; count < resp.length; count++){
				$("#employee").append("<option value=''>Select Employee</option>");
				$("#employee").append(
				"<option value='"+resp[count].empid+"'>"+resp[count].emp_name+"</option>");
				}           
			}

		}); 
	 }
  }

  function issueadvance(){
     if ($('#employee').val() == '') {
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
      else if ($('#amount').val() == 0) {
      swal({
      title: "Error Message",
      text: "Amount Can not be 0!",
      type: "error"
      });
    }
     else if ($('#loandate').val() == "") {
         swal({
             title: "Error Message",
             text: "Date Can not be Null!",
             type: "error"
         });
     }

      else{

      $.ajax({
            url: "{{url('/insert-advance')}}",
            type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          amount:$('#amount').val(),
          date:$('#loandate').val(),
          reason:$('#reason').val(),
          empid:$('#employee').val(),
          branchid:$('#branch').val(),
        },
            success:function(resp){ 
            console.log(resp) ;
                if(resp == 1){
                     swal({
                      title: "Success",
                      text: "Advance Salary generated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                      window.location= "{{ url('/view-advancelist') }}";
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
                else{
                    swal({
                        title: "Error",
                        text: "You can not Enter Advance Amount greater than Basic Salary!",
                        type: "error"});
                }

             }

          }); 
      }
  }


 

  function getdetails(){
 $.ajax({
          url: "{{url('/previousdetails')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
            dataType:"json",
          empid:$('#employee').val(),
        },
            success:function(resp){ 
                if(resp != 0){
                  $('#previous').html("Previous Advance: "+resp[0].advance);
                  $('#lastdate').html(resp[0].date);
                  }
             }

          });

      $.ajax({
          url: "{{url('/getbasicpay')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
              dataType:"json",
              empid:$('#employee').val(),
          },
          success:function(resp){
              if(resp != 0){
                  $('#basicsalary').html(resp[0].basic_pay);
              }
          }

      });

  }

     function compareamount() {
         if (parseFloat($('#basicsalary').html()) < parseFloat($('#amount').val()))
         {
             swal({
                 title: "Error",
                 text: "You can not Enter Advance Amount greater than Basic Salary!",
                 type: "error"});
         }

     }

</script>
@endsection