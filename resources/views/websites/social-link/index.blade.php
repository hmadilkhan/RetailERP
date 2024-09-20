@extends('layouts.master-layout')

@section('title','Social Link Lists')

@section('breadcrumtitle','Social Link Lists')

@section('navwebsite','active')

@section('content')

  
<section class="panels-wells p-t-3">
<div class="card">
  <div class="card-header">
    <h5 class="card-header-text">Create Social Link</h5>
  </div>      
    <div class="card-block ">
     <form method="post" class="form-inline" action="{{ route('socialinkStore') }}"> 
        @csrf
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
          <label class="form-control-label">Social Type</label>
          <select name="socialType" id="socialType" data-placeholder="Select" class="form-control select2">
            <option value="">Select</option>
              <option {{ old('socialType') == 'fb' ? 'selected' : '' }} value="fb">FaceBook</option>
              <option {{ old('socialType') == 'insta' ? 'selected' : '' }} value="insta">Instagram</option>
              <option {{ old('socialType') == 'linkedin' ? 'selected' : '' }} value="linkedin">Linkedin</option>
              <option {{ old('socialType') == 'twite' ? 'selected' : '' }} value="twite">Twitter</option>
              <option {{ old('socialType') == 'youtube' ? 'selected' : '' }} value="youtube">YouTube</option>
              <option {{ old('socialType') == 'tiktok' ? 'selected' : '' }} value="tiktok">TikTok</option>
              <option {{ old('socialType') == 'pinterest' ? 'selected' : '' }} value="pinterest">Pinterest</option>
              <option {{ old('socialType') == 'snapchat' ? 'selected' : '' }} value="snapchat">Snapchat</option>
          </select>
        @error('socialType')
          <div class="form-control-feedback text-danger">Field is required please select it</div>
        @enderror          
        </div>
        <div class="form-group  m-r-2">       
          <label  class="form-control-label">URL</label>
          <input type="text" name="url" id="url" class="form-control">
        @error('url')
          <div class="form-control-feedback text-danger">Field is required</div>
        @enderror  
        
        </div> 
                   
      <button class="btn btn-primary m-l-1 m-t-1" id="btn_create" type="submit">Add</button>
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
         <h5 class="card-header-text">Websites Social Link</h5>
         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th class="d-none">#</th>
               <th>Website</th>
               <th>Social Link</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
		
      @if(count($lists) > 0)
       @foreach($lists as $value)
				<tr>
				  <td class="d-none">{{ $value->website_id }}</td>
				  <td>{{ $value->name }}</td>
				  <td> 
				      @foreach($sublists as $sub_list)
				         @if($sub_list->website_id == $value->website_id)
				            
				            
        					<form id="UpdateFormValue{{ $sub_list->id }}" action="{{ route('socialinkUpdate',$sub_list->id) }}" method="post" class="d-none">
        					    @csrf
        					    @method('PATCH')
        					    <input type="hidden" value="{{ $sub_list->id }}" name="id">
        					    <input type="hidden" value="{{ $sub_list->social_type }}" id="upformType{{ $sub_list->id }}" name=type>
        					    <input type="hidden" value="{{ $sub_list->url }}" id="upformUrl{{ $sub_list->id }}" name="value">
        					</form>	
        					
        					<form id="DestroyFormValue{{ $sub_list->id }}" action="{{ route('socialinkDestroy',[$sub_list->id]) }}" method="post" class="d-none">
        					    @csrf
        					    @method('DELETE')
        					    <input type="hidden" name="mode" value="v">
        					</form>   
        					
				            <i class="{{ $sub_list->icon }} text-dark fa-2x" onclick="edit({{ $sub_list->id }},{{ $value->website_id }},'{{ $sub_list->social_type }}','{{ $sub_list->url }}')"></i>
				         @endif        
				      @endforeach
				  </td>
				  <td class="action-icon">
					<!--<a href="{{-- route('website.edit',$value->website_id) --}}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>-->
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="warning({{ $value->website_id }},'{{ $value->name }}')" data-toggle="tooltip" data-placement="top" data-original-title="Delete"></i>
					
					<form id="DestroyForm{{ $value->website_id }}" action="{{ route('socialinkDestroy',[$value->website_id]) }}" method="post" class="d-none">
					    @csrf
					    @method('DELETE')
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


<div class="modal fade modal-flex" id="edit_Modal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title" id="title_md_mf">Edit</h4>
               </div>
               <div class="modal-body">
                   <form id="createAreaForm_md" method="post">
             

                     <div class="form-group">
                          <label for="text_md" id="label_md"></label>
                          <input type="text" name="text_md" id="text_md" class="form-control">
                          <span id="alert_md" class=""></span>
                     </div>
                   </form>
               </div>
               <div class="modal-footer">
                   <button type="button" id="btn_remove_md" class="btn btn-danger waves-effect waves-light f-left">Remove</button>
                   <button type="button" data-dismiss="modal" class="btn btn-default waves-effect waves-light m-r-1">Close</button>
                  <button type="button" id="btn_update_md" class="btn btn-success waves-effect waves-light">Save Changes</button>
               </div>
            </div>
         </div>
      </div>  
@endsection

@section('scriptcode_three')



<script type="text/javascript">
  
  var id = null;
  $(".select2").select2();

	$('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Social Link',
          lengthMenu: '<span></span> _MENU_'
        }
    });
    
    function edit(unqid,webId,stype,surl){
        
        if($("alert_md").hasClass('text-danger')){
            $("alert_md").text('').removeClass('text-danger');
        }
        
        $("#edit_Modal").modal('show');
        $('#label_md').text(stype);
        $("#text_md").val(surl);
        id=unqid;
    }
    
    $("#btn_remove_md").on('click',function(){
         swal({
                title: 'Remove Scoial Link',
                text:  'Are you sure remove social links '+$('#label_md').text()+'?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     $("#DestroyFormValue"+id).submit();
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
    });
    
    $("#btn_update_md").on('click',function(){
        // alert(id)
        if($("#text_md").val() == ''){
           $("#text_md").focus();
           $("alert_md").text('Field is required.').addClass('text-danger');
        }else{
          $("#upformUrl"+id).val($("#text_md").val());
          $("#UpdateFormValue"+id).submit();
        }
        //     $.ajax({
        //          type:'PATCH',
        //          url: $("#urlUpdate"+id).val(),
        //          data:{_token:'{{ csrf_token() }}',id:id,type:$("#label_md").text(),value:$("#text_md").val()},
        //          async:true,
        //          success:function(data){
        //              console.log(data);
        //          },
        //          error: function(data){
        //              console.log("error");
        //              console.log(data);
        //          }
        //   });     
    })
    
//     $("#btn_create").on('click',function(){
//     //   var webid = $("#website");
//     //   var socialType = $("#socialType");
//     //   var formData = new FormData($('form[name="socialForm"]'));
//     //   var process = true;

//     //       if(webid.val() == ''){
//     //          process = false;
//     //          webid.focus();
//     //          $("#alert_website").text('Field is required.').addClass('text-danger');
//     //       }
          
//     //       if(socialType.val() == ''){
//     //          process = false;
//     //          socialType.focus();
//     //          $("#alert_socialType").text('Field is required.').addClass('text-danger');
//     //       } 
          
//     //       if(socialType.val() == ''){
//     //          process = false;
//     //          socialType.focus();
//     //          $("#alert_socialType").text('Field is required.').addClass('text-danger');
//     //       }          

//     //       if(process){
//     //             $('form[name="socialForm"]').submit();
//     //      }
//   })    
    
    
    function warning(webId,webName){
            swal({
                title: 'Remove Scoial Link',
                text:  'Are you sure remove social links from '+webName+' website?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     $("#DestroyForm"+webId).submit();
                }else{
                    swal.close();
                }
            });        
    }
 </script>
@endsection

@section('extra_css_js_libs')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>
@endsection