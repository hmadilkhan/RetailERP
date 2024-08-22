@extends('layouts.master-layout')

@section('title','Create Company')

@section('breadcrumtitle','Create Company')

@section('navcompany','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Create Company</h5>
         </div>      
       <div class="card-block">
       	        
       	 <div class="row">
           <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Company Name</label>
                  <input class="form-control" type="text" required="required" 
                   name="companyname" id="companyname"   />
				</div>
              </div>
               <div class="col-md-4">
                           <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                                    <option>Select Country</option>
                                    @if($country)
                                      @foreach($country as $value)
                                        <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select disabled="disabled" name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                    @if($city)
                                      @foreach($city as $value)
                                        <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
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
                  <label class="form-control-label">Company Email</label>
                  <input class="form-control" type="text" required="required" 
                   name="email" id="email"   />
				</div>
              </div>
              <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Mobile Number</label>
                  <input class="form-control" type="text" required="required" 
                   name="mobile" id="mobile"   />
				</div>
              </div>
               <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Ptcl Number</label>
                  <input class="form-control" type="text" required="required" 
                   name="ptcl" id="ptcl"   />
				</div>
              </div>
          </div>
          <div class="row">
           <div class="col-lg-8 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Company Address</label>
                 <textarea name="address" id="address" class="form-control"></textarea>
				</div>
              </div>
                      <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Company Logo</label>

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
           
  		<button type="button" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right" onclick="submit()"> Create Company </button>
                
                 
            
 
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


 

 {{--function submit(){--}}

 {{--	   $.ajax({--}}
 {{--            url: "{{url('/submitbranch')}}",--}}
 {{--            type: 'POST',--}}
 {{--            data:{_token:"{{ csrf_token() }}",--}}
 {{--            countryid:$('#country').val(),--}}
 {{--            cityid:$('#city').val(),--}}
 {{--            branch:$('#branchname').val(),--}}
 {{--            address:$('#address').val(),--}}
 {{--            ptcl:$('#ptcl').val(),--}}
 {{--            mobile:$('#mobile').val(),--}}
 {{--            email:$('#email').val(),--}}
 {{--        	},--}}
 {{--            success:function(resp){--}}
 {{--                if(resp == 1){--}}
 {{--                     swal({--}}
 {{--                      title: "Operation Performed",--}}
 {{--                      text: "Branch Created Successfully!",--}}
 {{--                      type: "success"},--}}
 {{--                      function(isConfirm){--}}
 {{--                      if(isConfirm){--}}
 {{--                   		window.location= "{{ url('/branches') }}";--}}
 {{--                      }--}}
 {{--                       });--}}
 {{--                  }--}}
 {{--                  else{--}}
 {{--                  	swal({--}}
 {{--                            title: "Already exsit",--}}
 {{--                            text: "Particular Branch Already exsit!",--}}
 {{--                            type: "warning"--}}
 {{--                       });--}}
 {{--                  }--}}
 {{--             }--}}

 {{--          });--}}
 {{--}--}}

     
</script>


@endsection



