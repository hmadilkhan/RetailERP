@extends('layouts.master-layout')

@section('title','Terminal Print Settings')

@section('breadcrumtitle','Terminal Print Settings')

@section('navterminals','active')

@section('content')
<style>
    :root {
        --brand: #4CAF50;
        --brand-dark: #2E7D32;
        --brand-light: #A5D6A7;
        --bg-light: #FAFBF7;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(76, 175, 80, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .modern-card:hover {
        box-shadow: 0 8px 30px rgba(76, 175, 80, 0.15);
    }
    
    .card-header-modern {
        background: #EEEEEE;
        padding: 20px 30px;
        border: none;
        border-bottom: 2px solid #c8e6c9;
    }
    
    .card-header-modern h5 {
        color: #534e4e;
        font-weight: 600;
        margin: 0;
        font-size: 18px;
    }
    
    .form-group-modern label {
        font-size: 13px;
        font-weight: 600;
        color: #2E7D32;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-control-modern {
        border: 2px solid #e8f5e9;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control-modern:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        outline: none;
    }
    
    .radio-group-modern {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        padding: 15px;
        background: #f1f8f4;
        border-radius: 12px;
        margin-top: 10px;
    }
    
    .radio-item-modern {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 8px 16px;
        background: white;
        border-radius: 8px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .radio-item-modern:hover {
        border-color: var(--brand-light);
        background: #e8f5e9;
    }
    
    .radio-item-modern input[type="radio"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--brand);
    }
    
    .radio-item-modern label {
        cursor: pointer;
        margin: 0;
        user-select: none;
    }
    
    .radio-item-modern input[type="radio"]:checked ~ label {
        color: var(--brand-dark);
        font-weight: 600;
    }
    
    .btn-modern {
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-success-modern {
        background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }
    
    .btn-success-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }
    
    .btn-info-modern {
        background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
    }
    
    .btn-danger-modern {
        background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        color: white;
    }
    
    .image-upload-modern {
        position: relative;
        width: 150px;
        height: 150px;
        border: 3px dashed #4CAF50;
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f1f8f4;
    }
    
    .image-upload-modern:hover {
        border-color: var(--brand-dark);
        background: #e8f5e9;
    }
    
    .image-upload-modern img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .table-modern thead th {
        background: #e8f5e9;
        color: #2E7D32;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 15px;
        border: none;
        border-bottom: 2px solid #c8e6c9;
    }
    
    .table-modern thead th:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .table-modern thead th:last-child {
        border-radius: 0 10px 10px 0;
    }
    
    .table-modern tbody tr {
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .table-modern tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.15);
    }
    
    .table-modern tbody td {
        padding: 15px;
        vertical-align: middle;
        border: none;
    }
    
    .table-modern tbody tr td:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .table-modern tbody tr td:last-child {
        border-radius: 0 10px 10px 0;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-success-modern {
        background: #e8f5e9;
        color: #2E7D32;
    }
    
    .badge-danger-modern {
        background: #ffebee;
        color: #c62828;
    }
    
    .alert-modern {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .alert-success-modern {
        background: #e8f5e9;
        color: #2E7D32;
        border-left: 4px solid #4CAF50;
    }
    
    .back-link-modern {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #2E7D32;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 8px 16px;
        background: white;
        border-radius: 8px;
        border: 2px solid #c8e6c9;
    }
    
    .back-link-modern:hover {
        color: white;
        background: var(--brand);
        border-color: var(--brand);
        transform: translateX(5px);
    }
    
    .action-icon-modern {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #e8f5e9;
        color: var(--brand-dark);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .action-icon-modern:hover {
        background: var(--brand);
        color: white;
        transform: scale(1.1);
    }
    
    /* DataTable Search Right Align */
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
    }
    
    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e8f5e9;
        border-radius: 10px;
        padding: 8px 16px;
        margin-left: 10px;
        transition: all 0.3s ease;
    }
    
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        outline: none;
    }
    
    .dataTables_wrapper .dataTables_length {
        float: left;
    }
    
    .dataTables_wrapper .dataTables_length select {
        border: 2px solid #e8f5e9;
        border-radius: 8px;
        padding: 6px 12px;
        margin: 0 10px;
    }
</style>

<section class="panels-wells">
    <div class="modern-card">
        <div class="card-header-modern">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h5>Terminal Print Settings</h5>
                <a href="{{ url('branches') }}" class="back-link-modern">
                    <i class="icofont icofont-arrow-left"></i>
                    Back to Settings
                </a>
            </div>
        </div>
        
        @if(session('message'))
        <div class="alert-modern alert-success-modern m-3">
            <i class="icofont icofont-check-circled"></i> {{session('message')}}
        </div>
        @endif
        
        <div class="card-block p-4">
            <form method="POST" action="{{url('store-printer-details')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="terminal_id" value="{{$terminal_id}}">
                <input type="hidden" name="mode" id="mode">
                <input type="hidden" name="print_id" id="id">
                <input type="hidden" name="previous_image" id="prevoius_image">
                
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group-modern">
                            <label>Header Text</label>
                            <input type="text" name="header" id="header" class="form-control form-control-modern" placeholder="Enter header text"/>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group-modern">
                            <label>Footer Text</label>
                            <input type="text" name="footer" id="footer" class="form-control form-control-modern" placeholder="Enter footer text"/>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group-modern">
                            <label>Printer Name</label>
                            <input type="text" name="printerName" id="printerName" class="form-control form-control-modern" placeholder="Enter printer name"/>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <label class="form-group-modern">Connection Type</label>
                        <div class="radio-group-modern">
                            <div class="radio-item-modern">
                                <input type="radio" name="optionsRadios" id="lan" value="lan" checked>
                                <label for="lan">Mobile/IP</label>
                            </div>
                            <div class="radio-item-modern">
                                <input type="radio" name="optionsRadios" id="lan_wifi" value="lan_wifi" checked>
                                <label for="lan_wifi">Desktop/IP</label>
                            </div>
                            <div class="radio-item-modern">
                                <input type="radio" name="optionsRadios" id="bluetooth" value="bluetooth">
                                <label for="bluetooth">Bluetooth</label>
                            </div>
                            <div class="radio-item-modern">
                                <input type="radio" name="optionsRadios" id="pts" value="pts">
                                <label for="pts">PTS</label>
                            </div>
                            <div class="radio-item-modern">
                                <input type="radio" name="optionsRadios" id="desktop" value="desktop">
                                <label for="desktop">Desktop</label>
                            </div>
                            <div class="radio-item-modern">
                                <input type="radio" name="optionsRadios" id="cloud" value="cloud">
                                <label for="cloud">Cloud</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <label class="form-group-modern">Receipt Logo</label>
                        <div class="image-upload-modern" onclick="document.getElementById('image').click()">
                            <img id="simg" src="{{ asset('storage/images/placeholder.jpg') }}" alt="Upload Image">
                        </div>
                        <input type="file" name="image" id="image" style="display: none;">
                        <small class="text-muted mt-2 d-block">Click to upload image (Max: 2MB)</small>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="submit" id="btnsubmit" class="btn btn-modern btn-success-modern">
                                <i class="icofont icofont-plus"></i>
                                Add Print Settings
                            </button>
                            
                            <button type="submit" id="btnUpdate" class="btn btn-modern btn-info-modern" style="display: none;">
                                <i class="icofont icofont-edit"></i>
                                Update Settings
                            </button>
                            
                            <button type="button" id="btnCancel" class="btn btn-modern btn-danger-modern" style="display: none;">
                                <i class="icofont icofont-close"></i>
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="mt-4">
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>Configured Print Settings</h5>
        </div>
        <div class="card-block p-4">
            <div class="table-responsive">
                <table id="tblterminals" class="table table-modern" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">Logo</th>
                            <th>Terminal</th>
                            <th>Header</th>
                            <th>Footer</th>
                            <th>Printer</th>
                            <th>Mobile/IP</th>
                            <th>Desktop/IP</th>
                            <th>Bluetooth</th>
                            <th>PTS</th>
                            <th>Desktop</th>
                            <th>Cloud</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($terminals as $value)
                        <tr>
                            <td class="text-center">
                                <a href="{{ asset('storage/images/receipt/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" data-toggle="lightbox">
                                    <img width="40" height="40" src="{{ asset('storage/images/receipt/'.(!empty($value->image) ? $value->image : 'placeholder.jpg')) }}" style="border-radius: 8px; object-fit: cover;" alt="Logo">
                                </a>
                            </td>
                            <td><strong>{{$value->terminal_name}}</strong></td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{$value->header}}">
                                    {{$value->header}}
                                </span>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{$value->footer}}">
                                    {{$value->footer}}
                                </span>
                            </td>
                            <td>{{$value->printer_name}}</td>
                            <td>
                                <span class="badge-modern {{$value->LAN == 1 ? 'badge-success-modern' : 'badge-danger-modern'}}">
                                    {{$value->LAN == 1 ? 'Enabled' : 'Disabled'}}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{$value->LAN_Wifi == 1 ? 'badge-success-modern' : 'badge-danger-modern'}}">
                                    {{$value->LAN_Wifi == 1 ? 'Enabled' : 'Disabled'}}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{$value->bluetooth == 1 ? 'badge-success-modern' : 'badge-danger-modern'}}">
                                    {{$value->bluetooth == 1 ? 'Enabled' : 'Disabled'}}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{$value->pts == 1 ? 'badge-success-modern' : 'badge-danger-modern'}}">
                                    {{$value->pts == 1 ? 'Enabled' : 'Disabled'}}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{$value->desktop == 1 ? 'badge-success-modern' : 'badge-danger-modern'}}">
                                    {{$value->desktop == 1 ? 'Enabled' : 'Disabled'}}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{$value->cloud == 1 ? 'badge-success-modern' : 'badge-danger-modern'}}">
                                    {{$value->cloud == 1 ? 'Enabled' : 'Disabled'}}
                                </span>
                            </td>
                            <td class="text-center">
                                <a class="action-icon-modern" data-toggle="tooltip" title="Edit Settings" 
                                   onclick="edit('{{ $value->id }}','{{ $value->header }}','{{ $value->footer }}','{{ $value->printer_name }}','{{ $value->LAN }}','{{ $value->bluetooth }}','{{ $value->pts }}','{{ $value->desktop }}','{{ $value->cloud }}','{{$value->image}}','{{$value->LAN_Wifi}}')">
                                    <i class="icofont icofont-ui-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
    $(document).ready(function () {
        $('#mode').val("insert");
        
        $('#tblterminals').DataTable({
            bLengthChange: true,
            displayLength: 10,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Search settings...',
                lengthMenu: '<span></span> _MENU_'
            }
        });
    });

    function readURL(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#'+id).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#image").change(function() {
        readURL(this, 'simg');
    });

    function edit(id, header, footer, printer, LAN, bluetooth, pts, desktop, cloud, image, LAN_Wifi) {
        $('#mode').val("update");
        $('#id').val(id);
        $('#btnsubmit').hide();
        $('#btnUpdate').show();
        $('#btnCancel').show();
        $('#header').val(header);
        $('#footer').val(footer);
        $('#printerName').val(printer);
        $('#prevoius_image').val(image);
        
        // Reset all radio buttons
        $('input[name="optionsRadios"]').prop('checked', false);
        
        if(LAN == 1) $('#lan').prop('checked', true);
        if(LAN_Wifi == 1) $('#lan_wifi').prop('checked', true);
        if(bluetooth == 1) $('#bluetooth').prop('checked', true);
        if(pts == 1) $('#pts').prop('checked', true);
        if(desktop == 1) $('#desktop').prop('checked', true);
        if(cloud == 1) $('#cloud').prop('checked', true);
        if(LAN_Wifi == 1) $('#lan_wifi').prop('checked', true);
        
        if(image) {
            $('#simg').attr('src', '{{asset("storage/images/receipt")}}' + "/" + image);
        }
        
        // Scroll to top
        $('html, body').animate({scrollTop: 0}, 500);
    }

    $('#btnCancel').click(function (e) {
        e.preventDefault();
        $('#mode').val("insert");
        $('#btnsubmit').show();
        $('#btnUpdate').hide();
        $('#btnCancel').hide();
        
        // Reset form
        $('#header').val('');
        $('#footer').val('');
        $('#printerName').val('');
        $('#lan').prop('checked', true);
        $('#simg').attr('src', '{{ asset("storage/images/placeholder.jpg") }}');
    });
</script>
@endsection
