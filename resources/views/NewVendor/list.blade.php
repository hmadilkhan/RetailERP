@extends('layouts.master-layout')

@section('title', 'Vendors')

@section('breadcrumtitle', 'Vendors')

@section('navvendor','active')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Vendors</h5>
			<a href="{{ route('vendors.create') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block">
				<i class="icofont icofont-plus f-18 m-r-5"></i>Create Vendor
			</a>
			<button type="button" id="btn_removeall" class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
		</div>
		<div class="card-block">
			<div class="project-table">
				<table class="table table-striped nowrap dt-responsive" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>Logo</th>
							<th>Vendor Name</th>
							<th>Company</th>
							<th>Contact</th>
							<th>Email</th>
							<th>City</th>
							<th>Country</th>
							<th>Balance</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($vendor as $value)
						<tr>
							<td class="text-center">
								<img width="42" height="42" src="{{ asset('storage/images/vendors/' . (!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" class="d-inline-block img-circle" alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
							</td>
							<td>{{ $value->vendor_name }}</td>
							<td>{{ $value->company_name ?? '-' }}</td>
							<td>{{ $value->vendor_contact }}</td>
							<td>{{ $value->vendor_email }}</td>
							<td>{{ $value->city_name ?? ($value->city ?? '-') }}</td>
							<td>{{ $value->country_name ?? ($value->country ?? '-') }}</td>
							<td>{{ isset($value->balance) ? number_format((float)$value->balance, 2) : '0.00' }}</td>
							<td class="action-icon">
								<a href="{{ route('vendors.edit', $value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
								<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	var rem_id = [];

	$('.table').DataTable({
		bLengthChange: true,
		displayLength: 50,
		info: false,
		language: {
			search: '',
			searchPlaceholder: 'Search Vendors',
			lengthMenu: '<span></span> _MENU_'
		}
	});

	// Alert confirm for single delete
	$('.alert-confirm').on('click', function(){
		var id= $(this).data("id");
		swal({
			title: "Are you sure?",
			text: "Do you want to delete this vendor!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "delete it!",
			cancelButtonText: "cancel plx!",
			closeOnConfirm: false,
			closeOnCancel: false
		}, function(isConfirm){
			if(isConfirm){
				$.ajax({
					url: "{{ url('vendors') }}/"+id,
					type: 'POST',
					data: {_token:"{{ csrf_token() }}", _method:'DELETE'},
					success:function(resp){
						swal({
							title: "Deleted",
							text: "Vendor Deleted",
							type: "success"
						},function(isConfirm){ if(isConfirm){ window.location = "{{ route('vendors.index') }}"; }});
						}
					});
			}else{
				swal("Cancelled", "Your vendor is safe :)", "error");
			}
		});
	});

	// Bulk select handling (mirrors other lists)
	$(".mainchk").on('click', function(){
		if($(this).is(":checked")){
			$("#btn_removeall").removeClass('invisible');
			$(".chkbx").each(function(index){ $(this).attr("checked", true); });
		}else{
			$("#btn_removeall").addClass('invisible');
			$(".chkbx").each(function(index){ $(this).attr("checked", false); });
		}
	});

	$(".chkbx").on('click', function(){
		if($(this).is(":checked")){
			$("#btn_removeall").removeClass('invisible');
		}else{
			$("#btn_removeall").addClass('invisible');
		}
	});

	$("#btn_removeall").on('click', function(){
		swal({
			title: "Delete",
			text: "Do you want to remove all vendor?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "YES",
			cancelButtonText: "NO",
			closeOnConfirm: false,
			closeOnCancel: false
		}, function(isConfirm){
			if(isConfirm){
				$(".chkbx").each(function(index){ if($(this).is(":checked")){ if(jQuery.inArray($(this).data('id'), rem_id) == -1){ rem_id.push($(this).data('id')); } } });
				if(rem_id.length > 0){
					$.ajax({
						url: "{{ url('/all-vendors-remove') }}",
						type: "PUT",
						data: { _token: "{{ csrf_token() }}", id: rem_id },
						success: function(resp){
							if(resp == 1){
								swal({ title: "Success!", text: "All vendor remove Successfully :)", type: "success" }, function(isConfirm){ if(isConfirm){ window.location = "{{ route('vendors.index') }}"; } });
							}else{
								swal("Alert!", "Vendor not removed:)", "error");
							}
						}
					});
				}
			}else{
				swal("Cancel!", "Your all vendor is safe:)", "error");
			}
		});
	});
</script>
@endsection 