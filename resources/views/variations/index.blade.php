@extends('layouts.master-layout')

@section('title','Variations')

@section('breadcrumtitle','View Variations')
@section('navinventory','active')
@section('navinvent_variation','active')

@section('content')

<section class="panels-wells">

  @if(Session::has('success'))
       <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  @if(Session::has('error'))
       <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

               <div class="card justify-content-center">
                  <div class="card-header">
                     <h5 class="card-header-text" id="title-hcard"> Create Variation</h5>
                  </div>
                  <div class="card-block">

    <form method="post" id="variatform" class="form-horizontal" enctype="multipart/form-data">
      @csrf
       
        <div class="row">

        <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Name</label>
                  <input class="form-control" type="text"
                   name="variat_name" id="variat_name" />
                   <div class="form-control-feedback text-danger" id="variat_name_alert"></div>
              </div>
            </div>

        <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Show website name</label>
                  <input class="form-control" type="text"
                   name="show_website_name" id="show_website_name" />
                   <div class="form-control-feedback text-danger" id="show_website_name_alert"></div>
              </div>
            </div>            

             <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Values</label>
                   <div class="tags_add">
                      <input class="form-control" id="variat_values" name="variat_values" type="text"  />
                    </div>
                   <span class="form-control-feedback text-danger" id="variat_values_alert"></span>
              </div>
            </div>
        </div>    
      <div class="row">
      <div class="col-lg-3 col-md-3">
        <label class="form-control-label">Required option</label>
        <select name="required_status" id="required_status" data-placeholder="Select" class="form-control">
          <option value="">Select</option>
              <option {{ old('required_status') == 1 ? 'selected' : '' }} value="1">Required</option>
              <option {{ old('required_status') == 0 ? 'selected' : '' }} value="0">Optional</option>
        </select>
          <span class="form-control-feedback text-danger" id="required_status_alert"></span>
       </div>             

      
      <div class="col-lg-3 col-md-3">
        <label class="form-control-label">Variation Type</label>
        <select name="type" id="variation_type" data-placeholder="Select" class="form-control">
          <option value="">Select</option>
              <option {{ old('type') == 'single' ? 'selected' : '' }} value="single">Single</option>
              <option {{ old('type') == 'multiple' ? 'selected' : '' }} value="multiple">Multiple</option>
        </select>
          <span class="form-control-feedback text-danger" id="variation_type_alert"></span>
       </div>             
      </div>      
      
              <div class="form-group row">
                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Variation"><i class="icofont icofont-plus" 
                  ></i>&nbsp; Save</button>
                  <!--  <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error"
                  </i> Clear</button>-->
              </div>
         </form>
            
                  </div>
               </div>
            </section>    

 <section class="panels-wells">
  <div class="row">

           <div class="col-lg-12 col-md-12">
              <div class="form-group">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Lists</h5>
           <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
         </div>      
       <div class="card-block">
       

    
           <div class="project-table">
     <table id="mainTable" class="table table-striped full-width">
         <thead>
            <tr>
               <th class="d-none">Code</th>
               <th>Name</th>
               <th>Values</th>
               <th>Is Required</th>
               <th>type</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
              @if($variations)
                @foreach($variations as $val)
                       <tr id="tblRow{{ $val->id }}">
                         <td class="d-none"> <input type="hidden" value="{{ $val->show_website_name }}" id="col_hiddename{{ $val->id }}"> {{ $val->id }}</td>
                         <td id="col_name{{ $val->id }}">{{ $val->name }}</td>
                         <td class="pointer" onclick="edit_all_variation_values({{ $val->id }},'{{ addslashes($val->name) }}')" id="mtblvalueCell{{ $val->id }}">
                            @if($variat_value)
                              @foreach($variat_value as $sb_val)
                                 @if($sb_val->parent == $val->id)
                                 <label class="badge badge-primary pointer p-10" id="label_variate_{{ $sb_val->id }}">{{ $sb_val->name }}</label>
                                 @endif
                              @endforeach
                            @endif  
                         </td>
                         <td id="col_is_required{{ $val->id }}" data-value="{{ $val->required_status }}">{{ $val->required_status == 1 ? 'Requried' : 'Optional' }}</td>
                         <td id="col_type{{ $val->id }}">{{ $val->type }}</td>
                         <td class="action-icon">
                             
                             <i onclick="addVariat({{ $val->id }})" class="text-primary text-center icofont icofont-plus" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Add"></i>
                             
                             <i onclick="edit({{ $val->id }})" class="text-warning text-center icofont icofont-edit" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"></i>

                             <i onclick="remove('{{ $val->name }}',{{ $val->id }})" class="text-danger text-center icofont icofont-trash" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove"></i>

                             <form action="{{ route('DestroyVariat',$val->id) }}" method="post" id="removeForm{{ $val->id }}">
                               @csrf
                               @method('DELETE')
                               <input type="hidden" name="mode" id="mode{{ $val->id }}" value="0">
                             </form>
                       </td>            
                     </tr>
               @endforeach
             @endif
                     </tbody>
                 </table>
        </div>
         </div>
          

    </div>
      </div>
          </div>
    


    </div>

   
<div class="modal fade modal-flex" id="edit-variat-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Edit Variation Detail</h4>
			</div>
		<div class="modal-body">
			<form id="edit-variation-form" method="POST">
				@csrf
				<input type="hidden" name="id" id="md_id">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Name:</label>
							<input type="text"  name="edit_variat_name" id="edit_variat_name" class="form-control" />
							<div class="form-control-feedback text-danger message" id="edit_variat_name_alert"></div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Show Website Name:</label>
							<input type="text"  name="edit_show_website_name" id="edit_show_website_name" class="form-control" />
							<div class="form-control-feedback text-danger message" id="edit_show_website_name_alert"></div>
						</div>
					</div>					
					<div class="col-lg-12 col-md-12">
						<div class="form-group">
							<label class="form-control-label">Variation Type</label>
							<select class="form-control select2" id="edit_vtype" name="edit_vtype">
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

				</div>   
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success waves-effect waves-light" id="btn_update">Update</button>
		</div>
		</div>
	</div>
</div> 


<div class="modal fade modal-flex" id="add-variat-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add Variation</h4>
			</div>
		<div class="modal-body">
				<input type="hidden" id="id_add_variatmd" name="id">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group"> 
							<label class="form-control-label">Variation value name:</label>
							<input type="text"  name="variation_value_md" id="variation_value_md" class="form-control" />
							<span class="text-danger" id="variation_value_md_alert"></span>
						</div>
					</div>
				</div>   
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success waves-effect waves-light" id="add_variation_value">Add</button>
		</div>
		</div>
	</div>
</div> 


<div class="modal fade modal-flex" id="edit-allvariation-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="md_title_variation_values"></h4>
			</div>
		<div class="modal-body">
			<table class="table" id="tbl_md">
			    <thead>
			        <tr>
			            <th>Name</th>
			            <th>Action</th>
			        </tr>
			    </thead>
			    <tbody></tbody>
			</table> 
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal">Close</button>
		</div>
		</div>
	</div>
</div> 
   
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">


   $(".select2").select2({
       minimumResultsForSearch: -1
   });

   $("#variat_values").tagsinput({
     maxTags: 20,
    });

    
      $('#mainTable').DataTable( {

        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Variations',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );
  
 function removeFormAlert(){
     $("#variat_name_alert,#variat_values_alert,#show_website_name_alert,#required_status_alert,#variation_type_alert").text(''); 
 }


 $("#btn_save").on('click',function(event){

   var process_md = true;
   
   removeFormAlert();

    if($("#variat_name").val().length === 0 ){
         $("#variat_name").focus();
         $("#variat_name_alert").text('Variation name is required.');
         process_md = false;
    }

    if($("#variat_values").val().length === 0 ){
         $("#variat_values").focus();
         $("#variat_values_alert").text('Variation values is required.');   
         process_md = false;   
    }

    if($("#show_website_name").val().length === 0 ){
         $("#show_website_name").focus();
         $("#show_website_name_alert").text('Variation values is required.');   
         process_md = false;   
    }

    if($("#required_status").val().length === 0 ){
         $("#required_status").focus();
         $("#required_status_alert").text('Select field is required.');   
         process_md = false;   
    }
    
    if($("#variation_type").val().length === 0 ){
         $("#variation_type").focus();
         $("#variation_type_alert").text('Select field is required.');   
         process_md = false;   
    }    
    
    if(process_md){
                   $.ajax({
                    url:'{{ route("StoreVariat") }}',
                    type:"POST",
                    data:$('#variatform').serialize(),
                    dataType:"json",
                    success:function(resp,status){
                      if(resp.status == 200){
                              window.location = "{{ route('listVariation') }}";
                      }else{
                          if(resp.control !== undefined){
                              $("#"+resp.control).focus();
                              $("#"+resp.control+"_alert").text(resp.msg);
                          }
                      }
                    }
                  });
              
            }
     });
     
     function addVariat(id){
         $('#add-variat-modal').modal('show');
         $("#id_add_variatmd").val(id);
         $("#variation_value_md_alert").text('');
     }
     
     $("#add_variation_value").on('click',function(){
         
         if($("#variation_value_md").val() != ''){
                 $("#variation_value_md_alert").text('');
                  $.ajax({
                            url: "{{ route('singleVariationAdd') }}",
                            type: 'POST',
                            data:{'_token':'{{ csrf_token() }}',value:$("#variation_value_md").val(),id:$("#id_add_variatmd").val()},
                            success:function(resp){
                                console.log(resp)
                                 if(resp.status == 200){
                                      window.location = "{{ route('listVariation') }}";
                                 }else{
                                     notify('Error! '+resp.msg, 'error');
                                     
                                      if(resp.control !== undefined){
                                          $("#"+resp.control).focus();
                                          $("#"+resp.control+"_alert").text(resp.msg);
                                      }                                     
                                 }
                            }

                        });
         }else{
             $("#variation_value_md").focus();
             $("#variation_value_md_alert").text('Field is required!');
         }
     })

     function edit(id){
         $("#md_id").val(id);
         $("#edit_variat_name").val($("#col_name"+id).text());
         $("#edit_show_website_name").val($("#col_hiddename"+id).val());
         $("#edit_is_required").val($("#col_is_required"+id).attr('data-value')).trigger('change');
         $("#edit_vtype").val($("#col_type"+id).text()).trigger('change');
         
         $('#edit-variat-modal').modal('show');
     }
     
     $("#btn_update").on('click',function(){
           $.ajax({
                    url: "{{ route('modifyVariat') }}",
                    type: 'POST',
                    data:$("#edit-variation-form").serialize(),
                    success:function(resp){
                         if(resp.status == 200){
                             $("#col_name"+$("#md_id").val()).text($("#edit_variat_name").val());
                             $("#col_is_required"+$("#md_id").val()).attr('data-value',$("#edit_is_required option:selected").val()).text($("#edit_is_required option:selected").text());
                             $("#col_type"+$("#md_id").val()).text($("#edit_vtype option:selected").text());  
                             notify('Success!', 'success');
                             $('#edit-variat-modal').modal('hide');
                         }else{
                                  if(resp.control !== undefined){
                                      $("#"+resp.control).focus();
                                      $("#"+resp.control+"_alert").text(resp.msg);
                                  }   
                                  
                             notify('Error! '+resp.msg, 'error');
                         }
                    }
    
                });  
     })     
     
     function edit_all_variation_values(id,name){
         $("#md_title_variation_values").text('Edit '+name+' values');
		$("#edit-allvariation-modal").modal("show");
            var pid = (id == '' ? 1 : id);
       
                   $.ajax({
                    url:'{{ route("listVariation") }}',
                    type:"GET",
                    data:{_token:'{{ csrf_token() }}',id:pid},
                    dataType:"json",
                    success:function(resp,status){
                        console.log(resp)
                        $("#tbl_md tbody").empty();
                        
                    		$.each(resp,function(index,value){
                    			$("#tbl_md tbody").append(
                    			  "<tr id='md_tbl_row"+value.id+"'>" +
                    				"<td><input type='text' value='"+value.name +"' class='form-control' id='name"+value.id+"'/>"+
                    				"<span class='text-danger' id='name"+value.id+"_alert'></span><span class='text-success' id='name"+value.id+"_success'></span>"+"</td>"+
                    				"<td class='action-icon'><button onclick='update("+value.id+","+value.parent+")' class='btn btn-primary m-r-1'>Update</button> <button onclick='remove_modal_tablecell("+value.id+","+value.parent+")' class='btn btn-danger'>Remove</button></td>"+
                    			  "</tr>"
                    			  );
                    		}) 
                    }
                  });		
		
        
     }
     
 function getAll_variation_values(pid){

                   $.ajax({
                    url:'{{ route("listVariation") }}',
                    type:"GET",
                    data:{_token:'{{ csrf_token() }}',id:pid},
                    dataType:"json",
                    success:function(resp,status){
                
                        $("#mtblvalueCell"+pid).empty();
                    		$.each(resp,function(index,value){
                    			$("#mtblvalueCell"+pid).append(
                    			    '<label class="badge badge-primary pointer m-r-1 p-10" id="#label_variate_'+value.id+'">'+value.name+'</label>'
                    			  );
                    		}) 
                    }
                  });		
		
        
     }    
     
     function update(id,parentID){
         if($("#name"+id).val() == ''){
             $("#name"+id).focus();
             $("#name"+id+"_alert").text('Field is required!');
         }else{
               $.ajax({
                        url: "{{ route('modifyVariatSubValue') }}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                            id:id,
                            name:$("#name"+id).val(),
                            parentId:parentID
                        },
                        success:function(resp){
                             if(resp.status == 200){  
                               notify('Success!', 'success');
                               getAll_variation_values(parentID);
                            //   $("#label_variate_"+id).html($("#name"+id).val());
                               $("#"+resp.control+"_success").text(resp.msg);
                               $("#"+resp.control+"_alert").text('');
                             }else{
                                  if(resp.control !== undefined){
                                      $("#"+resp.control).focus();
                                      $("#"+resp.control+"_alert").text(resp.msg);
                                      $("#"+resp.control+"_success").text('');
                                   } 
                             }   
                        }

                    }); 
         }
     }

        //Alert confirm
        function remove(name,id){

            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete "+ name +" variation?",
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
                        $("#removeForm"+id).submit();
                    }else {
                        swal("Cancelled", "Variations Safe :)", "error");
                    }
                });
        }
        
        
        function remove_modal_tablecell(id,parentId){

            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete "+$("#name"+id).val()+" variation?",
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
                            url:'{{ route("DestroyVariat_value") }}',
                            type:"POST",
                            data:{_token:'{{ csrf_token() }}',id:id,parentid:parentId,mode:1},
                            dataType:"json",
                            success:function(resp,status){
                              if(resp.status == 200){
                                   $("#md_tbl_row"+id).remove(); 
                                   $("#label_variate_"+id).remove();
                                   swal.close();
                                   notify('Success!', 'success');
                              }else{
                                  if(resp.control !== undefined){
                                      $("#"+resp.control).focus();
                                      $("#"+resp.control+"_alert").text(resp.msg);
                                  }
                              }
                            }
                          });                        
                    }else {
                        swal("Cancelled", "Variations Safe :)", "error");
                    }
                });
        }        
</script>

@endsection
