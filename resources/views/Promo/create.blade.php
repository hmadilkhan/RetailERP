@extends('layouts.master-layout')

@section('title','Create Promotion')

@section('breadcrumtitle','Create Promotion')

@section('navbranchoperation','active')
@section('navbranch','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Promotion</h5>
            </div>
            <div class="card-block">
                <form method="post" action="{{url('promo-save')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Prefix</label>
                                <input class="form-control" type="text" name="prefix" id="prefix" value="{{ old('prefix') }}"  />
                                @if ($errors->has('prefix'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Suffix</label>
                                <i id="btn_auto_generate" class="f-right text-primary" data-toggle="tooltip" data-placement="top" title="Auto Generate"> Auto Generate</i>
                                <input class="form-control" type="text" name="suffix" id="suffix" value="{{ old('suffix') }}">
                                @if ($errors->has('prefix'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Start Date</label>
                                <input class="form-control" type="text" name="startdate" id="startdate" value="{{ old('prefix') }}"  />
                                @if ($errors->has('prefix'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">End Date</label>
                                <input class="form-control" type="text" name="enddate" id="enddate" value="{{ old('prefix') }}"  />
                                @if ($errors->has('prefix'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                    </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Branch</label>
                                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
                                    <option value="">Select Branch</option>
                                    @if($branch)
                                        @foreach($branch as $value)
                                            <option value="{{$value->branch_id}}">{{$value->branch_name}}</option>
                                        @endforeach
                                    @endif

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Department</label>
                                <select name="department" id="department" data-placeholder="Select Department" class="form-control select2" >
                                    <option value="">Select Department</option>
                                    @if($department)
                                        @foreach($department as $value)
                                            <option value="{{$value->department_id}}">{{$value->department_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Sub-Department</label>
                                <select name="subdepartment" id="subdepartment" data-placeholder="Select Sub-Department" class="form-control select2" >
                                    <option value="">Select Sub-Department</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Products</label>
                                <select name="products[]" id="products" data-placeholder="Select Products" class="form-control select2" multiple>
                                    <option value="">Select Products</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Promotion Mode</label>
                                <select name="promotionmode" id="promotionmode" data-placeholder="Select Promotion Mode" class="form-control select2" >
                                    <option value="">Select Promotion Mode</option>
                                    @if($promo_mode)
                                        @foreach($promo_mode as $value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-control-label">Customer</label>
                                <select name="customer[]" id="customer" data-placeholder="Select Customer" class="form-control select2" multiple>
                                    <option value="">Select Customer</option>
                                </select>
                            </div>
                        </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="form-group">
                            <label class="form-control-label">Message</label>
                            <textarea rows="3" id="message" name="message" class="form-control" ></textarea>
                        </div>
                    </div>


                    <div class="col-lg-12 col-sm-12 ">
                        <div class="form-group ">

                            <button type="submit"  class="btn btn-md btn-success waves-effect waves-light m-t-25 f-right"  >
                                <i class="icofont icofont-ui-check"> </i>Submit
                            </button>

                        </div>
                    </div>


                </form>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();

        $('#startdate,#enddate').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false,
            clearButton: true,

            icons: {
                date: "icofont icofont-ui-calendar",
                up: "icofont icofont-rounded-up",
                down: "icofont icofont-rounded-down",
                next: "icofont icofont-rounded-right",
                previous: "icofont icofont-rounded-left"
            }
        });

        $('#btn_auto_generate').click(function (e) {
            var label = ($('#prefix').val() == "" ? makeid(3) : $('#prefix').val() )+ "-"+Math.floor((Math.random() * 100 + 500) + 1);
            $("#suffix").val(label);
        });

        function makeid(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }

        $('#department').change(function (e) {
            load_subdept($('#department').val())
        })



        $("#subdepartment").change(function () {
            load_products($("#subdepartment").val())
        })

        $("#branch").change(function () {
            getCustomersByBranch($("#branch").val())
        })

        function load_products(id)
        {
            $.ajax({
                url: "{{ url('get-inventory-by-subdepartment')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",id:id},
                success:function(resp){

                    $('#products').empty();
                    if(resp != ""){
                        $("#products").append("<option value='all'>All </option>");
                    }
                    $.each(resp, function( index, value ) {
                        $("#products").append(
                            "<option value="+value.id+">"+value.product_name+"</option>"
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

                    $('#subdepartment').empty();
                    $("#subdepartment").append("<option value=''>Select Sub Department</option>");
                    $.each(resp, function( index, value ) {
                        $("#subdepartment").append(
                            "<option value="+value.sub_department_id+">"+value.sub_depart_name+"</option>"
                        );
                    });

                }

            });
        }

        function getCustomersByBranch(id)
        {
            $.ajax({
                url: "{{ url('get-customers-by-branch')}}",
                type: 'GET',
                data:{branch:id},
                success:function(resp){

                    $('#customer').empty();
                    if(resp != ""){
                        $("#customer").append("<option value='all'>All</option>");
                    }

                    $.each(resp, function( index, value ) {
                        $("#customer").append(
                            "<option value="+value.id+">"+value.name+"</option>"
                        );
                    });

                }

            });
        }

    </script>
@endsection