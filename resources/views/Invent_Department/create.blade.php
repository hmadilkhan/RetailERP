@extends('layouts.master-layout')

@section('title','Create Inventory Department')

@section('breadcrumtitle','Create Department')
@section('navinventory','active')
@section('navinvent_depart','active')

@section('content')

<section class="panels-wells p-t-30">

@if(Session::has('error'))
  <div class="alert alert-danger">{{ Session::get('error') }}</div>
@endif

@if(Session::has('success'))
  <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif

@if(Auth::user()->username == 'uzair.sdb')
   {{ $depart }}
@endif

<form method="post" id="deptform" action="{{ route('invent_dept.store') }}" class="form-horizontal" enctype="multipart/form-data">
   @csrf
   <div class="row">
     <div class="col-md-8 p-1">
        <div class="card">
             <div class="card-header">
                <h5 class="card-header-text" id="title-hcard"> Create Department</h5>
              </div>
              <div class="card-block">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Department Code <span class="text-muted">(Optional)</span></label>
                      <input class="form-control" type="text" name="department_code" id="department_code" value="{{ old('department_code') }}" placeholder='Department Code'/>
                      @error('department_code')
                      <span class="form-control-feedback text-danger" id="department_code_alert">{{ $message }}</span>
                      @enderror
                  </div>
                </div>

    		    <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Department Name <span class="text-danger">*</span></label>
                      <input class="form-control" type="text"
                       name="department_name" id="department_name" placeholder='Department Name' value="{{ old('department_name') }}"/>
                       @error('department_name')
                       <span class="form-control-feedback text-danger" id="department_name_alert">{{ $message }}</span>
                       @enderror
                  </div>
                </div>

                {{-- <div class="col-lg-4 col-md-4">
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
                </div>                 --}}

            </div>
           @if($websites)
            <hr/>
            <div class="form-group">
                <label for="showWebsite">
                    <input type="checkbox" id="showWebsite" name="showWebsite" {{ old('showWebsite') ? 'checked' : ''}}>
                    Show on Website
                </label>
            </div>
           <div class="d-none" id="website-module">
              <div class="row">
              <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Section</label>
                      <select class="select2" name="sections[]" id="sections" placeholder='Select' multiple>
                        <option value="">Select</option>
                        @php $oldSection = (array) old('sections') @endphp
                        @foreach($sections as $val)
                         <option {{ in_array($val->id,$oldSection) ? 'select' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                      </select>
                       <span class="form-control-feedback text-danger" id="webdeptname_alert"></span>
                  </div>
                </div>
              </div>
              <div class="row">
    		       <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Show website department name</label>
                      <input class="form-control" type="text"
                       name="website_department_name" id="website_department_name" placeholder='Show website department name' value="{{ old('website_department_name') }}"/>
                       @error('website_department_name')
                        <span class="form-control-feedback text-danger" id="website_department_name_alert"></span>
                       @enderror
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Meta Title</label>
                      <input class="form-control" type="text"
                       name="metatitle" id="metatitle" placeholder='Meta Title' value="{{ old('metatitle') }}"/>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Meta Description</label>
                      <textarea class="form-control" rows="5"
                       name="metadescript" id="metadescript" placeholder='Meta Description'></textarea>
                  </div>
                </div>
              </div>
            </div>
           @endif
         </div>
       </div>
       <div class="form-group row justify-content-center">
          <button class="btn  btn-lg btn-primary f-left m-t-30 m-l-20"  type="submit" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus"
            ></i>&nbsp; Save</button>.
              <a class="btn btn-lg btn-danger f-left m-t-30 m-l-10" href="{{ route('invent_dept.index') }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Discard"><i class="icofont icofont-error"
            ></i> Discard</a>
       </div>
     </div> <!-- field portion-->
     <div class="col-md-4 p-1">


     <div class="card">
                  <div class="card-header">
                  <h4 for="department_image">Image</h4>
                  </div>
                  <div class="card-block p-2 p-t-0">
              <div class="form-group">
                       <a href="javascript:void(0)">
                        <img id="previewDepartImage" src="{{ asset('storage/images/placeholder.jpg') }}" height="180px" width="180px" class="thumb-img" alt="img">
                        </a>

                    <div class="form-group {{ $errors->has('department_image') ? 'has-danger' : '' }} m-t-10">


                                    <label for="department_image" class="custom-file">
                                                <input type="file" name="department_image" id="department_image" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                            <br/>
                                        @error('department_image')
                                            <span class="form-control-feedback">{{ $message }}</span>
                                        @enderror
                              </div>

              </div>
              </div>
              </div>
        @if($websites)
        <div class="card d-none" id="banner-imageBox">
                  <div class="card-header">
                     <h4 for="bannerImage">Desktop Banner</h4>
                  </div>
                  <div class="card-block p-2 p-t-0">
                    <div class="form-group">
                       <a href="javascript:void(0)">
                        <img id="previewDepartBannerImage" src="{{ asset('storage/images/placeholder.jpg') }}" height="180px" class="thumb-img width-100" alt="img">
                        </a>

                    <div class="form-group {{ $errors->has('banner_image') ? 'has-danger' : '' }} m-t-10">
                                    <label for="banner_image" class="custom-file">
                                                <input type="file" name="banner_image" id="banner_image" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                            <br/>
                                @error('banner_image')
                                    <span class="form-control-feedback">{{ $message }}</span>
                                @enderror
                              </div>

              </div>
            </div>
          </div>

          <div class="card d-none" id="mobile-banner-imageBox">
            <div class="card-header">
               <h4 for="bannerImage">Mobile Banner</h4>
            </div>
            <div class="card-block p-2 p-t-0">
              <div class="form-group">
                 <a href="javascript:void(0)">
                  <img id="previewDepartMobileBannerImage" src="{{ asset('storage/images/placeholder.jpg') }}" height="200" width="150" class="" alt="img">
                  </a>

              <div class="form-group {{ $errors->has('mobile_banner') ? 'has-danger' : '' }} m-t-10">
                              <label for="mobile_banner" class="custom-file">
                                          <input type="file" name="mobile_banner" id="mobile_banner" class="custom-file-input">
                                          <span class="custom-file-control"></span>
                                      </label>
                                      <br/>
                          @error('mobile_banner')
                              <span class="form-control-feedback">{{ $message }}</span>
                          @enderror
                        </div>

        </div>
      </div>
    </div>
        @endif
     </div> <!-- col-md-4 close image portion -->
   </div>
   </form>
</section>


@endsection


@section('scriptcode_three')

<script type="text/javascript">
$(".select2").select2();

@if(old('metadescript'))
   $("#metadescript").val('{{ old("metadescript") }}');
@endif

@if(old('showWebsite'))
$("#showWebsite").trigger('click');
@endif

// $("#parent").on('change',function(){
//   if($(this).val() != '' && $("#showWebsite").is(':checked')==true){
//     $("#showWebsite").trigger('click');
//   }
// });

$("#showWebsite").on('click',function(){

    if($(this).is(':checked')==true){
      // $("#parent").val('').change();
        if($("#website-module").hasClass('d-none')){
            $("#website-module").removeClass('d-none');
        }

        if($("#banner-imageBox").hasClass('d-none')){
            $("#banner-imageBox").removeClass('d-none');
        }

        if($("#mobile-banner-imageBox").hasClass('d-none')){
            $("#mobile-banner-imageBox").removeClass('d-none');
        }
    }

    if($(this).is(':checked')==false){
        if(!$("#website-module").hasClass('d-none')){
            $("#website-module").addClass('d-none');
        }

        if(!$("#banner-imageBox").hasClass('d-none')){
            $("#banner-imageBox").addClass('d-none');
        }

        if(!$("#mobile-banner-imageBox").hasClass('d-none')){
            $("#mobile-banner-imageBox").addClass('d-none');
        }
    }
});



// $("#deptform").on('submit',function(event){
//       event.preventDefault();

//       var formData = new FormData(this);

//     if($("#deptname").val() == ""){
//          $("#deptname").focus();
//          $("#deptname_alert").html('Department name is required.');
//          swal('Cancel!','Department name is required.','error',false);
//     }else{
//       $('#uploadForm').on('submit', function(event) {
//     event.preventDefault(); // Prevent the default form submission

//     var formData = new FormData(this); // Create FormData object directly from form element

//     $.ajax({
//         url: '{{ route("invent_dept.store") }}',
//         type: 'POST',
//         data: formData,
//         dataType: 'json',
//         cache: false,
//         contentType: false,
//         processData: false,
//         success: function(r, textStatus, jqXHR) {
//             var statusCode = jqXHR.status;
//             if (statusCode == 500) {
//                 if (r.contrl != "") {
//                     $("#" + r.contrl).focus();
//                     $("#" + r.contrl + "_alert").html(r.msg);
//                 }
//                 swal_alert('Alert!', r.msg, 'error', false);
//             } else {
//                 $("#deptname_alert").html('');
//                 swal_alert('Successfully!', r.msg, 'success', true);
//                 $("#subdpt").tagsinput('removeAll');
//             }
//         },
//         error: function(jqXHR, textStatus, errorThrown) {
//             swal_alert('Alert!', errorThrown, 'error', false);
//         }
//     });
// });
        //        $.ajax({
        //         url:'{{ route("invent_dept.store") }}',
        //         type:"POST",
        //         data:formData,
        //         dataType:"json",
    		//     cache:false,
    		//     contentType: false,
    		//     processData: false,
        //         success:function(r){
    		// // 		console.log(r)
        //           if(r.state == 1){
        //               if(r.contrl != ""){
        //                 $("#"+r.contrl).focus();
        //                 $("#"+r.contrl+"_alert").html(r.msg);
        //               }
        //               swal_alert('Alert!',r.msg,'error',false);

        //           }else {
        //              $("#deptname_alert").html('');
        //             swal_alert('Successfully!',r.msg,'success',true);
        //              $("#subdpt").tagsinput('removeAll');
        //           }
        //         }
        //       });
  //   }
  // });

  function readURL(input,id) {
		  if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
			  $('#'+id).attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#department_image").change(function() {
	   readURL(this,'previewDepartImage');
	});

  $("#banner_image").change(function() {
	   readURL(this,'previewDepartBannerImage');
	});

  $("#mobile_banner").change(function() {
	   readURL(this,'previewDepartMobileBannerImage');
	});

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
</script>

@endsection
