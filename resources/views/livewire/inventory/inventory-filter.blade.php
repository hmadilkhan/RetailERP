<form wire:submit="applyFilters">
    <section class="panels-wells">
        <div class="card">
            <div class=" card-header">
                <div class="button-group col-md-2 f-right">
                    <a style="color:white;" target="_blank" href="{{ URL::to('get-export-csv-for-retail-price') }}"
                        class="btn btn-md btn-success waves-effect waves-light f-right"><i
                            class="icofont icofont-file-excel">
                        </i>
                        Export to Excel Sheet
                    </a>
                </div>
                <div class="button-group col-md-2 mt-2 f-left">
                    <div class="rkmd-checkbox checkbox-rotate">
                        <label class="input-checkbox checkbox-primary">
                            <input type="checkbox" id="Inactive" class="f-left">
                            <span class="checkbox"></span>
                        </label>
                        <div class="captions">Show In-Active Items</div>
                    </div>
                </div>
                <div class="button-group col-md-2 mt-2 f-left">
                    <div class="rkmd-checkbox checkbox-rotate">
                        <label class="input-checkbox checkbox-primary">
                            <input type="checkbox" id="nonstock" class="f-left">
                            <span class="checkbox"></span>
                        </label>
                        <div class="captions">Show Non-Stock Items</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="card-block">
        <div class="col-md-12">
            <div id="ddselect" class="dropdown-secondary dropdown  f-right" style="display: none;">
                <button class="btn btn-default btn-md dropdown-toggle waves-light bg-white b-none txt-muted"
                    type="button" id="dropdown6" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                        class="icofont icofont-navigation-menu"></i> Change
                    Settings</button>
                <div class="dropdown-menu" aria-labelledby="dropdown6" data-dropdown-in="fadeIn"
                    data-dropdown-out="fadeOut">
                    <a class="dropdown-item waves-light waves-effect" id="btn_change_website" data-toggle="modal"
                        data-target="#website-detail-modal"><i class="icofont icofont-company"></i>&nbsp;Link
                        Website</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect" id="btn_change_brand" data-toggle="modal"
                        data-target="#brand-detail-modal"><i class="icofont icofont-company"></i>&nbsp;Link
                        Brand</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect" id="btn_change_tags" data-toggle="modal"
                        data-target="#tags-detail-modal"><i class="icofont icofont-company"></i>&nbsp;Link Tags</a>
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
                    <input class="form-control" type="text" name="code" id="code" wire:model="code"
                        placeholder="Enter Product ItemCode for search" />
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
                    <label class="form-control-label "><i class="icofont icofont-barcode"></i> Department</label>
                    <select class="select2" id="depart" wire:model="dept">
                        <option value="">Select Department</option>
                        @if ($departments)
                            @foreach ($departments as $department)
                                <option value="{{ $department->department_id }}">{{ $department->department_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="col-md-2 col-sm-12">
                <div id="itemcode" class="form-group">
                    <label class="form-control-label "><i class="icofont icofont-barcode"></i>Sub-Department</label>
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
                    <label class="form-control-label "><i class="icofont icofont-barcode"></i> References</label>
                    <select class="select2" id="reference" wire:model="ref">
                        <option value="">Select References</option>
                        @foreach ($references as $reference)
                            <option value="{{ $reference->refrerence }}">{{ $reference->refrerence }}</option>
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
                <button type="button" id="clear" data-placement="bottom"
                    class="btn btn-warning  waves-effect waves-light f-right m-r-10">Clear</button>
                <button type="submit" id="search" data-placement="bottom"
                    class="btn btn-success  waves-effect waves-light f-right m-r-10">Search</button>
            </div>
        </div>

    </div>

</form>
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

            $("#clear").click(function() {
                console.log("clear called");
                Livewire.dispatch("clear");
                @this.refresh();
                @this.set('dept', null);
                @this.set('sdept', null);
                @this.set('ref', null);
            })

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
