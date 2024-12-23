  <div class="card m-t-2">
          <div class="card-header">
              <h5 class="card-header-text"> Create Addon</h5>
          </div>
          <div class="card-block">
             <form method="POST" class="form-horizontal" id="createAddonTabForm">
                @csrf
                <input type="hidden" name="finishgood"    value="{{ $generalItem[0]->id }}">
                <input type="hidden" name="itemName"      value="{{ $generalItem[0]->product_name }}">
                <input type="hidden" name="addonTabBox"   value="active">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><strong>Addon Name</strong></label>
                            <input type="text" class="form-control" placeholder="Addon Name" name="addon_name" id="addon_name_addonTab">
                            <span class="text-danger" id="addon_name_addonTab_alert"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                           <label><strong>Addon Type</strong></label>
                           <select class="select2" data-placeholder="Select Type" name="addon_type" id="addon_type_addonTab">
                               <option value="">Select</option>
                               <option {{ old('addon_type') == 'single' ? 'selected' : '' }} value="single">Single</option>
                               <option {{ old('addon_type') == 'multiple' ? 'selected' : '' }} value="multiple">Multiple</option>
                           </select>
                        </div>
                            <span class="text-danger" id="addon_type_addonTab_alert"></span>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                           <label><strong>Selection Limit</strong></label>
                           <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limit" name="selection_limit" id="selection_limit_addonTab" disabled>
                            <span class="text-danger" id="selection_limit_addonTab_alert"></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                          <label><strong>Priority</strong></label>
                          <input type="number" min="0" class="form-control" placeholder="Priority" name="priority" id="priority_addonTab">
                            <span class="text-danger" id="priority_addonTab_alert"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           <label><strong>Is Required</strong></label>
                            <br/>
                            <label>
                             <input id="is_required_addonTab" name="is_required" type="checkbox" data-toggle="toggle">
                            </label>
                          <span class="text-danger" id="is_required_addonTab_alert"></span>
                        </div>
                    </div>
                </div>

                            <table class="table" id="table_generatList_addonTab" style="width:80%;">
                                <thead>
                                  <tr>
                                        <th>
                                            <div class="form-group">
                                               <label>Department</label>
                                               <select class="select2" data-placeholder="Select Depatrment" name="department_addonTab" id="department_addonTab">
                                                   <option value="">Select</option>
                                                   @foreach($department as $val)
                                                      <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                                   @endforeach
                                               </select>
                                               <span class="text-danger" id="department_addonTab_alert"></span>
                                            </div>
                                        </th>
                                        <th>
                                         <div class="form-group">
                                           <label>Sub-Department</label>
                                           <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_addonTab" disabled></select>
                                           <span class="text-danger" id="subDepartment_addonTab_alert"></span>
                                        </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Products</label>
                                               <select class="select2" data-placeholder="Select Products" name="product_addonTab" id="product_addonTab" disabled>
                                                   <option value="">Select</option>
                                               </select>
                                               <span class="text-danger" id="product_addonTab_alert"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Price</label>
                                               <input type="text" id="price_addonTab" class="form-control">
                                               <span id="price_addon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                           <div class="form-group">
                                               <button type="button" onclick="add_addon_addonTab()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Addon" class="btn btn-default">
                                            <i class="icofont icofont-plus text-success pointer f-18"></i> </button>
                                           </div>
                                        </th>
                                    </tr>

                                </thead>
                                <tbody></tbody>
                            </table>
		   <button type="button" class="btn btn-success f-right btn-lg" id="btn_save_addon">Create</button>
		</form>

         </div>
      </div>

 <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Lists</h5>
            </div>
            <div class="card-block">
                <!--<div class="rkmd-checkbox checkbox-rotate">-->
                <!--    <label class="input-checkbox checkbox-primary">-->
                <!--        <input type="checkbox" id="chkactive" class="mainchk">-->
                <!--        <span class="checkbox"></span>-->
                <!--    </label>-->
                <!--    <div class="captions">Show In-Active POS Products</div>-->
                <!--</div>-->
                <!--<br/>-->
                <!--<br/>-->
                <table class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0" id="addonList_tab">
                    <thead>
                    <tr>
                        <th class="d-none">#</th>
                        <th>Name</th>
                        <th>Values</th>
                        <th>Is Required</th>
                        <th>Type</th>
                        <th>Selection limit</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($addonCategories as $head_val)
                        <tr id="tr-addonTab-table-{{ $head_val->id }}">
                            <td class="d-none">
                                {{ $head_val->priority }}
                            </td>
                            <td>{{ $head_val->name }} </td>
                            <td id="cell-3-addonTab-{{ $head_val->id }}">
                                @foreach($head_val->addons as $child_val)
                                  <label class="badge badge-bg-success badge-lg">{{ $child_val->name.' - Rs.'.$child_val->price }}</label> <br/>
                                @endforeach
                            </td>
                            <td>{{$head_val->is_required == 1 ? 'Yes' : 'No'}}</td>
                            <td>{{$head_val->type}}</td>
                            <td>{{$head_val->addon_limit}}</td>
                            <td>{{$head_val->priority }}</td>
                            <td class="action-icon">

                                <a  class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" onclick="editAddon({{ $head_val->id }},'{{ $head_val->name }}','{{ $head_val->type }}',{{ $head_val->is_required }},{{ $head_val->addon_limit }},{{ $head_val->priority }})"></i></a>

                                <i class="icofont icofont-ui-delete text-danger f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"
                                onclick="remove_addon({{ $head_val->id }},'{{ $head_val->name }}',{{ $generalItem[0]->id }})"></i>

                                <a href="javascript:void(0)" class="text-primary" data-toggle="tooltip"
                                data-placement="top" title="" data-original-title="Copy addon product"
                                onclick="copyAddonProduct_modal({{ $head_val->id }},'{{ $head_val->name }}')">
                                    <i class="icofont icofont-share-alt f-18"></i>
                                </a>
                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>

	<div class="modal fade modal-flex" id="editAddon-modal" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" id="btn_editAddon_md_close"  aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
							<h4 id="editModel-title-addon" class="modal-title"></h4>
						</div>
						<div class="modal-body">
                               <h3 class="card-header-text" style="text-transform:none;">Edit Addon</h3>
                               <div class="f-right">
                                 <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Save Addon" id="btn_updateAddon">
                                   <i class="icofont icofont-save pointer f-18 m-r-1"></i> Save Changes
                                 </button>
                              </div>
                          <div class="row m-t-3">
                            <div class="col-md-12">

                            <form id="editAddon_Form" method="post">
						        @csrf
						        <input type="hidden" name="finishgood" id="inventory_id_editmdAddon">
						        <input type="hidden" name="productName" id="inventory_name_editmdAddon">
						        <input type="hidden" name="addonheadId" id="addonheadId_editmdAddon">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Addon Name</label>
                                        <input type="text" class="form-control" placeholder="Addon Name" name="addon_name" id="addon_name_editmdAddon">
                                        <span id="addon_name_editmdAddon_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                       <label>Addon Type</label>
                                       <select class="select2" data-placeholder="Select Type" name="addon_type" id="addon_type_editmdAddon">
                                           <option value="">Select</option>
                                           <option value="single">Single</option>
                                           <option value="multiple">Multiple</option>
                                       </select>
                                    </div>
                                    <span id="addon_type_editmdAddon_alert" class="text-danger"></span>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                       <label>Selection Limited</label>
                                       <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="selection_limit" id="selection_limit_editmdAddon" disabled>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                       <label>Priority</label>
                                       <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="priority" id="priority_editmdAddon">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                       <label>Is Required</label>
                                       <br/>
                                     <label>
                                        <input id="is_required_editmdAddon" name="is_required" type="checkbox" data-toggle="toggle" data-size="lg">
                                      </label>
                                      <span class="text-danger" id="is_required_editmdAddon_alert"></span>
                                    </div>
                                </div>
                            </div>
                            <table class="table" id="table_addonGeneratList_editmd">
                                <thead>
                                     <tr>
                                        <th>
                                            <div class="form-group">
                                               <label>Department</label>
                                               <select class="select2" data-placeholder="Select Depatrment" id="department_editmdAddon">
                                                   <option value="">Select</option>
                                                   @foreach($department as $val)
                                                      <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                                   @endforeach
                                               </select>
                                               <span id="department_editmdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Sub-Department</label>
                                               <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_editmdAddon"></select>
                                               <span id="subDepartment_editmdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Products</label>
                                               <select class="select2" data-placeholder="Select Products" id="product_editmdAddon" disabled>
                                                   <option value="">Select</option>
                                               </select>
                                               <span id="product_editmdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Price</label>
                                               <input type="text" id="price_editmdAddon" class="form-control">
                                               <span id="price_editmdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                           <div class="form-group">
                                               <button type="button" onclick="modal_add_editaddon_tmp()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Addon" class="btn btn-default">
                                            <i class="icofont icofont-plus text-success pointer f-18"></i> </button>
                                           </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                          </form>
                          </div>
						</div>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal" >Close</button>
						</div>
					</div>
				</div>
			</div>


            <div class="modal fade modal-flex" id="copy-addon-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" onclick="clearSelectedProduct_bindAddon()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Copy Addon Product: <span id="cpyAddonName_head"></span> </h4>
                        </div>
                        <form id="copy_addonForm" method="POST">
                            @csrf
                            <input type="hidden" name="addonId_cpymd" id="addonId_cpymd">
                            <input type="hidden" id="addonName_cpymd">
                         <div class="modal-body">
                            <h3>General Product Lists</h3>

                            <div class="row">
                            <div class="col-md-6">
                             <div class="form-group">
                                       <label>Department</label>
                                       <select class="select2" data-placeholder="Select Depatrment" id="department_cpymd">
                                           <option value="">Select</option>
                                           @foreach($department as $val)
                                              <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                           @endforeach
                                       </select>
                                       <span class="text-danger" id="department_cpymd_alert"></span>
                                    </div>
                             </div>
                             <div class="col-md-6">
                                 <div class="form-group">
                                   <label>Sub-Department</label>
                                   <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_cpymd" disabled></select>
                                   <span class="text-danger" id="subDepartment_cpymd_alert"></span>
                                </div>
                            </div>
                          </div>

                            <table class="table" id="tbl_productListcpyaddonmd" width="100%">
                                <thead>
                                    <tr>
                                      <th>
                                        <div class="form-check">
                                            <label class="form-check-label f-18">
                                                <input class="form-check-input" type="checkbox"
                                                 name="tble_chk_allprodcpyaddonmd"> Select all Item
                                            </label>
                                         </div>
                                      </th>
                                   </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect waves-light m-r-15" onclick="clearSelectedProduct_bindAddon()">Close</button>
                            <button type="button" class="btn btn-success waves-effect waves-light" onclick="selectedProduct_bindAddon()">Past</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
