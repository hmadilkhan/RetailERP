@extends('layouts.master-layout')

@section('title', 'Edit Branch')

@section('breadcrumtitle', 'Edit Branch')

@section('navbranchoperation', 'active')
@section('navbranch', 'active')

@section('content')
    <section class="panels-wells">
        <div class="card">

            <div class="card-header">
                <h5 class="card-header-text">Edit Branch</h5>
                <h5 class=""><a href="{{ url('/branches') }}"><i
                            class="text-success text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            </div>
            <div class="card-block">
                <form method="put" id="upload_form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" id="imagename" name="imagename" value="{{ $details[0]->branch_logo }}" />
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Company</label>
                                <select name="company" id="company" data-placeholder="Select Company"
                                    class="form-control select2">
                                    <option value="">Select Company</option>
                                    @if ($company)
                                        @foreach ($company as $value)
                                            <option
                                                {{ $value->company_id == $details[0]->company_id ? 'selected="selected"' : '' }}
                                                value="{{ $value->company_id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Branch Name</label>
                                <input class="form-control" type="text" required="required" name="branchname"
                                    id="branchname" value="{{ $details[0]->branch_name }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country"
                                    class="form-control select2">
                                    @if ($country)
                                        @foreach ($country as $value)
                                            <option
                                                {{ $value->country_name == $details[0]->country_name ? 'selected="selected"' : '' }}
                                                value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select name="city" id="city" data-placeholder="Select City"
                                    class="form-control select2">
                                    <option>Select City</option>
                                    @if ($city)
                                        @foreach ($city as $value)
                                            <option
                                                {{ $value->city_name == $details[0]->city_name ? 'selected="selected"' : '' }}
                                                value="{{ $value->city_id }}">{{ $value->city_name }}</option>
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
                                <input class="form-control" type="text" required="required" name="br_email"
                                    id="email" value="{{ $details[0]->branch_email }}" />
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Mobile Number</label>
                                <input class="form-control" type="text" required="required" name="br_mobile"
                                    id="mobile" value="{{ $details[0]->branch_mobile }}" />
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Ptcl Number</label>
                                <input class="form-control" type="text" required="required" name="br_ptcl" id="ptcl"
                                    value="{{ $details[0]->branch_ptcl }}" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Branch Code</label>
                                <input class="form-control" type="text" name="br_code" id="code"
                                    value="{{ $details[0]->code }}" />
                                @if ($errors->has('br_code'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Record Daily Stock ?</label>
                                <select name="record_daily_stock" id="record_daily_stock"
                                    data-placeholder="Record Daily Stock" class="form-control select2">
                                    <option {{ $details[0]->record_daily_stock == 1 ? 'selected' : '' }} value="1">Yes</option>
                                    <option {{ $details[0]->record_daily_stock == 0 ? 'selected' : '' }} value="0">No</option>
                                </select>
                                <span class="form-control-feedback text-danger" id="record_daily_stock_message"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Reports</label>
                                <select multiple name="reportlist[]" id="report" data-placeholder="Select Company"
                                    class="form-control select2">
                                    <option value="">Select Reports</option>
                                    @if ($reports)
                                        @foreach ($reports as $value)
                                            <option
                                                {{ in_array($value->id, $branchreports->toArray()) ? 'selected="selected"' : '' }}
                                                value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <span class="form-control-feedback text-danger" id="reportmessage"></span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group">
                                <label class="form-control-label">Report Send Date</label>
                                <input class="form-control" type="text" name="report_send_date" id="report_send_date"
                                    value="{{ $details[0]->report_send_date }}" />
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Branch Address</label>
                                <textarea name="br_address" id="address" class="form-control">{{ $details[0]->branch_address }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <a href="#">
                                <img id="vdpimg"
                                    src="{{ asset('storage/images/branch/' . (!empty($details[0]->branch_logo) ? $details[0]->branch_logo : 'placeholder.jpg') . '') }}"
                                    class="thumb-img img-fluid width-100" alt="img"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                <label for="vdimg" class="form-control-label">Branch Logo</label>

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

                    <input class="form-control" type="hidden" name="br_id" id="br_id"
                        value="{{ $details[0]->branch_id }}" />
                    <input class="form-control" type="hidden" name="br_old_image" id="br_old_image"
                        value="{{ $details[0]->branch_logo }}" />

                    <button type="submit" id="btnsubmit"
                        class="btn btn-md btn-success waves-effect waves-light f-right"> Update Branch </button>

                </form>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();

        $("#branch_emails").tagsinput({
            maxTags: 10
        });

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
                url: "{{ url('/updatebranch') }}",
                method: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(resp) {
                    // console.log(resp)
                    if (resp == 1) {
                        swal({
                                title: "Operation Performed",
                                text: "Branch Updated Successfully!",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    window.location = "{{ url('/branches') }}";
                                }
                            });
                    }
                }

            });
        });

        $('#report_send_date').bootstrapMaterialDatePicker({
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
    </script>


@endsection
