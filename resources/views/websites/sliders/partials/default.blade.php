<div class="card">
    <div class="card-header">
      <h5 class="card-header-text">Create Slider</h5>
    </div>
      <div class="card-block">
       <form id="sliderCreateForm " action="{{ route('sliderStore') }}" method="post" enctype="multipart/form-data">
        @csrf
    <div class="col-md-9">
       <div class="row">
          <div class="col-md-6">
            <div class="form-group @error('desktop_slide') 'has-danger' @enderror m-r-2">
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="512" height="256" id="previewImg"/>
                <video id="videoPreview" width="512" height="256" style="display:none;" controls></video>
              </br>

              <label for="desktop_slide" class="form-control-label">Desktop Slide</label></br>

              <label for="desktop_slide" class="custom-file">
              <input type="file" name="desktop_slide" id="desktop_slide" class="custom-file-input">
              <span class="custom-file-control"></span>
              </label>
              @error('desktop_slide')
                <div class="form-control-feedback text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group @error('mobile_slide') 'has-danger' @enderror m-r-2">
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="200" height="250" id="previewMobileSlide"/>
                <video id="videoMobilePreview" width="200" height="250" style="display:none;" controls></video></br>
              <label for="mobile_slide" class="form-control-label">Mobile Slide</label></br>

              <label for="mobile_slide" class="custom-file">
              <input type="file" name="mobile_slide" id="mobile_slide" class="custom-file-input">
              <span class="custom-file-control"></span>
              </label>
              @error('mobile_slide')
                <div class="form-control-feedback text-danger">{{ $message }}</div>
              @enderror
            </div>
          </div>
       </div>
       <div class="alert alert-info">
        Be informed that the required image size not exceeding 1MB.
       </div>
    </div>

        <div class="col-md-4">
        <div class="form-group m-r-2">
          <label class="form-control-label">Website</label>
          <select name="website" id="website" data-placeholder="Select" class="form-control select2">
            <option value="">Select</option>
            @if($websites)
               @php $oldWebsite = old('website') @endphp
              @foreach($websites as $val)
                <option {{ old('website') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
              @endforeach
            @endif
          </select>
          @error('website')
            <span class="form-control-feedback text-danger">Field is required please select it</span>
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
            <label class="form-control-label">Inventory Department</label>
            <select name="depart" id="depart" data-placeholder="Select" class="form-control select2">
              <option value="">Select</option>
            @if($departments)

               @php $oldDepart = old('depart') @endphp
              @foreach($departments as $val)
                <option {{ old('depart') == $val->department_id ? 'selected' : '' }} value="{{ $val->department_id }}">{{ $val->department_name }}</option>
              @endforeach
            @endif
            </select>
            @error('depart')
              <span class="form-control-feedback text-danger">Field is required please select it</span>
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

           <button class="btn btn-primary m-l-1 m-t-1" id="btn_create" type="submit"> Submit</button>
         </div>
        </form>
      </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Lists</h5>
            </div>
          <div class="card-block">

        <table class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
            <thead>
               <tr>
                  <th class="d-none">#</th>
                  <th>Website</th>
                  <th>Slider</th>
                  <th>Status</th>
                  <th>Action</th>
               </tr>
           </thead>
           <tbody>

          @foreach($websiteSlider as $value)
             {{-- @if($value->slider_type == 'default') --}}
                   <tr>
                     <td class="d-none">{{ $value->id }}</td>
                     <td>{{ $value->name }}</td>
                     <td>
              @foreach($websiteSliderList as $val)
                @if($val->slider_type == 'default')
                 @if($val->website_id == $value->id )
                   <!--<input type="hidden" value="{{-- route('updateSliderImage',$val->id) --}}" id="updateUrlslideImg{{-- $val->id --}}">-->
                      @if(in_array(strtolower(pathinfo($val->slide,PATHINFO_EXTENSION)),['mp4','webm','ogg']))
                          <img src="{{ asset('storage/images/video-icon-image.png') }}" onclick="editSlide({{ $val->id }},{{ $value->id }},'{{ addslashes($value->name) }}','{{ addslashes($val->invent_department_id) }}','{{ addslashes($val->prod_id) }}','{{ addslashes($val->prod_dept_id) }}','{{ addslashes($val->prod_subdept_id) }}','{{ $val->mobile_slide }}','vd')" width="128" height="128" class="pointer"/>
                      @else
                          <img src="{{ asset('storage/images/website/sliders/'.session('company_id').'/'.$value->id.'/'.$val->slide) }}" alt=" {{ $val->slide }}" width="256" height="128" id="slide{{ $val->id }}" onclick="editSlide({{ $val->id }},{{ $value->id }},'{{ addslashes($value->name) }}','{{ addslashes($val->invent_department_id) }}','{{ addslashes($val->prod_id) }}','{{ addslashes($val->prod_dept_id) }}','{{ addslashes($val->prod_subdept_id) }}','{{ $val->mobile_slide }}','img')" class="pointer"/>
                      @endif
                @endif
               @endif
              @endforeach
             </td>
                     <td>{{($value->status == 1 ? "Active" : "In-Active")}}</td>
                     <td class="action-icon">

                       <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="warning({{ $value->id }},'{{ addslashes($value->name) }}')" data-toggle="tooltip" data-placement="top" data-original-title="Delete"></i>

                       <form id="DestroyForm{{ $value->id }}" action="{{ route('destroySliderImage',[$value->id]) }}" method="post" class="d-none">
                           @csrf
                           @method('DELETE')
                           <input type="hidden" name="mode{{ $value->id }}" id="mode{{ $value->id }}">
                           <input type="hidden" name="id" value="{{ $value->id }}">
                       </form>
                     </td>
                   </tr>
              {{-- @endif --}}
          @endforeach
            </tbody>
        </table>
     </div>
   </div>

   <!-- Slide edit modal box -->
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
