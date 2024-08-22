<div class="project-table">
	<table id="order_table" class="table table-striped nowrap dt-responsive" width="100%">
		<thead>
			<tr>
				@if(date('Y-m-d'))
				<th>
					<div class="rkmd-checkbox checkbox-rotate">
					 <label class="input-checkbox checkbox-primary">
						<input type="checkbox" id="checkbox32" class="mainchk">
						<span class="checkbox"></span>
					 </label>
					 <div class="captions"></div>
					</div>
				</th>
				@endif
				<!--<th>Order#</th>-->
				<th>Receipt No</th>
				<th>Date</th>
				<th>Time</th>
				<th>Customer</th>
				<th>Total Items</th>
				<th>Driver</th>
				<th>Assign Time</th>
				<!--<th>Total Amount</th>-->
				<th>Service Provider</th>
				<th>Order Status</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $key => $value)
				<tr >
					@if(date("Y-m-d"))
					<td>
					   <div class="rkmd-checkbox checkbox-rotate">
						 <label class="input-checkbox checkbox-primary">
							<input type="checkbox" id="checkbox32{{$value->serviceprovidersorders->id}}" class="chkbx" data-id="{{$value->serviceprovidersorders->id}}">
							<span class="checkbox"></span>
						 </label>
						 <div class="captions"></div>
					  </div>
					</td>
					@endif
					<!--<td>{{$value->serviceprovidersorders->id}}</td>-->
					<td>{{$value->serviceprovidersorders->receipt_no}}</td>
					<td>{{$value->serviceprovidersorders->date}}</td>
					<td>{{date("H:i a",strtotime($value->serviceprovidersorders->time))}}</td>
					<td>{{$value->serviceprovidersorders->customer->name}}</td>
					<td>{{$value->serviceprovidersorders->total_item_qty}}</td>
					<td>{{(!empty($value->serviceprovidersorders->orderassign->driver) ? $value->serviceprovidersorders->orderassign->driver->name : '')}}</td>
					<td>{{(!empty($value->serviceprovidersorders->orderassign) ? date("H:i a",strtotime($value->serviceprovidersorders->orderassign->created_at)) : '' )}}</td>
					<td>
						<select id="serviceprovider{{$key}}" class="form-control select2" dataplaceholder="Select Service Provider" onchange="providerChange({{$value->id}},'serviceprovider{{$key}}',{{$value->serviceprovidersorders->id}},{{$value->serviceprovider->id}})">

							@foreach($providers as $provider)
								<option value="{{$provider->id}}" {{($provider->id == $value->serviceprovider->id ? "Selected" : "")}}>{{$provider->provider_name}}</option>
							@endforeach

						</select>
					</td>
					<td>
						<select id="orderstatus{{$key}}" class="form-control select2" dataplaceholder="Select Order Status" onchange="statusChange('orderstatus{{$key}}',{{$value->serviceprovidersorders->id}})">

							@foreach($status as $orderstatus)
								<option value="{{$orderstatus->order_status_id}}" {{($value->serviceprovidersorders->status == $orderstatus->order_status_id ? "Selected" : "")}}>{{$orderstatus->order_status_name}}</option>
							@endforeach

						</select>
					</td>
					<td class='action-icon'>
						<i onclick="showReceipt('{{$value->serviceprovidersorders->receipt_no}}')" class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Print Receipt'></i>
					</td>

				</tr>
			@endforeach
		</tbody>
	</table>
</div>

<script type="text/javascript">
	var rem_id = [];
	$(".select2").select2();
	
	$('.table').DataTable({
		bLengthChange: true,
		displayLength: 300,
		destroy: true,
		paging:true,
		info: true,
		language: {
		  search:'', 
		  searchPlaceholder: 'Search Order',
		  lengthMenu: '<span></span> _MENU_'
		}
	});
	
	$(".mainchk").on('click',function(){
	
		if($(this).is(":checked")){
			$("#orderAssign").css("display", "block");

			$(".chkbx").each(function( index ) {
			   $(this).prop("checked",true);
			   console.log($(this).attr('id'))
			});

		}else {
			$("#orderAssign").css("display", "none");
			
			$(".chkbx").each(function( index ) {
			  $(this).prop("checked",false);
			  console.log($(this).attr('id'))
			});
		}    
	});
	
	$(".chkbx").on('click',function(){
        if($(this).is(":checked")){
          $("#orderAssign").css("display", "block");
		  $(this).prop("checked",true);
		  console.log($(this).attr('id'))
        }else {
          $("#orderAssign").css("display", "none");
		  $(this).prop("checked",false);
		  console.log($(this).attr('id'))
        }
	});
	
	
</script>