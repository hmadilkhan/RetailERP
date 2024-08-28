@extends('layouts.master-layout')

@section('title','Public Holidays')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Public Holidays</h5>
			 <button onclick="openPage()" class="f-right btn btn-success white--text"><a>Mark Public Holiday</a></button>
		</div>
	</div>
</section>
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text">Holidays List</h5>
		</div>
		<div class="card-block">
			<table class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0" style="margin-top: -40px;">
				<thead>
					<tr>
						<th>S No.</th>
						<th>Employee Name</th>
						<th>Date</th>
						<th>Reason</th>
					</tr>
				</thead>
				<tbody>
				   @foreach($results as $key => $value)
						<tr>
							<td>{{++$key}}</td>
							<td>{{$value->emp_name}}</td>
							<td>{{$value->date}}</td>
							<td>{{$value->reason}}</td>
						</tr>
				   @endforeach
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
$(".table").DataTable();
function openPage()
{
	window.location = "{{url('create-public-holidays')}}";
}
</script>
@endsection

