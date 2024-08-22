@extends('layouts.master-layout')

@section('title','Create Company')

@section('breadcrumtitle','Create Company')

@section('navcompany','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Create Company</h5>
         <h5 class=""><a href="{{route('company.index')}}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
         </div>      
       <div class="card-block">
       <form method="post" action="{{ url('insert-company') }}" class="form-horizontal" enctype="multipart/form-data">	        
        @csrf
       	 <div class="row">
           <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('companyname') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Company Name</label>
                  <input class="form-control" type="text" 
                   name="companyname" id="companyname" value="{{ old('companyname') }}"  />
                   @if ($errors->has('companyname'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
               <div class="col-md-4">
                           <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                                    <option>Select Country</option>
                                    @if($country)
                                      @foreach($country as $value)
                                        @if(old('country') == $value->country_id)
                                          <option selected="selected" value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                        @else
                                          <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                        @endif
                                      @endforeach
                                    @endif
                                </select>
                                  @if ($errors->has('country'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group {{ $errors->has('city') ? 'has-danger' : '' }}">
                                <label class="form-control-label">City</label>
                                <select disabled="disabled" name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                    @if($city)
                                      @foreach($city as $value)
                                        @if(old('city') == $value->city_id)
                                           <option selected="selected" value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                        @else
                                          <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                        @endif
                                      @endforeach
                                    @endif
                                </select>
                                  @if ($errors->has('city'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
           
             

            </div>
        </div>
         <div class="row">
           <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('company_email') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Company Email</label>
                  <input class="form-control" type="text" 
                   name="company_email" id="company_email"  value="{{ old('company_email') }}" />
                    @if ($errors->has('company_email'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
              <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('company_mobile') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Mobile Number</label>
                  <input class="form-control" type="text" 
                   name="company_mobile" id="company_mobile"  value="{{ old('company_mobile') }}" />
                    @if ($errors->has('company_mobile'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
               <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('company_ptcl') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Ptcl Number</label>
                  <input class="form-control" type="text" 
                   name="company_ptcl" id="company_ptcl"   value="{{ old('company_ptcl') }}"/>
                   @if ($errors->has('company_ptcl'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
          </div>
          <div class="row">
           <div class="col-lg-12 col-md-4">
               <div class="form-group {{ $errors->has('company_address') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Company Address</label>
                 <textarea name="company_address" id="company_address" class="form-control">{{ old('company_address') }}</textarea>
                 @if ($errors->has('company_address'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
                      <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group {{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Company Logo</label>
                                 <br/>
                                    <label for="vdimg" class="custom-file m-t-20">
                                        <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                        <span class="custom-file-control"></span>
                                    </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                              </div>
                      </div>

                      <div class="col-md-4" >
                          <a href="#">
                              <img id="posbimg" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                          </a>
                          <div class="form-group {{ $errors->has('posbgimg') ? 'has-danger' : '' }} ">
                              <label for="posbgimg" class="form-control-label">POS Background</label>
                          <br/>
                              <label for="posbgimg" class="custom-file m-t-20">
                                  <input type="file" name="posbgimg" id="posbgimg" class="custom-file-input">
                                  <span class="custom-file-control"></span>
                              </label>
                              @if ($errors->has('posbgimg'))
                                  <div class="form-control-feedback">{{ $errors->first('posbgimg') }}</div>
                              @endif
                          </div>
                      </div>

          </div> 
           
  		<button type="submit" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right" > Create Company </button>
                
                 
            
 </form>
</div>
</div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
$(".select2").select2();


$("#country").on('change',function(){

   if($(this).val() != ""){
       $("#city").attr("disabled",false);
   }else {
    $("#city").attr("disabled",true);
   }
});

function readURL(input,id) {

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#'+id).attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

$("#vdimg").change(function() {
  readURL(this,'vdpimg');
});

$("#posbgimg").change(function() {
    readURL(this,'posbimg');
});


 


     
</script>


@endsection



