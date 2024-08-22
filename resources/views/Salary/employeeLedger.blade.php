@extends('layouts.master-layout')

@section('title','Payroll')

@section('breadcrumtitle','Individual Wise Salary')

@section('navpayroll','active')

@section('navempwise','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Employee Ledger</h5>
                <h6 class=""><a href="{{ url('/salary-details') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
            </div>
            <div class="card-block">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <table id="tblledger" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                                <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Code | Employee Name</th>
                                    <th>Designation</th>
                                    <th>Contact</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($ledger as $value)
                                    <tr>
                                        <td class="text-center">
                                            <img width="42" height="42" src="{{ asset('public/assets/images/employees/images/'.(!empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg' }}"/>
                                        </td>
                                        <td >{{$value->emp_acc}} | {{$value->emp_name}}</td>
                                        <td >{{$value->designation_name}}</td>
                                        <td >{{$value->emp_contact}}</td>
                                        <td class="f-18 f-bold text-info">{{number_format($value->balance,2,'.','')}}</td>
                                        <td class="action-icon">
                                            <a  class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cash Withdraw" onclick="debit('{{$value->ledger_id}}','{{$value->empid}}')"><i class="icofont icofont-money-bag text-danger f-18" ></i> </a>
                                            <a class="p-r-10 text-primary f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ledger Details" onclick="details('{{$value->empid}}')"><i class="icofont icofont-list"></i></a>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>


                            </table>

                        </div>
                    </div>


                </div>





            </div>
        </div>
    </section>

    <!-- modals -->
    <div class="modal fade modal-flex" id="debit-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Cash Withdraw</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="ledgerid" value="" />
                    <input type="hidden" id="empid" value="" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Enter Amount:</label>
                                <input type="Number" min="1"  name="debit" id="debit" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label">Narration:</label>
                                <textarea type="text"  name="narration" id="narration" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-success waves-effect waves-light" onClick="cashwithdraw()">Cash With Draw</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Ledger Details</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="employeeid" value="">
                    <div class="row">
                        <div class="col-md-12">

                            <table id="tbldetails" class="dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                    <th>Date</th>
                                    <th>Narration</th>
                                </tr>
                                </thead>
                                <tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-success waves-effect waves-light" onclick="getpdf()"><i class="icofont icofont-file-pdf" ></i>&nbsp;Generate Pdf</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scriptcode_three')
    <script type="text/javascript">
    $(".select2").select2();

    $('.table').DataTable({

    bLengthChange: true,
    displayLength: 10,
    info: false,
    language: {
    search:'',
    searchPlaceholder: 'Search Employee',
    lengthMenu: '<span></span> _MENU_'

    }

    });

    function debit(ledgerid,empid) {
        $('#debit').val('');
        $('#narration').val('');
        $('#ledgerid').val(ledgerid);
        $('#empid').val(empid);
        $('#debit-modal').modal('show');
    }
    
    
    function cashwithdraw() {
        if($('#ledgerid').val() == ""){
            swal({
                title: "Error Message",
                text: "You can not withdraw on Zero Balance!",
                type: "error"
            });
        }
        else if($('#debit').val() == 0 || $('#debit').val() < 0){
            swal({
                title: "Error Message",
                text: "Please Enter Valid Amount",
                type: "error"
            });
            $('#debit').val('');
        }
        else{
            $.ajax({
                url:'{{ url("/insert-emp-ledger") }}',
                type:"POST",
                data:{_token : "{{csrf_token()}}",
                    empid:$('#empid').val(),
                    amount:$('#debit').val(),
                    mode:1,
                    narration:$('#narration').val(),
                },
                success:function(resp){
                    console.log(resp);
                    if (resp == 1) {
                        swal({
                            title: "Success",
                            text: "Amount Withdraw Successfully!",
                            type: "success"
                        },function(isConfirm){
                            if(isConfirm){
                                window.location = "{{url('/show-emp-ledger')}}";
                            }
                        });
                    }
                    else if(resp == 2){
                        swal({
                            title: "Error Message",
                            text: "You Enter Incorrect Amount!",
                            type: "error"
                        });
                        $('#debit').val('');
                    }
                    else if(resp == 3){
                        swal({
                            title: "Error Message",
                            text: "Cash Ledger does not have sufficient amount for this transaction!",
                            type: "error"
                        });
                        $('#debit').val('');
                    }
                    else{
                        swal({
                            title: "Error Message",
                            text: "You can not withdraw on Zero Balance!",
                            type: "error"
                        });
                    }
                }
            });
        }

    }

    function details(empid) {
        $.ajax({
            url: "{{url('/get-emp-ledgerdetails')}}",
            type: 'GET',
            data:{_token:"{{ csrf_token() }}",
                empid:empid,
            },
            success:function(result){
                $('#employeeid').val(empid);
                $('#details-modal').modal('show');
                $("#tbldetails tbody").empty();
                for(var count =0;count < result.length; count++){
                    $("#tbldetails tbody").append(
                        "<tr>" +
                        "<td class='pro-name' >"+result[count].emp_name+"</td>" +
                        "<td>"+result[count].debit+"</td>" +
                        "<td>"+result[count].credit+"</td>" +
                        "<td>"+result[count].balance+"</td>" +
                        "<td>"+result[count].Date+"</td>" +
                        "<td>"+result[count].narration+"</td>" +
                        "</tr>"
                    );
                }
            }

        });
    }

    function getpdf(){
        window.location = "{{url('getledgerpdf')}}?empid="+$('#employeeid').val();
    }

    </script>

@endsection
