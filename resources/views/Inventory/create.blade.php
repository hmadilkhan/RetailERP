@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','Add Inventory')

@section('navinventory','active')

@section('content')



  <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                      <h5 class="card-header-text"> Create Inventory</h5>
                      <h5 class=""><a href="{{ route('invent-list') }}"><i class="text-primary text-center icofont icofont-arrow-left m-t-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">Back to list</i></a></h5>
                  </div>
                  <div class="card-block">
    <form method="POST" class="form-horizontal" enctype="multipart/form-data" action="{{route('insert')}}" >
      @csrf

        <div class="row">
          <div class="col-lg-4 col-md-4">
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

                <div class="col-md-4">
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

           <div class="col-lg-4 col-md-4">
              <div  id="itemcode" class="form-group {{ $errors->has('code') ? 'has-danger' : '' }} ">
                  <label class="form-control-label"><i class="icofont icofont-barcode"></i>&nbsp;Product Code <span class="text-danger m-l-5">*</span></label>
                  <i id="btngen" class="f-right text-primary" data-toggle="tooltip" data-placement="top" title="Auto Generate"> Auto Generate</i>
                  <input class="form-control" type="text"
                   name="code" id="code" value="{{ old('code') }}" placeholder="Enter Product Code" onchange="samecode()"/>
                    @if ($errors->has('code'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
              </div>

             

            </div>
          
		  {{-- onkeypress="return blockSpecialChar(event)"  --}}
		  {{-- Raza asked to remove this   --}}
		  
 <div class="row">
        <div class="col-lg-4 col-md-4">
              <div class="form-group {{ $errors->has('name') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Name<span class="text-danger m-l-5">*</span></label>
                  <input class="form-control" type="text"
                   name="name" id="name"  value="{{ old('name') }}" onchange="samename()"  placeholder="Enter Product Name"/>
                    @if ($errors->has('name'))
                      <div class="form-control-feedback" id="nameerror">{{ $errors->first('name') }}</div>
                    @endif
              </div>
            </div>


             <div class="col-lg-3 col-md-3">
              <div class="form-group {{ $errors->has('uom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Unit Measure<span class="text-danger m-l-5">*</span></label>
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
     <div class="col-lg-2 col-md-2">
         <div class="form-group {{ $errors->has('weight') ? 'has-danger' : '' }}">
             <label class="form-control-label">Weight | Quantity</label>
             <input class="form-control" type="text" onkeypress="return myfunction(event);"
                    name="weight" id="weight" value="{{ old('weight') }}" placeholder=" Weight / Packet" />
             @if ($errors->has('weight'))
                 <div class="form-control-feedback">Required field can not be blank.</div> 
             @endif
         </div>
     </div>
	 
	  <div class="col-lg-3 col-md-3">
              <div class="form-group {{ $errors->has('cuom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Conversion Unit Measure<span class="text-danger m-l-5">*</span></label>
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

       </div>

       <div class="row">
          <div class="col-md-4">
              <div class="form-group {{ $errors->has('product_mode') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Mode<span class="text-danger m-l-5">*</span></label>
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


                  <label class="form-control-label m-t-10">Qty Reminder<span class="text-danger m-l-5">*</span></label>
                  <input class="form-control" type="Number" min="0"
                         name="reminder" id="reminder" value="{{ old('reminder') }}" placeholder="Enter Quantity Reminder" />
                  @if ($errors->has('reminder'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                  @endif
              </div>

           </div>
           
             <div class="col-lg-4 col-md-4">
              <div class="form-group {{ $errors->has('description') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Description</label>

                  <textarea class="form-control"
                   name="description" id="description" rows="5" >{{ old('description') }}</textarea>
                    @if ($errors->has('description'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
            </div>

            <div class="col-md-4">
               <label for="image" class="form-control-label">Image</label>
             <a href="#">
                        <img id="simg" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
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
                                    <div class="form-control-feedback">{{ $errors->first('image') }}</div>
                                @endif
                              </div> 
                
              </div>
         </div>
         
        @if(in_array(Auth::user()->username,['demoadmin','urs.sb.gs']))
        
           <div class="row m-t-2 d-none" id="prodAdvans_Media">
            <hr/>   
               <div class="col-md-5">
                   <div class="form-group">
                       <label class="form-control-label">Product Video</label>
                       <br/>
                        <label for="prodvideo" class="custom-file">
                            <input type="file" name="prodvideo" id="prodvideo" class="custom-file-input">
                            <span class="custom-file-control"></span>
                        </label>
                   </div>
               </div>
               <div class="col-md-7">
                   <div class="form-group">
                       <label class="form-control-label">Product Gallery</label>
                       <br/>
                        <label for="prodgallery" class="custom-file">
                            <input type="file" name="prodgallery[]" id="prodgallery" class="custom-file-input" multiple>
                            <span class="custom-file-control"></span>
                        </label>
                   </div>
               </div>               
               
           </div>
        @endif         

		 <hr/>
		 <div class="row">
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('cuom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Select Vendors of the product (if any)</label>
                    <select class="form-control  select2" data-placeholder="Select Vendors" id="vendor" name="vendor[]" multiple>
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
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
			</div>
			<!--<div class="col-lg-3 col-md-3">-->
			<!--	<div class="form-group {{-- $errors->has('addons') ? 'has-danger' : '' --}}">-->
   <!--               <label class="form-control-label">Select Addons of the product (if any)</label>-->
   <!--                 <select class="form-control  select2" data-placeholder="Select Addons" id="addons" name="addons[]" multiple>-->
   <!--                    <option value="">Addons</option>-->
                       {{-- @if($totaladdons)--}}
                           {{--  @foreach($totaladdons as $addon)--}}
                              {{--  @if( old('addons') == $addon->id)--}}
   <!--                               <option selected="selected" value="{{--$addon->id--}}">{{--$addon->name--}}</option>-->
                               {{-- @else--}}
   <!--                               <option value="{{--$addon->id--}}">{{--$addon->name--}}</option>-->
                              {{--  @endif--}}
                            {{--  @endforeach--}}
                      {{-- @endif--}}
   <!--                 </select> -->
                   {{-- @if ($errors->has('addons'))--}}
   <!--                   <div class="form-control-feedback">Required field can not be blank.</div>-->
                   {{-- @endif--}}
   <!--           </div>-->
			<!--</div>-->
			@if(count($websites) > 0)
			<div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('website') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Select Website (Select on where product to show)</label>
                    <select class="form-control  select2" data-placeholder="Select Website" id="website" name="website[]" multiple>
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
			@endif
		
			
			<div class="col-lg-3 col-md-3">
				<div class="form-group"> 
				   <label>Is Deal</label>
				   <br/>
				 <label>
					<input id="is_deal" name="is_deal" type="checkbox" data-toggle="toggle" data-size="mini" {{ old('is_deal') ? 'checked' : '' }}>
				  </label> 
				</div>
			</div>
		</div>
		
		<hr/>

       <div class="row">
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('priority') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Priority</label>
                    <input type="number" class="form-control" name="priority" min="0" value="0">
              </div>
			</div>           

            <div class="col-lg-3 col-md-3">
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
           
            <div class="col-lg-3 col-md-3">
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

        <div class="row">
            <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="form-group ">
                    <label class="form-control-label">Short Description <i>(For Website Only)</i></label>
                    <textarea class="form-control" name="sdescription" id="summary-ckeditor" rows="3" >{{ old('sdescription') }}</textarea>
                    @if ($errors->has('sdescription'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            </div>
            <div class="col-lg-6 col-md-8">
                <div class="form-group ">
                    <label class="form-control-label">Details <i>(For Website Only)</i></label>
                    <textarea class="form-control" name="details" id="details" rows="3" >{{ old('details') }}</textarea>
                    @if ($errors->has('details'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label class="form-control-label">References</label>
                    <div class="tags_add">
                        <input class="form-control" id="reference" name="reference" type="text"  />
                    </div>
                    <span class="form-control-feedback text-danger" id="subdpt_alert"></span>
                </div>
            </div>
        </div>

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

        <div class="row col-md-12 gallery">

        </div>
        

            <div class="row m-t-30">
              <div class="col-lg-12 col-md-12 ">
                  <button id="btnSubmit" class="btn btn-lg btn-circle btn-primary m-b-10 f-right" type="submit"><i class="icofont icofont-plus"></i>&nbsp;Submit Details</button>
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
                  <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="adddepartment()">Add Department</button>
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
                  <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="addsubdepart()">Add Sub Department</button>
               </div>
            </div>
         </div>
      </div> 
            </section>    
@endsection
@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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


    function readURL(input,id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#'+id).attr('src', e.target.result);

            }

            reader.readAsDataURL(input.files[0]);
        }
    }

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

  </script>

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'summary-ckeditor' );

</script>
  @endsection



