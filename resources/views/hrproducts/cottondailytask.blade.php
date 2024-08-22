@extends('layouts.master-layout')
 
@section('title','Cotton Per Pcs Salary')

@section('breadcrumtitle','Cotton Per Pcs Salary')
@section('navinventory','active')
@section('navinventorys','active')

@section('content')
<style>
.vertical-middle {
	text-align: center;
    vertical-align: middle;
}
</style>
<section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Cotton Per Pcs</h5>
            </div>
            <div class="card-block">
				<form action="{{url('save-perpcs-salary')}}" method="POST">
				@csrf
				<div class="row">
					<div class="col-lg-3 col-md-3 f-right">
						<div class="form-group {{ $errors->has('doj') ? 'has-danger' : '' }} ">
							<label class="form-control-label">Date</label>
							<input type="text" name="date" id="date" class="form-control" placeholder="23-12-2019" value="{{ old('doj') }}"/>
							@if ($errors->has('date'))
							<div class="form-control-feedback">Required field can not be blank.</div>
							@endif
						</div>
					</div>
				</div>
				<table id="salarytbl" class="table " style="border:1px solid" >
					<thead>
					<tr style="width:30px;">
						<td style="text-align: center;vertical-align: middle;border:1px solid;width:130px;font-weight:bold;" rowspan="2" >Employee Name</td>
						 @foreach($lists as $list)
							<td style="text-align: center;border:1px solid;width:30px;font-weight:bold;">{{$list->name}}</td>
						 @endforeach
						 <td style="text-align: center;vertical-align: middle;border:1px solid;width:130px;font-weight:bold;" rowspan="2" >Salary</td>
					</tr>
					</thead>
					<tbody>
						@foreach($employees as $employee)
						<tr>
							<td style="text-align: center;vertical-align: middle;border:1px solid;width:3;"><input type="hidden" name="employee[]" value="{{$employee->empid}}"/>{{$employee->emp_name." ".$employee->gross_salary}}</td>
							@foreach($lists as $list)
							<td style="text-align: center;border:1px solid;"><input type="hidden" name="product[{{$employee->empid}}][]" value="{{$list->id}}"/><input type="hidden" id="rate" name="price[{{$employee->empid}}][]" value="{{$perpiecerate}}"/><input type="text" name="quantity[{{$employee->empid}}][]" class="form-control text-center" id="product{{$employee->empid.$list->id}}" onchange="calculateSalary('{{$perpiecerate}}','product{{$employee->empid.$list->id}}','dailysalary{{$employee->empid}}')"/></td>
							@endforeach
							<td style="text-align: center;border:1px solid;"><input id="dailysalary{{$employee->empid}}" readonly type="text" class="form-control text-center" name="dailysalary[]" value="0"/></td>
						</tr>
						@endforeach
					</tbody>
				 </table>
				 <div class="row">
					<div class="col-md-12">
						<button type="submit" id="search" data-placement="bottom" class="btn btn-success  waves-effect waves-light f-right m-r-10">Save</button>
					</div>
				</div>
				</form>
			</div>
			</div>
		</section>
@endsection
@section('scriptcode_three')
 <script type="text/javascript">
	$('#date').bootstrapMaterialDatePicker({
	  format: 'DD-MM-YYYY',
	  time: false,
	  clearButton: true,

	icons: {
		date: "icofont icofont-ui-calendar",
		up: "icofont icofont-rounded-up",
		down: "icofont icofont-rounded-down",
		next: "icofont icofont-rounded-right",
		previous: "icofont icofont-rounded-left"
	  }
	});
	function calculateSalary(rate,qtyInputId,SalaryInputId){
		 let quantity = $("#"+qtyInputId).val();
		 let totalSalary = $("#"+SalaryInputId).val();
		 let total = rate * quantity;
		 total = parseFloat(totalSalary) + total;
		 $("#"+SalaryInputId).val(total)
	}
 </script>
@endsection