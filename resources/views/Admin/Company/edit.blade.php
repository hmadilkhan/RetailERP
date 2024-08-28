@extends('layouts.master-layout')

@section('title','Edit Company')

@section('breadcrumtitle','Edit Company')

@section('navcompany','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Edit Company</h5>
         <h5 class=""><a href="{{route('company.index')}}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
         </div>      

       <div class="card-block">
       <form method="post" action="{{ url('update-company') }}" class="form-horizontal" enctype="multipart/form-data">	        
        @csrf
       	 <div class="row">
           <div class="col-lg-4 col-md-4">
            <input type="hidden" name="company_id" id="company_id" value="{{$company[0]->company_id}}">
            <input type="hidden" name="prev_logo" id="prev_logo" value="{{$company[0]->logo}}">
            <input type="hidden" name="pos_bg_logo" id="pos_bg_logo" value="{{$company[0]->pos_background}}">
               <div class="form-group {{ $errors->has('companyname') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Company Name</label>
                  <input class="form-control" type="text" 
                   name="companyname" id="companyname" value="{{ $company[0]->name }}"  />
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
                                        @if($company[0]->country_id == $value->country_id)
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
                                <select  name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                    @if($city)
                                      @foreach($city as $value)
                                        @if($company[0]->city_id == $value->city_id)
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
                   name="company_email" id="company_email"  value="{{ $company[0]->email }}" />
                    @if ($errors->has('company_email'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
              <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('company_mobile') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Mobile Number</label>
                  <input class="form-control" type="text" 
                   name="company_mobile" id="company_mobile"  value="{{ $company[0]->mobile_contact }}" />
                    @if ($errors->has('company_mobile'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
               <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('company_ptcl') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Ptcl Number</label>
                  <input class="form-control" type="text" 
                   name="company_ptcl" id="company_ptcl"   value="{{ $company[0]->ptcl_contact }}"/>
                   @if ($errors->has('company_ptcl'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
          </div>
          <div class="row">
		  <div class="col-md-4">
			   <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
					<label class="form-control-label">Currency</label>
					<select name="currency" id="currency" data-placeholder="Select Currency" class="form-control select2" >
						<option>Select Currency</option>
						@if($currencies)
						  @foreach($currencies as $currency)
							@if($currencyname == $currency->name)
							  <option selected="selected" value="{{ $currency->name }}">{{ $currency->name }}</option>
							@else
							  <option value="{{ $currency->name }}">{{ $currency->name }}</option>
							@endif
						  @endforeach
						@endif
					</select>
					  @if ($errors->has('currency'))
						<div class="form-control-feedback">Required field can not be blank.</div>
					@endif
				</div>
			</div>
			<div class="col-md-4">
			   <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
					<label class="form-control-label">Packages</label>
					<select name="package" id="package" data-placeholder="Select Packages" class="form-control select2" >
						<option>Select Packages</option>
						@if($packages)
						  @foreach($packages as $package)
							@if($company[0]->package_id == $package->id)
							  <option selected="selected" value="{{ $package->id }}">{{ $package->name }}</option>
							@else
							  <option value="{{ $package->id }}">{{ $package->name }}</option>
							@endif
						  @endforeach
						@endif
					</select>
					  @if ($errors->has('package'))
						<div class="form-control-feedback">Required field can not be blank.</div>
					@endif
				</div>
			</div>
           <div class="col-lg-4 col-md-4">
               <div class="form-group {{ $errors->has('company_address') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Company Address</label>
                 <textarea name="company_address" id="company_address" class="form-control">{{ $company[0]->address }}</textarea>
                 @if ($errors->has('company_address'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
              </div>
                      <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('assets/images/company/'.$company[0]->logo) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group {{ $errors->has('vdimg') ? 'has-danger' : '' }} ">

                                 <label for="vdimg" class="form-control-label">Company Logo</label>
                                 <br/>
                                    <label for="vdimg" class="custom-file">
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
                                  <img id="posbimg" src="{{ asset('assets/images/pos-background/'.$company[0]->pos_background) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                              </a>
                              <div class="form-group {{ $errors->has('posbgimg') ? 'has-danger' : '' }} ">
                                  <label for="posbgimg" class="form-control-label">POS Background</label>
                                  <br/>
                                  <label for="posbgimg" class="custom-file ">
                                      <input type="file" name="posbgimg" id="posbgimg" class="custom-file-input">
                                      <span class="custom-file-control"></span>
                                  </label>
                                  @if ($errors->has('posbgimg'))
                                      <div class="form-control-feedback">{{ $errors->first('posbgimg') }}</div>
                                  @endif
                              </div>
                          </div>
          </div> 
           
  		<button type="submit" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right" > Edit Company </button>
                
                 
            
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



