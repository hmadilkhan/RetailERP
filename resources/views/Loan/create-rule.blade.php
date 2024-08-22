@extends('layouts.master-layout')

@section('title','Loan')

@section('breadcrumtitle','Loan Deduction')

@section('navloan','active')

@section('navdeduct','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Loan Deduction Rule</h5>
          <h6 class=""><a href="{{ url('/view-loandeduct') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
    
             <form method="POST" action="{{ url('/insert-loandeduct') }}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
			<div class="form-group row ">
                <div class="col-lg-12 col-md-12">
                	<label class="form-control-label">Loan Deduction Rule Value</label>
                	<label class="sr-only" for="alighaddon2">Align addon</label></div>
                <div class="col-lg-12 col-md-12">
                   <div class="input-group">
                      <input type="number" id="alighaddon2" class="form-control"  aria-describedby="basic-addon2" min="1" name="loan"  value="{{ old('loan') }}">
                      <span class="input-group-addon" id="basic-addon2">days</span>
               
                   </div>
                           @if ($errors->has('loan'))
                <div class="form-control-feedback text-danger">Required field can not be blank.</div>
            @endif
                </div>
             </div>
              <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > <i class="icofont icofont-plus"> </i>
                        Set Rule
                    </button>
                    </div>       
                </div>  
            </div> 
       </form>
   </div>
</div>
	</section>
	@endsection

@section('scriptcode_three')

<script type="text/javascript">
	
</script>
@endsection