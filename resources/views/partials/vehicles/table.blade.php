<div class="project-table m-t-20">
	<table class="table table-striped nowrap dt-responsive m-t-10" width="100%">
		<thead>
			<tr>
				<th>Model</th>
				<th>Model No</th>
				<th>Number</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if($vehicles)
				@foreach($vehicles as $vehicle)
					<tr>
						<td>{{$vehicle->model_name}}</td>
						<td>{{$vehicle->model_no}}</td>
						<td>{{$vehicle->number}}</td>
						<td class="action-icon">
							@if($mode == 1)
								<a href="{{ route('vehicle.edit',$vehicle->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
								<i onclick="deleteDriver('{{$vehicle->id}}')" class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $vehicle->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
							@elseif($mode == 2)
								<a onclick="reactiVehicle('{{$vehicle->id}}')" class="icofont icofont-ui-check text-success f-18 alert-confirm" data-id="{{ $vehicle->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reactive"></a>
							@endif
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
</div>

<script>
$('.table').DataTable({
	bLengthChange: true,
	displayLength: 10,
	info: true,
	language: {
	  search:'', 
	  searchPlaceholder: 'Search Vehicle',
	  lengthMenu: '<span></span> _MENU_'
	}
});

	function deleteDriver(id){
		var id= $(this).data("id");

		swal({
		  title: "Are you sure?",
		  text: "Your will not be able to recover this vehicle!",
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
						url: "{{route('vehicle.delete')}}",
						type: 'POST',
						data:{_token:"{{ csrf_token() }}",
						id:id,
						mode:2,
						},
						success:function(resp){
						  console.log(resp);
							if(resp == 1){
								 swal({
										title: "Deleted",
										text: "Vehicle successfully deleted.",
										type: "success"
								   },function(isConfirm){
									   if(isConfirm){
										window.location="{{route('vehicle.list')}}";
									   }
								   });
							 }
						}

					});
			  
		   }else {
			  swal("Cancelled", "Your vehicle is safe :)", "error");
		   }
		});
	};
	
	function reactiVehicle(id){
		

		swal({
		  title: "Are you sure?",
		  text: "You want to active this vehicle!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-success",
		  confirmButtonText: "Active it!",
		  cancelButtonText: "cancel plx!",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
		  if(isConfirm){
					 $.ajax({
						url: "{{route('vehicle.delete')}}",
						type: 'POST',
						data:{_token:"{{ csrf_token() }}",
						id:id,
						mode:1,
						},
						success:function(resp){
							if(resp == 1){
								 swal({
										title: "Activated",
										text: "Vehicle activated successfully.",
										type: "success"
								   },function(isConfirm){
									   if(isConfirm){
										window.location="{{route('vehicle.list')}}";
									   }
								   });
							 }
						}

					});
			  
		   }else {
			  swal("Cancelled", "Your vehicle is still inactive :)", "error");
		   }
		});
	}
</script>
				