@extends('layouts.master-layout')
@section('title','Create Website Testimonial')
@section('breadcrumtitle','Create Website Testimonial')
@section('navwebsite','active')
@section('content')

<section class="panels-wells p-t-3">
<div class="card ">
	<div class="card-header">
		<h5 class="card-header-text">Create Website Testimonial</h5>
	</div>      
    <div class="card-block">

   <div class="col-md-6">
    <form role="form" method="POST" action="{{ route('testimonials.store') }}" enctype="multipart/form-data">
		@csrf 

			<div class="form-group">
				<label class="form-control-label">Website</label>
				<select name="website_id" id="website_id" data-placeholder="Select Company" class="form-control select2" >
					<option value="">Select Company</option>
					@if($websites)
					   @php $oldwebsite = old('website_id') @endphp
						@foreach($websites as $website)
							<option {{ $oldwebsite == $website->id ? 'selected' : '' }} value="{{ $website->id }}">{{ $website->name }}</option>
						@endforeach
					@endif
				</select>
				@error('website_id')
					<span class="form-control-feedback text-danger">Field is required please select it</span>
				@enderror
			 </div>
                <div class="form-group">
                    <label class="control-label">Customer Name</label>
                    <input name="customer_name" type="text" class="form-control" placeholder="Customer Name" value="{{ old('customer_name') }}" />
                    @error('customer_name')
                     <span class="form-control-feedback text-danger">{{ $message }}</span>
                    @enderror 
                </div>

                <div class="form-group">
                    <label class="control-label">Rating</label>
                    <input type="text" class="form-control" name="rating" placeholder="Rating" value="{{ old('rating') }}"/>
                    @error('rating')
                     <span class="form-control-feedback text-danger">{{ $message }}</span>
                    @enderror                
                </div>                

                <div class="form-group">
                    <label class="control-label">Content</label>
                    <textarea class="form-control" name="content" id="content" placeholder="Content" rows="5"></textarea>
                    @error('rating')
                     <span class="form-control-feedback text-danger">{{ $message }}</span>
                    @enderror                
                </div> 

				<div class="form-group @error('image') 'has-danger' @enderror ">
				<a href="#">
					<img id="preview" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
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


				<a class="btn btn-danger m-r-2" href="{{ route('testimonials.index') }}">Cancel</a>			               
                <button class="btn btn-primary position-right" type="submit">Submit</button>
              </form> 
        </div>   
    </div>
  </div>  
	
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">

    @if(old('content'))
     $("#content").val('{{ old("content")}}');
    @endif

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

	$("#image").change(function() {
	  readURL(this,'preview');
	});
	
</script>

 
@endsection


@section('css_code')
   <link rel="stylesheet" type="text/css" href="{{ asset('storage/css/wizardform.css') }}">
   
@endsection