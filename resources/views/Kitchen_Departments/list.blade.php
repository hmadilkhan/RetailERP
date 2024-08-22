@extends('layouts.master-layout')

@section('title','Kitchen Department')

@section('breadcrumtitle','View Inventory')
@section('navkitchenDepartment','active')
@section('navinvent_depart','active')

@section('content')

    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text" id="title-hcard"> Create Kitchen Department</h5>
            </div>
            <div class="card-block">

                <form method="POST" id="deptform" class="form-horizontal">
                    @csrf

                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Kitchen Department Name</label>
                                <input class="form-control" type="text"
                                       name="deptname" id="deptname" />
                                <div class="form-control-feedback text-danger" id="dptname_alert"></div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">Select Department</label>
                                <select class="form-control select2" id="depart" name="depart[]" data-placeholder="Select Department" multiple="">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{$department->department_id}}">{{$department->department_name}}</option>
                                    @endforeach
                                </select>
                                <span class="form-control-feedback text-danger" id="subdpt_alert"></span>
                            </div>
                        </div>


                        <div class="col-lg-4 col-md-4">
                            <div class="form-group row">
                                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus"
                                    ></i>&nbsp; Save</button>.
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
                <h5 class="card-header-text" id="title-hcard"> Kitchen Departments List</h5>
            </div>
            <div class="card-block">
                <table id="mainTable" class="table table-striped full-width">
                    <thead>
                    <tr>
                        <th>Main Department</th>
                        <th>Sub Departments</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($general as $value)
                            <tr>
                                <td onclick="editdepart('{{$value->kitchen_department_name}}','{{$value->id}}')">{{$value->kitchen_department_name}}</td>
                                <td onclick="editsubdepart('{{$value->id}}')">
                                    @foreach($details as $val)
                                        @if($value->id == $val->kitchen_depart_id)
                                            {{$val->department_name}},
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <a class="text-info" href="{{url('printers-kitchen-departments',$value->id)}}"><i class="icofont icofont-print"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Department</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Department Name:</label>
                                <input type="text"  name="department" id="department" class="form-control" />
                                <input type="hidden" name="departid"  id="departid"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="updatedepart()">Edit Department</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="subdepart-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Sub-Department</h4>
                </div>
                <div class="modal-body">
                    <form id="modelForm" class="form-horizontal">
                        @csrf
                        <input type="hidden" name="uhidd_id" id="uhidd_id" value="0" />

                        <select class="form-control select2" id="kdepartment" name="kdepartment[]" multiple="">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{$department->department_id}}">{{$department->department_name}}</option>
                            @endforeach
                        </select>

                    </form>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info f-right" onclick="update()">Update</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();
        var departments = [];
        $('#mainTable').DataTable( {

            bLengthChange: true,
            displayLength: 50,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Department',
                lengthMenu: '<span></span> _MENU_'
            }
        });

        $('#btn_save').click(function (e) {
            if($('#deptname').val() == ""){
                swal_alert('Alert!',"Enter Department Name",'error',false);
            }
            else if($('#depart').val() == ""){
                swal_alert('Alert!',"Select Department",'error',false);
            }
            else{
                $.ajax({
                    url:'{{ url("save-kitchen-department") }}',
                    type:"POST",
                    data:$('#deptform').serialize(),
                    dataType:"json",
                    success:function(r){

                        if(r.state == 1){
                            swal_alert('Alert!',r.msg,'error',false);
                        }else {
                            $("#deptname_alert").html('');
                            swal_alert('Successfully!',r.msg,'success',true);
                        }
                    }
                });
            }
        })

        function swal_alert(title,msg,type,mode){

            swal({
                title: title,
                text: msg,
                type: type
            },function(isConfirm){
                if(isConfirm){
                    if(mode==true){
                        window.location="{{ url('view-kitchen-departments') }}";
                    }
                }
            });
        }

        function editdepart(depart,departid){
            $("#depart-modal").modal("show");
            $('#department').val(depart);
            $('#departid').val(departid);
        }

        function updatedepart(){
            $.ajax({
                url: "{{url('/update-depart')}}",
                type:"PUT",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                    departid:$('#departid').val(),
                    departname: $('#department').val(),
                },
                success:function(resp){
                    if(resp != 0){
                        swal({
                                title: "Operation Performed",
                                text: "Department Updated Successfully!",
                                type: "success"},
                            function(isConfirm){
                                if(isConfirm){
                                    $("#depart-modal").modal("hide");
                                    window.location= "{{ url('/view-kitchen-departments') }}";
                                }
                            });


                    }
                }
            });
        }

        function editsubdepart(departid){

            $.ajax({
                url: "{{url('/getsubkitchendepart')}}",
                type:"POST",
                dataType:"json",
                data:{_token:"{{ csrf_token()}}",
                    departid:departid,
                },
                success:function(r){
                    $("#subdepart-modal").modal("show");
                    for(var s=0;s < r.length ;s++){
                        departments.push(r[s].inventory_department_id)

                    }
                    $("#kdepartment").val(departments).change();
                    $("#uhidd_id").val(r[0].kitchen_depart_id);


                }

            });

        }

        function update(){
                if($("kdepartment").val() == ""){
                    swal_alert('Alert!',"Please Select Department",'error',false);
                }else{
                    $.ajax({
                        url:'{{ url("update-kitchen-details-update") }}',
                        type:"POST",
                        data:$('#modelForm').serialize(),
                        success:function(r){
                            if(r==1){
                                swal_alert('Successfully!',"Updated Successfully",'success',true);
                            }
                            if(r==2){
                                swal_alert('Alert!',"Please Select Department",'error',false);
                            }
                        }
                    });
                }




        }

    </script>

@endsection