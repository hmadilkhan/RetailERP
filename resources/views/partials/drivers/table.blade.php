<div class="project-table m-t-20">
	<table class="table table-striped nowrap dt-responsive m-t-10" width="100%">
		<thead>
			<tr>
				<th>Preview</th>
				<th>Name</th>
				<th>Contact</th>
				<th>Address</th>
				<th>License No</th>
				<th>NIC No</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@if($drivers)
				@foreach($drivers as $driver)
					<tr>
						<td class="text-center">
							<img src="{{ asset('public/assets/images/drivers/'.(!empty($driver->image) ? $driver->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->driver) ? $value->driver : 'placeholder.jpg' }}">
						</td>
						<td>{{$driver->name}}</td>
						<td>{{$driver->mobile}}</td>
						<td>{{$driver->address}}</td>
						<td>{{$driver->license_no}}</td>
						<td>{{$driver->nic_no}}</td>
						<td class="action-icon">
							@if($mode == 1)
								<a href="{{ route('driver.edit',$driver->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
								<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $driver->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
							@elseif($mode == 2)
								<a onclick="reactiveDriver('{{$driver->id}}')" class="icofont icofont-ui-check text-success f-18 alert-confirm" data-id="{{ $driver->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Reactive"></a>
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
	  searchPlaceholder: 'Search Driver',
	  lengthMenu: '<span></span> _MENU_'
	}
});
</script>
				