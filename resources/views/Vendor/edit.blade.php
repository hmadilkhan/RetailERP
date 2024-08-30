@extends('layouts.master-layout')

@section('title', 'Vendor')

@section('breadcrumtitle', 'Create Vendor')
@section('navVendorPO', 'active')
@section('navvendor', 'active')
@section('nav_addvendor', 'active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Edit Vendor Details</h5>
                <h5 class=""><a href="{{ route('vendors.index') }}"><i
                            class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>

            </div>
            <div class="card-block">

                <form method="POST" action="{{ route('vendors.update', $vendor->id) }}" class="form-horizontal"
                    enctype="multipart/form-data">
                    @method('PATCH')
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('vdname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Vendor Name</label>
                                <input type="text" name="vdname" id="vdname" placeholder="Vendor Name"
                                    class="form-control" value="{{ $vendor->vendor_name }}" />
                                @if ($errors->has('vdname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('vdcontact') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Vendor Contact</label>
                                <input type="text" name="vdcontact" id="vdcontact" placeholder="Vendor Contact"
                                    class="form-control" value="{{ $vendor->vendor_contact }}" />
                                @if ($errors->has('vdcontact'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Company Name</label>
                                <input type="text" name="cpname" id="cpname" placeholder="Company Name"
                                    class="form-control"
                                    value="{{ sizeof($company) > 0 ? $company[0]->company_name : '' }}" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country"
                                    onchange="$(this).val('{{ $vendor->country_id }}').change()"
                                    data-placeholder="Select Country" class="form-control select2">
                                    <option>Select Country</option>
                                    @if ($country)
                                        @foreach ($country as $value)
                                            <option
                                                {{ $value->country_id == $vendor->country_id ? 'selected="selected"' : '' }}
                                                value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select name="city" id="city" data-placeholder="Select City"
                                    class="form-control select2">
                                    <option>Select City</option>
                                    @if ($city)
                                        @foreach ($city as $value)
                                            <option {{ $value->city_id == $vendor->city_id ? 'selected="selected"' : '' }}
                                                value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Payment Terms Days:</label>
                                <input type="Number" name="paymentdays" id="paymentdays" placeholder="0"
                                    class="form-control" value="{{ $vendor->payment_terms }}" min="0" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">NTN</label>
                                <input type="text" name="ntn" id="ntn" placeholder="Enter NTN number"
                                    class="form-control" value="{{ $vendor->ntn }}" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">STRN</label>
                                <input type="text" name="strn" id="strn" placeholder="Enter STRN number"
                                    class="form-control" value="{{ $vendor->strn }}" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Company Address</label>
                                <textarea name="address" rows="3" id="address" class="form-control">{{ $vendor->address }}</textarea>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <div class="col-md-4">
                            <a href="#">
                                <img id="plogo"
                                    src="{{ asset('assets/images/vendors/' . (empty($company[0]->logo) ? 'placeholder.jpg' : $company[0]->logo) . '') }}"
                                    class="thumb-img img-fluid width-100"
                                    alt="{{ empty($vendor->image) ? 'placeholder.jpg' : $company[0]->logo }}"
                                    class="thumb-img img-fluid width-100"
                                    alt="{{ empty($company[0]->logo) ? 'placeholder.jpg' : $company[0]->logo }}"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('logo') ? 'has-danger' : '' }} ">
                                <label for="logo" class="form-control-label">Company Logo</label>

                                <label for="logo" class="custom-file">
                                    <input type="file" name="logo" id="logo" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                    <input type="hidden" id="companyvendorimage" name="companyvendorimage"
                                        value="{{ $company[0]->logo }}">
                                </label>
                                @if ($errors->has('logo'))
                                    <div class="form-control-feedback">{{ $errors->first('logo') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="#">
                                <img id="vdpimg"
                                    src="{{ asset('assets/images/vendors/' . (empty($vendor->image) ? 'placeholder.jpg' : $vendor->image) . '') }}"
                                    class="thumb-img img-fluid width-100"
                                    alt="{{ empty($vendor->image) ? 'placeholder.jpg' : $vendor->image }}"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                <label for="vdimg" class="form-control-label">Vendor Image</label>

                                <label for="vdimg" class="custom-file">
                                    <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                    <input type="hidden" id="prevvendorimage" name="prevvendorimage"
                                        value="{{ $vendor->image }}">
                                </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="contact-card-button ">
                        <button type="submit" class="btn btn-md btn-success waves-effect waves-light f-right">
                            <i class="icofont icofont-ui-edit m-r-5"> </i>Update Vendor
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </section>

@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();

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

        $("#logo").change(function() {
            readURL(this, 'plogo');
        });



        $("#vdemail").on('change', function() {

            if ($(this).val() != "") {

                $.ajax({
                    url: "{{ url('/vendoremail') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        email: $(this).val()
                    },
                    success: function(resp) {

                        if (resp == 1) {
                            $("#vdemail_alert").html("Sorry, that email address taken. Try another?");
                        } else {
                            $("#vdemail_alert").html("");
                        }
                    }

                });
            }



        });
    </script>


@endsection
