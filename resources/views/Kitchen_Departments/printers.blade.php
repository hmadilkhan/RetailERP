@extends('layouts.master-layout')

@section('title','Kitchen Department Printers')

@section('breadcrumtitle','View Inventory')
@section('navkitchenDepartment','active')
@section('navinvent_depart','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Kitchen Printer Details</h5>
                <span id="message" class="text-danger f-right f-24">{{session('message')}}</span>
                <h5 class=""><a href="{{ url('view-kitchen-departments') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            </div>
            <div class="card-block">
                <form method="POST" action="{{url('store-printing-details')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="department_id" value="{{$department_id}}">
                        <input type="hidden" name="mode" id="mode" >
                        <input type="hidden" name="print_id" id="id" >





                        <div class="col-lg-4 col-md-4">

                            <div class="form-group {{ $errors->has('printerName') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Printer Name:</label>
                                <input type="text" name="printerName" id="printerName" class="form-control"/>
                                @if ($errors->has('printerName'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>

                        </div>
                        <div class="col-md-4 ">
                            <label class="form-control-label">Select Printer:</label>
                            <div class="form-check">
                                <label for="optionsRadios1" class="form-check-label">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="lan" value="lan" checked>
                                    WLAN/Wifi
                                </label>

                                <label for="optionsRadios1" class="form-check-label m-l-20">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="bluetooth" value="bluetooth" checked>
                                    Bluetooth
                                </label>
								
								<label for="optionsRadios1" class="form-check-label m-l-20">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="desktop" value="desktop" checked>
                                    Desktop
                                </label>
								
								<label for="optionsRadios1" class="form-check-label m-l-20">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="cloud" value="cloud" checked>
                                    Cloud Printer
                                </label>
                            </div>
                        </div>


                    </div>


                    <div class="row">
                        <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right  m-r-5 " onclick="submit()" >
                            <i class="icofont icofont-plus"> </i>
                            Add Printing Details
                        </button>

                        <button style="display: none;" type="button" id="btnUpdate" class="btn btn-md btn-info waves-effect waves-light f-right m-r-2 " onclick="submit()" >
                            <i class="icofont icofont-edit"> </i>
                            Update Printing Details
                        </button>


                        <button style="display: none;" type="button" id="btnCancel" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-2 " >
                            <i class="icofont icofont-edit"> </i>
                            Cancel
                        </button>



                    </div>
                </form>
            </div>
        </div>

    </section>

    <section>
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Printer Details</h5>

            </div>
            <div class="card-block">

                <table id="mainTable" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th>Printer Name</th>
                        <th>WLAN/Wifi</th>
                        <th>Bluetooth</th>
						<th>Desktop</th>
						<th>Cloud</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($printers as $value)
                        <tr>
                            <td >{{$value->printer_name}}</td>
                            <td >{{($value->LAN == 1 ? 'Enable' : 'Disable')}}</td>
                            <td >{{($value->bluetooth == 1 ? 'Enable' : 'Disable')}}</td>
							<td >{{($value->Desktop == 1 ? 'Enable' : 'Disable')}}</td>
							<td >{{($value->cloud == 1 ? 'Enable' : 'Disable')}}</td>
{{--                            --}}
                            <td class="action-icon">
                                <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onClick="editPrinter({{ $value->id }},'{{ $value->printer_name }}',{{ $value->LAN }},{{ $value->bluetooth }},{{ $value->Desktop }})"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                                {{--<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->terminal_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>--}}



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
        $('#mode').val("insert");
        $(".select2").select2();

        $('#mainTable').DataTable( {

            bLengthChange: true,
            displayLength: 50,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Department',
                lengthMenu: '<span></span> _MENU_'

            }


        } );

        function editPrinter(id,printer,LAN,bluetooth,desktop){

            $('#mode').val("update");
            $('#message').html('');
            $('#id').val(id);
            $('#btnsubmit').css('display','none');
            $('#btnUpdate').css('display','block');
            $('#btnCancel').css('display','block');
            $('#printerName').val(printer);
            if(LAN == 1){
                $('input:radio[id=lan]').prop('checked', true);
            }
            if(bluetooth == 1){
                $('input:radio[id=bluetooth]').prop('checked', true);
            }
			if(desktop == 1){
                $('input:radio[id=desktop]').prop('checked', true);
            }
			
        }

        $('#btnCancel').click(function (e) {
            $('#mode').val("insert");
            $('#printerName').val("");
            $('#btnsubmit').css('display','block');
            $('#btnUpdate').css('display','none');
            $('#btnCancel').css('display','none');
        });
    </script>
@endsection