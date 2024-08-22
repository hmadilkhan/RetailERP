<!--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">-->

<table id="producttb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
                <!-- <th>Preview</th>-->
				<th>Product Id</th>
				<th>Product Code</th>
				<th>Name</th>
				<th>Retail</th>
				<th>Wholesale</th>
				<th>Online</th>
				<th>Discount</th>
				<th>Status</th>
				<th>POS</th>
				<th>Online</th>
				<th>Hide</th>
            </tr>
         </thead>
         <tbody>
           @foreach($main as $key => $value)
                 <tr>
				   <td >{{$value->product_id}}</td>
				   <td >{{$value->item_code}}</td>
				   <td >{{$value->product_name}}</td>
				   <td >{{$value->retail_price}}</td>
				   <td >{{$value->wholesale_price}}</td>
				   <td >{{$value->online_price}}</td>
				   <td >{{$value->discount_price}}</td>
				   <td >{{$value->status}}</td>
				  <!-- <td class="text-center">
						<input id="posCheckbox{{$value->product_id}}" onchange="changeCheckbox('posCheckbox{{$value->product_id}}','{{($value->status == 'inventory' ? $value->product_id : $value->pos_item_id)}}','{{$value->status}}','pos')" type="checkbox" {{($value->isPos == 1 ?  'checked' : '')}}  data-toggle="toggle" data-size="mini" data-width="50" data-height="50">  
					</td>
					<td class="text-center">
						<div class="checkbox" >
						  <label>
								<input id="onlineCheckbox{{$value->product_id}}" onchange="changeCheckbox('onlineCheckbox{{$value->product_id}}','{{($value->status == 'inventory' ? $value->product_id : $value->pos_item_id)}}','{{$value->status}}','online')" type="checkbox" {{($value->isOnline == 1 ?  'checked' : '')}}  data-toggle="toggle" data-size="mini" data-width="50" data-height="50">
						  </label>
						</div>
					</td>
					<td class="text-center">
						<input id="hideCheckbox{{$value->product_id}}" onchange="changeCheckbox('hideCheckbox{{$value->product_id}}','{{($value->status == 'inventory' ? $value->product_id : $value->pos_item_id)}}','{{$value->status}}','hide')" type="checkbox" {{($value->isHide == 1 ?  'checked' : '')}}  data-toggle="toggle" data-size="mini" data-width="50" data-height="50">
					</td> -->
					<td class="text-center">
						<select id="select{{$key}}pos" onchange="valueChange('select{{$key}}pos','{{($value->status == 'inventory' ? $value->product_id : $value->pos_item_id)}}','{{$value->status}}','pos')")">
							<option {{($value->isPos == 1 ?  'selected' : '')}} value="1">Yes</option>
							<option {{($value->isPos == 0 ?  'selected' : '')}} value="0">No</option>
						</select>
					</td>
					<td class="text-center">
						<select id="select{{$key}}online" onchange="valueChange('select{{$key}}online','{{($value->status == 'inventory' ? $value->product_id : $value->pos_item_id)}}','{{$value->status}}','online')")">
							<option {{($value->isOnline == 1 ?  'selected' : '')}} value="1">Yes</option>
							<option {{($value->isOnline == 0 ?  'selected' : '')}} value="0">No</option>
						</select>
					</td>
					<td class="text-center">
						<select id="select{{$key}}hide" onchange="valueChange('select{{$key}}hide','{{($value->status == 'inventory' ? $value->product_id : $value->pos_item_id)}}','{{$value->status}}','hide')")">
							<option {{($value->isHide == 1 ?  'selected' : '')}} value="1">Yes</option>
							<option {{($value->isHide == 0 ?  'selected' : '')}} value="0">No</option>
						</select>
					</td>
        
                </tr>
           @endforeach
         </tbody>
        
      
     </table>
	 
	    {!! $main->links() !!}
<!--<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>-->
<script>
	// $(document).on('change', ":checkbox", function () { 
		// console.log($(this).val())
	// });
	
	function valueChange(divid,id,table,columnname){

		console.log(divid)
		console.log($("#"+divid).val())
		
		$.ajax({
		  url: "{{url('/change-inventory-status')}}",
		  type: "POST",
		  data: {_token:"{{csrf_token()}}",id:id,table:table,columnname:columnname,value:$("#"+divid).val()},
		  success:function(resp){
			  console.log(resp)
		  }
		});
	}
	
	function changeCheckbox(divid,id,table,columnname)
		{
			console.log(divid)
			var value = "";
			if($('#' + divid).is(":checked")){
				value = 1;
			}else{
				value = 0;
			}
			console.log(value)
			
			$.ajax({
				  url: "{{url('/change-inventory-status')}}",
				  type: "POST",
				  data: {_token:"{{csrf_token()}}",id:id,table:table,columnname:columnname,value:value},
				  success:function(resp){
					  console.log(resp)
				  }
			 });
		}
	
</script>