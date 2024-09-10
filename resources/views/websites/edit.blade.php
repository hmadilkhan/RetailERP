@extends('layouts.master-layout')
@section('title','Edit Website')
@section('breadcrumtitle','Edit Website')
@section('navwebsite','active')
@section('content')
<section class="panels-wells">
<div class="card">
	<div class="card-header">
		<h5 class="card-header-text">Edit Website</h5>
	</div>      
    <div class="card-block">
    <form method="POST" action="{{route('website.update',$website->id)}}" enctype="multipart/form-data">
		@csrf  
		@method("PUT")	

            <div class="form-group">
				<label class="form-control-label">Company</label>
                <p>{{ $website->company_name }}</p>
			 </div>
				<div class="form-group">
					<label class="form-control-label">Website Type</label>
					@php $oldWebType = old('type') ? old('type') : $website->type @endphp
					<select class="form-control" name="type" id="type">
						<option>Select</option>
						<option {{ $oldWebType == 'restaurant' ? 'selected' : '' }} value="restaurant">Restaurant</option>
						<option {{ $oldWebType == 'grocery' ? 'selected' : '' }} value="grocery">Grocery</option>
						<option {{ $oldWebType == 'boutique' ? 'selected' : '' }} value="boutique">Boutique</option>
					</select>
					@error('type')
						<div class="form-control-feedback text-danger">Field is required please select it</div>
					@enderror
				</div>

			

                <div class="form-group">
                    <label class="control-label">Website Name</label>
                    <input name="name" type="text" class="form-control" placeholder="Website Name" value="{{ old('name') ? old('name') : $website->name }}" />
                    @error('name')
                     <div class="form-control-feedback text-dange">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="control-label">Domain Name</label>
                    <input name="url" type="text" class="form-control" placeholder="Domain Name" value="{{ old('url') ? old('url') : $website->url }}"/>
                </div>
                <!--<div class="form-group">-->
                <!--    <label class="control-label">TopBar</label>-->
                <!--    <input type="text" class="form-control" name="topbar" placeholder="TopBar Message" value="{{-- old('topbar') ? old('topbar') : $website->topbar --}}" />-->
                <!--</div>-->

                <div class="form-group">
                    <label class="control-label">UAN Number</label>
                    <input name="uan_number" type="text" class="form-control" placeholder="UAN Number" value="{{ old('uan_number') ? old('uan_number') : $website->uan_number }}" />
                </div>
                <div class="form-group">
                    <label class="control-label">WhatsApp</label>
                    <input type="text" class="form-control" name="whatsapp" placeholder="WhatsApp Number" value="{{ old('whatsapp') ? old('whatsapp') : $website->whatsapp }}"/>
                </div>                

				<div class="form-group @error('logo') 'has-danger' @enderror ">
				<a href="javascript:void(0)">
					@php $logo = $website->logo != "" ? 'website/'.$website->logo : 'placeholder.jpg' @endphp
					<img id="preview" src="{{ asset('storage/images/'.$logo) }}" class="thumb-img img-fluid width-100" alt="{{ $website->logo == '' ? $website->logo : 'placeholder.jpg' }}" style="width: 128px;height: 128px;">
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
				<a href="javascript:void(0)">
					@php $favicon = $website->favicon != "" ? 'website/'.$website->favicon : 'placeholder.jpg' @endphp
					<img id="fpreview" src="{{ asset('storage/images/'.$favicon) }}" class="thumb-img img-fluid width-100" alt="{{ $website->favicon == '' ? $website->favicon : 'placeholder.jpg' }}" style="width: 128px;height: 128px;">
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

		       <button type="submit" class="btn btn-md btn-primary waves-effect waves-light f-right" > Update Website </button>       
	</form>
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

	$("#logo").change(function() {
	  readURL(this,'preview');
	});

	$("#favicon").change(function() {
	  readURL(this,'fpreview');
	});
</script>
@endsection