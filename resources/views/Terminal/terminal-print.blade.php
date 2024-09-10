@extends('layouts.master-layout')

@section('title','Terminals')

@section('breadcrumtitle','Terminals').

@section('navterminals','active')

@section('content')
    <style>
        .table {
            table-layout:fixed;
        }

        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

        }
    </style>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Terminal Print Details</h5>
                <span id="message" class="text-danger f-right f-24">{{session('message')}}</span>
                <h5 class=""><a href="{{ url('branches') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            </div>
            <div class="card-block">
            <form method="POST" action="{{url('store-printer-details')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <input type="hidden" name="terminal_id" value="{{$terminal_id}}">
                    <input type="hidden" name="mode" id="mode" >
                    <input type="hidden" name="print_id" id="id" >
                    <input type="hidden" name="previous_image" id="prevoius_image">
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group {{ $errors->has('header') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Header Text:</label>
                            <input type="text" name="header" id="header"  class="form-control"/>
                            @if ($errors->has('header'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="form-group {{ $errors->has('footer') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Footer Text:</label>
                            <input type="text" name="footer" id="footer" class="form-control"/>
                            @if ($errors->has('footer'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="f-right">
                            <div class="form-check">
                                <label for="optionsRadios1" class="form-check-label">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="lan" value="lan" checked>
                                    WLAN/Wifi
                                </label>

                                <label for="optionsRadios1" class="form-check-label m-l-10">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="bluetooth" value="bluetooth" checked>
                                    Bluetooth
                                </label>
								
								<label for="optionsRadios1" class="form-check-label m-l-10">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="pts" value="pts" checked>
                                    PTS
                                </label>

                                <label for="optionsRadios1" class="form-check-label m-l-10">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="desktop" value="desktop" checked>
                                    Desktop
                                </label>
								
								 <label for="optionsRadios1" class="form-check-label m-l-10">
                                    <input type="radio" class="form-check-input" name="optionsRadios" id="cloud" value="cloud" checked>
                                    Cloud
                                </label>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('printerName') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Printer Name:</label>
                            <input type="text" name="printerName" id="printerName" class="form-control"/>
                            @if ($errors->has('printerName'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>
                    </div>



                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="image" class="form-control-label">Image</label>
                        <a href="#">
                            <img id="simg" src="{{ asset('storage/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
                        </a>

                        <div class="form-group {{ $errors->has('image') ? 'has-danger' : '' }} m-t-10">


                            <label for="image" class="custom-file">
                                <input type="file" name="image" id="image" class="custom-file-input">
                                <span class="custom-file-control"></span>
                            </label>
                            @if ($errors->has('image'))
                                <div class="form-control-feedback">{{ $errors->first('image') }}</div>
                            @endif
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


                            <button style="display: none;" type="button" id="btnCancel" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-2 " onclick="submit()" >
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
            <h5 class="card-header-text">Print Details</h5>

        </div>
        <div class="card-block">

            <table id="tblterminals" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                <thead>
                <tr>
                    <th class="text-center">Image</th>
                    <th>Terminal Name</th>
                    <th>Header</th>
                    <th>Footer</th>
                    <th>Printer Name</th>
                    <th>WLAN/Wifi</th>
                    <th>Bluetooth</th>
					<th>PTS</th>
                    <th>Desktop</th>
                    <th>Cloud</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($terminals as $value)
                    <tr>
                        <td class="text-center">
                            <a href="{{ asset('assets/images/receipt/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" data-toggle="lightbox" data-title="Article : {{$value->id.' '.$value->terminal_name}}" data-footer="{{$value->header}}">
                                <img width="32" height="32" data-modal="modal-12" src="{{ asset('assets/images/receipt/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                            </a>
                        </td>
                        <td >{{$value->terminal_name}}</td>
                        <td onclick="show('{{$value->header}}')" >{{$value->header}}</td>
                        <td onclick="show('{{$value->footer}}')">{{$value->footer}}</td>
                        <td >{{$value->printer_name}}</td>
                        <td >{{($value->LAN == 1 ? 'Enable' : 'Disable')}}</td>
                        <td >{{($value->bluetooth == 1 ? 'Enable' : 'Disable')}}</td>
						<td >{{($value->pts == 1 ? 'Enable' : 'Disable')}}</td>
                        <td >{{($value->desktop == 1 ? 'Enable' : 'Disable')}}</td>
                        <td >{{($value->cloud == 1 ? 'Enable' : 'Disable')}}</td>

                        <td class="action-icon">
                            <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit('{{ $value->id }}','{{ $value->header }}','{{ $value->footer }}','{{ $value->printer_name }}','{{ $value->LAN }}','{{ $value->bluetooth }}','{{ $value->pts }}','{{ $value->desktop }}','{{$value->image}}')"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

{{--                            <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->terminal_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>--}}



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
        $(document).ready(function () {
            $('#mode').val("insert");
        })

        $('#tblterminals').DataTable({
            bLengthChange: true,
            displayLength: 10,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Search Details',
                lengthMenu: '<span></span> _MENU_'
            }
        });


        function readURL(input,id) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#'+id).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#image").change(function() {
            readURL(this,'simg');
        });

        function edit(id,header,footer,printer,LAN,bluetooth,pts,desktop,image){

            $('#mode').val("update");
            $('#message').html('');
            $('#id').val(id);
            $('#btnsubmit').css('display','none');
            $('#btnUpdate').css('display','block');
            $('#btnCancel').css('display','block');
            $('#header').val(header);
            $('#footer').val(footer);
            $('#printerName').val(printer);
            $('#prevoius_image').val(image);
            if(LAN == 1){
                $('input:radio[id=lan]').prop('checked', true);
            }
            if(bluetooth == 1){
                $('input:radio[id=bluetooth]').prop('checked', true);
            }
			if(pts == 1){
                $('input:radio[id=pts]').prop('checked', true);
            }
            if(desktop == 1){
                $('input:radio[id=desktop]').prop('checked', true);
            }
            $('#simg').attr('src','{{asset('assets/images/receipt/')}}'+"/"+image);
        }

        $('#btnCancel').click(function (e) {
            $('#mode').val("insert");
            $('#btnsubmit').css('display','block');
            $('#btnUpdate').css('display','none');
            $('#btnCancel').css('display','none');
        });

        function show(text) {
            alert(text);
        }
    </script>
@endsection