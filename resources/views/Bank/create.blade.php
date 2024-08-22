@extends('layouts.master-layout')

@section('title','Create Bank')

@section('breadcrumtitle','Create Company')

@section('navbank','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Bank</h5>
            </div>
            <div class="card-block">
                <form method="post" action="{{url('save-bank')}}" enctype="multipart/form-data">
                    @csrf
                    @method('post')
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Bank Name</label>
                            <input class="form-control" type="text" required="required" name="bankname" id="bankname"   />
                        </div>
                    </div>
                    <div class="col-md-4" >
                        <a href="#">
                            <img id="vdpimg" src="{{ asset('public/assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                        </a>
                        <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                            <label for="vdimg" class="form-control-label">Bank Logo</label>
                        </br>
                            <label for="vdimg" class="custom-file">
                                <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>

                            @if ($errors->has('vdimg'))
                                <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                            @endif
                        </div>
                    </div>
                    <button type="submit" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right"> Create Bank </button>
                </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        function readURL(input,id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#'+id).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#vdimg").change(function() {
            readURL(this,'vdpimg');
        });
    </script>
@endsection