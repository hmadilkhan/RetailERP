@extends('layouts.master-layout')

@section('title','HR Steam Products')

@section('breadcrumtitle','HR Steam Products')

@section('navmanage','active') 

@section('navtaxslabs','active')

@section('content')

 <section class="panels-wells">
 <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Steam Product</h5>
            </div>
            <div class="card-block">

                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Product Name:</label>
                            <input type="text" name="product" id="product" class="form-control">
                            <div id="product_message" class="form-control-feedback text-danger"></div>
                        </div>
                    </div>
					<div class="col-lg-4 col-md-4">
					 <label class="form-control-label"></label>
						<div class="button-group ">
						
							<button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light m-t-5 " onclick="submit()">
								<i class="icofont icofont-plus"> </i>
								Create Product
							</button>
						</div>
                    </div>
                </div>
            </div>
        </div>
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Product List</h5>
         </div>      
       <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active</div>
                  </div>
                  <br/>
                      <br/>

     <table id="tblpffund" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>ID</th>
               <th>Product</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
		 @if($lists)
            @foreach($lists as $value)
                    <tr>
                          <td>{{$value->id}}</td>
                          <td>{{$value->name}}</td>
                          <td>{{$value->status}}</td>
						  <td>
							<a onclick='change_name("{{$value->id}}","{{$value->name}}")' class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i class='icofont icofont-ui-edit'></i></a>
							<i class='icofont icofont-ui-delete text-danger f-18 ' onclick='deleteCall("{{$value->id}}")' data-id='"+value.id+"' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>
						  </td>
                    </tr>
			@endforeach
		 @endif
         </tbody>
     </table>
  </div>
</div>
</section>
<div class="modal fade modal-flex" id="change-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 id="mod-title" class="modal-title">Change Price</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<input type="hidden" id="id" name="id" />
					<div class="col-md-12">
						<label class="form-control-label">Change Price</label>
						<input type="text" name="modal_price" id="modal_price" class="form-control">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" onclick="update('changeprice')" class="btn btn-success waves-effect waves-light">Change Price</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade modal-flex" id="change-name-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 id="mod-title" class="modal-title">Change Name</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<input type="hidden" id="id" name="id" />
					<div class="col-md-12">
						<label class="form-control-label">Change Name</label>
						<input type="text" name="modal_name" id="modal_name" class="form-control">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success waves-effect waves-light" onclick="update('changename')">Change Name</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
	function submit()
	{
		$("#product_message").html("");
		$("#price_message").html("");
		if($("#product").val() == ""){
			$("#product_message").html("Please enter product name.");
		}else if($("#price").val() == ""){
			$("#price_message").html("Please enter product price.");
		}else{
			$.ajax({
				url: "{{url('/insert-steam-press-products')}}",
				type: 'POST',
				data:{
					_token:"{{ csrf_token() }}",
					product:$("#product").val(),
					price:$("#price").val(),
				},
				success:function(resp){
					if(resp.status == 200){
						 $("#charges").val("");
						 location.reload();
					}
					if(resp.status == 500){
						$("#charges_message").html(resp.message);
					}
				}

			});
		}
	}
    $('#tblpffund').DataTable( {
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search..',
          lengthMenu: '<span></span> _MENU_'
        }
    } );
	
	function change_price(id,price)
	{
		$("#change-modal").modal("show");
		$("#id").val(id);
		$("#modal_price").val(price);
	}
	
	function change_name(id,name)
	{
		$("#change-name-modal").modal("show");
		$("#id").val(id);
		$("#modal_name").val(name);
	}
	
	function update(mode){
		
		$.ajax({
				url: "{{url('/update-steam-press-products')}}",
				type: 'POST',
				data:{
					_token:"{{ csrf_token() }}",
					mode : mode,
					id : $("#id").val(),
					product:$("#modal_name").val(),
					price:$("#modal_price").val(),
				},
				success:function(resp){
					if(resp.status == 200){
						 $("#modal_name").val("");
						 $("#modal_price").val("");
						 $("#id").val("");
						 location.reload();
					}
					if(resp.status == 500){
						$("#charges_message").html(resp.message);
					}
				}

			});
	}
	
	function deleteCall(id){
	swal({
			title: "Are you sure?",
			text: "This item will mark as inactive and will not be further available for sales!",
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
					url: "{{ url('delete-steam-press-products')}}",
					type: 'POST',
					data:{_token:"{{ csrf_token() }}",id:id,status:2},
					success:function(resp){

						if(resp == 1){
							swal({
								title: "Deleted",
								text: "Product Successfully Inactive.",
								type: "success"
							},function(result){
								if(result.status == 200){
									location.reload();
								}
							});
						}
					}

				});

			}else {
				swal("Cancelled", "Your product is safe :)", "error");
			}
		});
	}
 


function edit(id,min,max,per,year){
$('#update-modal').modal('show');
$('#slabmin').val(min);
$('#slabmax').val(max);
$('#taxpercentage').val(per);
$('#year').val(year);
$('#taxid').val(id);
}

  



$('#chkactive').change(function(){
  if (this.checked) {
    getTable(0)
  }
  else{
	getTable(1)
  }
});

function getTable(status)
{
	$.ajax({
            url: "{{url('/get-hr-products')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",status : status
          },
            success:function(result){
                if(result){
                   $("#tblpffund tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblpffund tbody").append(
                          "<tr>" +
                            "<td>"+result[count].id+"</td>" +  
                            "<td>"+result[count].name+"</td>" +  
                            "<td>"+result[count].price+"</td>" +  
                            "<td>"+result[count].status+"</td>" +   
							"<td>" +
								"<a onclick='show_barcode()' class='p-r-10 f-18 text-success' data-toggle='tooltip' data-placement='top' title='Change Price' data-original-title='Change Price'><i class='icofont icofont-barcode'></i></a>"+
								"<a onclick='edit_route()' class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i class='icofont icofont-ui-edit'></i></a>"+
								"<i class='icofont icofont-ui-delete text-danger f-18 ' onclick='deleteCall("+result[count].id+")' data-id='"+result[count].id+"' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
							"</td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          });
}

function reactive(id){
swal({
          title: "Are you sure?",
          text: "You want to Re-Active Tax Slab!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "yes plx!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/reactive-taxslab')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        taxid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Re-Active",
                                        text: "Tax Slab Re-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/showtaxslabs-active') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled:)", "error");
           }
        });
}






</script>

@endsection
