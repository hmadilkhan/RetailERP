@extends('layouts.master-layout')

@section('title','Cheque Module')

@section('breadcrumtitle','Cheque Module')

@section('navaccounts','active')

@section('navcheque','active')


@section('content')

    <section class="panels-wells">


        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Cheque Details</h5>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group {{ $errors->has('fromdate') ? 'has-danger' : '' }}">
                            <label class="form-control-label">From Date</label>
                            <input class="form-control date" type="text"
                                   name="fromdate" id="fromdate" placeholder="DD-MM-YYYY" value="{{ old('fromdate') }}"/>
                            @if ($errors->has('fromdate'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group {{ $errors->has('todate') ? 'has-danger' : '' }}">
                            <label class="form-control-label">To Date</label>
                            <input class="form-control date" type="text"
                                   name="todate" id="todate" placeholder="DD-MM-YYYY" value="{{ old('todate') }}"/>
                            @if ($errors->has('todate'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Select Cheque Status:</label>
{{--                            <i id="btn_status" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Status" ></i>--}}
                            <select id="chequestatus" name="chequestatus" class="form-control select2">
                                <option value="">Select Status</option>
                                @foreach($status as $value)
                                    <option value="{{$value->id}}">{{$value->status}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Cheque Type</label>
                            <select id="chtype" name="chtype" class="form-control select2">
                                <option value="">Select Cheque Type</option>
                                <option value="cash">Cash</option>
                                <option value="Account Title">Account Title</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Select Customer</label>
                            <select id="customer" name="customer" class="form-control select2">
                                <option value="">Select Customer</option>
                                @if($customer)
                                    @foreach($customer as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 f-right">
                        <div class="form-group row ">
                            <button class="btn btn-circle btn-danger f-right m-r-20" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error"
                                ></i> Clear</button>
                            <button class="btn btn-circle btn-primary f-right m-r-20"  type="button" id="btn_filter" data-toggle="tooltip" data-placement="top" title="" data-original-title="Filter Result"><i class="icofont icofont-plus"
                                ></i>&nbsp; Filter Result</button>.

                        </div>
                    </div>
                </div>
                <hr>
                <table id="chequetable" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                    <thead>
                    <tr>
                        <th>Cheque Date</th>
                        <th>Cheque Number</th>
                        <th>Cheque Type</th>
                        <th>Bank</th>
                        <th>Amount</th>
                        <th>Customer</th>
                        <th>Status</th>
                        {{--                        <th>Narration</th>--}}
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($details as $value)
                        <tr>

                            <td>{{$value->cheque_date}}</td>
                            <td>{{$value->cheque_number}}</td>
                            <td>{{$value->payment_mode}}</td>
                            <td>{{$value->bank_name}}</td>
                            <td>{{$value->amount}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->status}}</td>
                            {{--                            <td>{{$value->naraation}}</td>--}}

                            <td class="action-icon">
                                <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Other Operations" onclick="showmodal('{{$value->cheque_id}}','{{$value->cheque_number}}','{{$value->amount}}','{{$value->cheque_date}}')"><i class="icofont icofont-tasks-alt text-primary f-18" ></i> </a>
                                <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Details" onclick="view('{{$value->cheque_id}}')"><i class="icofont icofont-eye-alt text-success f-18" ></i> </a>
                            </td>
                        </tr>

                    @endforeach


                    </tbody>


                </table>
            </div>
        </div>

    </section>
    <!-- modals -->
    <div class="modal fade modal-flex" id="clearance-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Clearance Section</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden"  id="chequeid" name="chequeid" value="">
                    <input type="hidden"  id="chequeamount" name="chequeamount" value="">
                    <input type="hidden"  id="chequedatemodal" name="chequedatemodal" value="">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <label class="text-primary f-28" id="chequenumber"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Date:</label>
                                <input class="form-control date" type="text" name="seconddate" id="seconddate" placeholder="DD-MM-YYYY" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Select Cheque Status:</label>
                                <i id="btn_status" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Status" ></i>
                                <select id="chequestatusmodal" name="chequestatusmodal" class="form-control select2" onchange="showaccounts()">
                                    <option value="">Select Status</option>
                                    @foreach($status as $value)
                                        <option value="{{$value->id}}">{{$value->status}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="divaccounts" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                            <label class="form-control-label">Select Bank Account:</label>
                            <select id="account" name="account" class="form-control select2">
                                <option value="0">Select Bank Account</option>
                                @if($accounts)
                                @foreach($accounts as $value)
                                    <option value="{{$value->bank_account_id}}">{{$value->account_title}} | {{$value->account_no}} | {{$value->bank_name}} | {{$value->branch_name}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Narration:</label>
                                <textarea id="secondnarration" name="secondnarration" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_bank" class="btn btn-success waves-effect waves-light" onClick="save()">Save Details</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="status-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Create Status</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Status Name:</label>
                                <input type="text" name="statusname" id="statusname" placeholder="Enter Status Name" class="form-control">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_bank" class="btn btn-primary waves-effect waves-light" onClick="addstatus()">Create Status</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="view-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Cheque History</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <table id="tblcheque" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Cheque Number</th>
                                        <th>Status</th>
                                        <th>Narration</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>


                                </table>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();

        $('.table').DataTable({
            displayLength: 10,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Details',
                lengthMenu: '<span></span> _MENU_'

            },


        });


        $('.date').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false,
            clearButton: true,

            icons: {
                date: "icofont icofont-ui-calendar",
                up: "icofont icofont-rounded-up",
                down: "icofont icofont-rounded-down",
                next: "icofont icofont-rounded-right",
                previous: "icofont icofont-rounded-left"
            }
        });

        function addstatus(){
            if ($('#statusname').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Please Enter Status First!",
                    type: "error"

                });
            }
            else{
                $.ajax({
                    url: "{{url('/insert-chequeStatus')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",
                        dataType:"json",
                        statusname:$('#statusname').val(),
                    },
                    success:function(resp){
                        if (resp == 1) {
                            swal({
                                title: "Success",
                                text: "Status Created Successfully!",
                                type: "success"
                            });
                            $("#status-modal").modal("hide");
                        }
                        else{
                            swal({
                                title: "Error Message",
                                text: "Already Exsist!",
                                type: "error"
                            });
                        }
                    }

                });
            }

        }



        function divhide() {
            $('#details').toggle();
        }

        function showmodal(chequeid, chequenumber,amount,chequedate) {
            $('#chequeid').val(chequeid);
            $('#chequenumber').html(chequenumber);
            $('#chequeamount').val(amount);
            $('#chequedatemodal').val(chequedate);
            $('#clearance-modal').modal('show');
            $('#seconddate').val('');
            $('#secondnarration').val('');

        }

        $("#btn_status").on('click',function(){
            $('#statusname').val('');
            $('#clearance-modal').modal('hide');
            $("#status-modal").modal("show");

        });

        function save() {
            if ($('#chequestatusmodal').val() == "") {
                swal({
                    title: "Error Message",
                    text: "Please Select Status First!",
                    type: "error"
                });
            }
            else{
                $.ajax({
                    url: "{{url('/save-chequeClearance')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",
                        dataType:"json",
                        chequeid:$('#chequeid').val(),
                        narration:$('#secondnarration').val(),
                        status:$('#chequestatusmodal').val(),
                        date:$('#seconddate').val(),
                        accountid:$('#account').val(),
                        amount:$('#chequeamount').val(),
                        chequenumber:$('#chequenumber').html(),
                        chequedate:$('#chequedatemodal').val(),
                    },
                    success:function(resp){
                        if (resp == 1) {
                            swal({
                                title: "Success",
                                text: "Status Changed Successfully!",
                                type: "success"
                            });
                            $("#clearance-modal").modal("hide");
                        }
                    }

                });
            }

        }

        function view(chequeid) {
            $.ajax({
                url: "{{url('/getdetails-cheque')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",
                    dataType:"json",
                    chequeid:chequeid,
                },
                success:function(result){
                    if(result){
                        $("#tblcheque tbody").empty();
                        for(var count =0;count < result.length; count++){

                            $("#tblcheque tbody").append(
                                "<tr>" +
                                "<td>"+result[count].date+"</td>" +
                                "<td>"+result[count].cheque_number+"</td>" +
                                "<td>"+result[count].status+"</td>" +
                                "<td>"+result[count].naraation+"</td>" +
                                "</tr>"
                            );
                        }

                    }
                }

            });
            $('#view-modal').modal('show');
        }


        $('#btn_clear').on('click', function () {
            $('#fromdate').val('');
            $('#todate').val('');
        });


        $('#btn_filter').on('click', function () {
            // if($('#fromdate').val() == ""){
            //     swal({
            //         title: "Error Message",
            //         text: "Please Select Date!",
            //         type: "error"
            //     });
            // }
            // else if($('#todate').val() == ""){
            //     swal({
            //         title: "Error Message",
            //         text: "Please Select Date!",
            //         type: "error"
            //     });
            // }
            // else{
                filter();
            // }
        });

        function filter() {
            $.ajax({
                url: "{{url('/filterCheques')}}",
                type: 'GET',
                data:{_token:"{{ csrf_token() }}",
                    dataType:"json",
                    fromdate:$('#fromdate').val(),
                    todate:$('#todate').val(),
                    chequestatus:$('#chequestatus').val(),
                    paymentmode:$('#chtype').val(),
                    customer:$('#customer').val(),
                },
                success:function(result){
                    if(result){
                        $("#chequetable tbody").empty();
                        for(var count =0;count < result.length; count++){
                            $("#chequetable tbody").append(
                                "<tr>" +
                                "<td>"+result[count].cheque_date+"</td>" +
                                "<td>"+result[count].cheque_number+"</td>" +
                                "<td>"+result[count].payment_mode+"</td>" +
                                "<td>"+result[count].bank_name+"</td>" +
                                "<td>"+result[count].amount+"</td>" +
                                "<td>"+result[count].name+"</td>" +
                                "<td>"+result[count].status+"</td>" +
                                "<td class=\'action-icon\'><a class=\"m-r-10\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"Other Operations\" onclick=\"showmodal("+result[count].cheque_id+","+result[count].cheque_number+","+result[count].amount+","+result[count].cheque_date+")\"><i class=\"icofont icofont-tasks-alt text-primary f-18\" ></i> </a>\n" +
                                "        <a class=\"m-r-10\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title=\"View Details\" onclick=\"view("+result[count].cheque_id+")\"><i class=\"icofont icofont-eye-alt text-success f-18\" ></i> </a></td>" +
                                "</tr>"
                            );
                        }
                    }
                }
            });
        }

        function showaccounts(){
            if($('#chequestatusmodal').val() == 2)
            {
             $('#divaccounts').css('display','block');
            }
            else{
                $('#divaccounts').css('display','none');
            }

        }
    </script>

@endsection



