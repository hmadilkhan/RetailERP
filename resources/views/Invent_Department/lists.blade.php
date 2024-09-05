@extends('layouts.master-layout')

@section('title','Inventory-Department')

@section('breadcrumtitle','View Inventory')
@section('navinventory','active')
@section('navinvent_depart','active')

@section('content')

<section class="panels-wells">

<form method="POST" id="deptform" class="form-horizontal" enctype="multipart/form-data">
   @csrf
   <div class="row">
     <div class="col-md-8">
     <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text" id="title-hcard"> Create Department</h5>
                  </div>
                  <div class="card-block">


       
    
            <div class="row">    
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Department Code:</label>
                      <input class="form-control" type="text" name="code" id="code" placeholder='Department Code'/>
                      <div class="form-control-feedback text-danger" id="dptcode_alert"></div>
                  </div>
                </div>
            
    		    <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Department Name</label>
                      <input class="form-control" type="text"
                       name="deptname" id="deptname" placeholder='Department Name'/>
                       <div class="form-control-feedback text-danger" id="deptname_alert"></div>
                  </div>
                </div>   
                

                
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Parent</label>
                      <select name="parent" id="parent" class="select2">
                          <option value="">Select</option>
                          @foreach($depart as $val)
                            <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                          @endforeach
                      </select>    
                      <span class="form-control-feedback text-danger" id="parent_alert"></span>
                  </div>
                </div>                
                
              </div>
           </div>   
           @if($websites)
            <div class="form-group">
                <label for="showProductWebsite">
                    <input type="checkbox" id="showProductWebsite" name="showProductWebsite">
                    Show Product on Website
                </label>
            </div>

    		    <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label class="form-control-label">Show website department name</label>
                      <input class="form-control" type="text"
                       name="webdeptname" id="webdeptname" placeholder='Show website department name'/>
                       <div class="form-control-feedback text-danger" id="webdeptname_alert"></div>
                  </div>
                </div>  

           @endif
         </div>
       </div>
     </div> <!-- field portion-->
     <div class="col-md-4">
     <div class="row">
               <div class="col-lg-3 col-md-2">
                 <div class="form-group">
                  <label class="form-control-label">Department Image</label>
                      <input class="form-control" id="departImage" name="departImage" type="file" />
                   <span class="form-control-feedback text-danger" id="departImage_alert"></span>
                 </div>
               </div>

        @if($websites)    
        
        @endif
     </div> <!-- col-md-4 close image portion -->
   </div>

      <div class="form-group row">
          <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="submit" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus" 
            ></i>&nbsp; Save</button>.
              <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
            ></i> Clear</button>
       </div>
   </form>
</section>    

 <section class="panels-wells">
  <div class="row">

           <div class="col-lg-12 col-md-12">
              <div class="form-group">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Sub Department List</h5>
           <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
         </div>      
       <div class="card-block">
       

    
           <div class="project-table">
     <table id="mainTable" class="table table-striped full-width">
         <thead>
            <tr>
               <th class="d-none">Code</th>
               <th>Code</th>
               <th>Image</th>
               <th>Department</th>
               <th>Website Department</th>
               <th>Sub Department</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
              @if($depart)
                @for($d=0;$d < sizeof($depart);$d++)
                       <tr>
	                     <td class="d-none">{{ $depart[$d]->department_id }}</td>    
                         <td class="pointer" onclick="editdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_name}}','{{ $depart[$d]->website_department_name }}','{{$depart[$d]->department_id}}')">{{ $depart[$d]->code }}</td>
                         <td class="pointer" onclick="editdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_name}}','{{ $depart[$d]->website_department_name }}','{{$depart[$d]->department_id}}')">
                             @if(!empty($depart[$d]->image) && file_exists(public_path('assets/images/department/').$depart[$d]->image))
                                <img id="img-tble-{{ $depart[$d]->department_id }}" src="{{ asset('assets/images/department/'.$depart[$d]->image) }}" alt="{{ $depart[$d]->image }}" height="64" width="64"/>
                             @else
                                <img id="img-tble-{{ $depart[$d]->department_id }}" src="{{ asset('assets/images/no-image.jpg') }}" alt="no-image.jpg" height="64" width="64" />
                             @endif
                         </td>
                         <td class="pointer" onclick="editdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_name}}','{{ $depart[$d]->website_department_name }}','{{$depart[$d]->department_id}}')">{{ $depart[$d]->department_name }}</td>
                         <td class="pointer" onclick="editdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_name}}','{{ $depart[$d]->website_department_name }}','{{$depart[$d]->department_id}}')">{{ $depart[$d]->website_department_name }}</td>
                         <td class="pointer" onclick="editsubdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_id}}','{{ addslashes($depart[$d]->department_name) }}')" >
                            @if($depart)
                              @for($sd=0;$sd < sizeof($sdepart);$sd++)
                                 @if($sdepart[$sd]->department_id == $depart[$d]->department_id)
                                     <label>{{ $sdepart[$sd]->sub_depart_name }}</label>,
                                 @endif
                              @endfor
                            @endif  
                         </td>
                         <td class="action-icon">
                             <i onclick="addsubdepart('{{$depart[$d]->department_id}}')" class="text-success text-center icofont icofont-plus" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Sub-Department"></i>
                             <i onclick="deletedepart('{{$depart[$d]->department_id}}')" class="text-danger text-center icofont icofont-ui-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete Department"></i>
                       </td>            
                     </tr>
               @endfor
             @endif
                     </tbody>
                 </table>
        </div>
         </div>
          

    </div>
      </div>
          </div>
    


    </div>

     <div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Edit Department</h4>
             </div>
             <div class="modal-body">
                 <form id="editDepartmentForm" method="post" enctype="multipart/form-data">
                     @csrf
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Code:</label>
                         <input type="text"  name="editcode" id="editcode" class="form-control" />
                         <input type="hidden" name="code"  id="codeid"/>
                        </div>
                      </div>
					  <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Department Name:</label>
                         <input type="text"  name="departname" id="depart" class="form-control" />
                         <input type="hidden" name="departid"  id="departid"/>
                        </div>
                      </div>

        		    <div class="col-md-12">
                      <div class="form-group">
                          <label class="form-control-label">Show website department name</label>
                          <input class="form-control" type="text"
                           name="webdeptname" id="webdeptname_md" />
                           <div class="form-control-feedback text-danger" id="webdeptname_md_alert"></div>
                      </div>
                    </div>                        
                      
                      <div class="col-md-12">
                          <div class="form-group">
                                 <img src="../../assets/images/no-image.jpg" alt="placeholder.jpg" width="128" height="128" id="previewImg"/></br>
                              <label for="departImage_md" class="form-control-label">Department Image</label></br>
                    
                              <label for="departImage_md" class="custom-file">
                              <input type="file" name="departImage" id="departImage_md" class="custom-file-input">
                              <span class="custom-file-control"></span>
                              </label>
                          </div>
                      </div>
                  </div>  
                  </form>
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="updatedepart()">Edit Department</button>
             </div>
          </div>
           </div>
        </div> 



     <div class="modal fade modal-flex" id="subdepart-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-xlg" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Edit Sub-Department</h4>
									<hr/>
                                    <h4 class="modal-title" id="depart_modal_name">Department</h4>
                                 </div>
                                 <div class="modal-body">
                                    <input type="hidden" name="uhidd_id" id="uhidd_id" value="0" />
                                    <input type="hidden" name="department_code" id="department_code" />
                                      
                                    <table class="table full-width sb_tble">
                                         <thead>
                                            <tr>
                                              <th>Code</th>
                                              <th>Sub Department</th>
                                               <th>Website Department</th>
                                               <th>Image</th>
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
                                  <a href="javascript:void()" onclick="window.location.href='{{url('invent_dept')}}'"   data-dismiss="modal" class="btn btn-success waves-effect waves-light">OK</a>
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
                 <div class="row">
                  <div class="col-md-12">
                  <div class="form-group"> 
                  <select class="form-control  select2" data-placeholder="Select Department" id="departmodal" name="departid">
             <option value="">Select Department</option>
             @if($depart)
                    @foreach($depart as $val)
                      @if( old('depart') == $val->department_id)
                        <option selected="selected" value="{{$val->department_id}}">{{$val->department_name}}</option>
                      @else
                        <option value="{{$val->department_id}}">{{$val->department_name}}</option>
                      @endif
                    @endforeach
             @endif
          </select> 
                  </div>
                </div>
						<div class="col-md-12">
                        <div class="form-group"> 
							<label class="form-control-label">Code:</label>
							<input type="text"  name="code" id="subdepartcode" class="form-control" placeholder="Sub department code" />
                        </div>
                        </div>
                       <div class="col-md-12">
                        <div class="form-group"> 
                          <label class="form-control-label">Sub Department Name:</label>
                         <input type="text"  name="subdepart" id="subdepartname" class="form-control" placeholder="Sub department name" />
                          </div>
                        </div>
                       <div class="col-md-12">
                        <div class="form-group"> 
                          <label class="form-control-label">Show website sub department name:</label>
                         <input type="text"  name="websubdepart" id="websubdepartname" class="form-control" placeholder="Show website sub department name" />
                          </div>
                        </div>                        
                        <div class="col-md-12">
                          <div class="form-group">
                                 <img src="../../assets/images/no-image.jpg" alt="placeholder.jpg" width="128" height="128" id="previewImg_sbmd"/></br>
                              <label for="subdepartImage_add" class="form-control-label">Sub Department Image</label></br>
                    
                              <label for="subdepartImage_add" class="custom-file">
                              <input type="file" name="subdepartImage" id="subdepartImage_add" class="custom-file-input">
                              <span class="custom-file-control"></span>
                              </label>
                          </div>                            
                        </div>
                    </div>    
               </div>
               <div class="modal-footer">
                  <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="insertsubdepart()">Add Sub Department</button>
               </div>
            </div>
         </div>
      </div> 
   
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">


   $(".select2").select2();

   $("#subdpt").tagsinput({
     maxTags: 10
    });
      $('#mainTable').DataTable( {

        bLengthChange: true,
        displayLength: 50,
        info: false,
        order: [[0, 'desc']],
        language: {
          search:'', 
          searchPlaceholder: 'Search Department',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );
	
	function readURL(input,id) {
		  if (input.files && input.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function(e) {
			  $('#'+id).attr('src', e.target.result);
			}
			
			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#image").change(function() {
	  readURL(this,'simg');
	});

	$("#departImage_md").change(function() {
	  readURL(this,'previewImg');
	});
	
	$("#subdepartImage_add").change(function() {
	  readURL(this,'previewImg_sbmd');
	});


$("#btn_clear").on('click',function(){

    $("#deptform")[0].reset();
    $("#subdpt").tagsinput('removeAll');

});


//  $("#btn_save").on('click',function(event){
  $("#deptform").on('submit',function(event){
      event.preventDefault();
      
      var formData = new FormData(this);

    if($("#deptname").val() == ""){
         $("#deptname").focus();
         $("#deptname_alert").html('Department name is required.');
         swal('Cancel!','Department name is required.','error',false);
    }else{
        
               $.ajax({
                url:'{{ route("invent_dept.store") }}',
                type:"POST",
                data:formData,
                dataType:"json",
    		    cache:false,
    		    contentType: false,
    		    processData: false,
                success:function(r){
    		// 		console.log(r)
                  if(r.state == 1){
                      if(r.contrl != ""){
                        $("#"+r.contrl).focus();
                        $("#"+r.contrl+"_alert").html(r.msg);
                      }
                      swal_alert('Alert!',r.msg,'error',false); 
    
                  }else {
                     $("#deptname_alert").html('');
                    swal_alert('Successfully!',r.msg,'success',true);
                     $("#subdpt").tagsinput('removeAll');
                  }
                }
              });
    }
  });



$("#btn_update").on('click',function(){

      if($("#udeptname").val() == ""){
          $("#udeptname_alert").html('Department name is required.').addClass('text-danger');
      }else {
          $("#udeptname_alert").html('').removeClass('text-danger');
            $.ajax({
                        url:'{{ route("invent_deptup") }}',
                        type:"PUT",
                        data:{_token:"{{ csrf_token()}}",id:$("#uhidd_id").val(),depart:$("#udeptname").val()},
                        dataType:"json",
                        success:function(r){
                 
                         if(r.state == 1){
                            if(r.contrl != ""){
                             $("#"+r.contrl).focus();
                            
                            }
                             
                             swal_alert('Alert!',r.msg,'error',false);
                             $("#udeptname_alert").html(r.msg).addClass('text-danger');

                         }else {
                            swal_alert('Success!',r.msg,'success',false);
                            
                         }
                       }
                    });         

      }

});

 function edit_record(id,name){
       
           $.ajax({
              url:'invent_dept'+'/'+id+'/edit',
              type:"GET",
              dataType:"json",
              success:function(r){ 
                 if(r != 0){ 
                    $("#depat-modal").modal("show");
                    $("#udeptname").val(r[0].deptname);
                    $("#uhidd_id").val(id);
                     $(".sb_tble tbody").empty();
                    for(var s=0;s < r.length ;s++){
                      $(".sb_tble tbody").append(
                          "<tr>" +
                            "<td><input type='text' value='"+r[s].sbname +"' class='form-control' id='tbx_"+r[s].sb_id+"'/>"+
                            "<div class='form-control-feedback text-danger' id='tbx_"+r[s].sb_id+"_alert'></div>"+"</td>"+
                            "<td class='action-icon'><i onclick='update("+r[s].sb_id+")' class='icofont icofont-ui-edit text-primary' data-toggle='tooltip' data-placement='top' title='' data-original-title='Update'></i></td>"+
                          "</tr>"
                          );
                    }
                    

                }else {

                   
                }
             }

           });
     }



 function update(id,dept){
  let code = $("#department_code").val();
  if(code == $("#code_"+id).val()){
	  swal_alert('Alert!',"Cannot use the main department code.",'error',false);
  }else if($("#tbx_"+id).val() == ""){
          $("#tbx_"+id+"_alert").html('Sub-Department name is required.').addClass('text-danger');
  }else {
	  $("#tbx_"+id+"_alert").html('').removeClass('text-danger'); 
	  
	  var formData = new FormData();
	  
	  formData.append('_token','{{ csrf_token() }}');
	  formData.append('sdepart',$("#tbx_"+id).val());
	  formData.append('dept',dept);
	  formData.append('code',$("#code_"+id).val());
	  formData.append('id',id);
	  
	if($('#sdbptImg'+id)[0].files.length != 0 ){  
	  formData.append('subdepartImage', $('#sdbptImg'+id)[0].files[0]);
	}
		$.ajax({
			url:'{{ route("invent_sb_deptup") }}',
    		type:"POST",
            data:formData,
            dataType:"json",
    	    cache:false,
    	    contentType: false,
    	    processData: false,
			success:function(r){
	           console.log(r)
			 if(r.state == 1){
				if(r.contrl != ""){
				 $("#"+r.contrl).focus();
				}
				
				 swal_alert('Alert!',r.msg,'error',false);
				 $("#tbx_"+id+"_alert").text(r.msg).addClass('text-danger');

			 }else {
			    
			    if($("#tbx_"+id+"_alert").hasClass('text-danger')){
			        $("#tbx_"+id+"_alert").removeClass('text-danger').text('');
			    } 
			        
				swal_alert('Success!',r.msg,'success',false);
			 }
		   }
		});
	  
// 		$.ajax({
// 			url:'{{-- route("invent_sb_deptup") --}}',
// 			type:"POST",
// 			data:{_token:"{{-- csrf_token()--}}",id:id,sdepart:$("#tbx_"+id).val(),dept:dept,code:$("#code_"+id).val()},
// 			dataType:"json",
// 			success:function(r){
	 
// 			 if(r.state == 1){
// 				if(r.contrl != ""){
// 				 $("#"+r.contrl).focus();
				
// 				}
				 
// 				 swal_alert('Alert!',r.msg,'error',false);
// 				 $("#tbx_"+id+"_alert").html(r.msg).addClass('text-danger');

// 			 }else {
// 				swal_alert('Success!',r.msg,'success',false);
// 			 }
// 		   }
// 		}); 	
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
              window.location="{{ route('invent_dept.index') }}";
            }
          }
      });
}


function editdepart(code,depart,webDepart,departid){
  $("#depart-modal").modal("show");
  $('#depart').val(depart);
  $('#departid').val(departid);
  $('#editcode').val(code);
  $('#webdeptname_md').val(webDepart)
   //alert($("#img-tble-"+code).attr('src'))
//   if($("#img-tble-"+departid).attr('src') != ''){
  
         $("#previewImg").attr('src',$("#img-tble-"+departid).attr('src'));
//   }else{
//          $("#previewImg").attr('src','{{-- asset("assets/images/department/no-image.jpg") --}}');
//   }
}
function editsubdepart(departcode,departid,departname){
    //alert()
   $.ajax({
                url: "{{url('/getsubdepart')}}",
                type:"GET",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                departid:departid,
              },
              success:function(r){  
                  console.log(r)
                 $("#subdepart-modal").modal("show");
                 $("#depart_modal_name").html(departname);
                 $("#department_code").val(departcode);
				 
                $(".sb_tble tbody").empty();
                    for(var s=0;s < r.length ;s++){
                        
                        var imageColumn = "<td id='imgCell_md"+r[s].sub_department_id+"'><div><input type='file' name='sdbptImg' class='d-none' id='sdbptImg"+r[s].sub_department_id+"'> <i id='btn_selectImg"+r[s].sub_department_id+"' class='icofont icofont-upload text-success icofont-3x' onclick='selectImg("+r[s].sub_department_id+")'></i></div></td>";
                        
                        if(r[s].image != null && r[s].image != ''){
                            imageColumn ="<td id='imgCell_md"+r[s].sub_department_id+"'><div><img src='"+location.origin+"/assets/images/department/"+r[s].image+"' width='64' height='64'/><i class='icofont icofont-close text-danger' onclick='removeImgCell("+r[s].sub_department_id+")'></i></div></td>";
                        }
                        
                    
                      $(".sb_tble tbody").append(
                          "<tr>" +
                            "<td><input type='text' value='"+(r[s].code != null ? r[s].code : '')+"' placeholder='Department code' class='form-control' id='code_"+r[s].sub_department_id+"'/>"+
                            "<td><input type='text' value='"+r[s].sub_depart_name +"' placeholder='Sub department name' class='form-control' id='tbx_"+r[s].sub_department_id+"'/>"+
                            "<div class='form-control-feedback text-danger' id='tbx_"+r[s].sub_department_id+"_alert'></div>"+"</td>"+
                            "<td><input type='text' value='"+(r[s].website_sub_department_name != null ? r[s].website_sub_department_name : '') +"' placeholder='Show website department name' class='form-control' id='tbxwb_"+r[s].sub_department_id+"'/>"+
                            "<div class='form-control-feedback text-danger' id='tbxwb_"+r[s].sub_department_id+"_alert'></div>"+"</td>"
                            +imageColumn+
                            "<td class='action-icon'><i onclick='update("+r[s].sub_department_id+","+departid+")' class='btn btn-primary'> Update</i></td>"+
                          "</tr>"
                          ); 
                    //   $(".sb_tble tbody").append(
                    //       "<tr>" +
                    //         "<td><input type='text' value='"+r[s].code +"' class='form-control' id='code_"+r[s].sub_department_id+"'/>"+
                    //         "<td><input type='text' value='"+r[s].sub_depart_name +"' class='form-control' id='tbx_"+r[s].sub_department_id+"'/>"+
                    //         "<div class='form-control-feedback text-danger' id='tbx_"+r[s].sub_department_id+"_alert'></div>"+"</td>"+
                    //         "<td class='action-icon'><i onclick='update("+r[s].sub_department_id+","+departid+")' class='btn btn-primary'> Update</i></td>"+
                    //       "</tr>"
                    //       );
                        

                    }
              }

  });
 
}

function removeImgCell(id){
    
    $("#imgCell_md"+id).empty();
    $("#imgCell_md"+id).append('<input type="file" name="sdbptImg" class="d-none" id="sdbptImg'+id+'"> <i id="btn_selectImg'+id+'" class="icofont icofont-upload text-success icofont-3x" onclick="selectImg('+id+')"></i>');
}

let imgdId = null;

function selectImg(id){
    imgdId = id;
    $("#sdbptImg"+id).trigger('click');
    fileGet_sbd_md(id)
}

function fileGet_sbd_md(id){
  $("#sdbptImg"+id).on('change',function(){
  $("#btn_selectImg"+id).remove();
  $("#imgCell_md"+id).append('<img src="" width="64" height="64" id="imgPreviewsdbpt'+id+'"> <i class="icofont icofont-close text-danger" onclick="removeImgCell('+id+')"></i>');      
      readURL(this,"imgPreviewsdbpt"+id);
  })    
}

function updatedepart(){
    var form = document.getElementById('editDepartmentForm'); 
    var formData = new FormData(form); 
 $.ajax({
		url: "{{url('/updatedepart')}}",
		type:"POST",
        data:formData,
        dataType:"json",
	    cache:false,
	    contentType: false,
	    processData: false,
// 		data:{_token:"{{-- csrf_token() --}}",
// 		departid:$('#departid').val(),
// 		departname: $('#depart').val(),
// 		code: $('#editcode').val(),
// 	  },
	  success:function(resp){
			if(resp.state == 1){
			  swal_alert('Alert!',resp.msg,'error',false); 
			}else {				  
			// if(resp != 0){
			 swal({
			  title: "Operation Performed",
			  text: "Department Updated Successfully!",
			  type: "success"},
			  function(isConfirm){
				   if(isConfirm){
						$("#depart-modal").modal("hide");
						window.location= "{{ url('/invent_dept') }}";
				   }
			   });
			}
		}
	});
}

function addsubdepart(id){
    $("#subdepartcode").val('');
    $('#subdepartname').val('');
    $('#subdepartImage_add').val('');
    $('#departmodal').val(id).change();
    $("#subdepartment-modal").modal("show");
}

function deletedepart(id){
	swal({
		title: "Are you sure?",
		text: "Department and its relative sub-department will also be delete !!!",
		type: "warning",
		showCancelButton: true,
		confirmButtonClass: "btn-danger",
		confirmButtonText: "Active it!",
		cancelButtonText: "cancel plx!",
		closeOnConfirm: false,
		closeOnCancel: false
	},
	function(isConfirm){
		if(isConfirm){
			$.ajax({
				url: "{{ url('deletedepartment')}}",
				type: 'POST',
				data:{_token:"{{ csrf_token() }}",id:id},
				success:function(resp){

					if(resp.status == 200){
						swal({
							title: "Deactivated",
							text: "Department deactivated Successfully .",
							type: "success"
						},function(isConfirm){
							if(isConfirm){
								window.location="{{ url('invent_dept') }}";
							}
						});
					}
				}

			});

		}else {
			swal("Cancelled", "Your Item is safe :)", "error");
		}
	});
}

function insertsubdepart(){
    
    if($('#subdepartname').val() == ''){
        $('#subdepartname').focus();
        $('#subdepartname_alert_md').text('field is required');
    }else{
        
     var formData = new FormData();
     
	  formData.append('_token','{{ csrf_token() }}');
	  formData.append('departid',$("#departmodal").val());
	  formData.append('code',$("#subdepartcode").val());
	  formData.append('subdepart',$("#subdepartname").val());
	  formData.append('websubdepart',$("#websubdepartname").val())

	if($('#subdepartImage_add')[0].files.length != 0 ){  
	  formData.append('subdepartImage', $('#subdepartImage_add')[0].files[0]);
	}       
    
     $.ajax({
            url: "{{url('/addsubdepart')}}",
            type: 'POST',
            data:formData,
            dataType:"json",
    	    cache:false,
    	    contentType: false,
    	    processData: false,            
            success:function(resp){

                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Sub Department Added Successfully!",
                      type: "success"
                       },
                         function(isConfirm){
                   if(isConfirm){
                     $("#subdepart-modal").modal("hide");
                    window.location= "{{ url('/invent_dept') }}";
                   }
               });
                  }
                  else
                  {
                     swal({
                            title: "Already exsist",
                            text: "Particular Sub Department Already exsist!",
                            type: "warning"
                       });
                      $("#subdepart-modal").modal("hide");

                  }
             }

          }); 
    }

}


</script>

@endsection
