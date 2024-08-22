@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')
@section('content')
        <section class="panels-wells">

               <div class="card">
                   <div class="card-header">
                     <h5 class="card-header-text"> Update Master Details</h5>
                     <h5 class=""><a href="{{ url('get-masters') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                    
                    </div>
                  <div class="card-block">


    <form id="customerform" method="POST" action="{{url('/updatemasters')}}" class="form-horizontal" enctype="multipart/form-data">
      @method('PUT')
      @csrf
              <div class="row">
                    <div class="col-md-4">
                          <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
                           <label class="form-control-label">Customer Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{$details[0]->name}}"  />
                             @if ($errors->has('name'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('mobile') ? 'has-danger' : '' }} ">
                             <label class="form-control-label">Customer Mobile</label>
                                <input type="text" id="mobile" name="mobile" class="form-control" value="{{$details[0]->mobile }}" />
                                 @if ($errors->has('mobile'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                         <div class="form-group {{ $errors->has('phone') ? 'has-danger' : '' }} ">
                             <label class="form-control-label">Contact No</label>
                                <input type="text" id="phone" name="phone" class="form-control" value="{{$details[0]->phone }}" />
                               @if ($errors->has('phone'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                        </div>
                    </div>

                </div>      
               
              <div class="row"> 

                   <div class="col-lg-4">
                     <div class="form-group {{ $errors->has('nic') ? 'has-danger' : '' }} ">
                      <label class="form-control-label">CNIC Number</label>
                         
                       <input type="text" id="nic" name="nic" class="form-control" value="{{$details[0]->nic }}" />
                        @if ($errors->has('nic'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                  </div>


                  <div class="col-md-4">
                 <div class="form-group">
                      <label class="form-control-label">Country</label>
                      <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                          <option>Select Country</option>
                          @if($country)
                            @foreach($country as $value)
                              <option {{$value->country_name == $details[0]->country_name ? 'selected="selected"' : '' }} 
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
                                <select name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                    @if($city)
                                      @foreach($city as $value)
                                        <option {{$value->city_name == $details[0]->city_name ? 'selected="selected"' : '' }}
                                         value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
         </div>
         <div class="row">
                 <div class="col-lg-4">
                     <div class="form-group">
                      <label class="form-control-label">Credit Limit</label>
                         
                       <input type="text" id="creditlimit" name="creditlimit" class="form-control" value="{{$details[0]->credit_limit }}" />
                    </div>
                  </div>

                

                       <div class="col-lg-4">
                     <div class="form-group">
                      <label class="form-control-label">Customer Email</label>
                       <input type="text" id="email" name="email" class="form-control" value="{{$details[0]->email }}" />
                    </div>
                  </div>

                  

         </div>

         <div class="row">
                 <div class="col-md-8">
                    <div class="form-group {{ $errors->has('address') ? 'has-danger' : '' }} ">
                        <label class="form-control-label">Address</label>
                        <textarea name="address" rows="6" id="address" class="form-control" >
                          {{$details[0]->address }}
                        </textarea>
                          @if ($errors->has('address'))
                           <div class="form-control-feedback">Required field can not be blank.</div>
                           @endif
                    </div>
                </div>

                     <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('public/assets/images/master/'.(!empty($details[0]->image) ? $details[0]->image : 'placeholder.jpg').'') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Customer Image</label>
                                    <label for="vdimg" class="custom-file">
                                                <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                              </div>
                        </div>

                        <input type="hidden" name="created_at" id="created_at" value="{{$details[0]->created_at }}" />
                        <input type="hidden" name="masterid" id="masterid" value="{{$details[0]->id }}" />
                        <input type="hidden" name="custimage" id="custimage" value="{{$details[0]->image }}" />

                
               
             </div>

                  <button type="submit" class="btn btn-success f-right"><i class="icofont icofont-ui-edit"> </i>&nbsp;Update</button>
                

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