@extends('layouts.master-layout')

@section('title','Inventory-Department')

@section('breadcrumtitle','View Inventory')
@section('navinventory','active')
@section('navinvent_depart','active')

@section('content')

<section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                     <h5 class="card-header-text" id="title-hcard"> Create Department</h5>
                  </div>
                  <div class="card-block">

    <form method="POST" id="deptform" class="form-horizontal" enctype="multipart/form-data">
      @csrf
       
        <div class="row">
            <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Department Code:</label>
                  <input class="form-control" type="text" name="code" id="code" />
                  <div class="form-control-feedback text-danger" id="dptcode_alert"></div>
              </div>
            </div>
		    <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Department Name</label>
                  <input class="form-control" type="text"
                   name="deptname" id="deptname" />
                   <div class="form-control-feedback text-danger" id="dptname_alert"></div>
              </div>
            </div>

             <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Sub Department</label>
                   <div class="tags_add">
                      <input class="form-control" id="subdpt" name="subdpt" type="text"  />
                    </div>
                   <span class="form-control-feedback text-danger" id="subdpt_alert"></span>
              </div>
            </div>
			
             

            <div class="col-lg-3 col-md-3">
              <div class="form-group row">
                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus" 
                  ></i>&nbsp; Save</button>.
                    <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
                  ></i> Clear</button>
              </div>
            </div>

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
         <h5 class="card-header-text">Sub Department List</h5>
           <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
         </div>      
       <div class="card-block">
       

    
           <div class="project-table">
     <table id="mainTable" class="table table-striped full-width">
         <thead>
            <tr>
               <th>Code</th>
               <th>Department</th>
               <th>Sub Department</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
              @if($depart)
                @for($d=0;$d < sizeof($depart);$d++)
                       <tr>
	
                         <td style="cursor: pointer;" >{{ $depart[$d]->code }}</td>
                         <td style="cursor: pointer;" onclick="editdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_name}}','{{$depart[$d]->department_id}}')">{{ $depart[$d]->department_name }}</td>
                         <td style="cursor: pointer;" onclick="editsubdepart('{{ $depart[$d]->code }}','{{$depart[$d]->department_id}}','{{$depart[$d]->department_name}}')" >
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
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Code:</label>
                         <input type="text"  name="editcode" id="editcode" class="form-control" />
                         <input type="hidden" name="codeid"  id="codeid"/>
                        </div>
                      </div>
					  <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Department Name:</label>
                         <input type="text"  name="depart" id="depart" class="form-control" />
                         <input type="hidden" name="departid"  id="departid"/>
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="updatedepart()">Edit Department</button>
             </div>
          </div>
           </div>
        </div> 



     <div class="modal fade modal-flex" id="subdepart-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
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
                  <select class="form-control  select2" data-placeholder="Select Department" id="departmodal" name="departmodal">
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
							<input type="text"  name="subdepartcode" id="subdepartcode" class="form-control" />
                        </div>
                        </div>
                       <div class="col-md-12">
                        <div class="form-group"> 
                          <label class="form-control-label">Sub Department Name:</label>
                         <input type="text"  name="subdepartname" id="subdepartname" class="form-control" />
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




$("#btn_clear").on('click',function(){

    $("#deptform")[0].reset();
    $("#subdpt").tagsinput('removeAll');

});


 $("#btn_save").on('click',function(event){
 // $("#deptform").on('submit',function(event){
// event.preventDefault();
    if($("#deptname").val() == ""){
         $("#deptname").focus();
         $("#deptname_alert").html('Department name is required.');
    }else{
              
  
                  
                   $.ajax({
                    url:'{{ route("invent_dept.store") }}',
                    type:"POST",
                    data:$('#deptform').serialize(),
                    dataType:"json",
				   // contentType: false,
				   // processData: false,
                    success:function(r){
						// console.log(r)
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
		$.ajax({
			url:'{{ route("invent_sb_deptup") }}',
			type:"PUT",
			data:{_token:"{{ csrf_token()}}",id:id,sdepart:$("#tbx_"+id).val(),dept:dept,code:$("#code_"+id).val()},
			dataType:"json",
			success:function(r){
	 
			 if(r.state == 1){
				if(r.contrl != ""){
				 $("#"+r.contrl).focus();
				
				}
				 
				 swal_alert('Alert!',r.msg,'error',false);
				 $("#tbx_"+id+"_alert").html(r.msg).addClass('text-danger');

			 }else {
				swal_alert('Success!',r.msg,'success',false);
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
              window.location="{{ route('invent_dept.index') }}";
            }
          }
      });
}


function editdepart(code,depart,departid){
  $("#depart-modal").modal("show");
  $('#depart').val(depart);
  $('#departid').val(departid);
  $('#editcode').val(code);
}
function editsubdepart(departcode,departid,departname){
   $.ajax({
                url: "{{url('/getsubdepart')}}",
                type:"GET",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                departid:departid,
              },
              success:function(r){  
                 $("#subdepart-modal").modal("show");
                 $("#depart_modal_name").html(departname);
                 $("#department_code").val(departcode);
				 
                $(".sb_tble tbody").empty();
                    for(var s=0;s < r.length ;s++){
                      $(".sb_tble tbody").append(
                          "<tr>" +
                            "<td><input type='text' value='"+r[s].code +"' class='form-control' id='code_"+r[s].sub_department_id+"'/>"+
                            "<td><input type='text' value='"+r[s].sub_depart_name +"' class='form-control' id='tbx_"+r[s].sub_department_id+"'/>"+
                            "<div class='form-control-feedback text-danger' id='tbx_"+r[s].sub_department_id+"_alert'></div>"+"</td>"+
                            "<td class='action-icon'><i onclick='update("+r[s].sub_department_id+","+departid+")' class='btn btn-primary'> Update</i></td>"+
                          "</tr>"
                          );
                    }
              }

  });
 
}



function updatedepart(){
 $.ajax({
		url: "{{url('/updatedepart')}}",
		type:"PUT",
		dataType:"json",
		data:{_token:"{{ csrf_token()}}",
		departid:$('#departid').val(),
		departname: $('#depart').val(),
		code: $('#editcode').val(),
	  },
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

        $('#subdepartname').val('');
        $('#departmodal').val(id).change();
        $("#subdepartment-modal").modal("show");
}

function insertsubdepart(){
     $.ajax({
            url: "{{url('/addsubdepart')}}",
            type: 'POST',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            departid:$('#departmodal').val(),
            subdepart:$('#subdepartname').val(),
            code:$('#subdepartcode').val(),
          },
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


</script>

@endsection
