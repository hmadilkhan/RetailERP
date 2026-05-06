@extends('layouts.master-layout')

@section('title', 'Create Company')

@section('breadcrumtitle', 'Create Company')

@section('navcompany', 'active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Company</h5>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="m-b-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <h5 class=""><a href="{{ route('company.index') }}"><i
                            class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            </div>
            <div class="card-block">
                <form method="post" action="{{ url('insert-company') }}" class="form-horizontal"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('companyname') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Company Name</label>
                                <input class="form-control" type="text" name="companyname" id="companyname"
                                    value="{{ old('companyname') }}" />
                                @if ($errors->has('companyname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country"
                                    class="form-control select2">
                                    <option value="">Select Country</option>
                                    @if ($country)
                                        @foreach ($country as $value)
                                            @if (old('country') == $value->country_id)
                                                <option selected="selected" value="{{ $value->country_id }}">
                                                    {{ $value->country_name }}</option>
                                            @else
                                                <option value="{{ $value->country_id }}">{{ $value->country_name }}</option>
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
                                <select {{ old('country') ? '' : 'disabled="disabled"' }} name="city" id="city" data-placeholder="Select City"
                                    class="form-control select2">
                                    <option value="">Select City</option>
                                    @if ($city)
                                        @foreach ($city as $value)
                                            @if (old('city') == $value->city_id)
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
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('company_email') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Company Email</label>
                                <input class="form-control" type="text" name="company_email" id="company_email"
                                    value="{{ old('company_email') }}" />
                                @if ($errors->has('company_email'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('company_mobile') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Mobile Number</label>
                                <input class="form-control" type="text" name="company_mobile" id="company_mobile"
                                    value="{{ old('company_mobile') }}" />
                                @if ($errors->has('company_mobile'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('whatsapp_number') ? 'has-danger' : '' }}">
                                <label class="form-control-label">WhatsApp Number</label>
                                <input class="form-control" type="text" name="whatsapp_number" id="whatsapp_number"
                                    value="{{ old('whatsapp_number') }}" />
                                @if ($errors->has('whatsapp_number'))
                                    <div class="form-control-feedback">{{ $errors->first('whatsapp_number') }}</div>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('company_ptcl') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Ptcl Number</label>
                                <input class="form-control" type="text" name="company_ptcl" id="company_ptcl"
                                    value="{{ old('company_ptcl') }}" />
                                @if ($errors->has('company_ptcl'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('currency') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Currency</label>
                                <select name="currency" id="currency" data-placeholder="Select Currency"
                                    class="form-control select2">
                                    <option value="">Select Currency</option>
                                    @if ($currencies)
                                        @foreach ($currencies as $currency)
                                            @if (old('currency') == $currency->name)
                                                <option selected="selected" value="{{ $currency->name }}">
                                                    {{ $currency->name }}</option>
                                            @else
                                                <option value="{{ $currency->name }}">{{ $currency->name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('currency'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('package') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Packages</label>
                                <select name="package" id="package" data-placeholder="Select Packages"
                                    class="form-control select2">
                                    <option value="">Select Packages</option>
                                    @if ($packages)
                                        @foreach ($packages as $package)
                                            @if (old('package') == $package->id)
                                                <option selected="selected" value="{{ $package->id }}">
                                                    {{ $package->name }}</option>
                                            @else
                                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('package'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group {{ $errors->has('company_address') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Company Address</label>
                                <textarea name="company_address" id="company_address" class="form-control">{{ old('company_address') }}</textarea>
                                @if ($errors->has('company_address'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('invoice_type') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Invoice Type</label>
                                <select name="invoice_type" id="invoice_type" class="form-control select2">
                                    <option value="branch" {{ old('invoice_type', 'branch') === 'branch' ? 'selected' : '' }}>Branch</option>
                                    <option value="terminal" {{ old('invoice_type') === 'terminal' ? 'selected' : '' }}>Terminal</option>
                                </select>
                                @if ($errors->has('invoice_type'))
                                    <div class="form-control-feedback">{{ $errors->first('invoice_type') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('billing_cycle_day') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Billing Cycle Day</label>
                                <input class="form-control" type="number" name="billing_cycle_day" min="1" max="28"
                                    value="{{ old('billing_cycle_day', 1) }}" />
                                @if ($errors->has('billing_cycle_day'))
                                    <div class="form-control-feedback">{{ $errors->first('billing_cycle_day') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('payment_due_days') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Payment Due Days</label>
                                <input class="form-control" type="number" name="payment_due_days" min="1" max="90"
                                    value="{{ old('payment_due_days', 15) }}" />
                                @if ($errors->has('payment_due_days'))
                                    <div class="form-control-feedback">{{ $errors->first('payment_due_days') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('invoice_prefix') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Invoice Prefix</label>
                                <input class="form-control" type="text" name="invoice_prefix" maxlength="30"
                                    value="{{ old('invoice_prefix') }}" />
                                @if ($errors->has('invoice_prefix'))
                                    <div class="form-control-feedback">{{ $errors->first('invoice_prefix') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('monthly_charges_amount') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Monthly Charges Amount</label>
                                <input class="form-control" type="number" name="monthly_charges_amount" min="0" step="0.01"
                                    value="{{ old('monthly_charges_amount', 0) }}" />
                                @if ($errors->has('monthly_charges_amount'))
                                    <div class="form-control-feedback">{{ $errors->first('monthly_charges_amount') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('is_auto_invoice') ? 'has-danger' : '' }}">
                                <label class="form-control-label d-block">Auto Invoice Generation</label>
                                <label>
                                    <input type="checkbox" name="is_auto_invoice" value="1" {{ old('is_auto_invoice', $errors->any() ? null : '1') ? 'checked' : '' }}>
                                    Enable
                                </label>
                                @if ($errors->has('is_auto_invoice'))
                                    <div class="form-control-feedback">{{ $errors->first('is_auto_invoice') }}</div>
                                @endif
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-md-4">
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('storage/images/placeholder.jpg') }}"
                                    class="thumb-img img-fluid width-100" alt="img"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group {{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                <label for="vdimg" class="form-control-label">Company Logo</label>
                                <br />
                                <label for="vdimg" class="custom-file m-t-20">
                                    <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <a href="#">
                                <img id="posbimg" src="{{ asset('storage/images/placeholder.jpg') }}"
                                    class="thumb-img img-fluid width-100" alt="img"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group {{ $errors->has('posbgimg') ? 'has-danger' : '' }} ">
                                <label for="posbgimg" class="form-control-label">POS Background</label>
                                <br />
                                <label for="posbgimg" class="custom-file m-t-20">
                                    <input type="file" name="posbgimg" id="posbgimg" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                                @if ($errors->has('posbgimg'))
                                    <div class="form-control-feedback">{{ $errors->first('posbgimg') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <a href="#">
                                <img id="previewordercallingbgimg" src="{{ asset('storage/images/placeholder.jpg') }}"
                                    class="thumb-img img-fluid width-100" alt="img"
                                    style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group {{ $errors->has('ordercallingbgimg') ? 'has-danger' : '' }} ">
                                <label for="ordercallingbgimg" class="form-control-label">Order Calling Display</label>
                                <br />
                                <label for="ordercallingbgimg" class="custom-file m-t-20">
                                    <input type="file" name="ordercallingbgimg" id="ordercallingbgimg"
                                        class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>
                                @if ($errors->has('ordercallingbgimg'))
                                    <div class="form-control-feedback">{{ $errors->first('ordercallingbgimg') }}</div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <button type="submit" id="btnsubmit"
                        class="btn btn-md btn-primary waves-effect waves-light f-right"> Create Company </button>



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

        $("#posbgimg").change(function() {
            readURL(this, 'posbimg');
        });

        $("#ordercallingbgimg").change(function() {
            readURL(this, 'previewordercallingbgimg');
        });
    </script>
@endsection
