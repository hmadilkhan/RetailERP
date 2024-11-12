@extends('layouts.master-layout')
@section('title','Create Website Slider')
@section('breadcrumtitle','Create Website Slider')
@section('navwebsite','active')
@section('content')

<section class="panels-wells">
<div class="card">
	<div class="card-header">
		<h5 class="card-header-text">Create Slider</h5>
	</div>
    <div class="card-block">


    <form id="sliderCreateForm" role="form" class="form-inline" method="POST" action="{{ route('sliderStore') }}" enctype="multipart/form-data">
		@csrf
			<div class="form-group m-r-2">
				<label class="form-control-label">Website</label>
				<select name="website" id="website" data-placeholder="Select" class="form-control select2" >
					<option value="">Select</option>
					@if($websites)
					   @php $oldWebsite = old('website') @endphp
						@foreach($websites as $val)
							<option {{ old('website') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
						@endforeach
					@endif
				</select>
				@error('website')
					<div class="form-control-feedback text-danger">Field is required please select it</div>
				@enderror
			 </div>
				<div class="form-group m-r-2">
					<label class="form-control-label">Inventory Department</label>
					<select name="depart" id="depart" data-placeholder="Select" class="form-control select2" >
						<option>Select</option>
					@if($departments)

					   @php $oldDepart = old('depart') @endphp
						@foreach($departments as $val)
							<option {{ old('depart') == $val->department_id ? 'selected' : '' }} value="{{ $val->department_id }}">{{ $val->department_name }}</option>
						@endforeach
					@endif
					</select>
					@error('depart')
						<div class="form-control-feedback text-danger">Field is required please select it</div>
					@enderror
				</div>




				<div class="form-group @error('slide') 'has-danger' @enderror m-r-2">
					<label for="slide" class="form-control-label">Slide</label></br>

					<label for="slide" class="custom-file">
					<input type="file" name="slide" id="slide" class="custom-file-input">
					<span class="custom-file-control"></span>
					</label>
					@error('slide')
						<div class="form-control-feedback text-danger">{{ $message }}</div>
					@enderror
				</div>

                <button class="btn btn-primary m-l-3 m-t-1" type="submit" id="btn_slider_create">Submit</button>
              </form>


    </div>
  </div>

</section>
@endsection

@section('scriptcode_one')
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

    $('#sliderCreateForm').on('submit', function() {
        $('#btn_slider_create').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
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


	// $("#btn_contact_create").on('click',function(){

	// 	var type   = $("#contactype").val();
	// 	var number = $("#number").val();

    //        if(!type && !number){
    //        	   $("#alert_contact").text("both field is required");
    //        }else{
    //            //formData({id:webid,ctype:type,phone:number});
    //       }
	// })


	// function formData(arrayRow){
    //            // $.ajax({
    //            //          url:"",
    //            //          type:"POST",
    //            //          data:{arrayRow},
    //            //          dataType:'json',
    //            //          async:true,
    //            //          success:function(resp){
    //            //              if(resp.state == 200){
    //            //              	$.each(resp.data,function(i,v){
    //            //                       console.log(i+' : '+v)
    //            //              	})
    //            //              }
    //            //          }
    //            // })

	// }
</script>


@endsection


@section('css_code')
   <link rel="stylesheet" type="text/css" href="{{ asset('storage/css/wizardform.css') }}">
@endsection
