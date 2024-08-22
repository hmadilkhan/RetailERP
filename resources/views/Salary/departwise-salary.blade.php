@extends('layouts.master-layout')

@section('title','Payroll')

@section('breadcrumtitle','Department Wise Salary')

@section('navpayroll','active')

@section('navdepartwise','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Department Wise Salary</h5>
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
                <label class="form-control-label">Select Department</label>
                <select name="depart" id="depart" data-placeholder="Select Department" class="form-control select2" >
                    <option value="">Select Department</option>
                    @if($depart)
                      @foreach($depart as $value)
                        <option value="{{ $value->department_id }}">{{ $value->department_name }}</option>
                      @endforeach
                    @endif
                </select>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label class="form-control-label">Date</label>
                      <input class="form-control" type="text"
                       name="salarydate" id="salarydate" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
        <div class="col-lg-1  col-sm-1">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-success waves-effect waves-light m-t-25" onclick="departsal()">
                                  <i class="icofont icofont-money-bag">&nbsp;Make Salary</i>
                            </button>
                    </div>       
                </div> 
           </div>
	
   </div>
</div>
	</section>
	@endsection

@section('scriptcode_three')

<script type="text/javascript">
	$(".select2").select2();

  $('#salarydate').bootstrapMaterialDatePicker({
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

  function departsal(){
    if ($('#branch').val() == '') {
  swal({
          title: "Error Message",
          text: "Please Select Branch First!",
          type: "error"
     });
}
else if ($('#depart').val() == '') {
 swal({
          title: "Error Message",
          text: "Please Select Department First!",
          type: "error"
     });
}
  else if ($('#salarydate').val() == '') {
      swal({
          title: "Error Message",
          text: "Please Enter Salary Date!",
          type: "error"
     });
  }
  else{

    }
  }
    
  
</script>
@endsection