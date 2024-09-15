@extends('layouts.master-layout')
@section('title','Edit Testimonial')
@section('breadcrumtitle','Edit Testimonial')
@section('navwebsite','active')
@section('content')
<section class="panels-wells p-t-3">
<div class="card">
	<div class="card-header">
		<h5 class="card-header-text">Edit Testimonial</h5>
	</div>      
    <div class="card-block">
    <form method="POST" action="{{route('testimonials.update',$testimonial->id)}}" enctype="multipart/form-data">
		@csrf  
		@method("PUT")	
				<div class="form-group">
					<label class="form-control-label">Website</label>
					@php $oldwebsite = old('website_id') ? old('website_id') : $testimonial->website_id @endphp
					<select class="form-control" name="website_id" id="website_id">
						<option>Select</option>
                        @foreach($websites as $value)
						<option {{ $oldwebsite == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
					</select>
					@error('website_id')
						<span class="form-control-feedback text-danger">Field is required please select it</span>
					@enderror
				</div>

			

                <div class="form-group">
                    <label class="control-label">Customer Name</label>
                    <input name="customer_name" type="text" class="form-control" placeholder="Customer Name" value="{{ old('customer_name') ? old('customer_name') : $testimonial->customer_name }}" />
                    @error('customer_name')
                     <span class="form-control-feedback text-dange">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="control-label">Rating</label>
                    <input name="rating" type="text" class="form-control" placeholder="Rating" value="{{ old('rating') ? old('rating') : $testimonial->rating }}"/>
					@error('rating')
                     <span class="form-control-feedback text-dange">{{ $message }}</span>
                    @enderror               
				</div>

                <div class="form-group">
                    <label class="control-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="content" id="content" placeholder="Content" rows="5"></textarea>
                    @error('rating')
                     <span class="form-control-feedback text-danger">{{ $message }}</span>
                    @enderror                
                </div>         

				<div class="form-group @error('image') 'has-danger' @enderror ">
				<a href="javascript:void(0)">
					@php $image = $testimonial->image != "" ? 'testimonials/'.$testimonial->image : 'no-image.jpg' @endphp
					<img id="preview" src="{{ asset('storage/images/'.$image) }}" class="thumb-img img-fluid width-100" alt="{{ $testimonial->image == '' ? $testimonial->image : 'placeholder.jpg' }}" style="width: 128px;height: 128px;">
				</a>					
					<label for="image" class="form-control-label">Customer Image</label></br>

					<label for="image" class="custom-file">
					<input type="file" name="image" id="image" class="custom-file-input">
					<span class="custom-file-control"></span>
					</label>
					@error('image')
						<div class="form-control-feedback text-danger">{{ $message }}</div>
					@enderror
				</div> 

				
				<a class="btn btn-danger m-r-2" href="{{ route('filterTestimonial',$testimonial->website_id) }}">Cancel</a>	

		       <button type="submit" class="btn btn-md btn-primary waves-effect waves-light f-right" > Update Website </button>       
	</form>
    </div>
	
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">

@if(old('content'))
     $("#content").val('{{ old("content")}}');
@else
$("#content").val('{{ $testimonial->content }}'); 
@endif

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

	$("#image").change(function() {
	  readURL(this,'preview');
	});

</script>
@endsection