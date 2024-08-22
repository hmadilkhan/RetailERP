@extends('layouts.master-layout')

@section('title','Service Providers Orders')

@section('content')
 <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Service Provider Orders Details</h5>
				<h5 class=""><a href="{{ url('web-orders-view') }}"><i class="text-primary text-center icofont icofont-arrow-left m-t-10 m-b-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">Back to list</i></a></h5>
				
				<div class="row">
					
					<div class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">Receipt No</label>
							<input type='text' class="form-control" id="receipt" name="receipt" placeholder="Receipt No"/>
							<span class="help-block text-danger" id="rpbox"></span>
						</div>
					</div>
					<div id="from" class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">From Date</label>
							<input type='text' class="form-control" id="fromdate" name="fromdate" placeholder="DD-MM-YYYY"/>
							<span class="help-block text-danger" id="rpbox"></span>
						</div>
					</div>
					<div id="to" class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">To Date</label>
							<input type='text' class="form-control" id="todate" name="todate" placeholder="DD-MM-YYYY"/>
							<span class="help-block text-danger" id="dbox"></span>
						</div>
					</div>

					<div class="col-md-3">
						<label class="form-control-label">Select Service Provider</label>
						<select id="serviceprovider" name="serviceprovider" data-placeholder="Select Service Provider" class="f-right select2">
							<option value="">Select Service Provider</option>
							@foreach($providers as $provider)
								<option value="{{$provider->id}}">{{$provider->provider_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="row">
                    <div class="col-md-3 f-right">
                        <label class="form-control-label"></label>
                        <button type="button" id="search"  class="btn btn-success waves-effect waves-light f-right"  >
                            <i class="icofont icofont-ui-check"> </i>Search 
                        </button>
                    </div>
                </div>
			</div>
			
			<div class="card-header" id="orderAssign" style="display:none;">
				<h5 class="card-header-text">Assign Orders</h5>
				<hr/>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">Drivers</label>
							<select id="driver" class="form-control select2" data-placeholder="Select Driver" >
								<option value="">Select Driver</option>
								@foreach($drivers as $driver)
									<option value="{{$driver->id}}">{{$driver->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">Vehicles</label>
							<select id="vehicle" class="form-control select2" data-placeholder="Select Vehicle" >
								<option value="">Select Vehicle</option>
								@foreach($vehicles as $vehicle)
									<option value="{{$vehicle->id}}">{{$vehicle->name}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">Loaders</label>
							<select id="loader" class="form-control select2" data-placeholder="Select Loader" >
								<option value="">Select Loader</option>
								@foreach($loaders as $loader)
									<option value="{{$loader->id}}">{{$loader->fullname}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="form-control-label">Checkers</label>
							<select id="checker" class="form-control select2" data-placeholder="Select Checker" >
								<option value="">Select Checker</option>
								@foreach($checkers as $checker)
									<option value="{{$checker->id}}">{{$checker->fullname}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-12 f-right">
                        <label class="form-control-label"></label>
                        <button type="button" id="assignOrders"  class="btn btn-success waves-effect waves-light m-t-25 f-right"  >
                            <i class="icofont icofont-ui-check"> </i>Assign 
                        </button>
                    </div>
				</div>
				<hr/>
			</div>
			
			<div class="card-block">
				<ul class="nav nav-tabs md-tabs m-b-20">
					<input type="hidden" name="type" id="type" value="1" />
					<li class="nav-item">
					   <a class="nav-link active text-size" id="draft" onclick="changeTab(this,'')">All</a>
					   <div class="slide"></div>
					</li>
					@foreach($status as $key => $value)
						<li class="nav-item">
						   <a class="nav-link text-size" id="draft" onclick="changeTab(this,'{{$value->order_status_id}}')">{{$value->order_status_name}}</a>
						   <div class="slide"></div>
						</li>
					@endforeach
					<li class="nav-item">
					   <a class="nav-link text-size" id="draft" onclick="changeTab(this,'drivers')">Drivers</a>
					   <div class="slide"></div>
					</li>
				</ul>
                
				<div id="tabledata"></div>
						

			
		</div>
 </section>

@endsection

@section('scriptcode_three')
<script type="text/javascript">
	var orderStatus = "";
	var loader = "<div class='col-xl-12 col-md-12 col-sm-12'>"+
					"<div class='preloader3 loader-block text-center'>"+
						"<div class='circ1 bg-success'></div>"+
						"<div class='circ2 bg-success'></div>"+
						"<div class='circ3 bg-success'></div>"+
						"<div class='circ4 bg-success'></div>"+
					"</div></div>";
					
	$('#fromdate,#todate').bootstrapMaterialDatePicker({
		format: 'YYYY-MM-DD',
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
	getOrders();
	
	$("#search").click(function(){
		if(orderStatus != "drivers"){
			getOrders();
		}else{
			getDriversOrders()
		}
		
	});
	function getOrders(){

		$.ajax({
			url: "{{url('/service-providers-orders')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				receipt:$('#receipt').val(),
				from:$('#fromdate').val(),
				to:$('#todate').val(),
				serviceprovider:$('#serviceprovider').val(),
				status : orderStatus,
			},
			beforeSend: function() {
				$("#tabledata").empty();
				$("#tabledata").append(loader);
			},
			success:function(result){
				console.log(result)
				$("#tabledata").empty();
				$("#tabledata").append(result)
			}
		});
	}
	
	function getDriversOrders(){

		$.ajax({
			url: "{{route('driver.assign')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				from:$('#fromdate').val(),
				to:$('#todate').val(),
			},
			beforeSend: function() {
				$("#tabledata").empty();
				$("#tabledata").append(loader);
			},
			success:function(result){
				console.log(result);
				$("#tabledata").empty();
				$("#tabledata").append(result)
			}
		});
	}
	
	
	function providerChange(id,dropdownid,receipt,oldserviceprovider){
		$.ajax({
			url: "{{url('/update-service-providers')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				id:id,
				serviceprovider:$("#"+dropdownid).val(),
				receipt:receipt,
				oldserviceprovider:oldserviceprovider,
			},
			beforeSend: function() {
				$("#tabledata").empty();
				$("#tabledata").append(loader);
			},
			success:function(result){
				if(result.success == 1){
					swal_alert("Success","Service Provider changed successfully","success","false")
				}else{
					swal_alert("Error","Service Provider change failed","error","false")
				}
			}
		});
	}
	
	function statusChange(id,receipt)
	{
		$.ajax({
			url: "{{url('/update-order-status')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				status:$("#"+id).val(),
				receipt:receipt,
			},
			beforeSend: function() {
				$("#tabledata").empty();
				$("#tabledata").append(loader);
			},
			success:function(result){
				if(result.success == 1){
					swal_alert("Success","Order Status changed successfully","success","false")
				}else{
					swal_alert("Error","Order Status change failed","error","false")
				}
			}
		});
	}
	
	function swal_alert(title,msg,type,mode) {

		swal({
			title: title,
			text: msg,
			type: type
		}, function (isConfirm) {
			if (isConfirm) {
				if (mode === true) {
					window.location = "{{url('/view-purchases')}}";
				}
			}
		});
	}
	
	function changeTab(arg,type){
		$(".nav-link").removeClass('active');
		$(arg).addClass('active');
		$('.drp-selected').text('');
		$('.drp-selected').text('');
		orderStatus = type;
		if(type != "drivers"){
			getOrders();
		}else{
			getDriversOrders()
		}
		 
	}
	
	function showReceipt(ReceiptNo) {
        window.open("{{url('print')}}"+"/"+ReceiptNo);
    }
	
	$("#assignOrders").click(function(){
		rem_id = [];
		$(".chkbx").each(function( index ) {
			if($(this).is(":checked")){
			   if(jQuery.inArray($(this).data('id'), rem_id) == -1){
				   rem_id.push($(this).data('id'));
			   }
			}
		});
		console.log(rem_id)
		
		$.ajax({
			url: "{{route('sp.assign')}}",
			type: 'POST',
			dataType:"json",
			data:{_token:"{{ csrf_token() }}",
				orders:rem_id,
				driver:$("#driver").val(),
				vehicle:$("#vehicle").val(),
				loader:$("#loader").val(),
				checker:$("#checker").val(),
			},
			beforeSend: function() {
				$("#tabledata").empty();
				$("#tabledata").append(loader);
			},
			success:function(result){
				// console.log(result)
				$("#orderAssign").css("display", "none");
				if(result.status == 200){
					swal_alert('Success!',result.message,'success',false);
					getOrders();
				}
				if(result.status == 500){
					swal_alert('Error!',result.message,'error',false);
				}
			}
		});
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
   
   function swal_alert(title,msg,type,mode){
    
      swal({
            title: title,
            text: msg,
            type: type
         },function(isConfirm){
         if(isConfirm){
            if(mode==true){
              window.location="{{ route('invent_dept.index') }}";
            }
          }
      });
	}
	
	function showItems(driverId,drivertime)
	{
		console.log(drivertime)
		$.ajax({
			url: "{{route('driver.details')}}",
			type: 'POST',
			data:{_token:"{{ csrf_token() }}",
				driverId:driverId,
				time:drivertime,
				from:$('#fromdate').val(),
				to:$('#todate').val(),
			},
			beforeSend: function() {
				$("#tabledata").empty();
				$("#tabledata").append(loader);
			},
			success:function(result){
				console.log(result)
				$("#tabledata").empty();
				$("#tabledata").append(result)
			}
		});
	}
	</script>
@endsection