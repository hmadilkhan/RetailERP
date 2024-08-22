@extends('layouts.master-layout')

@section('title','Stock Transfer')

@section('breadcrumtitle','Stock Transfer')

@section('navbranchoperation','active')
@section('navstock','active')

@section('content')
<div class="card">
	<div class="card-header">
		<h5 class="card-header-text">Stock Transfer</h5>
		<a href="{{ url('/stock-list') }}" id="btnback" name="btnback" class="f-right"><i class="icofont icofont-arrow-left"></i>Back to List</a>
		<div class="text-center f-64 text-info">{{$branch}}</div>
	</div> 
</div> 

<div class="card">
	<div class="card-body dashboard-header p-15">
		<div class="row  m-l-2">
			<div class="col-md-3">
				<label class="form-control-label">Select Terminal</label>
				<select id="terminal" name="terminal" class="f-right select2" data-placeholder="Select Terminal">
				 <option value="">Select Terminal</option>
				 @foreach($terminals as $terminal)
					 <option value="{{ $terminal->terminal_id }}">{{ $terminal->terminal_name }}</option>
				 @endforeach
				</select>
			</div>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-body">
		<table id="stocklist" class="table dt-responsive nowrap" width="100%" cellspacing="0">
			<thead>
			   <tr>
				  <th>Product #</th>
				  <th>Item Code</th>
				  <th>Product Name</th>
				  <th>Unit</th>
				  <th>Department</th>
				  <th>Sub Department</th>
				  <th>Stock Qty</th>
				  <th>Date</th>
			   </tr>
			</thead>
			<tbody>
			</tbody>
    </table>
</div>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
  $(".select2").select2();
  
  $("#terminal").change(function(){
	$.ajax({
		url: "{{url('terminal-stock')}}",
		type: 'POST',
		data:{"_token":"{{csrf_token()}}",terminal_id:$(this).val()},
		success: function(response){
			console.log(response)
			$("#stocklist tbody").empty();
			$.each(response.stock, function(i,data) {
				let html = "<tr>"+
								"<td>"+data.inventory.id+"</td>"+
								"<td>"+data.inventory.item_code+"</td>"+
								"<td>"+data.inventory.product_name+"</td>"+
								"<td>"+data.inventory.uom.name+"</td>"+
								"<td>"+data.inventory.department.department_name+"</td>"+
								"<td>"+data.inventory.subdepartment.sub_depart_name+"</td>"+
								"<td>"+data.qty+"</td>"+
								"<td>"+data.date+"</td>"+
							"</tr>";
				$("#stocklist tbody").append(html);
			});
			
			
		},
	});
  });
</script>
@endsection