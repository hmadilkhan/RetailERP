@extends('layouts.master-layout')

@section('title','Raw Usage Report')

@section('breadcrumtitle','Orders Panel')

@section('navbranchoperation','active')
@section('navorder','active')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
body{
    margin-top:-3.5rem;
    background-color: #f1f3f7;
	text-decoration: none;
}

.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgb(76 175 80) !important;
}



.font-size-18 {
    font-size: 18px!important;
}

.font-size-24 {
    font-size: 24px!important;
}

.font-size-28 {
    font-size: 28px!important;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

a {
    text-decoration: none!important;
}

.w-xl {
    min-width: 160px;
}

.card {
    margin-bottom: 24px;
    -webkit-box-shadow: 0 2px 3px #e4e8f0;
    box-shadow: 0 2px 3px #e4e8f0;
}

.card {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #eff0f2;
    border-radius: 1rem;
}
</style>

<div class="row bg-transparent m-2 p-2">
	<div class="col-xl-12">
	<div class="card border shadow-none">
		<div class="card-body">
			<div class="col-xl-12">
				<div class=" d-flex align-items-start ">
					<div class="bg-transparent py-2 px-4">
						<h5 class="font-size-24 text-sm-end text-muted mb-5">Raw Usage Report </h5>
					</div>
				</div>
			</div>

			
			<div class="col-xl-12 ">
			<form method="get" action="{{url('raw-usage-report')}}" >
				<div class="col-md-2">
					<div class="form-group">
						<label class="form-control-label">From Date</label>
						<input type='date' class="form-control" id="rpdate" name="from" placeholder="DD-MM-YYYY" required value="{{$from}}"/>  
						<span class="help-block text-danger" id="rpbox"></span>  
					</div>
				</div>  
			   
				<div class="col-md-2">
					<div class="form-group">
						<label class="form-control-label">To Date</label>
						<input type='date' class="form-control" id="date" name="to" placeholder="DD-MM-YYYY" required value="{{$to}}"/>  
						<span class="help-block text-danger" id="dbox"></span>  
					</div>
				</div>
				
				<div class="col-md-2">
					<div class="form-group mt-2">
						<button type="submit" class="btn btn-success text-white  mt-4"><i class="mdi mdi-check me-1"></i> Submit </button>
					</div>
				</div>
			</form>
			</div>
		</div>
		</div>
	</div>
</div>


<div class="row bg-transparent m-2 p-2">
<div class="accordion" id="accordionExample">
  @foreach($totalSaleItems as $key => $item)
  <div class="accordion-item mt-2 bg-transparent">
    <h2 class="accordion-header " id="headingOne">
	<button id="collapsebutton{{$key}}" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="collapseOne" onclick="showAccordion('{{$key}}')" >
	  {{$item->item_name}}  <span class="m-l-4 p-l-2">Total Qty {{$item->totalqty}}</span> 
    </button>
	
    </h2>
    <div id="collapse{{$key}}" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
			<table class="table table-striped nowrap dt-responsive" width="100%">
				<thead>
					<tr>
					   <th>SNo#</th>
					   <th>Item Name#</th>
					   <th>UOM</th>
					   <th>Per Usage</th>
					   <th>Total Qty</th>
					   <th>Total Usage</th>
					</tr>
				</thead>
				<tbody>
					@php
					$filteredArray = Arr::where($totalItemsArray, function ($value, $key) use ($item) {
						return $value['recipy_id'] == $item->recipy_id;
					});
					@endphp
					
					@foreach($filteredArray as $key => $receipyItem)
						<tr>
						   <td>{{++$key}}</td>
						   <td>{{$receipyItem['item_name']}}</td>
						   <td>{{$receipyItem['uom']}}</td>
						   <td>{{$receipyItem['usage_qty']}}</td>
						   <td>{{$receipyItem['total_qty']}}</td>
						   <td>{{$receipyItem['total_usage']}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
      </div>
    </div>
  </div>
  @endforeach
</div>
</div>


<div class="row bg-transparent m-2 p-2">
	<div class="col-xl-12">
	<div class="card border shadow-none">
		<div class="card-body">
			<table id="order_table" class="table table-striped nowrap dt-responsive" width="100%">
				<thead>
					<tr>
					   <th>SNo#</th>
					   <th>Item Name#</th>
					   <th>UOM</th>
					   <th>Cost</th>
					   <th>Wastage</th>
					   <th>Total Usage</th>
					   <th>Total Cost</th>
					   <th>Previous Stock</th>
					   <th>Balance Stock</th>
					   <th>Closing Stock</th>
					</tr>
				</thead>
				<tbody>
					@foreach($allItemUsage as $key => $item)
						<tr>
						   <td>{{++$key}}</td>
						   <td>{{$item->product_name}}</td>
						   <td>{{$item->uom}}</td>
						   <td>{{($item->cost == "" ? 0 : $item->cost)}}</td>
						   <td>{{($item->wastage  == "" ? 0.00 : $item->wastage)}}</td>
						   <td>{{$item->totalUsage}}</td>
						   <td>{{($item->cost == "" ? 0 : $item->cost) * $item->totalUsage}}</td>
						   <td>{{$item->previous_stock}}</td>
						   <td>{{$item->current_stock}}</td>
						   <td>{{($item->closing_stock  == "" ? 0.00 : $item->closing_stock)}}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
@endsection

@section("scriptcode_three")
<script>
	$("#order_table").DataTable({
		pageLength: 50,
	});
	function showAccordion(id){
	 $("#collapse"+id).toggle("slow");
	 $("#collapsebutton"+id).toggleClass("collapsed");
	}
</script>
@endsection

