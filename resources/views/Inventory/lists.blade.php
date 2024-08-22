@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','View Inventory')
@section('navinventory','active')
@section('navinventorys','active')

@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Upload Inventory</h5>
                <a href="{{ route('create-invent') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Inventory" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE INVENTORY</a>

                <button id="downloadsample" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Download Sample" class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-plus m-r-5" ></i> Download Sample</button>
            </div>
            <div class="card-block">
                <form method='post' action='{{url('uploadInventory')}}' enctype='multipart/form-data'>
                    {{ csrf_field() }}
                      <div class="row col-md-2 " >
                        <div class="form-group">
                            <label for="" class="checkbox-inline">Update to Retail Price</label>
                            <br/>
                            <label for="" class="checkbox-inline">
                                <input type="checkbox" name="update" id="update" class="custom-control">
                            </label>
                            @if ($errors->has('file'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    <div class="row col-md-4 ">
                        <div class="form-group {{ $errors->has('file') ? 'has-danger' : '' }} ">
                            <label for="vdimg" class="form-control-label">Select File </label>
                            <br/>
                            <label for="vdimg" class="custom-file">
                                <input type="file" name="file" id="vdimg" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                            @if ($errors->has('file'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row col-md-2 " >
                        <input type='submit' class="btn btn-primary m-l-5 m-t-35" name='submit' value='Import'>

                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="row">
					<div class="button-group col-md-12">
                        <a style="color:white;" target="_blank" href="{{URL::to('get-export-csv-for-retail-price')}}" class="btn btn-md btn-success waves-effect waves-light f-right" ><i class="icofont icofont-file-excel"> </i>
                          Export to Excel Sheet
                        </a>	
					</div>
                    <div class="col-md-2">
                        <div class="rkmd-checkbox checkbox-rotate">
                            <label class="input-checkbox checkbox-primary">
                                <input type="checkbox" id="Inactive">
                                <span class="checkbox"></span>
                            </label>
                            <div class="captions">Show In-Active Items</div>
                        </div>
						</div>
                    <div class="col-md-2">
                        <div class="rkmd-checkbox checkbox-rotate">
                            <label class="input-checkbox checkbox-primary">
                                <input type="checkbox" id="nonstock">
                                <span class="checkbox"></span>
                            </label>
                            <div class="captions">Show Non-Stock Items</div>
                        </div>
					</div>
					
                    <div class="col-md-12">
                        <div id="ddselect" class="dropdown-secondary dropdown  f-right" style="display: none;">
                            <button class="btn btn-default btn-md dropdown-toggle waves-light bg-white b-none txt-muted" type="button" id="dropdown6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="icofont icofont-navigation-menu"></i> Change Settings</button>
                            <div class="dropdown-menu" aria-labelledby="dropdown6" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                <a class="dropdown-item waves-light waves-effect" id="btn_change_department"><i class="icofont icofont-company"></i>&nbsp;Change Department</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="change_subdept"><i class="icofont icofont-chart-flow-alt-2"></i>&nbsp;Change Sub Department</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="change_uom"><i class="icofont icofont-measure "></i>&nbsp;Change Unit of Measure</a>
                                <div class="dropdown-divider"></div>
								<a class="dropdown-item waves-light waves-effect" id="change_tax"><i class="icofont icofont-measure "></i>&nbsp;Change Tax</a>
                                
								<div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="change_price"><i class="icofont icofont-price"></i>&nbsp;Change Price</a>
								
								<div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" onclick="sunmiCloud()"><i class="icofont icofont-price"></i>&nbsp;Sunmi ESL</a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="btn_activeall"><i class="icofont icofont-ui-check"></i>&nbsp;Active All</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item waves-light waves-effect" id="btn_removeall"><i class="icofont icofont-close-line"></i>&nbsp;Inactive All</a>
								
                                <a class="dropdown-item waves-light waves-effect" id="btn_deleteall"><i class="icofont icofont-close-line"></i>&nbsp;Delete All</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row m-b-20">

                </div>
                <div class="row">
                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search ItemCode</label>
                            <input class="form-control" type="text" name="code" id="code"   placeholder="Enter Product ItemCode for search"/>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search Product</label>
                            <input class="form-control" type="text" name="name" id="name"   placeholder="Enter Product Name for search"/>
                        </div>
                    </div>
					<div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search Price</label>
                            <input class="form-control" type="text" name="retail_price" id="retail_price"   placeholder="Enter Price for search"/>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> Department</label>
                            <select class="select2" id="depart">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 col-sm-12">
                        <div  id="itemcode" class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i>Sub-Department</label>
                            <select class="select2" id="subdepart">
                                <option value="">Select Sub Department</option>
                            </select>
                        </div>
                    </div>
					
					<div class="col-md-2 col-sm-12">
                        <div  class="form-group">
                            <label class="form-control-label "><i class="icofont icofont-barcode"></i> References</label>
                            <select class="select2" id="reference" name="reference">
                                <option value="">Select References</option>
								@foreach($references as $reference)
									<option value="{{$reference->refrerence}}">{{$reference->refrerence}}</option>
								@endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 ">

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button type="button" id="search" data-placement="bottom" class="btn btn-success  waves-effect waves-light f-right m-r-10">Search</button>

                    </div>
                </div>

                <!-- end of dropdown-secondary -->

                <!-- end of dropdown menu -->


            </div>
            <div class="card-block">
                <div class="project-table ">
                    <table id="inventtbl" class="table table-striped table-responsive dt-responsive" >
                        <thead>
                        <tr>
                            <th>
                                <div class="rkmd-checkbox checkbox-rotate">
                                    <label class="input-checkbox checkbox-primary">
                                        <input type="checkbox" id="checkbox32" class="mainchk">
                                        <span class="checkbox"></span>
                                    </label>
                                    <div class="captions"></div>
                                </div>
                            </th>
                            <th>Preview</th>
                            <th>Product Code</th>
                            <th>Name</th>
							<th>Depart</th>
                            <th>Sub-Depart</th>
							<th>Price</th>
							<th>GST%</th>
                            <th>Retail</th>
                            <th>Wholesale</th>
                            <th>Online</th>
							<th>Stock</th>
							<th>UOM</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <div id="tableend" class="text-center"></div>
                    </table>
                    <br>
                    <div class="button-group ">
                        <a style="color:white;" target="_blank" href="{{URL::to('get-export-csv-for-retail-price')}}" class="btn btn-md btn-success waves-effect waves-light f-right" ><i class="icofont icofont-file-excel"> </i>
                          Export to Excel Sheet
                      </a>
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


        </div>
    </section>


@endsection

@section('scriptcode_three')
	<script src="https://cdn.jsdelivr.net/npm/md5-js-tools@1.0.2/lib/md5.min.js"></script>
    <script type="text/javascript">
        // $('#loader-modal').modal("show");
        $(".select2").select2();
        var departments = "";
        var rem_id = [];
        var page = 1;
        var count = 0;
        getData(page);
        $(window).scroll(function() {
            if($("#Inactive").is(":checked")){
                count = count + 1;
                if (count > 4) {

                    page = page + 1;

                    count = 0;
                    if ($('#code').val() != "" || $('#name').val() != "" || $('#retail_price').val() != "" || $('#depart').val() != "" || $('#subdepart').val() != "") {
                        getInactiveInventoryBySearch(page);
                    } else {
                        getInactiveInventory(page);
                    }
                }
            }else if($("#nonstock").is(":checked")){
				// getNonStockInventory(1)
				// count = count + 1;
                // if (count > 4) {

                    // page = page + 1;

                    // count = 0;
                    // if ($('#code').val() != "" || $('#name').val() != "" || $('#retail_price').val() != "" || $('#depart').val() != "" || $('#subdepart').val() != "") {
                        // getNonStockInventory(page);
                    // } else {
                        // getNonStockInventory(page);
                    // }
                // }
			}else {
                count = count + 1;

                if (count > 4) {

                    page = page + 1;

                    count = 0;
                    if ($('#code').val() != "" || $('#name').val() != "" || $('#retail_price').val() != "" || $('#depart').val() != "" || $('#subdepart').val() != "") {
                        getProductByName(page);
                    } else {
                        getData(page);
                    }
                }
            }




        });
        function getData(page)
        {
            $.ajax({
                url: "{{ url('get-inventory-pagewise')}}" + "?page="+page,
                type: 'GET',
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // console.log(resp);
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    // $('#loader-modal').modal("hide");
                    tableData(resp)


                }

            });
        }
		
		function tableData(resp)
		{
			if(resp.data.length > 0){
				$.each(resp.data, function( index, value ) {
				var stock = parseFloat(value.stock).toFixed(2);
				stock = (stock == "NaN" ? 0.00 : stock);
				$("#inventtbl tbody").append(
					"<tr>"+
					"<td>" +
					"<div class='rkmd-checkbox checkbox-rotate'>"+
						"<label class='input-checkbox checkbox-primary'>"+
						"<input type='checkbox' id='checkbox32"+value.id+"' class='chkbx' onclick='chkbox(this.id)' data-id='"+value.id+"'>"+
						"<span class='checkbox'></span>"+
					"</label>"+
					"<div class='captions'></div>"+
					"</td>"+
					"<td>" +
					"<a href='{{ asset('public/assets/images/products/')}}"+(value.product_image == "" ? "/placeholder.jpg" : "/"+value.product_image)+"' data-toggle='lightbox'  data-footer=''>"+
					"<img width='12' height='12' data-modal='modal-12' src='{{ asset('public/assets/images/products/')}}"+(value.product_image == "" ? "/placeholder.jpg" : "/"+value.product_image)+"' class='d-inline-block img-circle ' alt=''>"+
					"</a>"+
					"</td>"+
					"<td>"+value.item_code  +"</td>"+
					"<td>"+value.product_name+"</td>"+
					"<td>"+value.department_name+"</td>"+
					"<td>"+value.sub_depart_name+"</td>"+
					"<td>"+value.actual_price+"</td>"+
					"<td>"+(value.tax_rate == null ? 0.00 : value.tax_rate)+"</td>"+
					"<td>"+value.retail_price+"</td>"+
					"<td>"+value.wholesale_price+"</td>"+
					"<td>"+value.online_price+"</td>"+
					"<td>"+stock+"</td>"+
					"<td>"+value.name+"</td>"+
					"<td>" +
					"<a onclick='show_barcode(\""+value.item_code+"\",\""+value.product_name+"\","+value.retail_price+")' class='p-r-10 f-18 text-success' data-toggle='tooltip' data-placement='top' title='Print Barcode' data-original-title='Barcode'><i class='icofont icofont-barcode'></i></a>"+
						 "<a onclick='edit_route(\""+value.slug+"\")' class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i class='icofont icofont-ui-edit'></i></a>"+
						"<i class='icofont icofont-ui-delete text-danger f-18 ' onclick='deleteCall("+value.id+")' data-id='"+value.id+"' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
						"&nbsp;<i onclick='assignToVendorModal(\""+ value.id+"\")' class='icofont icofont icofont-business-man #3A6EFF' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign To Vendors'></i>"+
					"</td>"+
					"</tr>"
				);
			});
			}
			else{
				$("#tableend").empty();
				$("#tableend").append("")
			}
		}

        function edit_route(id)
        {
			let location = "{{url('edit-invent')}}" + "/"+id;
			window.open(location)
            // window.location = "{{url('edit-invent')}}" + "/"+id;
        }

        $("#search").click(function(){
            page = 1;
            $("#inventtbl tbody").empty();
            if($("#Inactive").is(":checked")){
                getInactiveInventoryBySearch(page);
			}else if($("#nonstock").is(":checked")){
				getNonStockInventory(page);
            }else{
                getProductByName(page);
            }


        });

        function  getProductByName(page) {
			console.log("Calling")
            $.ajax({
                url: "{{ url('get-inventory-by-name')}}"+ "?page="+page,
                type: 'GET',
                data:{code:$('#code').val(),name:$('#name').val(),rp:$('#retail_price').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
                dataType:"json",
                success:function(resp){
  
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    tableData(resp)

                }

            });
        }

        $('#Inactive').click(function(){
            if ($("#Inactive").is(":checked")) {
				$("#nonstock").prop('checked',false)
                $("#inventtbl tbody").empty();
                page = 1;
                getInactiveInventory(page)
            }else{
                $("#inventtbl tbody").empty();
                page = 1;
                getData(page);
            }
        });
		
		$('#nonstock').click(function(){
            if ($("#nonstock").is(":checked")) {
				$("#Inactive").prop('checked',false)
                $("#inventtbl tbody").empty();
                page = 1;
                getNonStockInventory(page)
            }else{
                $("#inventtbl tbody").empty();
                page = 1;
                getData(page);
            }
        });

        function getInactiveInventory(page)
        {

            $.ajax({
                url: "{{ url('get-inactive-inventory')}}" + "?page="+page,
                type: 'GET',
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // $('#loader-modal').modal("hide");
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    tableData(resp)
                }

            });
        }
		
		function getNonStockInventory(page)
        {

            $.ajax({
                url: "{{ url('get-non-stock-inventory')}}" ,//+ "?page="+page
                type: 'GET',
				data:{code:$('#code').val(),name:$('#name').val(),rp:$('#retail_price').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // $('#loader-modal').modal("hide");
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    if(resp.length > 0){
                        $.each(resp, function( index, value ) {
						var stock = parseFloat(value.stock).toFixed(2);
						stock = (stock == "NaN" ? 0.00 : stock);
                            $("#inventtbl tbody").append(
                                "<tr>"+
                                "<td>" +
                                "<div class='rkmd-checkbox checkbox-rotate'>"+
                                "<label class='input-checkbox checkbox-primary'>"+
                                "<input type='checkbox' id='checkbox32"+value.id+"' class='chkbx' onclick='chkbox(this.id)' data-id='"+value.id+"'>"+
                                "<span class='checkbox'></span>"+
                                "</label>"+
                                "<div class='captions'></div>"+
                                "</td>"+
                                "<td>" +
                                "<a href='{{ asset('public/assets/images/products/')}}"+(value.product_image == "" ? "/placeholder.jpg" : +"/"+value.product_image)+"' data-toggle='lightbox'  data-footer=''>"+
                                "<img width='12' height='12' data-modal='modal-12' src='{{ asset('public/assets/images/products/')}}"+(value.product_image == "" ? "/placeholder.jpg" : "/"+value.product_image)+"' class='d-inline-block img-circle ' alt=''>"+
                                "</a>"+
                                "</td>"+
                                "<td>"+value.item_code+"</td>"+
                                "<td>"+value.product_name+"</td>"+
								 "<td>"+value.department_name+"</td>"+
                                "<td>"+value.sub_depart_name+"</td>"+
								"<td>"+value.actual_price+"</td>"+
								"<td>"+(value.tax_rate == null ? 0.00 : value.tax_rate)+"</td>"+
                                "<td>"+value.retail_price+"</td>"+
                                "<td>"+value.wholesale_price+"</td>"+
                                "<td>"+value.online_price+"</td>"+
								"<td>"+stock+"</td>"+
                                "<td>"+value.name+"</td>"+
                                "<td>" +
								"<a onclick='edit_route(\""+value.slug+"\")' class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i class='icofont icofont-ui-edit'></i></a>"+
                                "</td>"+
                                "</tr>"
                            );
                        });
                    }
                    else{
                        $("#tableend").empty();
                        $("#tableend").append("")
                    }


                }

            });
        }

        function getInactiveInventoryBySearch(page)
        {

            $.ajax({
                url: "{{ url('get-inactive-inventory-by-search')}}" + "?page="+page,
                type: 'GET',
                data:{code:$('#code').val(),name:$('#name').val(),dept:$('#depart').val(),sdept:$('#subdepart').val(),ref:$('#reference').val()},
                dataType:"json",
                async : 'false',
                beforeSend:function(){
                    // $('#loader-modal').modal("show");
                },
                success:function(resp){
                    // $('#loader-modal').modal("hide");
                    if(page == 1){
                        $("#inventtbl tbody").empty();
                    }
                    tableData(resp)


                }

            });
        }


        function load_department()
        {
            $.ajax({
                url: "{{ url('get_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddldepartment').empty();
                    $("#ddldepartment").append("<option value=''>Select Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddldepartment").append(
                            "<option value="+value.department_id+">"+value.department_name+"</option>"
                        );
                    });

                }

            });
        }

        function load_uom()
        {
            $.ajax({
                url: "{{ url('get_uom')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddluom').empty();
                    $("#ddluom").append("<option value=''>Select Unit of Measure</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddluom").append(
                            "<option value="+value.uom_id+">"+value.name+"</option>"
                        );
                    });

                }

            });
        }
		
		function load_taxes()
        {
            $.ajax({
                url: "{{ url('get_taxes')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddltax').empty();
                    $("#ddltax").append("<option value=''>Select Tax</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddltax").append(
                            "<option value="+value.tax_rate+">"+value.tax_rate+"</option>"
                        );
                    });

                }

            });
        }

        function load_sub_dept(id)
        {
            $.ajax({
                url: "{{ url('get_sub_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:$("#department_modal_id").val()},
                success:function(resp){

                    $('#modalsubdept').empty();
                    $("#modalsubdept").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#modalsubdept").append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });

                }

            });
        }
		
		function load_subdept(id)
        {
            $.ajax({
                url: "{{ url('get_sub_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:id},
                success:function(resp){

                    $('#ddlsubdept').empty();
                    $("#ddlsubdept").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddlsubdept").append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });

                }

            });
        }


        $('#btnDptSave').click(function(){

            if ($('#ddldepartment').val() == "")
            {
                swal("Cancelled", "Please Select Department :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ url('update_product_department')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,deptId:$('#ddldepartment').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change department. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });


        $('#btnUomSave').click(function(){

            if ($('#ddluom').val() == "")
            {
                swal("Cancelled", "Please Select Unit of Measure :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ url('update_product_uom')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,uomId:$('#ddluom').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change Unit of Measure. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });
		
		$('#btnTaxSave').click(function(){
			if ($('#ddltax').val() == "")
            {
                swal("Cancelled", "Please Select Tax :)", "error");
            }
			else if($('#tax_rate_new').val() == ""){
				swal("Cancelled", "Please Enter New Tax :)", "error");
			}else{
				$('#btnTaxSave').prop('disabled',true);
				$.ajax({
                    url: "{{ url('update_product_tax')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",prev_tax:$('#ddltax').val(),new_tax:$('#tax_rate_new').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
							$('#btnTaxSave').prop('disabled',false);
                            swal("Cancelled", "Cannot change tax. An error occured :)", "error");
                        }
                    }

                });
			}
		});

        $('#btnsubdeptSave').click(function(){

            if ($('#ddlsubdept').val() == "")
            {
                swal("Cancelled", "Please Select Sub Department :)", "error");
            }
            else
            {

                $(".chkbx").each(function( index ) {
                    if($(this).is(":checked")){
                        if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                            rem_id.push($(this).data('id'));
                        }
                    }
                });

                $.ajax({
                    url: "{{ url('update_product_subdepartment')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",inventid:rem_id,subdeptId:$('#ddlsubdept').val(),deptID:$('#ddldepartment1').val()},
                    success:function(resp){
                        if (resp == 1)
                        {
                            window.location = "{{url('inventory-list')}}"
                        }
                        else
                        {
                            swal("Cancelled", "Cannot change Sub Department. An error occured :)", "error");
                        }
                    }

                });//ajax end
            }//else end
        });

        //light box
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
            event.preventDefault();
            $(this).ekkoLightbox();

        });



        function deleteCall(id){
            // var id= $(this).data("id");
            swal({
                    title: "Are you sure?",
                    text: "This item will mark as inactive and will not be further available for sales!",
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
                            url: "{{ url('delete-invent')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id,status:2},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Product Successfully Inactive.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            page = 1;
                                            if ($('#code').val() != "" || $('#name').val() != "" || $('#depart').val() != "" || $('#subdepart').val() != "") {
                                                getProductByName(page);
                                            } else {
                                                getData(page);
                                            }

                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your product is safe :)", "error");
                    }
                });
        }

        function item_inactive(id){

            swal({
                    title: "Are you sure?",
                    text: "This item will be the part of inventory again !!!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Active it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{ url('delete-invent')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id,status:1},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "Activated",
                                        text: "Product activated Successfully .",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('inventory-list') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });
        }


        function activeCall(id){
            // var id= $(this).data("id");
            swal({
                    title: "Are you sure?",
                    text: "This item will be active and will now be available for sales!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Activate it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{ url('delete-invent')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id,status:1},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "Activated",
                                        text: "Product Successfully Active.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            page = 1;
                                            getInactiveInventory(page);

                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your product is still inactive :)", "error");
                    }
                });
        }


        $(".mainchk").on('click',function(){

            if($(this).is(":checked")){
                // $("#btn_removeall").removeClass('invisible');
                // $("#btn_change_department").removeClass('invisible');
                // $("#change_uom").removeClass('invisible');
                // $("#change_subdept").removeClass('invisible');

                // $("#btn_removeall").css("display", "block");
                // $("#btn_change_department").css("display", "block");
                // $("#change_uom").css("display", "block");
                // $("#change_subdept").css("display", "block");
                $("#ddselect").css("display", "block");


                $(".chkbx").each(function( index ) {

                    $(this).attr("checked",true);
                });

            }else {
                // $("#btn_removeall").addClass('invisible');
                // $("#btn_change_department").addClass('invisible');
                // $("#change_uom").addClass('invisible');
                // $("#change_subdept").addClass('invisible');
                $("#ddselect").css("display", "none");
                // $("#btn_removeall").css("display", "none");
                // $("#btn_change_department").css("display", "none");
                // $("#change_uom").css("display", "none");
                // $("#change_subdept").css("display", "none");

                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",false);
                });
            }

        });



        // $(".chkbx").on('click',function(){
        function chkbox(id) {
            if ($("#"+id).is(":checked")) {

                // $("#btn_removeall").removeClass('invisible');
                // $("#btn_change_department").removeClass('invisible');
                // $("#change_uom").removeClass('invisible');
                // $("#change_subdept").removeClass('invisible');

                // $("#btn_removeall").css("display", "block");
                // $("#btn_change_department").css("display", "block");
                // $("#change_uom").css("display", "block");
                // $("#change_subdept").css("display", "block");

                $("#ddselect").css("display", "block");

            } else {
                alert();
                // $("#btn_removeall").addClass('invisible');
                // $("#btn_change_department").addClass('invisible');
                // $("#change_uom").addClass('invisible');
                // $("#change_subdept").addClass('invisible');

                // $("#btn_removeall").css("display", "none");
                // $("#btn_change_department").css("display", "none");
                // $("#change_uom").css("display", "none");
                // $("#change_subdept").css("display", "none");

                $("#ddselect").css("display", "none");

            }
        }

        // });

        $(".subchk").on('click',function(){

            if($(this).is(":checked")){
                // $("#btn_activeall").removeClass('invisible');
                $("#btn_activeall").css("display", "block");

                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",true);
                });

            }else {
                // $("#btn_activeall").addClass('invisible');
                $("#btn_activeall").css("display", "none");
                $(".chkbx").each(function( index ) {
                    $(this).attr("checked",false);
                });
            }

        });


        $(".chkbx").on('click',function(){
            if($(this).is(":checked")){
                // $("#btn_activeall").removeClass('invisible');
                $("#btn_activeall").css("display", "block");

            }
            else {
                // $("#btn_activeall").addClass('invisible');
                $("#btn_activeall").css("display", "none");
            }
        });

        $("#btn_activeall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {

                if($(this).is(":checked")){
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));

                    }
                }

            });
			// console.log(rem_id)
            $.ajax({
                url: "{{url('/get_names')}}",
                type: "POST",
                data: {_token:"{{csrf_token()}}",ids:rem_id},
                async:false,
                success:function(resp){
                    for(var s=0;s < resp.length ;s++){
                        products.push(resp[s].product_name);
                    }
                }
            });

            var names = products.join();

            swal({
                title: "RE-ACTIVE",
                text: "Do you want to activate  "+names+" this items?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){

                        $.ajax({
                            url: "{{url('/multiple-active-invent')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products activated Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/inventory-list')}}";
                                        }
                                    });

                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }

                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are safe:)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{url('/inventory-list')}}";
                            // $('#pro').removeClass("active");
                            // $('#act').addClass("active");
                        }
                    });

                }

            });


        });

        message("{{session('message')}}");
        function message(message)
        {
            if (message == 1)
            {
                notify("Import Successful", "success")
            }
            else if(message == 2)
            {
                notify("File too large. File must be less than 2MB.", "danger")
            }
            else if(message == 3)
            {
                notify("Invalid File Extension.", "danger")
            }
        }

        //Welcome Message (not for login page)
        function notify(message, type) {
            $.growl({
                message: message
            }, {
                type: type,
                allow_dismiss: true,
                label: 'Cancel',
                className: 'alert-success btn-primary',
                placement: {
                    from: 'top',
                    align: 'center'
                },
                delay: 3000,
                animate: {
                    enter: 'animated flipInX',
                    exit: 'animated flipOutX'
                },
                offset: {
                    x: 30,
                    y: 30
                }
            });
        };
        $("#btn_change_department").on('click',function(){
            load_department();
            $('#details-modal').modal("show");

            $(".chkbx").each(function( index ) {

                if($(this).is(":checked")){
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }
            });

        });

        $('#change_price').click(function () {
            $('#price-modal').modal("show");
        });
		
		$('#sunmi_cloud').click(function () {
            $('#sunmi-coud-modal').modal("show");
        });

        $("#change_uom").on('click',function(){
            load_uom();
            $('#details-uom').modal("show");
        });
		$("#change_tax").on('click',function(){
            load_taxes();
            $('#change-tax-modal').modal("show");
        });

        $('#ddldepartment1').change(function(){
            load_subdept($('#ddldepartment1').val());
        });

        $("#change_subdept").on('click',function(){

            $.ajax({
                url: "{{ url('get_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){

                    $('#ddldepartment1').empty();
                    $("#ddldepartment1").append("<option value=''>Select Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#ddldepartment1").append(
                            "<option value="+value.department_id+">"+value.department_name+"</option>"
                        );
                    });

                }

            });
            $('#details-subdept').modal("show");
        });

        $("#btn_removeall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {
                if($(this).is(":checked")){
					// console.log($(this).data('id'))
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }

            });
			// console.log(rem_id)
            $.ajax({
                url: "{{url('/get_names')}}",
                type: "POST",
                data: {_token:"{{csrf_token()}}",ids:rem_id},
                async:false,
                success:function(resp){
                    for(var s=0;s < resp.length ;s++){
                        products.push(resp[s].product_name);
                    }
                }
            });

            var names = products.join();

            swal({
                title: "INACTIVE PRODUCTS",
                text: "Do you want to inactive  "+names+" ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){
                        $.ajax({
                            url: "{{url('/all_invent_remove')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id,statusid:2},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products In-Active Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/inventory-list')}}";
                                        }
                                    });

                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }

                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{url('/inventory-list')}}";

                        }
                    });

                }

            });


        });
		
		$("#btn_deleteall").on('click',function(){
            var products = [];
            $(".chkbx").each(function( index ) {
                if($(this).is(":checked")){
					// console.log($(this).data('id'))
                    if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                        rem_id.push($(this).data('id'));
                    }
                }
            });

            swal({
                title: "DELETE PRODUCTS",
                text: "Do you want to delete products ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){

                    if(rem_id.length > 0){
                        $.ajax({
                            url: "{{url('/all_invent_delete')}}",
                            type: "POST",
                            data: {_token:"{{csrf_token()}}",inventid:rem_id},
                            success:function(resp){

                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products deleted Successfully :)",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/inventory-list')}}";
                                        }
                                    });
                                }else{
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }
                            }

                        });
                    }

                }else{
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{url('/inventory-list')}}";

                        }
                    });
                }
            });
        });

//'https://sabsoft.com.pk//Retail/public/assets/samples/sample_inventory.csv',
        $('#downloadsample').click(function(){
						window.open("https://sabsoft.com.pk//Retail/public/assets/samples/sample_inventory.csv");
                        // $.ajax({
                            // url: '{{ asset('public/assets/samples/sample_inventory.csv')}}',
                            // method: 'GET',
                            // xhrFields: {
                                // responseType: 'blob'
                            // },
                            // success: function (data) {
                                // var a = document.createElement('a');
                                // var url = window.URL.createObjectURL(data);
                                // a.href = url;
                                // a.download = 'sample_inventory.csv';
                                // document.body.append(a);
                                // a.click();
                                // a.remove();
                                // window.URL.revokeObjectURL(url);
                            // }
                        // });
                    });
					
					
					
					

                    function Submitprice() {

                        $(".chkbx").each(function( index ) {
                            if($(this).is(":checked")){
                                if(jQuery.inArray($(this).data('id'), rem_id) == -1){
                                    rem_id.push($(this).data('id'));
                                }
                            }
                        });


                        if ($('#pricemode').val() == "") {
                            swal({
                                title: "Error Message!",
                                text: "Please Select Mode First!",
                                type: "error"
                            });
                        }
                        else{
                            $.ajax({
                                url: "{{url('/insertnewprice')}}",
                    type: "POST",
                    data: {_token:"{{csrf_token()}}",
                        productid:rem_id,
						departmentId : $('#department_modal_id').val(),
						subDepartmentId : $('#modalsubdept').val(),
                        rp:$('#rp').val(),
                        wp:$('#wp').val(),
                        dp:$('#dp').val(),
                        op:$('#op').val(),
                        pricemode:$('#pricemode').val(),
                    },
                    success:function(resp){
                        // console.log(resp);
                        swal({
                            title: "Success!",
                            text: "Price Change Successfully!",
                            type: "success"
                        });
                        $('#price-modal').modal('hide');
                        window.location="{{url('/inventory-list')}}";
                    }

                });
            }




        }
        loadDepartment();

        function loadDepartment() {
            $.ajax({
                url: "{{ url('get_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}"},
                success:function(resp){
                    $('#depart').empty();
                    $("#depart").append("<option value=''>Select Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#depart").append(
                            "<option value="+value.department_id+">"+value.department_name+"</option>"
                        );
                    });

                }

            });
        }

        $('#depart').change(function (e) {

            $.ajax({
                url: "{{ url('get_sub_departments')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",id:$("#"+this.id).val()},
                success:function(resp){
                    $('#subdepart').empty();
                    if(resp != 0){
                        $("#subdepart").append("<option value='all'>Select Sub Department</option>");
                        $.each(resp, function( index, value ) {
                            $("#subdepart").append(
                                "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                            );
                        });
                    }else{
                        $("#subdepart").append(
                            "<option value='0'>No Data Found</option>"
                        );
                    }


                }

            });
        })

        function show_barcode(code,name,price) {
            // EMPTY CONTROLS
            $('#label_code').val('');
            $('#label_name').val('');
            $('#label_price').val('');

            // SET VALUE TO CONTROLS
            $('#label_code').val(code);
            $('#label_name').val(name);
            $('#label_price').val(price);

            $('#label-modal').modal("show");
{{--            window.location = "{{url('label')}}" + "?code="+code+"&name="+name+"&price="+price;--}}
        }

        function print_barcode() {
            var url = $('#labelsize').val() +""+ $('#labelpattern').val();

            $.ajax({
                url: "{{ url('printBarcode')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",url:url,code:$('#label_code').val(),name:$('#label_name').val(),price:$('#label_price').val(),margin1:$('#name_margin1').val(),margin2:$('#name_margin2').val(),printheader:$("#printheader").val()},
                success:function(resp){
                    window.open(resp);
                }

            });


        }
		
		function assignToVendorModal(productId)
		{
		  $('#sp-modal').modal("show");
		  $("#productidforvendors").val(productId);
		}
		
		$("#btn_assign").click(function(){
			var vendor = $("#vendor").val();
			var productId = $(productidforvendors).val();
			if(vendor == ""){
					alert("Please Select Vendors")
			  }else{
				  $.ajax({
					url : "{{url('/assign-product-to-vendors')}}",
					type : "POST",
					data : {_token : "{{csrf_token()}}", productId:productId,vendors:vendor},
					dataType : 'json',
					success : function(result){
						// console.log(result);
						if(result == 1){
							$('#sp-modal').modal("hide");
							// location.reload();
						}
					}
				});
			  }
		});

		function sunmiCloud(){
			$(".chkbx").each(function( index ) {
				if($(this).is(":checked")){
					if(jQuery.inArray($(this).data('id'), rem_id) == -1){
						rem_id.push($(this).data('id'));
					}
				}
			});
			
			$.ajax({
				url: "{{url('/sunmi-cloud')}}",
				type: "POST",
				data: {_token:"{{csrf_token()}}",
					inventory:rem_id,
				},
				success:function(resp){
					// console.log(resp);
					
						sendToSunmi(resp)
					
					// swal({
						// title: "Success!",
						// text: "Price Change Successfully!",
						// type: "success"
					// });
					// window.location="{{url('/inventory-list')}}";
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
			// console.log("PRODUCT LIST:",productList);
			let random = generateString(7);
			// console.log("RANDOM:",random);

			let timestamp = getCurrentUnixTimestamp();
			// console.log("TIMESTAMP:",timestamp);
			
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
			  }
			});
		}
    </script>

@endsection

