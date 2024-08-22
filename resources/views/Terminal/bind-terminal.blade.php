@extends('layouts.master-layout')

@section('title','Terminals')

@section('breadcrumtitle','Terminals').

@section('navterminals','active')

@section('content')

<section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h1 class="">Bind Terminals For ( {{$terminal_name[0]->terminal_name}} ) </h1>
            <h5 class=""><a href="{{ url('terminals') }}/{{Crypt::encrypt($branch)}}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
        </div>
        <div class="card-block">
            <form method="POST" action="{{url('save-bind-terminals')}}">
                @csrf
                <div class="row">
                    <input type="hidden" name="terminalID" value="{{$terminalID}}" />
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Terminals</label>

                            <select name="terminal" id="terminal" data-placeholder="Select Terminal" class="form-control select2">
                                <option value="">Select Terminal</option>
                                @if($terminals)
                                @foreach($terminals as $value)
                                @if($value->terminal_id != $terminalID)
                                <option value="{{ $value->terminal_id }}">{{ $value->terminal_name }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            <div class="form-control-feedback"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 m-t-25">
                        <label class="form-control-label"></label>
                        <button class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <div class="card">

        <div class="card-header">
            <h5 class="card-header-text">Bind Terminal Detail </h5>
        </div>
        <div class="card-block">
            <table id="tblterminals" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Terminal Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bindTerminals as $value)
                    <tr>
                        <td>{{$value->terminal_name}}</td>
                        <td>
                            <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
                        </td>
                    </tr>
                    @endforeach


                </tbody>
            </table>
        </div>
    </div>




</section>


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
        info: false,
        language: {
            search: '',
            searchPlaceholder: 'Search Terminals',
            lengthMenu: '<span></span> _MENU_'

        }

    });

    $('.alert-confirm').on('click', function() {
        var id = $(this).data("id");

        swal({
                title: "Are you sure?",
                text: "Do You want to unbind this Terminal?",
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
                        url: "{{url('/delete-bind-terminals')}}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                        },
                        success: function(resp) {
                            if (resp == 1) {
                                swal({
                                    title: "success",
                                    text: "Terminal unbind Successfully!",
                                    type: "success"
                                }, function(isConfirm) {
                                    if (isConfirm) {
                                        location.reload();
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








    function edit(id, name, mac, branchid) {
        $('#update-modal').modal('show');
        $('#terminalnamemodal').val(name);
        $('#macmodal').val(mac);
        $('#terminalid').val(id);
        $('#branchmodal').val(branchid).change();

    }


    function update() {

        $.ajax({
            url: "{{url('/update-terminal')}}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                terminalid: $('#terminalid').val(),
                branch: '{{$branch}}',
                terminalname: $('#terminalnamemodal').val(),
                macaddress: $('#macmodal').val(),
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
                            window.location = "{{url('terminals',$branch)}}";
                        }
                    });
            }
        });
    }








    $('#chkactive').change(function() {
        if (this.checked) {

            $.ajax({
                url: "{{url('/inactive-terminals-details')}}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{$branch}}"
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
                                "<td class='action-icon'><a class='m-r-10' onclick='reactive(" + result[count].terminal_id + ")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>" +
                                "</tr>"
                            );
                        }

                    }
                }
            });
        } else {
            window.location = "{{ url('/terminals',$branch) }}";
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
                        url: "{{url('/reactive-terminal')}}",
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
                                        window.location = "{{ url('/terminals',$branch) }}";
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