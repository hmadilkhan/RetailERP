@extends('layouts.master-layout')

@section('title', 'Terminals')

@section('breadcrumtitle', 'Terminals').

@section('navterminals', 'active')

@section('content')

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create New Terminal</h5>
                <h5 class=""><a href="{{ url('branches') }}"><i
                            class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip"
                            data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            </div>
            <div class="card-block">

                <div class="row">
                    <!--    <div class="col-lg-4 col-md-4">
                    <div class="form-group">
                    <label class="form-control-label">Branch</label>
                     
                    <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2"  >
                        <option value="">Select Branch</option>
                        @if ($getbranch)
                          @foreach ($getbranch as $value)
    <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
    @endforeach
                        @endif
                    </select>
                     <div class="form-control-feedback"></div>
                      </div>
                  </div> -->

                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Terminal Name:</label>
                            <input type="text" name="terminalname" id="terminalname" class="form-control" />
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">MAC Address:</label>
                            <input type="text" name="macaddress" id="macaddress" class="form-control" />
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Device Serial Number:</label>
                            <input type="text" name="serial_no" id="serial_no" class="form-control" />
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Model No:</label>
                            <input type="text" name="modelno" id="modelno" class="form-control" />
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>

                    <div class="button-group ">
                        <label class="form-control-label"></label>
                        <button type="button" id="btnsubmit"
                            class="btn btn-md btn-success waves-effect waves-light m-t-20 " onclick="submit()">
                            <i class="icofont icofont-plus"> </i>
                            Add Terminal
                        </button>
                    </div>

                </div>


            </div>
        </div>
        <div class="card">

            <div class="card-header">
                <h5 class="card-header-text">Terminal Detail </h5>
            </div>
            <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkactive" class="mainchk">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions">Show In-Active Terminals</div>
                </div>
                <br />
                <br />
                <table id="tblterminals" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Terminal id</th>
                            <th>Branch</th>
                            <th>Terminal Name</th>
                            <th>MAC Address</th>
                            <th>Serial Number</th>
                            <th>Model No</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($terminals as $value)
                            <tr>
                                <td>{{ $value->terminal_id }}</td>
                                <td>{{ $value->branch_name }}</td>
                                <td>{{ $value->terminal_name }}</td>
                                <td>{{ $value->mac_address }}</td>
                                <td>{{ $value->serial_no }}</td>
                                <td>{{ $value->model_no }}</td>
                                <td>{{ $value->status_name }}</td>

                                <td class="action-icon">
                                    <a class="m-r-10" data-toggle="tooltip" data-placement="top" title=""
                                        data-original-title="Edit"
                                        onclick="edit('{{ $value->terminal_id }}','{{ $value->terminal_name }}','{{ $value->mac_address }}','{{ $value->branch_id }}','{{ $value->serial_no }}','{{ $value->model_no }}')"><i
                                            class="icofont icofont-ui-edit text-primary f-18"></i> </a>

                                    <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm"
                                        data-id="{{ $value->terminal_id }}" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Delete"></i>

                                    <a href="{{ url('/permission') }}/{{ Crypt::encrypt($value->terminal_id) }}"
                                        class="icofont icofont icofont-layout text-info f-18"
                                        data-id="{{ $value->terminal_id }}" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Permission"></a>

                                    <a href="{{ url('/printing-details') }}/{{ Crypt::encrypt($value->terminal_id) }}"
                                        class="icofont  icofont icofont-print text-success f-18"
                                        data-id="{{ $value->terminal_id }}" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Print Details"></a>

                                    <a href="{{ url('/bind-terminals') }}/{{ Crypt::encrypt($value->terminal_id) }}/{{ Crypt::encrypt($branch) }}"
                                        class="icofont  icofont icofont-at text-success f-18"
                                        data-id="{{ $value->terminal_id }}" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Bind Terminal"></a>


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
                        {{--
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Branch</label>

                                <!--  <select name="branchmodal" id="branchmodal" data-placeholder="Select Branch" class="form-control select2"  >
                        <option value="">Select Branch</option>
                        @if ($getbranch)
                          @foreach ($getbranch as $value)
    <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
    @endforeach
                        @endif
                    </select> -->
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <label class="form-control-label">Terminal Name:</label>

                            <input type="text" name="terminalnamemodal" id="terminalnamemodal"
                                class="form-control" />

                            <input type="hidden" name="terminalid" id="terminalid" class="form-control" />

                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-control-label">MAC Address:</label>

                            <input type="text" name="macmodal" id="macmodal" class="form-control" />

                        </div>

                        <div class="col-lg-6 col-md-6">
                            <label class="form-control-label">Model No:</label>

                            <input type="text" name="modelnoupdate" id="modelnoupdate" class="form-control" />

                        </div>

                        <div class="col-lg-6 col-md-6">
                            <label class="form-control-label">Serial No:</label>

                            <input type="text" name="model_serial_no" id="model_serial_no" class="form-control" />

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect waves-light" onClick="update()"><i
                            class="icofont icofont-ui-edit"></i>&nbsp; Update</button>
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



        $('#tblterminals').DataTable({

            bLengthChange: true,
            displayLength: 10,
            "order": [0, "DESC"],
            info: false,
            language: {
                search: '',
                searchPlaceholder: 'Search Terminals',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        function submit() {
            if ($('#terminalname').val() == "") {
                swal("Missing Fields", "Please enter terminal name :)", "error");
            } else if ($('#macaddress').val() == "") {
                swal("Missing Fields", "Please enter MAC Address :)", "error");
            } else {

                $.ajax({
                    url: "{{ url('/submitterminal') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        branch: '{{ $branch }}',
                        terminalname: $('#terminalname').val(),
                        macaddress: $('#macaddress').val(),
                        serial_no: $('#serial_no').val(),
                        modelno: $('#modelno').val(),
                    },
                    dataType: "json",
                    success: function(resp) {
                        console.log(resp)
                        if (resp != 0) {
                            swal({
                                    title: "Operation Performed",
                                    text: "Terminal Added Successfully!",
                                    type: "success"
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        window.location = "{{ url('terminals', Crypt::encrypt($branch)) }}";
                                    }
                                });
                        } else {
                            swal({
                                title: "ALready Exists!",
                                text: "Terminal or Mac Address already in use !",
                                type: "error"
                            });
                        }


                    }
                });
            }
        }

        $('.alert-confirm').on('click', function() {
            var id = $(this).data("id");

            swal({
                    title: "Are you sure?",
                    text: "Do You want to In-Active Terminal?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "In-Active!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{ url('/inactive-terminal') }}",
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}",
                                terminalid: id,
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "success",
                                        text: "Terminal In-Active Successfully!",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location =
                                                "{{ url('/terminals', Crypt::encrypt($branch)) }}";
                                        }
                                    });
                                }
                            }

                        });

                    } else {
                        swal("Cancelled", "Your Terminal is safe :)", "error");
                    }
                });
        });

        function edit(id, name, mac, branchid, serialNo,modelNo) {
            $('#update-modal').modal('show');
            $('#terminalnamemodal').val(name);
            $('#macmodal').val(mac);
            $('#terminalid').val(id);
            $('#model_serial_no').val(serialNo);
            $('#modelnoupdate').val(modelNo);
            $('#branchmodal').val(branchid).change();

        }


        function update() {

            $.ajax({
                url: "{{ url('/update-terminal') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    terminalid: $('#terminalid').val(),
                    branch: '{{ $branch }}',
                    terminalname: $('#terminalnamemodal').val(),
                    macaddress: $('#macmodal').val(),
                    serial_no: $('#model_serial_no').val(),
                    modelno: $('#modelnoupdate').val(),
                },
                dataType: "json",
                success: function(resp) {
                    swal({
                            title: "Operation Performed",
                            text: "Terminal Updated Successfully!",
                            type: "success"
                        },
                        function(isConfirm) {
                            if (isConfirm) {
                                $('#update-modal').modal('hide');
                                window.location = "{{ url('terminals', Crypt::encrypt($branch)) }}";
                            }
                        });
                }
            });
        }








        $('#chkactive').change(function() {
            if (this.checked) {

                $.ajax({
                    url: "{{ url('/inactive-terminals-details') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: "{{ $branch }}"
                    },
                    dataType: "json",
                    success: function(result) {
                        console.log(result);
                        if (result) {
                            $("#tblterminals tbody").empty();
                            for (var count = 0; count < result.length; count++) {

                                $("#tblterminals tbody").append(
                                    "<tr>" +
                                    "<td>" + result[count].terminal_id + "</td>" +
                                    "<td>" + result[count].branch_name + "</td>" +
                                    "<td>" + result[count].terminal_name + "</td>" +
                                    "<td>" + result[count].mac_address + "</td>" +
                                    "<td>" + result[count].status_name + "</td>" +
                                    "<td class='action-icon'><a class='m-r-10' onclick='reactive(" +
                                    result[count].terminal_id +
                                    ")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>" +
                                    "</tr>"
                                );
                            }

                        }
                    }
                });
            } else {
                window.location = "{{ url('/terminals', Crypt::encrypt($branch)) }}";
            }
        });

        function reactive(id) {
            swal({
                    title: "Are you sure?",
                    text: "You want to Re-Active Terminal!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "yes plx!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{ url('/reactive-terminal') }}",
                            type: 'PUT',
                            data: {
                                _token: "{{ csrf_token() }}",
                                terminalid: id,
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Re-Active",
                                        text: "Terminal Re-Active Successfully!",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location =
                                                "{{ url('/terminals', Crypt::encrypt($branch)) }}";
                                        }
                                    });
                                }
                            }

                        });

                    } else {
                        swal("Cancelled", "Operation Cancelled:)", "error");
                    }
                });
        }
    </script>

@endsection
