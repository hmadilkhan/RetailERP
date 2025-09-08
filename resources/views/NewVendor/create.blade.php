@extends('layouts.master-layout')

@section('title','Create Vendor')

@section('breadcrumtitle','Create Vendor')

@section('navvendor','active')

@section('content')
	<section class="panels-wells">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text"> Create Vendor</h5>
				<h5 class=""><a href="{{ route('vendors.index') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
			</div>
			<div class="card-block">
				<form method="POST" action="{{ route('vendors.store') }}" class="form-horizontal" enctype="multipart/form-data">
				@csrf
					<h5>Vendor Details</h5>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('vdname') ? 'has-danger' : '' }} ">
								<label class="form-control-label">Vendor Name</label>
								<input type="text" name="vdname" id="vdname" placeholder="Vendor Name" class="form-control" value="{{ old('vdname') }}"/>
								@if ($errors->has('vdname'))
									<div class="form-control-feedback">{{ $errors->first('vdname') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Email</label>
								<input type="email" name="vdemail" id="vdemail" placeholder="vendor@email.com" class="form-control" value="{{ old('vdemail') }}"/>
								<div class="form-control-feedback text-danger" id="vdemail_alert"></div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('vdcontact') ? 'has-danger' : '' }} ">
								<label class="form-control-label">Contact Number</label>
								<input type="text" name="vdcontact" id="vdcontact" placeholder="03xx-xxxxxxx" class="form-control" value="{{ old('vdcontact') }}"/>
								@if ($errors->has('vdcontact'))
									<div class="form-control-feedback">{{ $errors->first('vdcontact') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
								<label class="form-control-label">Country</label>
								<select name="country" id="country" data-placeholder="Select Country" class="form-control select2">
									<option value="">Select Country</option>
									@if ($country)
										@foreach ($country as $value)
											@if ($value->country_name == 'Pakistan')
												<option selected="selected" value="{{ $value->country_id }}">{{ $value->country_name }}</option>
											@else
												<option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
											@endif
										@endforeach
									@endif
								</select>
								@if ($errors->has('country'))
									<div class="form-control-feedback">Required field can not be blank.</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ $errors->has('city') ? 'has-danger' : '' }}">
								<label class="form-control-label">City</label>
								<select name="city" id="city" data-placeholder="Select City" class="form-control select2">
									<option value="">Select City</option>
									@if ($city)
										@foreach ($city as $value)
											@if ($value->city_name == 'Karachi')
												<option selected="selected" value="{{ $value->city_id }}">{{ $value->city_name }}</option>
											@else
												<option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
											@endif
										@endforeach
									@endif
								</select>
								@if ($errors->has('city'))
									<div class="form-control-feedback">Required field can not be blank.</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Address</label>
								<textarea name="address" id="address" class="form-control" rows="1">{{ old('address') }}</textarea>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Payment Terms (Days)</label>
								<input type="number" min="0" name="paymentdays" id="paymentdays" class="form-control" value="{{ old('paymentdays', 0) }}"/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">NTN</label>
								<input type="text" name="ntn" id="ntn" class="form-control" value="{{ old('ntn') }}"/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">STRN</label>
								<input type="text" name="strn" id="strn" class="form-control" value="{{ old('strn') }}"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Opening Balance</label>
								<input type="number" step="0.01" min="0" name="ob" id="ob" class="form-control" value="{{ old('ob', 0) }}"/>
							</div>
						</div>
						<div class="col-md-4">
							<a href="#">
								<img id="vendor_img_preview" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
							</a>
							<div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
								<label for="vdimg" class="form-control-label">Vendor Image</label>
								<br/>
								<label for="vdimg" class="custom-file">
									<input type="file" name="vdimg" id="vdimg" class="custom-file-input">
									<span class="custom-file-control"></span>
								</label>
								@if ($errors->has('vdimg'))
									<div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
								@endif
							</div>
						</div>
					</div>

					<hr>
					<h5>Company Details</h5>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Company Name</label>
								<input type="text" name="cpname" id="cpname" class="form-control" value="{{ old('cpname') }}"/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Company Email</label>
								<input type="email" name="cpemail" id="cpemail" class="form-control" value="{{ old('cpemail') }}"/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-control-label">Company Contact</label>
								<input type="text" name="cpcontact" id="cpcontact" class="form-control" value="{{ old('cpcontact') }}"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<a href="#">
								<img id="company_logo_preview" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
							</a>
							<div class="form-group{{ $errors->has('logo') ? 'has-danger' : '' }} ">
								<label for="logo" class="form-control-label">Company Logo</label>
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
					</div>

					<div class="contact-card-button m-t-20 f-right">
						<button type="submit" class="btn btn-md btn-info waves-effect waves-light">
							<i class="icofont icofont-plus m-r-5"> </i>Save Vendor
						</button>
					</div>
				</form>
			</div>
		</div>
	</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	$(".select2").select2();
	$("#vdimg").change(function(){ readURL(this,'vendor_img_preview'); });
	$("#logo").change(function(){ readURL(this,'company_logo_preview'); });
	function readURL(input,id){
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e){ $('#'+id).attr('src', e.target.result); }
			reader.readAsDataURL(input.files[0]);
		}
	}
</script>
@endsection 