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
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="512" height="256" id="previewImg"/>
                <video id="videoPreview" width="512" height="256" style="display:none;" controls></video>
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
                <img src="{{ asset('storage/images/placeholder.jpg') }}" alt="placeholder.jpg" width="200" height="250" id="previewMobileSlide"/>
                <video id="videoMobilePreview" width="200" height="250" style="display:none;" controls></video></br>
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
          <label class="form-control-label">Website</label>
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
        <label class="form-control-label">Inventory Department</label>
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
        <label class="form-control-label">Select Inventory</label>
        <select name="product_dpt_slide" id="product_dpt_slide" data-placeholder="Select" class="form-control select2" disabled>
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
        {{ $websiteSlider }}
    <table class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
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

      @foreach($websiteSlider as $value)
        {{-- @if($value->slider_type == 'department') --}}
               <tr>
                 <td class="d-none">{{ $value->id }}</td>
                 <td>{{ $value->name }}</td>
                 <td>{{ $value->department_slider_name }}</td>
                 <td>
          @foreach($websiteSliderList as $val)
           @if($val->website_id == $value->id )
             @if($val->slider_type == 'department')
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
