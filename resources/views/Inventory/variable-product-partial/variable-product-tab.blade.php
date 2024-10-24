     <div class="card m-t-2">
          <div class="card-header">
              <h5 class="card-header-text"> Create Variable Products</h5>
          </div>
          <div class="card-block">
             <form method="POST" class="form-horizontal" enctype="multipart/form-data" action="{{route('storeVariableProduct')}}">
                @csrf
                <input type="hidden" name="finishgood" id="m_finishgood" value="{{ $generalItem[0]->id }}">
                <input type="hidden" name="itemName"   value="{{ $generalItem[0]->product_name }}">

        <div class="row">
            <div class="col-md-5">
                <div  id="positemcode" class="form-group {{ $errors->has('item_code') ? 'has-danger' : '' }} ">
                    <label class="form-control-label"><i class="icofont icofont-barcode"></i> Item Code <span class="text-danger">*</span></label>
                    <a href="javascript:void(0)" class="f-right text-primary" data-toggle="tooltip" data-placement="top" title="Auto Code Generate"
                    onclick="autoCodeGenerate({{ $generalItem[0]->id }},'item_code')" >Auto Generate</a>
                    <input type="text" name="item_code" id="item_code" class="form-control"  value="{{ old('item_code') }}" />
                    @if ($errors->has('item_code'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-control-label">Attribute<span class="text-danger">*</span></label>

                    <i id="btn_attr_create" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Attribute" ></i>
                    <label class="switch f-right m-r-1">
                        <input type="checkbox" title="" name="attribute_mode" data-toggle="tooltip" data-placement="top" data-original-title="You want attribute name show on website">
                        <span class="slider round"></span>

                      </label>
                    <select class="form-control select2 @error('attribute') 'has-danger' @enderror" placeholder="Attribute" name="attribute" id="attribute">
                        <option value="">Select</option>
                              @foreach($attributes as $val)
                                  <option value="{{$val->id}}">{{$val->name}}</option>
                              @endforeach
                    </select>
                    @error('attribute')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group {{ $errors->has('item_name') || $errors->has('item_name') ? 'has-danger' : '' }} ">
                    <label class="form-control-label">Item Name <span class="text-danger">*</span></label>
                    <input type="text" name="item_name" id="item_name" class="form-control"  value="{{ old('item_name') }}" />
                    @if ($errors->has('item_name'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif

                    @if($errors->has('variable_product_name_error'))
                        <div class="form-control-feedback">{{ $errors->get('variable_product_name_error') }}</div>
                    @endif

                </div>

                <div class="form-group {{ $errors->has('item_price') ? 'has-danger' : '' }} ">
                    <label class="form-control-label">Item Price <span class="text-danger">*</span></label>
                    <input type="text" name="item_price" id="item_price" class="form-control"  value="{{ old('item_price') }}" />
                    @if($errors->has('item_price'))
                        <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
                </div>

              <div class="form-group {{ $errors->has('uom') ? 'has-danger' : '' }}">
                  <label class="form-control-label">Unit Measure<span class="text-danger m-l-5">*</span></label>
                   <i id="btn_uom" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add UOM" ></i>
                    <select class="form-control  select2" data-placeholder="Select Unit Measure" id="uom" name="uom">
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
                    @if ($errors->has('uom'))
                      <div class="form-control-feedback">Required field can not be blank.</div>
                    @endif
              </div>

                <div class="form-group">
                    <label>Priority</label>
                    <input type="number" class="form-control @error('item_priority') 'has-danger' @enderror" placeholder="Priority" name="item_priority" id="priority" value="{{ old('item_priority') }}">
                    @error('item_priority')
                      <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="col-md-6">
               <label for="productImage" class="form-control-label">Product Image</label>
             <a href="#">
                        <img id="productImages_preview" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
                        </a>

                    <div class="form-group {{ $errors->has('productImage') ? 'has-danger' : '' }} m-t-10">


                                    <label for="productImage" class="custom-file">
                                                <input type="file" name="productImage" id="productImage" class="custom-file-input" multiple>
                                                <span class="custom-file-control"></span>
                                            </label>
                                @if ($errors->has('productImage'))
                                    <div class="form-control-feedback">{{ $errors->first('productImage') }}</div>
                                @endif
                              </div>

              </div>
		 </div>
		 <button type="submit" class="btn btn-success f-right m-t-2 btn-lg">Create</button>
		</form>

         </div>
      </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Variable Products List</h5>
            </div>
            <div class="card-block">
                {{-- $variationProductCount --}}
                <!--<div class="rkmd-checkbox checkbox-rotate">-->
                <!--    <label class="input-checkbox checkbox-primary">-->
                <!--        <input type="checkbox" id="chkactive" class="mainchk">-->
                <!--        <span class="checkbox"></span>-->
                <!--    </label>-->
                <!--    <div class="captions">Show In-Active POS Products</div>-->
                <!--</div>-->
                <!--<br/>-->
                <!--<br/>-->
                <table id="tblposproducts" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th class="d-none">#</th>
                        <th>Image</th>
                        <th>Item Code | Name</th>
                        <th>Attribute</th>
                        <th>Variations</th>
                        <th>UOM</th>
                        <th>Ref. Product</th>
                        <th>Retail Price</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($posProduct_details as $value)
                        <tr id="tr-{{ $value->pos_item_id }}">
                            <td class="d-none">{{ $value->priority }}</td>
                            <td class="text-center">
                              <a href="{{ asset('storage/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" data-fancybox data-caption="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                <img width="64" height="64" src="{{ asset('storage/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}"/>
                              </a>
                            </td>
                            <td>{{$value->item_code}} | {{$value->item_name}} </td>
                            <td >{{ $value->attribute }}</td>
                            <td id="cell-3-{{ $value->pos_item_id }}">

                                  @foreach($inventoryVariations as $variation)
                                     @if($variation->product_id == $value->pos_item_id)
                                           <label class="badge badge-bg-success badge-lg pointer" id="lable-variation-{{ $variation->id }}" onclick="editVariationValue({{ $variation->id }},{{ $variation->variation_id }},{{ $variation->product_id }},'{{ $variation->item_name}}','{{ $variation->name}}','{{ $variation->type}}','{{ $variation->addon_limit}}')"> {{ $variation->name }} </label>
                                         @foreach($variationProductCount as $count)
                                              @if($count->addon_category_id == $variation->variation_id)
                                               <span class="badge badge-black badge-header3">{{ $count->countProduct }}</span>
                                              @endif
                                         @endforeach
                                         <br/>
                                     @endif
                                  @endforeach
                            </td>
                            <td >{{$value->uomname}}</td>
                            <td >{{$value->product_name}}</td>
                            <td >{{$value->online_price}}</td>
                            <td >{{$value->priority}}</td>
                            <td class="action-icon">

                                <i class="icofont icofont-plus text-success pointer m-r-1 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Variation" onclick="createVariation({{ $value->pos_item_id }},'{{ $value->item_name }}')"></i>

                                <a  class="m-r-1" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" onclick="edit({{$value->pos_item_id}},{{$value->product_id}},'{{$value->item_code}}','{{$value->item_name}}','{{$value->online_price}}','{{$value->uom_id}}','{{ asset('storage/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}','{{$value->image}}',{{$value->priority}},{{ $value->attribute_id }},{{ $value->is_hidden_attribute }})" ></i> </a>

                                <i class="icofont icofont-ui-delete text-danger m-r-1 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onclick="remove('{{$value->item_name}}','{{$value->pos_item_id}}')"></i>

                                <i class="icofont icofont-share-alt text-primary f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Copy Variable Product" onclick="copyVariableProduct_modal({{$value->pos_item_id}},'{{$value->item_name}}',{{$value->product_id}})"></i>
                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>

 <div class="modal fade modal-flex" id="create-attribute-modal" tabindex="-1" role="dialog">
               <div class="modal-dialog modal-md" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                        <h4 class="modal-title">Create Attribute</h4>
                     </div>
                     <div class="modal-body">
                       <div class="row">
                             <div class="col-md-12">
                              <div class="form-group">
                                <label class="form-control-label">Attribute Name:</label>
                                 <input type="text"  name="attribute_txt" id="attribute_txt" class="form-control" placeholder="Attribute Name"/>
                                </div>
                              </div>
                          </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-success waves-effect waves-light" onClick="add_attribute()">Create</button>
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


<div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Variable Product</h4>
                </div>
				<form id="update-variableProductForm"  method="POST" enctype="multipart/form-data">
				@csrf
                <div class="modal-body">
                            <div class="form-group">
                                <label class="form-control-label">Item Code <span class="text-danger">*</span></label>
                                <a href="javascript:void(0)" class="f-right text-primary" data-toggle="tooltip" data-placement="top" title="Auto Code Generate"
                                onclick="autoCodeGenerate({{ $generalItem[0]->id }},'item_code_vpmd')" >Auto Generate</a>
                                <input type="text" name="item_code" id="item_code_vpmd" class="form-control"  />
                                <span id="item_code_alert" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Attribute<span class="text-danger">*</span></label>

                                <i id="btn_attr_create_vpmd" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Attribute" ></i>
                                <label class="switch f-right m-r-1">
                                    <input type="checkbox" title="" name="attribute_mode" id="attribute_mode_vpmd" data-toggle="tooltip" data-placement="top" data-original-title="You want attribute name show on website">
                                    <span class="slider round"></span>

                                  </label>
                                <select class="form-control select2" placeholder="Attribute" name="attribute" id="attribute_vpmd">
                                    <option value="">Select</option>
                                          @foreach($attributes as $val)
                                              <option value="{{$val->id}}">{{$val->name}}</option>
                                          @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="item_name" id="item_name_vpmd" class="form-control"  />
                                <input type="hidden" name="finishgood" id="finishgood_vpmd" class="form-control"  />
                                <input type="hidden" name="item_id" id="item_id_vpmd" class="form-control"  />
                                <input type="hidden" name="prevImageName" id="prevImageName_vpmd" class="form-control" />
                                <span id="item_name_alert_vpmd" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Select Unit Of measure <span class="text-danger">*</span></label>
                                <select name="uom" id="uom_vpmd" data-placeholder="Select unit of measure" class="form-control select2"  >
                                    <option value="">Select Unit Of measure</option>
                                    @if($uom)
                                        @foreach($uom as $uom)
                                            <option value="{{ $uom->uom_id }}">{{ $uom->name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Price <span class="text-danger">*</span></label>
                                <input type="number" min="0" name="price" id="price_vpmd" class="form-control"  />
                                <span id="price_alert_vpmd" class="text-danger"></span>
                            </div>

                            <div class="form-group">
                                <label class="form-control-label">Priority:</label>
                                <input type="number" min="0" name="priority" id="priority_vpmd" class="form-control"  />
                                <span id="priority_alert_vpmd" class="text-danger"></span>
                            </div>

							<a id="modal_previewImageFancy_vpmd" href="{{ asset('storage/images/placeholder.jpg') }}" data-fancybox data-caption="placeholder.jpg">
								<img id="modal_previewImage_vpmd" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
							</a>
							<div class="form-group{{ $errors->has('updateproduct') ? 'has-danger' : '' }} ">
								<label for="item_image_vpmd" class="form-control-label">Product Image</label>
								<br/>
								<label for="item_image_vpmd" class="custom-file">
									<input type="file" name="item_image" id="item_image_vpmd" class="custom-file-input">
									<span class="custom-file-control"></span>
								</label>
								<span class="text-danger" id="item_image_vpmd_alert"></span>
					        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="btn_variableProduct_update">
                        <i class="icofont icofont-edit-alt"> </i>
                        Update</button>
                </div>
				</form>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="copy-variable-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Copy Variable </h4>
                </div>
				<form id="copy_variationForm" method="POST">
				    @csrf
    		        <input type="hidden" name="variable_id" id="variableId_cpymd">
    		        <input type="hidden" id="variableName_cpymd">
    		        <input type="hidden" id="generalInventoryId_cpymd">
                 <div class="modal-body">
                    <h3 id="copy-variableName"></h3>

                    <div class="row">
                    <div class="col-md-6">
                     <div class="form-group">
                               <label>Department</label>
                               <select class="select2" data-placeholder="Select Depatrment" name="department_variableTab" id="department_variableTab">
                                   <option value="">Select</option>
                                   @foreach($department as $val)
                                      <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                   @endforeach
                               </select>
                               <span class="text-danger" id="department_variableTab_alert"></span>
                            </div>
                     </div>
                     <div class="col-md-6">
                         <div class="form-group">
                           <label>Sub-Department</label>
                           <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_variableTab" disabled></select>
                           <span class="text-danger" id="subDepartment_variableTab_alert"></span>
                        </div>
                    </div>
                  </div>

                    <table class="table" id="tbl_variablecpymd" width="100%">
                        <thead>
                            <tr>
                              <th>Item Name</th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btn_past_variableTogeneral">Past</button>
                </div>
				</form>
            </div>
        </div>
    </div>


    <div class="modal fade modal-flex" id="copy-variation-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"  aria-label="Close" onclick="close_copyModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Copy Variation</h4>
                </div>
				<form id="copy_variationForm" method="POST">
				    @csrf
    		        <input type="hidden" name="item_name" id="itemName_cpymd">
    		        <input type="hidden" name="item_id" id="itemId_cpymd">
    		        <input type="hidden" name="mode" id="mode_cpymd">
    		        <input type="hidden" name="variation_id" id="variationId_cpymd">
    		        <input type="hidden" id="variationName_cpymd">
    		        <input type="hidden" id="variationType_cpymd">
    		        <input type="hidden" id="selection_limited_cpymd">

                 <div class="modal-body">
                    <h3 id="copy-variationName"></h3>

                    <table id="tbl_cpymd" class="table" width="100%">
                        <thead>
                            <tr>
                              <th>Item Name</th>
                           </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" id="btn_past_variation">Past</button>
                </div>
				</form>
            </div>
        </div>
    </div>


<div class="modal fade modal-flex" id="createVariation-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-title-variation">Create Variation</h4>
                </div>
				<form id="variationForm" method="POST">
				    @csrf
    		        <input type="hidden" name="item_name" id="itemName_md">
    		        <input type="hidden" name="item_id" id="itemId_md">
    		        <input type="hidden" name="mode" id="mode_md">
    		        <input type="hidden" name="variation_id" id="variationId_md">

                  <div class="modal-body">

                    <div id="createVariationModal_alert"></div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Variation Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Variation Name" name="variation_name" id="variation_name">
                                        <span id="variation_name_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <label>Variation Type <span class="text-danger">*</span></label>
                                       <select class="select2" data-placeholder="Select Type" name="variation_type" id="variation_type">
                                           <option value="">Select</option>
                                           <option value="single">Single</option>
                                           <option value="multiple">Multiple</option>
                                       </select>
                                    </div>
                                    <span id="variation_type_alert" class="text-danger"></span>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <label>Selection Limited</label>
                                       <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="selection_limited" id="selection_limited" disabled>
                                       <span id="selection_limited_alert" class="text-danger"></span>
                                    </div>
                                </div>

                            </div>

                            <table class="table" id="table_variationLists_md">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-group">
                                               <label>Department</label>
                                               <select class="select2" data-placeholder="Select Depatrment" id="department_md">
                                                   <option value="">Select</option>
                                                   @foreach($department as $val)
                                                      <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                                   @endforeach
                                               </select>
                                               <span id="department_md_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Sub-Department</label>
                                               <select class="select2" data-placeholder="Select Sub-Depatrment" id="subDepartment_md"></select>
                                               <span id="subDepartment_md_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Products</label>
                                               <select class="select2" data-placeholder="Select Products" id="product_md" disabled>
                                                   <option value="">Select</option>
                                               </select>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Price</label>
                                               <input type="text" id="price_md" class="form-control">
                                               <span id="price_md_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                           <div class="form-group">
                                               <button type="button" onclick="modal_add_variation()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Variation" class="btn btn-default">
                                            <i class="icofont icofont-plus text-success pointer f-18"></i> </button>
                                           </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                </div>
                <div class="modal-footer">
                    <div class="f-left">
                        <button type="button" class="btn btn-info waves-effect waves-light m-r-2 d-none" id="btn_copy_variation">Copy Variation</button>
                    </div>
                    <div class="f-right">
                    <button type="button" class="btn btn-danger waves-effect waves-light m-r-2 d-none" id="btn_remove_variation">Remove</button>

                    <button type="button" class="btn btn-primary waves-effect waves-light" id="btn_submit_variation">Submit</button>
                    </div>
                </div>
				</form>
            </div>
        </div>
    </div>
