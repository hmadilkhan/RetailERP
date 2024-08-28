@extends('layouts.master-layout')
@section('title','Addons')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
		<h5 class="card-header-text" id="title-hcard">Addons Categories</h5>
		</div>
		<div class="card-block">
			<form method="POST" id="deptform" class="form-horizontal">
			@csrf
				<div class="row">
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Addon Name:</label>
							<input class="form-control" type="text" name="name" id="name" />
							<div class="form-control-feedback text-danger" id="name_alert"></div>
						</div>
					</div>
					
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Addon Type</label>
							<select class="form-control select2" id="type" name="type">
								<option value="single">Single</option>
								<option value="multiple">Multiple</option>
							</select> 
							<div class="form-control-feedback text-danger" id="type_alert"></div>
						</div>
					</div>
					
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Mode</label>
							<select class="form-control select2" id="mode" name="mode">
								<option value="addons">Addons</option>
								<option value="variations">Variations</option>
								<option value="groups">Deal Groups</option>
							</select> 
							<div class="form-control-feedback text-danger" id="type_alert"></div>
						</div>
					</div>

					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Description</label>
							<textarea class="form-control" id="description" name="description" type="text" rows="2"></textarea>
							<span class="form-control-feedback text-danger" id="description_alert"></span>
						</div>
					</div>

					<div class="col-lg-3 col-md-3">
						<div class="form-group row">
							<button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus" 
							></i>&nbsp; Save</button>.
							<button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
							></i> Clear</button>
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
	$(".select2").select2();
	
	$("#btn_clear").on('click',function(){
		$("#deptform")[0].reset();
	});
	
	$("#btn_save").on('click',function(event){
		if($("#name").val() == ""){
			 $("#name").focus();
			 $("#name_alert").html('Addon name is required.');
		}else{
			$.ajax({
				url:'{{ route("addons.store") }}',
				type:"POST",
				data:$('#deptform').serialize(),
				dataType:"json",
				success:function(response){
				// console.log(response)
					if(response.status != 200){
						if(response.contrl != ""){
							$("#"+response.contrl).focus();
							$("#"+response.contrl+"_alert").html(response.msg);
						}
						swal_alert('Alert!',response.msg,'error',false); 

					}else {
						$("#name_alert").html('');
						swal_alert('Successfully!',response.msg,'success',true);
					}
				}
			});
		}
	});
	
</script>

@endsection