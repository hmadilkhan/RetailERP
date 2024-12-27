@extends('layouts.master-layout')

@section('title','Deal Products')

@section('breadcrumtitle','Create Deal')

@section('navinventory','active')

@section('content')



<section class="panels-wells p-t-20">
    <h3>Product Name :{{ $generalItem[0]->product_name }}</h3>

    <a href="{{ route('invent-list') }}">
        <i class="text-primary text-center icofont icofont-arrow-left f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to list">Back to list</i>
    </a>

     <div class="card m-t-2">
          <div class="card-header">
              <h5 class="card-header-text"> Create Deal Products</h5>
          </div>
          <div class="card-block">
             <form method="POST" class="form-horizontal" id="dealCreateForm">
                @csrf
                <input type="hidden" name="finishgood" value="{{ $generalItem[0]->id }}">
                <input type="hidden" name="itemName"   value="{{ $generalItem[0]->product_name }}">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Group Name</label>
                            <input type="text" class="form-control @error('group_name') 'has-danger' @enderror" placeholder="Group Name" name="group_name" id="group_name" value="{{ old('group_name') }}">
                            <span class="text-danger" id="group_name_alert"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           <label>Group Type</label>
                           <select class="select2" data-placeholder="Select Type" name="group_type" id="group_type">
                               <option value="">Select</option>
                               <option {{ old('group_type') == 'single' ? 'selected' : '' }} value="single">Single</option>
                               <option {{ old('group_type') == 'multiple' ? 'selected' : '' }} value="multiple">Multiple</option>
                           </select>
                        </div>
                        <span class="text-danger" id="group_type_alert"></span>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Selection Limited</label>
                            <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limit" name="selection_limit" id="selection_limit" disabled>
                            <span class="text-danger" id="selection_limit_alert"></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Priority</label>
                            <input type="number" min="0" value="0" class="form-control" placeholder="Priority" name="priority" id="priority">
                            <span class="text-danger" id="priority_alert"></span>
                        </div>
                    </div>
                </div>

            <div class="row">
                <table class="table table-striped" id="table_dealCrearte" style="width:60%">
                    <thead>
                    <tr>
                        <th>
                         <div class="form-group">
                           <label>Department</label>
                           <select class="select2" data-placeholder="Select Depatrment" id="department_deal">
                               <option value="">Select</option>
                               @foreach($department as $val)
                                  <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                               @endforeach
                           </select>
                           <span class="text-danger" id="department_deal_alert"></span>
                        </div>
                        </th>
                        <th>
                         <div class="form-group">
                           <label>Sub-Department</label>
                           <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_deal" disabled></select>
                           <span class="text-danger" id="subDepartment_deal_alert"></span>
                        </div>
                        </th>
                        <th>
                         <div class="form-group">
                           <label>Products</label>
                           <select class="select2" data-placeholder="Select Products" id="product_deal" disabled>
                               <option value="">Select</option>
                           </select>
                           <span class="text-danger" id="product_deal_alert"></span>
                         </div>
                        </th>
                        <th>
                        <div class="form-group">
                           <label>Quantity</label>
                           <input type="number" name="qty_deal" id="qty_deal" class="form-control" placeholder="Quantity">
                        </div>
                    </th>
                    <th>
                        <div class="form-group">
                          <button type="button" onclick="add_dealProduct_tmp()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Product" class="btn btn-default">
                             <i class="icofont icofont-plus text-success pointer f-18 m-t-0"></i> </button>
                        </div>
                    </th>
                  </tr>
                  </thead>
                  <tbody></tbody>
                </table>
            </div>
		   <button type="button" class="btn btn-success f-right" id="btn_storeDeal">Save</button>
		</form>

         </div>
      </div>


        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Deal Products Lists</h5>
            </div>
            <div class="card-block">
                <table id="table_deal" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th class="d-none">#</th>
                        <th>Group Name</th>
                        <th>Products</th>
                        <th>Group Type</th>
                        <th>Selection Limit</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($dealHead as $head_val)
                         <tr id="row-{{ $head_val->id }}">
                             <td class="d-none">{{ $head_val->priority }}</td>
                             <td>
                                 {{ $head_val->name }}
                             </td>
                             <td id="deal-cell-2-{{ $head_val->id }}">
                                 @foreach($dealChild as $chil_val)
                                   @if($chil_val->inventory_general_id == $head_val->id)
                                       @php $showNewLine = '' @endphp
                                     <label class="badge badge-bg-success badge-lg pointer" onclick="addon_list({{ $head_val->id }},{{ $chil_val->id }},'{{ $chil_val->product_name }}')">
                                       {{ $chil_val->product_name }}
                                       @foreach($dealprodAddons as $value)
                                           @if($value->product_id == $chil_val->id)
                                               @php $showNewLine = '<br/><br/>' @endphp
                                              <span class="badge badge-black badge-header2"> Qty {{ $chil_val->product_quantity }}</span>
                                              <span class="badge badge-black badge-header">Addons {{ $value->counts }}</span>
                                           @endif
                                       @endforeach
                                     </label><br/><br/>
                                        <?php //print $showNewLine ?>
                                   @endif
                                 @endforeach
                             </td>
                             <td>{{ $head_val->type }}</td>
                             <td>{{ $head_val->selection_limit }}</td>
                             <td>{{ $head_val->priority }}</td>
                             <td>
                                <a  class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="editDeal({{ $head_val->id }},{{ $head_val->inventory_deal_id }},'{{ $head_val->name }}','{{ $head_val->type }}',{{ $head_val->selection_limit }},{{ $head_val->priority }})"><i class="icofont icofont-ui-edit text-primary f-18"></i> </a>

                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onclick="removeDeal({{ $head_val->id }},{{ $head_val->inventory_deal_id }},'{{ $head_val->name }}')"><i class="icofont icofont-ui-delete text-danger f-18"></i></a>
                             </td>
                         </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>

			<div class="modal fade modal-flex" id="editDeal-modal" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
							<h4 id="mod-title" class="modal-title">Edit Deal</h4>
						</div>
						<div class="modal-body">
                               <h3 class="card-header-text" style="text-transform:none;"></h3>
                               <div class="f-right">
                                 <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Save Changes" id="btn_updateDeal">
                                   <i class="icofont icofont-save pointer f-18 m-r-1"></i> Save
                                 </button>
                              </div>

						    <form id="editDealForm" method="post">
						        @csrf
						        <input type="hidden" name="finishgood" id="inventory_deal_id_editmd">
						        <!--<input type="hidden" name="group_id" id="group_id_editmd">-->
						        <input type="hidden" name="id_editmd" id="unqId_editmd">

                            <div class="row m-t-1" id="rowBox_editmd_deal">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Group Name</label>
                                        <input type="text" class="form-control" placeholder="Group Name" name="group_name" id="group_name_editmd">
                                        <span id="group_name_editmd_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                       <label>Group Type</label>
                                       <select class="select2" data-placeholder="Select Type" name="group_type" id="group_type_editmd">
                                           <option value="">Select</option>
                                           <option value="single">Single</option>
                                           <option value="multiple">Multiple</option>
                                       </select>
                                    </div>
                                    <span id="group_type_editmd_alert" class="text-danger"></span>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                       <label>Selection Limit</label>
                                       <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limit" name="selection_limit" id="selection_limit_editmd" disabled>
                                       <span id="selection_limit_editmd_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Priority</label>
                                        <input type="number" min="0" value="0" class="form-control" placeholder="Priority" name="priority" id="priority_editmd">
                                        <span class="text-danger" id="priority_editmd_alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                <table class="table table-striped" id="table_deal_editmd" >
                    <thead>
                    <tr>
                <!--<div class="col-md-4">-->
                    <th>
                     <div class="form-group">
                       <label>Department</label>
                       <select class="select2" data-placeholder="Select Depatrment" id="department_editmd">
                           <option value="">Select</option>
                           @foreach($department as $val)
                              <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                           @endforeach
                       </select>
                       <span class="text-danger" id="department_editmd_alert"></span>
                    </div>
                    </th>
                    <th>
                     <div class="form-group">
                       <label>Sub-Department</label>
                       <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_editmd" disabled></select>
                       <span class="text-danger" id="subDepartment_editmd_alert"></span>
                    </div>
                    </th>
                    <th>
                    <div class="form-group">
                       <label>Products</label>
                       <select class="select2" data-placeholder="Select Products" id="product_editmd" disabled>
                           <option value="">Select</option>
                       </select>
                       <span class="text-danger" id="product_editmd_alert"></span>
                    </div>
                    </th>
                <th>
                    <div class="form-group">
                       <label>Quantity</label>
                       <input type="number" name="qty_deal" id="qty_editmd" class="form-control" placeholder="Quantity">
                    </div>
                </th>
                <th>
                    <div class="form-group">
                      <button type="button" onclick="add_dealProduct_tmp_editmd()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Product" class="btn btn-default">
                         <i class="icofont icofont-plus text-success pointer f-18 m-t-0"></i> </button>
                    </div>
                </th>
                <!--</div>-->
                  </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
                         </div>
                            </div>
                          </form>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>



	<div class="modal fade modal-flex" id="addonList-modal" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
							<h4 id="mod-title-addon" class="modal-title"></h4>
						</div>
						<div class="modal-body">
                               <h3 class="card-header-text" style="text-transform:none;">Create Addon</h3>
                               <div class="f-right">
                                 <button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Save Addon" id="btn_storeAddon">
                                   <i class="icofont icofont-save pointer f-18 m-r-1"></i> Save
                                 </button>
                              </div>
                          <div class="row">
                            <div class="col-md-12">

                            <form id="storeAddon_dealProductForm" method="post">
						        @csrf
						        <input type="hidden" name="finishgood" id="inventory_id_mdAddon">
						        <input type="hidden" name="productName" id="inventory_name_mdAddon">
						        <input type="hidden" id="dealGenHeadId_mdAddon">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Addon Name</label>
                                        <input type="text" class="form-control" placeholder="Addon Name" name="addon_name" id="addon_name">
                                        <span id="addon_name_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Show Website Name</label>
                                        <input type="text" class="form-control" placeholder="Show webiste name" name="showebsite_name" id="showebsite_name">
                                        <span id="showebsite_name_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <label>Is Required</label>
                                       <br/>
                                     <label>
                                        <input id="is_required" name="is_required" type="checkbox" data-toggle="toggle" data-size="lg">
                                      </label>
                                      <span class="text-danger" id="is_required_mdAddon_alert"></span>
                                    </div>
                                </div>
                              </div>
                                <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <label>Addon Type</label>
                                       <select class="select2" data-placeholder="Select Type" name="addon_type" id="addon_type">
                                           <option value="">Select</option>
                                           <option value="single">Single</option>
                                           <option value="multiple">Multiple</option>
                                       </select>
                                    </div>
                                    <span id="addon_type_alert" class="text-danger"></span>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <label>Selection Limited</label>
                                       <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="selection_limit" id="selection_limit_mdAddon" disabled>
                                       <span class="text-danger" id="selection_limit_mdAddon_alert"></span>
                                    </div>
                                </div>

                            </div>
                            <table class="table" id="table_addonGeneratList_md">
                                <thead>
                                     <tr>
                                        <th>
                                            <div class="form-group">
                                               <label>Department</label>
                                               <select class="select2" data-placeholder="Select Depatrment" id="department_mdAddon">
                                                   <option value="">Select</option>
                                                   @foreach($department as $val)
                                                      <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                                                   @endforeach
                                               </select>
                                               <span id="department_mdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                         <div class="form-group">
                                           <label>Sub-Department</label>
                                           <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_mdAddon" disabled></select>
                                           <span class="text-danger" id="subDepartment_mdAddon_alert"></span>
                                        </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Products</label>
                                               <select class="select2" data-placeholder="Select Products" id="product_mdAddon" disabled>
                                                   <option value="">Select</option>
                                               </select>
                                               <span id="product_mdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Price</label>
                                               <input type="text" id="price_mdAddon" class="form-control">
                                               <span id="price_mdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                           <div class="form-group">
                                               <button type="button" onclick="modal_add_addon_tmp()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Addon" class="btn btn-default">
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
						<hr/>
						  <div class="row m-t-10">
						   <div class="col-md-12">
						    <h3>Addon Lists</h3>
                            <table class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0" id="table_addon">
                                <thead>
                                <tr>
                                    <th class="d-none">#</th>
                                    <th>Name</th>
                                    <th>Products</th>
                                    <th>Type</th>
                                    <th>Selection Limited</th>
                                    <th>Is Required</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody id="addon_record"></tbody>
                            </table>
                           </div>
                          </div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>


	<div class="modal fade modal-flex" id="editAddon-modal" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" id="btn_editAddon_md_close"  aria-label="Close" onclick="addon_list($('#addonheadId_editmdAddon').val(),$('#inventory_id_editmdAddon').val(),$('#inventory_name_editmdAddon').val())">
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
                          <div class="row">
                            <div class="col-md-12">

                            <form id="editAddon_dealProductForm" method="post">
						        @csrf
						        <input type="hidden" name="finishgood" id="inventory_id_editmdAddon">
						        <input type="hidden" name="productName" id="inventory_name_editmdAddon">
						        <input type="hidden" name="addonheadId" id="addonheadId_editmdAddon">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Addon Name</label>
                                        <input type="text" class="form-control" placeholder="Addon Name" name="addon_name" id="addon_name_editmdAddon">
                                        <span id="addon_name_editmdAddon_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Show Website Name</label>
                                        <input type="text" class="form-control" placeholder="Show website name" name="showebsite_name" id="showebsite_name_editmdAddon">
                                        <span id="showebsite_name_editmdAddon_alert" class="text-danger"></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
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
                                <div class="row">
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                       <label>Selection Limited</label>
                                       <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="selection_limit" id="selection_limit_editmdAddon" disabled>
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
                                               <span id="department_mdAddon_alert" class="text-danger"></span>
                                            </div>
                                        </th>
                                        <th>
                                         <div class="form-group">
                                           <label>Sub-Department</label>
                                           <select class="select2" data-placeholder="Select Depatrment" id="subDepartment_editmdAddon" disabled></select>
                                           <span class="text-danger" id="subDepartment_editmdAddon_alert"></span>
                                        </div>
                                        </th>
                                        <th>
                                            <div class="form-group">
                                               <label>Products</label>
                                               <select class="select2" data-placeholder="Select Products" id="product_editmdAddon" disabled>
                                                   <option value="">Select</option>
                                               </select>
                                               <span id="product_mdAddon_alert" class="text-danger"></span>
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
                                               <button type="button" onclick="modal_add_editmdAddon_dataRow()" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Addon" class="btn btn-default">
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
							<button type="button" class="btn btn-default waves-effect waves-light" data-dismiss="modal" onclick="addon_list($('#addonheadId_editmdAddon').val(),$('#inventory_id_editmdAddon').val(),$('#inventory_name_editmdAddon').val())">Close</button>
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

       @if(old('group_type'))
             $("#group_type").trigger('change');
       @endif

   $(".select2").select2();

   let table_createDeal     = [];
   let table_editDeal       = [];
   let table_row_mdId       = [];
   let table_row_editmdId   = [];

       $('#table_deal').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: true,
            order: [[0, 'desc']],
            language: {
                search:'',
                searchPlaceholder: 'Search Product',
                lengthMenu: '<span></span> _MENU_'

            }

        });

       $('#table_addon').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Search Product',
                lengthMenu: '<span></span> _MENU_'

            }

        });

		function load_subdept(id,elementId){
            $.ajax({
                url: "{{ url('get_sub_departments') }}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:id},
                success:function(resp){
                    $('#'+elementId).empty();

    				 if($("#"+elementId).attr('disabled')){
    				     $("#"+elementId).attr('disabled',false);
    				 }

                    $('#'+elementId).append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $('#'+elementId).append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });
                }
            });
        }

		$("#department_deal").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_deal").val('change');
		        if(!$("#subDepartment_deal").attr('disabled')){
		            $("#subDepartment_deal").attr('disabled',true);
		        }
		    } else{
		       load_subdept($(this).val(),'subDepartment_deal');
		    }
		});

		$("#subDepartment_deal").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_deal").val('change');
		        if(!$("#product_deal").attr('disabled')){
		            $("#product_deal").attr('disabled',true);
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_deal',0);
		    }
		});

		$("#department_editmd").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_editmd").val('change');
		        if(!$("#subDepartment_editmd").attr('disabled')){
		            $("#subDepartment_editmd").attr('disabled',true);
		        }
		    } else{
		       load_subdept($(this).val(),'subDepartment_editmd');
		    }
		  //  productload_department_wise($(this).val(),'product_editmd',0);
		});

		$("#subDepartment_editmd").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_editmd").val('change');
		        if(!$("#product_editmd").attr('disabled')){
		            $("#product_editmd").attr('disabled',true);
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_editmd',0);
		    }
		});

		$("#department_mdAddon").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_mdAddon").val('change');
		        if(!$("#subDepartment_mdAddon").attr('disabled')){
		            $("#subDepartment_mdAddon").attr('disabled',true);
		        }
		    } else{
		       load_subdept($(this).val(),'subDepartment_mdAddon');
		    }
		    $("#department_mdAddon_alert").text('');
		});

		$("#subDepartment_mdAddon").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_mdAddon").val('change');
		        if(!$("#product_mdAddon").attr('disabled')){
		            $("#product_mdAddon").attr('disabled',true);
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_mdAddon',0);
		    }
		});

		$("#department_editmdAddon").on('change',function(){
		    if($(this).val() == ''){
		        $("#subDepartment_editmdAddon").val('change');
		        if(!$("#subDepartment_editmdAddon").attr('disabled')){
		            $("#subDepartment_editmdAddon").attr('disabled',true);
		        }
		    } else{
		       load_subdept($(this).val(),'subDepartment_editmdAddon');
		    }
		    $("#department_editmdAddon_alert").text('');
		});

		$("#subDepartment_editmdAddon").on('change',function(){
		    if($(this).val() == ''){
		        $("#product_editmdAddon").val('change');
		        if(!$("#product_editmdAddon").attr('disabled')){
		            $("#product_editmdAddon").attr('disabled',true);
		        }
		    } else{
		       productload_department_wise($(this).val(),'product_editmdAddon',0);
		    }
		});

		function loadDealProduct(dealGenHeadId){
			$.ajax({
			  url: "{{ route('reloadDealProduct') }}",
			  method : "POST",
			  data:{_token:'{{ csrf_token() }}',id:dealGenHeadId},
			  dataType:'json',
			  success: function(resp){
			    if(resp != null){
			        console.log(resp)
				 $("#deal-cell-2-"+dealGenHeadId).empty();
    			      $.each(resp.dealChild,function(i,v){
    			            $("#deal-cell-2-"+dealGenHeadId).append("<label class='badge badge-bg-success badge-lg pointer' onclick='addon_list("+dealGenHeadId+","+v.id+",\""+v.product_name+"\")'>"+
                                                  v.product_name+
                                                  "<span class='' id='child-cell-2"+v.id+"'></span></label><br/>");
    			      });

                      $.each(resp.dealprodAddons,function(index,addon_val){
                          $("#child-cell-2"+addon_val.product_id).text(addon_val.counts).addClass('badge badge-black badge-header');
                      });
			    }

			  }
			});
		}

		function productload_department_wise(departId,elementId,selectedMode){
		    if(departId == ''){
    				 $("#"+elementId).empty();

    				 if(!$("#"+elementId).attr('disabled')){
    				     $("#"+elementId).attr('disabled',true);
    				 }

		    }else{
    			$.ajax({
    			  url: "{{ route('invent-list-department') }}",
    			  method : "POST",
    			  data:{_token:'{{ csrf_token() }}',id:departId},
    			  success: function(resp){
    			    if(resp != null){
    				 $("#"+elementId).empty();

    				 if($("#"+elementId).attr('disabled')){
    				     $("#"+elementId).attr('disabled',false);
    				 }

    				   $("#"+elementId).append('<option value="">Select</option>');

        			   $.each(resp,function(i,v){
        			       $("#"+elementId).append('<option '+(selectedMode == 1 ? "selected" : "")+' value="'+v.id+'">'+v.product_name+'</option>');
        			   })
    			    }

    			  }
    			});
		    }
		}

		$("#group_type").on('change',function(){
		    if($(this).val() == 'multiple'){
		        if($('#selection_limit').attr('disabled')){
		            $('#selection_limit').attr('disabled',false);
		        }
		    }else{
		        if(!$('#selection_limit').attr('disabled')){
		            $('#selection_limit').attr('disabled',true);
		        }
		    }
		    $('#selection_limit').val('');
		});

		$("#group_type_editmd").on('change',function(){
		    if($(this).val() == 'multiple'){
		        if($('#selection_limit_editmd').attr('disabled')){
		            $('#selection_limit_editmd').attr('disabled',false);
		        }
		    }else{
		        if(!$('#selection_limit_editmd').attr('disabled')){
		            $('#selection_limit_editmd').attr('disabled',true);
		        }
		    }
		  //  $('#selection_limit_editmd').val('');
		});

    $("#group_name").on('change',function(){
        let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;

        if(!regex.test($(this).val())){
            $(this).focus();
            $("#group_name_alert").text('This field is required. Please note, special characters (such as @, #, $, %, &, ) are not allowed.!');
            swal('Error!','This field is required. Please note, special characters (such as @, #, $, %, &, ) are not allowed.!','error');
        }else{
            $("#group_name_alert").text('');
        }
    });

	$("#btn_storeDeal").on('click',function(){

		   let process = true;
           let regex = /^[a-zA-Z0-9\s\u0600-\u06FF\u0750-\u077F\-\(\)\.]+$/;

		    if(!regex.test($("#group_name").val())){
		        process = false;
		        $("#group_name").focus();
		        $("#group_name_alert").text('This field is required. Please note, special characters (such as @, #, $, %, &, ) are not allowed.!');
                swal('Error!','This field is required. Please note, special characters (such as @, #, $, %, &, ) are not allowed.!','error');
		    }

		    if($("#group_type").val() == ''){
		        process = false;
		        $("#group_type").focus();
		        $("#group_type_alert").text('Field is required!')
		    }

		    if($("#group_type").val() == 'multiple' && $("#selection_limit").val() <= 1){
		        process = false;
		        $("#selection_limit").focus();
		        $("#selection_limit_alert").text('Limit should be atleast 2')
		    }

		   if(process){
                $.ajax({
                    url: "{{route('storeDeal_product')}}",
                    type: "POST",
                    data: $("#dealCreateForm").serialize(),
                    success:function(resp){

                        if (resp.status == 200) {
                            swal("Success!", "", "success");
                            dealFormClear();
                            window.location = "{{ route('createDealProduct',$generalItem[0]->id) }}";
                        }else{
                            if(resp.status == 409){
                                $("#"+resp.control).focus();
                                $("#"+resp.control+"_alert").text(resp.msg);
                                swal("Alert!", resp.msg, "error");
                            }

                            if(resp.status == 500){
                                swal("Alert!", resp.msg, "error");
                            }
                        }
                    }
                });
		   }

		});

	   function dealFormClear(){
	       $("#group_name").val('');
	       $("#group_type").val('').trigger('change');
	       $("#subDepartment_deal").val('').trigger('change');
	       $("#department_deal").val('').trigger('change');

	       $.each(table_createDeal,function(i,v){
	           //alert(v)
	           $("#"+v).remove();
	           //arrayValue_remove_addon_md(v,0)
	       });

	       $("#group_name_alert").text('');
	       $("#group_type_alert").text('');
	       $("#department_deal_alert").text('');
	       $("#subDepartment_deal_alert").text('');
	       $("#product_deal_alert").text('');

	       table_createDeal = [];
	   }

	   function dealEditFormClear(){
	       $("#group_name_editmd").val('');
	       $("#group_type_editmd").val('').trigger('change');
	       $("#subDepartment_editmd").val('').trigger('change');
	       $("#department_editmd").val('').trigger('change');

           $("#table_deal_editmd tbody").empty();

	       $("#group_name_editmd_alert").text('');
	       $("#group_type_editmd_alert").text('');
	       $("#department_editmd_alert").text('');
	       $("#subDepartment_editmd_alert").text('');
	       $("#product_editmd_alert").text('');

	       table_editDeal = [];
	   }

		function editDeal(unqId,itemId,groupName,groupType,groupLimit,priority){
		    $("#table_deal_editmd tbody").empty();
		    $("#inventory_deal_id_editmd").val(itemId);
		  //  $("#group_id_editmd").val(groupId)
		    $("#editDeal-modal").modal('show');

		    $("#group_name_editmd").val(groupName);
		    $("#priority_editmd").val(priority);
		    $("#unqId_editmd").val(unqId);

		    $("#group_type_editmd").val(groupType).trigger('change');
		    $('#selection_limit_editmd').val(groupLimit);
		  //  ,prod_id:itemId
    			$.ajax({
    			  url: "{{ route('getDeal_prod_values') }}",
    			  method : "POST",
    			  data:{_token:'{{ csrf_token() }}',id:unqId},
    			  dataType:'json',
    			  async: false,
    			  success: function(resp){
                      if(resp != null){
                          table_editDeal = [];
                        //   $("#department_md_edit").val(resp.departmentId).trigger('change');
                        //   setTimeout(function() {
                        //         selectedProduct(resp.productId);
                        //     },300);

                        $.each(resp,function(i,v){
                            var productId         = v.product_id;
                            var productName       = v.product_name;
                            var productQty        = v.product_quantity;
                            var deaprtmentName    = v.department_name;
                            var subDepartmentName = v.sub_depart_name;

                      	       if($("#row_deal_editmd"+productId).length == 0){
                    	           var qty = productQty == '' ? 0 : productQty;
                    	         $("#table_deal_editmd tbody").append('<tr id="row_deal_editmd'+productId+'"><td>'+deaprtmentName+'</td><td>'+subDepartmentName+'</td><td id="deal-cell-2-editmd'+productId+'">'+productName+'<input type="hidden" name="products[]" value="'+productId+'"></td><td><input type="hidden" name="product_qty[]" value="'+qty+'">'+qty+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Product" onclick="removeDealProduct_tmp('+productId+',1,'+unqId+')"></i></td></tr>');

                    	         table_editDeal.push("row_deal_editmd"+productId);

                    	         $("#product_editmd").val('').trigger('change');
                    	         $("#qty_deal_editmd").val('0');
                    	         $("#department_editmd_alert").text('');
                    	         $("#product_editmd_alert").text('');
                    	       }

                        })


                      }
    			  }
    			});

		}

	   function add_dealProduct_tmp(){

	       if($("#product_deal").val() != '' && $("#department_deal").val() != '' && $("#subDepartment_deal").val() != ''){
    	       if($("#row_deal"+$("#product_deal").val()).length == 0){
    	           var qty = $("#qty_deal").val() == '' ? 0 : $("#qty_deal").val();
    	         $("#table_dealCrearte tbody").append('<tr id="row_deal'+$("#product_deal").val()+'"><td>'+$("#department_deal option:selected").text()+'</td><td>'+$("#subDepartment_deal option:selected").text()+'</td><td id="deal-cell-2-'+$("#product__deal").val()+'">'+$("#product_deal option:selected").text()+'<input type="hidden" name="products[]" value="'+$("#product_deal").val()+'"></td><td><input type="hidden" name="product_qty[]" value="'+qty+'">'+qty+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Product" onclick="removeDealProduct_tmp('+$("#product_deal").val()+',0,0)"></i></td></tr>');

    	         table_createDeal.push("row_deal"+$("#product_deal").val());

    	         $("#product_deal").val('').trigger('change');
    	         $("#qty_deal").val('0');
    	         $("#department_deal_alert").text('');
    	         $("#product_deal_alert").text('');
    	       }else{
    	                swal({
                                title: "Error",
                                text: "This Product is already taken",
                                type: "error"
                           });
    	       }
	     }else{
	                swal({
                            title: "Error",
                            text: "Select the product please",
                            type: "error"
                       });
	     }
	   }

	   function add_dealProduct_tmp_editmd(){

	       let productId         = $("#product_editmd").val();
	       let productName       = $("#product_editmd option:selected").text();
	       let deaprtmentId      = $("#department_editmd").val();
	       let deaprtmentName    = $("#department_editmd option:selected").text();
	       let subDeaprtmentName = $("#subDepartment_editmd option:selected").text();

	       if(deaprtmentId != '' && productId != ''){
    	       if($("#row_deal_editmd"+productId).length == 0){
    	           let qty = $("#qty_editmd").val() == '' ? 0 : $("#qty_editmd").val();
    	         $("#table_deal_editmd tbody").append('<tr id="row_deal_editmd'+productId+'"><td>'+deaprtmentName+'</td><td>'+subDeaprtmentName+'</td><td id="deal-cell-2-editmd'+productId+'">'+productName+'<input type="hidden" name="products[]" value="'+productId+'"></td><td><input type="hidden" name="product_qty[]" value="'+qty+'">'+qty+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Product" onclick="removeDealProduct_tmp('+productId+',1,'+$("#unqId_editmd").val()+')"></i></td></tr>');

    	         table_editDeal.push("row_deal_editmd"+productId);

    	       //  singleDealValue_store($("#unqId_editmd").val(),$("#group_id_editmd").val(),productId,productName,qty)

    	         $("#product_editmd").val('').trigger('change');
    	         $("#qty_editmd").val('0');
    	         $("#department_editmd_alert").text('');
    	         $("#product_editmd_alert").text('');
    	       }else{
    	                swal({
                                title: "Error",
                                text: "This Product is already taken",
                                type: "error"
                           });
    	       }
	     }else{
	                swal({
                            title: "Error",
                            text: "Select the product please",
                            type: "error"
                       });
	     }
	   }

	   function singleDealValue_store(unqDealId,dealHeadId,productId,productName,qty){

          $.ajax({
    			  url: "{{ route('storeDealValue_single') }}",
    			  method : "POST",
    			  data:{_token:'{{ csrf_token() }}',id:dealHeadId,dealRowIdUnq:unqDealId,product_id:productId,product_name:productName,quantity:qty},
    			  async: false,
    			  success: function(resp){
    			     console.log(resp)
                     if(resp.status == 200){
                         swal("Success", "", "success");
                         loadDealProduct(unqDealId);
                     }else{
                         if(resp.status == 500){
                           swal("Alert!", resp.msg, "error");
                         }
                     }
    			  }
    			});
	   }

         $("#btn_updateDeal").on('click',function(){

		    if($("#group_name_editmd").val() == ''){
		        $("#group_name_editmd_alert").text('Field is required.');
		    }else if($("#group_type_editmd").val() == ''){
		        $("#group_type_editmd_alert").text('Field is required.');
		    }else{
    			$.ajax({
    			  url: "{{ route('updateDealProduct_up') }}",
    			  method : "POST",
    			  data:$("#editDealForm").serialize(),
    			  async: false,
    			  success: function(resp){
    			     console.log(resp)
                     if(resp.status == 200){
                         swal("Success", "", "success");
                         $("#editDeal-modal").modal('hide');
                         window.location = "{{ route('createDealProduct',$generalItem[0]->id) }}";
                        //  loadDealProduct($("#inventory_id_edit").val());
                     }else{
                         if(resp.status == 409){
                           $("#"+resp.control).focus();
                           $("#"+resp.control+"_alert").text(resp.msg);
                         }

                         if(resp.status == 500){

                           swal("Alert!", resp.msg, "error");
                         }
                     }
    			  }
    			});
		    }
		});

	   function removeDealProduct_tmp(id,md,dealHeadGenID){
	       if(md == 1){
	           modal_remove_dealProduct(id,"row_deal_editmd","deal-cell-2-editmd",md,dealHeadGenID)
	       }else{
	           modal_remove_dealProduct(id,"row_deal","deal-cell-2-",md,0)
	       }
	   }

	   function modal_remove_dealProduct(id,rowElementID,productElementID,md,dealHeadGenID){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#"+productElementID+id).text()+" deal product!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                             $("#"+rowElementID+id).remove();
                             arrayValue_remove_addon_md(rowElementID+id,md);
                             swal("Success", "", "success");
                        //   if(md == 1){
                        //       removeDealProduct(dealHeadGenID,id)
                        //   }else{
                        //       swal("Success", "", "success");
                        //   }
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
	   }

	   //function removeDealProduct(generalDealHeadId,productId){
    //         $.ajax({
    //             url: "{{route('removeDeal_up')}}",
    //             type: "POST",
    //             data: {_token:"{{csrf_token()}}",general_deal_id:generalDealHeadId,product_id:productId},
    //             success:function(resp){
    //                 if (resp.status == 200) {
    //                     $("#row-"+groupId).remove();

    //                     swal("Success!", "", "success");
    //                 }else{
    //                     swal("Alert!", "Product is not removed", "error");
    //                 }
    //             }
    //         });
	   //}

	   function arrayValue_remove_deal(value,md){
          if(md == 1){
            const index = table_editDeal.indexOf(value);
            table_editDeal.splice(index, 1);
          }else{
            const index = table_createDeal.indexOf(value);
            table_createDeal.splice(index, 1);
          }
	   }


		function selectedProduct(values){
		   $("#products_md_edit").select2('val',[values]);
		}

		function removeDeal(headId,productId,groupName){

              swal({
                    title: "DELETE DEAL",
                    text: "Do you want to delete deal "+groupName+"?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "YES",
                    cancelButtonText: "NO",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },function(isConfirm){
                    if(isConfirm){
                            $.ajax({
                                url: "{{route('removeDeal_product')}}",
                                type: "POST",
                                data: {_token:"{{csrf_token()}}",inventid:productId,id:headId},
                                dataType:'json',
                                success:function(resp){
                                    if (resp.status == 200) {
                                        $("#row-"+headId).remove();
                                        swal("Success!", "Deal is Deleted:)", "success");
                                        $("#editDeal-modal").modal('hide');
                                    }else{
                                        swal("Alert!", "Deal not Deleted:)", "error");
                                    }
                                }
                            });
                    }else{
                        swal.close();
                    }
                });

		}


		$("#addon_type").on('change',function(){
		    if($(this).val() == 'multiple'){
		        if($('#selection_limit_mdAddon').attr('disabled')){
		            $('#selection_limit_mdAddon').attr('disabled',false);
		        }
		    }else{
		        if(!$('#selection_limit_mdAddon').attr('disabled')){
		            $('#selection_limit_mdAddon').attr('disabled',true);
		        }
		    }

		    $("#selection_limit_mdAddon").val('');
		});

		$("#addon_type_editmdAddon").on('change',function(){
		    if($(this).val() == 'multiple'){
		        if($('#selection_limit_editmdAddon').attr('disabled')){
		            $('#selection_limit_editmdAddon').attr('disabled',false);
		        }
		    }else{
		        if(!$('#selection_limit_editmdAddon').attr('disabled')){
		            $('#selection_limit_editmdAddon').attr('disabled',true);
		        }
		    }

		    $("#selection_limit_editmdAddon").val('');
		});


		function addon_list(dealGenHeadId,productId,productName){
           $("#addonList-modal").modal('show');
           $("#mod-title-addon").html('<span>ProductName:</span>'+productName);
           $("#inventory_id_mdAddon").val(productId);
           $("#inventory_name_mdAddon").val(productName);
           $("#dealGenHeadId_mdAddon").val(dealGenHeadId);

           loadAddons(productId);
		}

        function loadAddons(productId){

                $.ajax({
                    url: "{{route('getAddons_dealProduct')}}",
                    type: "POST",
                    data: {_token:'{{ csrf_token() }}',finishgood:productId},
                    dataType:'json',
                    success:function(resp){
                       $("#table_addon tbody").empty();
                        if(resp.addonHead != ''){
                            $.each(resp.addonHead,function(i,v){

                                $("#table_addon tbody").append("<tr>"+
                                                          "<td class='d-none'>"+v.id+"</td>"+
                                                          "<td>"+v.name+"</td>"+
                                                          "<td id='cell-2-"+v.id+"'></td>"+
                                                          "<td>"+v.type+"</td>"+
                                                          "<td>"+v.addon_limit+"</td>"+
                                                          "<td>"+(v.is_required == 1 ? 'Yes' : 'No')+"</td>"+
                                                          "<td>"+
                                                           "<i class='icofont icofont-edit text-primary pointer m-t-2 m-r-1 f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit Addon' onclick='editAddon("+v.id+",\""+v.name+"\",\""+v.show_website_name+"\",\""+v.type+"\","+v.is_required+","+v.addon_limit+")'></i>"+
                                                           "<i class='icofont icofont-trash text-danger pointer m-t-2 f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Remove Addon' onclick='modal_remove_addonTableRow_md("+v.id+","+productId+",\""+v.name+"\")'></i>"
                                                          +"</td>"+
                                                          "</tr>");
                            });

                            $.each(resp.addon_value,function(i,v){
                                $("#cell-2-"+v.addon_category_id).append("<label class='badge badge-bg-success badge-md'>"+v.name+(v.price != null ? " - Rs."+v.price : '')+"</label><br/>");
                            });
                        }
                    }
                });
        }

	   function modal_add_addon_tmp(){

	       if($("#product_mdAddon").val() != '' && $("#department_mdAddon").val() != '' && $("#subDepartment_mdAddon").val() != ''){
    	       if($("#row_mdAddon"+$("#product_mdAddon").val()).length == 0){
    	           var price = $("#price_mdAddon").val() == '' ? 0 : $("#price_mdAddon").val();
    	         $("#table_addonGeneratList_md tbody").append('<tr id="row_mdAddon'+$("#product_mdAddon").val()+'"><td>  '+$("#department_mdAddon option:selected").text()+'</td><td>  '+$("#subDepartment_mdAddon option:selected").text()+'</td><td id="cel-2-'+$("#product_mdAddon").val()+'">'+$("#product_mdAddon option:selected").text()+'<input type="hidden" name="products[]" value="'+$("#product_mdAddon").val()+'"></td><td><input type="hidden" name="price[]" value="'+price+'">'+price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Addon" onclick="modal_remove_addonTmp('+$("#product_mdAddon").val()+')"></i></td></tr>');

    	         table_row_mdId.push("row_mdAddon"+$("#product_mdAddon").val());

    	         $("#product_mdAddon").val('').trigger('change');
    	         $("#price_mdAddon").val('');
    	         $("#department_mdAddon_alert").text('');
    	         $("#product_mdAddon_alert").text('');
    	       }else{
    	                swal({
                                title: "Error",
                                text: "This Product is already taken",
                                type: "error"
                           });
    	       }
	     }else{
	                swal({
                            title: "Error",
                            text: "Select the product please",
                            type: "error"
                       });
	     }
	   }

	   function modal_remove_addonTmp(id){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#cel-2-"+id).text()+" addon product!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                           $("#row_mdAddon"+id).remove();
                           arrayValue_remove_addon_md("row_mdAddon"+id,0);
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });

	   }

	   function editAddon(headAddonId,addName,showWebsiteName,type,isRequired,addonSelectionLimit){
           $("#editAddon-modal").modal('show');
           $("#addonList-modal").modal('hide');
           $("#editModel-title-addon").html('<span>Addon:</span>'+addName);
           $("#inventory_id_editmdAddon").val($("#inventory_id_mdAddon").val());
           $("#inventory_name_editmdAddon").val($("#inventory_name_mdAddon").val());
           $("#addonheadId_editmdAddon").val(headAddonId);

           $("#addon_name_editmdAddon").val(addName);
           $("#showebsite_name_editmdAddon").val(showWebsiteName);
           $("#addon_type_editmdAddon").val(type).trigger('change');

           if(isRequired == 1){
               $("#is_required_editmdAddon").attr('checked',true).trigger('click');
           }

           $("#selection_limit_editmdAddon").val(addonSelectionLimit);

                $.ajax({
                    url: "{{route('getAddonValues_dealProduct')}}",
                    type: "POST",
                    data: {_token:'{{ csrf_token() }}',id:headAddonId},
                    success:function(resp){

                        if (resp != null) {

                            $.each(resp,function(i,v){
                              if($("#row_editmdAddon"+v.inventory_product_id).length == 0){

                                	         $("#table_addonGeneratList_editmd").append('<tr id="row_editmdAddon'+v.inventory_product_id+'"><td> '+v.department_name+'</td><td> '+v.sub_depart_name+'</td><td id="cell-2-editmdAddon'+v.inventory_product_id+'">'+v.name+'<input type="hidden" name="products[]" value="'+v.inventory_product_id+'"></td><td><input type="hidden" name="price[]" value="'+v.price+'">'+v.price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Addon" onclick="modal_remove_editaddondataRow('+v.inventory_product_id+','+v.id+')"></i></td></tr>');

                                	         table_row_editmdId.push("row_editmdAddon"+v.inventory_product_id);

                                	         $("#product_editmdAddon").val('').trigger('change');
                                	         $("#price_editmdAddon").val('');
                                	         $("#department_editmdAddon_alert").text('');
                                	         $("#product_editmdAddon_alert").text('');
                               }
                            })
                        }
                    }
                });
	   }

	   function modal_add_editmdAddon_dataRow(){

	       if($("#product_editmdAddon").val() != '' && $("#department_editmdAddon").val() != '' && $("#subDepartment_editmdAddon").val() != ''){
    	       if($("#row_editmdAddon"+$("#product_mdAddon").val()).length == 0){
    	           var price = $("#price_editmdAddon").val() == '' ? 0 : $("#price_editmdAddon").val();
    	         $("#table_addonGeneratList_editmd tbody").append('<tr id="row_editmdAddon'+$("#product_editmdAddon").val()+'"><td>  '+$("#department_editmdAddon option:selected").text()+'</td><td>  '+$("#subDepartment_editmdAddon option:selected").text()+'</td><td id="cell-2-editmdAddon'+$("#product_editmdAddon").val()+'">'+$("#product_editmdAddon option:selected").text()+'<input type="hidden" name="products[]" value="'+$("#product_editmdAddon").val()+'"></td><td><input type="hidden" name="price[]" value="'+price+'">'+price+'</td><td><i class="icofont icofont-trash text-danger pointer m-t-2 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove Addon" onclick="modal_remove_editaddondataRow('+$("#product_editmdAddon").val()+')"></i></td></tr>');

    	         table_row_editmdId.push("row_editmdAddon"+$("#product_editmdAddon").val());

    	         addonSingle_value($("#addonheadId_editmdAddon").val(),$("#product_editmdAddon option:selected").text(),$("#product_editmdAddon").val(),price);

    	         $("#product_editmdAddon").val('').trigger('change');
    	         $("#price_editmdAddon").val('');
    	         $("#department_editmdAddon_alert").text('');
    	         $("#product_editmdAddon_alert").text('');
    	       }else{
    	                swal({
                                title: "Error",
                                text: "This Product is already taken",
                                type: "error"
                           });
    	       }
	     }else{
	                swal({
                            title: "Error",
                            text: "Select the product please",
                            type: "error"
                       });
	     }
	   }

	   function addonSingle_value(headAddonId,productName,productId,amount){
                $.ajax({
                    url: "{{route('store_singleValue_addonDealProduct')}}",
                    type: "POST",
                    data: {_token:'{{ csrf_token() }}',id:headAddonId,product_id:productId,product_name:productName,price:amount},
                    success:function(resp){

                        if (resp.status == 200) {
                            swal("Success!", "", "success");
                            loadAddons($("#inventory_id_editmdAddon").val());
                            addonFormClear();
                        }else{
                            if(resp.status == 500){
                                swal("Alert!", resp.msg, "error");
                            }
                        }
                    }
                });

	   }



	   function addonFormClear(){
	       $("#addon_name").val('');
	       $("#showebsite_name").val('');
	        $("#is_required").trigger('click');
	       $("#addon_type").val('').trigger('change');
	       $("#department_mdAddon").val('').trigger('change');

	       $("#table_addonGeneratList_md tbody").empty();
	       //$.each(table_row_mdId,function(i,v){
	       //    //alert(v)
	       //    $("#"+v).remove();
	       //    //arrayValue_remove_addon_md(v,0)
	       //});

	       $("#addon_name_alert").text('');
	       $("#showebsite_name_alert").text('');
	       $("#addon_type_alert").text('');
	       $("#department_mdAddon_alert").text('');
	       $("#product_mdAddon_alert").text('');
	       $("#is_required_alert").text('');

	       table_row_mdId = [];
	   }

	   function addonEditFormClear(){
	       $("#addon_name_editmdAddon").val('');
	       $("#showebsite_name_editmdAddon").val('');
	        $("#is_required_editmdAddon").trigger('click');
	       $("#addon_type_editmdAddon").val('').trigger('change');
	       $("#department_editmdAddon").val('').trigger('change');

	       $("#table_addonGeneratList_editmd tbody").empty();
	       //$.each(table_row_editmdId,function(i,v){
	       //    //alert(v)
	       //    $("#"+v).remove();
	       //    //arrayValue_remove_addon_md(v,1)
	       //});

	       $("#addon_name_editmdAddon_alert").text('');
	       $("#showebsite_name_editmdAddon_alert").text('');
	       $("#addon_type_editmdAddon_alert").text('');
	       $("#department_editmdAddon_alert").text('');
	       $("#product_editmdAddon_alert").text('');
	       $("#is_required_editmdAddon_alert").text('');

	       table_row_editmdId = [];
	   }


		$("#btn_storeAddon").on('click',function(){

		   let process = true;

		    if($("#addon_name").val() == ''){
		        process = false;
		        $("#addon_name").focus();
		        $("#addon_name_alert").text('Field is required!')
		    }

		    if($("#showebsite_name").val() == ''){
		        process = false;
		        $("#showebsite_name").focus();
		        $("#showebsite_name_alert").text('Field is required!')
		    }

		    if($("#addon_type").val() == ''){
		        process = false;
		        $("#addon_type").focus();
		        $("#addon_type_alert").text('Field is required!')
		    }

		    if($("#addon_type").val() == 'multiple' && $("#selection_limit_mdAddon").val() <= 1){
		        process = false;
		        $("#selection_limit_mdAddon").focus();
		        $("#selection_limit_mdAddon_alert").text('Limit should be atleast 2')
		    }

		  //  if(!$("#is_required").is(":checked")){
		  //      process = false;
		  //      $("#is_required").focus();
		  //      $("#is_required_mdAddon_alert").text('Field is required!')
		  //  }

		   if(process){
                $.ajax({
                    url: "{{route('storeAddon_dealProduct')}}",
                    type: "POST",
                    data: $("#storeAddon_dealProductForm").serialize(),
                    success:function(resp){

                        if (resp.status == 200) {
                            swal("Success!", "", "success");
                            addonFormClear();
                            loadAddons($("#inventory_id_mdAddon").val());
                            loadDealProduct($("#dealGenHeadId_mdAddon").val());

                        }else{
                            if(resp.status == 409){
                                $("#"+resp.control).focus();
                                $("#"+resp.control+"_alert").text(resp.msg);
                                swal("Alert!", resp.msg, "error");
                            }

                            if(resp.status == 500){
                                swal("Alert!", resp.msg, "error");
                            }
                        }
                    }
                });
		   }

		});

	   function modal_remove_addonTableRow_md(rowId,productId,addonName){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+addonName+" Addon!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                         $.ajax({
                            url: "{{route('removeAddon_dealProduct')}}",
                            type: "POST",
                            data: {_token:'{{ csrf_token() }}',id:rowId,product_id:productId},
                            success:function(resp){

                                if (resp.status == 200) {
                                       loadAddons(productId);
                                       loadDealProduct($("#dealGenHeadId_mdAddon").val());
                                       swal("Success!", "", "success");
                                }else{
                                       swal("Alert!", resp.msg, "error");
                                }
                            }
                        });
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });

	   }

	   $("#btn_updateAddon").on('click',function(){

		   let process = true;

		    if($("#addon_name_editmdAddon").val() == ''){
		        process = false;
		        $("#addon_name_editmdAddon").focus();
		        $("#addon_name_editmdAddon_alert").text('Field is required!');
		    }

		    if($("#showebsite_name_editmdAddon").val() == ''){
		        process = false;
		        $("#showebsite_name_editmdAddon").focus();
		        $("#showebsite_name_editmdAddon_alert").text('Field is required!');
		    }

		    if($("#addon_type_editmdAddon").val() == ''){
		        process = false;
		        $("#addon_type_editmdAddon").focus();
		        $("#addon_type_editmdAddon_alert").text('Select addon type field is required!');
		    }

		    if($("#addon_type_editmdAddon").val() == 'multiple' && $("#selection_limit_editmdAddon").val() <= 1){
		        process = false;
		        $("#selection_limit_editmdAddon").focus();
		        $("#selection_limit_editmdAddon_alert").text('Limit should be atleast 2');
		    }

		  //  if(!$("#is_required_editmdAddon").is(":checked")){
		  //      process = false;
		  //      $("#is_required_editmdAddon").focus();
		  //      $("#is_required_editmdAddon_alert").text('Field is required!');
		  //  }

		   if(process){
                $.ajax({
                    url: "{{route('update_addonDealProduct')}}",
                    type: "POST",
                    data: $("#editAddon_dealProductForm").serialize(),
                    success:function(resp){

                        if (resp.status == 200) {
                            swal("Success!", "", "success");
                            addonEditFormClear();
                            loadAddons($("#inventory_id_editmdAddon").val());
                            loadDealProduct($("#dealGenHeadId_mdAddon").val());
                            $("#editAddon-modal").modal('hide');
                            $("#addonList-modal").modal('show');

                            // swal("Success!", "", "success");
                            // btn_editAddon_md_close
                        }else{
                            if(resp.status == 409){
                                $("#"+resp.control).focus();
                                $("#"+resp.control+"_alert").text(resp.msg);
                                swal("Alert!", resp.msg, "error");
                            }

                            if(resp.status == 500){
                                swal("Alert!", resp.msg, "error");
                            }
                        }
                    }
                });
		   }

		});

	   function modal_remove_editaddondataRow(id,addonVlId){
           swal({
                    title: "Are you sure?",
                    text: "You want to remove this "+$("#cell-2-editmdAddon"+id).text()+" addon product!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){

                         $.ajax({
                            url: "{{route('removeAddonValue_dealProduct')}}",
                            type: "POST",
                            data: {_token:'{{ csrf_token() }}',id:addonVlId},
                            success:function(resp){

                                if (resp.status == 200) {
                                       $("#row_editmdAddon"+id).remove();
                                       arrayValue_remove_addon_md("row_editmdAddon"+id,1);
                                       loadAddons($("#inventory_id_editmdAddon").val());
                                       swal("Success!", "", "success");
                                }else{
                                       swal("Alert!", resp.msg, "error");
                                }
                            }
                        });
                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
	   }

	   function arrayValue_remove_addon_md(value,md){

          if(md == 1){
            const index = table_row_editmdId.indexOf(value);
            table_row_editmdId.splice(index, 1);
          }else{
            const index = table_row_mdId.indexOf(value);
            table_row_mdId.splice(index, 1);
          }
	   }
</script>

@endsection



