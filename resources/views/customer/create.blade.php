@extends('layouts.master-layout')

@section('title','Customer')

@section('breadcrumtitle','Add Expense')

@section('navcustomer','active')


@section('content')
<section class="panels-wells">

 <div class="card">
  <div class="card-header">
   <h5 class="card-header-text"> Create Customer</h5>
   <h5 class=""><a href="{{ route('customer.index') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>

 </div>
 <div class="card-block">
 <!-- Supplier charges Field -->
    <div style="display:none">
        <div class="col-lg-12">
      <div id="inputClone">
          <div class="col-lg-3 col-md-3">
            <div class="form-group">
              <label class="form-control-label">Area:</label>
               <input type="text" name="area[]" id="area" required="" class="form-control"/>
            </div>
          </div>
          <div class="col-lg-3 col-md-3">
            <div class="form-group">
              <label class="form-control-label">Street Address:</label>
              <input type="text" name="street_address[]" id="area" required="" class="form-control"/>
            </div>
          </div>
           <div class="col-lg-3 col-md-3">
            <div class="form-group">
              <label class="form-control-label">Comment:</label>
              <textarea name="comment[]" class="form-control"></textarea>
            </div>
          </div>
          <div class="col-lg-1 col-md-1">
            <div class="form-group">
                <label class="m-t-40"></label>
              <a class="blue-btn2 addset floatleft remove_row remove-button" href="javascript:void(0)" style="text-align: center;">
              &nbsp;Remove 
            </a>
          </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
      <div class="clear"></div>
    </div>
    <!-- Supplier Field -->
  <form id="customerform" method="POST" class="form-horizontal" enctype="multipart/form-data"  action="{{ route('customer.store') }}">
   @csrf
   <input type="hidden" id="hidd_amt" name="hidd_amt">
   <input type="hidden" id="hidd_id" name="hidd_id">
   <div class="row">

    <div class="col-md-4">
      <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
       <label class="form-control-label">Customer Name</label>
       <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" />
       @if ($errors->has('name'))
       <div class="form-control-feedback">Required field can not be blank.</div>
       @endif
     </div>
   </div>

   <div class="col-md-4">
    <div class="form-group {{ $errors->has('mobile') ? 'has-danger' : '' }} ">
     <label class="form-control-label">Custome Mobile</label>
     <input type="text" id="mobile" name="mobile" class="form-control"  value="{{ old('mobile') }}" oninput="validity.valid||(value='');" onkeypress='return restrictAlphabets(event)' />
     @if ($errors->has('mobile'))
     <div class="form-control-feedback">{{ $errors->first('mobile') }}</div>
     @endif
   </div>
 </div>

 <div class="col-md-4">
   <div class="form-group {{ $errors->has('phone') ? 'has-danger' : '' }} ">
     <label class="form-control-label">Contact No</label>
     <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" onkeypress='return restrictAlphabets(event)' />
     @if ($errors->has('phone'))
     <div class="form-control-feedback">{{ $errors->first('phone') }}</div>
     @endif
   </div>
 </div>

</div>      

<div class="row"> 

 <div class="col-lg-4">
   <div class="form-group {{ $errors->has('nic') ? 'has-danger' : '' }} ">
    <label class="form-control-label">CNIC</label>

    <input type="text" id="nic" name="nic" class="form-control" value="{{ old('nic') }}" onkeypress='return restrictAlphabets(event)' />
    @if ($errors->has('nic'))
    <div class="form-control-feedback">{{ $errors->first('nic') }}</div>
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
    <option {{(old("country") == $val->country_id ? "selected" : "")}}  value="{{$val->country_id}}">{{$val->country_name}}</option>
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
    <option {{(old("city") == $value->city_id ? "selected" : "")}}  value="{{ $value->city_id }}">{{ $value->city_name }}</option>
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
 <div class="col-lg-3">
   <div class="form-group">
    <label class="form-control-label">Credit Limit</label>

    <input type="numeric" id="creditlimit" name="creditlimit" onkeypress='return restrictAlphabets(event)'  class="form-control" value="{{ old('creditlimit') }}" />
  </div>
</div>

<div class="col-lg-3">
 <div class="form-group">
  <label class="form-control-label">Discount</label>
  <input type="numeric" id="discount" name="discount" onkeypress='return restrictAlphabets(event)' class="form-control" value="{{ old('discount') }}" />
</div>
</div>

<div class="col-lg-3">
 <div class="form-group">
  <label class="form-control-label">Customer Email</label>
  <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}" />
</div>
</div>

<div class="col-lg-3">
 <div class="form-group">
   <label class="form-control-label">Opening Balance</label>
   <input type="numeric" value="0" id="ob" name="ob" class="form-control" onkeypress='return restrictAlphabets(event)' />
 </div>
</div>



</div>

<div class="row">
 <div class="col-md-8">
  <div class="form-group {{ $errors->has('address') ? 'has-danger' : '' }} ">
    <label class="form-control-label">Address</label>
    <textarea name="address" rows="6" id="address" class="form-control" value="">{{ old('address') }}</textarea>
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

<div class="col-lg-3">
 <div class="form-group">
  <label class="form-control-label">Customer Type</label>
  <select onchange="CustomerType(this)" name="customer_type" class="form-control">
    <option value="">Select</option>
    <option value="1">Retailer</option>
    <option value="2">Supplier</option>
  </select>
</div>
</div>

<div class="col-lg-3">
 <div class="form-group">
  <label class="form-control-label">Payment Type</label>
  <select  name="payment_type" class="form-control">
    <option value="">Select</option>
    <option value="1">Cash</option>
    <option value="2">Credit</option>
  </select>
</div>
</div> 

<div class="col-lg-3">
 <div class="form-group">
  <label class="form-control-label">Area</label>
  <input type="text" name="customer_area" value="" placeholder="Area" class="form-control" />
</div>
</div>



</div>
<div class="row" id="hideSupplierDiv" style="margin-top: 20px;display: none">
<div class="col-md-12">
  <h5>Supplier Detail    
    <i href="javascript:void(0)" onclick="return clone_field()" class=" pull-right icofont icofont-plus f-right text-success" title="Add Supplier Detail" ></i>
  </h5>    
  <hr/>
  <div id="inputfieldClone">
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

 /*code: 48-57 Numbers*/
 function restrictAlphabets(e) {
   var x = e.which || e.keycode;
   if ((x >= 45 && x <= 57))
     return true;
   else
     return false;
 }


 $('#raw').change(function(){

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
function clone_field(){
    $('#inputClone').clone().appendTo("#inputfieldClone");
  }

  $(document).on("click",".remove_row",function(){
    $(this).closest('#inputClone').remove();
  });

  function CustomerType(arg){
      var idx = arg.selectedIndex; 
      if(idx == 1){
        $('#hideSupplierDiv').hide();
        $('#inputfieldClone').empty();
      }else if(idx == 2){
        $('#hideSupplierDiv').show();
      }else{
        $('#hideSupplierDiv').hide();
        $('#inputfieldClone').empty();
      }
  }
</script>

@endsection