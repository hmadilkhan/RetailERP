@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','Add Inventory')

@section('navinventory','active')

@section('content')

 <div class="main-header">
     <h4> Create Inventory</h4>
     <br/>
     <a href="{{ route('invent-list') }}">
         <i class="text-primary text-center icofont icofont-arrow-left m-t-10 f-18"
         data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">
             Back to list</i>
     </a>
  </div>

  <section class="panels-wells">


    <form method="POST" id="inventCreateForm" class="form-horizontal" enctype="multipart/form-data" action="{{route('insert')}}">
      @csrf

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
                              @foreach($department as $val)
                                @if( old('depart') == $val->department_id)
                                  <option selected="selected" value="{{$val->department_id}}">{{$val->department_name}}</option>
                                @else
                                  <option value="{{$val->department_id}}">{{$val->department_name}}</option>
                                @endif
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
                       name="code" id="code" value="{{ old('code') }}" placeholder="Enter Product Code" onchange="samecode()"/>
                        @if ($errors->has('code'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                  </div>
              </div>
             <div class="col-lg-6 col-md-6">
              <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Name <span class="text-danger">*</span></label>
                  <input class="form-control" type="text"
                   name="name" id="name"  value="{{ old('name') }}" onchange="samename()"  placeholder="Enter Product Name"/>
                    @if ($errors->has('name'))
                      <div class="form-control-feedback" id="nameerror">Required field can not be blank.</div>
                    @endif
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
                              @foreach($uom as $val)
                                @if( old('uom') == $val->uom_id)
                                  <option selected="selected" value="{{$val->uom_id}}">{{$val->name}}</option>
                                @else
                                  <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                @endif
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
                    name="weight" id="weight" value="{{ old('weight') }}" placeholder=" Weight / Packet" />
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
                              @foreach($uom as $val)
                                @if( old('cuom') == $val->uom_id)
                                  <option selected="selected" value="{{$val->uom_id}}">{{$val->name}}</option>
                                @else
                                  <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                @endif
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
                              @foreach($mode as $val)
                                @if( old('product_mode') == $val->product_mode_id)
                                  <option selected="selected" value="{{$val->product_mode_id}}">{{$val->product_name}}</option>
                                @else
                                  <option value="{{$val->product_mode_id}}">{{$val->product_name}}</option>
                                @endif
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
                         name="reminder" id="reminder" value="{{ old('reminder') }}" placeholder="Enter Quantity Reminder" />
                  @if ($errors->has('reminder'))
                      <span class="form-control-feedback">Required field can not be blank.</span>
                  @endif
            </div>

				<div class="form-group {{ $errors->has('cuom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Select Vendors of the product (if any)</label>
                    <select class="form-control select2" data-placeholder="Select Vendors" id="vendor" name="vendor[]" multiple>
                       <option value="">Vendors</option>
                       @if($uom)
                              @foreach($vendors as $vendor)
                                @if( old('vendor') == $vendor->id)
                                  <option selected="selected" value="{{$vendor->id}}">{{$vendor->vendor_name}}</option>
                                @else
                                  <option value="{{$vendor->id}}">{{$vendor->vendor_name}}</option>
                                @endif
                              @endforeach
                       @endif
                    </select>
                    @if ($errors->has('cuom'))
                      <span class="form-control-feedback">Required field can not be blank.</span>
                    @endif
              </div>
			</div>


             <div class="col-md-6">
              <div class="form-group {{ $errors->has('description') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Description</label>

                  <textarea class="form-control"
                   name="description" id="description" rows="5" >{{ old('description') }}</textarea>
                    @if ($errors->has('description'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label class="form-control-label">References</label>
                    <div class="tags_add">
                        <input class="form-control" id="reference" name="reference" type="text"  />
                    </div>
                    <span class="form-control-feedback text-danger" id="subdpt_alert"></span>
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
    				<div class="form-group {{ $errors->has('brand') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Brand</label>
                      <i data-toggle="modal" data-target="#createbrand-modal" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Brand"></i>
                        <select class="form-control  select2" data-placeholder="Select Brand" id="brand" name="brand">
                           <option value="">Select</option>

                                  @foreach($brandList as $val)
                                      <option {{ old('brand') == $val->id ? 'selected' : '' }} value="{{$val->id}}">{{$val->name}}</option>
                                  @endforeach
                        </select>
                        @if ($errors->has('brand'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                  </div>
            </div>
        </div>

        <hr/>

	  @if(count($websites) > 0)
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="showProductWebsite">
                        <input type="checkbox" id="showProductWebsite" name="showProductWebsite">
                        Show Product on Website
                    </label>
                </div>
            </div>
        </div>
      <div class="d-none" id="website-module">
        <div class="row">
            <div class="col-md-5">
				<div class="form-group {{ $errors->has('website') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Select Website (Select on where product to show)</label>
                    <select class="form-control  select2" data-placeholder="Select Website" id="website" name="website">
                       <option value="">Websites</option>
                       @if($websites)
						  @foreach($websites as $website)
							  <option value="{{$website->id}}">{{$website->name}}</option>
						  @endforeach
                       @endif
                    </select>
                    @if ($errors->has('website'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
				</div>
            </div>
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('priority') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Priority</label>
                    <input type="number" class="form-control" name="priority" min="0" value="0">
              </div>
			</div>

            <div class="col-lg-3 col-md-4">
				<div class="form-group {{ $errors->has('tags') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Tags</label>
                  <i data-toggle="modal" data-target="#createtag-modal" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Tag"></i>
                    <select class="form-control  select2" data-placeholder="Select Tags" id="tags" name="tags[]" multiple>
                       <option value="">Select</option>
                              @php $oldTag = (array) old('tag') @endphp
                              @foreach($tagsList as $val)
                                  <option {{ (in_array($val->id,$oldTag)) ? 'selected' : '' }} value="{{$val->id}}">{{$val->name}}</option>
                              @endforeach
                    </select>
                    @if ($errors->has('tags'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
			</div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="form-group ">
                    <label class="form-control-label">Short Description <i>(For Website Only)</i></label>
                    <textarea class="form-control" name="sdescription" id="summary-ckeditor" rows="3" >{{ old('sdescription') }}</textarea>
                    @if ($errors->has('sdescription'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="form-group ">
                    <label class="form-control-label">Details <i>(For Website Only)</i></label>
                    <textarea class="form-control" name="details" id="details" rows="6"></textarea>
                    @if ($errors->has('details'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
          </div>
         </div>
       @endif

                  </div>    <!--card block-->
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
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)"  min="0" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" placeholder="0"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('ap') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Actual Price:<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="ap" id="ap" value="{{ old('ap') }}" placeholder="0"/>
                    @if ($errors->has('ap'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Tax Rate:</label>
                    <input class="form-control" type="Number"  step=".01" name="taxrate" id="taxrate" value="{{ old('taxrate') }}"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Tax Amount:</label>
                    <input class="form-control" type="Number" min="0" name="taxamount" id="taxamount" value="{{ old('taxamount') }}"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('rp') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Retail Price<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="rp" id="rp" placeholder="0" value="{{ old('rp') }}" />
                    @if ($errors->has('rp'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Wholesale Price:</label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="wp" id="wp" value="0"/>
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Discount Price:</label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="dp" id="dp" value="0"/>
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Online Price:</label>
                    <input class="form-control" type="text" onkeypress="return isDecimalKey(event,this)" min="0" name="op" id="op" value="0"/>
                </div>
            </div>

        </div>
{{--        style="display: none;"--}}

            <hr>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <h5>POS Inventory</h5>
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkactive" name="chkactive" class="mainchk" onchange="toggle()">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions text-info f-16 m-b-5">If you want to sale this product in POS so please enter here name, item code and also selling price.</div>
                </div>
            </div>

        </div>
        <div class="row" id="posdiv" style="display: none;">
            <div class="col-lg-3 col-md-4">
                <div  id="positemcode" class="form-group {{ $errors->has('poscode') ? 'has-danger' : '' }} ">
                    <label class="form-control-label"><i class="icofont icofont-barcode"></i>&nbsp;Enter POS Inventory Code</label>
                    <input type="text" name="poscode" id="poscode" class="form-control"  value="{{ old('poscode') }}" />
                    @if ($errors->has('poscode'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @else
                    @endif
                </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="form-group {{ $errors->has('posname') ? 'has-danger' : '' }} ">
                    <label class="form-control-label">Enter POS Inventory Name</label>
                    <input type="text" name="posname" id="posname" class="form-control"  value="{{ old('posname') }}" />
                    @if ($errors->has('posname'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @else
                    @endif

                </div>
			</div>
            <div class="col-lg-3 col-md-3">
              <div class="form-group {{ $errors->has('posuom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">POS Unit Measure<span class="text-danger m-l-5">*</span></label>
                   <i id="btn_uom" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add UOM" ></i>
                    <select class="form-control  select2" data-placeholder="Select Unit Measure" id="posuom" name="posuom">
                       <option value="">POS Unit Measure</option>
                       @if($uom)
                              @foreach($uom as $val)
                                @if( old('posuom') == $val->uom_id)
                                  <option selected="selected" value="{{$val->uom_id}}">{{$val->name}}</option>
                                @else
                                  <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                @endif
                              @endforeach
                       @endif
                    </select>
                    @if ($errors->has('posuom'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
            </div>
            <div class="col-lg-3 col-md-4">
                <div class="form-group {{ $errors->has('posprice') ? 'has-danger' : '' }} ">
                    <label class="form-control-label">Enter POS Inventory Price</label>
                    <input type="text" name="posprice" id="posprice" class="form-control"  value="{{ old('posprice') }}" />
                    @if ($errors->has('posprice'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @else
                    @endif

                </div>

            </div>







        </div>

        <div class="row m-t-20">
            <div class="col-md-12 col-lg-12">
                <h5>Stock Opening</h5>
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkstock" name="chkstock" class="mainchk" onchange="togglestock()">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions text-info f-16 m-b-5">If you want to perform stock opening, please enter cost and qty for the specific product.</div>
                </div>
            </div>
        </div>

        <div class="row" id="stockdiv" style="display: none;">
            <div class="col-lg-4 col-md-4">
                <div  id="stockcost" class="form-group {{ $errors->has('poscode') ? 'has-danger' : '' }} ">
                    <label class="form-control-label">Stock Cost</label>
                    <input type="text" name="stock_cost" id="stock_cost" class="form-control"  />
                    @if ($errors->has('poscode'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @else
                    @endif
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group {{ $errors->has('posname') ? 'has-danger' : '' }} ">
                    <label class="form-control-label">Stock Qty</label>
                    <input type="text" name="stock_qty" id="stock_qty" class="form-control"   />
                    @if ($errors->has('posname'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @else
                    @endif

                </div>
            </div>
        </div>

                  </div>    <!--card block-->
              </div> <!-- card div close -->

          </div>


          <div class="col-md-3">
              <div class="card">
                  <div class="card-header">
                  <h4 for="image">Product Image</h4>
                  </div>
                  <div class="card-block p-2 p-t-0">
              <div class="form-group">
                       <a href="javascript:void(0)">
                        <img id="simg" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img width-100" height="380px" alt="img">
                        </a>

                    <div class="form-group {{ $errors->has('image') ? 'has-danger' : '' }} m-t-10">


                                    <label for="image" class="custom-file">
                                                <input type="file" name="image" id="image" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                    <div>
                                      <label class="pointer"><input type="checkbox" name="actual_image_size" class=""> You want to actual image size</label>
                                    </div>
                                @if ($errors->has('image'))
                                    <span class="form-control-feedback">{{ $errors->first('image') }}</span>
                                @endif
                              </div>

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
                  <div id="videoPreviewBox"></div>
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
        {{-- @endif                   --}}

                  </div>
              </div>


          </div>
      </div>



                <div class="row m-t-30">
              <div class="col-lg-12 col-md-12 text-center">
                  <button id="btnSubmit" class="btn btn-lg btn-circle btn-primary m-b-10" type="submit"><i class="icofont icofont-plus"></i>&nbsp;Submit Details</button>
             </div>
           </div>




          </form>


                  </div>
               </div>





            <div class="modal fade modal-flex" id="createbrand-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Add Brand</h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group">
                                            <label class="form-control-label">Brand:</label>
                                             <input type="text"  name="brand_md" id="brand_md" class="form-control" />
                                            </div>
                                          </div>
                                      </div>
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn_brand_md" class="btn btn-success waves-effect waves-light" onClick="insertProduct_attribute('brand')">Add</button>
                                 </div>
                              </div>
                           </div>
                        </div>

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
            </section>
@endsection
@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  @if(in_array(Auth::user()->username,['demoadmin','urs.sb.gs']))
    <style>
.nav-tabs1 {
    border-bottom: 1px solid #ddd
}
.nav-tabs1::after {
    content: "";
    display: table;
      clear: both
}
.nav-tabs1 .nav-item {
    float: left;
    margin-bottom: -1px
}
.nav-tabs1 .nav-item+.nav-item {
    margin-left: .2rem
}
.nav-tabs1 .nav-link {
    display: block;
    padding: .5em 1em;
    border: 1px solid transparent;
    border-top-right-radius: .25rem;
    border-top-left-radius: .25rem
}
.nav-tabs1 .nav-link:focus,
.nav-tabs1 .nav-link:hover {
    border-color: #eceeef #eceeef #ddd
}
.nav-tabs1 .nav-link.disabled,
.nav-tabs1 .nav-link.disabled:focus,
.nav-tabs1 .nav-link.disabled:hover {
    color: #818a91;
    background-color: transparent;
    border-color: transparent
}
.nav-tabs1 .nav-item.open .nav-link,
.nav-tabs1 .nav-item.open .nav-link:focus,
.nav-tabs1 .nav-item.open .nav-link:hover,
.nav-tabs1 .nav-link.active,
.nav-tabs1 .nav-link.active:focus,
.nav-tabs1 .nav-link.active:hover {
    color: #55595c;
    background-color: #fff;
    border-color: #ddd #ddd transparent
}
.nav-tabs1 .dropdown-menu {
    margin-top: -1px;
    border-top-right-radius: 0;
    border-top-left-radius: 0
}

 .nav-tabs1 .nav-item .nav-link{
    color: #55595c;
    font-size: 22px;
    font-weight: 400;
    cursor: pointer;
    -webkit-transition: all 0.3s 0s;
    -moz-transition: all 0.3s 0s;
    -ms-transition: all 0.3s 0s;
    transition: all 0.3s 0s;
}


        /* Product Gallery */
/* ----------------------------------------- */
        /* .image-container {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .image-container img {
            max-width: 50px; /* Adjust as needed */
       /*     max-height: 50px; /* Adjust as needed */
       /*     object-fit: cover;
        }
        .remove-button {
            position: absolute;
            top: -6px;
            right: -6px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
        } */
        /* Product Video */
/* ----------------------------------------- */
        /*.video-container {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .video-container video {
            max-width: 300px; /* Adjust as needed */
       /*     max-height: 300px; /* Adjust as needed */
       /*     object-fit: cover;
        }
        .remove-button {
            position: absolute;
            top: -6px;
            right: -6px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            padding: 5px;
        }
        */
    </style>

  @endif
@endsection
@section('scriptcode_three')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript">
	const isDecimalKey = (event,element)=>{
       event.target.setCustomValidity('');
      const patt = /^\d+\.{0,1}\d{0,2}$/;
      let value = event.target.value;
      if(!patt.test(value)){
        event.target.reportValidity();
        element.setAttribute("maxlength",value.length);
      }
      else
      {
        element.removeAttribute("maxlength")
      }
     if(value.length === 0){
        element.removeAttribute("maxlength");
     }
   }
   $(".select2").select2();

   $('#inventCreateForm').on('submit', function() {
                $('#btnSubmit').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait');
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

   $("#reference").tagsinput({
       maxTags: 10
   });

   $("#image").change(function() {
       // readURL(this,'simg');
       imagesPreview(this, 'div.gallery');
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

            // Validate file size (1MB = 1,024 * 1,024 bytes)
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


//   let filesArray = []; // Array to keep track of the files

// function readURL_multiple(input, containerId) {
//     if (input.files && input.files.length) {
//         // Get the container element where images will be appended
//         const container = document.getElementById(containerId);

//         // Update the filesArray with new files
//         for (let i = 0; i < input.files.length; i++) {
//             filesArray.push(input.files[i]);
//         }

//         // Loop through the filesArray and display images
//         updateImageContainer(container);

//         // Reset the file input to allow selecting the same files again
//         // input.value = '';
//     }
// }

// function updateImageContainer(container) {
//     // Clear the container
//     container.innerHTML = '';

//     filesArray.forEach((file, index) => {
//         const reader = new FileReader();

//         reader.onload = function(e) {
//             // Create a container for each image and remove button
//             const imageContainer = document.createElement('div');
//             imageContainer.style.position = 'relative';
//             imageContainer.style.display = 'inline-block';
//             imageContainer.style.margin = '10px';

//             // Create a new image element
//             const img = document.createElement('img');
//             img.src = e.target.result;
//             img.style.maxWidth = '75px'; // Adjust as needed
//             img.style.maxHeight = '75px'; // Adjust as needed
//             img.style.objectFit = 'cover'; // Ensures images fit well

//             // Create a remove button
//             const removeButton = document.createElement('button');
//             removeButton.innerHTML = '✖'; // Cross symbol
//             removeButton.style.position = 'absolute';
//             removeButton.style.top = '-6px';
//             removeButton.style.right = '-6px';
//             removeButton.style.backgroundColor = 'red';
//             removeButton.style.color = 'white';
//             removeButton.style.border = 'none';
//             removeButton.style.borderRadius = '50%';
//             removeButton.style.cursor = 'pointer';
//             removeButton.style.fontSize = '12px';

//             // Add event listener to remove button
//             removeButton.addEventListener('click', function() {
//                 // Remove the file from filesArray
//                 filesArray.splice(index, 1);
//                 // Remove the image from the container
//                 container.removeChild(imageContainer);
//                 // Update the file input to reflect changes
//                 updateFileInput();
//             });

//             // Append image and button to the container
//             imageContainer.appendChild(img);
//             imageContainer.appendChild(removeButton);
//             container.appendChild(imageContainer);
//         }

//         // Read the file as a Data URL
//         reader.readAsDataURL(file);
//     });
// }

// function updateFileInput() {
//     // Get the file input element
//     const fileInput = document.getElementById('prodgallery');
//     // Create a new DataTransfer object
//     const dataTransfer = new DataTransfer();

//     // Add files to the DataTransfer object
//     filesArray.forEach(file => dataTransfer.items.add(file));

//     // Update the file input's files property
//     fileInput.files = dataTransfer.files;

//     // console.log($("#prodgallery").get)
// }

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
                removeButton.classList.add('remove-button');

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

//   function readURL_multiple(input, containerId) {
//     // Ensure the input element allows multiple files
//     if (input.files && input.files.length) {
//         // Get the container element where images will be appended
//         var container = document.getElementById(containerId);

//         // Clear any existing images
//         container.innerHTML = '';

//         // Loop through each selected file
//         for (var i = 0; i < input.files.length; i++) {
//             var file = input.files[i];
//             var reader = new FileReader();

//             reader.onload = function(e) {
//                 // Create a container for each image and remove button
//                 var imageContainer = document.createElement('div');
//                 imageContainer.style.position = 'relative';
//                 imageContainer.style.display = 'inline-block';
//                 imageContainer.style.margin = '10px';

//                 // Create a new image element
//                 var img = document.createElement('img');
//                 img.src = e.target.result;
//                 img.style.maxWidth = '50px'; // Adjust as needed
//                 img.style.maxHeight = '50px'; // Adjust as needed
//                 img.style.objectFit = 'cover'; // Ensures images fit well

//                 // Create a remove button
//                 var removeButton = document.createElement('button');
//                 removeButton.innerHTML = '✖'; // Cross symbol
//                 removeButton.style.position = 'absolute';
//                 removeButton.style.top = '-6px';
//                 removeButton.style.right = '-6px';
//                 removeButton.style.backgroundColor = 'red';
//                 removeButton.style.color = 'white';
//                 removeButton.style.border = 'none';
//                 removeButton.style.borderRadius = '50%';
//                 removeButton.style.cursor = 'pointer';
//                 // removeButton.style.padding = '5px 8px';
//                 removeButton.style.fontSize = '12px';

//                 // Add event listener to remove button
//                 removeButton.addEventListener('click', function() {
//                     container.removeChild(imageContainer);
//                 });

//                 // Append image and button to the container
//                 imageContainer.appendChild(img);
//                 imageContainer.appendChild(removeButton);
//                 container.appendChild(imageContainer);
//             }

//             // Read the file as a Data URL
//             reader.readAsDataURL(file);
//         }
//     }
// }



$("#image").change(function() {
  readURL(this,'simg');
});

   if($('#depart').val() != ""){
       $.ajax({
           url:'{{ url("/getSubdepartBydepartID") }}',
           data:{_token : "{{csrf_token()}}",id:$('#depart').val()},
           type:"POST",
           success:function(result){
               $('#subDepart').empty();
               $('#subDepart').append($('<option>').text('Select City').attr('value', ''));
               $.each(result, function (i, value) {
                   if(value.sub_department_id == "{{old('subDepart')}}"){
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

    function getProduct_attribute(id){
             $.ajax({
                    url: "{{route('getProduct_attribute')}}",
                    type: 'POST',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                       control:id,
                    },success:function(resp,textStatus, getStatus){
                        if(resp != null){
                            var element = (id == 'tag' ? 'tags' : id);
                            $("#"+element).empty();

                            $.each(resp,function(i,v){
                                  $("#"+element).append($('<option>').text(v.name).attr('value', v.id));
                            })
                        }
                    }
                  });
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




     $("#code").on('change',function(){
      $.ajax({
            url: "{{url('/chk-itemcode')}}",
            method: 'POST',
            data:{_token:"{{ csrf_token() }}",itemcode:$('#code').val()},
            success:function(resp){
              if(resp == 1){
                  $('#itemcode').addClass('has-danger');
                      swal({
                            title: "Already exsist",
                            text: "Item Code Already exsist!",
                            type: "warning"
                       });
                      $('#code').val('');
                      $('#poscode').val('');

              }else{
                  $('#itemcode').removeClass('has-danger');
                  $('#itemcode').addClass('has-success');
              }

            }
         });
       $("#code").focus();
});

   $("#poscode").on('change',function(){

       $.ajax({
           url: "{{url('/verifycode')}}",
           method: 'GET',
           data:{_token:"{{ csrf_token() }}",code:$('#poscode').val()},
           success:function(resp){
               if(resp == 1){
                   $('#positemcode').addClass('has-danger');
                   swal({
                       title: "Already exsist",
                       text: "Item Code Already exsist!",
                       type: "warning"
                   });
                   $('#poscode').val('');

               }else{
                   $('#positemcode').removeClass('has-danger');
                   $('#positemcode').addClass('has-success');
               }

           }
       });
   });

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

  function toggle() {
      if($('#chkactive').is(":checked")){

        $('#posdiv').css('display','block');
    }
    else{
        $('#posdiv').css('display','none');
    }


  }

   function togglestock() {
       if($('#chkstock').is(":checked")){

           $('#stockdiv').css('display','block');
       }
       else{
           $('#stockdiv').css('display','none');
       }


   }

   $('#btngen').on('click', function(){
       if ($('#depart').val() == "") {
           swal({
               title: "Error Message",
               text: "Select Department First!",
               type: "error"
           });
       }
       else if ($('#subDepart').val() == "") {
           swal({
               title: "Error Message",
               text: "Select Sub Department First!",
               type: "error"
           });
       }
       else{

           // let depart = $('#depart option:selected').text();
           // let subdepart = $('#subDepart option:selected').text();

           // let d = depart.substring(0,1);
           // let sub = subdepart.substring(0,1)
           // let rand = Math.floor(Math.random() * 10000);

           // let codes = d+ sub + "-" + rand;

           // $('#code').val(codes);

			$.ajax({
				url: "{{url('/get-product-code')}}",
				type: 'POST',
				dataType:"json",
				data:{
					_token:"{{ csrf_token() }}",
					departmentId:$('#depart').val(),
					subdepartmentId:$('#subDepart').val(),
				},
				success:function(resp){
					console.log(resp)
					if(resp.status == 200){
						$('#code').val(resp.code);
					}else{
						$('#code').val("");
					}
				}
			});
       }

   });


function samecode() {
	let code = $('#code').val();
	code = "POS-"+code;
	$('#poscode').val(code);
}

function samename() {
	let name = $('#name').val();
		name = "POS-"+name;
		$('#posname').val(name);


}

function validate(form) {
  var re = /^[a-z,A-Z]+$/i;

  if (!re.test(form)) {
    alert('Please enter only letters from a to z');
    return false;
  }
}

   function blockSpecialChar(e) {
       var k = e.keyCode;
       return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32   || ( k >= 40 && k <= 42) || (k >= 48 && k <= 57));

   }

// ALLOW ONLY DECIMAL VALUE IN IN WEIGHT QTY
function myfunction(e) {
  return e.charCode === 0 || ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 46 && document.getElementById("test").value.indexOf('.') < 0));
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
})

  </script>

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'summary-ckeditor' );

</script>
  @endsection



