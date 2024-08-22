@extends('layouts.master-layout')

@section('title','Mark Manual Attendance')

@section('breadcrumtitle','Mark Manual Attendance')

@section('navattendance','active')

@section('navabsent','active')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Filter</h5>
			<div class="row">
				<div class="col-lg-2 col-md-2">
					  <div class="form-group">
						  <label class="form-control-label">Date</label>
						  <input class="form-control" type="text" name="fromdate" id="fromdate" placeholder="DD-MM-YYYY"/>
						  <div class="form-control-feedback"></div>
					  </div>
				 </div>
				<div class="col-lg-3 col-md-3">
					<div class="form-group">
						<label class="form-control-label">Select Branch</label>
						<select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
							<option value="">Select Branch</option>
							@if($branches)
								@foreach($branches as $value)
									<option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
								@endforeach
							@endif
						</select>
					</div>
				</div>
				<div class="col-lg-3 col-md-3">
					<div class="form-group">
						<label class="form-control-label">Select Department</label>
						<select name="department" id="department" data-placeholder="Select Department" class="form-control select2" >
							<option value="">Select Department</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="card-block">
			<form method="post" action="{{url('mark-manual-attendance')}}">
			@csrf
			<table id="tblattendance" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
				<thead>
					<tr>
						<th>Employee Code</th>
						<th>Employee Name</th>
						<th>Father Name</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				
				</tbody>
			</table>
			</form>
		</div>
	</div>
</section>

@endsection
@section('scriptcode_three')
<script type="text/javascript">
	$(".select2").select2();
	
	$('#fromdate').bootstrapMaterialDatePicker({
		format: 'YYYY-MM-DD',
		time: false,
		clearButton: true,

		icons: {
			date: "icofont icofont-ui-calendar",
			up: "icofont icofont-rounded-up",
			down: "icofont icofont-rounded-down",
			next: "icofont icofont-rounded-right",
			previous: "icofont icofont-rounded-left"
		}
	});
	
	$("#branch").change(function(){
	  $("#employee").empty();
	  $("#department").empty();
	  $.ajax({
		url:'{{ url("/get-departments-from-branch") }}',
		type:"POST",
		data:{_token : "{{csrf_token()}}",
			branch:$(this).val(),
		},
		success:function(result){
		   if(result.status == 200){
			    $("#department").append("<option value=''>Select Department</option>");
				$.each(result.departments , function(index, val) { 
				  $("#department").append("<option value="+val.department_id+">"+val.department_name+"</option>");
				});
		   }
		}
	  }); 
	});
	
	$("#department").change(function(){
	  $("#employee").empty();
	  $("#tblattendance tbody").empty();
	  $.ajax({
		url:'{{ url("/get-employees-from-departments") }}',
		type:"POST",
		data:{_token : "{{csrf_token()}}",
			department_id:$(this).val(),
		},
		success:function(result){
			$("#tblattendance > tbody").empty();
		   if(result.status == 200){
			   if(result.employees.length > 0){
				$.each(result.employees , function(index, val) { 
				  $("#tblattendance").append(
				  "<tr>"+
					"<input type='hidden' name='empid[]' value='"+val.empid+"'/>"+
					"<input type='hidden' name='clock_in[]' value='"+val.shift_start+"'/>"+
					"<input type='hidden' name='clock_out[]' value='"+val.shift_end+"'/>"+
					"<input type='hidden' name='branch' value='"+$("#branch").val()+"'/>"+
					"<input type='hidden' name='fromdate' value='"+$("#fromdate").val()+"'/>"+
					"<td>"+val.emp_acc +"</td>"+
					"<td>"+val.emp_name+"</td>"+
					"<td>"+val.emp_fname+"</td>"+
					"<td>"+
						"<select name='attendance[]' id='attendance' data-placeholder='Select Attendance' class='form-control select2' >"+
							"<option value='present'>Present</option>"+
							"<option value='absent'>Absent</option>"+
							"<option value='leave'>Leave</option>"+
						"</select>"+
					"</td>"+
					"</tr>");
				});
				 $("#tblattendance").append( "<tr><td colspan='4'><button type='submit' class='btn btn-success waves-effect waves-light f-right d-inline-block m-r-10'>Submit</button></td></tr>");
			   }else{
				   $("#tblattendance").append( "<tr><td colspan='4' class='text-center'>No Record Found</td></tr>");
			   }
				$(".select2").select2();
		   }
		}
	  }); 
	});
</script>
@endsection