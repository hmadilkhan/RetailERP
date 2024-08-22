@extends('layouts.master-layout')

@section('title','Create Delivery Area')

@section('breadcrumtitle','Create Delivery Area')

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
               <th>City</th>
               <th>Areas</th>
               <th>Charge</th>
               <th>Min of Order</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
      @if(isset($deliveryList))
       @foreach($deliveryList as $value)
				<tr>
				  <td>{{ $value->website_name }}</td>
				  <td>{{ $value->branch_name }}</td>
          <td>{{ $value->city }}</td>
          <td>
            @php $count = 0 @endphp
	          @if(!empty($deliveryAreaValues))
             @foreach($deliveryAreaValues as $area_val)
                 @if($area_val->branch_id == $value->branch_id)
                   @php $count = $count + 1 @endphp
                   
                   <input type="hidden" id="modifyUrl{{ $area_val->id }}" value="{{ route('deliveryAreaUpdate',$area_val->id) }}"/>
                   <label class="label bg-primary m-1 pointer" id="label{{ $area_val->id }}" onclick="editArea({{ $area_val->id }},{{ $value->website_id }},'{{ $area_val->name }}','{{ $value->website_name }}')">{{ $area_val->name }}</label>
                 @endif  
             @endforeach
            @endif 

          </td>
          <td id="charge{{ $value->branch_id }}" class="pointer" onclick="modifyField({{ $value->branch_id }},'{{ $value->charge }}','charge')">{{ 'PKR '.$value->charge }}</td>
          <td id="minOrder{{ $value->branch_id }}" class="pointer" onclick="modifyField({{ $value->branch_id }},'{{ $value->min_order }}','min_order')">{{ 'PKR '.$value->min_order }}</td>
				  <td class="action-icon">
          <i class="icofont icofont-ui-wifi text-dark f-18" data-toggle="tooltip" data-placement="top" data-original-title="Live " onclick="aliveAllArea({{ $value->website_id }},{{ $value->branch_id }})"></i>
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm pointer" data-toggle="tooltip" data-placement="top" data-original-title="Delete" onclick="removeAllArea({{ $value->website_id }},{{ $value->branch_id }})"></i>
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
                   
                   <form action="" name="editForm_md" method="post">
                     @csrf
                     @method('PATCH')

                     <input type="hidden" name="id">
                     <input type="hidden" name="webId">
                     <input type="hidden" name="webName">

                     <div class="form-group">
                       <label>Area Name</label>
                       <input type="text" name="areaName" id="areaName" class="form-control">
                     </div>

                   </form>
               </div>
               <div class="modal-footer">
                  <button type="button" id="btn_update_areaName" class="btn btn-success waves-effect waves-light">Update</button>
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

                     <div class="form-group">
                       <label id="labelName_md_mf">label</label>
                       <input type="text" name="" id="inputName_md_mf" class="form-control">
                     </div>

                   </form>
               </div>
               <div class="modal-footer">
                  <button type="button" id="btn_update_md_mf" class="btn btn-success waves-effect waves-light">Update</button>
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


  function modifyField(id,value,md){

      $("#modal_modifyField").modal('show');

      $("#labelName_md_mf").text(md.replace('_',' '));

      $("#inputName_md_mf").val(value).attr('name',md);
      $("#branchId_md_mf").val(id);
  }


   function editArea(id,webId,areaName,webName){
     $("#editArea_md").modal('show');

     $("#id").val(id);
     $("#webId").val(webId);
     $("#areaName").val(areaName);
     $("#webName").val(webName); 

     $("form[name='editForm_md']").attr('action',$("#modifyUrl"+id).val());   
   }

   $("#btn_update_areaName").on('click',function(){
      $("form[name='editForm_md']").submit();
   })

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


  function swal_alert(title,msg,type,mode){
    
      swal({
            title: title,
            text: msg,
            type: type
         },function(isConfirm){
         if(isConfirm){
            if(mode==true){
              window.location="{{ route('deliveryAreasList') }}";
            }
          }
      });
}


function removeAllArea(id,webId){


            swal({
                title: "DELETE PRODUCTS",
                text: "Do you want to delete products?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            //window.location="{{--url('/inventory-list')--}}";

                        }
                    });
                }
            });
        }


   $("#website").on('change',function(){
        
        $.ajax({
                 url:'{{ route("getWebsiteBranches") }}',
                 type:'POST',
                 data:{_token:'{{ csrf_token() }}',websiteId:$(this).val()},
                 async:true,
                 success:function(resp){
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