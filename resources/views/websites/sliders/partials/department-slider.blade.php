<div class="card">
    <div class="card-header">
      <h5 class="card-header-text">Create Department Slider</h5>
    </div>
      <div class="card-block">
       <form id="sliderCreateForm " action="{{ route('sliderStore') }}" method="post" enctype="multipart/form-data">
         @csrf
        <input type='hidden' name="slider_type" value='{{ Hash::make('department') }}'>
    <div class="col-md-9">
       <div class="row">
          <div class="col-md-6">
            <div class="form-group @error('desktop_slide_dept') 'has-danger' @enderror m-r-2">
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="512" height="256" id="previewImg_deptslide"/>
                <video id="videoPreview_deptslide" width="512" height="256" style="display:none;" controls></video>
              </br>

              <label for="desktop_slide_dept" class="form-control-label">Desktop Slide</label></br>

              <label for="desktop_slide_dept" class="custom-file">
              <input type="file" name="desktop_slide_dept" id="desktop_slide_dept" class="custom-file-input">
              <span class="custom-file-control"></span>
              </label>
              @error('desktop_slide_dept')
                <div class="form-control-feedback text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group @error('mobile_slide_dept') 'has-danger' @enderror m-r-2">
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="200" height="250" id="previewMobileSlide_deptslide"/>
                <video id="videoMobilePreview_deptslide" width="200" height="250" style="display:none;" controls></video></br>
              <label for="mobile_slide_dept" class="form-control-label">Mobile Slide</label></br>

              <label for="mobile_slide_dept" class="custom-file">
              <input type="file" name="mobile_slide_dept" id="mobile_slide_dept" class="custom-file-input">
              <span class="custom-file-control"></span>
              </label>
              @error('mobile_slide_dept')
                <div class="form-control-feedback text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>
       </div>
    </div>

    <div class="col-md-4">
      <div class="form-group m-r-2">
        <label class="form-control-label">Slider Name <span class="text-danger">*</span></label>
        <input type="text" name="slider_name" id="slider_name" class="form-control">
        @error('slider_name')
          <div class="form-control-feedback text-danger">Field is required please enter the slider name</div>
        @enderror
   </div>
        <div class="form-group m-r-2">
          <label class="form-control-label">Website <span class="text-danger">*</span></label>
          <select name="website_dept_slide" id="website_dept_slide" data-placeholder="Select" class="form-control select2">
            <option value="">Select</option>
            @if($websites)
               @php $oldWebsite = old('website_dept_slide') @endphp
              @foreach($websites as $val)
                <option {{ old('website_dept_slide') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
              @endforeach
            @endif
          </select>
          @error('website_dept_slide')
            <div class="form-control-feedback text-danger">Field is required please select it</div>
          @enderror
     </div>

     <div class="form-group m-r-2">
        <label class="form-control-label">Select Department (Displays on Department Page) <span class="text-danger">*</span></label>
        <select name="department_dpt_slide" id="department_dpt_slide" data-placeholder="Select" class="form-control select2">
          <option value="">Select</option>
        @if($departments)

           @php $oldDepart = old('department_dpt_slide') @endphp
          @foreach($departments as $val)
            <option {{ old('department_dpt_slide') == $val->department_id ? 'selected' : '' }} value="{{ $val->department_id }}">{{ $val->department_name }}</option>
          @endforeach
        @endif
        </select>
        @error('department_dpt_slide')
          <span class="form-control-feedback text-danger">Field is required please select it</span>
        @enderror
      </div>

     <div class="form-group m-r-2">
        <label class="form-control-label">Select Products</label>
        <select name="product_dpt_slide[]" id="product_dpt_slide" data-placeholder="Select" class="form-control select2 multiple" multiple disabled>
          <option value="">Select</option>
        </select>
        @error('product_dpt_slide')
          <div class="form-control-feedback text-danger">Field is required please select it</div>
        @enderror
      </div>
      <button class="btn btn-primary m-l-1 m-t-1" id="btn_create_dept_slide" type="submit"> Submit</button>
    </div>
    </form>
  </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-header-text">Lists</h5>
        </div>
      <div class="card-block">
    <table class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
        <thead>
           <tr>
              <th class="d-none">#</th>
              <th>Website</th>
              <th>Department</th>
              <th>Slider</th>
              <th>Status</th>
              <th>Action</th>
           </tr>
       </thead>
       <tbody>

      @foreach($websiteDeaprtmentSlider as $value)
        {{-- @if($value->slider_type == 'department') --}}
               <tr>
                 <td class="d-none">{{ $value->id }}</td>
                 <td>{{ $value->name }}</td>
                 <td id="department_name_deptslider{{ $value->id }}">{{ $value->department_slider_name }}</td>
                 <td>
          @foreach($websiteSliderList as $val)
           @if($val->website_id == $value->id )
             @if($val->slider_type == 'department')
               <!--<input type="hidden" value="{{-- route('updateSliderImage',$val->id) --}}" id="updateUrlslideImg{{-- $val->id --}}">-->
                  @php
                      $products = null;
                    if($slider_bin_products != null){
                        $products = $slider_bin_products->where('slider_id',$val->id)->pluck('product_id');
                    }
                  @endphp
                  @if(in_array(strtolower(pathinfo($val->slide,PATHINFO_EXTENSION)),['mp4','webm','ogg']))
                      <img src="{{ asset('storage/images/video-icon-image.png') }}" onclick="editDepartSlide({{ $val->id }},{{ $value->id }},{{ $value->department_slider }},'{{ addslashes($value->name) }}','{{ $val->mobile_slide }}',{{ $products }},'vd')" width="128" height="128" class="pointer"/>
                  @else
                      <img src="{{ asset('storage/images/website/sliders/'.session('company_id').'/'.$value->id.'/'.$val->slide) }}" alt=" {{ $val->slide }}" width="256" height="128" id="slide{{ $val->id }}" onclick="editDepartSlide({{ $val->id }},{{ $value->id }},{{ $value->department_slider }},'{{ addslashes($value->name) }}','{{ $val->mobile_slide }}',{{ $products }},'img')" class="pointer"/>
                  @endif
             @endif
            @endif
          @endforeach
         </td>
                 <td>{{($value->status == 1 ? "Active" : "In-Active")}}</td>
                 <td class="action-icon">

                   <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="warning({{ $value->id }},'{{ addslashes($value->name) }}','{{ $value->department_slider }}')" data-toggle="tooltip" data-placement="top" data-original-title="Delete"></i>

                   <form id="DestroyFormDepartSlide{{ $value->id }}" action="{{ route('destroySliderImage',[$value->id]) }}" method="post" class="d-none">
                       @csrf
                       @method('DELETE')
                       <input type="hidden" name="mode{{ $value->id }}" id="deptslide_mode{{ $value->id }}">
                       <input type="hidden" name="id" value="{{ $value->id }}">
                       <input type="hidden" name="depart" value="{{ $value->department_slider }}">
                   </form>
                 </td>
               </tr>
           {{-- @endif --}}
      @endforeach
        </tbody>
    </table>
 </div>
</div>



<div class="modal fade modal-flex" id="departmentslideEdit_Modal" tabindex="-1" role="dialog">
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

                <input type="hidden" id="webname_dpetslideEdMd" name="webName">
                <input type="hidden" id="webid_dpetslideEdMd" name="webId">
                <input type="hidden" id="departSlider_dpetslideEdMd" name="deaprtment_slider">
                <input type="hidden" id="id_dpetslideEdMd" name="id">

                <div class="form-group">
                      <img for="desktopslide_deptEdtmd" src="{{ asset('storage/images/no-image.png') }}" class="img-fluid" id="previewslide_deptEdtmd" width="250" height="128"/>
                      <video id="slideVd_deptEdtmd" width="250" height="128" controls style="display: none;"></video>
                </div>
                <div class="form-group">
                     <label for="desktopslide_deptEdtmd" class="custom-file">
                     <input type="file" name="desktop_slide" id="desktopslide_deptEdtmd" class="custom-file-input">
                     <span class="custom-file-control"></span>
                     </label>
                </div>

               <div class="form-group">
                    <img for="mobile_slide_deptEdtmd" src="{{ asset('storage/images/no-image.png') }}" class="img-fluid" id="previewMobileSlide_deptEdtmd" width="100" height="150"/>
                    <video id="previewMobileSlideVd_deptEdtmd" width="200" height="250" style="display:none;" controls></video></br>
                   </div>
              <div class="form-group">
                   <label for="mobile_slide_md" class="custom-file">
                   <input type="file" name="mobile_slide" id="mobile_slide_deptEdtmd" class="custom-file-input">
                   <span class="custom-file-control"></span>
                   </label>
             </div>


             <div class="alert alert-info">
               Be informed that the required image size not exceeding 1MB.
            </div>

            <div class="form-group m-r-2">
                <label class="form-control-label">Select Products</label>
                <select name="product_dpt_slide[]" id="product_dpt_slide_deptEdtmd" data-placeholder="Select" class="form-control select2 multiple" multiple disabled>
                  <option value="">Select</option>
                </select>
              </div>
             </form>
          </div>
          <div class="modal-footer">
              <button type="button" id="btn_remove_deptslidmd" class="btn btn-danger waves-effect waves-light f-left">Remove</button>
              <button type="button" data-dismiss="modal" class="btn btn-default waves-effect waves-light m-r-1">Close</button>
             <button type="button" id="btn_modify_deptslidmd" class="btn btn-success waves-effect waves-light">Save Changes</button>
          </div>
       </div>
    </div>
 </div>
