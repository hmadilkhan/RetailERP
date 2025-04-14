@extends('layouts.master-layout')

@section('title','Delivery Service Provider')

@section('breadcrumtitle','Delivery Service Provider').

@section('navdelivery','active')

@section('navservices','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Edit Service Provider</h5>
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
            <form method="post" action="{{url('update-serviceprovider')}}" enctype="multipart/form-data">
                @csrf
                @method('post')
                <input type="hidden" name="prev_image" value="{{$details[0]->image}}">
                <input type="hidden" name="proid" id="proid" class="form-control" value="{{$details[0]->id}}"/>
           <div class="row">
             <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Branch</label>

                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2"  >
                    <option value="">Select Branch</option>
                    @if($getbranch)
                      @foreach($getbranch as $value)
                <option {{$value->branch_name == $details[0]->branch_name ? 'selected="selected"' : '' }}
             value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

          <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Service Provider Name:</label>
                   <input type="text" name="providername" id="providername" class="form-control" value="{{$details[0]->provider_name}}"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

                     <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Service Category</label>
                 <i id="btn_category" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Category" ></i>
                <select name="category" id="category" data-placeholder="Select Category" class="form-control select2"  >
                    <option value="">Select Category</option>
                    @if($getcategory)
                      @foreach($getcategory as $value)
                       <option {{$value->category == $details[0]->category ? 'selected="selected"' : '' }}
             value="{{ $value->category_id }}">{{ $value->category}}</option>

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
                <label class="form-control-label">Contact Person</label>
             <input type="text" name="person" id="person" class="form-control" value="{{$details[0]->person}}"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

             <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Contact Number</label>
             <input type="text" name="contact" id="contact" class="form-control" value="{{$details[0]->contact}}"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

          <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">CNIC | NTN No:</label>
                   <input type="text" name="cnic" id="cnic" class="form-control" value="{{$details[0]->cnic_ntn}}"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

            </div>
            <div class="row">


			<div class="col-lg-4 col-md-4">
				<div class="form-group {{ $errors->has('percentage') ? 'has-danger' : '' }}">
					<label class="form-control-label">Payment Type</label>
					<select name="paymenttype" id="paymenttype" data-placeholder="Select Payment Type" class="form-control select2"  >
					  <option value="">Select Payment Type</option>
					  @if($providersPaymentType)
					  @foreach($providersPaymentType as $value)
					  <option {{($details[0]->type ==  $value->type ? "selected" : ""  )}} value="{{ $value->id }}">{{ $value->type}} %</option>
					  @endforeach
					  @endif
					</select>
					 @if ($errors->has('paymenttype'))
						<div class="form-control-feedback">Required field can not be blank.</div>
					 @endif
					<div class="form-control-feedback text-info">
						<label class="form-control-label">Payment Value </label>
						<input type="text" name="paymentValue" id="paymentValue" class="form-control" placeholder="In percentage% or amount" value="{{$details[0]->payment_value }}" />
					</div>
				</div>
	 	    </div>

                     <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Address</label>
 <textarea class="form-control" name="address" id="address">{{$details[0]->address}}</textarea>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

              @if($website != null && $details[0]->category == 6)
              <div id="websiteBox" class="row d-none">
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                     <label class="form-control-label">Show on website select website</label>
                        <select class="select2 form-control" data-placeholder="Select Website" id="website" name="website">
                         <option value="">Select website</option>
                           @foreach($website as $value)
                         <option {{ $details[0]->website_id == $value->id ? 'selected' : '' }} value="{{ Crypt::encrypt($value->id) }}">{{$value->name}}</option>
                         @endforeach
                       </select>
                   </div>
                 </div>
              </div>
           @endif

                <div class="col-md-4">
                    <label for="image" class="form-control-label">Image</label>
                    <a href="#">
                        <img id="simg" src="{{ asset('storage/images/service-provider/'.(!empty($details[0]->image) ? $details[0]->image : 'placeholder.jpg').'') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
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
              @foreach($getAdditionalCharges as $val)
              <div class="col-lg-12">
                <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                    <label class="form-control-label">Charge Name:</label>
                    <input type="text" disabled="" value="{{$val->chargeName}}" name="chargeName[]" id="chargeName" required="" class="form-control"/>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                    <label class="form-control-label">Charge Value:</label>
                    <input type="text" disabled="" value="{{$val->chargeValue}}" name="chargeValue[]" onkeypress="return isNumberKey(event)" id="chargeValue" required="" class="form-control"/>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3">
                <div class="form-group">
                  <label class="form-control-label">Type:</label>
                  <select class="form-control" name="type[]" disabled="">
                    <option>select</option>
                    <option {{$val->type == 'commission' ? 'selected':''}} value="commission">Commission</option>
                    <option {{$val->type == 'bank' ? 'selected':''}} value="bank">Bank</option>
                    <option {{$val->type == 'other' ? 'selected':''}} value="other">Other</option>
                  </select>
                </div>
              </div>
                <div class="col-lg-1 col-md-1">
                  <div class="form-group">
                    <label class="m-t-40"></label>
                    <a class="blue-btn2 addset floatleft" onclick="return additionalChargesUpdate('{{$val->chargeName}}','{{$val->chargeValue}}','{{$val->id}}','{{$val->type}}')" href="javascript:void(0)" style="text-align: center;">
                      &nbsp;Edit
                    </a>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
              @endforeach
              <div id="inputfieldClone">
              </div>
            </div>
          </div>



          <div class="button-group ">
                      <button type="submit"  class="btn btn-md btn-primary waves-effect waves-light f-right" >
                        <i class="icofont icofont-ui-edit"> </i>
                        Update Service Provider
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
{{-- Model --}}
<div class="modal fade modal-flex" id="additional-charges-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">EDIT ADDITIONAL CHARGES</h4>
            </div>
            <form action="" method="post" id="editAdditionalCharge">
                {{csrf_field()}}
                <input class="form-control" type="hidden" name="addition_charges_id" id="model-additional-charge-id" value="" />
                <div class="row m-r-0 m-l-0 m-t-10">
                    <div class="messages"></div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Charge Name</label>
                            <input class="form-control" type="text" name="chargeName" id="model-charge-name" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Charge Value</label>
                            <input class="form-control" onkeypress="return isNumberKey(event)" type="number" name="chargeValue" id="model-charge-value" />
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <div class="form-group">
                        <label class="form-control-label">Type:</label>
                        <select id="model-type" class="form-control" name="type[]">
                          <option>select</option>
                          <option value="commission">Commission</option>
                          <option value="bank">Bank</option>
                          <option value="other">Other</option>
                        </select>
                      </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-success waves-effect waves-light f-right">
                        Update
                    </button>
                </div>
            </form>
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

function update(){

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

else{
   $.ajax({
                    url: "{{url('/update-serviceprovider')}}",
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
                    providerid:$('#proid').val(),
          },
                    dataType:"json",
                    success:function(resp){
                    if (resp != 0) {
                 swal({
                      title: "Operation Performed",
                      text: "Service Provider Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                         window.location = "{{url('/service-provider')}}";
                      }
                       });
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

     function additionalChargesUpdate(name,val,id,type) {
        $('#model-additional-charge-id').val(id);
        $('#model-charge-name').val(name);
        $('#model-charge-value').val(val);
         $('#model-type option[value='+type+']').attr('selected','selected')
        $('#additional-charges-modal').modal('show');
    }

    // Edit Manual Payment
    $('#editAdditionalCharge').on('submit', function(e) {
        e.preventDefault();
        var $form = $('#editAdditionalCharge');
        // check if the input is valid using a 'valid' property
        var formStatus = $('#editAdditionalCharge')[0].checkValidity();
        $.ajax({
            async: false,
            type: "POST",
            url: "<?php echo URL::to('edit-additional-charge') ?>",
            data: $form.serialize(),
            success: function(response) {
                // var obj = $.parseJSON(response);
                var obj = response;
                if (obj.status == 'true') {
                    $('.messages').html('<div class="alert alert-success p-r-20 p-l-10" style="background-color:#dff0d8;color:#3c763d;border-color:d0e9c6" >' + obj.message + '</div>').fadeIn().delay(3000).fadeOut();
                    window.setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    var message = response.message;
                    // $.each(response.message, function(key, value) {
                    //     message += value + '<br>';
                    // });
                    $('.messages').html('<div class="alert alert-danger">' + message + '</div>').fadeIn().delay(3000).fadeOut();
                }
            }

        });
        return false; //mark-2

    });

 </script>

@endsection


