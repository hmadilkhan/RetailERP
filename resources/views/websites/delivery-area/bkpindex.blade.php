@extends('layouts.master-layout')

@section('title','Delivery Area Lists')

@section('breadcrumtitle','Delivery Area Lists')

@section('navwebsite','active')

@section('content')


<section class="panels-wells ">

  @if(Session::has('success'))
      <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  @if(Session::has('error'))
      <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

<div class="card mt-0">
  <div class="card-header">
    <h5 class="card-header-text">Create Delivery Area</h5>
  </div>      
    <div class="card-block ">
     <form id="deliveryAreasForm" method="post">
       @csrf
      <div class="row">
         <div class="col-md-4">
            <div class="form-group">
              <label class="form-control-label">Website</label>
              <select name="website" id="website" data-placeholder="Select" class="form-control select2">
                <option value="">Select</option>
                @if($websites)
                   @php $oldWebsite = old('website'); @endphp
                  @foreach($websites as $val)
                    <option {{ old('website') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                  @endforeach
                @endif
              </select>
                <div class="form-control-feedback text-danger" id="website_alert"></div>
             </div>
         </div>

         <div class="col-md-4">
          <div class="form-group">
            <label class="form-control-label">Branch</label>
            <select name="branch" id="branch" data-placeholder="Select" class="form-control select2" disabled>
              <option value="">Select</option>
            </select>
              <div class="form-control-feedback text-danger" id="branch_alert"></div>
           </div>
         </div>
         
         <div class="col-md-4">
          <div class="form-group">
            <label class="form-control-label">City</label>
            <select name="city" id="city" data-placeholder="Select" class="form-control select2">
              <option value="">Select</option>
              @if($city)
                 @php $oldcity = old('city'); @endphp
                @foreach($city as $val)
                  <option {{ old('city') == $val->city_id ? 'selected' : '' }} value="{{ $val->city_id }}">{{ $val->city_name }}</option>
                @endforeach
              @endif
            </select>
              <div class="form-control-feedback text-danger" id="city_alert"></div>
           </div>
         </div> 
      </div>  

      <div class="row">

  
        <div class="col-md-4">
          <div class="form-group">       
            <label for="area" class="form-control-label">Area</label>
            <br/>
            <input type="text" name="areas" id="areas" class="form-control">
            <div class="form-control-feedback text-danger" id="areas_alert"></div>
          </div> 
        </div>
        <div class="col-md-3">
          <div class="form-group">       
            <label for="estimate_time" class="form-control-label">Time Estimate</label>
            <input type="text" name="estimate_time" id="estimate_time" class="form-control">
          </div>  
        </div>
        <div class="col-md-3">
          <div class="form-group">       
            <label for="charge" class="form-control-label">Delivery Charge</label>
            <input type="text" name="charge" id="charge" class="form-control">
          </div>  
        </div>  
        <div class="col-md-2">
          <div class="form-group">       
            <label for="min_order" class="form-control-label">Minium Order</label>
            <input type="text" name="min_order" id="min_order" class="form-control" value="0">
          </div>  
        </div>          
      </div>

                  
      <button class="btn btn-primary m-l-1 m-t-1 f-right" id="btn_create" type="button">Create</button>
      </form>
    </div>
  </div>  
  
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
         <h5 class="card-header-text">Lists</h5>
         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
         <thead>
            <tr>
               <th>Website</th>
               <th>Branch</th>
               <th>Areas</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
      @if(isset($deliveryList))
       @foreach($deliveryList as $value)
				<tr>
				  <td>{{ $value->website_name }}</td>
				  <td>{{ $value->branch_name }}</td>

          <td id="areaColumn{{ $value->branch_id }}" onclick="getAreaValues({{ $value->website_id }},{{ $value->branch_id }})" class="pointer">
            @php $count = 0 @endphp
	          @if(!empty($deliveryAreaValues))
             @foreach($deliveryAreaValues as $area_val)
                 @if($area_val->branch_id == $value->branch_id)
                   @php $count = $count + 1 @endphp
                   
                   <!--<input type="hidden" id="modifyUrlArea{{-- $area_val->id --}}" value="{{-- route('deliveryAreaNameUpdate',$area_val->id) --}}"/>-->
                   <input type="hidden" id="removeUrlArea{{ $area_val->id }}" value="{{ route('deliveryAreaValueDestroy',[$area_val->id,$value->branch_id]) }}"/>
                   <label class="label {{ $value->status == 1 ? 'bg-primary' : 'bg-gray' }} m-1" id="areaLabel{{ $area_val->id }}">{{ $area_val->name.' - Rs.'.$area_val->charge }}</label>
                 @endif  
             @endforeach
              
            @endif 

          </td>
          <!--<td id="charge{{-- $value->branch_id --}}"   class="pointer" onclick="modifyField({{-- $value->branch_id --}},'{{-- $value->charge --}}','charge')">-->
          <!--    <input type="hidden" id="modifyUrl{{-- $value->branch_id --}}" value="{{-- route('deliveryAreaUpdate',[$value->website_id,$value->branch_id]) --}}"/> {{-- 'PKR '.$value->charge --}}</td>-->
          <!--<td id="minOrder{{-- $value->branch_id --}}" class="pointer" onclick="modifyField({{-- $value->branch_id --}},'{{-- $value->min_order --}}','min_order')">{{-- 'PKR '.$value->min_order --}}</td>-->
				  <td class="action-icon">
                    				      
                    <a href="javascript:void(0)" class="m-r-1" title="Add Area Name" onclick="createAreaName({{ $value->website_id }},{{ $value->branch_id }},'{{ addslashes($value->website_name) }}','{{ addslashes($value->branch_name) }}')"><i class="icofont icofont-plus text-success"></i></a>				      
				      
                    <i class="icofont icofont-ui-wifi {{ $value->status == 1 ? 'text-success' : 'text-muted' }} f-18 m-r-1" data-toggle="tooltip" data-placement="top" data-original-title="Live " onclick="swalModal({{ $value->website_id }},{{ $value->branch_id }},1,'{{ addslashes($value->website_name) }}','{{ addslashes($value->branch_name) }}',{{ $value->status }})"></i>
          
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm pointer m-r-1" data-toggle="tooltip" data-placement="top" data-original-title="Delete" onclick="swalModal({{ $value->website_id }},{{ $value->branch_id }},0,'{{ addslashes($value->website_name) }}','{{ addslashes($value->branch_name) }}','')"></i>
					
					<form id="removeDeliveryArea{{ $value->website_id.$value->branch_id }}" action="{{ route('deliveryAreaDestroy',[$value->website_id,$value->branch_id]) }}" method="post">
					    @csrf
					    @method('DELETE')
					    <input type="hidden" name="websiteName" value="{{ $value->website_name }}">
					    <input type="hidden" name="branchName" value="{{ $value->branch_name }}">
					</form>
					
					<form id="DeliveryAreaForm_act_inact{{ $value->website_id.$value->branch_id }}" action="{{ route('deliveryAreaUpdate',[$value->website_id,$value->branch_id]) }}" method="post">
					    @csrf
					    @method('Patch')
					    <input type="hidden" name="status" id="status{{ $value->website_id.$value->branch_id }}" value="1">
					</form>
				  </td>
				</tr>
       @endforeach
       @endif
         </tbody>
     </table>
  </div>
</div>
</section>

<div class="modal fade modal-flex" id="editArea_md" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title">Edit Area Name</h4>
               </div>
               <div class="modal-body">
                   
                   <div class="" id="alert_md"></div>
                   
                     <table class="table area_table_md">
                         <thead>
                            <tr>
                              <th>Area Name</th>
                              <th>Charge</th>
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
                  <button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal">Close</button>
               </div>
            </div>
         </div>
      </div> 

<div class="modal fade modal-flex" id="modal_modifyField" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title" id="title_md_mf">Edit</h4>
               </div>
               <div class="modal-body">
                   
                   <form action="" name="modifyFieldForm_md" method="post">
                     @csrf
                     @method('PATCH')

                     <input type="hidden" name="branchId" id="branchId_md_mf">
                     <input type="hidden" name="mode" value="1" id="mode_md_mf">

                     <div class="form-group">
                       <label id="area_md_mf">Area Name</label>
                       <input type="text" name="" id="area_md_mf" class="form-control">
                       <span id="area_md_mf_alert"></span>
                     </div>
                     
                   </form>
               </div>
               <div class="modal-footer">
                  <button type="button" id="btn_update_md_mf" class="btn btn-success waves-effect waves-light">Update</button>
               </div>
            </div>
         </div>
      </div> 
      
<div class="modal fade modal-flex" id="createAreaName_Modal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title" id="title_md_mf">Add Area Name</h4>
               </div>
               <div class="modal-body">
                   <form id="createAreaForm_md" method="post">
                     @csrf
                     <input type="hidden" name="websiteId" id="websiteId_md_can">
                     <input type="hidden" name="branchId" id="branchId_md_can"> 
                     <input type="hidden" name="websiteName" id="websiteName_md_can">
                     <input type="hidden" name="branchName" id="branchName_md_can">
                     
                     <div class="form-group">
                       <input type="text" name="areaName_md" id="areaName_md" class="form-control" placeholder="Area Name">
                       <span id="areaName_md_alert"></span>
                     </div>
                   </form>
               </div>
               <div class="modal-footer">
                  <button type="button" id="btn_creatre_md_can" class="btn btn-success waves-effect waves-light">Save</button>
               </div>
            </div>
         </div>
      </div>      

@endsection

@section('scriptcode_three')



<script type="text/javascript">

  $(".select2").select2();

   $("#areas").tagsinput({
     maxTags: 40
    });

	$('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Delivery Areas',
          lengthMenu: '<span></span> _MENU_'
        }
    });
    
    $("#btn_remove_md").on('click',function(){
        $("#removeDeliveryAreaValueForm"+$("#id_md_edarea").val()).submit();
    }); 
    
    
  function getAreaValues(webId,brnhId){
      
        $.ajax({
                url: "{{ route('getdeliveryAreasValues') }}",
                type:"POST",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                webId:webId,
                branchId:brnhId
              },
              success:function(r){  
                $("#editArea_md").modal("show");
                $(".area_table_md tbody").empty();
                $(".area_table_md").removeClass('dataTable');
                  for(var s=0;s < r.length ;s++){
                      $(".area_table_md tbody").append(
                          "<tr id='tbl_md_row"+r[s].id+"'>"+
                            "<td><input type='text' value='"+r[s].name +"' class='form-control' id='name_md_"+r[s].id+"'/>"+
                            "<td><input type='text' value='"+r[s].charge +"' class='form-control' id='charge_md_"+r[s].id+"'/>"+
                            "<td class='action-icon'><i onclick='updateAreaDetail("+r[s].id+","+brnhId+")' class='btn btn-primary'>Update</i></td>"+
                          "</tr>"
                       );
                   }
              }
        });
      
  }
  
  function updateAreaDetail(id,brnchId){
    
      $.ajax({
              url:"{{ route('deliveryAreaNameUpdate') }}",
              type:"POST",
              data:{_token:'{{ csrf_token() }}',id:id,area:$("#name_md_"+id).val(),charge:$("#charge_md_"+id).val()},
              async:true,
              dataType:'json',
              success:function(resp){
                   console.log(resp)
                   if(resp.status == 200){
                       $("#alert_md").text('Success!').addClass('alert alert-success');
                       
                       $("#areaLabel"+id).text($("#name_md_"+id).val()+' - Rs.'+$("#charge_md_"+id).val());
                   }else{
                       $("#alert_md").text(resp.msg).addClass('alert alert-alert');
                   }
              }
             });
      
  }
  
  function removeAreaDetail(id,brnchId){

         swal({
                title: 'Remove Delivery Area',
                text:  'Are you sure you want to remove this '+$("#name_md_"+id).val()+' delivery area?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                      $.ajax({
                              url:$("#removeUrlArea"+id).val(),
                              type:"DELETE",
                              data:{_token:'{{ csrf_token() }}',id:id,stp_redirect:1},
                              async:true,
                              dataType:'json',
                              success:function(resp){
                                   console.log(resp)
                                   if(resp.status == 200){
                                       $("#alert_md").text('Success!').addClass('alert alert-success');
                                       
                                       $("#areaLabel"+id).remove();
                                       $("#tbl_md_row"+id).remove();
                                   }else{
                                       $("#alert_md").text(resp.msg).addClass('alert alert-alert');
                                   }
                              }
                             });                    
                }else{
                    swal.close();
                }
                
            });      
  }
    
    
  function createAreaName(webId,brnhId,webName,brnhName){
      
      $("#createAreaName_Modal").modal('show');
      
      $("#websiteId_md_can").val(webId);
      $("#branchId_md_can").val(brnhId);
      $("#websiteName_md_can").val(webName);
      $("#branchName_md_can").val(brnhName);
      
  }   
  
  $("#btn_creatre_md_can").on('click',function(){
      if($("#areaName_md").val() == ''){
          $("#areaName_md").focus();
          $("#areaName_md_alert").text('Field is required.').addClass('text-danger');
      }else{
          if($("#areaName_md_alert").hasClass('text-danger')){
              $("#areaName_md_alert").text('').removeClass('text-danger');
          }
          
          $.ajax({
                   url:'{{ route("deliveryAreaNameStore") }}',
                   type:'POST',
                   data:$("#createAreaForm_md").serialize(),
                   dataType:'json',
                   async:true,
                   success:function(resp){
                    console.log(resp)
                      if(Object.keys(resp)=='error'){
                          $("#areaName_md_alert").text(resp.error).addClass('text-danger');
                      }
                      
                      if(resp == 'success'){
                           window.location= '{{ route("deliveryAreasList") }}';
                      }
                   }
              
          });
      }
  })


  function modifyField(id,value,md){
      
      if($("#inputName_md_mf_alert").hasClass('text-danger')){
          $("#inputName_md_mf_alert").text('').removeClass('text-danger');
      }

      $("#modal_modifyField").modal('show');

      $("#labelName_md_mf").text(md.replace('_',' '));
      $("#mode_md_mf").val(md);
      $("#inputName_md_mf").val(value).attr('name',md);
    //   $("#branchId_md_mf").val(id);
    
      $("form[name='modifyFieldForm_md']").attr('action',$("#modifyUrl"+id).val())
  }
  
  $("#btn_update_md_mf").on('click',function(){
      if($("#inputName_md_mf").val() == ''){
          $("#inputName_md_mf").focus();
          $("#inputName_md_mf_alert").text('field is required.').addClass('text-danger');
      }else{
         $("form[name='modifyFieldForm_md']").submit(); 
      }
      
  });


   function editArea(id,webId,areaName,webName,charge){
     $("#editArea_md").modal('show');

     $("#id_md_edarea").val(id);
     $("#webid_md_edarea").val(webId);
     $("#areaName").val(areaName);
     $("#charge").val(charge);
     $("#webname_md_edarea").val(webName); 

     $("form[name='editForm_md']").attr('action',$("#modifyUrlArea"+id).val());   
   }

//   $("#btn_update_areaName").on('click',function(){
//       $("form[name='editForm_md']").submit();
//   })

   $("#btn_create").on('click',function(){
      var process_md = true;

      if($("#website").val().length === 0 ){
           $("#website").focus();
           $("#website_alert").html('Select website name is required.');
           process_md = false;
      }

      if($("#branch").val().length === 0 ){
           $("#branch").focus();
           $("#branch_alert").html('Select branch name is required.');   
           process_md = false;   
      }


      if($("#city").val().length === 0 ){
           $("#city").focus();
           $("#city_alert").html('Select city name is required.');   
           process_md = false;   
      }


      if($("#city").val().length === 0 ){
           $("#city").focus();
           $("#city_alert").html('Select city name is required.');   
           process_md = false;   
      }


      if(process_md){
                   $.ajax({
                    url:'{{ route("deliveryAreaStore") }}',
                    type:"POST",
                    data:$('#deliveryAreasForm').serialize(),
                    dataType:"json",
                    success:function(resp,status){
                      //console.log(resp+'/n'+status)
                       if(status == 'success'){
                            window.location = "{{ route('deliveryAreasList') }}";
                       }
                      if(resp == 1){
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


//   function swal_alert(title,msg,type,mode){
    
//       swal({
//             title: title,
//             text: msg,
//             type: type
//          },function(isConfirm){
//          if(isConfirm){
//             if(mode==true){
//               window.location="{{ route('deliveryAreasList') }}";
//             }
//           }
//       });
// }





function swalModal(id,branchId,mode,webName,brnhName,status){
  var title,text='';
  var btnClass = 'btn-danger'; 
       if(mode == 1){
           title    = (status == 1 ? 'In-Activate' : 'Activate')+' Delivery Areas';
           text     = 'Are you sure you want to '+(status == 1 ? 'In-Activate' : 'Activate')+' the delivery area from the '+webName+' website '+brnhName+' branch?';
           btnClass = 'btn-success';
       }else{
           title = 'Remove Delivery Areas';
           text  = 'Are you sure you want to remove the delivery area from the '+webName+' website '+brnhName+' branch?';           
       }        

            swal({
                title: title,
                text:  text,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: btnClass,
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     if(mode == 1){
                            if(status == 1){
                                $("#status"+id+branchId).val(0);
                            }
                         $("#DeliveryAreaForm_act_inact"+id+branchId).submit();
                     }else{
                         $("#removeDeliveryArea"+id+branchId).submit();
                     }
                }else{
                    swal.close();
                }
                
                // else{
                //     swal({
                //         title: "Cancel!",
                //         text: "All products are still inactive :)",
                //         type: "error"
                //     },function(isConfirm){
                //         if(isConfirm){
                //             //window.location="{{--url('/inventory-list')--}}";

                //         }
                //     });
                // }
            });
        }


   $("#website").on('change',function(){
        // alert($(this).val())
        $.ajax({
                 url:'{{ route("getWebsiteBranches") }}',
                 type:'POST',
                 data:{_token:'{{ csrf_token() }}',websiteId:$(this).val()},
                 async:true,
                 success:function(resp){
                    //  console.log(resp)
                    if(resp != null){
                       $("#branch").attr('disabled',false);
                       $.each(resp,function(i,v){
                          $("#branch").append("<option value='"+v.branch_id+"'>"+v.branch_name+"<option>")
                       })
                    }
                 }
        });
   });


 </script>
@endsection