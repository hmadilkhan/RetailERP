@extends('layouts.master-layout')

@section('title','Empty Data')

@section('breadcrumtitle','Empty Data')

@section('navemptydata','active')

@section('content')


    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Empty Database</h5>
            </div>

            <div class="card-block">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Select Company</label>
                            <select name="company" id="company" data-placeholder="Select Company" class="form-control select2"  >
                                <option value="">Select Company</option>
                                @if($company)
                                    @foreach($company as $value)
                                        <option value="{{ $value->company_id }}">{{ $value->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 m-b-5">
                        <div class="form-group row">
                            <div class="col-md-10 has-success">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="erp" id="erp" class="custom-control-input" >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">ERP Database</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 m-b-5">
                        <div class="form-group row">
                            <div class="col-md-10 has-success">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="hr" id="hr" class="custom-control-input" >
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">HR Database</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <button class="f-right btn btn-md btn-primary m-10" onclick="deletedata()">Delete Data</button>

            </div>
        </div>
        @endsection

        @section('scriptcode_three')
            <script type="text/javascript">
                $(".select2").select2();
                
                function deletedata() {
                    let erp = $('#erp').is(":checked") ? 1 : 0;
                    let hr = $('#hr').is(":checked") ? 1 : 0;
                        swal({
                                title: "Are you sure?",
                                text: "Do you want to Delete Database!!",
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
                                        url: "{{url('/delete_data')}}",
                                        type: 'POST',
                                        data:{_token:"{{ csrf_token() }}",
                                            copmanyid:$('#company').val(),
                                            erp:erp,
                                            hr:hr,
                                        },
                                        success:function(resp){
                                            console.log(resp);
                                            if(resp == 1){
                                                swal({
                                                    title: "Deleted",
                                                    text: "All Data Delete Successfully!!",
                                                    type: "success"
                                                },function(isConfirm){
                                                    if(isConfirm){
                                                        window.location="{{ url('/dashboard') }}";
                                                    }
                                                });
                                            }
                                        }

                                    });

                                }else {
                                    swal("Cancelled", "Operation Cancel :)", "error");
                                }
                            });
                }
            </script>
@endsection
