@extends('layouts.master-layout')

@section('title','Over Time Formula')

@section('breadcrumtitle','Over Time Formula')

@section('navattendance','active')

@section('navot','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Edit Over Time Formula</h5>
            <div class="new-users-more text-left p-t-10">
        <a href="{{ url('/view-ot') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
        </div>
         </div>      
       <div class="card-block">
        <form method="post" action="{{url('/update-ot')}}" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}

       	 <div class="row">
           <div class="col-lg-12 col-md-12">
               <div class="form-group {{ $errors->has('otformula') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Over Time Formula:</label>
                  <input type="hidden" name="otformulaid" id="otformulaid" value="{{ $details[0]->OT_formulaid }}">
                  <input class="form-control" type="text" 
                   name="otformula" id="otformula" value="{{ $details[0]->OTFormula }}"  />
                    @if ($errors->has('otformula'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
				</div>
              </div>
          
        </div>
    
     	<button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > Edit OT Formula </button>
                
               </form>  
           </div> 
 </div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">

</script>
@endsection

