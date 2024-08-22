@extends('layouts.master-layout')

@section('title','Master')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')


@section('content')
        <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text"> Create Master</h5>
                     <h5 class=""><a href="{{ url('get-masters') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                    
                    </div>
                  <div class="card-block">

    <form id="customerform" method="POST" class="form-horizontal" enctype="multipart/form-data"  action="{{ url('/store-master') }}">
      @method('POST')
       @csrf
    	<input type="hidden" id="hidd_amt" name="hidd_amt">
    	<input type="hidden" id="hidd_id" name="hidd_id">
              <div class="row">
        	
                    <div class="col-md-4">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Master Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('mobile') ? 'has-danger' : '' }} ">
                             <label class="form-control-label">Mobile</label>
                                <input type="text" id="mobile" name="mobile" class="form-control" value="{{ old('mobile') }}" />
                                 @if ($errors->has('mobile'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                         <div class="form-group {{ $errors->has('phone') ? 'has-danger' : '' }} ">
                             <label class="form-control-label">Contact No</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" />
                               @if ($errors->has('phone'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                </div>      
               
              <div class="row"> 

                   <div class="col-lg-4">
                     <div class="form-group {{ $errors->has('nic') ? 'has-danger' : '' }} ">
                      <label class="form-control-label">CNIC</label>
                         
                       <input type="text" id="nic" name="nic" class="form-control" value="{{ old('nic') }}" />
                        @if ($errors->has('nic'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                  </div>


                <div class="col-lg-4">
                    <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
                         <label class="form-control-label">Select Country</label>
                        <select class="select2" data-placeholder="Select Country" id="country" name="country">
                            <option value="">Select Country</option>
                            @if($country)
                              @foreach($country as $val)
                                <option value="{{$val->country_id}}">{{$val->country_name}}</option>
                              @endforeach
                            @endif
                            
                        </select>
                          @if ($errors->has('country'))
                               <div class="form-control-feedback">Required field can not be blank.</div>
                          @endif
                    </div>
                </div> 




                <div class="col-lg-4">
                    <div class="form-group {{ $errors->has('city') ? 'has-danger' : '' }}">
                         <label class="form-control-label">Select City</label>
                        <select class="select2" data-placeholder="Select City" id="city" name="city" >
                        	     <option value="">Select City</option>
                                 @if($city)
                                @foreach($city as $value)
                                  <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
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
                 <div class="col-lg-4">
                     <div class="form-group">
                      <label class="form-control-label">Credit Limit</label>
                         
                       <input type="text" id="creditlimit" name="creditlimit" class="form-control" value="{{ old('creditlimit') }}" />
                    </div>
                  </div>
                
                       <div class="col-lg-4">
                     <div class="form-group">
                      <label class="form-control-label">Customer Email</label>
                       <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}" />
                    </div>
                  </div>

                  

         </div>

         <div class="row">
                 <div class="col-md-8">
                    <div class="form-group {{ $errors->has('address') ? 'has-danger' : '' }} ">
                        <label class="form-control-label">Address</label>
                        <textarea name="address" rows="6" id="address" class="form-control" value="{{ old('address') }}">
                        </textarea>
                          @if ($errors->has('address'))
                           <div class="form-control-feedback">Required field can not be blank.</div>
                           @endif
                    </div>
                </div>

                       <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Customer Profile Picture</label>
                                <br/>
                                    <label for="vdimg" class="custom-file">
                                                 <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                               
                              </div>
                        </div>

                
               
             </div>

                  <button type="submit" class="btn btn-primary f-right">Submit</button>
                

         </form>
            
                  </div>
               </div>
            </section>    
@endsection


@section('scriptcode_three')


  <script type="text/javascript">
         $(".select2").select2();

          $('#dob').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
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


    $('#country').change(function(){

        $.ajax({
        url:'{{ url("/getCityById") }}',
        data:{_token : "{{csrf_token()}}",id:$('#country').val()},
        type:"POST",
        success:function(result){
           $('#city').empty();
          $('#city').append($('<option>').text('Select City').attr('value', ''));
             $.each(result, function (i, value) {
              $('#city').append($('<option>').text(value.city_name).attr('value', value.city_id)); 
            });
          }
        });

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

  </script>

@endsection