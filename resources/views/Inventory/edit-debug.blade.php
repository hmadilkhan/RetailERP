@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','Add Inventory')

@section('navinventory','active')
@section('nav_addinventory','active')
@section('content')

@php $product_description = ''; @endphp
@if(!empty($data[0]->product_description))
   @php
         $product_description = htmlentities($data[0]->product_description);
         $product_description = html_entity_decode($product_description);
   @endphp
@endif

<div class="main-header m-t-0">
     <h4>Edit Inventory</h4>
     <br/>
     <a href="{{ route('invent-list') }}">
         <i class="text-primary text-center icofont icofont-arrow-left m-t-10 f-18"
         data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">
             Back to list</i>
     </a>
  </div>
<section class="panels-wells">
  <form id="inventoryupdate" method="post" class="form-horizontal" enctype="multipart/form-data" action="{{ route('update') }}">
      {{-- @method('POST') --}}
      @csrf

      <input type="hidden" id="id" name="id" value="{{$data[0]->id}}">
      <input type="hidden" id="previmage" name="previmage" value="{{$data[0]->image}}">
      <input type="hidden" id="reminder_id" name="reminder_id" value="{{$data[0]->reminder_id}}">
      <input type="hidden" id="oldGalleryImage" name="galleryImage">
      <input type="hidden" id="oldurlGalleryImage" name="urlGalleryImage">
      <input type="hidden" id="oldvideo" name="oldvideo">

      <div class="row">
          <div class="col-md-9">
              <div class="card b-radius">
                <div class="card-header">
                  <h4 class="">Product Detail</h4>
                  </div>
                <div class="card-block p-2 p-t-0">
                  <div class="row">
                  <div class="col-md-6">
            <div class="form-group {{ $errors->has('depart') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Department<span class="text-danger m-l-5">*</span></label>
                         <i id="btn_depart" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Department" ></i>
                    <select class="form-control  select2" data-placeholder="Select Department" id="depart" name="depart">
                       <option value="">Select Department</option>
                       @if($department)
                             @php $departmentValue = old('depart') ? old('depart') : $data[0]->department_id @endphp
                              @foreach($department as $val)
                                  <option {{ $departmentValue == $val->department_id ? 'selected' : ''}} value="{{$val->department_id}}">{{$val->department_name}}</option>
                              @endforeach
                       @endif
                    </select>
                   @if ($errors->has('depart'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
              </div>
              <div class="col-md-6">
              <div class="form-group {{ $errors->has('subDepart') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Sub Department<span class="text-danger m-l-5">*</span></label>
                  <i id="btn_subdepart" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Sub Department" ></i>
                    <select class="form-control  select2" data-placeholder="Select Sub Department" id="subDepart" name="subDepart">
                       <option value="">Select Sub Department</option>
                       @if($subdepartment)
                               @php $subDepartment = old('subDepart') ? old('subDepart') : $data[0]->sub_department_id @endphp
                               @foreach($subdepartment as $val)
                                  <option {{ $subDepartment == $val->sub_department_id ? 'selected' : null }} value="{{$val->sub_department_id}}">{{$val->sub_depart_name}}</option>
                               @endforeach
                           @endif
                    </select>
                    @if ($errors->has('subDepart'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
              </div>
                  </div>

                  <div class="row">
                <div class="col-lg-6 col-md-6">
                  <div  id="itemcode" class="form-group {{ $errors->has('code') ? 'has-danger' : '' }} ">
                      <label class="form-control-label"><i class="icofont icofont-barcode"></i>&nbsp;Product Code <span class="text-danger">*</span></label>
                      <i id="btngen" class="f-right text-primary" data-toggle="tooltip" data-placement="top" title="Auto Generate"> Auto Generate</i>
                      <input class="form-control" type="text"
                       name="code" id="code" value="{{ old('code') ? old('code') : $data[0]->item_code }}" placeholder="Enter Product Code" onchange="samecode()"/>
                        @if ($errors->has('code'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                  </div>
              </div>
             <div class="col-lg-6 col-md-6">
              <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Name <span class="text-danger">*</span></label>
                  <input class="form-control" type="text"
                   name="name" id="name"  value="{{ old('name') ? old('name') : $data[0]->product_name }}" onchange="samename()"  placeholder="Enter Product Name"/>
                    @if ($errors->has('name'))
                      <div class="form-control-feedback" id="nameerror">Required field can not be blank.</div>
                    @endif
                    <span class="text-danger" id="product_name_alert"></span>
              </div>
             </div>
     </div>

     <div class="row">
             <div class="col-md-3">
              <div class="form-group {{ $errors->has('uom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Unit Measure <span class="text-danger">*</span></label>
                   <i id="btn_uom" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add UOM" ></i>
                    <select class="form-control  select2" data-placeholder="Select Unit Measure" id="uom" name="uom">
                       <option value="">Unit Measure</option>
                       @if($uom)
                         @php $uomValue = old('uom') ? old('uom') : $data[0]->uom_id @endphp
                              @foreach($uom as $val)
                                  <option {{ $uomValue == $val->uom_id ? 'selected' : '' }} value="{{$val->uom_id}}">{{$val->name}}</option>
                              @endforeach
                       @endif
                    </select>
                    @if ($errors->has('uom'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
           </div>
     <div class="col-md-3">
         <div class="form-group {{ $errors->has('weight') ? 'has-danger' : '' }}">
             <label class="form-control-label">Weight | Quantity</label>
             <input class="form-control" type="text" onkeypress="return myfunction(event);"
                    name="weight" id="weight" value="{{ old('weight') ? old('weight') : $data[0]->weight_qty }}" placeholder=" Weight / Packet" />
             @if ($errors->has('weight'))
                 <div class="form-control-feedback">Required field can not be blank.</div>
             @endif
         </div>
     </div>

	     <div class="col-md-3">
              <div class="form-group {{ $errors->has('cuom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Conversion Unit Measure <span class="text-danger">*</span></label>
                   <i id="btn_uom" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add UOM" ></i>
                    <select class="form-control  select2" data-placeholder="Select Unit Measure" id="cuom" name="cuom">
                       <option value="">Conversion Unit Measure</option>
                       @if($uom)
                       @php $cuomValue = old('cuom') ? old('cuom') : $data[0]->cuom @endphp
                              @foreach($uom as $val)
                                  <option {{ $cuomValue == $val->uom_id ? 'selected' : ''}} value="{{$val->uom_id}}">{{$val->name}}</option>
                              @endforeach
                       @endif
                    </select>
                    @if ($errors->has('cuom'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
           </div>
          <div class="col-md-3">
              <div class="form-group {{ $errors->has('product_mode') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Mode <span class="text-danger">*</span></label>
                    <select class="form-control  select2" data-placeholder="Select Product Mode" id="product_mode" name="product_mode" onchange="toggle()">
                       <option value="">Select Product Mode</option>
                        @if($mode)
                        @php $productModeValue = old('product_mode') ? old('product_mode') : $data[0]->product_mode @endphp
                              @foreach($mode as $val)
                                  <option {{ $val->product_mode_id == $productModeValue ? 'selected' : '' }} selected="selected" value="{{$val->product_mode_id}}">{{$val->product_name}}</option>
                             @endforeach
                       @endif
                    </select>
                    @if ($errors->has('product_mode'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif

           </div>
         </div>
      </div>

      <div class="row">
        <div class="col-md-6">
              <div class="form-group">
                  <label class="form-control-label">Qty Reminder <span class="text-danger">*</span></label>
                  <input class="form-control" type="Number" min="0"
                         name="reminder" id="reminder" value="{{ old('reminder') ?  old('reminder') : $data[0]->reminder_qty }}" placeholder="Enter Quantity Reminder" />
                  @if($errors->has('reminder'))
                      <span class="form-control-feedback">Required field can not be blank.</span>
                  @endif
             </div>
		</div>


             <div class="col-md-6">
              <div class="form-group {{ $errors->has('description') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Description</label>

                  <textarea class="form-control"
                   name="description" id="description" rows="5" ></textarea>
                    @if($errors->has('description'))
                      <span class="form-control-feedback">Required field can not be blank.</span>
                    @endif
              </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label class="form-control-label">References</label>
                    <div class="tags_add">
                        <input class="form-control" id="reference" name="reference" type="text" value="{{ $references != '' ? $references : '' }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
    				<div class="form-group {{ $errors->has('brand') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Brand</label>
                      <i data-toggle="modal" data-target="#createbrand-modal" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Brand"></i>
                        <select class="form-control  select2" data-placeholder="Select Brand" id="brand" name="brand">
                           <option value="">Select</option>
                            @php $brandOld_val = old('brand') ? old('brand') : $data[0]->brand_id @endphp
                              @foreach($brandList as $val)
                                  <option {{ $brandOld_val == $val->id ? 'selected' : '' }} value="{{$val->id}}">{{$val->name}}</option>
                              @endforeach
                        </select>
                        @if ($errors->has('brand'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                  </div>
            </div>

            <div class="col-md-4">
				<div class="form-group {{ $errors->has('tags') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Tags</label>
                  <i data-toggle="modal" data-target="#createtag-modal" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Tag"></i>
                    <select class="select2" data-placeholder="Select Tags" id="tags" name="tags[]" multiple>
                       <option value="">Select</option>
                          @if($tagsList)
                            @php $tagsOld_val = old('tags') ? (array) old('tags') : $inventoryTags->toArray(); @endphp
                              @foreach($tagsList as $val)
                                  <option {{ (in_array($val->id,$tagsOld_val)) ? 'selected' : '' }} value="{{$val->id}}">{{$val->name}}</option>
                              @endforeach
                          @endif
                    </select>
                    @if($errors->has('tags'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
			</div>
        </div>

        @if(count($websites) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="showProductWebsite" class="pointer">
                        <input type="checkbox" id="showProductWebsite" name="showProductWebsite">
                        Show Product on Website
                    </label>
                </div>
            </div>
        </div>
      <div class="{{ count($selectedWebsites->toArray()) > 0 ? '' : 'd-none' }}" id="website-module">
        <div class="row">
            <div class="col-md-5">
				<div class="form-group {{ $errors->has('website') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Select Website (Select on where product to show)</label>
                    <select class="form-control  select2" data-placeholder="Select Website" id="website" name="website">
                       <option value="">Websites</option>
                       @if($websites)
                          @foreach($websites as $website)
							  <option {{(in_array($website->id, $selectedWebsites->toArray()) ? 'selected="selected"' : '' )}} value="{{$website->id}}">{{$website->name}}</option>
						  @endforeach
                       @endif
                    </select>
                    @if($errors->has('website'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
            </div>
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('priority') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Priority</label>
                    <input type="number" class="form-control" name="priority" min="0" value="{{ old('priority') ? old('priority') : $data[0]->priority }}">
              </div>
			</div>


        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="form-group ">
                    <label class="form-control-label">Short Description <i>(For Website Only)</i></label>
                    <textarea class="form-control" name="sdescription" id="summary-ckeditor" rows="3" >{{ old('sdescription') }}</textarea>
                    @if($errors->has('sdescription'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="form-group ">
                    <label class="form-control-label">Details <i>(For Website Only)</i></label>
                    <textarea class="form-control" name="details" id="details" rows="6"></textarea>
                    @if($errors->has('details'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
          </div>
         </div>
       @endif
   </div>


              </div> <!-- card div close -->
              <div class="card">
                 <div class="card-header">
                     <h4>Product Price</h4>
                </div>
                  <div class="card-block p-2">
        <div class="row">
			<div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('cost_price') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Cost Price:<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)"  min="0" name="cost_price" id="cost_price" value="{{ old('cost_price') ? old('cost_price') : $prices[0]->cost_price }}"  placeholder="0"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('ap') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Actual Price:<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="ap" id="ap" value="{{ old('ap') ? old('ap') : $prices[0]->actual_price}}" placeholder="0"/>
                    @if ($errors->has('ap'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Tax Rate:</label>
                    <input class="form-control" type="Number"  step=".01" name="taxrate" id="taxrate" value="{{ old('taxrate') ? old('taxrate') : $prices[0]->tax_rate }}"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Tax Amount:</label>
                    <input class="form-control" type="Number" min="0" name="taxamount" id="taxamount" value="{{ old('taxamount') ? old('taxamount') : $prices[0]->tax_amount }}"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('rp') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Retail Price<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="rp" id="rp" placeholder="0" value="{{ old('rp') ? old('rp') : $prices[0]->retail_price }}" />
                    @if($errors->has('rp'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Wholesale Price:</label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="wp" id="wp" value="{{ old('wp') ? old('wp') : $prices[0]->wholesale_price }}"/>
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Discount Price:</label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="dp" id="dp" value="{{ old('dp') ? old('dp') : $prices[0]->discount_price }}"/>
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Online Price:</label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="op" id="op" value="{{ old('op') ? old('op') : $prices[0]->online_price }}"/>
                </div>
            </div>
          </div>    <!--card block-->
        </div> <!-- card div close -->

       </div>
     </div>

         <div class="col-md-3">
         <div class="card">
                  <div class="card-header">
                  <h4 for="image">Product Image</h4>
                  </div>
                  <div class="card-block p-1 p-t-0">
                    <div class="form-group">
                        @php $imageUrl = asset('storage/images/placeholder.jpg') @endphp
                        {{-- @if(in_array(session('company_id'), [95, 102, 104]) || in_array(Auth::user()->username,['demoadmin','fnkhan']))
                               @if(!empty($data[0]->url)) --}}
                                  {{-- @php $imageUrl = $data[0]->url @endphp --}}
                                  {{-- @php $imageUrl = route('imageOptimize',$data[0]->image) @endphp
                               @else --}}
                                @if(!empty($data[0]->image) && Storage::disk('public')->exists('images/products/' . $data[0]->image))
                                    {{-- @php $imageUrl = asset('storage/images/products/'.$data[0]->image) @endphp --}}
                                    {{-- @php $imageUrl = route('imageOptimize',$data[0]->image) @endphp --}}
                                    @php $imageUrl = asset('storage/images/products/' . $data[0]->image) @endphp
                                @endif
                               {{-- @endif
                        @else --}}
                        {{-- Storage::disk('public')->exists('images/products/' . $data[0]->image) --}}
                               {{-- @if(File::exists('storage/images/products/'.$data[0]->image))
                                  @php $imageUrl = asset('storage/images/products/'.$data[0]->image) @endphp
                               @endif --}}
                        {{-- @endif --}}
                            <a href="{{ $imageUrl }}" data-toggle="lightbox" data-title="{{$data[0]->product_name}}">
                                <img id="simg" src="{{ $imageUrl }}" class="max-height-100 width-100 m-b-15" alt="{{ empty($data[0]->image) ? $data[0]->image : 'placeholder.jpg' }}">
                                </a>
                                <br/>
                                <label for="image" class="custom-file">
                                            <input type="file" name="image" id="image" class="custom-file-input">
                                            <span class="custom-file-control"></span>
                                        </label>
                                <div>
                                    <label class="pointer"><input type="checkbox" name="actual_image_size" class="" {{ $data[0]->actual_image_size == 1 ? 'checked' : ''}}> You want to actual image size</label>
                                </div>
                            @if($errors->has('image'))
                                <span class="form-control-feedback">{{ $errors->first('image') }}</span>
                            @endif
                        </div>
                </div>
            </div>

            {{-- @if(in_array(Auth::user()->username,['demoadmin','urs.sb.gs'])) --}}

        <div class="d-none" id="prodAdvans_Media">
        <div class="card">
               <div class="card-header">
               <h4 >Product Gallery</h4>
               </div>
               <div class="card-block p-2 p-t-0">
                {{-- {{ $images }} --}}
                @if($images)
                <div class="">
                    {{-- @if(in_array(session('company_id'),[])) --}}
                      @foreach($images as $val)
                        @if(empty($val->url))
                        <div style="position: relative; display: inline-block; margin: 10px;" id="gallery-{{$val->id}}">
                            <img src="{{ asset('storage/images/products/'.$val->image) }}" style="max-width: 75px; max-height: 75px; object-fit: cover;">
                            <button type="button" onclick="removeImage({{ $val->id }},'{{ $val->image }}')" style="position: absolute; top: -6px; right: -6px; background-color: red; color:
                            white; border: none; border-radius: 50%; cursor: pointer; font-size: 12px;">
                                ✖</button>
                        </div>
                        @else
                        <div id="urlgallery-{{$val->id}}" style="position: relative; display: inline-block; margin: 10px;">
                            <img src="{{ $val->url }}" style="max-width: 75px; max-height: 75px; object-fit: cover;">
                            <button type="button" onclick="removeImageUrl({{ $val->id }},'{{ $val->image }}')" style="position: absolute; top: -6px; right: -6px; background-color: red; color:
                            white; border: none; border-radius: 50%; cursor: pointer; font-size: 12px;">
                                ✖</button>
                        </div>
                        @endif
                      @endforeach
                    {{-- @endif --}}
                </div>
                @endif

               <div id="imgGalleryBox"></div>
                <div class="form-group">
                    <br/>
                     <label for="prodgallery" class="custom-file">
                         <input type="file" name="prodgallery[]" id="prodgallery" onchange="readURL_multiple(this,'imgGalleryBox')" class="custom-file-input" multiple>
                         <span class="custom-file-control"></span>
                     </label>
                </div>
                </div>
                </div>
                <div class="card">
               <div class="card-header">
               <h4 >Product Video</h4>
               </div>
               <div class="card-block p-2 p-t-0">
               <div id="videoPreviewBox">
                 @if($inventoryVideo != null)
                   @php $videoExtension = strtolower(pathinfo($inventoryVideo->file,PATHINFO_EXTENSION)); @endphp
                 <video controls width="300" height="300">
                    <source src="{{ asset('storage/video/products/'.$inventoryVideo->file) }}" type="video/{{ $videoExtension }}">
                </video>
                <button type="button" onclick="removeOldVideo('{{ $inventoryVideo->file }}')" class="btn btn-danger">Remove</button>
                 @endif
               </div>
                <div class="form-group">
                    <br/>
                     <label for="productvideo" class="custom-file">
                         <input type="file" name="prodvideo" id="productvideo" onchange="handleVideo(this,'videoPreviewBox')" class="custom-file-input">
                         <span class="custom-file-control"></span>
                     </label>
                </div>
                </div>
                </div>

        </div>
     {{-- @endif                            --}}
         </div>

         </div>
         <div class="row m-t-30">
              <div class="col-lg-12 col-md-12 text-center">
                  <button class="btn btn-lg btn-circle btn-primary m-b-10" type="submit" id="btn_submit_save_changes">Save Changes</button>
             </div>
           </div>
</form>

      </div>




                  </div>
               </div>

            </section>

            <div class="modal fade modal-flex" id="createtag-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-md" role="document">
                   <div class="modal-content">
                      <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                     <span aria-hidden="true">&times;</span>
                                 </button>
                         <h4 class="modal-title">Add Tag</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                              <div class="col-md-12">
                               <div class="form-group">
                                 <label class="form-control-label">Tag:</label>
                                  <input type="text" name="tag_md" id="tag_md" class="form-control" />
                                 </div>
                               </div>
                           </div>
                      </div>
                      <div class="modal-footer">
                         <button type="button" id="btn_tag_md" class="btn btn-success waves-effect waves-light" onClick="insertProduct_attribute('tag')">Add</button>
                      </div>
                   </div>
                </div>
             </div>


          <div class="modal fade modal-flex" id="uom-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-md" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                          <h4 class="modal-title">Add Unit of Measure</h4>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group">
                                      <label class="form-control-label">Unit of Measure:</label>
                                      <input type="text"  name="txtuom" id="txtuom" class="form-control" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" id="btn_bank" class="btn btn-success waves-effect waves-light" onClick="adduom()">Add</button>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-md" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                          <h4 class="modal-title">Add Department</h4>
                      </div>
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group">
                                      <label class="form-control-label">Department Name:</label>
                                      <input type="text"  name="departname" id="departname" class="form-control" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-success waves-effect waves-light" onClick="adddepartment()">Add Department</button>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal fade modal-flex" id="subdepart-modal" tabindex="-1" role="dialog">
              <div class="modal-dialog modal-md" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                          <h4 class="modal-title">Add Sub Department</h4>
                      </div>
                      <div class="modal-body">

                          <div class="row">
                              <div class="col-md-12">
                                  <div class="form-group">
                                      <select class="form-control  select2" data-placeholder="Select Department" id="departmodal" name="departmodal">
                                          <option value="">Select Department</option>
                                          @if($department)
                                              @foreach($department as $val)
                                                  @if( old('depart') == $val->department_id)
                                                      <option selected="selected" value="{{$val->department_id}}">{{$val->department_name}}</option>
                                                  @else
                                                      <option value="{{$val->department_id}}">{{$val->department_name}}</option>
                                                  @endif
                                              @endforeach
                                          @endif

                                      </select>
                                      {{--                      <select class="form-control  select2" data-placeholder="Select Department" id="departmodal" name="departmodal">--}}
                                      {{--                       <option value="">Select Department</option>--}}
                                      {{--                  </select> --}}
                                  </div>
                              </div>
                              <div class="col-md-12">
                                  <div class="form-group">
                                      <label class="form-control-label">Sub Department Name:</label>
                                      <input type="text"  name="subdepartname" id="subdepartname" class="form-control" />
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-success waves-effect waves-light" onClick="addsubdepart()">Add Sub Department</button>
                      </div>
                  </div>
              </div>
          </div>
@endsection

@section('css_code')
    <style>
        .video-remove-button {
            position: absolute;
            top: 65px;
            right: 35px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 30%;
            cursor: pointer;
            font-size: 14px;
            padding: 6px;
        }
        @media screen and (max-width: 32em) {
            .video-remove-button {
                    top: 45px;
                    right: 75px;
            }
        }

    </style>
@endsection

@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection
@section('scriptcode_three')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/md5-js-tools@1.0.2/lib/md5.min.js"></script>
	<script type="text/javascript">

       @if(old('description'))
          $("#description").val("{{ e(old('description')) }}")
       @else
          $("#description").val('{{ e($product_description) }}');
       @endif

       $(".select2").select2();

//light box
$(document).on('click', '[data-toggle="lightbox"]', function(event) {
 event.preventDefault();
 $(this).ekkoLightbox();
});

$(document).ready(function(){
    @if(count($selectedWebsites->toArray()) > 0 )
    $("#showProductWebsite").trigger('click');
    $("#showProductWebsite").attr('checked',true);
    @endif
});

function removeImage(id, img) {
    $("#gallery-" + id).remove();
    let input = $('#oldGalleryImage');
    let currentValue = input.val() === '' ? [] : input.val().split(', '); // 'split' ka sahi spelling
    let newValue = img;

    if (currentValue.length) {
        currentValue.push(newValue);
    } else {
        currentValue = [newValue];
    }

    input.val(currentValue.join(', '));
}

function removeImageUrl(id,img){
   $("#urlgallery-"+id).remove();
   let input = $('#oldurlGalleryImage');
    let currentValue = input.val() === '' ? [] : input.val().split(', '); // 'split' ka sahi spelling
    let newValue = img;

    if (currentValue.length) {
        currentValue.push(newValue);
    } else {
        currentValue = [newValue];
    }

    input.val(currentValue.join(', '));
}

$("#showProductWebsite").on('click',function(){

    if($(this).is(':checked')==true){
        if($("#website-module").hasClass('d-none')){
            $("#website-module").removeClass('d-none');
        }


        if($("#prodAdvans_Media").hasClass('d-none')){
            $("#prodAdvans_Media").removeClass('d-none');
        }
    }

    if($(this).is(':checked')==false){
        if(!$("#website-module").hasClass('d-none')){
            $("#website-module").addClass('d-none');
        }

        if(!$("#prodAdvans_Media").hasClass('d-none')){
            $("#prodAdvans_Media").addClass('d-none');
        }
    }
});


    $("#website").on('change',function(){
      if($(this).val() != ''){
        if($("#prodAdvans_Media").hasClass('d-none')){
            $("#prodAdvans_Media").removeClass('d-none')
        }
      }else{
        if(!$("#prodAdvans_Media").hasClass('d-none')){
            $("#prodAdvans_Media").addClass('d-none')
        }
      }
    });


      CKEDITOR.replace( 'summary-ckeditor' );
	  var rem_id = [];

      $("#name").on('change',function(){
         //let regex = /[^a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F()]/g;
         //let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)]+$/;
         let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;
          if(!regex.test($("#name").val())){
            swal('Error!','This field is required. Please note, special characters (such as @, #, $, %, &, ) are not allowed.!','error');
             $("#product_name_alert").text('This field is required. Please note, special characters (such as @, #, $, %, &, ) are not allowed.!');
             if(!$(this).hasClass('input-danger')){
                $(this).addClass('input-danger')
             }

             if($(this).hasClass('input-success')){
                $(this).removeClass('input-success')
             }

          }else{
            $("#product_name_alert").text('');
            if($(this).hasClass('input-danger')){
                $(this).removeClass('input-danger')
             }

             if(!$(this).hasClass('input-success')){
                $(this).addClass('input-success')
             }
          }
      });

	  $( '#inventoryupdate').submit( function(e){
		  e.preventDefault();
		  let form = $(this);
		  let actionUrl = form.attr('action');
          let process = true;
          let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;
        //let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.\+\/]+$/;

          if(!regex.test($("#name").val())){
            process = false;
            swal('Error!','Special characters are not allowed!','error');
          }

         if(process){
		  $.ajax({
				type: "POST",
				url: actionUrl,
				// data: form.serialize(), // serializes the form's elements.
				data:new FormData(this),
			    dataType:'JSON',
			    contentType: false,
			    cache: false,
			    processData: false,
                beforeSend:function(){
                  $("#btn_submit_save_changes").attr('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
                },
				success: function(data,statusText,getStatus)
				{
				  if(getStatus.status == 200){
					  location.reload();
				  }
				}
			});

          }
		});

		function sunmiCloud(){
			rem_id.push($("#id").val());
			// console.log(rem_id)
			$.ajax({
				url: "{{url('/sunmi-cloud')}}",
				type: "POST",
				data: {_token:"{{csrf_token()}}",
					inventory:rem_id,
				},
				success:function(resp){
					sendToSunmi(resp)
				}
			});
		}
		const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

		function generateString(length) {
			let result = '';
			const charactersLength = characters.length;
			for ( let i = 0; i < length; i++ ) {
				result += characters.charAt(Math.floor(Math.random() * charactersLength));
			}
			return result;
		}

		function getCurrentUnixTimestamp() {
			return Math.floor(Date.now() / 1000);
		}

		function sendToSunmi(productList){
			let random = generateString(7);
			let timestamp = getCurrentUnixTimestamp();
			let string = 'app_id=KV1LI73MXVBAQ&product_list='+productList+'&random='+random+'&shop_id=1&timestamp='+timestamp+'&key=0XsVp45yO0vJlEbWsPPQ';
			var hash = MD5.generate(string);
			let sign = hash.toUpperCase();


			$.ajax({
			  url: "https://store.sunmi.com/openapi/product/update",
			  method : "POST",
			  data:{shop_id : 1,product_list:productList,app_id:'KV1LI73MXVBAQ',random:random,timestamp:timestamp,sign:sign},
			  cache: false,
			  success: function(response){
				console.log(response)
				if(response.msg == "succeed"){
					location.reload()
				}else{
					swal({
						title: "Error!",
						text: "Price changed failed on Sunmi Platform",
						type: "error"
					});
					location.reload()
				}
			  }
			});
		}
      function deleteImage(id,image){
          swal({
                  title: "Are you sure?",
                  text: "Do you really want to delete this image?",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "delete it!",
                  cancelButtonText: "cancel plx!",
                  closeOnConfirm: false,
                  closeOnCancel: false
              },
              function(isConfirm){
                  if(isConfirm){
                      $.ajax({
                          url:'{{ url("/delete-image")}}',
                          data:{_token : "{{csrf_token()}}",id:id,image:image},
                          type:"POST",
                          success:function(result){
                              swal("Success", "Your ٰimage is deleted :)", "success");
                              location.reload();
                          },error:function(err,res){
                              swal("Error", "Cannot delete image :)", "error")
                              location.reload();
                          }
                      });


                  }else {
                      swal("Cancelled", "Your ٰimage is safe :)", "error");
                  }
              });
      }

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



     $("#image").change(function() {
         readURL(this,'simg');
        //  imagesPreview(this, 'div.gallery');
     });

     var imagesPreview = function(input, placeToInsertImagePreview) {

         if (input.files) {
             var filesAmount = input.files.length;


             for (i = 0; i < filesAmount; i++) {
                 var reader = new FileReader();

                 reader.onload = function(event) {
                     $($.parseHTML('<img width=200 height=200 style="margin-left:20px;margin-top:20px;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                 }

                 reader.readAsDataURL(input.files[i]);
             }
         }

     };

     function readURL(input, id) {
    if (input.files && input.files[0]) {
        var file = input.files[0];

        // Validate file size (5MB = 5 * 1024 * 1024 bytes)
        if (file.size > 5 * 1024 * 1024) {
            swal("Error!","File size must be less than 5MB.","error");
            input.value = ""; // Clear the input
            return;
        }

        // Validate file type
        const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        if (!SUPPORTED_EXTENSIONS.includes(fileExtension)) {
            swal("Error!","Only JPG, JPEG, PNG, and WEBP files are allowed.","error");
            input.value = ""; // Clear the input
            return;
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            $('#' + id).attr('src', e.target.result);
        }

        reader.readAsDataURL(file);
    }
}
    /* function readURL(input,id) {
         if (input.files && input.files[0]) {
             var reader = new FileReader();

             reader.onload = function(e) {
                 $('#'+id).attr('src', e.target.result);
             }

             reader.readAsDataURL(input.files[0]);
         }
     }*/

    $("#website").on('change',function(){
      if($(this).val() != ''){
        if($("#prodAdvans_Media").hasClass('d-none')){
            $("#prodAdvans_Media").removeClass('d-none')
        }
      }else{
        if(!$("#prodAdvans_Media").hasClass('d-none')){
            $("#prodAdvans_Media").addClass('d-none')
        }
      }
    });

    subDepart();
    function subDepart(){
      var depart = '{{$data[0]->sub_department_id}}';
       $.ajax({
        url:'{{ url("/getSubdepartBydepartID") }}',
        data:{_token : "{{csrf_token()}}",id:$('#depart').val()},
        type:"POST",
        success:function(result){
          $('#subDepart').empty();
          $('#subDepart').append($('<option>').text('Select City').attr('value', ''));
             $.each(result, function (i, value) {
              if(value.sub_department_id == depart){
                $('#subDepart').append($('<option selected>').text(value.sub_depart_name).attr('value', value.sub_department_id));
              }else{
                $('#subDepart').append($('<option>').text(value.sub_depart_name).attr('value', value.sub_department_id));
              }

            });
          }
        });
    }
    $('#depart').change(function(){
        $.ajax({
        url:'{{ url("/getSubdepartBydepartID") }}',
        data:{_token : "{{csrf_token()}}",id:$('#depart').val()},
        type:"POST",
        success:function(result){
          $('#subDepart').empty();
          $('#subDepart').append($('<option>').text('Select City').attr('value', ''));
             $.each(result, function (i, value) {
              $('#subDepart').append($('<option>').text(value.sub_depart_name).attr('value', value.sub_department_id));
            });
          }
        });

     });

      $("#ap").change(function(){
          if($('#taxrate').val() != ""){
              let taxamount = $('#ap').val() * ($('#taxrate').val() / 100);
              let retailPrice = parseInt($('#ap').val()) + parseInt(taxamount);

              $('#taxamount').val(Math.round(taxamount));
              $("#rp").val(Math.round(retailPrice));
          }
      });

      $("#taxrate").change(function(){
          var taxamount = $('#ap').val() * ($('#taxrate').val() / 100);
          var retailPrice = parseInt($('#ap').val()) + parseInt(taxamount);

          $('#taxamount').val(Math.round(taxamount));
          $("#rp").val(Math.round(retailPrice));
      });

      $("#taxamount").change(function(){
          var taxrate = (($('#taxamount').val()  / $('#ap').val() ) *  100);
          var retailPrice = parseInt($('#ap').val()) + parseInt($('#taxamount').val());

          $('#taxrate').val(taxrate);
          $("#rp").val(Math.round(retailPrice));
      });

     $("#btn_uom").on('click',function(){
         $('#txtuom').val('');
         $("#uom-modal").modal("show");
     });

     $("#btn_depart").on('click',function(){
         $('#departname').val('');
         $("#depart-modal").modal("show");
     });

     $("#btn_subdepart").on('click',function(){
         $('#departmodal').val($('#depart').val()).change();
         $('#departname').val('');
         $("#subdepart-modal").modal("show");
     });

     function adduom(){
         if ($('#txtuom').val() == "") {
             swal({
                 title: "Error Message",
                 text: "Required Field can not be blank!",
                 type: "warning"
             });

         }
         else
         {
             $.ajax({
                 url: "{{url('/adduom')}}",
                 type: 'POST',
                 dataType:"json",
                 data:{_token:"{{ csrf_token() }}",
                     uom:$('#txtuom').val(),
                 },

                 success:function(resp){
                     if(resp != 0){
                         swal({
                             title: "Operation Performed",
                             text: "Unit of Measure Added Successfully!",
                             type: "success"
                         });
                         $("#uom-modal").modal("hide");
                         $("#uom").empty();
                         for(var count=0; count < resp.length; count++){
                             $("#uom").append("<option value=''>Select Unit of Measure</option>");
                             $("#uom").append(
                                 "<option value='"+resp[count].uom_id+"'>"+resp[count].name+"</option>");
                         }
                     }
                     else
                     {
                         swal({
                             title: "Already exsist",
                             text: "Particular UOM Already exsist!",
                             type: "warning"
                         });
                         $("#uom-modal").modal("hide");

                     }
                 }

             });
         }

     }

     function adddepartment(){
         if ($('#departname').val() == "") {
             swal({
                 title: "Error Message",
                 text: "Required Field can not be blank!",
                 type: "warning"
             });

         }
         else
         {
             $.ajax({
                 url: "{{url('/adddepartment')}}",
                 type: 'POST',
                 dataType:"json",
                 data:{_token:"{{ csrf_token() }}",
                     departname:$('#departname').val(),
                 },

                 success:function(resp){

                     if(resp != 0){
                         swal({
                             title: "Operation Performed",
                             text: "Department Added Successfully!",
                             type: "success"
                         });
                         $("#depart-modal").modal("hide");
                         $("#depart").empty();
                         $("#departmodal").empty();

                         $("#depart").append("<option value=''>Select Department</option>");
                         $("#departmodal").append("<option value=''>Select Department</option>");
                         for(var count=0; count < resp.length; count++){
                             $("#depart").append("<option value='"+resp[count].department_id+"'>"+resp[count].department_name+"</option>");
                             $("#departmodal").append("<option value='"+resp[count].department_id+"'>"+resp[count].department_name+"</option>");
                         }
                     }
                     else
                     {
                         swal({
                             title: "Already exsist",
                             text: "Particular Department Already exsist!",
                             type: "warning"
                         });
                         $("#depart-modal").modal("hide");

                     }
                 }

             });
         }

     }

     function addsubdepart(){
         if ($('#subdepartname').val() == "") {
             swal({
                 title: "Error Message",
                 text: "Required Field can not be blank!",
                 type: "warning"
             });

         }
         else if($('#departmodal').val() == "" )
         {
             swal({
                 title: "Error Message",
                 text: "Required Field can not be blank!",
                 type: "warning"
             });
         }
         else
         {
             $.ajax({
                 url: "{{url('/addsubdepart')}}",
                 type: 'POST',
                 dataType:"json",
                 data:{_token:"{{ csrf_token() }}",
                     departid:$('#departmodal').val(),
                     subdepart:$('#subdepartname').val(),
                 },
                 success:function(resp){
                     if(resp != 0){
                         swal({
                             title: "Operation Performed",
                             text: "Sub Department Added Successfully!",
                             type: "success"
                         });
                         $("#subdepart-modal").modal("hide");
                         $("#subDepart").empty();
                         for(var count=0; count < resp.length; count++){
                             $("#subDepart").append("<option value=''>Select Sub Department</option>");
                             $("#subDepart").append(
                                 "<option value='"+resp[count].sub_department_id+"'>"+resp[count].sub_depart_name+"</option>");
                         }
                     }
                     else
                     {
                         swal({
                             title: "Already exsist",
                             text: "Particular Sub Department Already exsist!",
                             type: "warning"
                         });
                         $("#subdepart-modal").modal("hide");

                     }
                 }

             });

         }

     }

     $("#reference").tagsinput({
         maxTags: 10
     });

      function blockSpecialChar(e) {
          var k = e.keyCode;
          console.log(e.keyCode);
          return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32   || ( k >= 40 && k <= 42) || (k >= 48 && k <= 57));

      }

	    function isNumberKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }

	   // ALLOW ONLY DECIMAL VALUE IN IN WEIGHT QTY
function myfunction(e) {
  return e.charCode === 0 || ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 46 && document.getElementById("test").value.indexOf('.') < 0));
}

let filesArray = []; // Array to keep track of the files

// Define supported file extensions
const SUPPORTED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

function readURL_multiple(input, containerId) {
    if (input.files && input.files.length) {
        // Get the container element where images will be appended
        const container = document.getElementById(containerId);

        // Clear existing files
        const newFiles = [];
        let hasError = false;

        // Check each file's size and extension
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            // Validate file size (5MB = 1,024 * 1,024 bytes)
            if (file.size > 5 * 1024 * 1024) {
                hasError = true;
                swal('Error! File Format','The file '+file.name+' exceeds the 1MB size limit.','error');
                continue;
            }

            // Validate file extension
            if (!SUPPORTED_EXTENSIONS.includes(fileExtension)) {
                hasError = true;
                swal('Error! File Format','The file '+file.name+' has an unsupported file type.','error');
                continue;
            }

            // Add valid file to newFiles array
            newFiles.push(file);
        }

        if (!hasError) {
            // Only proceed with valid files
            filesArray = [...filesArray, ...input.files];
        } else {
            // Only add valid files to filesArray
            filesArray = [...filesArray, ...newFiles];
        }

        // Update the image container
        updateImageContainer(container);

        // Reset the file input to allow selecting the same files again
        // input.value = '';
    }
}

function updateImageContainer(container) {
    // Clear the container
    container.innerHTML = '';

    filesArray.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = function(e) {
            // Create a container for each image and remove button
            const imageContainer = document.createElement('div');
            imageContainer.style.position = 'relative';
            imageContainer.style.display = 'inline-block';
            imageContainer.style.margin = '10px';

            // Create a new image element
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '75px'; // Adjust as needed
            img.style.maxHeight = '75px'; // Adjust as needed
            img.style.objectFit = 'cover'; // Ensures images fit well

            // Create a remove button
            const removeButton = document.createElement('button');
            removeButton.innerHTML = '✖'; // Cross symbol
            removeButton.style.position = 'absolute';
            removeButton.style.top = '-6px';
            removeButton.style.right = '-6px';
            removeButton.style.backgroundColor = 'red';
            removeButton.style.color = 'white';
            removeButton.style.border = 'none';
            removeButton.style.borderRadius = '50%';
            removeButton.style.cursor = 'pointer';
            removeButton.style.fontSize = '12px';

            // Add event listener to remove button
            removeButton.addEventListener('click', function() {
                // Remove the file from filesArray
                filesArray.splice(index, 1);
                // Remove the image from the container
                container.removeChild(imageContainer);
                // Update the file input to reflect changes
                updateFileInput();
            });

            // Append image and button to the container
            imageContainer.appendChild(img);
            imageContainer.appendChild(removeButton);
            container.appendChild(imageContainer);
        }

        // Read the file as a Data URL
        reader.readAsDataURL(file);
    });
}

function updateFileInput() {
    // Get the file input element
    const fileInput = document.getElementById('prodgallery');
    // Create a new DataTransfer object
    const dataTransfer = new DataTransfer();

    // Add files to the DataTransfer object
    filesArray.forEach(file => dataTransfer.items.add(file));

    // Update the file input's files property
    fileInput.files = dataTransfer.files;
}

function handleVideo(input, containerId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const container = document.getElementById(containerId);

                // Check if the file is an MP4 video
                if ($.inArray(file.type,['video/mp4','video/webm','video/ogg']) == -1) {
                    swal('Error!','Please select an mp4,webm,ogg video file.','error');
                    input.value = ''; // Clear the input if the file is not MP4
                    return;
                }

                // Clear previous content
                container.innerHTML = '';

                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.autoplay = false; // Prevent autoplay if you want to control playback
                video.style.maxWidth =  '300px'; // Adjust as needed
                video.style.maxHeight = '300px'; // Adjust as needed

                // Create a remove button
                const removeButton = document.createElement('button');
                removeButton.innerHTML = '✖'; // Cross symbol
                removeButton.classList.add('video-remove-button');

                // Add event listener to remove button
                removeButton.addEventListener('click', function() {
                    container.innerHTML = ''; // Clear the container
                    input.value = ''; // Clear the file input
                });

                // Append video and button to the container
                container.appendChild(video);
                container.appendChild(removeButton);
            }
        }

        function insertProduct_attribute(id){
           if($('#'+id+'_md').val() == "") {
             swal({
                    title: "Error Message",
                    text: "Required Field can not be blank!",
                    type: "warning"
               });

          }else{
             $.ajax({
                    url: "{{route('insertProduct_attribute')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",
                       value:$('#'+id+'_md').val(),
                       control:id,
                    },success:function(resp,textStatus, getStatus){
                        if(getStatus.status == 200){
                               swal('Success!','','success');
                               getProduct_attribute(id);
                               $('#'+id+'_md').val(null);
                               $("#create"+id+"-modal").modal('hide');
                        }else{
                            swal('Error!',resp,'error');
                        }
                    },error:function(errorResp){
                        swal('Error!',errorResp,'error');
                    }
                  });
            }
     }

     function removeOldVideo(video){
        $("#oldvideo").val(video);
        $("#videoPreviewBox").html("");
     }
  </script>

  @endsection
