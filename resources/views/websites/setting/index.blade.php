@extends('layouts.master-layout')

@section('title','Website Theme Setting')

@section('breadcrumtitle','Theme Setting Panel')


@section('content')

<section class="panels-wells p-t-3">

  @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

  @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

<div class="card">
  <div class="card-header">
    <h5 class="card-header-text">Theme Setting</h5>
  </div>    

  <div class="card-block">
   <div class="row">
                <div class="col-md-4">
                <div class="form-group">
                  <label class="form-control-label">Website</label>
                  <select name="website" id="website" data-placeholder="Select" class="form-control select2">
                    <option value="">Select</option>
                    @if($websiteLists)
                       @php $oldWebsite = old('website') ? old('website') : $webId; @endphp
                      @foreach($websiteLists as $val)
                        <option {{ $oldWebsite == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                      @endforeach
                    @endif
                  </select>
                    <div class="form-control-feedback text-danger" id="website_alert"></div>
                 </div>
               </div>
               </div>  
 </div>
</div> 
                    
                  @if($GetWebsite != null)
                  
            <div class="card card-block">      
                  <form name="themeSetting" method="post" action="{{ route('webSetSaveChanges') }}" enctype="multipart/form-data">
                      @csrf
<ul class="nav nav-tabs  tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Home<a>
    </li>  
    <li class="nav-item">
        <a class="nav-link " data-toggle="tab" href="#header" role="tab">Header<a>
    </li>    
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#contact" role="tab">Contact<a>
    </li> 
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#website-logo" role="tab">Logo<a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#product" role="tab">Product<a>
    </li>    
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#setting" role="tab">Setting</a>
    </li>
</ul>
 

 
<div class="tab-content tabs">
   
 <div class="tab-pane active" id="home" role="tabpanel">
    <div class="col-md-6 m-t-3">     
     <div class="form-group">
           <label for="page_title" class="form-control-label">Page Title</label>
           <div class="input-group">
              <input type="text" class="form-control" placeholder="Message" aria-describedby="btn-page_title" data-id="website" value="{{ stripslashes($GetWebsite->page_title) }}" name="page_title" id="page_title">
              <span class="input-group-btn" id="btn-page_title"><button type="button" onclick="btn_update('page_title')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
           </div>                                    
          <span id="page_title_alert"></span>         
     </div>    
     
     <div class="form-group">
           <label for="meta_title" class="form-control-label">Meta Title</label>
           <div class="input-group">
              <input type="text" class="form-control" placeholder="Message" aria-describedby="btn-meta_title" data-id="website" value="{{ $GetWebsite->meta_title }}" name="meta_title" id="meta_title">
              <span class="input-group-btn" id="btn-meta_title"><button type="button" onclick="btn_update('meta_title')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
           </div>                                   
          <span id="meta_title_alert"></span>        
     </div>

  <div class="form-group">
      <label for="meta_description" class="form-control-label">Meta Description</label>

        <textarea rows="5" class="form-control" name="meta_description" data-id="website" id="meta_description"></textarea>
        <button type="button" onclick="btn_update('meta_description')" class="btn btn-primary shadow-none waves-effect waves-light m-t-1">Update!</button> 
       <span id="meta_description_alert"></span>      
   </div>     
     
     <div class="form-group">
          <label for="depart_nav_layout" class="form-control-label">Department Navigation Bar Style</label>
          <select name="depart_nav_layout" id="depart_nav_layout" data-placeholder="Select" class="form-control select2" onchange="update('depart_nav_layout',$(this).val(),'theme')">
              @php 
                  $olddepart_nav_layout= old('depart_nav_layout') ? old('depart_nav_layout') : $GetWebsite->depart_nav_layout;
              @endphp
            <option value="">Select</option>
                <option {{ $olddepart_nav_layout == 1 ? 'selected' : '' }} value="1">UnderLine Navigate</option>
                <option {{ $olddepart_nav_layout == 2 ? 'selected' : '' }} value="2">Box Navigate</option>
          </select>
            <div class="form-control-feedback text-danger" id="depart_nav_layout_alert"></div>      
     </div>
     
     <div class="form-group">
          <label for="fonts" class="form-control-label">Fonts Style</label>
          <select name="fonts" id="fonts" data-placeholder="Select" class="form-control select2" onchange="update('fontstyle',$(this).val(),'theme')">
              @php 
                  $oldFonts = old('fonts') ? old('fonts') : $GetWebsite->fontstyle;
                  $fontStyleArray = ['Poppins','Roboto'];
              @endphp
            <option value="">Select</option>
            @foreach($fontStyleArray as $val)
                <option {{ $val == $oldFonts ? 'selected' : '' }} value="{{ $val }}">{{ $val }}</option>
            @endforeach
          </select>
            <div class="form-control-feedback text-danger" id="fonts_alert"></div>   
     </div>   
     
     <div class="form-group">
          <label for="cart_layout" class="form-control-label">Cart Style</label>
          <select name="cart_layout" id="cart_layout" data-placeholder="Select" class="form-control select2" onchange="update('cart_layout',$(this).val(),'theme')">
              @php 
                  $oldcart_layout = old('cart_layout') ? old('cart_layout') : $GetWebsite->cart_layout;
              @endphp
            <option value="">Select</option>
                <option {{ $oldcart_layout == '1' ? 'selected' : '' }} value="1">Fixed Cart</option>
                <option {{ $oldcart_layout == '2' ? 'selected' : '' }} value="2">Drawer Cart</option>
          </select>
            <div class="form-control-feedback text-danger" id="cart_layout_alert"></div>   
     </div> 
     
     <div class="form-group">
          <label for="footer_layout" class="form-control-label">Footer Layout</label>
          <select name="footer_layout" id="footer_layout" data-placeholder="Select" class="form-control select2" onchange="update('footer_layout',$(this).val(),'theme')">
              @php 
                  $oldfooter_layout = old('footer_layout') ? old('footer_layout') : $GetWebsite->footer_layout;
                  $footerLayoutArray = [1,2];
              @endphp
            <option value="">Select</option>
              @foreach($footerLayoutArray as  $val)
                <option {{ $oldfooter_layout == $val ? 'selected' : '' }} value="{{ $val }}">Layout {{ $val }}</option>
              @endforeach         
          </select>
            <div class="form-control-feedback text-danger" id="footer_layout_alert"></div> 
     </div>      
   </div>        

    </div>

 <div class="tab-pane" id="header" role="tabpanel">
   <div class="col-md-6 m-t-3">
    <div class="form-group">
        <label class="form-control-label">TopBar Header Message</label>
        <br/>
          <label>
              @php 
                  $topbar_mode = $GetWebsite->topbar;
              @endphp 
            <input type="checkbox" id="topbar_mode" onchange="changeCheckbox('topbar_mode',{{ $GetWebsite->id }},'website')"  value="{{ !empty($topbar_mode) ? 1 : 0 }}" {{ !empty($topbar_mode) ? 'checked' : '' }} data-toggle="toggle">
          </label>
       </div>  
    
    
    <div class="form-group {{ !empty($topbar_mode) ? '' : 'd-none' }}" id="topbarInput">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="TopBar Message" aria-describedby="btn-topbar" data-id="website" value="{{ $GetWebsite->topbar }}" id="topbar" name="topbar">
          <span class="input-group-btn" id="btn-topbar"><button type="button" onclick="btn_update('topbar')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
        </div>                                
      <span id="topbar_alert"></span>
    </div>


    <div class="form-group" id="topbarSlideMsgInput">
      <label class="form-controler-label">TopBar Slide Message</label>
        <div class="input-group">
          @php $topbarSlideValue = !empty($GetWebsite->topbar_slide_msg) ? json_decode($GetWebsite->topbar_slide_msg) : null; @endphp

          <input type="text" class="form-control" placeholder="TopBar Slide Message" aria-describedby="btn_topbar_slide_msg" data-id="website" value="" id="topbar_slide_msg" name="topbar_slide_msg">
          <span class="input-group-btn" id="btn_topbar_slide_msg"><button type="button" onclick="btn_update('topbar_slide_msg')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
        </div>                                
      <span id="topbar_slide_msg_alert"></span>
    </div>
   
</div>  
    <!-- <div class="col-md-6 m-t-3">     
     <div class="form-group">
         <label>JS Script</label>
         <textarea rows="5" class="form-control" name="js_script" data-id="theme" id="js_script"><//?= !empty($GetWebsite->js_script) ? str_replace('\n','',htmlspecialchars_decode($GetWebsite->js_script, ENT_QUOTES)) : null ?></textarea>
         <button type="button" onclick="btn_update('js_script')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light m-t-1">Update!</button> 
         <span id="script_value_alert"></span>         
         
     </div>     
    </div> -->
 </div>    
    
 <div class="tab-pane" id="contact" role="tabpanel">
    <div class="col-md-6 m-t-3">
     <div class="form-group">
         <label for="whatsapp" class="form-control-label">WhatsApp Number</label>
           <div class="input-group">
              <input type="text" class="form-control" placeholder="Message" aria-describedby="btn-whatsapp" data-id="website" value="{{ $GetWebsite->whatsapp }}" name="whatsapp" id="whatsapp">
              <span class="input-group-btn" id="btn-whatsapp"><button type="button" onclick="btn_update('whatsapp')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
           </div>
          <span id="whatsapp_alert"></span> 
     </div>     
     <div class="form-group">
         <label for="uan_number" class="form-control-label">UAN Number</label>
           <div class="input-group">
              <input type="text" class="form-control" placeholder="Message" aria-describedby="btn-uan_number" data-id="website" value="{{ $GetWebsite->uan_number }}" name="uan_number" id="uan_number">
              <span class="input-group-btn" id="btn-uan_number"><button type="button" onclick="btn_update('uan_number')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
           </div>                                   
          <span id="uan_number_alert"></span>
     </div>     
     <div class="form-group">
         <label for="address" class="form-control-label">Address</label>
         <textarea rows="5" class="form-control" name="address" data-id="website" id="address"></textarea>
         <button type="button" onclick="btn_update('address')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light m-t-1">Update!</button> 
                                  <span id="address_alert"></span>
     </div>
     </div>  
    </div>    
    <div class="tab-pane" id="product" role="tabpanel">
        
      <div class="col-md-6 m-t-3">
          <div class="form-group">
              <label for="product_view" class="form-control-label">Product navigat</label>
              <select name="product_view" id="product_view" data-placeholder="Select" class="form-control select2" onchange="update('product_view',$(this).val(),'theme')">
                  @php 
                      $oldproduct_view = old('product_view') ? old('product_view') : $GetWebsite->product_view;
                  @endphp
                <option value="">Select</option>
                    <option {{ $oldproduct_view == 'modal_view' ? 'selected' : '' }} value="modal_view">Modal View</option>
                    <option {{ $oldproduct_view == 'page_view' ? 'selected' : '' }} value="page_view">Page View</option>
              </select>
                <div class="form-control-feedback text-danger" id="product_view_alert"></div>              
          </div>
          <div class="form-group">
              <label for="product_list" class="form-control-label">Product List View</label>
              <select name="product_list" id="product_list" data-placeholder="Select" class="form-control select2" onchange="update('product_list',$(this).val(),'theme')">
                  @php 
                      $oldproduct_list= old('product_list') ? old('product_list') : $GetWebsite->product_list;
                  @endphp
                <option value="">Select</option>
                    <option {{ $oldproduct_list == 1 ? 'selected' : '' }} value="1">Landscape</option>
                    <option {{ $oldproduct_list == 2 ? 'selected' : '' }} value="2">Vertical</option>
              </select>
                <div class="form-control-feedback text-danger" id="product_list_alert"></div>              
          </div> 
          <div class="form-group">
              <label for="location_modal" class="form-control-label">Location Modal</label>
              <select name="location_modal" id="location_modal" data-placeholder="Select" class="form-control select2" onchange="update('location_modal',$(this).val(),'theme')">
                  @php 
                      $oldlocation_modal = old('location_modal') ? old('location_modal') : $GetWebsite->location_modal;
                  @endphp
                <option value="">Select</option>
                    <option {{ $oldlocation_modal == 0 ? 'selected' : '' }} value="0">After</option>
                    <option {{ $oldlocation_modal == 1 ? 'selected' : '' }} value="1">Start up</option>
              </select>
                <div class="form-control-feedback text-danger" id="location_modal_alert"></div>             
          </div> 
      </div>    
    </div>
    <div class="tab-pane" id="website-logo" role="tabpanel">
     
      <div class="col-md-6 m-t-3">
          <div class="form-group">
              <label for="logo" class="form-control-label">Light Logo</label> 
              @php
                $lightLogo = !empty($GetWebsite->logo) ? 'website/'.$GetWebsite->logo : 'placeholder.jpg';
              @endphp
             <img id="logoimages" src="{{ asset('storage/images/'.$lightLogo) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
            <br/>
            <label for="logo" class="custom-file">
                <input type="file" name="logo" id="logo" onchange="readURL(this,'logoimages')" class="custom-file-input">
                <span class="custom-file-control"></span>
            </label>               
          </div>
          
          <div class="form-group">
              <label for="dark_logo" class="form-control-label">Dark Logo</label> 
                  @php
                    $darkLogo = !empty($GetWebsite->dark_logo) ? 'website/'.$GetWebsite->dark_logo : 'placeholder.jpg';
                  @endphp
                 <img id="dark_logoimages" src="{{ asset('storage/images/'.$darkLogo) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                <br/>
                <label for="dark_logo" class="custom-file">
                    <input type="file" name="dark_logo" id="dark_logo" onchange="readURL(this,'dark_logoimages')" class="custom-file-input">
                    <span class="custom-file-control"></span>
                </label>              
          </div>          

          <div class="form-group">
              <label for="favicon" class="form-control-label">Favicon Logo</label> 
              @php
                $favicon = !empty($GetWebsite->favicon) ? 'website/'.$GetWebsite->favicon : 'placeholder.jpg';
              @endphp
             <img id="faviconimages" src="{{ asset('storage/images/'.$favicon) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
            <br/>
            <label for="favicon" class="custom-file">
                <input type="file" name="favicon" id="favicon" onchange="readURL(this,'faviconimages')" class="custom-file-input">
                <span class="custom-file-control"></span>
            </label>               
          </div>            
      </div>      
    </div>

    <div class="tab-pane" id="setting" role="tabpanel">
       <div class="col-md-6 m-t-3">
                              
                                <div class="form-group">
                                    <label class="form-control-label">Website Mode (On/Closing)</label>
                                     <br/>
                                      <label>
                                          @php 
                                             $is_open = $GetWebsite->is_open;
                                             $is_closing_msg = $GetWebsite->closing_msg;
                                          @endphp 
                                        <input id="is_open" onchange="changeCheckbox('is_open',{{ $GetWebsite->id }},'website')" type="checkbox" {{ $is_open == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                      
                                   <div class="input-group {{ $is_open == 1 ? 'd-none' : '' }}" id="closingInput">
                                      <input type="text" class="form-control" placeholder="Message" aria-describedby="btn-closing_msg" data-id="website" value="{{ !empty($closing_msg) ? $closing_msg : 'Sorry! We are closed right now' }}" id="closing_msg" name="closing_msg">
                                      <span class="input-group-btn" id="btn-closing_msg"><button type="button" onclick="btn_update('closing_msg')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
                                   </div>                                     
                                    
                                    <span id="closing_msg_alert"></span>                                    
                                    
                                </div>                      

                              
                                <div class="form-group">
                                    <label class="form-control-label">Maintenance Mode</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $maintenance_mode = isset($GetWebsite) ? $GetWebsite->maintenance_mode : 0;
                                          @endphp 
                                        <input id="maintenance_mode" onchange="changeCheckbox('maintenance_mode',{{ $GetWebsite->id }},'website')" type="checkbox" {{ $maintenance_mode == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                    
                                </div>   

                                <div class="form-group">
                                    <label class="form-control-label">Checkout OTP</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $checkout_otp = isset($GetWebsite) ? $GetWebsite->checkout_otp : 0;
                                          @endphp 
                                        <input id="checkout_otp" onchange="changeCheckbox('checkout_otp',{{ $GetWebsite->id }},'theme')" type="checkbox" {{ $checkout_otp == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                    
                                </div>

                                <div class="form-group">
                                    <label class="form-control-label">Otp Whatsapp Message</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $otp_whatsapp_msg = isset($GetWebsite) ? $GetWebsite->otp_whatsapp_msg : 0;
                                          @endphp 
                                        <input id="otp_whatsapp_msg" onchange="changeCheckbox('otp_whatsapp_msg',{{ $GetWebsite->id }},'theme')" type="checkbox" {{ $otp_whatsapp_msg == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Otp SMS</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $otp_msg = isset($GetWebsite) ? $GetWebsite->otp_msg : 0;
                                          @endphp 
                                        <input id="otp_msg" onchange="changeCheckbox('otp_msg',{{ $GetWebsite->id }},'theme')" type="checkbox" {{ $otp_msg == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Contact Show on Top</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $top_contact_box = isset($GetWebsite) ? $GetWebsite->top_contact_box : 0;
                                          @endphp 
                                        <input id="top_contact_box" onchange="changeCheckbox('top_contact_box',{{ $GetWebsite->id }},'theme')" type="checkbox" {{ $top_contact_box == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Back to Top Button</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $back_to_top_btn = isset($GetWebsite) ? $GetWebsite->back_to_top_btn : 0;
                                          @endphp 
                                        <input id="back_to_top_btn" onchange="changeCheckbox('back_to_top_btn',{{ $GetWebsite->id }},'theme')" type="checkbox" {{ $back_to_top_btn == 1 ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="form-control-label">Advertisement Notification</label>
                                    <br/>
                                      <label>
                                          @php 
                                             $advertisement_mode = $GetWebsite->advertisement;
                                          @endphp 
                                        <input type="checkbox" id="advertisement" onchange="changeCheckbox('advertisement',{{ $GetWebsite->id }},'theme')"  value="{{ !empty($advertisement_mode) ? 1 : 0 }}" {{ !empty($advertisement_mode) ? 'checked' : '' }} data-toggle="toggle">
                                      </label>
                                </div>
              
                                <div class="form-group">  
                                  <label class="form-control-label">Order Estimate Time</label>
                                   <div class="input-group">
                                      <input type="text" class="form-control" placeholder="Message" aria-describedby="btn-order_estimate_time" data-id="website" value="{{ $GetWebsite->order_estimate_time }}" name="order_estimate_time" id="order_estimate_time">
                                      <span class="input-group-btn" id="btn-order_estimate_time"><button type="button" onclick="btn_update('order_estimate_time')" class="btn btn-primary shadow-none addon-btn waves-effect waves-light">Update!</button></span>
                                   </div>                                   
                                  <span id="order_estimate_time_alert"></span>
                                 </div>                            
                    </div>      
    </div>

</div>                      
           
     </form>
    </div> 
    @endif


  <div class="modal fade modal-flex" id="branchModal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" onclick="branchModalClose({{ $webId }})" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Branch Lists</h4>
             </div>
             <div class="modal-body">
               <table class="table" id="md_tbl_branch">
                   <thead>
                       <tr>
                           <th>Branch Name</th>
                           <th>Action</th>
                       </tr>
                   </thead>
                   <tbody></tbody>
               </table>
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect waves-light" onclick="branchModalClose({{ $webId }})" data-dismiss="modal">Close</button>
             </div>
          </div>
           </div>
        </div> 
		
 
      
</section>

@endsection


@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>   
@endsection

@section('scriptcode_three')
<script>

  $(".select2").select2();

  $("#topbar_slide_msg").tagsinput({
     maxTags: 10
  });
  
  var is_openBranch = 0;
  
  $("#website").on('change',function(){
      if($(this).val() != ''){
          window.location = location.origin+'/website/theme-setting/'+$(this).val();
      }else{
          $(this).focus();
          $("#website_alert").text('Field is required.');
      }
      
  })
  
 @if(isset($GetWebsite) && $GetWebsite != null)
      $("#meta_description").val("{{ $GetWebsite->meta_description }}");
       $("#address").val("{{ $GetWebsite->address }}");
 @endif
 
{{-- @if(isset($GetWebsite) && $GetWebsite != null)--}}
      //$("#address").val("{{-- $GetWebsite->address --}}");
{{-- @endif --}}
 
//  $("#meta_description").on('change',function(){
//      update($(this).attr('name'),$(this).val(),'website');
//  })
  
 function changeCheckbox(elemId,webId,pmode) {

    var value = "";
    var process = true;
    
    if ($('#'+elemId).is(":checked")) {
         value = 1;
          if(elemId == 'topbar_mode'){
             if($("#topbarInput").hasClass('d-none')){
                 $("#topbarInput").removeClass('d-none');
             } 
             process = false;
          }else if(elemId == 'is_open'){
              if(!$("#closingInput").hasClass('d-none')){
                 $("#closingInput").addClass('d-none');
              }
             
              getBranches(webId);
              $("#branchModal").modal('show');
              process = false;
          }
      
    } else {
        value = 0;
          if(elemId == 'topbar_mode'){
             if(!$("#topbarInput").hasClass('d-none')){
                 $("#topbarInput").addClass('d-none'); 
             }
             value  = null;
             elemId = 'topbar';
          }else if(elemId == 'is_open'){
              if($("#closingInput").hasClass('d-none')){
                 $("#closingInput").removeClass('d-none');
              }               
          }  
    }
    
    if(process){
        $.ajax({
            url: "{{ route('webSetSaveChanges') }}",
            type: "POST",
            data: {
            _token: "{{csrf_token()}}",
            id: webId,
            mode:pmode,
            col:elemId,
            val:value,
          },
          dataType:'json',
          async:true,
          success: function(resp) {
              swal('Success!','', 'success');
            //console.log(resp)
          }
        });
    }
  }
  
  
//   $("#fonts").on('change',function(){
      
//     $.ajax({
//       url: "{{ route('webSetSaveChanges') }}",
//       type: "POST",
//       data: {
//         _token: "{{csrf_token()}}",
//         id: '{{ $webId }}',
//         mode:'theme',
//         col:'fontstyle',
//         val:$(this).val()
//       },
//       dataType:'json',
//       async:true,
//       success: function(resp) {
//         console.log(resp)
//       }
//     });      
//   })
  
  
  function update(elemId,value,wmd){
    $.ajax({
      url: "{{ route('webSetSaveChanges') }}",
      type: "POST",
      data: {
        _token: "{{csrf_token()}}",
        id: '{{ $webId }}',
        mode:wmd,
        col:elemId,
        val:value
      },
      dataType:'json',
      async:true,
      success: function(resp) {
          notify('Success!', 'success');
        console.log(resp)
      }
    });        
  }
  
  
// $('#whatsapp,#uan_number,#topbar,#meta_title,#page_title,#closing_msg,#order_estimate_time').keypress(function (e) {
//  var key = e.which;
//  if(key == 13)  // the enter key code
//   {
//       var elementId = $(this);
//     if($(this).val() != ''){
//         update(elementId.attr('name'),$(this).val(),'website');
//         if($("#"+elementId.attr('id')+"_alert").hasClass('text-danger')){
//             $("#"+elementId.attr('id')+"_alert").removeClass('text-danger').text('');
//         }
//     }else{
//         $("#"+elementId.attr('id')+"_alert").text('Field is required.').addClass('text-danger');
//     }
//   }
// });

// $('#whatsapp,#uan_number,#topbar,#meta_title,#page_title,#closing_msg,#order_estimate_time').on("focusout",function(){
  
//         var elementId = $(this);
//     if($(this).val() != ''){
//         update(elementId.attr('name'),$(this).val(),'website');
//         if($("#"+elementId.attr('id')+"_alert").hasClass('text-danger')){
//             $("#"+elementId.attr('id')+"_alert").removeClass('text-danger').text('');
//         }
//     }else{
//         $("#"+elementId.attr('id')+"_alert").text('Field is required.').addClass('text-danger');
//     }
// });

 function btn_update(elementId){
     
        var elementId = $("#"+elementId);
    if(elementId.val() != ''){
        //alert(elementId.attr('data-id'))
        update(elementId.attr('name'),elementId.val(),elementId.attr('data-id'));
        if($("#"+elementId.attr('id')+"_alert").hasClass('text-danger')){
            $("#"+elementId.attr('id')+"_alert").removeClass('text-danger').text('');
        }
    }else{
        $("#"+elementId.attr('id')+"_alert").text('Field is required.').addClass('text-danger');
    }     
 }

 function readURL(input, id) {
    if (input.files && input.files[0]) {
        let file = input.files[0];
        
        if (file.size > 1 * 1024 * 1024) {
            swal("Error!","File size must be less than 1MB.","error");
            return;
        }
        
        var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.webp)$/i;
        if (!allowedExtensions.exec(file.name)) {
            swal("Error!","Invalid file type. Please select a JPG, PNG, or GIF image.","error");
            return;
        }

        var reader = new FileReader();
        reader.onload = function(e) {
            $('#' + id).attr('src', e.target.result);
            updateImage(input, id.replace('images', '')); 
        }
        reader.readAsDataURL(file);
    }
}


//  function readURL(input,id) {
//     if (input.files && input.files[0]) {
//         var reader = new FileReader();
//         reader.onload = function(e) {
//           $('#'+id).attr('src', e.target.result);
//         }
//         updateImage(input,id.replace('images',''));
//         reader.readAsDataURL(input.files[0]);
//     }
//  }

 function updateImage(data,elem){
     var myFormData = new FormData();
         myFormData.append('value', data.files[0]);
         myFormData.append('col',$("#"+elem).attr('name'));
         myFormData.append('_token','{{ csrf_token() }}');
         myFormData.append('id','{{ $webId }}');
         myFormData.append('mode','website');
         
    $.ajax({
             url: "{{ route('webSetSaveChanges') }}",
             type: "POST",
             data: myFormData,
             contentType: false,
             cache: false,
             processData:false,
             success: function(data){
                 notify('Success!', 'success');
               //console.log(data);
             }         
    }); 
 } 
 
 
 
 function getBranches(id){
    $.ajax({
             url: "{{ route('getWebsiteBrancheSchedule') }}",
             type: "POST",
             data: {_token:'{{ csrf_token() }}',id:id},
             dataType:'json',
             async:true,
             success: function(resp){
              if(resp != ''){  
                  $("#md_tbl_branch tbody").empty();
                $.each(resp,function(i,v){
                    var isChecked = (v.is_open == 1 ? 'checked' : '') ;
                    var checkBox_input = '<label><input class="branchCheckBox" onclick="branch_isOpen('+v.id+','+id+',$(this))" type="checkbox" '+isChecked+' data-toggle="toggle"></label>';
                    
                    $("#md_tbl_branch tbody").append('<tr><td>'+v.branch_name+'</td><td>'+checkBox_input+'</td></tr>')
                })
              }
             }         
    });      
     
 }
 
 
 function branch_isOpen(id,webId,value){
     var mode = 0;
     if(value.is(':checked')){
         mode = 1;
     }else{
         mode = 0; 
     }
     
    $.ajax({
             url: "{{ route('websiteBranchesIsOpen') }}",
             type: "POST",
             data: {_token:'{{ csrf_token() }}',id:id,value:mode,website:webId},
             dataType:'json',
             async:true,
             success: function(resp){

                 swal('Success!','', 'success');
             }         
    });
 }
 
 function branchModalClose(id){
  if(id != ''){     
    $.ajax({
             url: "{{ route('websiteIsOpen') }}",
             type: "POST",
             data: {_token:'{{ csrf_token() }}',website:id},
             dataType:'json',
             async:true,
             success: function(resp){
                 if(resp == 0){
                     $("#is_open").trigger('click');
                 }
             }         
    }); 
  }
 }
 
//  $("#topbar_mode").on('click',function(){

//      if($(this).is(":checked")){
//          $(this).val(1);
//          if($("#topbar").hasClass('hidden')){
//              $("#topbar").removeClass('hidden');
//          }  
         
//      }else{
//          $(this).val(0);
//          if(!$("#topbar").hasClass('hidden')){
//              $("#topbar").addClass('hidden'); 
//          }
         
//          update('topbar','','website');
//      }
     
     
     
//  })


 </script>
@endsection