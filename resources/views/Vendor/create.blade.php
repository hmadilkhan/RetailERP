@extends('layouts.master-layout')

@section('title','Vendor')

@section('breadcrumtitle','Create Vendor')

@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
        <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text"> Create Vendor</h5>
                     <h5 class=""><a href="{{ route('vendors.index') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                    </div>
                  <div class="card-block">
               
                     <form method="POST" action="{{ route('vendors.store') }}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                       <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('vdname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Vendor Name</label>
                                <input type="text" name="vdname" id="vdname" placeholder="Vendor Name" class="form-control" value="{{ old('vdname') }}"/>
                                 @if ($errors->has('vdname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                <div class="form-control-feedback text-danger" id="vdemail_alert"></div>
                                
                            </div>
                        </div>
                     <!--    <div class="col-md-4">
                            <div class="form-group {{ $errors->has('vdemail') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Vendor Email</label>
                                <input type="email" name="vdemail" id="vdemail" placeholder="Vendor Email" class="form-control" value="{{ old('vdemail') }}" />
                                   @if ($errors->has('vdemail'))
                                    <div class="form-control-feedback">Required field and email format not valid.</div>
                                @endif
                                <div class="form-control-feedback text-danger" id="vdemail_alert"></div>
                            </div>
                        </div> -->
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('vdcontact') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Vendor Contact</label>
                                <input type="Number" name="vdcontact" id="vdcontact" placeholder="Vendor Contact" class="form-control" value="{{ old('vdcontact') }}" min="0"  />
                                  @if ($errors->has('vdcontact'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Company Name</label>
                                <input type="text" name="cpname" id="cpname" placeholder="Company Name" class="form-control" />
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                       </div>
                       <!-- <div class="row">                         -->
                        
                      <!--   <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Company Email</label>
                                <input type="email" name="cpemail" id="cpemail" placeholder="Vendor Email" class="form-control" />
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Company Contact</label>
                                <input type="text" name="cpcontact" id="cpcontact" placeholder="Company Contact" class="form-control" />
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                       </div>  -->
                       <!-- <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Company Fax</label>
                                <input type="text" name="cpfax" id="cpfax" placeholder="Company Fax" class="form-control" />
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Website</label>
                                <input type="text" name="cpwebsite" id="cpwebsite" placeholder="Website" class="form-control" />
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Company Type</label>
                                <select name="cptype" id="cptype" data-placeholder="Select Type" class="form-control select2" >
                                    <option>Select Type</option>
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                       </div> -->
                       <div class="row">   
                          <div class="col-md-4">
                            <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                                    <option value="">Select Country</option>
                                    @if($country)
                                      @foreach($country as $value)
{{--                                        @if($value->country_name == 'Pakistan')--}}
{{--                                           <option selected="selected" value="{{ $value->country_id }}">{{ $value->country_name }}</option>--}}
{{--                                        @else--}}
                                           <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
{{--                                        @endif--}}
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
                                <i id="btn_city" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add City" ></i>
                                <select  name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option value="">Select City</option>

                                </select>
                                  @if ($errors->has('city'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Opening Balance</label>
                                <input type="text" name="ob" id="ob" placeholder="Opening Balance" class="form-control" value="0" />
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                         
                       </div>
                       <div class="row">
                           <div class="col-md-4">
                               <div class="form-group">
                                   <label class="form-control-label">Payment Terms Days:</label>
                                   <input type="Number" name="paymentdays" id="paymentdays" placeholder="0" class="form-control" value="0" min="0" />
                                   <div class="form-control-feedback"></div>
                               </div>
                           </div>
						   <div class="col-md-4">
								<div class="form-group">
									<label class="form-control-label">NTN</label>
									<input type="text" name="ntn" id="ntn" placeholder="Enter NTN number" class="form-control" />
									 <div class="form-control-feedback"></div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="form-control-label">STRN</label>
									<input type="text" name="strn" id="strn" placeholder="Enter STRN number" class="form-control"  />
									 <div class="form-control-feedback"></div>
								</div>
							</div>
                       </div>
                       <div class="row">
							<div class="col-md-8">
                               <div class="form-group">
                                   <label class="form-control-label">Company Address</label>
                                   <textarea name="address" rows="3" id="address" class="form-control"></textarea>
                                   <div class="form-control-feedback"></div>
                               </div>
                           </div>
					   </div>
					   <div class="row">
                        <div class="col-md-4" >
                               <a href="#">
                                <img id="plogo" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>

                            <div class="form-group{{ $errors->has('logo') ? 'has-danger' : '' }} ">
                                 <label for="logo" class="form-control-label">Company Logo</label>
                                <br/>
                                    <label for="logo" class="custom-file">
                                                <input type="file" name="logo" id="logo" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                @if ($errors->has('logo'))
                                    <div class="form-control-feedback">{{ $errors->first('logo') }}</div>
                                @endif
                              </div> 
                        </div>
                        <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Vendor Image</label>
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

                       </div>  

                            <div class="contact-card-button m-t-20 f-right">
                               <button type="submit" class="btn btn-md btn-info waves-effect waves-light">
                                  <i class="icofont icofont-plus m-r-5"> </i>Create Vendor
                               </button>
                            </div>                                   
                      </form>
            
                  </div>
               </div>
            <div class="modal fade modal-flex" id="city-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Add City</h4>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <select class="form-control  select2" data-placeholder="Select Country" id="countrymodal" name="countrymodal">
                                            <option value="">Select Country</option>
                                            @foreach($country as $value)
                                               <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Enter City Name:</label>
                                        <input type="text"  name="citymodal" id="citymodal" class="form-control" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="add_city" class="btn btn-success waves-effect waves-light" >Add City</button>
                        </div>
                    </div>
                </div>
            </div>
            </section>



@endsection

@section('scriptcode_three')

<script type="text/javascript">
   $(".select2").select2();

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

$("#logo").change(function() {
  readURL(this,'plogo');
});

$("#country").on('change',function(){

   if($(this).val() != ""){
       $("#city").attr("disabled",false);
   }else {
    $("#city").attr("disabled",true);
   }
});

$("#vdemail").on('change',function(){

    if($(this).val() != ""){

        $.ajax({
                url:"{{ url('/vendoremail') }}",
                type:"POST",
                data:{_token:"{{ csrf_token() }}",email:$(this).val()},
                success:function(resp){

                    if(resp == 1){
                        $("#vdemail_alert").html("Sorry, that email address taken. Try another?");
                    }else{
                        $("#vdemail_alert").html("");
                    }
                }

        });
    }

    

});


$("#vdname").on('change',function(){

    if($(this).val() != ""){

            $.ajax({
                    url:"{{ url('/vendorname') }}",
                    type:"POST",
                    data:{_token:"{{ csrf_token() }}",name:$(this).val()},
                    success:function(resp){

                        if(resp == 1){
                            $("#vdemail_alert").html("Sorry, that name already in use. Try another?");
                            $('#vdname').val('');
                        }else{
                            $("#vdemail_alert").html("");
                        }
                    }

            });
    }

    

});

$('#btn_city').click(function () {
    $('#citymodal').val('');
    $('#city-modal').modal("show");

})


$('#add_city').click(function () {
    if($('#countrymodal').val() == ""){
        swal({
            title: "Error Message",
            text: "Country Required",
            type: "warning"
        });
    }
    else if($('#citymodal').val() == ""){
        swal({
            title: "Error Message",
            text: "City Required",
            type: "warning"
        });
    }else{
        $.ajax({
            url:"{{ url('/add-city') }}",
            type:"POST",
            data:{_token:"{{ csrf_token() }}",city:$('#citymodal').val(),country:$('#countrymodal').val()},
            success:function(resp){

                if(resp.status == 2){
                    swal({
                        title: "Error Message",
                        text: "City already Exists",
                        type: "error"
                    });
                }else if(resp.status == 0){
                    swal({
                        title: "Error Message",
                        text: "Something went wrong !!! ",
                        type: "error"
                    });
                }else{
                    swal({
                        title: "Success Message",
                        text: "City addedd Successfully ",
                        type: "success"
                    });
                    $('#city-modal').modal("hide");
                    loadCities($('#country').val());

                }
            }

        });
    }
});
   loadCities($('#country').val());
function loadCities(country) {
    $.ajax({
        url:"{{ url('/getCityById') }}",
        type:"POST",
        data:{_token:"{{ csrf_token() }}",id:country},
        success:function(resp){
            if(resp){
                $('#city').empty();
                $.each(resp, function( index, value ) {
                   $('#city').append(
                     "<option value='"+value.city_id+"'>"+value.city_name+"</option>"
                   );
                });

            }else{

            }
        }

    });
}

$('#country').change(function () {
    loadCities($('#country').val());
})





</script>


@endsection




