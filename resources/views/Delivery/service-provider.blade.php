@extends('layouts.master-layout')

@section('title','Delivery Service Provider')

@section('breadcrumtitle','Delivery Service Provider').

@section('navdelivery','active')

@section('navservices','active')

@section('content')

<section class="panels-wells">
  <div class="card">
   <div class="card-header">
     <h5 class="card-header-text">Create Service Provider</h5>
     <h6 class=""><a href="{{ url('/service-provider') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
   </div>
   <div class="card-block">
    <!-- Additional charges Field -->
    <div style="display:none">
        <div class="col-lg-12">
      <div id="inputClone">
          <div class="col-lg-3 col-md-3">
            <div class="form-group">
              <label class="form-control-label">Charge Name:</label>
              <input type="text" name="chargeName[]" id="chargeName" required="" class="form-control"/>
            </div>
          </div>
          <div class="col-lg-3 col-md-3">
            <div class="form-group">
              <label class="form-control-label">Charge Value:</label>
              <input type="text" name="chargeValue[]" onkeypress="return isNumberKey(event)" id="chargeValue" required="" class="form-control"/>
            </div>
          </div>
          <div class="col-lg-3 col-md-3">
                <div class="form-group">
                  <label class="form-control-label">Type:</label>
                  <select class="form-control" name="type[]">
                    <option>select</option>
                    <option value="commission">Commission</option>
                    <option value="bank">Bank</option>
                    <option value="other">Other</option>
                  </select>
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
    <!-- Additional charges Field -->
    <form method="post" action="{{url('insert-serviceprovider')}}" enctype="multipart/form-data">
      @csrf
      @method('post')
      <div class="row">
       <div class="col-lg-4 col-md-4">
        <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
          <label class="form-control-label">Branch</label>

          <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2"  >
            <option value="">Select Branch</option>
            @if($getbranch)
            @foreach($getbranch as $value)
            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
            @endforeach
            @endif
          </select>
             @if ($errors->has('branch'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
        </div>
      </div>

      <div class="col-lg-4 col-md-4">
        <div class="form-group {{ $errors->has('providername') ? 'has-danger' : '' }}">
          <label class="form-control-label">Service Provider Name:</label>
          <input type="text" name="providername" id="providername" class="form-control" value="{{old('providername')}}"/>
           @if ($errors->has('providername'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
        </div>
      </div>

      <div class="col-lg-4 col-md-4">
        <div class="form-group {{ $errors->has('category') ? 'has-danger' : '' }}">
          <label class="form-control-label">Service Category</label>
          <i id="btn_category" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Category" ></i>
          <select name="category" id="category" data-placeholder="Select Category" class="form-control select2"  >
            <option value="">Select Category</option>
            @if($getcategory)
            @foreach($getcategory as $value)
            <option value="{{ $value->category_id }}">{{ $value->category }}</option>
            @endforeach
            @endif
          </select>
         @if ($errors->has('category'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
        </div>
      </div>
    </div>
    <div class="row">
     <div class="col-lg-4 col-md-4">
      <div class="form-group {{ $errors->has('person') ? 'has-danger' : '' }}">
        <label class="form-control-label">Contact Person</label>
        <input type="text" name="person" id="person" class="form-control" value="{{old('person')}}"/>
        @if ($errors->has('person'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
      </div>
    </div>

    <div class="col-lg-4 col-md-4">
      <div class="form-group {{ $errors->has('contact') ? 'has-danger' : '' }}">
        <label class="form-control-label">Contact Number</label>
        <input type="text" name="contact" id="contact" class="form-control" value="{{old('contact')}}"/>
        @if ($errors->has('contact'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
      </div>
    </div>

    <div class="col-lg-4 col-md-4">
      <div id="cnicdiv" class="form-group {{ $errors->has('cnic') ? 'has-danger' : '' }}">
        <label class="form-control-label">CNIC | NTN No:</label>
        <input type="text" name="cnic" id="cnic" class="form-control" placeholder="12345-1234567-1" value="{{old('cnic')}}" />
		@if ($errors->has('cnic'))
			<div id="cnicerror" class="form-control-feedback">Required field can not be blank.</div>
       @endif
      </div>
    </div>

  </div>
  <div class="row">
    <!--
	<div class="col-lg-4 col-md-4">
      <div class="form-group {{ $errors->has('percentage') ? 'has-danger' : '' }}">
        <label class="form-control-label">Sales Agreement Percentage</label>
        <i id="btn_percent" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Percentage" ></i>
        <select name="percentage" id="percentage" data-placeholder="Select Percentage" class="form-control select2"  >
          <option value="">Select Percentage</option>
          @if($getpercen)
          @foreach($getpercen as $value)
          <option value="{{ $value->percentage_id }}">{{ $value->percentage}} %</option>
          @endforeach
          @endif
        </select>
		 @if ($errors->has('percentage'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
        <div class="form-control-feedback text-info">Please Define here on which percentage u agree to work with this service provider</div>
      </div>
    </div>
	-->
	<div class="col-lg-4 col-md-4">
      <div class="form-group {{ $errors->has('percentage') ? 'has-danger' : '' }}">
        <label class="form-control-label">Payment Type</label>
        <select name="paymenttype" id="paymenttype" data-placeholder="Select Payment Type" class="form-control select2"  >
          <option value="">Select Payment Type</option>
          @if($providersPaymentType)
          @foreach($providersPaymentType as $value)
          <option value="{{ $value->id }}">{{ $value->type}}</option>
          @endforeach
          @endif
        </select>
		 @if ($errors->has('paymenttype'))
			<div class="form-control-feedback">Required field can not be blank.</div>
       @endif
        <div class="form-control-feedback text-info">
			<label class="form-control-label">Payment Value </label>
        <input type="text" name="paymentValue" id="paymentValue" class="form-control" placeholder="In percentage% or amount" value="{{old('paymentValue')}}" />
		</div>
      </div>
    </div>

    <div class="col-lg-4 col-md-4">
      <div class="form-group">
        <label class="form-control-label">Enter Previous Balance</label>
        <input type="Number" name="prebal" id="prebal" class="form-control" value="0"/>

        <div class="form-control-feedback text-info">If any previous balance please enter here, If amount is payable then enter in positive figure while reciveable then enter in negative figure</div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4">
      <div class="form-group {{ $errors->has('address') ? 'has-danger' : '' }}">
        <label class="form-control-label">Address</label>
        <textarea class="form-control" name="address" id="address">{{old('address')}}</textarea>
         @if ($errors->has('address'))
       <div class="form-control-feedback">Required field can not be blank.</div>
       @endif
      </div>
    </div>

    @if($website != null)
    <div id="websiteBox" class="col-lg-4 col-md-4 d-none">
        <div class="form-group">
           <label class="form-control-label">Show on website select website</label>
              <select class="select2 form-control" data-placeholder="Select Bank" id="website" name="website">
               <option value="">Select website</option>
                 @foreach($website as $value)
               <option value="{{ Crypt::encrypt($value->id) }}">{{$value->name}}</option>
               @endforeach
             </select>
         </div>
       </div>
 @endif
  </div>


  <div class="row">
   <div class="col-md-4">
     <div id="user" class="form-group {{ $errors->has('username') ? 'has-danger' : '' }} ">
       <label class="form-control-label">User Name</label>
       <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}"/>
       @if ($errors->has('username'))
       <div class="form-control-feedback">Required field can not be blank.</div>
       @endif

     </div>
   </div>
   <div class="col-md-4">
     <div  class="form-group {{ $errors->has('password') ? 'has-danger' : '' }} ">
       <label class="form-control-label">Password</label>
       <input type="password" name="password" id="password" class="form-control" value="{{ old('password') }}"/>
       @if ($errors->has('password'))
       <div class="form-control-feedback">Required field can not be blank.</div>
       @endif

     </div>
   </div>

   <div class="col-md-4">
     <label for="image" class="form-control-label">Image</label>
     <a href="#">
       <img id="simg" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
     </a>
     <div class="form-group {{ $errors->has('image') ? 'has-danger' : '' }} m-t-10">
       <label for="image" class="custom-file">
         <input type="file" name="image" id="image" class="custom-file-input" multiple>
         <span class="custom-file-control"></span>
       </label>
       @if ($errors->has('image'))
       <div class="form-control-feedback">{{ $errors->first('image') }}</div>
       @endif
     </div>

   </div>

 </div>
 <div class="row">
   <div class="col-md-12">
    <h5>Add Additional Charges
      <i href="javascript:void(0)" onclick="return clone_field()" class=" pull-right icofont icofont-plus f-right text-success" title="Add Additional Charges" ></i>
    </h5>
    <hr/>
    <div id="inputfieldClone">
    </div>
   </div>
 </div>



 <div class="button-group ">
  <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right"  >
    <i class="icofont icofont-plus"> </i>
    Create Service Provider
  </button>
</div>
</form>


</div>
</div>





</section>

<!-- modals -->
<div class="modal fade modal-flex" id="category-modal" tabindex="-1" role="dialog">
 <div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">Add Category</h4>
  </div>
  <div class="modal-body">
   <div class="row">
     <div class="col-md-12">
      <div class="form-group">
        <label class="form-control-label">Category Name:</label>
        <input type="text"  name="catmodal" id="catmodal" class="form-control" />
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="addcat()">Add Category</button>
</div>
</div>
</div>
</div>

<div class="modal fade modal-flex" id="per-modal" tabindex="-1" role="dialog">
 <div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">Add Percentage</h4>
  </div>
  <div class="modal-body">
   <div class="row">
     <div class="col-md-12">
      <div class="form-group">
        <label class="form-control-label">Enter Percentage:</label>
        <input type="Number"  name="permodal" id="permodal" class="form-control" min="" />
      </div>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" id="btn_per" class="btn btn-success waves-effect waves-light" onClick="addper()">Add Percentage</button>
</div>
</div>
</div>
</div>


<div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
 <div class="modal-dialog modal-md" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">Update Modal</h4>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <div class="form-group">
          <label class="form-control-label">Branch</label>

          <select name="branchmodal" id="branchmodal" data-placeholder="Select Branch" class="form-control select2"  >
            <option value="">Select Branch</option>
            @if($getbranch)
            @foreach($getbranch as $value)

            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
            @endforeach
            @endif
          </select>
          <div class="form-control-feedback"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 col-md-6">
        <label class="form-control-label">Area Name:</label>

        <input type="text" name="areanamemodal" id="areanamemodal" class="form-control"/>

        <input type="hidden" name="chargesid" id="chargesid" class="form-control"/>

      </div>
      <div class="col-lg-6 col-md-6">
        <label class="form-control-label">Delivery Charges:</label>

        <input type="text" name="chargesmodal" id="chargesmodal" class="form-control"/>

      </div>

    </div>

  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-info waves-effect waves-light" onClick="update()"><i class="icofont icofont-ui-edit"></i>&nbsp; Update</button>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

  $("#mobnumb").tagsinput({
   maxTags: 10
 });

 $("#category").on('change',function(){
    if($(this).val() == 4){
        if($('#websiteBox').hasClass('d-none')){
            $('#websiteBox').removeClass('d-none');
        }
    }else{
        if(!$('#websiteBox').hasClass('d-none')){
            $('#websiteBox').addClass('d-none');
        }
    }
 })


  $("#btn_category").on('click',function(){
    $('#catmodal').val('');
    $("#category-modal").modal("show");
  });

  $("#btn_percent").on('click',function(){
    $('#permodal').val('');
    $("#per-modal").modal("show");
  });

  $("#image").change(function() {
    readURL(this,'simg');
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


  $("#username").on('change',function(){
    $.ajax({
      url: "{{url('/chk-user')}}",
      method: 'POST',
      data:{_token:"{{ csrf_token() }}",username:$('#username').val()},
      success:function(resp){
        console.log(resp)
        if(resp == 1){

          $('#user').addClass('has-danger');
          swal({
            title: "Already exsist",
            text: "Username Already exsist!",
            type: "warning"
          });
          $('#username').val('');

        }else{
          $('#user').removeClass('has-danger');
          $('#user').addClass('has-success');
        }

      }
    });
    $("#username").focus();
  });


  function addper(){
    if ($('#permodal').val() == "") {
      swal({
        title: "Error Message",
        text: "Percentage Can not left blank!",
        type: "error"
      });
    }
    else{

     $.ajax({
      url: "{{url('/store-percentage')}}",
      type: 'POST',
      data:{_token:"{{ csrf_token() }}",
      dataType:"json",
      percentage:$('#permodal').val(),
    },
    success:function(resp){
      if(resp != 0){
       swal({
        title: "Operation Performed",
        text: "Percentage Created Successfully!",
        type: "success"});

       $("#per-modal").modal("hide");

       $("#percentage").empty();
       for(var count=0; count < resp.length; count++){
        $("#percentage").append("<option value=''>Select Percentage</option>");
        $("#percentage").append(
          "<option value='"+resp[count].percentage_id+"'>"+resp[count].percentage+" %</option>");
      }
    }
    else{
      swal({
        title: "Already exsit",
        text: "Particular Percentage Already exsit!",
        type: "warning"
      });
    }

  }

});
   }
 }

 function addcat(){
  if ($('#catmodal').val() == "") {
    swal({
      title: "Error Message",
      text: "Category Can not left blank!",
      type: "error"
    });
  }
  else{

   $.ajax({
    url: "{{url('/store-category')}}",
    type: 'POST',
    data:{_token:"{{ csrf_token() }}",
    dataType:"json",
    category:$('#catmodal').val(),
  },
  success:function(resp){
    if(resp != 0){
     swal({
      title: "Operation Performed",
      text: "Category Created Successfully!",
      type: "success"});

     $("#category-modal").modal("hide");

     $("#category").empty();
     for(var count=0; count < resp.length; count++){
      $("#category").append("<option value=''>Select Category</option>");
      $("#category").append(
        "<option value='"+resp[count].category_id+"'>"+resp[count].category+"</option>");
    }
  }
  else{
    swal({
      title: "Already exsit",
      text: "Particular Category Already exsit!",
      type: "warning"
    });
  }

}

});
 }
}

$('#tblservice').DataTable({

  bLengthChange: true,
  displayLength: 10,
  info: false,
  language: {
    search:'',
    searchPlaceholder: 'Search Service Provider',
    lengthMenu: '<span></span> _MENU_'

  }

});

function submit(){

  if ($('#branch').val() == "") {
    swal({
      title: "Error Message!",
      text: "Please Select Branch!",
      type: "error"});
  }
  else if ($('#providername').val() == "") {
    swal({
      title: "Error Message!",
      text: "Please Enter Provider Name!",
      type: "error"});
  }
  else if ($('#category').val() == "") {
    swal({
      title: "Error Message!",
      text: "Please Select Category!",
      type: "error"});
  }
  else if ($('#person').val() == "") {
    swal({
      title: "Error Message!",
      text: "Please Enter Person Name!",
      type: "error"});
  }
  else if ($('#contact').val() == "") {
    swal({
      title: "Error Message!",
      text: "Please Enter Contact Number!",
      type: "error"});
  }
  else if ($('#prebal').val() == 0) {
    swal({
      title: "Error Message!",
      text: "Please enter amount or left blank!",
      type: "error"});
  }

  else{
   $.ajax({
    url: "{{url('/insert-serviceprovider')}}",
    type:"POST",
    data:{_token:"{{ csrf_token() }}",
    branch:$('#branch').val(),
    providername:$('#providername').val(),
    category:$('#category').val(),
    person:$('#person').val(),
    contact:$('#contact').val(),
    nic:$('#cnic').val(),
    address:$('#address').val(),
    percentage:$('#percentage').val(),
    prebalance:$('#prebal').val(),
    username : $('#username').val(),
    password : $('#password').val()
  },
  dataType:"json",
  success:function(resp){
    console.log(resp)
    if (resp != 0) {
     swal({
      title: "Operation Performed",
      text: "Service Provider Added Successfully!",
      type: "success"},
      function(isConfirm){
        if(isConfirm){
         window.location = "{{url('/service-provider')}}";
       }
     });
   }
   else{
    swal({
      title: "ALready Exsist!",
      text: "Service Provider ALready Exsist!",
      type: "error"});
  }

}
});
 }
}









function edit(id,branchid,name,charges){
  $('#update-modal').modal('show');
  $('#areanamemodal').val(name);
  $('#chargesmodal').val(charges);
  $('#chargesid').val(id);
  $('#branchmodal').val(branchid).change();

}


function update(){

 $.ajax({
  url: "{{url('/update-charges')}}",
  type:"POST",
  data:{_token:"{{ csrf_token() }}",
  branch:$('#branchmodal').val(),
  areaname:$('#areanamemodal').val(),
  charges:$('#chargesmodal').val(),
  chargesid:$('#chargesid').val(),
},
dataType:"json",
success:function(resp){
 if (resp != 0) {
   swal({
     title: "Operation Performed",
     text: "Delivery Charges Updated Successfully!",
     type: "success"},
     function(isConfirm){
      if(isConfirm){
       window.location = "{{url('/delivery-charges')}}";
     }
   });
 }
 else{
  swal({
    title: "ALready Exsist!",
    text: "Delivery Charges of this Area ALready Exsist!",
    type: "error"});
}
}
});
}








$('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
    url: "{{url('/inacive-delivery-charges')}}",
    type: 'GET',
    dataType:"json",
    data:{_token:"{{ csrf_token() }}",
  },
  success:function(result){
    if(result){
     $("#tblcharges tbody").empty();
     for(var count =0;count < result.length; count++){

      $("#tblcharges tbody").append(
        "<tr>" +
        "<td>"+result[count].branch_name+"</td>" +
        "<td>"+result[count].area_name+"</td>" +
        "<td>"+result[count].charges+"</td>" +
        "<td>"+result[count].status_name+"</td>" +
        "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
        "</tr>"
        );
    }

  }
}
});
 }
 else{
   window.location="{{ url('/delivery-charges') }}";
 }
});

function reactive(id){
  swal({
    title: "Are you sure?",
    text: "You want to Re-Active Delivery Charges!",
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "yes plx!",
    cancelButtonText: "cancel plx!",
    closeOnConfirm: false,
    closeOnCancel: false
  },
  function(isConfirm){
    if(isConfirm){
     $.ajax({
      url: "{{url('/reactive-charges')}}",
      type: 'PUT',
      data:{_token:"{{ csrf_token() }}",
      chargesid:id,
    },
    success:function(resp){
      if(resp == 1){
       swal({
        title: "Re-Active",
        text: "Delivery Charges Re-Active Successfully!",
        type: "success"
      },function(isConfirm){
       if(isConfirm){
        window.location="{{ url('/delivery-charges') }}";
      }
    });
     }
   }

 });

   }else {
    swal("Cancelled", "Operation Cancelled:)", "error");
  }
});
}
function clone_field(){
    $('#inputClone').clone().appendTo("#inputfieldClone");
  }

  $(document).on("click",".remove_row",function(){
    $(this).closest('#inputClone').remove();
  });

  $("#providername").blur(function(){
	  console.log($(this).val())

	   $.ajax({
		  url: "{{url('/chk-serviceprovider-name')}}",
		  type: 'POST',
		  data:{_token:"{{ csrf_token() }}",
			providername:$(this).val(),
			branch:$("#branch").val(),
		  },
		  success:function(resp){
				console.log(resp)
		  	 if(resp[0].counts == 1){
				 swal({
						title: "Already Exists",
						text: "Service Provider Name already exists in this branch. Please try another one !",
						type: "error"
					  },function(isConfirm){
					   if(isConfirm){
							// window.location="{{ url('/delivery-charges') }}";
							$("#providername").val('');
							$("#providername").focus();
						}
				});

			}
		}

		});
  });

  $("#cnic").blur(function(){
	  // let input = document.querySelector('#cnic')

	  let value = $(this).val();
	  let numbers = [Number(value.substr(0, 5)), Number(value.substr(6, 7)), Number(value.substr(14, 1))]
	  numbers = numbers.map(value => {if(isNaN(value)) return 'NaN'; else return value})
	  let hifens = [value.indexOf('-'), value.lastIndexOf('-')]
	  if(numbers.indexOf('NaN') == -1 && hifens[0] == 5 && hifens[1] == 13) {
		console.log('Valid!')
		$("#cnicdiv").removeClass( "has-danger" ).addClass("has-success");
	  } else {
		  $("#cnic").val("");
		  $("#cnicdiv").addClass("has-danger").removeClass('has-success');
		  $("#cnicerror").html("Format Not Matched");
		console.log('Invalid!')
	  }
  });

  $("#paymenttype").change(function(){
	  if($("#paymenttype").val() == 3){
		$("#paymentValue").val(0);
		$("#paymentValue").prop('readonly', true);
	  }else{
		 $("#paymentValue").prop('readonly', false);
	  }
  })
</script>

@endsection


