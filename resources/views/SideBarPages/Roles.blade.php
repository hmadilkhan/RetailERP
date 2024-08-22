@extends('layouts.master-layout')

@section('title','Role')

@section('breadcrumtitle','Role')

@section('navroles','active')

@section('content')
    <section class="panels-wells">
{{--        @if(session()->has('success'))--}}
{{--            <div class="alert alert-success">--}}
{{--                {{ session()->get('success')}}--}}
{{--            </div>--}}
{{--        @endif--}}

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-text">Role Managment</h5>
                    </div>
                    <div class="card-block">
                            <div class="row ">
                                <input type="hidden" name="mode" id="mode" value="0">
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Select Role:</label>
                                        <select name="role" id="role" data-placeholder="Select Role" class="form-control select2" >
                                            <option value="0">Select Role</option>
                                            @if($getroles)
                                                @foreach($getroles as $value)
                                                    <option value="{{ $value->role_id }}">{{ $value->role }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-group">
                                        <label class="form-control-label">Select Pages:</label>
                                        <select name="pages" id="pages" data-placeholder="Select Pages" class="form-control select2"  >
                                            <option value="0">Select Pages</option>
                                            @if($pages)
                                                @foreach($pages as $value)
                                                    <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="button-group ">
                                <button type="button" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="store()" > <i class="icofont icofont-plus"> </i>
                                    Submit
                                </button>
                            </div>

                    </div>
                </div>

                </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-text">Roles Details</h5>
                        <a class="f-right" href="{{ url('/roles')}}">refresh</a>
                    </div>
                    <div class="card-block">

                        <table id="tblrole" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                            <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Pages</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($roles)
                                @for($i=0;$i < sizeof($roles);$i++)
                                    <tr>
                                        <td >{{$roles[$i]->role}}</td>
                                        <td style="cursor: pointer;"  >
                                            @for($j=0;$j < sizeof($roledetails);$j++)
                                                @if($roledetails[$j]->role_id == $roles[$i]->role_id)
                                                    {{ $roledetails[$j]->page_name }},
                                                @endif
                                            @endfor
                                        </td>
                                        <td class="action-icon">
{{--                                            <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit('')"><i class="icofont icofont-plus text-primary f-18" onclick="addmore('{{$roles[$i]->role_id}}')"></i> </a>--}}
                                            <i class="icofont icofont-ui-delete text-danger f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onclick="remove('{{$roles[$i]->role_id}}')"></i>

                                        </td>
                                    </tr>
                                @endfor
                            @endif




                            </tbody>


                        </table>


                    </div>

                </div>
            </div>
            </div>
        </div>



    </section>
@endsection
<div class="modal fade modal-flex" id="pages-modal" tabindex="-1" role="dialog">

        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Modal</h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group">
                                <table id="tblpages" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                                    <thead>
                                    <tr>
                                        <th>Page Name</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
{{--                <div class="modal-footer">--}}
{{--                    <button type="submit"  class="btn btn-primary waves-effect waves-light" onClick="update()">Update</button>--}}
{{--                </div>--}}
            </div>
        </div>

</div>
<div class="modal fade modal-flex" id="addmore-modal" tabindex="-1" role="dialog">
    <form method="POST" action="{{ url('/insert-role') }}" class="form-horizontal" enctype="multipart/form-data">
        @csrf
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add More Pages</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="mode" id="mode" value="1">
                <input type="hidden" name="addmoreRoleid" id="addmoreRoleid" value="">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Select Page:</label>
                            <select name="pagename[]" id="pagename" data-placeholder="Select Page" class="form-control select2" multiple="" >
                                <option value="0">Select Page</option>
                                @if($details)
                                    @foreach($details as $value)
                                        <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit"  class="btn btn-primary waves-effect waves-light">Add More Pages</button>
            </div>
        </div>
    </div>
    </form>

</div>
@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();

        //
        // $('.table').DataTable({
        //
        //     bLengthChange: true,
        //     displayLength: 10,
        //     info: false,
        //     language: {
        //         search:'',
        //         searchPlaceholder: 'Search Page Name',
        //         lengthMenu: '<span></span> _MENU_'
        //
        //     }
        //
        // });

        function remove(roleid) {

            $.ajax({
                url: "{{url('/getbyroleid')}}",
                type: 'GET',
                dataType:"json",
                data:{_token:"{{ csrf_token() }}",
                    roleid:roleid,
                },
                success:function(result){
                    if(result){
                        $("#tblpages tbody").empty();
                        for(var count =0;count < result.length; count++){

                            $("#tblpages tbody").append(
                                "<tr>" +
                                "<td>"+result[count].page_name+"</td>" +
                                "<td class='action-icon'><a class='m-r-10' onclick='deletepage("+result[count].id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-ui-delete text-danger f-18' ></i></a></td>"+
                                "</tr>"
                            );
                        }

                    }
                }
            });
            $('#pages-modal').modal('show');

        }

        function addmore(roleid) {
            $('#addmoreRoleid').val(roleid);
            $('#addmore-modal').modal('show');

        }



        function deletepage(id) {
            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete",
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
                            url: "{{url('/deletepagesetting')}}",
                            type: 'PUT',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Successfully Deleted!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/roles') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Operation Cancelled :)", "error");
                    }
                });
        }


        function getchilds(id) {
            $.ajax({
                url: "{{url('/getpageschild')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",
                    dataType:"json",
                    parentid:id,
                },
                success:function(resp){
                    console.log(resp);
                    if(resp){
                            $("#child").empty();
                            $("#grandchild").empty();
                            $("#grandgrandchild").empty();
                            for (var count = 0; count < resp.length; count++) {
                                if(resp[count].page_mode == "Child") {
                                $("#child").append("<option value=''>Select Child</option>");
                                $("#child").append(
                                    "<option value='" + resp[count].id + "'>" + resp[count].page_name + "</option>");
                            }
                                else if(resp[count].page_mode == "Grand Child") {
                                    $("#grandchild").append("<option value=''>Select Grand Child</option>");
                                    $("#grandchild").append(
                                        "<option value='" + resp[count].id + "'>" + resp[count].page_name + "</option>");
                                }
                                else if(resp[count].page_mode == "Grand Grand Child") {
                                    $("#grandgrandchild").append("<option value=''>Select Grand Grand Child</option>");
                                    $("#grandgrandchild").append(
                                        "<option value='" + resp[count].id + "'>" + resp[count].page_name + "</option>");
                                }
                            }



                        }
                    }

            });
        }

        
        function store() {
            if($('#role').val() == 0)
            {
                swal({
                    title: "Error",
                    text: "Please Select Role First!",
                    type: "error"
                });
            }
            else if($('#pages').val() == 0){
                swal({
                    title: "Error",
                    text: "Please Select Page First!",
                    type: "error"
                });
            }
            else{
            $.ajax({
                url: "{{url('/insert-role')}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",
                    dataType:"json",
                    pages:$('#pages').val(),
                    role:$('#role').val(),
                    mode:$('#mode').val(),
                },
                success:function(resp){
                    if(resp == 1){
                        swal({
                            title: "Success",
                            text: "Successfully Insert!",
                            type: "success"
                        });
                    }
                    else{
                        swal({
                            title: "Error",
                            text: "Already Exsist!",
                            type: "error"
                        });
                    }
                }
            });
            }
        }

    </script>
@endsection