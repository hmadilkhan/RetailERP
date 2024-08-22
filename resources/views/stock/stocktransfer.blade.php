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
<form method="post" action="{{url('save-stock-tranfer')}}">
@csrf
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
		<table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
			<thead>
			   <tr>
				  <th>Product #</th>
				  <th>Item Code</th>
				  <th>Product Name</th>
				  <th>Unit</th>
				  <th>Department</th>
				  <th>Sub Department</th>
				  <th>Stock Qty</th>
				  <th>Transfer Qty</th>
			   </tr>
			</thead>
			<tbody>
				@foreach($products as $product)
					@if($product->qty > 0)
					<tr>
					  <td><input type="hidden" name="product[]" class="form-control" value="{{$product->id}}"/>{{$product->id}}</td>
					  <td>{{$product->item_code}}</td>
					  <td>{{$product->product_name}}</td>
					  <td>{{$product->name}}</td>
					  <td>{{$product->department_name}}</td>
					  <td>{{$product->sub_depart_name}}</td>
					  <td><input type="hidden" name="stock[]" class="form-control" value="{{$product->qty}}"/>{{$product->qty}}</td>
					  <td style="width:25px;"><input type="text" name="qty[]" class="form-control"/></td>
				   </tr>
				   @endif
				@endforeach
			</tbody>
		</table>
	</div>
</div> 
<div class="card">
	<div class="card-body">
		<div class="row ">
			<div class="col-md-6 f-right ">
				<label class="form-control-label"></label>
				<button class="waves-effect btn-md waves-light m-t-25 m-r-10 m-b-10 f-right btn btn-primary">Submit</button>
			</div>
		</div>
	</div>
</div>
</form>	 
@endsection
@section('scriptcode_three')
<script type="text/javascript">
  $(".select2").select2();
</script>
@endsection