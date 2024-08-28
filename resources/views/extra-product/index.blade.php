@extends('layouts.master-layout')
@section('title','Addons')

@section('content')
<section class="panels-wells">
	<div class="card">
		<div class="card-header">
		<h5 class="card-header-text" id="title-hcard">Addons Categories</h5>
		</div>
		<div class="card-block">
			<form method="POST" id="deptform" class="form-horizontal">
			@csrf
				<div class="row">
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Addon Name:</label>
							<input class="form-control" type="text" name="name" id="name" />
							<div class="form-control-feedback text-danger" id="name_alert"></div>
						</div>
					</div>

					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Show Website Addon Name:</label>
							<input class="form-control" type="text" name="show_website_name" id="show_website_name" />
							<div class="form-control-feedback text-danger" id="show_website_name_alert"></div>
						</div>
					</div>					
					
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Addon Type</label>
							<select class="form-control select2" id="type" name="type">
								<option value="single">Single</option>
								<option value="multiple">Multiple</option>
							</select> 
							<div class="form-control-feedback text-danger" id="type_alert"></div>
						</div>
					</div>
				   </div>	
                   <div class="row">
					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Required Option</label>
							<select class="form-control select2" id="is_required" name="is_required">
								<option value="">Select</option>
								<option value="1">Required</option>
								<option value="0">Optional</option>
							</select> 
							<div class="form-control-feedback text-danger" id="type_alert"></div>
						</div>
					</div>					

					<div class="col-lg-3 col-md-3">
						<div class="form-group">
							<label class="form-control-label">Description</label>
							<textarea class="form-control" id="description" name="description" type="text" rows="2"></textarea>
							<span class="form-control-feedback text-danger" id="description_alert"></span>
						</div>
					</div>
                  </div>
					
						<div class="form-group row">
							<button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus" 
							></i>&nbsp; Save</button>.
							<button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
							></i> Clear</button>
						</div>
					
				
			</form>
		</div>
	</div>
</section>

<section class="panels-wells">
	<div class="card">
		<div class="card-header">
			<h5 class="card-header-text" id="title-hcard">Addons Categories List</h5>
		</div>
		<div class="card-block">
			<div class="project-table">
			<table id="mainTable" class="table table-striped full-width">
				<thead>
					<tr>
					   <th>Name</th>
					   <th>Type</th>
					   <th>Addons</th>
					   <th>Is Required</th>
					   <th>Description</th>
					   <th>Limit</th>
					   <th>Action</th>
					</tr>
				</thead>
				<tbody>
				  @if($categories)
					@foreach($categories as $category)
						   <tr style="cursor: pointer;">
							 <td onclick="editAddonCategory('{{$category->id}}','{{$category->name}}','{{$category->show_website_name}}','{{$category->type}}','{{$category->is_required}}','{{$category->description}}','{{$category->addon_limit}}')">{{ $category->name }}</td>
							 <td>{{ $category->type}}</td>
							 <td onclick="editaddons('{{$category->id}}','{{$category->addons}}')" >{{ $category->addons->pluck("name")->implode(',')}}</td>
							 <td>{{ $category->is_required == 1 ? 'Yes' : 'No' }}</td>
							 <td>{{ $category->description}}</td>
							 <td>{{ $category->addon_limit}}</td>
							 <td class="action-icon">
								 <i onclick="addaddon('{{$category->id}}')" class="text-success text-center icofont icofont-plus" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Addons"></i>
								 <i onclick="deleteAddonCategory('{{$category->id}}','{{$category->addons->count()}}')" class="text-danger text-center icofont icofont-trash" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Addons"></i>
						   </td>            
						 </tr>
				   @endforeach
				 @endif
				</tbody>
				 </table>
			</div>
		</div>
	</div>
<div class="modal fade modal-flex" id="edit-category-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Edit Addon Category</h4>
			</div>
		<div class="modal-body">
			<form id="edit-addon-form" method="POST" >
				@csrf
				<input type="hidden"  name="category_id" id="category_id" class="form-control" />
				<div class="row">
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Name:</label>
							<input type="text"  name="edit_addon_name" id="edit_addon_name" class="form-control" />
							<div class="form-control-feedback text-danger message" id="edit_addon_name_alert"></div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Show Website Addon Name:</label>
							<input type="text"  name="edit_show_website_name" id="edit_show_website_name" class="form-control" />
							<div class="form-control-feedback text-danger message" id="edit_show_website_name_alert"></div>
						</div>
					</div>					
					<div class="col-lg-12 col-md-12">
						<div class="form-group">
							<label class="form-control-label">Addon Type</label>
							<select class="form-control select2" id="edit_type" name="edit_type">
								<option value="single">Single</option>
								<option value="multiple">Multiple</option>
							</select> 
							<div class="form-control-feedback text-danger message" id="edit_type_alert"></div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12">
						<div class="form-group">
							<label class="form-control-label">Required Option</label>
							<select class="form-control select2" id="edit_is_required" name="edit_is_required">
								<option value="">Select</option>
								<option value="1">Required</option>
								<option value="0">Optional</option>
							</select>
							<div class="form-control-feedback text-danger message" id="edit_type_alert"></div>
						</div>
					</div>
					<div class="col-lg-12 col-md-12">
						<div class="form-group">
							<label class="form-control-label">Description</label>
							<textarea class="form-control" id="edit_description" name="edit_description" type="text" rows="2"></textarea>
							<span class="form-control-feedback text-danger message" id="edit_description_alert"></span>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Limit:</label>
							<input type="text"  name="edit_addon_limit" id="edit_addon_limit" class="form-control" value="0"/>
							<div class="form-control-feedback text-danger message" id="edit_addon_limit_alert"></div>
						</div>
					</div>
				</div>   
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success waves-effect waves-light" onClick="updateAddonCategory()">Update Addon Category</button>
		</div>
		</div>
	</div>
</div> 
<div class="modal fade modal-flex" id="subdepartment-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add Sub Department</h4>
			</div>
		<div class="modal-body">
			<form id="addon-form" method="POST" >
				@csrf
				<input type="hidden"  name="addon_category_id" id="addon_category_id" class="form-control" />
				<div class="row">
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Name:</label>
							<input type="text"  name="addon_name" id="addon_name" class="form-control" />
							<div class="form-control-feedback text-danger" id="addon_name_alert"></div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Price:</label>
							<input type="text"  name="addon_price" id="addon_price" class="form-control" value="0"/>
							<div class="form-control-feedback text-danger" id="addon_price_alert"></div>
						</div>
					</div>
				</div>   
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="insertAddon()">Add Addon</button>
		</div>
		</div>
	</div>
</div> 

<div class="modal fade modal-flex" id="addons-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Edit Addons</h4>
				<hr/>
			</div>
			<div class="modal-body">
				<input type="hidden" name="addon_category_id" id="addon_category_id" value="0" />


				<table class="table full-width sb_tble">
					<thead>
						<tr>
							<th>Name</th>
							<th>Price</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<a href="javascript:void()" onclick="window.location.href='{{route('addon-category.index')}}'"   data-dismiss="modal" class="btn btn-success waves-effect waves-light">OK</a>
			</div>
		</div>
	</div>
</div> 
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
	$(".select2").select2();
	
	$("#btn_clear").on('click',function(){
		$("#deptform")[0].reset();
	});
	
	$("#btn_save").on('click',function(event){
		if($("#name").val() == ''){
			 $("#name").focus();
			 $("#name_alert").text('Addon name is required.');
		}else if($("#show_website_name").val() == ''){
			 $("#show_website_name").focus();
			 $("#show_website_name_alert").text('Enter the addon name show in website');
		}else if($("#is_required").val() == ''){
			 $("#is_required").focus();
			 $("#is_required_alert").text('Please select this '+$("#name").val()+' addon is required?');
		}else{
			$.ajax({
				url:'{{ route("addon-category.store") }}',
				type:"POST",
				data:$('#deptform').serialize(),
				dataType:"json",
				success:function(response){
				// console.log(response)
					if(response.status != 200){
						if(response.contrl != ""){
							$("#"+response.contrl).focus();
							$("#"+response.contrl+"_alert").html(response.msg);
						}
						swal_alert('Alert!',response.msg,'error',false); 

					}else {
						$("#name_alert").html('');
						swal_alert('Successfully!',response.msg,'success',true);
					}
				}
			});
		}
	});
	
	function addaddon(id){
        $('#name').val('');
        $('#price').val('');
        $('#addon_category_id').val(id);
        $("#subdepartment-modal").modal("show");
	}
	
	function insertAddon()
	{
		if($("#addon_name").val() == ""){
			 $("#addon_name").focus();
			 $("#addon_name_alert").html('Addon name is required.');
		}else{
			$.ajax({
				url:'{{ route("addons.store") }}',
				type:"POST",
				data:$('#addon-form').serialize(),
				dataType:"json",
				success:function(response){
				// console.log(response)
					if(response.status != 200){
						if(response.contrl != ""){
							$("#"+response.contrl).focus();
							$("#"+response.contrl+"_alert").html(response.msg);
						}
						swal_alert('Alert!',response.msg,'error',false); 

					}else {
						$("#name_alert").html('');
						swal_alert('Successfully!',response.msg,'success',true);
					}
				}
			});
		}
	}
	
	function swal_alert(title,msg,type,mode){

	  swal({
			title: title,
			text: msg,
			type: type
		 },function(isConfirm){
		 if(isConfirm){
			if(mode==true){
			  window.location="{{ route('addon-category.index') }}";
			}
		  }
	  });
	}
	
	function editaddons(categoryId,AddonsList)
	{
		let addons = JSON.parse(AddonsList)
		$("#addons-modal").modal("show");
		$.each(addons,function(index,value){
			$(".sb_tble tbody").append(
			  "<tr>" +
				"<td><input type='text' value='"+value.name +"' class='form-control' id='name_"+value.id+"'/>"+
				"<td><input type='text' value='"+value.price+"' class='form-control' id='price_"+value.id+"'/>"+
				"<div class='form-control-feedback text-danger' id='tbx_"+value.id+"_alert'></div>"+"</td>"+
				"<td class='action-icon'><i onclick='update("+ value.id+","+categoryId+")' class='text-warning icofont icofont-pencil f-24'></i>&nbsp;<i onclick='deleteAddon("+ value.id+")' class='text-danger icofont icofont-trash f-24'></i></td>"+
			  "</tr>"
			  );
		})
		 
	}
	
	function update(id,addonId)
	{
	  if($("#name_"+id).val() == ""){
		  $("#name_"+id+"_alert").html('Name is required.').addClass('text-danger');
	  }
	  // else if($("#price_"+id).val() == ""){
			//   $("#price_"+id+"_alert").html('price is required.').addClass('text-danger');
	  // }
	  else {
		  $("#tbx_"+id+"_alert").html('').removeClass('text-danger');
		  $.ajax({
			url:'{{ route("addons.update") }}',
			type:"POST",
			data:{_token:"{{ csrf_token()}}",id:id,name:$("#name_"+id).val(),price:$("#price_"+id).val(),category:addonId},
			dataType:"json",
			success:function(response){
	 
			 if(response.state == 1){
				if(response.contrl != "")
				{
					$("#"+response.contrl).focus();
				}
				swal_alert('Alert!',response.msg,'error',false);
				$("#tbx_"+id+"_alert").html(response.msg).addClass('text-danger');

			 }else {
				swal_alert('Success!',response.msg,'success',false);
			 }
		   }
		});
	  }
	}
	
	function deleteAddon(id)
	{
		swal({
                title: "DELETE Extra Product",
                text: "Do you want to delete extra product?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                    if(id > 0){
                        $.ajax({
                            url: "{{route('addons.delete')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",id:id},
                            success:function(resp){

                                if (resp.status == 200) {
                                    swal({
                                        title: "Success!",
                                        text: "Addon deleted Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/addon-category')}}";
                                        }
                                    });
                                }else{
                                    swal("Alert!", "Addons not Deleted:)", "error");
                                }
                            }

                        });
                    }
                }else{
                    swal({
                        title: "Cancel!",
                        text: "Addons are safe :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{url('/addon-category')}}";

                        }
                    });
                }
            });
	}
	
	
	function editAddonCategory (id,name,sw_addon_name,type,is_requiredMode,description,limit)
	{
		$("#edit_addon_name").val(name);
		$("#edit_show_website_name").val(sw_addon_name);
		$("#edit_type").val(type).change();
		$("#edit_description").val(description);
		$("#category_id").val(id);
		$("#edit_addon_limit").val(limit);
		$("#edit-category-modal").modal("show");

		$("#edit_is_required :selected").val(is_requiredMode);
	}
	
	function updateAddonCategory()
	{
	  if($("#edit_addon_name").val() == ""){
		  $("#edit_addon_name_alert").html('Name is required.').addClass('text-danger');
	  }else if($("#edit_show_website_name").val() == ""){
		  $("#edit_show_website_name_alert").html('Show website addon name is required.').addClass('text-danger');
	  }else if($("#edit_type").val() == ""){
		  $("#edit_type_alert").html('Type is required.').addClass('text-danger');
	  }else if($("#edit_is_required").val() == ""){
		  $("#edit_is_required_alert").html('Please select this '+$("#name").val()+' addon is required?').addClass('text-danger');
	  }else {
		  $(".message").html('').removeClass('text-danger');
		  $.ajax({
			url:'{{ route("addon-category.update") }}',
			type:"POST",
			data:{_token:"{{ csrf_token()}}",id:$("#category_id").val(),name:$("#edit_addon_name").val(),show_website_addoname:$("#edit_show_website_name").val(),type:$("#edit_type").val(),is_required:$("#edit_is_required").val(),description:$("#edit_description").val(),limit:$("#edit_addon_limit").val()},
			dataType:"json",
			success:function(response){
	            
			 if(response.state == 1){
				if(response.contrl != "")
				{
					$("#"+response.contrl).focus();
				}
				swal_alert('Alert!',response.msg,'error',false);
				$("#tbx_"+id+"_alert").html(response.msg).addClass('text-danger');

			 }else {
				swal_alert('Success!',response.msg,'success',true);
			 }
		   }
		});
	  }
	}
	
	function deleteAddonCategory(id,count)
	{		
		swal({
                title: "DELETE Extra Product",
                text: "Do you want to delete this category ? There are total "+count+" extra product in this category.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                    if(id > 0){
                        $.ajax({
                            url: "{{route('addon-category.delete')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",id:id},
                            success:function(resp){

                                if (resp.status == 200) {
                                    swal({
                                        title: "Success!",
                                        text: "Extra product deleted Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{route('extraProductList')}}";
                                        }
                                    });
                                }else{
                                    swal("Alert!", "Extra Product not Deleted:)", "error");
                                }
                            }

                        });
                    }
                }else{
                    swal({
                        title: "Cancel!",
                        text: "Extra product are safe :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{ route('extraProductList') }}";

                        }
                    });
                }
            });
		
	}
</script>

@endsection