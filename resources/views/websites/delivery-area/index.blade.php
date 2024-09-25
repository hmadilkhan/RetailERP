@extends('layouts.master-layout')

@section('title','Delivery Area Lists')

@section('breadcrumtitle','Delivery Area Lists')

@section('navwebsite','active')

@section('content')


<section class="panels-wells p-t-3">

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
     <form id="deliveryAreasForm" action="{{ route('deliveryAreaStore') }}" method="post">
       @csrf
      <div class="row">
         <div class="col-md-4">
          <div class="form-group">
            <label class="form-control-label">Website</label>
            <select name="website" id="website" data-placeholder="Select" class="form-control select2">
              <option value="">Select</option>
              @foreach($website as $val)
                 <option {{ old('$website') == $val->id ? 'selected' : '' }} data-type="{{ $val->type }}" value="{{ $val->id }}" >{{ $val->name }}</option> 
              @endforeach
            </select>
              @error('website')
              <div class="form-control-feedback text-danger" id="website_alert">Field is rquired</div>
              @enderror
           </div>    
         </div>  
         <div class="col-md-4">    
          <div class="form-group">
            <label class="form-control-label">Branch</label>
            <select name="branch" id="branch" data-placeholder="Select" class="form-control select2" disabled>
              <option value="">Select</option>
            </select>
              @error('branch')
              <div class="form-control-feedback text-danger" id="branch_alert">Field is rquired</div>
              @enderror
           </div>
          </div> 
         </div>
         
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">       
            <label for="charges" class="form-control-label">Delivery Charge (PKR)</label>
            <input type="text" name="charges" id="charges" class="form-control" value="{{ old('charges') }}">
              <div class="form-control-feedback text-danger" id="charges_alert"></div>
          </div>  
        </div>  
        <div class="col-md-4">
          <div class="form-group">       
            <label for="min_order" class="form-control-label">Minium Order</label>
            <input type="text" name="min_order" id="min_order" class="form-control" value="{{ old('min_order') ? old('min_order') : 0 }}">
          </div>  
        </div>          
      </div>
      
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">       
            <label for="estimate_time" class="form-control-label">Time Estimate</label>
            <input type="text" name="estimate_time" id="estimate_time" class="form-control">
            <div class="form-control-feedback text-danger" id="estimate_time_alert"></div>
          </div>  
        </div> 
        
        <div class="col-md-4">
          <div class="form-group">       
            <label for="estimate_day" class="form-control-label">Estimated delivery (days)</label>
            <input type="text" name="estimate_day" id="estimate_day" class="form-control">
            <div class="form-control-feedback text-danger" id="estimate_day_alert"></div>            
          </div>  
        </div>         
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">       
            <label for="city" class="form-control-label">City</label>
            <br/>
              @php $oldCity = old('city'); @endphp
            <select name="city" id="city" data-placeholder="Select" class="form-control select2">
              <option value="">Select</option>
              @foreach($city as $val)
                 <option {{ $val->city_id == $oldCity ? 'selected' : '' }}  value="{{ $val->city_id }}" >{{ $val->city_name }}</option> 
              @endforeach
            </select>
              <div class="form-control-feedback text-danger" id="city_alert"></div>
          </div> 
        </div> 
        <div class="col-md-4">
          <div class="form-group">       
            <label for="area" class="form-control-label">Area</label>
            <br/>
              @php $oldArea = old('areas') ? explode(',',old('areas')) : null; @endphp
            <input type="text" name="areas" id="areas" class="form-control" value="@if($oldArea != null) @foreach($oldArea as $val) {{ $val }} @endforeach @endif">
            <!--<div class="form-control-feedback text-danger" id="areas_alert"></div>-->
              <div class="form-control-feedback text-danger" id="areas_alert"></div>
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

     <table id="deliveryTable" class="table table-striped" width="100%" cellspacing="0">
         <thead>
            <tr>
               <th>Website</th>
               <th>Branch</th>
               <th>Locations</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
      @if(isset($deliveryList))
       @foreach($deliveryList as $parent_rowVal)
				<tr>
				  <td>{{ $parent_rowVal->website_name }}</td>
				  <td>{{ $parent_rowVal->branch_name }}</td>

          <td id="areaColumn{{ $parent_rowVal->branch_id }}" onclick="getAreaValues({{ $parent_rowVal->branch_id }},{{ $parent_rowVal->website_id }})"  class="pointer">
            @php $count = 0 @endphp
             @foreach($deliveryAreaValue as $area_val)
                 @if($area_val->website_id == $parent_rowVal->website_id)
                  
                   @php $count = $count + 1 @endphp
                   <input type="hidden" id="removeUrlArea{{ $area_val->id }}" value="{{ route('deliveryAreaValueDestroy',[$area_val->id,$area_val->branch_id]) }}"/>
                   {{-- @if($area_val->is_city == 1)
                   
                   <label class="label {{ $area_val->status == 1 ? 'bg-primary' : 'bg-gray' }} pointer m-1" id="areaLabel{{ $area_val->id }}">{{ $area_val->city_name.' - Rs.'.$area_val->charge }}</label>
                   @else --}}
                   <!--<input type="hidden" id="modifyUrlArea{{-- $area_val->id --}}" value="{{-- route('deliveryAreaNameUpdate',$area_val->id) --}}"/>-->
                   
                   <label class="label {{ $area_val->status == 1 ? 'bg-primary' : 'bg-gray' }} pointer m-1" id="areaLabel{{ $area_val->id }}">{{ $area_val->city_name.' - '.$area_val->name.' - Rs.'.$area_val->charge }}</label>
                   
                   {{-- @endif --}}
                 @endif  
             @endforeach
          </td>
          <!--<td id="charge{{-- $value->branch_id --}}"   class="pointer" onclick="modifyField({{-- $value->branch_id --}},'{{-- $value->charge --}}','charge')">-->
          <!--    <input type="hidden" id="modifyUrl{{-- $value->branch_id --}}" value="{{-- route('deliveryAreaUpdate',[$value->website_id,$value->branch_id]) --}}"/> {{-- 'PKR '.$value->charge --}}</td>-->
          <!--<td id="minOrder{{-- $value->branch_id --}}" class="pointer" onclick="modifyField({{-- $value->branch_id --}},'{{-- $value->min_order --}}','min_order')">{{-- 'PKR '.$value->min_order --}}</td>-->
				  <td class="action-icon">
                    				      
                    <a href="javascript:void(0)" class="m-r-1" title="Add Area Name" onclick="createSingleLocation({{ $parent_rowVal->branch_id }},'{{ addslashes($parent_rowVal->branch_name) }}',{{ $parent_rowVal->is_city }},{{ $parent_rowVal->website_id }},'{{ $parent_rowVal->website_name }}')"><i class="icofont icofont-plus text-success"></i></a>				      
				      
                    <i class="icofont icofont-ui-wifi {{ $parent_rowVal->status == 1 ? 'text-success' : 'text-muted' }} f-18 m-r-1" data-toggle="tooltip" data-placement="top" data-original-title="Live " onclick="swalModal({{ $parent_rowVal->branch_id }},1,'{{ addslashes($parent_rowVal->branch_name) }}',{{ $parent_rowVal->status }})"></i>
          
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm pointer m-r-1" data-toggle="tooltip" data-placement="top" data-original-title="Delete" onclick="swalModal({{ $parent_rowVal->branch_id }},0,'{{ addslashes($parent_rowVal->branch_name) }}','')"></i>
					
					<form id="removeDeliveryArea{{ $parent_rowVal->branch_id }}" action="{{ route('deliveryAreaDestroy',$parent_rowVal->branch_id) }}" method="post">
					    @csrf
					    @method('DELETE')
					    <input type="hidden" name="branchName" value="{{ $parent_rowVal->branch_name }}">
					</form>
					
					<form id="DeliveryAreaForm_act_inact{{ $parent_rowVal->branch_id }}" action="{{ route('deliveryAreaUpdate',$parent_rowVal->branch_id) }}" method="post">
					    @csrf
					    @method('Patch')
					    <input type="hidden" name="status" id="status{{ $parent_rowVal->branch_id }}" value="1">
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
                  <h4 class="modal-title" id="title_editmd">Edit Area Name</h4>
               </div>
               <div class="modal-body">
                   
                   <div class="" id="alert_md"></div>
                   
                     <table class="table area_table_md">
                         <thead>
                            <tr>
                              <th>Location</th>
                              <th>Area</th>
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
      
<div class="modal fade modal-flex" id="createSingleLocation_Modal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title" id="title_md_csl">Add Area Name</h4>
               </div>
               <div class="modal-body">
                   <form id="createLocationForm_md" method="post">
                     @csrf
                     <input type="hidden" name="branchId" id="branchId_md_can"> 
                     <input type="hidden" name="branchName" id="branchName_md_can">
                     <input type="hidden" name="websiteId" id="websiteId_md"> 
                     <input type="hidden" name="websiteName" id="websiteName_md">                     
                     <input type="hidden" name="iscity" id="city_md_can">

                      <div class="form-group d-none" id="cityBox_md">       
                        <label for="city" class="form-control-label">City</label>
                        <br/>
                        <select name="city" id="city_md" data-placeholder="Select" class="form-control select2">
                          <option value="">Select</option>
                          @foreach($city as $val)
                             <option value="{{ $val->city_id }}" >{{ $val->city_name }}</option> 
                          @endforeach
                        </select>
                          <div class="text-danger" id="city_md_alert"></div>
                      </div> 
                     
                     <div class="form-group d-none" id="areaBox_md">
                       <input type="text" name="areaName_md" id="areaName_md" class="form-control" placeholder="Area Name">
                       <span id="areaName_md_alert"></span>
                     </div>
                     <div class="form-group">
                       <input type="text" name="charges_md" id="charges_md" class="form-control" placeholder="Delivery Charges">
                       <span id="charges_md_alert"></span>
                     </div>                     
                     <div class="form-group">
                       <input type="text" name="min_order_md" id="min_order_md" class="form-control" placeholder="Min Order">
                       <span id="min_order_md_alert"></span>
                     </div>  
                     <div class="form-group">
                       <input type="text" name="estimate_md" id="estimate_md" class="form-control" placeholder="Estimate Time">
                       <span id="estimate_md_alert"></span>
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

	$('#deliveryTable').DataTable({
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
 

  $("#btn_create").on('click',function(){
     

      var process_md = true;

      if($("#website").val() == ''){
          $("#website").focus();
          $("#website_alert").text('Select website is required.');   
          process_md = false;   
      }
      
      if($("#branch").val() == ''){
          $("#branch").focus();
          $("#branch_alert").text('Select branch name is required.');   
          process_md = false;   
      }  
      
      if($("#charges").val() == ''){
          $("#charges").focus();
          $("#charges_alert").text('field is required.');   
          process_md = false;   
      } 
      
      if($("#areas").val() == ''){
          $("#areas").focus();
          $("#areas_alert").text('field is required.');   
          process_md = false;   
      }  
      
      if($("#time_estimate").val() == ''){
          $("#time_estimate").focus();
          $("#time_estimate_alert").text('field is required.');   
          process_md = false;   
      }       
      
      if($("#city").val() == ''){
          $("#city").focus();
          $("#city_alert").text('field is required.');   
          process_md = false;   
      }       
      
      if($("#estimate_day").val() == ''){
          $("#estimate_day").focus();
          $("#estimate_day_alert").text('field is required.');   
          process_md = false;   
      } 


      if(process_md){
            $("#deliveryAreasForm").submit();
                //   $.ajax({
                //     url:'{{-- route("deliveryAreaStore") --}}',
                //     type:"POST",
                //     data:$('#deliveryAreasForm').serialize(),
                //     dataType:"json",
                //     success:function(resp,status){
                //       //console.log(resp+'/n'+status)
                //       if(status == 'success'){
                //             window.location = "{{-- route('deliveryAreasList') --}}";
                //       }
                //       if(resp == 1){
                //           if(r.contrl != ""){
                //             $("#"+r.contrl).focus();
                //             $("#"+r.contrl+"_alert").html(r.msg);
                //           }
                //           swal_alert('Alert!',r.msg,'error',false); 

                //       }else {
                //          $("#deptname_alert").html('');
                //         swal_alert('Successfully!',r.msg,'success',true);
                //          $("#subdpt").tagsinput('removeAll');
                //       }
                //     }
                //   });
      }

  }); 
    
    
  function getAreaValues(brnhId,webId,md=''){
      $(".area_table_md tbody").empty();
        $.ajax({
                url: "{{ route('getdeliveryAreasValues') }}",
                type:"POST",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                branchId:brnhId,
                website:webId,
                mode:md,
              },
              success:function(r){  
                  if(md == 1){
                      $('#title_editmd').text('Edit City Name');
                  }else{
                      $('#title_editmd').text('Edit Area Name');
                  }
                $("#editArea_md").modal("show");
                
                $(".area_table_md").removeClass('dataTable');
                  for(var s=0;s < r.length ;s++){
                      // if(md == 1){ 
                      //     $(".area_table_md tbody").append(
                      //         "<tr id='tbl_md_row"+r[s].id+"'>"+
                      //           "<td id='name_md_"+r[s].id+"'>"+r[s].city_name+"</td>"+
                      //           "<td><input type='text' value='"+r[s].charge +"' class='form-control' id='charge_md_"+r[s].id+"'/></td>"+
                      //           "<td class='action-icon'><i onclick='updateAreaDetail("+r[s].id+","+brnhId+",1)' class='btn btn-primary'>Update</i></td>"+
                      //         "</tr>"
                      //      );
                      // }
                      
                      // if(md == 0){
                          $(".area_table_md tbody").append(
                              "<tr id='tbl_md_row"+r[s].id+"'>"+
                                "<td id='location_name_md_"+r[s].id+"'>"+r[s].city_name +"</td>"+
                                "<td><input type='text' value='"+r[s].name +"' class='form-control' id='name_md_"+r[s].id+"'/></td>"+
                                "<td><input type='text' value='"+r[s].charge +"' class='form-control' id='charge_md_"+r[s].id+"'/></td>"+
                                "<td>"+
                                  "<i onclick='updateAreaDetail("+r[s].id+","+brnhId+")' class='btn btn-primary'>Update</i>"+
                                  "<i onclick='deleteAreaDetail("+r[s].id+","+brnhId+")' class='btn btn-danger'>Remove</i>"+
                                  "</td>"+
                              "</tr>"
                           );                          
                      // }
                   }
              }
        });
      
  }
  
  function updateAreaDetail(id,brnchId,md=''){
    
      $.ajax({
              url:"{{ route('deliveryAreaNameUpdate') }}",
              type:"POST",
              data:{_token:'{{ csrf_token() }}',id:id,area:$("#name_md_"+id).val(),charge:$("#charge_md_"+id).val(),mode:md},
              async:true,
              dataType:'json',
              success:function(resp,txtStatus,jXst){
                  //  console.log(resp)
                   if(jXst.status == 200){
                       swal('Success!','','success');
                      //  $("#alert_md").text('Success!').addClass('alert alert-success');
                       let locationName = $("#name_md_"+id);
                       
                       $("#areaLabel"+id).text((md == 1 ? locationName.text() : locationName.val())+' - Rs.'+$("#charge_md_"+id).val());
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
    
    
  function createSingleLocation(brnhId,brnhName,md,webId,webName){
      
      if(md == 1){
          $("#title_md_csl").text('Add City Name');
          $("#city_md_can").val(1);
          divBox_on_off('cityBox_md',1);
          divBox_on_off('areaBox_md',0);
          
          $("#estimate_md").attr('placeholder','Estimate of day');
      }else{
          $("#title_md_csl").text('Add Area Name');
          $("#city_md_can").val(0);
          divBox_on_off('cityBox_md',0);
          divBox_on_off('areaBox_md',1);
          
          $("#estimate_md").attr('placeholder','Estimate time');
      }
      
      $("#createSingleLocation_Modal").modal('show');
      
      $("#branchId_md_can").val(brnhId);
      $("#branchName_md_can").val(brnhName);
      $("#websiteId_md").val(webId);
      $("#websiteName_md").val(webName);
      
  }   
  
  function singleArea_md(){
      var ElementId = ['locationName_md_alert','min_order_md_alert','charges_md_alert'];
      
      $.each(ElementId,function(i,v){
          if($("#"+v).hasClass('text-danger')){
              $("#"+v).text('').removeClass('text-danger');
          }
      })
  }
  
  $("#btn_creatre_md_can").on('click',function(){
    
    var process = true;  
    
      singleArea_md();
      
      if($("#areaName_md").val() == '' && $("#city_md_can").val() == 0){
          $("#areaName_md").focus();
          $("#areaName_md_alert").text('Field is required.').addClass('text-danger');
          process = false; 
      }
      
      if($("#city_md").val() == '' && $("#city_md_can").val() == 1){
          $("#city_md").focus();
          $("#city_md_alert").text('Field is required.').addClass('text-danger');
          process = false; 
      }      
      
      if($("#charges_md").val() == ''){
          $("#charges_md").focus();
          $("#charges_md_alert").text('Field is required.').addClass('text-danger');
          process = false; 
      }
      
      if($("#min_order_md").val() == ''){
          $("#min_order_md").focus();
          $("#min_order_md_alert").text('Field is required.').addClass('text-danger');
          process = false; 
      } 
      
      if(process){
          $.ajax({
                   url:'{{ route("deliveryAreaNameStore") }}',
                   type:'POST',
                   data:$("#createLocationForm_md").serialize(),
                   dataType:'json',
                   async:true,
                   success:function(resp){
                    console.log(Object.keys(resp))
                      if($.inArray('error',Object.keys(resp)) != -1){
                          if($.inArray('control',Object.keys(resp)) != -1){ 
                              $("#"+resp.control).focus();
                              swal('Cancel!',resp.error,'error');
                              $("#"+resp.control+"_alert").text(resp.error);
                          }else{
                               $("#msg_alert").text(resp.error);
                          }
                      }
                      
                      if(resp=='success'){
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


   function editArea(id,areaName,charge){
     $("#editArea_md").modal('show');

     $("#id_md_edarea").val(id);
     $("#areaName").val(areaName);
     $("#charge").val(charge); 

     $("form[name='editForm_md']").attr('action',$("#modifyUrlArea"+id).val());   
   }

//   $("#btn_update_areaName").on('click',function(){
//       $("form[name='editForm_md']").submit();
//   })




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





function swalModal(branchId,mode,brnhName,status){
  var title,text='';
  var btnClass = 'btn-danger'; 
       if(mode == 1){
           title    = (status == 1 ? 'In-Activate' : 'Activate')+' Delivery Areas';
           text     = 'Are you sure you want to '+(status == 1 ? 'In-Activate' : 'Activate')+' the delivery area from the '+brnhName+' branch?';
           btnClass = 'btn-success';
       }else{
           title = 'Remove Delivery Areas';
           text  = 'Are you sure you want to remove the delivery area from the '+brnhName+' branch?';           
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
                                $("#status"+branchId).val(0);
                            }
                         $("#DeliveryAreaForm_act_inact"+branchId).submit();
                     }else{
                         $("#removeDeliveryArea"+branchId).submit();
                     }
                }else{
                    swal.close();
                }
            });
        }


  $("#website").on('change',function(){
      
      //  if($(this).find(':selected').attr('data-type') == 'restaurant'){
      //     divBox_on_off('areaBox',1);
      //     divBox_on_off('cityBox',0);
      //  }else{
      //     divBox_on_off('cityBox',1);
      //     divBox_on_off('areaBox',0);

      //     // cityLoadNotExists();
      //  }

        $.ajax({
                 url:'{{ route("getWebsiteBranches") }}',
                 type:'POST',
                 data:{_token:'{{ csrf_token() }}',websiteId:$(this).val()},
                 async:true,
                 success:function(resp){
                    //  console.log(resp)
                    if(resp != null){
                      $("#branch").attr('disabled',false);
                      $("#branch").empty();
                      $("#branch").append("<option value=''>Select<option>");
                      $.each(resp,function(i,v){
                          $("#branch").append("<option value='"+v.branch_id+"'>"+v.branch_name+"<option>");
                      })
                    }
                 }
        });
  });


  function divBox_on_off(elementId,mode){
     if(mode == 1){
         if($("#"+elementId).hasClass('d-none')){
             $("#"+elementId).removeClass('d-none');
         }
     }else{
         if(!$("#"+elementId).hasClass('d-none')){
             $("#"+elementId).addClass('d-none');
         }         
     }
  }

  // $("#branch").on('change',function(){
  //   if($(this).val() != ''){
  //     cityLoadNotExists($(this).val(),$("#website").val());
  //   }

  // });

  function cityLoadNotExists(branch = '',website = ''){
    if(website != '' && branch != ''){
      $.ajax({
               url:'{{ route("cityLoadnotExistsdilveryArea") }}',
               type:'POST',
               data:{_token:'{{ csrf_token() }}',branchCode:branch,websiteCode:website,mode:1},
               dataType:'json',
               success: function(resp, txtStatus, jxState) {
            if (jxState.status === 200) {
              $("#city").empty();
                $.each(resp, function(i) {
                     $("#city").append('<option value="'+resp[i].city_id+'">'+resp[i].city_name+'</option>');
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            
        }
      }); // ajax method close
    }else{
      $.ajax({
               url:'{{ route("cityLoadnotExistsdilveryArea") }}',
               type:'POST',
               data:{_token:'{{ csrf_token() }}',mode:0},
               dataType:'json',
               success: function(resp, txtStatus, jxState) {
            if (jxState.status === 200) {
              $("#city").empty();
                $.each(resp, function(i) {
                     $("#city").append('<option value="'+resp[i].city+'">'+resp[i].city_name+'</option>');
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error:', textStatus, errorThrown);
            
        }
      });    
    }
  }

 </script>
@endsection