@extends('layouts.master-layout')

@section('title','Floors List')

@section('breadcrumtitle','Add Expense')

@section('navfloor','active')
@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">CREATE NEW FLOOR</h5>
            </div>
            <div class="card-block">

                <form method="POST" id="floorform" class="form-horizontal">
                    @csrf
                    <input class="form-control" type="hidden"
                           name="floorid" id="floorid" />
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Floor Name</label>
                                <input class="form-control" type="text"
                                       name="floorname" id="floorname" />

                                <div class="form-control-feedback text-danger" style="display: none;" id="dptname_alert"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Table Qty</label>
                                <input class="form-control" type="text"
                                       name="tableQty" id="tableQty" />

                                <div class="form-control-feedback text-danger" style="display: none;" id="dptname_alert"></div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <div class="form-group row">
                                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Floor"><i class="icofont icofont-plus"
                                    ></i>&nbsp; Save</button>
                                <button style="display: none;" class="btn btn-circle btn-info f-left m-t-30 m-l-20"  type="button" id="btn_update" data-toggle="tooltip" data-placement="top" title="" data-original-title="Update Floor"><i class="icofont icofont-plus"
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
                <h5 class="card-header-text">FLOOR DETAILS</h5>


            </div>
            <div class="card-block">

                <div class="project-table">
                    <table class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Floor Name</th>
                            <th>Table Qty</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if($floors->count())
                                @foreach($floors as $floor)
                                    <tr>
                                        <td>{{$floor->floor_name}}</td>
                                        <td>{{$floor->table_qty}}</td>
                                        <td>
                                            <a onclick="edit('{{$floor->floor_id}}','{{$floor->floor_name}}','{{$floor->table_qty}}')" class="text-warning p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                            <a onclick="deleteFloor('{{$floor->floor_id}}')" class="text-danger p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-delete"></i></a>
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

        $("#btn_clear").on('click',function(){

            $("#floorform")[0].reset();
            $("#subdpt").tagsinput('removeAll');
            $('#btn_save').css('display','block');
            $('#btn_update').css('display','none');

        });

        $("#btn_save").on('click',function(){

            if($("#floorname").val() == ""){
                $("#floorname").focus();
                $("#deptname_alert").html('Floor name is required.');
            }else{

                $.ajax({
                    url:'{{ url("create-floors") }}',
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
                    url:'{{ url("update-floors") }}',s
                    type:"POST",
                    data:$('#floorform').serialize(),s
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
                            swal_alert('Successfully!',r.msg,'s',true);

                        }
                    }
                });

            }
        });

        function edit(id,name,qty){
            $('#floorname').val(name);
            $('#floorid').val(id);
            $('#tableQty').val(qty);
            $('#btn_save').css('display','none');
            $('#btn_update').css('display','block');

        }

        function deleteFloor(id) {
            swal({
                    title: "Are you sure?",
                    text: "This Floor will be deleted!",
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
                            url: "{{url('/delete-floors')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Floor deleted successfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/view-floors')}}";
                                        }
                                    });
                                }
                            }

                        });


                    }else {
                        swal("Cancelled", "Your Floor is safe :)", "error");
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
                        window.location="{{ url('view-floors') }}";
                    }
                }
            });
        }
    </script>
@endsection