@extends('layouts.master-layout')

@section('title', 'User')

@section('breadcrumtitle', 'Create User')

@section('navbranchoperation', 'active')
@section('navuser', 'active')

@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text"> Create User</h5>
                <h5 class=""><a href="{{ url('/usersDetails') }}"><i
                            class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>

            </div>
            <div class="card-block">

                <form method="POST" action="{{ url('/store-user') }}" class="form-horizontal"
                    enctype="multipart/form-data">
                    @csrf
                    <h5>User Authorization</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('company') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Company</label>
                                <select name="company" id="company" class="form-control select2">
                                    <option value="">Select Company</option>
                                    @if ($company)
                                        @foreach ($company as $value)
                                            <option value="{{ $value->company_id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('company'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div id="singleBranch" class="col-md-4">
                            <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Branch</label>
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value="">Select Branch</option>
                                </select>
                                @if ($errors->has('branch'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div id="multipleBranch" class="col-md-4" style="display:none;">
                            <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Branch</label>
                                <select name="branch[]" id="multiplebranches" class="form-control select2" multiple>
                                    <option value="">Select Branch</option>
                                </select>
                                @if ($errors->has('branch'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('role') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Roles</label>
                                <i id="btn_role" class="icofont icofont-plus f-right text-success" data-toggle="tooltip"
                                    data-placement="top" title="Add Role"></i>
                                <select name="role" id="role" class="form-control select2">
                                    <option value="">Select Role</option>
                                    @if ($role)
                                        @foreach ($role as $value)
                                            <option value="{{ $value->role_id }}">{{ $value->role }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('role'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>
                    <h5>User Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('fullname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Full Name</label>
                                <input type="text" name="fullname" id="fullname" class="form-control"
                                    value="{{ old('fullname') }}" />
                                @if ($errors->has('fullname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="something@gmail.com" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Contact Number</label>
                                <input type="text" name="contact" id="contact" class="form-control"
                                    placeholder="0300-1234567" />
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country"
                                    class="form-control select2">
                                    <option value="">Select Country</option>
                                    @if ($country)
                                        @foreach ($country as $value)
                                            @if ($value->country_name == 'Pakistan')
                                                <option selected="selected" value="{{ $value->country_id }}">
                                                    {{ $value->country_name }}</option>
                                            @else
                                                <option value="{{ $value->country_id }}">{{ $value->country_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('country'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('city') ? 'has-danger' : '' }}">
                                <label class="form-control-label">City</label>
                                <select name="city" id="city" data-placeholder="Select City"
                                    class="form-control select2">
                                    <option value="">Select City</option>
                                    @if ($city)
                                        @foreach ($city as $value)
                                            @if ($value->city_name == 'Karachi')
                                                <option selected="selected" value="{{ $value->city_id }}">
                                                    {{ $value->city_name }}</option>
                                            @else
                                                <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('city'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Address</label>
                                <textarea name="address" id="address" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">



                    </div>
                    <hr>
                    <h5>Set Login Authentication</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div id="user" class="form-group {{ $errors->has('username') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">User Name</label>
                                <input type="text" name="username" id="username" class="form-control"
                                    value="{{ old('username') }}" />
                                @if ($errors->has('username'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('password') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    value="{{ old('password') }}" />
                                @if ($errors->has('password'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif

                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('storage/images/placeholder.jpg') }}"
                                    class="thumb-img img-fluid width-100" alt="img"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                <label for="vdimg" class="form-control-label">User Logo</label>
                                <br />
                                <label for="vdimg" class="custom-file">
                                    <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="btnsubmit"
                        class="btn btn-md btn-primary waves-effect waves-light f-right">
                        Create User
                    </button>


                </form>

            </div>
        </div>


        <div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Add Role</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Role Name:</label>
                                    <input type="text" name="rolename" id="rolename" class="form-control" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light"
                            onClick="addrole()">Create Role</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();

        $("#btn_role").on('click', function() {
            $('#departname').val('');
            $("#depart-modal").modal("show");
        });

        $("#country").on('change', function() {

            if ($(this).val() != "") {
                $("#city").attr("disabled", false);
            } else {
                $("#city").attr("disabled", true);
            }
        });

        $("#company").on('change', function() {
            if ($("#role").val() == 16) // For Regional Manager
            {
                getBranches("multiplebranches")
            } else {
                getBranches("branch")
            }
        });

        $("#username").on('change', function() {
            $.ajax({
                url: "{{ url('/chk-user') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    username: $('#username').val()
                },
                success: function(resp) {
                    console.log(resp)
                    if (resp == 1) {

                        $('#user').addClass('has-danger');
                        swal({
                            title: "Already exsist",
                            text: "Username Already exsist!",
                            type: "warning"
                        });
                        $('#username').val('');

                    } else {
                        $('#user').removeClass('has-danger');
                        $('#user').addClass('has-success');
                    }

                }
            });
            $("#username").focus();
        });

        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + id).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#vdimg").change(function() {
            readURL(this, 'vdpimg');
        });

        function addrole() {
            if ($('#rolename').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Required Field can not be blank!",
                    type: "warning"
                });

            } else {
                $.ajax({
                    url: "{{ url('/add-role') }}",
                    type: 'POST',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        rolename: $('#rolename').val(),
                    },

                    success: function(resp) {
                        console.log(resp);
                        if (resp != 2) {
                            swal({
                                title: "Operation Performed",
                                text: "Role Added Successfully!",
                                type: "success"
                            });
                            $("#depart-modal").modal("hide");
                            $("#role").empty();
                            $("#role").append("<option value=''>Select Role</option>");
                            for (var count = 0; count < resp.length; count++) {
                                $("#role").append(
                                    "<option value='" + resp[count].role_id + "'>" + resp[count].role +
                                    "</option>");
                            }
                            $('#rolename').val('')
                        } else {
                            swal({
                                title: "Already exsist",
                                text: "Particular Role Already exist!",
                                type: "warning"
                            });
                            $('#rolename').val('')
                            $("#depart-modal").modal("hide");

                        }
                    }

                });
            }

        }

        function getBranches(id) {
            $.ajax({
                url: "{{ url('/get-branches-by-company') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    company: $('#company').val()
                },
                success: function(result) {
                    $("#" + id).empty();
                    $.each(result, function(index, value) {
                        $("#" + id).append(
                            "<option value='" + value.branch_id + "'>" + value.branch_name +
                            "</option>"
                        );
                    });

                }
            });
        }


        $("#role").on('change', function() {
            if ($(this).val() == 16 || $(this).val() == 18) // For Regional Manager
            {
                $('#branch').attr('multiple', 'multiple').trigger('change');
                $("#multipleBranch").css("display", "block")
                $("#singleBranch").css("display", "none")
                getBranches("multiplebranches")
                console.log($(this).val())
            } else {
                console.log($(this).val())
                $("#multipleBranch").css("display", "none")
                $("#singleBranch").css("display", "block")
            }
        });
    </script>
@endsection
