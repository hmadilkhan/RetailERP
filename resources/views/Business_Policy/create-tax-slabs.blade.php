@extends('layouts.master-layout')

@section('title','Tax Slabs')

@section('breadcrumtitle','Create Tax Slabs')

@section('navmanage','active') 

@section('navtaxslabs','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Create Tax Slabs</h5>
      <h6 class=""><a href="{{ url('/showtaxslabs-active') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <form method="post" action="{{ url('/store-taxslabs') }}" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}
       	        
       	 <div class="row">
           <div class="col-lg-3 col-md-3">
            
           <div class="form-group {{ $errors->has('slabmin') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Tax Slab Minimum</label>
                  <input class="form-control" type="Number" min="1" 
                   name="slabmin" id="slabmin" value="{{ old('slabmin') }}"  />
                    @if ($errors->has('slabmin'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
				        </div>
            </div>
                 <div class="col-lg-3 col-md-3">
            
           <div class="form-group {{ $errors->has('slabmax') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Tax Slab Maximum</label>
                  <input class="form-control" type="Number" min="1"
                   name="slabmax" id="slabmax" value="{{ old('slabmax') }}"  />
                    @if ($errors->has('slabmax'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>
             <div class="col-lg-3 col-md-3">
            <div class="form-group {{ $errors->has('taxpercentage') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Tax Percentage</label>
                  <input class="form-control" type="Number" min="1" 
                   name="taxpercentage" id="taxpercentage" value="{{ old('taxpercentage') }}"  />
                    @if ($errors->has('taxpercentage'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>

                <div class="col-lg-3 col-md-3">
            <div class="form-group {{ $errors->has('year') ? 'has-danger' : '' }} ">
                  <label class="form-control-label">Session Year</label>
                      <input class="form-control" type="Number" min="1" 
                   name="year" id="year" value="{{ old('year') }}"  />
                    @if ($errors->has('year'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
            </div>
       
        </div>
    
  		<button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > Create Slab </button>
                
               </form>  
           </div> 
 </div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
$(".select2").select2();


</script>


@endsection



