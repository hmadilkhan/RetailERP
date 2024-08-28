@extends('layouts.master-layout')

@section('title','Edit Driver')

@section('breadcrumtitle','Edit Driver')

@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
	<section class="panels-wells">
		<div class="card">
		<div class="card-header">
			<h5 class="card-header-text"> Edit Driver</h5>
			<h5 class=""><a href="{{ route('driver.list') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
		</div>
		<div class="card-block">
			<form method="POST" action="{{ route('driver.update') }}" class="form-horizontal" enctype="multipart/form-data">
			@csrf
				<input type="hidden" name="prevImage" value="{{$driver->image}}"/>
				<input type="hidden" name="id" value="{{$driver->id}}"/>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Name</label>
							<input type="text" name="name" id="name" placeholder="Driver Name" class="form-control" value="{{$driver->name }}"/>
							 @if ($errors->has('name'))
								<div class="form-control-feedback">{{ $errors->first('name') }}</div>
							@endif
							<div class="form-control-feedback text-danger" id="vdemail_alert"></div>
							
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('mobile') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Mobile Number</label>
							<input type="text" name="mobile" id="mobile" placeholder="Driver Mobile Number" class="form-control" value="{{ $driver->mobile }}"/>
							 @if ($errors->has('mobile'))
								<div class="form-control-feedback">{{ $errors->first('mobile') }}</div>
							@endif
							<div class="form-control-feedback text-danger" id="vdemail_alert"></div>
							
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('license_no') ? 'has-danger' : '' }} ">
							<label class="form-control-label">License Number</label>
							<input type="text" name="license_no" id="license_no" placeholder="Driver License No." class="form-control" value="{{ $driver->license_no }}"/>
							 @if ($errors->has('license_no'))
								<div class="form-control-feedback">{{ $errors->first('license_no') }}</div>
							@endif
							<div class="form-control-feedback text-danger" id="vdemail_alert"></div>
							
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('nic_no') ? 'has-danger' : '' }} ">
							<label class="form-control-label">NIC Number</label>
							<input type="text" name="nic_no" id="nic_no" placeholder="Driver NIC No." class="form-control" value="{{ $driver->nic_no }}"/>
							 @if ($errors->has('nic_no'))
								<div class="form-control-feedback">{{ $errors->first('nic_no') }}</div>
							@endif
							<div class="form-control-feedback text-danger" id="vdemail_alert"></div>
							
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						   <label class="form-control-label">Address</label>
						   <textarea name="address" rows="3" id="address" class="form-control">{{$driver->address}}</textarea>
						   <div class="form-control-feedback"></div>
						</div>
                    </div>
					<div class="col-md-4" >
						<a href="#">
							<img id="vdpimg" src="{{ asset('assets/images/drivers/'.$driver->image) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
						</a>
						<div class="form-group{{ $errors->has('logo') ? 'has-danger' : '' }} ">
							<label for="logo" class="form-control-label">Image</label>
							<br/>
							<label for="logo" class="custom-file">
								<input type="file" name="logo" id="logo" class="custom-file-input">
								<span class="custom-file-control"></span>
							</label>
							@if ($errors->has('logo'))
								<div class="form-control-feedback">{{ $errors->first('logo') }}</div>
							@endif
						</div>
					</div>
					<div class="contact-card-button m-t-20 f-right">
						<button type="submit" class="btn btn-md btn-info waves-effect waves-light">
						  <i class="icofont icofont-plus m-r-5"> </i>Update Driver
						</button>
					</div>  
				</div>
			</form>
		</div>
	</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	$("#logo").change(function() {
		readURL(this,'vdpimg');
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
</script>
@endsection
