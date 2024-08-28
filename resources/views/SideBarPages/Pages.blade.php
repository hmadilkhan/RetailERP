@extends('layouts.master-layout')

@section('title','Pages')

@section('breadcrumtitle','Pages')

@section('navpages','active')


@section('content')
    <section class="panels-wells">
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success')}}
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Side Bar Pages</h5>
            </div>
            <div class="card-block">

                <form method="POST" action="{{ url('/insert-page') }}" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="row ">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Enter Page Name:</label>
                                <input type="text" id="pagename" name="pagename" placeholder="Enter Page Name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Enter Page Url:</label>
                                <input type="text" id="pageurl" name="pageurl" placeholder="Enter Page Url" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Nav Class:</label>
                                <input type="text" id="navclass" name="navclass" placeholder="Enter Nav Class" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Icofont Class:</label>
                                <input type="text" id="icofont" name="icofont" placeholder="Enter Icofont Class" class="form-control" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Relate with Parent:</label>
                                <select name="parent" id="parent" data-placeholder="Select Parent" class="form-control select2" >
                                    <option value="0">Select Parent</option>
                                    @if($details)
                                        @foreach($details as $value)
                                            <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Page Mode:</label>
                                <div class="rkmd-checkbox checkbox-rotate f-right">
                                    <label class="input-checkbox checkbox-primary">
                                        <input type="checkbox" name="iconarrow" class="mainchk f-right ">
                                        <span class="checkbox"></span>
                                    </label>
                                    <div class="captions">Arrow</div>
                                </div>


                                <select name="pagemode" id="pagemode" data-placeholder="Select Page Mode" class="form-control select2" >
                                    <option value="">Select Page Mode</option>
                                    <option value="Parent">Parent</option>
                                    <option value="Child">Child</option>
                                    <option value="Grand Child">Grand Child</option>
                                    <option value="Grand Grand Child">Grand Grand Child</option>
                                    <option value="Label">Label</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="button-group ">
                        <button type="submit" class="btn btn-md btn-success waves-effect waves-light f-right" > <i class="icofont icofont-plus"> </i>
                            Submit
                        </button>
                    </div>

                </form>
            </div>
        </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-header-text">Pages Details</h5>
                </div>
                <div class="card-block">
<div class="row">
    <div class="col-lg-12 col-md-12">
        <table id="pagetable" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

            <thead>
            <tr>
                <th>Page Name</th>
                <th>Page Url</th>
                <th>Nav Class</th>
                <th>Icofont Class</th>
                <th>Parent</th>
                <th>Page Mode</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>

            @foreach($details as $value)
                <tr>
                    <td >{{$value->page_name}}</td>
                    <td >{{$value->page_url}}</td>
                    <td >{{$value->navclass}}</td>
                    <td >{{$value->icofont}}</td>
                    <td >{{$value->parent_id}}</td>
                    <td >{{$value->page_mode}}</td>

                    <td class="action-icon">
                        <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit('{{$value->id}}','{{$value->page_name}}','{{$value->page_url}}','{{$value->navclass}}','{{$value->icofont}}','{{$value->parent_id}}','{{$value->page_mode}}','{{$value->icofont_arrow}}')"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>
                        <i class="icofont icofont-ui-delete text-danger f-18" data-id="{{ $value->id }}" onclick="warningRemove({{ $value->id }})" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>

                    </td>

                </tr>
            @endforeach


            </tbody>


        </table>
    </div>

</div>

                </div>
            </div>
    </section>
    <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
        <form method="POST" action="{{ url('/update-page') }}" class="form-horizontal" enctype="multipart/form-data">
            @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Update Modal</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="pageid" id="pageid" value="">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Page Name:</label>
                                <input type="text" name="updatepagename" id="updatepagename" class="form-control" value="" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Page Url:</label>
                                <input type="text" name="updatepageurl" id="updatepageurl" class="form-control" value="" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Nav Class:</label>
                                <input type="text" name="updatenavclass" id="updatenavclass" class="form-control" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Icofont Class:</label>
                                <input type="text" name="updateicofont" id="updateicofont" class="form-control" value="" />
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Relate with Parent:</label>
                                <select name="updateparent" id="updateparent" data-placeholder="Select Parent" class="form-control select2" >
                                    <option value="0">Select Parent</option>
                                    @if($details)
                                        @foreach($details as $value)
                                            <option value="{{ $value->id }}">{{ $value->page_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Page Mode:</label>
                                <div class="rkmd-checkbox checkbox-rotate f-right">
                                    <label class="input-checkbox checkbox-primary">
                                        <input type="checkbox" name="updateiconarrow" id="updateiconarrow" class="mainchk f-right ">
                                        <span class="checkbox"></span>
                                    </label>
                                    <div class="captions">Arrow</div>
                                </div>


                                <select name="updatepagemode" id="updatepagemode" data-placeholder="Select Page Mode" class="form-control select2" >
                                    <option value="">Select Page Mode</option>
                                    <option value="Parent">Parent</option>
                                    <option value="Child">Child</option>
                                    <option value="Grand Child">Grand Child</option>
                                    <option value="Grand Grand Child">Grand Grand Child</option>
                                    <option value="Label">Label</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit"  class="btn btn-primary waves-effect waves-light" onClick="update()">Update</button>
                </div>
            </div>
        </div>
        </form>
    </div>


@endsection


@section('scriptcode_three')

    <script type="text/javascript">
            $(".select2").select2();


        $('#pagetable').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Page Name',
                lengthMenu: '<span></span> _MENU_'

            }

        });
        
        function warningRemove(uid){
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
                            url: "{{url('/remove-page')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:uid,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Successfully Deleted!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/pages') }}";
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

        //Alert confirm
        $('.alert-confirm').on('click',function(){
            var id= $(this).data("id");

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
                            url: "{{url('/remove-page')}}",
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
                                            window.location="{{ url('/pages') }}";
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


        function edit(id,pagename,pageurl,navclass,icofont,parentid,pagemode,arrow) {
            $('#pageid').val(id);
            $('#updatepagename').val(pagename);
            $('#updatepageurl').val(pageurl);
            $('#updatenavclass').val(navclass);
            $('#updateicofont').val(icofont);
            $('#updateparent').val(parentid).change();
            $('#updatepagemode').val(pagemode).change();
             // $('#updateparent').attr('Selected',parentid);
            if(arrow != 0){
                $('#updateiconarrow').attr('checked',true);
            }


    $('#update-modal').modal('show');
        }

    </script>
@endsection