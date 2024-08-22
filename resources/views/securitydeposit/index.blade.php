@extends('layouts.master-layout')

@section('title','Employee Security Deposit')

@section('breadcrumtitle','View Employee')

@section('navemployees','active')

@section('navhire','active')

@section('navemployee','active')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Employee Security Deposit</h5>
		</div> 
		<div class="card-block">
			<form method="post">
			<input id="id" type="hidden" value=""/>
			@csrf
				<div class="row">
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Select Branch:</label>
							<select id="branch" name="branch" data-placeholder="Select Branch" class="form-control select2">
								<option value="">Select Branch</option>
									@if($branches)
										@foreach($branches as $branch)
											<option value="{{ $branch->branch_id }}" {{(!empty($deposit) && $deposit[0]->branch_id == $branch->branch_id ? 'selected="selected"' : '' )}}>{{ $branch->branch_name }}</option>
										@endforeach
									@endif
							</select>
							<div class="form-control-feedback"></div>
						</div>
					</div>
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Security Deposit Limit:</label>
							<input type="text" name="employee_security_deposit" id="employee_security_deposit" class="form-control" value="{{(!empty($deposit) ? $deposit[0]->total_limit : '' )}}"/>
							<div class="form-control-feedback"></div>
						</div>
					</div>
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Monthly Deduction:</label>
							<input type="text" name="monthly_deduction" id="monthly_deduction" class="form-control" value="{{(!empty($deposit) ? $deposit[0]->monthly_deduction : '' )}}"/>
							<div class="form-control-feedback"></div>
						</div>
					</div>
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light m-t-2" onclick="submitdata()"> Create </button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Details</h5>
		</div> 
		<div class="card-block">
			<table id="tblholiday" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
				<thead>
					<tr>
						<th>Branch Name</th>
						<th>Limit</th>
						<th>Monthly Deduction</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@if($deposits)
						@foreach($deposits as $deposit)
							<tr>
								<td>{{$deposit->branch_name}}</td>
								<td>{{$deposit->total_limit}}</td>
								<td>{{$deposit->monthly_deduction}}</td>
								<td class='action-icon'>
									<i class='icofont icofont-pencil text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit' onclick="edit('{{$deposit->id}}','{{$deposit->branch_id}}','{{$deposit->total_limit}}','{{$deposit->monthly_deduction}}')"></i> 
									<i class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete' onclick="deleteData('{{$deposit->id}}')"></i>
								</td>
							</tr>
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	$(".select2").select2();
	$('#tblholiday').DataTable({});
	
	function submitdata(){
		let url = "";
		if($('#id').val() != ""){
			url = "{{url('/update-security-deposit')}}"
		}else{
			url = "{{url('/store-security-deposit')}}"
		}
		$.ajax({
			url: url,
			type:"POST",
			data:{
				_token:"{{ csrf_token() }}",
				id:$('#id').val(),
				branch:$('#branch').val(),
				employee_security_deposit:$('#employee_security_deposit').val(),
				monthly_deduction:$('#monthly_deduction').val(),
			},
			dataType:"json",
			success:function(resp){
				console.log(resp)
				if(resp.status == 200){
					location.reload();
				}
				// swal({
					// title: "Operation Performed",
					// text: "Data Stored Successfully!",
					// type: "success"},
					// function(isConfirm){
					// if(isConfirm){
						// window.location = "{{url('view-sms')}}";
					// }
				// });
			}
		});
	}
	
	function edit(id,branchId,limit,monthly)
	{
		$("#id").val(id);
		$("#branch").val(branchId).change();
		$("#employee_security_deposit").val(limit);
		$("#monthly_deduction").val(monthly);
	}
	
	function deleteData(id){
		let name = $('#sendernamemodal').val();
		swal({
			title: "Are you sure?",
			text: "Do You want to delete ?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "delete it!",
			cancelButtonText: "cancel plx!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm){
			if(isConfirm){
			$.ajax({
			url: "{{url('/delete-security-deposit')}}",
			type: 'POST',
			data:{
				_token:"{{ csrf_token() }}",
				id:id,
			},
			success:function(resp){
				if(resp.status == 200){
					swal({
						title: "Deleted",
						text: "Data Deleted Successfully!",
						type: "success"
					},function(isConfirm){
						if(isConfirm){
							window.location="{{ url('/view-security-deposit') }}";
						}
					});
				}
			}
		});
		}else {
			swal("Cancelled", "Operation Cancelled:)", "error");
		}
		});
	}
</script>
@endsection