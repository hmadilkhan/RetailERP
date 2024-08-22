<div class="project-table">
	<table class="table table-striped nowrap dt-responsive" width="100%">
		<thead>
			<tr>
				<th>ID#</th>
				<th>Date</th>
				<th>Time</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Narration</th>
			</tr>
		</thead>
		<tbody>
		
		@if($payments)
			@foreach ($payments as $value)
				<tr>
				  <td>{{$value->id}}</td>
				  <td>{{date("d F Y",strtotime($value->created_at))}}</td>
				  <td>{{date("h:i a",strtotime($value->created_at))}}</td>
				  <td>{{number_format($value->debit,2)}}</td>
				  <td>{{number_format($value->credit,2)}}</td>
				  <td>{{ $value->narration}}</td>
				</tr>
			@endforeach
		@endif
		 
		</tbody>
	</table>
	{{$payments->links()}}
</div>