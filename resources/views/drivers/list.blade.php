@extends('layouts.master-layout')

@section('title','Drivers')

@section('content')
	<section class="panels-wells">
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Drivers List</h5>
				<a href="{{ route('driver.create') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Driver" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE DRIVER</a>
			</div>      
			<div class="card-block">
				<ul class="nav nav-tabs md-tabs " role="tablist">
					<li class="nav-item">
					   <a class="nav-link active" data-toggle="tab" onclick="changeTab('1')" role="tab">Active</a>
					   <div class="slide"></div>
					</li>
					<li class="nav-item">
					   <a class="nav-link" data-toggle="tab" onclick="changeTab('2')"  role="tab">InActive</a>
					   <div class="slide"></div>
					</li>
				</ul>
				<div id="tableData"></div>
			</div>
		</div>
	</section>
@endsection


@section('scriptcode_three')
	<script type="text/javascript">
		function changeTab(mode)
		{
			getDrivers(mode);
		}
		getDrivers(1);
		function getDrivers(mode)
		{
			 $.ajax({
				url: "{{route('driver.get')}}",
				type: 'POST',
				data:{_token:"{{ csrf_token() }}",
				mode:mode,
				},
				success:function(resp){
				  $("#tableData").empty();
				  $("#tableData").html(resp);
				}
			});
		}
		
		
		$('.alert-confirm').on('click',function(){
		var id= $(this).data("id");

		swal({
		  title: "Are you sure?",
		  text: "Your will not be able to recover this driver!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "delete it!",
		  cancelButtonText: "cancel plx!",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
		  if(isConfirm){
					 $.ajax({
						url: "{{route('driver.delete')}}",
						type: 'POST',
						data:{_token:"{{ csrf_token() }}",
						id:id,
						mode:2,
						},
						success:function(resp){
						  console.log(resp);
							if(resp == 1){
								 swal({
										title: "Deleted",
										text: "Driver successfully deleted.",
										type: "success"
								   },function(isConfirm){
									   if(isConfirm){
										window.location="{{route('driver.list')}}";
									   }
								   });
							 }
						}

					});
			  
		   }else {
			  swal("Cancelled", "Your Driver is safe :)", "error");
		   }
		});
		});
		
		function reactiveDriver(id){
		

		swal({
		  title: "Are you sure?",
		  text: "You want to active this driver!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-success",
		  confirmButtonText: "Active it!",
		  cancelButtonText: "cancel plx!",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm){
		  if(isConfirm){
					 $.ajax({
						url: "{{route('driver.delete')}}",
						type: 'POST',
						data:{_token:"{{ csrf_token() }}",
						id:id,
						mode:1,
						},
						success:function(resp){
							if(resp == 1){
								 swal({
										title: "Activated",
										text: "Driver activated successfully.",
										type: "success"
								   },function(isConfirm){
									   if(isConfirm){
										window.location="{{route('driver.list')}}";
									   }
								   });
							 }
						}

					});
			  
		   }else {
			  swal("Cancelled", "Your Driver is still inactive :)", "error");
		   }
		});
		}
	</script>
@endsection