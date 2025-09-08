@extends('layouts.master-layout')

@section('title','Vendor Advance Payments')

@section('breadcrumtitle','Vendor Advance Payments')
@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
 <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Add Advance Payments </h5>

                <h5 class=""><a href="{{ url('vendors') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>

            </div>
            <div class="card-block">
                <form method="GET" action="{{url('save-advance-payment')}}">
                    <input type="hidden" name="vendor_id" value="{{$vendor->id}}"/>
                    <div class="row">
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Date</label>
                                <input class="form-control" type="text"
                                       name="date" id="date" placeholder="DD-MM-YYYY" value="{{ old('date') }}"/>
                                @error('date')
                                    <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group {{ $errors->has('debit') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Debit</label>
                                <input class="form-control" type="number"
                                       name="debit" id="debit" required value="0"  value="{{ old('debit') }}"/>
                                @error('debit')
                                  <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Credit</label>
                                <input class="form-control" type="number"
                                       name="credit" id="credit" required value="0" value="{{ old('credit') }}" />
                                @error('credit')
                                    <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Narration</label>
                                <input class="form-control" type="text"
                                       name="narration" id="narration"   value="{{ old('narration') }}" />
                                @error('narration')
                                <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group">
                                <label class="form-control-label"></label>
                                <button type="submit"  class="btn btn-md btn-primary waves-effect waves-light m-t-20" >
                                    Add Amount
                                </button>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
	<section class="panels-wells">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Advance Payments - ( {{$vendor->vendor_name}} )</h5>
				<h5 class="card-header-text f-right">Total Advance - ( {{$balance}} )</h5>
			</div>
			<div class="card-block">
				<div id="tableData"></div>
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
		
		$(document).ready(function(){

			$(document).on('click', '.pagination a', function(event){
				event.preventDefault(); 
				var page = $(this).attr('href').split('page=')[1];
				getData(page);
			});

			 
		});
		getData(1)
		function getData(page)
		{
			$.ajax({
				url: "{{ url('get-advance-payment')}}"+ "?page="+page,
				type: 'GET',
				data:{_token:"{{ csrf_token() }}",id:"{{$vendor->id}}"},
				success:function(resp){
					$("#tableData").empty();
					$("#tableData").append(resp);
				}
			});
		}
		
		$("#debit").blur(function(){
			let debitValue = $("#debit").val();
			let balance = "{{$balance}}";
			
			if(debitValue > balance){
				$("#debit").val(0);
				$("#debit").focus();
				notify('Ledger does not have sufficient balance', 'danger');
			}
		});
	</script>
@endsection