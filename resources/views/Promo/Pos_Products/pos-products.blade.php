@extends('layouts.master-layout')

@section('title','POS Products')

@section('breadcrumtitle','POS Products')

@section('navpos','active')



@section('content')

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create POS Products</h5>
                <a href="#" class="f-right" onclick="toggle()">
                    Collapse
{{--                    <i class="icofont icofont-minus"></i>Collapse--}}
                </a>
            </div>
            <div class="card-block" id="insert-card">

                <form method="post" id="upload_form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
{{--                        <div class="col-lg-4 col-md-4">--}}
{{--                            <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }} ">--}}
{{--                                <label class="form-control-label">Branch</label>--}}

{{--                                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2">--}}
{{--                                    <option value="">Select Branch</option>--}}
{{--                                    @if($getbranch)--}}
{{--                                        @foreach($getbranch as $value)--}}
{{--                                            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    @endif--}}
{{--                                </select>--}}
{{--                                @if ($errors->has('branch'))--}}
{{--                                    <div class="form-control-feedback">Required field can not be blank.</div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('finishgood') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Product</label>
                                <select name="finishgood" id="finishgood" data-placeholder="Select Product" class="form-control select2"  >
                                    <option value="">Select Product</option>
                                    @if($getfinishgood)
                                        @foreach($getfinishgood as $value)
                                            <option value="{{ $value->id }}">{{ $value->item_code }} | {{ $value->product_name }} | {{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('finishgood'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div  id="itemcode" class="form-group {{ $errors->has('code') ? 'has-danger' : '' }} ">
                                <label class="form-control-label"><i class="icofont icofont-barcode"></i>&nbsp;Item Code</label>
{{--                                <i id="btngen" class="f-right text-primary" data-toggle="tooltip" data-placement="top" title="Auto Generate"> Auto Generate Code</i>--}}
                                <input class="form-control" type="text"
                                       name="code" id="code" value="{{ old('code') }}" onchange="verifycode()"/>
                                @if ($errors->has('code'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
						
						<div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('uom') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Unit of measure</label>
                                <select name="uom" id="uom" data-placeholder="Select Unit of measure" class="form-control select2"  >
                                    <option value="">Select Unit of measure</option>
                                    @if($uom)
                                        @foreach($uom as $value)
                                            <option value="{{ $value->uom_id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('uom'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('itemname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Item Name</label>
                                <input type="text" name="itemname" id="itemname" class="form-control"  value="{{ old('itemname') }}" />
                                @if ($errors->has('itemname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @else
                                    <div class="form-control-feedback text-info">
                                        Enter POS Product Name
                                    </div>
                                @endif

                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('qty') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Qty. Usage</label>
                                <i id="btn_help" class="icofont icofont-eye-alt f-right text-success" data-toggle="tooltip" data-placement="top" title="Help" ></i>
                                <input type="text"  name="qty" id="qty" class="form-control" value="{{ old('qty') }}"  />
                                @if ($errors->has('qty'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @else
                                    <div class="form-control-feedback text-info">
                                        Please Enter usage qty according to unit of measure
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <a href="#">
                                <img id="productimages" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('productimage') ? 'has-danger' : '' }} ">
                                <label for="productimage" class="form-control-label">Product Image</label>
                                <br/>
                                <label for="productimage" class="custom-file">
                                    <input type="file" name="productimage" id="productimage" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group {{ $errors->has('rp') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Retail Price:</label>
                                <input class="form-control" type="Number" min="0" name="rp" id="rp" value="0" />
                                @if ($errors->has('rp'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Wholesale Price:</label>
                                <input class="form-control" type="Number" min="0" name="wp" id="wp" value="0"/>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Discount Price:</label>
                                <input class="form-control" type="Number" min="0" name="dp" id="dp" value="0"/>
                            </div>
                        </div>

                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">On-line Price:</label>
                                <input class="form-control" type="Number" min="0" name="op" id="op" value="0"/>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-12 col-sm-12">
                        <div class="button-group ">
                            <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-plus"> </i>
                                Create Product
                            </button>
                        </div>
                    </div>


            </div>
            </form>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">POS Products List</h5>
            </div>
            <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkactive" class="mainchk">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions">Show In-Active POS Products</div>
                </div>
                <br/>
                <br/>
                <table id="tblposproducts" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th>Image</th>
{{--                        <th>Branch</th>--}}
{{--                        <th>Department</th>--}}
                        <th>Item Code | Name</th>
						<th>UOM</th>
                        <th>Ref. Product</th>
                        <th>Retail Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($details as $value)
                        <tr>
                            <td class="text-center">
                                <img width="42" height="42" src="{{ asset('public/assets/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}"/>
                            </td>
{{--                            <td >{{$value->branch_name}}</td>--}}
{{--                            <td >{{$value->department_name}}</td>--}}

                            <td >{{$value->item_code}} | {{$value->item_name}}</td>
							<td >{{$value->uomname}}</td>
                            <td >{{$value->product_name}}</td>
                            <td >{{$value->retail_price}}</td>
                            <td >{{$value->status_name}}</td>
                            <td class="action-icon">

                                <a  class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" onclick="edit('{{$value->pos_item_id}}','{{$value->item_name}}','{{$value->retail_price}}','{{$value->wholesale_price}}','{{$value->online_price}}','{{$value->discount_price}}','{{$value->quantity}}','{{$value->uom}}')" ></i> </a>

                                <i class="icofont icofont-ui-delete text-danger f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onclick="remove('{{$value->item_name}}','{{$value->pos_item_id}}')"></i>

                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>




    </section>


    <!-- modals -->

    <div class="modal fade modal-flex" id="help-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Conversion Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <table id="tblhelp" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Gram</th>
                                    <th>Kilo Gram</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>250 gram</td>
                                    <td>0.25 kg</td>
                                </tr>
                                <tr>
                                    <td>500 gram</td>
                                    <td>0.5 kg</td>
                                </tr>
                                <tr>
                                    <td>750 gram</td>
                                    <td>0.75 kg</td>
                                </tr>
                                <tr>
                                    <td>1000 gram</td>
                                    <td>1 kg</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>



    <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Modal</h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Item Name:</label>
                                <input type="text" name="itemnamemodal" id="itemnamemodal" class="form-control"  />
                                <input type="hidden" name="itemid" id="itemid" class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Qty. Usage:</label>
                                <input type="number" min="0" name="qtymodal" id="qtymodal" class="form-control"  />
                            </div>
                        </div>
						
						<div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="form-control-label">Unit of measure:</label>
                                <select name="updateuom" id="updateuom" data-placeholder="Select Unit of measure" class="form-control select2"  >
                                    <option value="">Select Unit of measure</option>
                                    @if($uom)
                                        @foreach($uom as $value)
                                            <option value="{{ $value->uom_id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select> 
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Retail Price:</label>
                                <input type="number" min="0" name="rpmodal" id="rpmodal" class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Wholesale Price:</label>
                                <input type="number" min="0" name="wpmodal" id="wpmodal" class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Discount Price:</label>
                                <input type="number" min="0" name="dpmodal" id="dpmodal" class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label class="form-control-label">Online Price:</label>
                                <input type="number" min="0" name="opmodal" id="opmodal" class="form-control"  />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onClick="update()">
                        <i class="icofont icofont-edit-alt"> </i>
                        Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();
        $('#tblposproducts').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Search Product',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        function readURL(input,id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#'+id).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#productimage").change(function() {
            readURL(this,'productimages');
        });

        function toggle(){

            $('#insert-card').toggle();
        }



        $('#upload_form').on('submit', function(event){
            event.preventDefault();

       if ($('#finishgood').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Please Select Product!",
                    type: "error"
                });
            }
            else if ($('#itemname').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Item name can not left blank!",
                    type: "error"
                });
            }
            else if ($('#qty').val() == "" || $('#qty').val() == 0) {
                swal({
                    title: "Error Message",
                    text: "Qty can not left blank!",
                    type: "error"
                });
            }
            else if ($('#code').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Please Enter Item Code!",
                    type: "error"
                });
            }

            else{

                $.ajax({
                    url: "{{url('/insert-posproducts')}}",
                    method: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,

                    success:function(resp){
                        if(resp == 1){
                            swal({
                                title: "Success",
                                text: "POS Product Added Successfully!",
                                type: "success"
                            },function(isConfirm){
                                if(isConfirm){
                                    window.location= "{{ url('/posproducts') }}";
                                }
                            });
                        }
                        else{
                            swal({
                                title: "Already Exsist!",
                                text: "Same Name POS Product Already Exsist!",
                                type: "error"
                            });
                        }
                    }

                });
            }

        });


        $('#btn_help').click(function(){
            $('#help-modal').modal('show');
        });

        //Alert confirm
        function remove(name,id){
            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete "+ name +" ?",
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
                            url: "{{url('/inactive-posproducts')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                subid:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "POS Product Deleted Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/posproducts') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "POS Product Safe :)", "error");
                    }
                });
        }


        $('#chkactive').change(function(){
            if (this.checked) {
                $.ajax({
                    url: "{{url('/inactive-posproducts')}}",
                    type: 'GET',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                    },
                    success:function(result){
                        if(result){
                            $("#tblposproducts tbody").empty();
                            for(var count =0;count < result.length; count++){

                                $("#tblposproducts tbody").append(
                                    "<tr>" +
                                    "<td class='text-center'><img width='42' height='42' src='public/assets/images/products/"+((result[count].image != "") ? result[count].image : 'placeholder.jpg')+"' alt='"+result[count].image+"'/></td>" +
                                    "<td>"+result[count].item_code +" | "+result[count].item_name+"</td>" +
                                    "<td>"+result[count].product_name+"</td>" +
                                    "<td>"+result[count].department_name+"</td>" +
                                    "<td>"+result[count].price+"</td>" +
                                    "<td>"+result[count].status_name+"</td>" +
                                    "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].pos_item_id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                                    "</tr>"
                                );
                            }

                        }
                    }
                });
            }
            else{
                window.location="{{ url('/posproducts') }}";
            }
        });

        function reactive(id){
            swal({
                    title: "Are you sure?",
                    text: "You want to Re-Active POS Product!",
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
                            url: "{{url('/reactive-posproducts')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                subid:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Re-Active",
                                        text: "POS Product Re-Active Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/posproducts') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
        }

        function edit(id,name,rp,wp,op,dp,qty,uom){
            $('#update-modal').modal('show');
            $('#itemid').val(id);
            $('#itemnamemodal').val(name);
            $('#rpmodal').val(rp);
            $('#wpmodal').val(wp);
            $('#opmodal').val(op);
            $('#dpmodal').val(dp);
            $('#qtymodal').val(qty);
			$('#updateuom').val(uom).change();
        }


        function update(){

            $.ajax({
                url: "{{url('/update-posproducts')}}",
                type: 'PUT',
                data:{_token:"{{ csrf_token() }}",
                    itemid: $('#itemid').val(),
                    itemname: $('#itemnamemodal').val(),
					uom: $('#updateuom').val(),
                    rp: $('#rpmodal').val(),
                    wp: $('#wpmodal').val(),
                    op: $('#opmodal').val(),
                    dp: $('#dpmodal').val(),
                    qty: $('#qtymodal').val(),
                },
                success:function(resp){
                    if (resp != 2) {
                        swal({
                            title: "success",
                            text: "Updated Successfully!",
                            type: "success"
                        },function(isConfirm){
                            if(isConfirm){
                                window.location="{{ url('/posproducts') }}";
                            }
                        });
                    }
                    else{
                        swal({
                            title: "Already Exsist!",
                            text: "Same Name POS Product Already Exsist!",
                            type: "error"
                        });
                    }
                }
            });
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

                let depart = $('#depart option:selected').text();
                let subdepart = $('#subDepart option:selected').text();

                let d = depart.substring(0,1);
                let sub = subdepart.substring(0,1)
                let rand = Math.floor(Math.random() * 10000);

                let codes = d+ sub + "-" + rand;

                $('#code').val(codes);
            }

        });
        
        function verifycode() {
            $.ajax({
                url: "{{url('/verifycode')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",
                    code: $('#code').val(),
                },
                success:function(resp){
                    console.log(resp);
                    if (resp != 0) {
                        swal({
                            title: "Already Exsist!",
                            text: "Item Code Already Exsist, Please try different!",
                            type: "error"
                        });
                        $('#code').val('');
                    }
                }
            });

        }
    </script>
@endsection


