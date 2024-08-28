@extends('layouts.master-layout')

@section('title','Create Public Holidays')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Public Holidays</h5>
			 <button onclick="openPage()" class="f-right btn btn-success white--text"><a>Back to list</a></button>
		</div>
	</div>
</section>
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Create Public Holidays</h5>
		</div>
		<div class="card-block">
			<form method="post" id="upload_form" action="{{url('mark-public-holiday')}}" enctype="multipart/form-data">
			@csrf
			<div class="row">
				<div class="col-lg-4 col-md-4">
					<div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }} ">
						<label class="form-control-label">Select Branch</label>
						<select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
							<option value="">Select Branch</option>
							@foreach($branches as $branch)
								<option value="{{$branch->branch_id}}">{{$branch->branch_name}}</option>
							@endforeach
						</select>
						@if ($errors->has('branch'))
							<div class="form-control-feedback">Required field can not be blank.</div>
						@endif
					</div>
				</div>
				<div class="col-lg-4 col-md-4">
					<div class="form-group {{ $errors->has('department') ? 'has-danger' : '' }} ">
						<label class="form-control-label">Select Department</label>
						<select name="department" id="department" data-placeholder="Select Department" class="form-control select2" >
							<option value="">Select Department</option>
						</select>
						@if ($errors->has('department'))
							<div class="form-control-feedback">Required field can not be blank.</div>
						@endif
					</div>
				</div>
				<div class="col-lg-4 col-md-4">
					<div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }} ">
						<label class="form-control-label">Date</label>
						<input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" onchange="accchk()" />
						@if ($errors->has('date'))
							<div class="form-control-feedback">Required field can not be blank.</div>
						@endif
					</div>
				</div>
				
			</div>
			<div class="row">
				<div class="col-lg-4 col-md-4">
					<div class="form-group {{ $errors->has('reason') ? 'has-danger' : '' }} ">
						<label class="form-control-label">Reason</label>
						<input type="text" name="reason" id="reason" class="form-control" value="{{ old('reason') }}" onchange="accchk()" />
						@if ($errors->has('reason'))
							<div class="form-control-feedback">Required field can not be blank.</div>
						@endif
					</div>
				</div>
			</div>
			<div class="row ">
				<div class="col-lg-12 col-sm-12">
					<div class="button-group ">
						  <button type="submit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-plus"> </i>
							Submit
						</button>
					</div>       
				</div>  
            </div> 
			</form>
		</div>
	</div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
$(".table").DataTable();
$(".select2").select2();

$("#branch").change(function(){
	$.ajax({
		url: "{{url('get-department-by-branch')}}",
		type: 'POST',
		data:{_token:"{{ csrf_token() }}",branch_id:$(this).val()},
		success:function(resp){ 
			$("#department").empty();
			if(resp.status == 200){
				$.each(resp.departments, function(index,value) {
					$("#department").append("<option value='"+value.department_id+"'>"+value.department_name+"</option>");
				});
			}
		}
	});
});
function openPage()
{
	window.location = "{{url('get-public-holidays')}}";
}
</script>
@endsection