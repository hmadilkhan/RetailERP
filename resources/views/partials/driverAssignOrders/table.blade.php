<div class="project-table">
	<table id="order_table" class="table table-striped nowrap dt-responsive" width="100%">
		<thead>
			<tr>
				<th>Driver Name</th>
				<th>Loader Name</th>
				<th>Checker Name</th>
				<th>Vehicle Number</th>
				<th>Total Orders</th>
				<th>Assign Time</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($driverOrders as $key => $driverOrder)
				<tr >
					<td>{{$driverOrder->driver->name}}</td>
					<td>{{$driverOrder->loader->fullname}}</td>
					<td>{{(!empty($driverOrder->checker) ? $driverOrder->checker->fullname : "N/A") }}</td>
					<td>{{$driverOrder->vehicles->name}}</td>
					<td>{{$driverOrder->orders}}</td>
					<td>{{date("H:i a",strtotime($driverOrder->created_at))}}</td>
					<td class='action-icon'>
						<i onclick="showItems('{{$driverOrder->driver->id}}','{{$driverOrder->created_at}}')" class='icofont icofont icofont-list text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Print Receipt'></i>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

<script type="text/javascript">
	var rem_id = [];

	$('.table').DataTable({
		bLengthChange: true,
		displayLength: 10,
		destroy: true,
		paging:true,
		info: true,
		language: {
		  search:'', 
		  searchPlaceholder: 'Search Order',
		  lengthMenu: '<span></span> _MENU_'
		}
	});

</script>