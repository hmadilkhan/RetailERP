@extends('layouts.master-layout')

@section('title','Website Details')

@section('breadcrumtitle','Website Details')

@section('navwebsite','active')

@section('content')


<section class="panels-wells p-t-3">

  @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

  @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

  <ul class="nav nav-tabs  tabs" role="tablist">
    <li class="nav-item">
       <a class="nav-link active" data-toggle="tab" id="sliderTabNav" href="#sliderTab" role="tab">Slider</a>
    </li>
    <li class="nav-item">
       <a class="nav-link" data-toggle="tab" id="departmentSliderNav" href="#departmentSliderTab" role="tab">Department Slider</a>
    </li>
 </ul>

    <!-- Tab panes -->
    <div class="tab-content tabs">
        <div class="tab-pane active" id="sliderTab" role="tabpanel">
            @include('websites.sliders.partials.default')
        </div>
        <div class="tab-pane" id="departmentSliderTab" role="tabpanel">
            @include('websites.sliders.partials.department-slider')
        </div>
     </div>




</section>



<div class="modal fade modal-flex" id="slideEdit_Modal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                  <h4 class="modal-title" id="title_md_mf">Edit Slide</h4>
               </div>
               <div class="modal-body">
                   <form id="editSlideForm_md" action="{{ route('updateSliderImage') }}" method="post" enctype="multipart/form-data">
                     @csrf

                     <input type="hidden" id="webname_md" name="webName">
                     <input type="hidden" id="webid_md" name="webId">
                     <input type="hidden" id="id_md" name="id">

                     <div class="form-group">
                           <img for="slide_md" src="{{ asset('storage/images/no-image.jpg') }}" class="img-fluid" id="slideImgMD" width="250" height="128"/>
                           <video id="slideVdMD" width="250" height="128" controls style="display: none;"></video>
                     </div>
                     <div class="form-group">
                          <label for="slide_md" class="custom-file">
                          <input type="file" name="slide_md" id="slide_md" class="custom-file-input">
                          <span class="custom-file-control"></span>
                          </label>
                     </div>

                    <div class="form-group">
                         <img for="mobile_slide_md" src="{{ asset('storage/images/no-image.jpg') }}" class="img-fluid" id="previewMobileSlide_md" width="100" height="150"/>
                         <video id="previewMobileSlideVd_md" width="200" height="250" style="display:none;" controls></video></br>
                        </div>
                   <div class="form-group">
                        <label for="mobile_slide_md" class="custom-file">
                        <input type="file" name="mobile_slide" id="mobile_slide_md" class="custom-file-input">
                        <span class="custom-file-control"></span>
                        </label>
                  </div>


                  <div class="alert alert-info">
                    Be informed that the required image size not exceeding 1MB.
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
                             @php $oldDepart = old('depart_md') @endphp
                            @foreach($departments as $val)
                              <option {{ old('depart_md') == $val->department_id ? 'selected' : '' }} value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                            @endforeach
                          @endif
                          </select>
                    </div>
                   </div>
                   <div class="d-none" id="productbox_md">
                     <div class="form-group">
                         <label class="form-control-label">Department</label>
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
                      <label class="form-control-label">Select Sub Department</label>
                      <select id="subDepartment_prod_editmd" data-placeholder="Select" class="form-control select2" disabled>
                        <option value="">Select</option>
                      </select>
                    </div>
                     <div class="form-group">
                          <label class="form-control-label">Product</label>
                          <select name="product_md" id="product_editmd" data-placeholder="Select" class="form-control select2" disabled>
                            <option value="">Select</option>
                          </select>

                    </div>
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

@section('scriptcode_one')
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

    $('#sliderCreateForm').on('submit', function() {
        $('#btn_create').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
    });
//    $("#btn_create").on('click',function(){
//        $(this).atrr('disabled',true);
//    })

   $("#btn_update_md").on('click',function(){
       $("#editSlideForm_md").submit();
   });

   $('#editSlideForm_md').on('submit', function() {
        $('#btn_update_md').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
    });

   function editSlide(unqid,webId,webName,depart,prod,prod_depart,prod_sbdepart,mobileSlide,ftype){
    $("#slideImgMD").attr('src',location.origin+'/storage/images/no-image.png');
    $("#previewMobileSlide_md").attr('src',location.origin+'/storage/images/no-image.png').show();
    $("#previewMobileSlideVd_md").attr('src',location.origin+'/storage/images/no-image.png').hide();
       $("#slideEdit_Modal").modal('show');

     if(ftype == 'vd'){
        $("#slideVdMD").attr('src',$("#slide"+unqid).attr('src'));
        $("#slideVdMD").show();
        $("#slideImgMD").hide();

     }else{
       $("#slideImgMD").attr('src',$("#slide"+unqid).attr('src'));
       $("#slideVdMD").hide();
       $("#slideImgMD").show();
     }

       $("#webname_md").val(webName);
       $("#webid_md").val(webId);
       $("#id_md").val(unqid);

       id=unqid;

       if(mobileSlide != ''){
        if(ftype == 'vd'){
            $("#previewMobileSlideVd_md").attr('src',location.origin+'/storage/images/website/sliders/{{ session("company_id") }}/'+webId+'/'+mobileSlide);
            $("#previewMobileSlideVd_md").show();
            $("#previewMobileSlide_md").hide();
        }else{
            $("#previewMobileSlide_md").attr('src',location.origin+'/storage/images/website/sliders/{{ session("company_id") }}/'+webId+'/'+mobileSlide);
            $("#previewMobileSlideVd_md").hide();
            $("#previewMobileSlide_md").show();
        }

       }


       if(prod != '' && depart == ''){
           $("#navigat_prod_md").trigger('click');

           $("#depart_editmd").val(prod_depart).trigger('change');

           load_subdept(prod_depart,webId,'subDepartment_prod_editmd',prod_sbdepart);
          getProduct(webId,'product_editmd',prod,depart,prod_sbdepart);
       }

       if(depart != '' && prod == ''){
           $("#navigat_depart_md").trigger('click');

           $("#depart_md").val(depart).trigger('change');
       }

       //$("#editSlideForm_md").attr('action',$("#updateSliderImage"+id).val());
   }

   $("#btn_remove_md").on('click',function(){
            swal({
                title: 'Remove Slider',
                text:  'Are you sure remove slider from '+$("#webname_md").val()+' website?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                    $("#mode"+$("#webid_md").val()).val(id);
                    $("#DestroyForm"+$("#webid_md").val()).submit();
                }else{
                    swal.close();
                }
            });

   })

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

  $("#desktop_slide").on('change',function(){
      readURL(this,'previewImg','videoPreview');
  });

  $("#mobile_slide").on('change',function(){
      readURL(this,'previewMobileSlide','videoMobilePreview');
  });

  $("#slide_md").on('change',function(){
    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.mp4|\.webm|\.ogg)$/i;  // Add the allowed extensions
        var file = this.files[0];  // Get the selected file
        // Check if a file is selected
            var fileName = file.name;  // Get the name of the file
            if (!allowedExtensions.exec(fileName)) {
                alert("Invalid file type. Please select an image (jpg, jpeg, png, gif) or video (mp4, webm, ogg).");
                $(this).val('');  // Clear the input field
            }

            if (inAyaar(file,['webm','mp4','ogg'])) {
                readURL(this,'slideVdImgMD');
            }else{
                readURL(this,'slideImgMD');
            }


  });

  $("#mobile_slide_md").on('change',function(){

    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.mp4|\.webm|\.ogg)$/i;  // Add the allowed extensions
        var file = this.files[0];  // Get the selected file
        // Check if a file is selected
            var fileName = file.name;  // Get the name of the file
            if (!allowedExtensions.exec(fileName)) {
                alert("Invalid file type. Please select an image (jpg, jpeg, png, gif) or video (mp4, webm, ogg).");
                $(this).val('');  // Clear the input field
            }

            if (inAyaar(file,['webm','mp4','ogg'])) {
                readURL(this,'previewMobileSlideVd_md');
            }else{
                readURL(this,'previewMobileSlide_md');
            }

  });

  function getProduct(webId,elemId,prod,depart,sub_depart){

           $.ajax({
             url: '{{ route("getWebsiteProd") }}',
             type:'POST',
             data:{_token:'{{ csrf_token() }}',id:webId,department:depart,subDepart:sub_depart},
             async:true,
             success:function(data){
                $("#"+elemId).empty().attr('disabled',false);
               if(data != null){
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

    function warning(webId,webName){
            swal({
                title: 'Remove Slider',
                text:  'Are you sure remove slider from '+webName+' website?',
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

    function readURL(input,id,msp_Id) {

  // Allowed file extensions for image and video
  var allowedImageExtensions = /(\.jpg|\.jpeg|\.png|\.webp)$/i;
    var allowedVideoExtensions = /(\.mp4|\.webm|\.ogg)$/i;

    if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();

        // Image preview
        if (allowedImageExtensions.test(file.name)) {
            reader.onload = function(e) {
                $('#' + id).attr('src', e.target.result); // Image preview
                $('#' + id).show(); // Show image element
                $('#'+msp_Id).hide(); // Hide video element
            }
            reader.readAsDataURL(file);
        }
        // Video preview
        else if (allowedVideoExtensions.test(file.name)) {
            reader.onload = function(e) {
                $('#'+msp_Id).attr('src', e.target.result); // Video preview
                $('#'+msp_Id).show(); // Show video element
                $('#' + id).hide(); // Hide image element
            }
            reader.readAsDataURL(file);
        } else {
            alert("Invalid file type. Please select a valid image (jpg, jpeg, png, gif) or video (mp4, webm, ogg).");
        }
    }


      /*  if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {

              $('#'+id).attr('src', e.target.result);

            }

            reader.readAsDataURL(input.files[0]);
        }*/
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
		        $("#subDepartment_prod_editmd").val('change');
		        if(!$("#subDepartment_prod_editmd").attr('disabled')){
		            $("#subDepartment_prod_editmd").attr('disabled',true);
		            $("#subDepartment_prod_editmd").val('');
		        }
		    } else{
		      load_subdept($(this).val(),$("#webid_md").val(),'subDepartment_prod_editmd',null);
		    }
		});

 		$("#subDepartment_prod_editmd").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_editmd").val('change');
		        if(!$("#product_editmd").attr('disabled')){
		            $("#product_editmd").attr('disabled',true);
		            $("#product_editmd").val('');
		        }
		    } else{
		          getProduct($('#webid_md').val(),'product_editmd','','',$(this).val());
		    }
		});

    function load_dept(id,elementId){
            $.ajax({
                url: "{{ route('getDepart_n_subDepart_wb') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",website:id,mode:'depart'},
                success:function(resp){
                    // console.log(resp)
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
                    // console.log(resp)
                    if(resp != null){
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
                }
            });
        }

        $("#website_dept_slide").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_dpt_slide").val('change');
		        if(!$("#product_dpt_slide").attr('disabled')){
		            $("#product_dpt_slide").attr('disabled',true);
		            $("#product_dpt_slide").val('');
		        }
		    } else{
                $("#department_dpt_slide").trigger('change');
		    }
		});

        $("#department_dpt_slide").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_dpt_slide").val('change');
		        if(!$("#product_dpt_slide").attr('disabled')){
		            $("#product_dpt_slide").attr('disabled',true);
		            $("#product_dpt_slide").val('');
		        }
		    } else{
		          getProduct($('#website_dept_slide').val(),'product_dpt_slide',$(this).val(),'');
		    }
		});

    $(document).ready(function() {
        // Check if there is a hash in the URL
        if (window.location.hash) {
            var hash = window.location.hash; // Get the hash part of the URL
            // Trigger click on the tab if it exists
            $('#departmentSliderNav').trigger('click');
        }
    });

 </script>
@endsection
