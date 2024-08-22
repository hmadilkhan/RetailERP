@extends('layouts.master-layout')

@section('title','Delivery Charges')

@section('breadcrumtitle','Delivery Charges').
@section('navdelivery','active')
@section('navcharges','active')


@section('content')

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Delivery Charges</h5>
            </div>
            <div class="card-block">

                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Branch</label>

                            <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2"  >
                                <option value="">Select Branch</option>
                                @if($getbranch)
                                    @foreach($getbranch as $value)
                                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Area Name:</label>
                            <input type="text" name="areaname" id="areaname" class="form-control"/>
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Charges:</label>
                            <input type="text" name="charges" id="charges" class="form-control"/>
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="button-group ">
                        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right m-r-20" onclick="submit()" >
                            <i class="icofont icofont-plus"> </i>
                            Add Delivery Charges
                        </button>
                    </div>

                </div>


            </div>
        </div>
        <div class="card">

            <div class="card-header">
                <h5 class="card-header-text">Delivery Charges Detail</h5>
            </div>
            <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkactive" class="mainchk">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions">Show In-Active Delivery Charges</div>
                </div>
                <br/>
                <br/>
                <table id="tblcharges" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th>Branch Name</th>
                        <th>Area Name</th>
                        <th>Charges</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($charges as $value)
                        <tr>
                            <td >{{$value->branch_name}}</td>
                            <td >{{$value->area_name}}</td>
                            <td >{{$value->charges}}</td>
                            <td >{{$value->status_name}}</td>
                            <td class="action-icon">

                                <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit('{{ $value->id }}','{{ $value->branch_id }}','{{ $value->area_name }}','{{ $value->charges }}')"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                                <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>




    </section>

    <!-- modals -->
    <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Modal</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Branch</label>

                                <select name="branchmodal" id="branchmodal" data-placeholder="Select Branch" class="form-control select2"  >
                                    <option value="">Select Branch</option>
                                    @if($getbranch)
                                        @foreach($getbranch as $value)

                                            <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <label class="form-control-label">Area Name:</label>

                            <input type="text" name="areanamemodal" id="areanamemodal" class="form-control"/>

                            <input type="hidden" name="chargesid" id="chargesid" class="form-control"/>

                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-control-label">Delivery Charges:</label>

                            <input type="text" name="chargesmodal" id="chargesmodal" class="form-control"/>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect waves-light" onClick="update()"><i class="icofont icofont-ui-edit"></i>&nbsp; Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();

        $("#mobnumb").tagsinput({
            maxTags: 10
        });



        $('#tblcharges').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Delivery Charges',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        function submit(){

            if ($('#branch').val() == "") {
                swal({
                    title: "Error Message!",
                    text: "Please Select Branch!",
                    type: "error"});
            }
            else if ($('#areaname').val() == "") {
                swal({
                    title: "Error Message!",
                    text: "Please Enter Area Name!",
                    type: "error"});
            }
            else if ($('#charges').val() == "") {
                swal({
                    title: "Error Message!",
                    text: "Please Enter Charges!",
                    type: "error"});
            }
            else{
                $.ajax({
                    url: "{{url('/insert-charges')}}",
                    type:"POST",
                    data:{_token:"{{ csrf_token() }}",
                        branch:$('#branch').val(),
                        areaname:$('#areaname').val(),
                        charges:$('#charges').val(),
                    },
                    dataType:"json",
                    success:function(resp){
                        if (resp != 0) {
                            swal({
                                    title: "Operation Performed",
                                    text: "Delivery Charges Added Successfully!",
                                    type: "success"},
                                function(isConfirm){
                                    if(isConfirm){
                                        window.location = "{{url('/delivery-charges')}}";
                                    }
                                });
                        }
                        else{
                            swal({
                                title: "ALready Exsist!",
                                text: "Delivery Charges of this Area ALready Exsist!",
                                type: "error"});
                        }


                    }
                });
            }
        }

        $('.alert-confirm').on('click',function(){
            var id= $(this).data("id");

            swal({
                    title: "Are you sure?",
                    text: "Do You want to In-Active Delivery Charges?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "In-Active!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{url('/inactive-charges')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                chargesid:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "success",
                                        text: "Delivery Charges In-Active Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/delivery-charges') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Operation Cancelled :)", "error");
                    }
                });
        });








        function edit(id,branchid,name,charges){
            $('#update-modal').modal('show');
            $('#areanamemodal').val(name);
            $('#chargesmodal').val(charges);
            $('#chargesid').val(id);
            $('#branchmodal').val(branchid).change();

        }


        function update(){

            $.ajax({
                url: "{{url('/update-charges')}}",
                type:"POST",
                data:{_token:"{{ csrf_token() }}",
                    branch:$('#branchmodal').val(),
                    areaname:$('#areanamemodal').val(),
                    charges:$('#chargesmodal').val(),
                    chargesid:$('#chargesid').val(),
                },
                dataType:"json",
                success:function(resp){
                    if (resp != 0) {
                        swal({
                                title: "Operation Performed",
                                text: "Delivery Charges Updated Successfully!",
                                type: "success"},
                            function(isConfirm){
                                if(isConfirm){
                                    window.location = "{{url('/delivery-charges')}}";
                                }
                            });
                    }
                    else{
                        swal({
                            title: "ALready Exsist!",
                            text: "Delivery Charges of this Area ALready Exsist!",
                            type: "error"});
                    }
                }
            });
        }








        $('#chkactive').change(function(){
            if (this.checked) {
                $.ajax({
                    url: "{{url('/inacive-delivery-charges')}}",
                    type: 'GET',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                    },
                    success:function(result){
                        if(result){
                            $("#tblcharges tbody").empty();
                            for(var count =0;count < result.length; count++){

                                $("#tblcharges tbody").append(
                                    "<tr>" +
                                    "<td>"+result[count].branch_name+"</td>" +
                                    "<td>"+result[count].area_name+"</td>" +
                                    "<td>"+result[count].charges+"</td>" +
                                    "<td>"+result[count].status_name+"</td>" +
                                    "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                                    "</tr>"
                                );
                            }

                        }
                    }
                });
            }
            else{
                window.location="{{ url('/delivery-charges') }}";
            }
        });

        function reactive(id){
            swal({
                    title: "Are you sure?",
                    text: "You want to Re-Active Delivery Charges!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{url('/reactive-charges')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                chargesid:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Re-Active",
                                        text: "Delivery Charges Re-Active Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/delivery-charges') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
        }

    </script>

@endsection


