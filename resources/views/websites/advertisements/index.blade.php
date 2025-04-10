@extends('layouts.master-layout')

@section('title','Website - Advertisements')

@section('breadcrumtitle','Advertisements')

@section('navwebsite','active')

@section('content')


<section class="panels-wells p-t-3">

  @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

  @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

<form name="sliderForm" action="{{ route('storeAdvertisement') }}" method="post" enctype="multipart/form-data">
     @csrf  
  <div class="row">
     <div class="col-md-6">
      <div class="card">
       <div class="card-header">
          <h5 class="card-header-text">Advertisement Detail Create</h5>
       </div>  
       <div class="card-block">
          <div class="form-group m-r-2">
            <label class="form-control-label">Website</label>
            <select name="website" id="website" data-placeholder="Select" class="form-control select2">
              <option value="">Select</option>
              @if($websites)
                 @php $oldWebsite = old('website');
                @foreach($websites as $val)
                  <option {{ old('website') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                @endforeach
              @endif
            </select>
            @error('website')
              <div class="form-control-feedback text-danger">Field is required please select it</div>
            @enderror
           </div>
           <div class="form-group m-r-2">
       			<label class="pointer">
    				<input type="radio" name="navigato" value="department"/>
    					<i class="helper"></i>Navigate to department
    			</label>
    			
       			<label class="pointer m-l-2">
    				<input type="radio" name="navigato" value="product"/>
    					<i class="helper"></i>Navigate to product
    			</label>			
           </div>
           <div class="d-none" id="departmentbox">
            <div class="form-group m-r-2">
              <label class="form-control-label">Department</label>
              <select name="depart" id="depart" data-placeholder="Select" class="form-control select2" disabled>
                <option value="">Select</option>
                 
              </select>
              @error('depart')
                <div class="form-control-feedback text-danger">Field is required please select it</div>
              @enderror
            </div>
          </div>
      
          <div class="d-none" id="productbox">
            <div class="form-group m-r-2">
              <label class="form-control-label">Select Department</label>
              <select id="depart_prod" data-placeholder="Select" class="form-control select2" disabled> 
                <option value="">Select</option>
              </select>
              @error('depart')
                <div class="form-control-feedback text-danger">Field is required please select it</div>
              @enderror
            </div>

            <div class="form-group m-r-2">
              <label class="form-control-label">Select Sub Department</label>
              <select id="subDepartment_prod" data-placeholder="Select" class="form-control select2" disabled> 
                <option value="">Select</option>
              </select>
              @error('sub-depart')
                <div class="form-control-feedback text-danger">Field is required please select it</div>
              @enderror
            </div>
            
            <div class="form-group m-r-2">
              <label class="form-control-label">Select Inventory</label>
              <select name="product" id="product" data-placeholder="Select" class="form-control select2" disabled> 
                <option value="">Select</option>
              </select>
              @error('product')
                <div class="form-control-feedback text-danger">Field is required please select it</div>
              @enderror
            </div>
          </div>      


           <button class="btn btn-primary m-l-1 m-t-1" id="btn_create" type="submit">Add</button>
    
      
        </div>
       </div>      
     </div>  
     
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5 class="card-header-text">Advertisement Image</h5>
           </div>  
         <div class="card-block">
             <div class="form-group @error('image') 'has-danger' @enderror m-r-2"> 
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="128" height="128" id="previewImg"/></br>
              <label for="image" class="form-control-label"></label></br>
    
              <label for="image" class="custom-file">
              <input type="file" name="image" id="image" class="custom-file-input">
              <span class="custom-file-control"></span>
              </label>
              @error('image')
                <div class="form-control-feedback text-danger">{{ $message }}</div>
              @enderror
            </div> 
        
            <div class="alert alert-info">
               Image dimensions should be 576 by 576 pixels and file size should not exceed 150 KB.
            </div>        
         </div>
       </div>    
     </div>    
     
  </div>
 </form>
  
</section>
<section class="panels-wells">
   
    @if(Session::has('error'))
         <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
         <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Websites Slider</h5>
         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th class="d-none">#</th>
               <th>Website</th>
               <th>Post</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
  
       @foreach($websiteSlider as $value)
				<tr>
				  <td class="d-none">{{ $value->id }}</td>
				  <td>{{ $value->name }}</td>
				  <td>
                      <img src="{{ asset('storage/images/website/advertisements/'.session('company_id').'/'.$value->website_id.'/'.$value->image) }}" alt=" {{ $value->image }}" width="128" height="64" id="slide{{ $value->id }}"/>
                  </td>
				  
				  <td class="action-icon">
				      
		            <i class="icofont icofont-ui-edit text-warning f-18 alert-confirm" onclick="editPost({{ $value->id }},{{ $value->website_id }},'{{ addslashes($value->name) }}','{{ addslashes($value->invent_department_id) }}','{{ addslashes($value->prod_id) }}','{{ addslashes($value->prod_depart) }}','{{ addslashes($value->prod_sb_depart) }}')" data-toggle="tooltip" data-placement="top" data-original-title="Edit"></i>
		            
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm m-l-1" onclick="warning({{ $value->id }},'{{ addslashes($value->name) }}')" data-toggle="tooltip" data-placement="top" data-original-title="Delete"></i>
					
					<form id="DestroyForm{{ $value->id }}" action="{{ route('destroyAdvertisement',[$value->id]) }}" method="post" class="d-none">
					    @csrf
					    @method('DELETE')
					    <input type="hidden" name="website" value="{{ $value->website_id }}">
					    <input type="hidden" name="id" value="{{ $value->id }}">
					</form>					
				  </td>
				</tr>
       @endforeach
         </tbody>
     </table>
  </div>
</div>
</section>


<div class="modal fade modal-flex" id="advPostEdit_Modal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title" id="title_md_mf">Edit Advertisement Post</h4>
               </div>
               <div class="modal-body">
                   <form id="editPostForm_md" action="{{ route('updateAdvertisement') }}" method="post" enctype="multipart/form-data">
                     @csrf
                     
                     <input type="hidden" id="webname_md" name="webName">
                     <input type="hidden" id="webid_md" name="webId">
                     <input type="hidden" id="id_md" name="id">
                     <div class="form-group z-depth-left-2">
                         
                           <img src="" class="img-fluid" id="slideImgMD"/>
                     </div>
                     <div class="form-group">
                          <label for="image_md" class="custom-file">
                          <input type="file" name="image_md" id="image_md" class="custom-file-input">
                          <span class="custom-file-control"></span>
                          </label>
                          
           <div class="alert alert-info">
               Be informed that the required image dimensions should be 1520 pixels in width and 460 pixels in height, with a file size not exceeding 200 kilobytes. translate in urdu
           </div>                          
                     </div>
                   
                   <div class="form-group">
               			<label class="pointer">
            				<input type="radio" name="navigato_md" id="navigat_depart_md" value="department"/>
            					<i class="helper"></i>Navigate to department
            			</label>
            			
               			<label class="pointer m-l-2">
            				<input type="radio" name="navigato_md" id="navigat_prod_md" value="product"/>
            					<i class="helper"></i>Navigate to product
            			</label>			
                   </div>   
                   
                   <div class="d-none" id="departmentbox_md">    
                     <div class="form-group">
                          <label class="form-control-label">Inventory Department</label>
                          <select name="depart_md" id="depart_md" data-placeholder="Select" class="form-control select2">
                            <option value="">Select</option>
                          @if($departments)
                             @php $oldDepart = old('depart_md');
                            @foreach($departments as $val)
                              <option {{ old('depart_md') == $val->department_id ? 'selected' : '' }} value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                            @endforeach
                          @endif
                          </select>
                    </div> 
                   </div>    
                   <div class="d-none" id="productbox_md"> 

                     <div class="form-group">
                          <label class="form-control-label">Inventory Department</label>
                          <select id="depart_editmd" data-placeholder="Select" class="form-control select2">
                            <option value="">Select</option>
                          @if($departments)
                            @foreach($departments as $val)
                              <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                            @endforeach
                          @endif
                          </select>
                    </div>
                    
                     <div class="form-group">
                          <label class="form-control-label">Inventory Sub-Department</label>
                          <select id="sub_depart_editmd" data-placeholder="Select" class="form-control select2" disabled></select>
                    </div>                     
                   
                     <div class="form-group">
                          <label class="form-control-label">Product</label>
                          <select name="product_editmd" id="product_editmd" data-placeholder="Select" class="form-control select2" disabled>
                            <option value="">Select</option>
                          </select>
   
                    </div>                       
                   </div> 
                    
                   </form>
               </div>
               <div class="modal-footer">
                   <button type="button" data-dismiss="modal" class="btn btn-default waves-effect waves-light m-r-1">Close</button>
                  <button type="button" id="btn_update_md" class="btn btn-success waves-effect waves-light">Save Changes</button>
               </div>
            </div>
         </div>
      </div>  
@endsection

@section('scriptcode_three')



<script type="text/javascript">
   
   var id=null;
   
  $(".select2").select2();

	$('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Web Slider',
          lengthMenu: '<span></span> _MENU_'
        }
    });
   
    $("input[name='navigato']").on('click',function(){
      if($(this).is(':checked') == true){  
         
        if($(this).val() == 'department'){
            if($("#departmentbox").hasClass('d-none')){
                $("#departmentbox").removeClass('d-none');
                $("#depart").val('');
            }
            
            if(!$("#productbox").hasClass('d-none')){
                $("#productbox").addClass('d-none');
                $("#product").val('');
            }            
        }
        
        
        if($(this).val() == 'product'){
            if(!$("#departmentbox").hasClass('d-none')){
                $("#departmentbox").addClass('d-none');
                $("#depart").val('');
            }
            
            if($("#productbox").hasClass('d-none')){
                $("#productbox").removeClass('d-none');
                $("#product").val('');
            }            
        } 
      }
    })  
    
    $("input[name='navigato_md']").on('click',function(){
      if($(this).is(':checked') == true){  
         
        if($(this).val() == 'department'){
            if($("#departmentbox_md").hasClass('d-none')){
                $("#departmentbox_md").removeClass('d-none');
                $("#depart_md").val('');
            }
            
            if(!$("#productbox_md").hasClass('d-none')){
                $("#productbox_md").addClass('d-none');
                $("#product_md").val('');
            }            
        }
        
        
        if($(this).val() == 'product'){
            if(!$("#departmentbox_md").hasClass('d-none')){
                $("#departmentbox_md").addClass('d-none');
                $("#depart_md").val('');
            }
            
            if($("#productbox_md").hasClass('d-none')){
                $("#productbox_md").removeClass('d-none');
                $("#product_md").val('');
            }            
        } 
      }
    });
    
   $("#btn_create").on('click',function(){
       $(this).atrr('disabled',true);
   })    
    
   $("#btn_update_md").on('click',function(){
       $("#editPostForm_md").submit();
   })    
    
    
   function editPost(unqid,webId,webName,depart,prod,prod_depart,prod_subdepart){
       $("#advPostEdit_Modal").modal('show');
       $("#slideImgMD").attr('src',$("#slide"+unqid).attr('src'))
       $("#webname_md").val(webName);
       $("#webid_md").val(webId);
       $("#id_md").val(unqid);
       id=unqid;
       
       getProduct(webId,'product_md',prod);
        
       if(prod != '' && depart == ''){
           $("#navigat_prod_md").trigger('click');
           $("#depart_editmd").val(prod_depart).trigger('change');
           load_subdept(prod_depart,webId,'sub_depart_editmd',prod_subdepart);
           getProduct(webId,'product_editmd',prod,prod_subdepart);
           
       }
       
       if(depart != '' && prod == ''){
           $("#navigat_depart_md").trigger('click');
           
           $("#depart_md").val(depart).trigger('change');
       }  
       
       //$("#editSlideForm_md").attr('action',$("#updateSliderImage"+id).val());
   }  
   
//   $("#btn_remove_md").on('click',function(){
//             swal({
//                 title: 'Remove advertisements post',
//                 text:  'Are you sure remove post from '+$("#webname_md").val()+' website?',
//                 type: "warning",
//                 showCancelButton: true,
//                 confirmButtonClass: 'btn btn-danger',
//                 confirmButtonText: "YES",
//                 cancelButtonText: "NO",
//                 closeOnConfirm: false,
//                 closeOnCancel: false
//             },function(isConfirm){
//                 if(isConfirm){
//                     $("#mode"+$("#webid_md").val()).val(id);
//                     $("#DestroyForm"+$("#webid_md").val()).submit();
//                 }else{
//                     swal.close();
//                 }
//             });          
       
//   })

//   $("#btn_create").on('click',function(){
//       var webid = $("#website").val();
//       var slide = $("#image").get(0).files.length;
//       var formData = new FormData($('form[name="sliderForm"]'));


//           if(webid == ''){

//           }

//           if(slide > 0){
//                 $('form[name="sliderForm"]').submit();
//           // $.ajax({
//           //   type:'POST',
//           //   url: $('form[name="sliderForm"]').attr('action'),
//           //   data:formData,
//           //   cache:false,
//           //   contentType: false,
//           //   processData: false,
//           //   async:true,
//           //   success:function(data){
//           //     if()
//           //       console.log("success");
//           //       console.log(data);
//           //   },
//           //   error: function(data){
//           //       console.log("error");
//           //       console.log(data);
//           //   }
//           // });
//          }
//   })
  
  
  $('#website').on('change',function(){
      load_dept($(this).val(),'depart','');
      load_dept($(this).val(),'depart_prod','');
  });
  
  $("#image").on('change',function(){
      readURL(this,'previewImg');
  })
  
  function getProduct(webId,elemId,prod,sub_depart){

           $.ajax({
             url: '{{ route("getWebsiteProd") }}',  
             type:'POST',
             data:{_token:'{{ csrf_token() }}',id:webId,subDepart:sub_depart},
             async:true,
             success:function(data){
               if(data != null){
                    $("#"+elemId).empty().attr('disabled',false);
                    $.each(data,function(i,v){
                        if(i == 0){
                            $("#"+elemId).append('<option value="">Select</option>')
                        }
                        
                        if(v.id == prod){
                            $("#"+elemId).append('<option selected value="'+v.id+'">'+v.product_name+'</option>')
                        }else{
                            $("#"+elemId).append('<option value="'+v.id+'">'+v.product_name+'</option>')
                        }
                    })
               }
             },
             error: function(data){
                 console.log("error");
                 console.log(data);
             }
           });       
      
  }
  
    function warning(id,webName){
            swal({
                title: 'Remove Slider',
                text:  'Are you sure remove post from '+webName+' website?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     $("#DestroyForm"+id).submit();
                }else{
                    swal.close();
                }
            });        
    }  
    
    function readURL(input,id) {
         
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
               
              $('#'+id).attr('src', e.target.result);

            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    
 		$("#depart_prod").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_prod").val('change');
		        if(!$("#subDepartment_prod").attr('disabled')){
		            $("#subDepartment_prod").attr('disabled',true);
		            $("#subDepartment_prod").val('');
		        }
		    } else{
		      load_subdept($(this).val(),$("#website").val(),'subDepartment_prod',null);
		    }
		});  
		
 		$("#subDepartment_prod").on('change',function(){
		    if($(this).val() == ''){
		        $("#product").val('change');
		        if(!$("#product").attr('disabled')){
		            $("#product").attr('disabled',true);
		            $("#product").val('');
		        }
		    } else{
		          getProduct($('#website').val(),'product','',$(this).val());
		    }
		});  		
		
 		$("#depart_editmd").on('change',function(){
		    if($(this).val() == ''){
		        $("#sub_depart_editmd").val('change');
		        if(!$("#sub_depart_editmd").attr('disabled')){
		            $("#sub_depart_editmd").attr('disabled',true);
		            $("#sub_depart_editmd").val('');
		        }
		    } else{
		      load_subdept($(this).val(),$("#webid_md").val(),'sub_depart_editmd',null);
		    }
		});  
		
 		$("#sub_depart_editmd").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_editmd").val('change');
		        if(!$("#product_editmd").attr('disabled')){
		            $("#product_editmd").attr('disabled',true);
		            $("#product_editmd").val('');
		        }
		    } else{
		          getProduct($('#webid_md').val(),'product_editmd','',$(this).val());
		    }
		}); 

    function load_dept(id,elementId){
            $.ajax({
                url: "{{ route('getDepart_n_subDepart_wb') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",website:id,mode:'depart'},
                success:function(resp){
                    console.log(resp)
                    if(resp != 0){
                        $('#'+elementId).empty();
                        
        				 if($("#"+elementId).attr('disabled')){
        				     $("#"+elementId).attr('disabled',false);
        				 }
        				 
                        $('#'+elementId).append("<option value=''>Select Department</option>");
                        $.each(resp, function( index, value ) {
                            $('#'+elementId).append(
                                "<option value="+value.department_id+">"+value.department_name+"</option>"
                            );
                        });
                    }
                }
            });
        } 
    
    function load_subdept(id,wbId,elementId,selectedVal){
            $.ajax({
                url: "{{ route('getDepart_n_subDepart_wb') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",depart:id,website:wbId,mode:'subdepart'},
                success:function(resp){
                    $('#'+elementId).empty();
                    
    				 if($("#"+elementId).attr('disabled')){
    				     $("#"+elementId).attr('disabled',false);
    				 }
    				 
                    $('#'+elementId).append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                      if(value.sub_department_id == selectedVal){    
                        $('#'+elementId).append(
                            "<option selected value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                      }else{
                         $('#'+elementId).append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        ); 
                      }
                    });
                }
            });
        } 
        
//  		$("#subDepartment_prod").on('change',function(){
// 		    if($(this).val() == ''){
// 		        $("#product").val('change');
// 		        if(!$("#product").attr('disabled')){
// 		            $("#product").attr('disabled',true);
// 		            $("#product").val('');
// 		        }
// 		    } else{
// 		       //productload_department_wise($(this).val(),'product_addonTab');
// 		    }
// 		});        
 </script>
@endsection