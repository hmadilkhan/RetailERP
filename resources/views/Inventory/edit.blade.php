@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','Add Inventory')

@section('navinventory','active')
@section('nav_addinventory','active')
@section('content')
    <style>

    </style>
  <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                    <h5 class="card-header-text">Edit Inventory</h5>
                    <h5 class=""><a href="{{ route('invent-list') }}"><i class="text-primary text-center icofont icofont-arrow-left m-t-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list ">Back to list</i></a></h5>
                     
                  </div>
                  <div class="card-block">
  <div class="col-md-12 col-lg-12 col-sm-12" id="bar-parent2">
    <form id="inventoryupdate" method="post" class="form-horizontal" enctype="multipart/form-data" action="{{ route('update') }}">
      @method('POST')
      @csrf
            <input type="hidden" id="id" name="id" value="{{$data[0]->id}}">
            <input type="hidden" id="previmage" name="previmage" value="{{$data[0]->image}}">
            <input type="hidden" id="reminder_id" name="reminder_id" value="{{$data[0]->reminder_id}}">
           <div class="row">
               <div class="col-lg-4 col-md-4">
                   <div class="form-group">
                       <label class="form-control-label">Department</label>
                       <i id="btn_depart" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Department" ></i>
                       <select class="form-control  select2" data-placeholder="Select Department" id="depart" name="depart">
                           <option value="">Select Department</option>
                           @if($department)
                               @foreach($department as $val)
                                   @if($val->department_id == $data[0]->department_id)
                                       <option selected="selected" value="{{$val->department_id}}">{{$val->department_name}}</option>
                                   @else
                                       <option value="{{$val->department_id}}">{{$val->department_name}}</option>
                                   @endif
                               @endforeach
                           @endif
                       </select>
                       <span class="help-block"></span>
                   </div>
               </div>

               <div class="col-md-4">
                   <div class="form-group">
                       <label class="form-control-label">Sub Department</label>
                       <i id="btn_subdepart" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Sub Department" ></i>
                       <select class="form-control  select2" data-placeholder="Select Sub Department" id="subDepart" name="subDepart">
                           <option value="">Select Sub Department</option>
                           @if($subdepartment)
                               @foreach($subdepartment as $val)
                                   @if($val->sub_department_id == $data[0]->sub_department_id)
                                       <option selected="selected" value="{{$val->sub_department_id}}">{{$val->sub_depart_name}}</option>
                                   @else
                                       <option value="{{$val->sub_department_id}}">{{$val->sub_depart_name}}</option>
                                   @endif
                               @endforeach
                           @endif
                       </select>
                       <span class="help-block"></span>
                   </div>
               </div>

           <div class="col-lg-4 col-md-4">
              <div class="form-group">
                  <label class="form-control-label"><i class="icofont icofont-barcode"></i>&nbsp;Product Code</label>
                  <input class="form-control" type="text"
                   name="code" id="code" value="{{$data[0]->item_code}}" />
                   <div class="form-control-feedback"></div>
              </div>
              </div>



            </div>
          

        <div class="row">
     <div class="col-lg-4 col-md-4">
         <div class="form-group">
             <label class="form-control-label">Product Name</label>
             <input class="form-control" type="text" onkeypress="return blockSpecialChar(event)"
                    name="name" id="name" required value="{{$data[0]->product_name}}" />
             <span class="help-block"></span>
         </div>
     </div>
             <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Unit Measure</label>
                  <i id="btn_uom" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add UOM" ></i>
                    <select class="form-control  select2" data-placeholder="Select Unit Measure" id="uom" name="uom">
                       <option value="">Unit Measure</option>
                       @if($uom)
                              @foreach($uom as $val)
                                @if($val->uom_id == $data[0]->uom_id)
                                  <option selected="selected" value="{{$val->uom_id}}">{{$val->name}}</option>
                                @else
                                  <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                @endif
                              @endforeach
                       @endif
                    </select> 
                   <span class="help-block"></span>
              </div>
           </div>

     <div class="col-lg-2 col-md-2">
         <div class="form-group {{ $errors->has('weight') ? 'has-danger' : '' }}">
             <label class="form-control-label">Weight | Quantity</label>
             <input class="form-control" type="text" onkeypress="return myfunction(event);"
                    name="weight" id="weight" min="0"  value="{{$data[0]->weight_qty}}" placeholder="Enter Weight or Packet" />
             @if ($errors->has('weight'))
                 <div class="form-control-feedback">Required field can not be blank.</div>
             @endif
         </div>
     </div>
	  <div class="col-lg-3 col-md-3">
              <div class="form-group">
                  <label class="form-control-label">Conversion Unit Measure</label>
                  <i id="btn_uom" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add UOM" ></i>
                    <select class="form-control  select2" data-placeholder="Select Unit Measure" id="cuom" name="cuom">
                       <option value="">Conversion Unit Measure</option>
                       @if($uom)
                              @foreach($uom as $val)
                                @if($val->uom_id == $data[0]->cuom)
                                  <option selected="selected" value="{{$val->uom_id}}">{{$val->name}}</option>
                                @else
                                  <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                @endif
                              @endforeach
                       @endif
                    </select> 
                   <span class="help-block"></span>
              </div>
       </div>

       <div class="row">

           <div class="col-md-4">
               <div class="form-group {{ $errors->has('product_mode') ? 'has-danger' : '' }}">
                   <label class="form-control-label">Product Mode</label>
                   <select class="form-control  select2" data-placeholder="Select Product Mode" id="product_mode" name="product_mode">
                       <option value="">Select Product Mode</option>
                       @if($mode)
                           @foreach($mode as $value)
                               <option {{$value->product_mode_id == $data[0]->product_mode ? 'selected="selected"' : '' }}
                                       value="{{ $value->product_mode_id }}">{{ $value->product_name}}</option>
                           @endforeach
                       @endif

                   </select>
                   @if ($errors->has('product_mode'))
                       <div class="form-control-feedback">Required field can not be blank.</div>
                   @endif

                   <label class="form-control-label m-t-10">Qty Reminder</label>
                   <input class="form-control" type="Number"
                          name="reminder" id="reminder" required  value="{{$data[0]->reminder_qty}}"/>
                   <span class="help-block"></span>
               </div>
           </div>
             <div class="col-lg-4 col-md-4">
              <div class="form-group">
                  <label class="form-control-label">Description</label>
                  <textarea class="form-control"
                   name="description" id="description" rows="5" >{{$data[0]->product_description}}</textarea>
                   <span class="help-block"></span>
              </div>
            </div>

            <div class="col-md-4">
               <label for="image" class="form-control-label">Image</label>
             <a href="{{ asset('storage/images/products/'.(empty($data[0]->image) ? 'placeholder.jpg' : $data[0]->image).'') }}" data-toggle="lightbox" data-title="{{$data[0]->product_name}}" data-footer="{{$data[0]->product_description}}">

                        <img id="simg" src="{{ asset('storage/images/products/'.(empty($data[0]->image) ? 'placeholder.jpg' : $data[0]->image).'') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
                        </a>

                    <div class="form-group {{ $errors->has('image') ? 'has-danger' : '' }} m-t-10">
                                

                                    <label for="image" class="custom-file">
                                                <input type="file" name="image[]" id="image" class="custom-file-input" multiple>
                                                <span class="custom-file-control"></span>
                                            </label>

                                    <div>       
                                      <label class="pointer"><input type="checkbox" name="actual_image_size" class="" {{ $data[0]->actual_image_size == 1 ? 'checked' : null }}>
                                      You want to actual image size</label>
                                    </div>                                               
                                            
                                @if ($errors->has('image'))
                                    <div class="form-control-feedback">{{ $errors->first('image') }}</div>
                                @endif
                              </div> 
                
              </div>
         </div>
         
 @if(in_array(Auth::user()->username,['demoadmin','urs.sb.gs']))
        
           <div class="row m-t-2 {{ count($selectedWebsites->toArray()) > 0 ? '' : 'd-none' }}" id="prodAdvans_Media">
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
			<!--<div class="col-lg-3 col-md-3">-->
			<!--	<div class="form-group {{-- $errors->has('addons') ? 'has-danger' : '' --}}">-->
   <!--               <label class="form-control-label">Select Addons of the product (if any)</label>-->
   <!--                 <select class="form-control  select2" data-placeholder="Select Addons" id="addons" name="addons[]" multiple>-->
   <!--                    <option value="">Addons</option>-->
                       {{--@if($totaladdons)--}}
                              {{--@foreach($totaladdons as $addon)--}}
   <!--                               <option {{--(in_array($addon->id, $selectedAddons->toArray()) ? 'selected="selected"' : '' )--}}  value="{{--$addon->id--}}">{{--$addon->name--}}</option>-->
                              {{--@endforeach--}}
                      {{--@endif--}}
   <!--                 </select> -->
                    {{--@if ($errors->has('addons'))--}}
   <!--                   <div class="form-control-feedback">Required field can not be blank.</div>-->
                    {{--@endif--}}
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
							  <option {{(in_array($website->id, $selectedWebsites->toArray()) ? 'selected="selected"' : '' )}} value="{{$website->id}}">{{$website->name}}</option>
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
				     @php $isDeal = old('is_deal') ? old('is_deal') : $data[0]->is_deal @endphp
					<input id="is_deal" name="is_deal" type="checkbox" data-toggle="toggle" data-size="mini" {{ $isDeal == 1 ? 'checked' : '' }}>
				  </label> 
				</div>			    
			</div>
		 </div>
		 
		 

       <div class="row">
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('priority') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Product Priority</label>
                    <input type="number" class="form-control" name="priority" min="0" value="{{ old('priority') ? old('priority') : $data[0]->priority  }}">
              </div>
			</div>           
           
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('brand') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Brand</label>
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
			
            <div class="col-lg-3 col-md-3">
				<div class="form-group {{ $errors->has('tags') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Tags</label>
                    <select class="form-control  select2" data-placeholder="Select Tags" id="tags" name="tags[]" multiple>
                       <option value="">Select</option>
                              @php $tagsOld_val = old('tags') ? (array) old('tags') : $inventoryTags->toArray() @endphp 
                              @foreach($tagsList as $val)
                                  <option {{ (in_array($val->id,$tagsOld_val)) ? 'selected' : '' }} value="{{$val->id}}">{{$val->name}}</option>
                              @endforeach
                    </select> 
                    @if ($errors->has('tags'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>
			</div>
       </div>
      <hr/>		 
		 

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="form-group ">
                    <label class="form-control-label">Short Description (Optional)</label>
                    <textarea class="form-control" name="sdescription" id="summary-ckeditor" rows="3" >{{ $data[0]->short_description}}</textarea>
                    @if ($errors->has('sdescription'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group ">
                    <label class="form-control-label">Details (Optional)</label>
                    <textarea class="form-control" name="details" id="details" rows="3" >{{ $data[0]->details}}</textarea>
                    @if ($errors->has('details'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="form-group">
                    <label class="form-control-label">References</label>
                    <div class="tags_add">
                        <input class="form-control" id="reference" name="reference" type="text"  value="{{($references != "" ? $references : '')}}" />
                    </div>
                    <span class="form-control-feedback text-danger" id="subdpt_alert"></span>
                </div>
            </div>
        </div>

        <div class="row">
			<div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('cost_price') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Cost Price:<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="cost_price" id="cost_price" value="{{ $prices[0]->cost_price }}" placeholder="0"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('ap') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Actual Price:<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="ap" id="ap" value="{{ $prices[0]->actual_price }}" placeholder="0"/>
                    @if ($errors->has('ap'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Tax Rate:</label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0"  step=".01" name="taxrate" id="taxrate" value="{{ $prices[0]->tax_rate }}"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Tax Amount:</label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="taxamount" id="taxamount" value="{{ $prices[0]->tax_amount }}"/>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="form-group {{ $errors->has('rp') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Retail Price<span class="text-danger m-l-5">*</span></label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="rp" id="rp" placeholder="0" value="{{ $prices[0]->retail_price }}" />
                    @if ($errors->has('rp'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Wholesale Price:</label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="wp" id="wp" value="{{ $prices[0]->wholesale_price }}"/>
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Discount Price:</label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="dp" id="dp" value="{{ $prices[0]->discount_price }}"/>
                </div>
            </div>

            <div class="col-md-3 col-lg-3">
                <div class="form-group">
                    <label class="form-control-label">Online Price:</label>
                    <input class="form-control" type="text" onkeypress="return isNumberKey(event)"  min="0" name="op" id="op" value="{{ $prices[0]->online_price }}"/>
                </div>
            </div>

        </div>

            <div id="gallery" class="row gallery">
                @foreach($images as $value)
                    <div class="img-wrap col-md-2 ">
                        <img width=200 height=200 style="margin-left:5px;margin-top:20px;" src="{{ asset('assets/images/products/'.$value->image)}}"/>
                        <center> <button onclick="deleteImage('{{$value->id}}','{{$value->image}}')" class="btn btn-danger btn-icon waves-effect waves-light m-t-10" type="button" ><i class="icofont icofont-ui-delete"></i></button></center>
                    </div>
{{--                    <img width=200 height=200 style="margin-left:20px;margin-top:20px;" src="{{ asset('public/assets/images/products/'.$value->image)}}"/>--}}
{{--                    <label ><i class="icofont icofont-ui-danger text-danger"></i></label>--}}
                @endforeach
            </div>

            <div class="row m-t-30">
              <div class="col-lg-12 col-md-12 ">
                  <button class="btn btn-md btn-circle btn-primary m-b-10 f-right" type="submit"><i class="icofont icofont-ui-edit"></i>Update</button>
             </div>
           </div>


         </form>
  </div>
            
                  </div>
               </div>

            </section>

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
@endsection
@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection
@section('scriptcode_three')
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script> 
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/md5-js-tools@1.0.2/lib/md5.min.js"></script>
	<script type="text/javascript">
      CKEDITOR.replace( 'summary-ckeditor' );
	  var rem_id = [];
	  
	  $( '#inventoryupdate').submit( function(e){
		  e.preventDefault();
		  var form = $(this);
		  var actionUrl = form.attr('action');
		  $.ajax({
				type: "POST",
				url: actionUrl,
				// data: form.serialize(), // serializes the form's elements.
				data:new FormData(this),
			    dataType:'JSON',
			    contentType: false,
			    cache: false,
			    processData: false,
				success: function(data,statusText,getStatus)
				{
				  console.log("",data); // show response from the php script.
				  if(data == 1)
				  {
					  location.reload();
				  }
				  
		
				}
			});
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
     $(".select2").select2();

     //light box
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();
    });



     $("#image").change(function() {
         // readURL(this,'simg');
         imagesPreview(this, 'div.gallery');
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


  </script>

  <script>


  </script>

  @endsection
