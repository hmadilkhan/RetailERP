@extends('layouts.master-layout')
@section('title','Create Website')
@section('breadcrumtitle','Create Website')
@section('navcompany','active')
@section('content')

<section class="panels-wells p-t-3">
<div class="card">
	<div class="card-header">
		<h5 class="card-header-text">Add New Website</h5>
	</div>
    <div class="card-block">


    <form role="form" method="POST" action="{{ route('website.store') }}" enctype="multipart/form-data">
		@csrf

			<div class="form-group">
				<label class="form-control-label">Company</label>
				<select name="company_id" id="company_id" data-placeholder="Select Company" class="form-control select2" >
					<option value="">Select Company</option>
					@if($companies)
					   @php $oldCompany = old('company_id');
						@foreach($companies as $company)
							<option {{ old('company_id') == $company->company_id ? 'selected' : '' }} value="{{ $company->company_id }}">{{ $company->name }}</option>
						@endforeach
					@endif
				</select>
				@error('company_id')
					<div class="form-control-feedback text-danger">Field is required please select it</div>
				@enderror
			 </div>
				<div class="form-group">
					<label class="form-control-label">Website Type</label>
					<select class="form-control" name="type" id="type">
						<option>Select</option>
						<option {{ old('type') == 'restaurant' ? 'selected' : '' }} value="restaurant">Restaurant</option>
						<option {{ old('type') == 'grocery' ? 'selected' : '' }} value="grocery">Grocery</option>
						<option {{ old('type') == 'boutique' ? 'selected' : '' }} value="boutique">Boutique</option>
					</select>
					@error('type')
						<div class="form-control-feedback text-danger">Field is required please select it</div>
					@enderror
				</div>

                <div class="form-group">
                    <label class="control-label">Website Name</label>
                    <input name="name" type="text" class="form-control" placeholder="Website Name" value="{{ old('name') }}" />
                    @error('name')
                     <div class="form-control-feedback text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="control-label">Domain Name</label>
                    <input name="url" type="text" class="form-control" placeholder="Domain Name" value="{{ old('url') }}"/>
                </div>
                <!--<div class="form-group">-->
                <!--    <label class="control-label">TopBar</label>-->
                <!--    <input type="text" class="form-control" name="topbar" placeholder="TopBar Message" value="{{-- old('topbar') --}}" />-->
                <!--</div>-->

                <div class="form-group">
                    <label class="control-label">UAN Number</label>
                    <input name="uan_number" type="text" class="form-control" placeholder="UAN Number" value="{{ old('uan_number') }}" />
                </div>
                <div class="form-group">
                    <label class="control-label">WhatsApp</label>
                    <input type="text" class="form-control" name="whatsapp" placeholder="WhatsApp Number" value="{{ old('whatsapp') }}"/>
                </div>

				<div class="form-group @error('logo') 'has-danger' @enderror ">
				<a href="#">
					<img id="preview" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
				</a>
					<label for="logo" class="form-control-label">Logo</label></br>

					<label for="logo" class="custom-file">
					<input type="file" name="logo" id="logo" class="custom-file-input">
					<span class="custom-file-control"></span>
					</label>
					@error('logo')
						<div class="form-control-feedback text-danger">{{ $message }}</div>
					@enderror
				</div>

				<div class="form-group @error('favicon') 'has-danger' @enderror ">
				<a href="#">
					<img id="fpreview" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
				</a>
					<label for="favicon" class="form-control-label">Favicon</label></br>

					<label for="favicon" class="custom-file">
					<input type="file" name="favicon" id="favicon" class="custom-file-input">
					<span class="custom-file-control"></span>
					</label>
					@error('favicon')
						<div class="form-control-feedback text-danger">{{ $message }}</div>
					@enderror
				</div>
				<a class="btn btn-danger m-r-2" href="{{ route('website.index') }}">Cancel</a>
                <button class="btn btn-primary position-right" type="submit">Submit</button>
              </form>


    </div>
  </div>

</section>
@endsection

@section('scriptcode_three')



<script type="text/javascript">

	$(".select2").select2();
	var count = 0;
	function readURL(input,id) {
	  if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
		  $('#'+id).attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	  }
	}

	$("#logo").change(function() {
	  readURL(this,'preview');
	});


	$("#favicon").change(function() {
	  readURL(this,'fpreview');
	});

	// function clone_field(){
	// 	console.log(count)
	// 	if(count <3){
	// 		count++;
	// 		$('#inputClone').clone().appendTo("#inputfieldClone");
	// 	}else{
	// 		alert("You can only select three at a time. "+count);
	// 	}
	// }

	// function social_clone_field(){
	// 	console.log(count)
	// 	if(count <3){
	// 		count++;
	// 		$('#socialInputClone').clone().appendTo("#socialinputfieldClone");
	// 	}else{
	// 		alert("You can only select three at a time. "+count);
	// 	}
	// }

	// $(document).on("click",".remove_row",function(){
	// 	count= count - 1;
	// 	$(this).closest('#inputClone').remove();
	// });
	// $(document).on("click",".social_remove_row",function(){
	// 	count= count - 1;
	// 	$(this).closest('#socialinputfieldClone').remove();
	// });


	$("#btn_contact_create").on('click',function(){

		var type   = $("#contactype").val();
		var number = $("#number").val();

           if(!type && !number){
           	   $("#alert_contact").text("both field is required");
           }else{
               //formData({id:webid,ctype:type,phone:number});
          }
	})


	function formData(arrayRow){
               // $.ajax({
               //          url:"",
               //          type:"POST",
               //          data:{arrayRow},
               //          dataType:'json',
               //          async:true,
               //          success:function(resp){
               //              if(resp.state == 200){
               //              	$.each(resp.data,function(i,v){
               //                       console.log(i+' : '+v)
               //              	})
               //              }
               //          }
               // })

	}
</script>


@endsection


@section('css_code')
   <link rel="stylesheet" type="text/css" href="{{ asset('storage/css/wizardform.css') }}">

@endsection
