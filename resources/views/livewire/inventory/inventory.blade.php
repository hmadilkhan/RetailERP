<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Upload Inventory</h5>
            <a href="{{ route('create-invent') }}" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Create Inventory"
                class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i
                    class="icofont icofont-plus m-r-5"></i> CREATE INVENTORY</a>

            <a href="{{ url('get-sample-csv') }}" data-toggle="tooltip" data-placement="bottom" title=""
                data-original-title="Download Sample"
                class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i
                    class="icofont icofont-plus m-r-5"></i> Download Sample</a>
        </div>
        <div class="card-block">
            <livewire:Inventory.inventory-upload wire:key="{{ str()->random(10) }}" />
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            {{-- wire:submit="applyFilters" --}}
            <form > 
                <section class="panels-wells">
                    {{-- <div class="card"> --}}
                    <div class="card-header">
                        <div class="button-group col-md-2 f-right">
                            <a style="color:white;" target="_blank"
                                href="{{ URL::to('get-export-csv-for-retail-price') }}"
                                class="btn btn-md btn-success waves-effect waves-light f-right"><i
                                    class="icofont icofont-file-excel">
                                </i>
                                Export to Excel Sheet
                            </a>
                        </div>
                        <div class="button-group col-md-2 mt-2 f-left">
                            <div class="rkmd-checkbox checkbox-rotate">
                                <label class="input-checkbox checkbox-primary">
                                    <input type="checkbox" id="Inactive" class="f-left" wire:model="inactiveChecked"
                                        wire:change="applyFilters()" wire:loading.attr="disabled">
                                    <span class="checkbox"></span>
                                </label>
                                <div class="captions">Show In-Active Items</div>
                            </div>
                        </div>
                        <div class="button-group col-md-2 mt-2 f-left">
                            <div class="rkmd-checkbox checkbox-rotate">
                                <label class="input-checkbox checkbox-primary">
                                    <input type="checkbox" id="nonstock" class="f-left" wire:model="nonstockChecked"
                                        wire:change="applyFilters()" wire:loading.attr="disabled">
                                    <span class="checkbox"></span>
                                </label>
                                <div class="captions">Show Non-Stock Items</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div id="ddselect" class="dropdown-secondary dropdown  f-right" style="display: none;">
                                <button
                                    class="btn btn-default btn-md dropdown-toggle waves-light bg-white b-none txt-muted"
                                    type="button" id="dropdown6" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"><i class="icofont icofont-navigation-menu"></i> Change
                                    Settings</button>
                                <div class="dropdown-menu" aria-labelledby="dropdown6" data-dropdown-in="fadeIn"
                                    data-dropdown-out="fadeOut">
                                    <a class="dropdown-item waves-light waves-effect" id="btn_change_website"
                                        data-toggle="modal" data-target="#website-detail-modal"><i
                                            class="icofont icofont-company"></i>&nbsp;Link
                                        Website</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="btn_change_brand"
                                        data-toggle="modal" data-target="#brand-detail-modal"><i
                                            class="icofont icofont-company"></i>&nbsp;Link
                                        Brand</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="btn_change_tags"
                                        data-toggle="modal" data-target="#tags-detail-modal"><i
                                            class="icofont icofont-company"></i>&nbsp;Link
                                        Tags</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="btn_change_department"><i
                                            class="icofont icofont-company"></i>&nbsp;Change Department</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="change_subdept"><i
                                            class="icofont icofont-chart-flow-alt-2"></i>&nbsp;Change Sub Department</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="change_uom"><i
                                            class="icofont icofont-measure "></i>&nbsp;Change Unit of Measure</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="change_tax"><i
                                            class="icofont icofont-measure "></i>&nbsp;Change Tax</a>

                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="change_price"><i
                                            class="icofont icofont-price"></i>&nbsp;Change Price</a>

                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" onclick="sunmiCloud()"><i
                                            class="icofont icofont-price"></i>&nbsp;Sunmi ESL</a>

                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="btn_activeall"><i
                                            class="icofont icofont-ui-check"></i>&nbsp;Active All</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect" id="btn_removeall"><i
                                            class="icofont icofont-close-line"></i>&nbsp;Inactive All</a>

                                    <a class="dropdown-item waves-light waves-effect" id="btn_deleteall"><i
                                            class="icofont icofont-close-line"></i>&nbsp;Delete All</a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <div id="itemcode" class="form-group">
                                    <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                        ItemCode</label>
                                    <input class="form-control" type="text" name="code" id="code"
                                        wire:model="code" placeholder="Enter Product ItemCode for search" />
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <div id="itemcode" class="form-group">
                                    <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                        Product</label>
                                    <input class="form-control" type="text" id="name" wire:model="name"
                                        placeholder="Enter Product Name for search" />
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <div id="itemcode" class="form-group">
                                    <label class="form-control-label "><i class="icofont icofont-barcode"></i> Search
                                        Price</label>
                                    <input class="form-control" type="text" wire:model="rp" id="retail_price"
                                        placeholder="Enter Price for search" />
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12">
                                <div id="itemcode" class="form-group">
                                    <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                        Department</label>
                                    <select class="select2" id="depart" wire:model="dept">
                                        <option value="">Select Department</option>
                                        @if ($departments)
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->department_id }}">
                                                    {{ $department->department_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <div id="itemcode" class="form-group">
                                    <label class="form-control-label "><i
                                            class="icofont icofont-barcode"></i>Sub-Department</label>
                                    <select class="select2" id="subdepart" wire:model="sdept">
                                        <option value="">Select Sub Department</option>
                                        @if ($subDepartments)
                                            @foreach ($subDepartments as $subDepartment)
                                                <option value="{{ $subDepartment->sub_department_id }}">
                                                    {{ $subDepartment->sub_depart_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label class="form-control-label "><i class="icofont icofont-barcode"></i>
                                        References</label>
                                    <select class="select2" id="reference" wire:model="ref">
                                        <option value="">Select References</option>
                                        @foreach ($references as $reference)
                                            <option value="{{ $reference->refrerence }}">{{ $reference->refrerence }}
                                            </option>
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
                                <button type="button" data-placement="bottom"
                                    class="btn btn-warning  waves-effect waves-light f-right m-r-10"
                                    wire:click="clear()">Clear</button>
                                <button type="button" data-placement="bottom"
                                    class="btn btn-success  waves-effect waves-light f-right m-r-10"
                                    wire:click="applyFilters()">Search</button>
                            </div>
                        </div>

                    </div>
                    {{-- </div> --}}
                </section>
            </form>
        </div>
        <div class="card-block">
            <section>
                <div wire:loading.class="d-flex flex-column" wire:loading>
                    <div
                        class='position-relative w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>
                        <div class='spinner-border text-dark' role='status'>
                            <span class='visually-hidden'>Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="project-table">
                    <table wire:loading.remove id="inventtbl"
                        class="table table-striped nowrap dt-responsive m-t-10 dataTable no-footer dtr-inline">
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
                                <th>Code</th>
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
                            @foreach ($inventories as $inventory)
                                <tr>
                                    <td>
                                        <div class='rkmd-checkbox checkbox-rotate'>
                                            <label class='input-checkbox checkbox-primary'>
                                                <input type='checkbox' id='checkbox32{{ $inventory->id }}'
                                                    class='chkbx' onclick='chkbox("checkbox32{{ $inventory->id }}")'
                                                    data-id='{{ $inventory->id }}'>
                                                <span class='checkbox'></span>
                                            </label>
                                            <div class='captions'></div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ asset('storage/images/products/' . ($inventory->product_image == '' ? '/placeholder.jpg' : $inventory->product_image)) }}"
                                            data-toggle="lightbox" data-footer=''>
                                            <img width="12" height="12" data-modal="modal-12"
                                                src="{{ asset('storage/images/products/' . ($inventory->product_image == '' ? '/placeholder.jpg' : $inventory->product_image)) }}"
                                                class='d-inline-block img-circle ' alt=''>
                                        </a>
                                    </td>
                                    <td>{{ $inventory->item_code }}</td>
                                    <td>{{ $inventory->product_name }}</td>
                                    <td>{{ $inventory->department_name }}</td>
                                    <td>{{ $inventory->sub_depart_name }}</td>
                                    <td>{{ $inventory->actual_price }}</td>
                                    <td>{{ $inventory->tax_rate == null ? 0.0 : $inventory->tax_rate }}</td>
                                    <td>{{ $inventory->retail_price }}</td>
                                    <td>{{ $inventory->wholesale_price }}</td>
                                    <td>{{ $inventory->online_price }}</td>
                                    <td>{{ $inventory->stock }}</td>
                                    <td>{{ $inventory->name }}</td>
                                    <td>
                                        <a onclick='show_barcode("{{ $inventory->item_code }}","{{ $inventory->product_name }}","{{ $inventory->retail_price }}")'
                                            class='p-r-10 f-18 text-success' data-toggle='tooltip'
                                            data-placement='top' title='Print Barcode'
                                            data-original-title='Barcode'><i class='icofont icofont-barcode'></i></a>
                                        <a onclick='edit_route("{{ $inventory->slug }}")'
                                            class='p-r-10 f-18 text-warning' data-toggle='tooltip'
                                            data-placement='top' title='' data-original-title='Edit'><i
                                                class='icofont icofont-ui-edit'></i></a>
                                        <i class='icofont icofont-ui-delete text-danger f-18 '
                                            onclick='deleteCall("{{ $inventory->id }}")' data-id='value.id'
                                            data-toggle='tooltip' data-placement='top' title=''
                                            data-original-title='Delete'></i>
                                        &nbsp;<i
                                            onclick='assignToVendorModal("{{ $inventory->id }}") class="icofont icofont icofont-business-man #3A6EFF" data-toggle='tooltip'
                                            data-placement='top' title=''
                                            data-original-title='Assign To Vendors'></i>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $inventories->links() }}
                <br>
                <div class="button-group ">
                    <a style="color:white;" target="_blank" href="{{ URL::to('get-export-csv-for-retail-price') }}"
                        class="btn btn-md btn-success waves-effect waves-light f-right"><i
                            class="icofont icofont-file-excel"> </i>
                        Export to Excel Sheet
                    </a>
                </div>
            </section>
        </div>
    </div>
</section>
@script
    <script>
        $(document).ready(function() {
            $(".select2").select2();

            $('#depart').on('change', function(e) {
                var data = $('#depart').select2("val");
                @this.set('dept', data);
            });

            $('#subdepart').on('change', function(e) {
                var data = $('#subdepart').select2("val");
                @this.set('sdept', data);
            });

            $('#reference').on('change', function(e) {
                var data = $('#reference').select2("val");
                @this.set('ref', data);
            });

            Livewire.hook('morph.updating', ({
                component,
                cleanup
            }) => {
                $('#depart').select2();
                $('#subdepart').select2();
                $('#reference').select2();
            })
        });
    </script>
@endscript
