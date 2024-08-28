@extends('Admin.layouts.master-layout')

@section('title','Edit Branch')

@section('breadcrumtitle','Create Branch')

@section('navbranch','active')

@section('content')
<section class="panels-wells">
<div class="card">

     <div class="card-header">
         <h5 class="card-header-text">Edit Branch</h5>
         <h5 class=""><a href="{{ url('/view-branch') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
         </div>      
       <div class="card-block">
       	<form method="POST" id="upload_form" enctype="multipart/form-data">
        {{ csrf_field() }}
       	 <div class="row">
           <div class="col-lg-4 col-md-4">
              <input type="hidden" name="branchId" id="branchId" value="{{$details[0]->branch_id}}">
              <input type="hidden" name="branchLogo" id="branchLogo" value="{{$details[0]->branch_logo}}">

               <div class="form-group">
                  <label class="form-control-label">Branch Name</label>
                  <input class="form-control" type="text" required="required"  
                   name="branchname" id="branchname"  value="{{$details[0]->branch_name}}"   />
				      </div>
           </div>
               <div class="col-md-4">
                           <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                                     @if($country)
                                      @foreach($country as $value)
                                        <option {{$value->country_id == $details[0]->country_id ? 'selected="selected"' : '' }} 
                                         value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select  name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                     @if($city)
                                      @foreach($city as $value)
                                       <option {{$value->city_id == $details[0]->city_id ? 'selected="selected"' : '' }}
                                         value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
           
             

            </div>
        </div>
         <div class="row">
           <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Branch Email</label>
                  <input class="form-control" type="text" required="required" 
                   name="br_email" id="br_email" value="{{$details[0]->branch_email}}"/>
				</div>
              </div>
              <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Mobile Number</label>
                  <input class="form-control" type="text" required="required" 
                   name="br_mobile" id="br_mobile" value="{{$details[0]->branch_mobile}}"   />
				</div>
              </div>
               <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Ptcl Number</label>
                  <input class="form-control" type="text" required="required" 
                   name="br_ptcl" id="br_ptcl" value="{{$details[0]->branch_ptcl}}"   />
				</div>
              </div>
          </div>
          <div class="row">
             <div class="col-md-4">
                           <div class="form-group">
                                <label class="form-control-label">Company</label>
                                <select name="br_company" id="br_company" data-placeholder="Select Company" class="form-control select2" >
                                    <option value="">Select Company</option>
                                    @if($country)
                                      @foreach($company as $value)
                                         <option {{$value->company_id == $details[0]->company_id ? 'selected="selected"' : '' }} 
                                         value="{{ $value->company_id }}">{{ $value->name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
           <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Branch Address</label>
                 <textarea name="br_address" id="br_address" class="form-control">{{$details[0]->branch_address}}</textarea>
				</div>
              </div>
                      <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('assets/images/branch/'.$details[0]->branch_logo) }}" class="thumb-img img-fluid width-100" alt="{{$details[0]->branch_logo}}" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Branch Logo</label>

                                    <label for="vdimg" class="custom-file">
                                                <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                              </div>
                        </div>
          </div> 
         
           
  		<button type="submit"  class="btn btn-md btn-primary waves-effect waves-light f-right"> Update Branch </button>
                
            
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

$('#upload_form').on('submit', function(event){
  event.preventDefault();

     $.ajax({
            url: "{{url('/update-branch')}}",
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success:function(resp){
              console.log(resp);
                if(resp == 1){
                     swal({
                      title: "Operation Performed",
                      text: "Branch Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                      window.location= "{{ url('/view-branch') }}";
                      }
                       });
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Name of Branch Already exists!",
                            type: "warning"
                       });
                  }
             }

          });  
});

 
</script>


@endsection



