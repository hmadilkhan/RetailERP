@extends('layouts.master-layout')

@section('title','Business Policy')

@section('breadcrumtitle','View Policies')

@section('navVendorPO','active')

@section('navtax','active')

@section('content')

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Edit Tax</h5>
            </div>
            <div class="card-block">
                <form method="post" action="{{ url('/tax-insert') }}" id="upload_form" enctype="multipart/form-data">

                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-lg-3 col-md-3">

                            <div class="form-group {{ $errors->has('taxname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Tax Head Name</label>
                                <input class="form-control" type="text"
                                       name="taxname" id="taxname"   />
                                @if ($errors->has('taxname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group {{ $errors->has('taxpercentage') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Tax Percentage</label>
                                <input class="form-control" type="text"
                                       name="taxpercentage" id="taxpercentage"    />
                                @if ($errors->has('taxpercentage'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 m-t-15 ">
                            <div class="form-group row">
                                <div class="col-md-10 has-success">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" name="purchase" class="custom-control-input" >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description f-18">Show In Purchase</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 m-t-15">
                            <div class="form-group row">
                                <div class="col-md-10 has-success">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" name="pos" class="custom-control-input" >
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description f-18">Show In POS</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > Create Rule </button>

                </form>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();


    </script>


@endsection



