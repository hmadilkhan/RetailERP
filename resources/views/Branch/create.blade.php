@extends('layouts.master-layout')

@section('title', 'Create Branch')

@section('breadcrumtitle', 'Create Branch')

@section('navbranchoperation', 'active')
@section('navbranch', 'active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Branch</h5>
            </div>
            <div class="card-block">
                <form method="post" id="upload_form" enctype="multipart/form-data">

                    {{ csrf_field() }}

                    <div class="row">
                        <!-- @if (session('roleId') == 1) -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Company</label>
                                <select name="company" id="company" data-placeholder="Select Company"
                                    class="form-control select2">
                                    <option value="">Select Company</option>
                                    @if ($company)
                                        @foreach ($company as $value)
                                            <option value="{{ $value->company_id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <!-- @endif -->

                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Branch Name</label>
                                <input class="form-control" type="text" name="branchname" id="branchname"
                                    value="{{ old('branchname') }}" />
                                @if ($errors->has('branchname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country"
                                    class="form-control select2">
                                    <option value="">Select Country</option>
                                    @if ($country)
                                        @foreach ($country as $value)
                                            <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select disabled="disabled" name="city" id="city" data-placeholder="Select City"
                                    class="form-control select2">
                                    <option value="">Select City</option>
                                    @if ($city)
                                        @foreach ($city as $value)
                                            <option value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Branch Email</label>
                                <input class="form-control" type="text" name="br_email" id="email" />
                                @if ($errors->has('br_email'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Mobile Number</label>
                                <input class="form-control" type="text" name="br_mobile" id="mobile" />
                                @if ($errors->has('br_mobile'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Ptcl Number</label>
                                <input class="form-control" type="text" name="br_ptcl" id="ptcl" />
                                @if ($errors->has('br_ptcl'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Branch Code</label>
                                <input class="form-control" type="text" name="br_code" id="code" />
                                @if ($errors->has('br_code'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                          <div class="form-group">
                              <label class="form-control-label">Record Daily Stock ?</label>
                              <select multiple name="record_daily_stock" id="record_daily_stock" data-placeholder="Record Daily Stock"
                                  class="form-control select2">
                                  <option value="1">Yes</option>
                                  <option selected value="0">No</option>
                              </select>
                              <span class="form-control-feedback text-danger" id="record_daily_stock_message"></span>
                          </div>
                      </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-lg-4 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Reports</label>
                                <select multiple name="report[]" id="report" data-placeholder="Select Company"
                                    class="form-control select2">
                                    <option value="">Select Reports</option>
                                    @if ($reports)
                                        @foreach ($reports as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="form-control-feedback text-danger" id="reportmessage"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-5">
                            <div class="form-group">
                                <label class="form-control-label">Branch Address</label>
                                <textarea name="br_address" id="address" class="form-control"></textarea>
                                @if ($errors->has('br_address'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('assets/images/placeholder.jpg') }}"
                                    class="thumb-img img-fluid width-100" alt="img"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                <label for="vdimg" class="form-control-label">Branch Logo</label>
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
                        class="btn btn-md btn-success waves-effect waves-light f-right"> Create Branch </button>

                </form>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();


        $("#country").on('change', function() {

            if ($(this).val() != "") {
                $("#city").attr("disabled", false);
            } else {
                $("#city").attr("disabled", true);
            }
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




        $('#upload_form').on('submit', function(event) {
            event.preventDefault();
            $.ajax({
                url: "{{ url('/submitbranch') }}",
                method: 'POST',
                data: new FormData(this),

                contentType: false,
                cache: false,
                processData: false,
                success: function(resp) {

                    if (resp == 1) {
                        swal({
                                title: "Operation Performed",
                                text: "Branch Created Successfully!",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    window.location = "{{ url('/branches') }}";
                                }
                            });
                    } else {
                        swal({
                            title: "Already exsit",
                            text: "Particular Branch Already exsit!",
                            type: "warning"
                        });
                    }
                }

            });
        });
    </script>


@endsection
