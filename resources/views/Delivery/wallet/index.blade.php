@extends('layouts.master-layout')

@section('title','Bank Discount')

@section('navdelivery','active')
@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Wallet Discount</h5>
            </div>
            <div class="card-block">

                <form method="POST" id="floorform" class="form-horizontal">
                    @csrf
                    <input class="form-control" type="hidden"
                           name="id" id="id" />
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Wallet Name</label>
                                <select class="form-control select2" data-placeholder="Select Wallet" id="wallet" name="wallet">
                                    <option value="">Select Wallet</option>
                                    @if($wallets)
                                        @foreach($wallets as $value)
                                            <option value="{{ Crypt::encrypt($value->id) }}">{{ $value->provider_name}}</option>
                                        @endforeach
                                    @endif
                                </select>

                                <div class="form-control-feedback text-danger" style="display: none;" id="dptname_alert"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Discount Percentage</label>
                                <input type="number" class="form-control" name="discount_percentage" id="discount_percentage">

                                <div class="form-control-feedback text-danger" style="display: none;" id="discont_percentage_alert"></div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <div class="form-group row">
                                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus"
                                    ></i>&nbsp; Save</button>
                                <button style="display: none;" class="btn btn-circle btn-info f-left m-t-30 m-l-20"  type="button" id="btn_update" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus"
                                    ></i>&nbsp; Update</button>
                                <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error"
                                    ></i> Clear</button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </section>

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Wallet Discount Details</h5>


            </div>
            <div class="card-block">

                <div class="project-table">
                    <table class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Wallet</th>
                            <th>Percentage</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if($wallet_discount)
                                @foreach($wallet_discount as $val)
                                    <tr>
                                        <td class="text-center">
                                            <img width="50" height="50" src="{{ asset('storage/images/service-provider/'.(!empty($val->image) ? $val->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                        </td>
                                        <td>{{$val->provider_name}}</td>
                                        <td>{{$val->percentage}} %</td>
                                        <td>
                                            <a onclick="edit('{{$val->id}}','{{$val->id}}','{{$val->percentage}}')" class="text-warning p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                            <a onclick="deleteDiscount('{{$val->id}}')" class="text-danger p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-delete"></i></a>
                                        </td>

                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();
        $("#btn_clear").on('click',function(){

            $("#floorform")[0].reset();
            $("#subdpt").tagsinput('removeAll');
            $('#btn_save').css('display','block');
            $('#btn_update').css('display','none');

        });

        $("#btn_save").on('click',function(){

            if($("#bank").val() == "") {
                $("#bank").focus();
                $("#deptname_alert").html('Floor name is required.');
            }else if($("#discont_percentage").val() == ""){
                    $("#discont_percentage").focus();
                    $("#discont_percentage_alert").html('Floor name is required.');
            }else{

                $.ajax({
                    url:'{{ url("create-bank-discount") }}',
                    type:"POST",
                    data:$('#floorform').serialize(),
                    dataType:"json",
                    success:function(r){
                        if(r.state == 1){
                            if(r.contrl != ""){
                                $("#"+r.contrl).focus();
                                $("#"+r.contrl+"_alert").html(r.msg);
                                location.reload();
                            }
                            swal_alert('Alert!',r.msg,'error',false);

                        }else {
                            $("#deptname_alert").html('');
                            swal_alert('Successfully!',r.msg,'success',true);

                        }
                    }
                });

            }
        });

        $("#btn_update").on('click',function(){

            if($("#floorname").val() == ""){
                $("#floorname").focus();
                $("#deptname_alert").html('Floor name is required.');
            }else{

                $.ajax({
                    url:'{{ url("update-bank-discount") }}',
                    type:"POST",
                    data:$('#floorform').serialize(),
                    dataType:"json",
                    success:function(r){
                        if(r.state == 1){
                            if(r.contrl != ""){
                                $("#"+r.contrl).focus();
                                $("#"+r.contrl+"_alert").html(r.msg);
                                location.reload();
                            }
                            swal_alert('Alert!',r.msg,'error',false);

                        }else {
                            $("#deptname_alert").html('');
                            swal_alert('Successfully!',r.msg,'success',true);

                        }
                    }
                });

            }
        });

        function edit(id,bank,percentage){
            $('#id').val(id);
            $('#bank').val(bank).change();
            $('#discount_percentage').val(percentage);
            $('#btn_save').css('display','none');
            $('#btn_update').css('display','block');

        }

        function deleteDiscount(id) {
            swal({
                    title: "Are you sure?",
                    text: "This discount will be deleted!",
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
                            url: "{{url('/delete-bank-discount')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Discount deleted successfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/view-bank-discount')}}";
                                        }
                                    });
                                }
                            }

                        });


                    }else {
                        swal("Cancelled", "Your discount is safe :)", "error");
                    }
                });
        }

        function swal_alert(title,msg,type,mode){

            swal({
                title: title,
                text: msg,
                type: type
            },function(isConfirm){
                if(isConfirm){
                    if(mode==true){
                        window.location="{{ url('view-bank-discount') }}";
                    }
                }
            });
        }
    </script>
@endsection
