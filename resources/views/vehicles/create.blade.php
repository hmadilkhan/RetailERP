@extends('layouts.master-layout')

@section('title','Create Driver')

@section('breadcrumtitle','Create Driver')

@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
	<section class="panels-wells">
		<div class="card">
		<div class="card-header">
			<h5 class="card-header-text"> Create Vehicle</h5>
			<h5 class=""><a href="{{ route('vehicle.list') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
		</div>
		<div class="card-block">
			<form method="POST" action="{{ route('vehicle.store') }}" class="form-horizontal" enctype="multipart/form-data">
			@csrf
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('model_name') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Model Name</label>
							<input type="text" name="model_name" id="model_name" placeholder="Vehicle Model Name" class="form-control" value="{{ old('model_name') }}"/>
							 @if ($errors->has('model_name'))
								<div class="form-control-feedback">{{ $errors->first('model_name') }}</div>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('model_no') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Model Number</label>
							<input type="text" name="model_no" id="model_no" placeholder="Vehicle Model No." class="form-control" value="{{ old('model_no') }}"/>
							 @if ($errors->has('model_no'))
								<div class="form-control-feedback">{{ $errors->first('model_no') }}</div>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ $errors->has('mobile') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Number</label>
							<input type="text" name="number" id="number" placeholder="Vehicle Number" class="form-control" value="{{ old('number') }}"/>
							 @if ($errors->has('number'))
								<div class="form-control-feedback">{{ $errors->first('number') }}</div>
							@endif
						</div>
					</div>
					

					<div class="contact-card-button m-t-20 f-right">
						<button type="submit" class="btn btn-md btn-info waves-effect waves-light">
						  <i class="icofont icofont-plus m-r-5"> </i>Save Vehicle
						</button>
					</div>  
				</div>
			</form>
		</div>
	</section>
@endsection