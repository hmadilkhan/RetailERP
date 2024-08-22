@extends('layouts.master-layout')

@section('title','Payroll')

@section('breadcrumtitle','Branch Wise Salary')

@section('navpayroll','active')

@section('navbranchwise','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Branch Wise Salary</h5>
          <h6 class=""><a href="{{ url('') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <div class="row">
                <div class="col-lg-3 col-md-3">
            <div class="form-group">
                <label class="form-control-label">Select Branch</label>
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
                    <option value="">Select Branch</option>
                    @if($branch)
                      @foreach($branch as $value)
                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                      @endforeach
                    @endif
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
                       name="salarydateto" id="salarydateto" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
        <div class="col-lg-1  col-sm-12">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-success waves-effect waves-light m-t-25" onclick="brsal()">
                                  <i class="icofont icofont-money-bag">&nbsp;Make Salary</i>
                            </button>
                    </div>  
                       <div class="form-group">
                           <button type="button" id="btnmodal"  class="btn btn-md btn-info waves-effect waves-light m-t-25">Show Modal
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
                         <input type="text"  name="specialamt" id="specialamt" class="form-control" />
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

  function brsal(){
    if ($('#branch').val() == '') {
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
  else{

    }
  }

  $("#btnmodal").on('click',function(){
  $('#desgname').val('');
  $("#special-modal").modal("show");
});

  function addspecialallowance(){

     $.ajax({
        url:'{{ url("/store-budget") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
        branch:$('#branch').val(),
        fromdate:$('#budgetfrom').val(),
        todate:$('#budgetto').val(),
        debit:$('#amount').val(),
      },
    success:function(resp){
        if(resp == 1){
             swal({
                    title: "Success",
                    text: "Food Budget Allocated Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                         getladger();
                   }
               });
          } 
     }
  }); 

}
    
  
</script>
@endsection