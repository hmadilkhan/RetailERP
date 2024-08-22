@extends('layouts.master-layout')

@section('title','Reports Dashboard')

@section('breadcrumtitle','Reports Dashboard')

@section('navreport','active')


@section('content')
    <section class="panels-wells">
        <div class="row">

            <div class="col-lg-3 grid-item">
                <div class="card" style="cursor: pointer;" id="loan" >
                    <div class="card-block txt-white bg-success text-center">
                        <h1 class="m-b-20 txt-white"><i class="icofont icofont-money-bag"></i></h1>
                        <h4 class="f-w-100">Loan Details</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 grid-item">
                <div class="card" style="cursor: pointer;" id="advance" >
                    <div class="card-block txt-white bg-danger text-center">
                        <h1 class="m-b-20 txt-white"><i class="icofont icofont-money-bag"></i></h1>
                        <h4 class="f-w-100">Advance Details</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 grid-item">
                <div class="card" style="cursor: pointer;" id="salarysheet" >
                    <div class="card-block txt-white bg-primary text-center">
                        <h1 class="m-b-20 txt-white"><i class="icofont icofont-money-bag"></i></h1>
                        <h4 class="f-w-100">Salary Sheet</h4>
                    </div>
                </div>
            </div>

        </div>
    </section>
{{--    modals--}}
    <div class="modal fade modal-flex" id="filter-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Filter
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </h4>

                </div>

                <div class="modal-body">
                    <input type="hidden" value="0" id="txtloan" />
                    <input type="hidden" value="0" id="txtsalarysheet" />
                    <input type="hidden" value="0" id="txtadvance" />
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-group">
                                <label class="form-control-label">Select Employee</label>
                                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" >
                                    <option value="">Select Employee</option>
                                    @if($getemp)
                                        @foreach($getemp as $value)
                                            <option value="{{ $value->empid }}">{{$value->emp_acc}} | {{$value->emp_name}} | {{$value->department_name}} | {{$value->branch_name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">From Date</label>
                                <input class="form-control" type="text"
                                       name="datefrom" id="datefrom" placeholder="DD-MM-YYYY" onchange="copydate()" />
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-control-label">To Date</label>
                                <input class="form-control" type="text"
                                       name="dateto" id="dateto" placeholder="DD-MM-YYYY"/>
                                <div class="form-control-feedback"></div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success waves-effect waves-light" onClick="getdata()">get Details</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
$('#loan').on('click', function (){
    $('#txtloan').val(1);
    $('#txtsalarysheet').val(0);
    $('#txtadvance').val(0);
$('#filter-modal').modal('show');
});

$('#advance').on('click', function (){
    $('#txtadvance').val(1);
    $('#txtsalarysheet').val(0);
    $('#txtloan').val(0);
    $('#filter-modal').modal('show');
});

$('#salarysheet').on('click', function (){
    $('#txtloan').val(0);
    $('#txtadvance').val(0);
    $('#txtsalarysheet').val(1);
    $('#filter-modal').modal('show');
});

function copydate(){
    let date = $('#datefrom').val();
    $('#dateto').val(date);
}

$(".select2").select2();

$('#tblsheet').DataTable({

    bLengthChange: true,
    displayLength: 10,
    info: false,
    language: {
        search:'',
        searchPlaceholder: 'Search Employee',
        lengthMenu: '<span></span> _MENU_'

    }

});


$('#datefrom, #dateto').bootstrapMaterialDatePicker({
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

function  getdata(){
    let emp = $('#employee').val();
    let date = $('#datefrom').val();
    let todate = $('#dateto').val();

    if ($('#txtloan').val() == 1){
        window.location = "{{url('pdfloandetails')}}?fromdate="+date+"&todate="+todate+"&empid="+emp;
    }
    else if ($('#txtsalarysheet').val() == 1){
        window.location = "{{url('pdfsalarysheet')}}?fromdate="+date+"&todate="+todate+"&empid="+emp;
    }
    else if ($('#txtadvance').val() == 1){
        window.location = "{{url('pdfadvancedetails')}}?fromdate="+date+"&todate="+todate+"&empid="+emp;
    }
    else{
        alert(0);
    }

}


    </script>
@endsection