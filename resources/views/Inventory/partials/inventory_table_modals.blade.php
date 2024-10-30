<div class="modal fade modal-flex" id="productSetting-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Choose Action</h4>
			</div>
			<div class="modal-body">
                <div class="list-group">
                    <a href="" id="linkVariableProd" class="list-group-item list-group-item-action">Create Variable Product</a>
                    <a href="" id="linkDeal" class="list-group-item list-group-item-action">Create Deal</a>
                  </div>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-default waves-effect waves-light f-right">
					Close
				</button>
			</div>

		</div>
	</div>
</div>


<div class="modal fade modal-flex" id="website-detail-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Website Change</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
				   <select id="website_md" class="form-control select2">
					   <option>Select</option>
					   @foreach($websites as $val)
						 <option value="{{ $val->id }}">{{ $val->name }}</option>
					   @endforeach
				   </select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnwebsiteSave" class="btn btn-success waves-effect waves-light f-right">
					Save
				</button>
			</div>

		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="brand-detail-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Brand Change</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
				   <select id="brand_md" name="brand" class="form-control select2">
					   <option>Select</option>
					   @foreach($brandList as $val)
						 <option value="{{ $val->id }}">{{ $val->name }}</option>
					   @endforeach
				   </select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnbrandSave" class="btn btn-success waves-effect waves-light f-right">
					Save
				</button>
			</div>

		</div>
	</div>
</div>
<div class="modal fade modal-flex justify-content-center"  style=" background: transparent;position: absolute;float: left;left: 50%;top: 50%;transform: translate(-50%, -50%);" id="loader-modal" role="document"  >
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="loader animation-start">
					<span class="circle delay-1 size-2"></span>
					<span class="circle delay-2 size-4"></span>
					<span class="circle delay-3 size-6"></span>
					<span class="circle delay-4 size-7"></span>
					<span class="circle delay-5 size-7"></span>
					<span class="circle delay-6 size-6"></span>
					<span class="circle delay-7 size-4"></span>
					<span class="circle delay-8 size-2"></span>
				</div>
				<div class="text-center f-24">
					<label class="m-l-25" >Please Wait...</label>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="label-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-md" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<h4 class="modal-title">Label Printing</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-md-4 col-sm-4">
					<div id="" class="form-group">
						<label class="form-control-label "><i class="icofont icofont-barcode"></i>Select Size</label>
						<select class="select2" id="labelsize" data-placeholder="Select Size">
							<option value="">Select Size</option>
							<option value="6040">60 x 40</option>
							<option value="1928">19 x 28</option>
							<option value="3828">38 x 28</option>
							<option value="4020">40 x 20</option>
						</select>
					</div>
				</div>
				<div class="col-md-4 col-sm-4">
					<div id="" class="form-group">
						<label class="form-control-label "><i class="icofont icofont-barcode"></i>Select Pattern</label>
						<select class="select2" id="labelpattern" data-placeholder="Select Pattern">
							<option value="">Select Pattern</option>
							<option value="single">Single</option>
							<option value="double">Double</option>
							<option value="tripple">Tripple</option>
						</select>
					</div>
				</div>
				<div class="col-md-4 col-lg-4">
					<div class="form-group">
						<label class="form-control-label">Print Header</label>
						<select class="select2" id="printheader" data-placeholder="Select Pattern">
							<option value="company">Company</option>
							<option value="branch">Branch</option>
						</select>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-8 col-lg-8">
					<div class="form-group">
						<label class="form-control-label">Name</label>
						<input type="text" name="label_name" id="label_name" class="form-control" placeholder="Code">

					</div>
				</div>

				<div class="col-md-2 col-lg-2">
					<div class="form-group">
						<label class="form-control-label">Margin1</label>
						<input type="number" name="name_margin1" id="name_margin1" class="form-control"  value="0">

					</div>
				</div>
				<div class="col-md-2 col-lg-2">
					<div class="form-group">
						<label class="form-control-label">Margin2</label>
						<input type="number" name="name_margin2" id="name_margin2" class="form-control"  value="0">

					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-md-12 col-lg-6">
					<div class="form-group">
						<label class="form-control-label">Code</label>
						<input type="text" name="label_code" id="label_code" class="form-control" placeholder="Code">
					</div>
				</div>
				<div class="col-md-6 col-lg-6">
					<div class="form-group">
						<label class="form-control-label">Price</label>
						<input type="text"  name="label_price" id="label_price" class="form-control" placeholder="Enter Percentage or Amount">
					</div>
				</div>
			</div>




		</div>

		<div class="modal-footer m-5 m-t-10">
			<button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light f-right m-b-10" onClick="print_barcode()">Print Label</button>
{{--                        <label class="f-left f-18 f-w-100 ">NOTES</label>--}}
{{--                        <br/>--}}
{{--                        <br/>--}}

{{--                        <ul style="list-style-type: circle;margin-left: 20px;">--}}
{{--                            <li class=" f-12 f-italic f-left" >Please input qty that you want to increase.</li>--}}
{{--                            <li class=" f-12 f-italic f-left" >In-case you want to decrease the qty please input with negative sign .</li>--}}
{{--                            <li class=" f-12 f-italic f-left" >For example: increase 3000 to 3500 just need to enter 500 .</li>--}}
{{--                        </ul>--}}

		</div>

	</div>
</div>
</div>

<div class="modal fade modal-flex" id="sp-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
				<h4 id="mod-title" class="modal-title">Select Vendors</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<input type="hidden" id="productidforvendors" name="productidforvendors" />
					<div class="col-md-12">
						<label class="form-control-label">Select Vendors</label>
						 <select id="vendor" name="vendor" data-placeholder="Select Vendors" class="f-right select2" multiple>
							 <option value="">Select Vendors</option>
							 @foreach($vendors as $vendor)
								 <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
							 @endforeach
						 </select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn_assign" class="btn btn-success waves-effect waves-light">Assign</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade modal-flex" id="createDeal-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
				<h4 id="mod-title" class="modal-title">Create Deal</h4>
			</div>
			<div class="modal-body">
				<form id="createDealForm" method="post">
					@csrf
					<input type="hidden" name="inventory_id" id="inventory_id">
					<input type="hidden" name="inventory_name" id="inventory_name">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label>Group Name</label>
							<input type="text" class="form-control" placeholder="Group Name" name="group_name" id="group_name">
							<span id="group_name_alert" class="text-danger"></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						   <label>Group Type</label>
						   <select class="select2" data-placeholder="Select Type" name="group_type" id="group_type">
							   <option value="">Select</option>
							   <option value="single">Single</option>
							   <option value="multiple">Multiple</option>
						   </select>
						</div>
						<span id="group_type_alert" class="text-danger"></span>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						   <label>Selection Limited</label>
						   <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="selection_limited" id="selection_limited" disabled>
						</div>
					</div>

				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
						   <label>Department</label>
						   <select class="select2" id="department_md" data-placeholder="Select Depatrment" name="department" id="department">
							   <option value="">Select</option>
							   @foreach($department as $val)
								  <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
							   @endforeach
						   </select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						   <label>Products</label>
						   <select class="select2" data-placeholder="Select Products" name="products[]" id="products_md" multiple disabled>
							   <option value="">Select</option>
						   </select>
						</div>
					</div>
				</div>
			  </form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn_addDeal" class="btn btn-success waves-effect waves-light">Submit</button>
			</div>
		</div>
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
				<form id="editDealForm" method="post">
					@csrf
					<input type="hidden" name="inventory_id" id="inventory_id_edit">
					<input type="hidden" name="group_id" id="group_id">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label>Group Name</label>
							<input type="text" class="form-control" placeholder="Group Name" name="group_name_edit" id="group_name_edit">
							<span id="group_name_alert" class="text-danger"></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						   <label>Group Type</label>
						   <select class="select2" data-placeholder="Select Type" name="group_type_edit" id="group_type_edit">
							   <option value="">Select</option>
							   <option value="single">Single</option>
							   <option value="multiple">Multiple</option>
						   </select>
						</div>
						<span id="group_type_alert" class="text-danger"></span>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						   <label>Selection Limited</label>
						   <input type="number" min="0" value="0" class="form-control" placeholder="Selection Limited" name="selection_limited_edit" id="selection_limited_edit" disabled>
						</div>
					</div>

				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
						   <label>Department</label>
						   <select class="select2" data-placeholder="Select Depatrment" name="department_md_edit" id="department_md_edit">
							   <option value="">Select</option>
							   @foreach($department as $val)
								  <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
							   @endforeach
						   </select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
						   <label>Products</label>
						   <select class="select2" data-placeholder="Select Products" name="products_md_edit[]" id="products_md_edit" multiple disabled>
							   <option value="">Select</option>
						   </select>
						</div>
					</div>
				</div>
			  </form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn_removeDeal" class="btn btn-danger waves-effect waves-light m-r-2">Remove</button>

				<button type="button" id="btn_updateDeal" class="btn btn-success waves-effect waves-light">Save Changes</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="tags-detail-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Product link to Tags</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
                    <label>Tags</label>
                    <i onclick="tagCreate()" class="icofont icofont-plus f-right text-success pointer" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Tag"></i>
				   <select id="tags_md" name="tags" class="select2" data-placeholder="Select Tags" multiple>
					   <option>Select</option>
					   @foreach($tagsList as $val)
						 <option value="{{ $val->id }}">{{ $val->name }}</option>
					   @endforeach
				   </select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btntagSave" class="btn btn-success waves-effect waves-light f-right">
					Save
				</button>
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
                      <input type="text" name="tagname_md" id="tagname_md" class="form-control" placeholder="Like 'best product'" />
                     </div>
                   </div>
               </div>
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-success waves-effect waves-light" onClick="insertProduct_attribute()">Add</button>
          </div>
       </div>
    </div>
 </div>

<div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Department Change</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group ">
							<select id="ddldepartment" class="form-control select2">

							</select>
						</div>
					</div>
					<div class="col-md-12 m-t-10">

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnDptSave" class="btn btn-success waves-effect waves-light f-right">
					Save
				</button>
			</div>

		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="change-tax-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Change Tax </h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group ">
							<select id="ddltax" class="form-control select2">

							</select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group ">
							<label class="form-control-label "><i class="icofont icofont-barcode"></i> New Tax Rate</label>
							<input class="form-control" type="text" name="tax_rate_new" id="tax_rate_new"   placeholder="Enter New Tax Rate"/>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnTaxSave" class="btn btn-success waves-effect waves-light f-right m-r-20">
					Save
				</button>
			</div>

		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="details-uom" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Change Unit of Measure </h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group ">
							<select id="ddluom" class="form-control select2">

							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnUomSave" class="btn btn-success waves-effect waves-light f-right m-r-20">
					Save
				</button>
			</div>

		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="details-subdept" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Change Sub Department</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 m-b-20">
						<div class="form-group ">
							<select id="ddldepartment1" class="form-control select2">

							</select>
						</div>
						<div class="form-group ">

							<select id="ddlsubdept" class="form-control select2 m-t-50">
								<option value="">Select Sub Department</option>
							</select>

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="btnsubdeptSave" class="btn btn-success waves-effect waves-light f-right">
						Save
					</button>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="modal fade modal-flex" id="price-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Change Price</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 col-lg-12">
						<div class="form-group">
							<label class="form-control-label">Select Department</label>
							<select class="form-control select2" id="department_modal_id" onchange="load_sub_dept()">
								<option value="">Select Department</option>
								@foreach($department as $depart)
									<option value="{{$depart->department_id}}">{{$depart->department_name}}</option>
								@endforeach
							</select>
						</div>
					</div><div class="col-md-12 col-lg-12">
						<div class="form-group">
							<label class="form-control-label">Select Sub Department</label>
							<select class="form-control select2" id="modalsubdept">
								<option value="">Select Sub Department</option>
							</select>
						</div>
					</div>
					<div class="col-md-12 col-lg-12">
						<div class="form-group">
							<label class="form-control-label">Select Mode</label>
							<select class="form-control select2" id="pricemode">
								<option value="">Select Mode</option>
								<option value="1">Percentage Vise</option>
								<option value="2">Amount Vise</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-6">
						<div class="form-group">
							<label class="form-control-label">Retail Price</label>
							<input type="Number" min="1" name="rp" id="rp" class="form-control" placeholder="Enter Percentage or Amount">

						</div>
					</div>
					<div class="col-md-6 col-lg-6">
						<div class="form-group">
							<label class="form-control-label">Wholesale Price</label>
							<input type="Number" min="1" name="wp" id="wp" class="form-control" placeholder="Enter Percentage or Amount">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 col-lg-6">
						<div class="form-group">
							<label class="form-control-label">Discount Price</label>
							<input type="Number" min="1" name="dp" id="dp" class="form-control" placeholder="Enter Percentage or Amount">
						</div>
					</div>
					<div class="col-md-6 col-lg-6">
						<div class="form-group">
							<label class="form-control-label">Online Price</label>
							<input type="Number" min="1" name="op" id="op" class="form-control" placeholder="Enter Percentage or Amount">
						</div>
					</div>
				</div>

				<button type="button" id="btn_desg" class="btn btn-success waves-effect waves-light f-right m-b-10" onClick="Submitprice()">Submit Changes</button>

			</div>

			<div class="modal-footer m-5 m-t-20">
				<label class="f-left f-18 f-w-100 ">NOTES</label>
				<br/>
				<br/>

				<ul style="list-style-type: circle;margin-left: 20px;">
					<li class=" f-12 f-italic f-left" >For example: increase 3000 to 3500 just need to enter 500 .</li>
				</ul>

			</div>

		</div>
	</div>
</div>
