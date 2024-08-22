<div>
    <div class="card">
        <div class="card-header">
        </div>
        <div class="card-block">
            <div class="project-table ">
                <table id="inventtbl" class="table  dt-responsive">
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
                        @if($inventories)
                        @foreach($inventories as $inventory)
                        <tr>
                            <th>
                                <div class="rkmd-checkbox checkbox-rotate">
                                    <label class="input-checkbox checkbox-primary">
                                        <input type="checkbox" id="checkbox32{{$inventory->id}}" class="mainchk" data-id='{{$inventory->id}}'>
                                        <span class="checkbox"></span>
                                    </label>
                                    <div class="captions"></div>
                                </div>
                            </th>
                            <th>{{$inventory->id}}</th>
                            <th>{{$inventory->id}}</th>
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
                        @endforeach
                        @endif
                    </tbody>
                    <div id="tableend" class="text-center"></div>
                </table>
                <br>
                <div class="button-group ">
                    <a style="color:white;" target="_blank" href="{{URL::to('get-export-csv-for-retail-price')}}" class="btn btn-md btn-success waves-effect waves-light f-right"><i class="icofont icofont-file-excel"> </i>
                        Export to Excel Sheet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>