@extends('layouts.master-layout')

@section('title','Terminal Assign Lists')

@section('breadcrumtitle','Terminal Assign Lists')

@section('navwebsite','active')

@section('content')

<section class="panels-wells p-t-3">

    @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif
    <div class="card mt-0">
        <div class="card-header">
            <h5 class="card-header-text">Assign Terminal</h5>
        </div>
        <div class="card-block ">
            <form method="post" action="{{route('terminalAssignStore')}}">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Website</label>
                            <select name="website" id="website" data-placeholder="Select" class="form-control select2">
                                <option value="">Select</option>
                                @if($websites)
                                @php $oldWebsite = old('website') ? old('website') : (!empty($terminalAssign) ? $terminalAssign->website_id : '') ; @endphp
                                @foreach($websites as $val)
                                  <option {{ $oldWebsite == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <div class="form-control-feedback text-danger" id="website_alert"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Branch</label>
                            <select name="branch" id="branch" data-placeholder="Select" class="form-control select2">
                                <option value="">Select</option>
                            </select>
                            @error('branch')
                            <div class="form-control-feedback text-danger" id="branch_alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label">Terminal</label>
                            <select name="terminal" id="terminal" data-placeholder="Select" class="form-control select2" disabled>
                                <option value="">Select</option>
                            </select>
                            @error('terminal')
                            <div class="form-control-feedback text-danger" id="terminal_alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                           <label>Is Open Branch</label>
                           <br/>
                         <label>
                            <input id="is_open" name="is_open" type="checkbox" data-toggle="toggle" data-size="mini" {{ old('is_open') ? 'checked' : '' }}>
                          </label>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary m-l-1 m-t-1 f-right" type="submit">Create</button>
            </form>
        </div>
    </div>

</section>

<section class="panels-wells">

    @if(Session::has('error'))
    <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
    <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Lists</h5>
        </div>
        <div class="card-block">

            <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Website</th>
                        <th>Branch</th>
                        <th>Terminal</th>
                        <th>Is Open</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($terminalAssigns)
                    @foreach($terminalAssigns as $assign)
                    <tr>
                        <td id="cell_website{{ $assign->id }}" data-value="{{ $assign->website_id }}">{{$assign->name}}</td>
                        <td id="cell_branch{{ $assign->id }}" data-value="{{ $assign->branch_id }}">{{$assign->branch_name}}</td>
                        <td id="cell_terminal{{ $assign->id }}" data-value="{{ $assign->terminal_id }}">{{ '('.$assign->terminal_id.')  '.$assign->terminal_name }}</td>
                        <td id="cell_isOpen{{ $assign->id }}" data-value="{{ $assign->is_open }}">
                          <div class="form-group">
                              <label>
                                <input type="checkbox" id="branchStatus-{{ $assign->id }}" onchange="branchIsOpen({{ $assign->id }})" data-toggle="toggle" data-size="mini" {{ $assign->is_open == 1 ? 'checked' : '' }}>
                              </label>
                         </div>
                        </td>
                        <td>
                            <!--<a href="javascript:void(0)" onclick="edit({{-- $assign->id --}})" class="m-r-1"><i class="icofont icofont-ui-edit text-warning f-18" data-toggle="tooltip" data-placement="top" data-original-title="Edit"></i></a>-->

                            <a href="javascript:void(0)" onclick="edit({{ $assign->id }},'{{$assign->name}}')" class="m-r-1"><i class="icofont icofont-ui-edit text-warning f-18" data-toggle="tooltip" data-placement="top" data-original-title="Edit"></i></a>

                            <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm pointer p-l-4" data-toggle="tooltip" data-placement="top" data-original-title="Delete" onclick="swalModal('{{ $assign->id }}')"></i>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</section>


  <div class="modal fade modal-flex" id="editModal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Edit Assign Terminal</h4>
             </div>
            <form id="editFormTerminalBind" method="post">
             @csrf
             <div class="modal-body">
                 <input type="hidden" id="id_md" name="id">
                 <!--<input type="hidden" id="oldTerminal" name="oldTerminal">-->
                 <input type="hidden" name="mode" value="0">

                <div class="form-group">
                    <label class="form-control-label">Website</label>
                    <select name="website_md" id="website_md" data-placeholder="Select" class="form-control select2">
                        <option value="">Select</option>
                        @if($websites)
                            @foreach($websites as $val)
                              <option value="{{ $val->id }}">{{ $val->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="form-control-feedback text-danger" id="website_md_alert"></div>
                </div>

                <div class="form-group">
                    <label class="form-control-label">Branch</label>
                    <select name="branch_md" id="branch_md" data-placeholder="Select" class="form-control select2">
                        <option value="">Select</option>
                    </select>
                    <div class="form-control-feedback text-danger" id="branch_md_alert"></div>
                </div>
                <div class="form-group">
                    <label class="form-control-label">Terminal</label>
                    <select name="terminal_md" id="terminal_md" data-placeholder="Select" class="form-control select2" disabled>
                        <option value="">Select</option>
                    </select>
                    <div class="form-control-feedback text-danger" id="terminal_md_alert"></div>
                </div>
                <div class="form-group">
                    <label class="form-control-label">Is Open Branch</label>
                    <br/>
                    <label>
                      <input id="is_open_md" name="is_open_md" type="checkbox" data-toggle="toggle" data-size="mini" data-width="20" data-height="20">
                    </label>
                </div>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-default waves-effect waves-light m-r-2" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="btn_update">Save Changes</button>
             </div>
            </form>
           </div>
          </div>
        </div>

@endsection

@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('scriptcode_three')
 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript">
    $(".select2").select2();

    $('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
            search: '',
            searchPlaceholder: 'Search',
            lengthMenu: '<span></span> _MENU_'
        }
    });

	loadBranchesDropdown();

	@if(old('branch'))
	   $("#branch").val('{{ old('branch') }}').trigger('change');
	@endif

	@if(old('terminal'))
	   $("#terminal").val('{{ old('terminal') }}').trigger('change');
	@endif


    $("#branch").on('change', function() {
        loadTerminalsDropdown($(this).val(),'terminal','');
    });

    $("#branch_md").on('change', function() {
        loadTerminalsDropdown($(this).val(),'terminal_md','');
    });

    // $("#terminal_md").on('change', function() {
    //     $("#oldTerminal").val($(this).val());
    // });


    function edit(id,webName){
       $("#editModal").modal('show');

       $("#id_md").val(id);

    //   $("#website_name_md").text(webName);

       if($("#branchStatus-"+id).is(':checked')){

          $("#is_open_md").trigger('click');
        //  $("#is_open_md").attr('checked',true);
       }

       $("#website_md").val($("#cell_website"+id).attr('data-value')).trigger('change');
       $("#branch_md").val($("#cell_branch"+id).attr('data-value')).trigger('change');

    setTimeout(function() {
       loadTerminalsDropdown($("#cell_branch"+id).attr('data-value'),'terminal_md',$("#cell_terminal"+id).attr('data-value'));
    },550);

    //   $("#terminal_md").val().trigger('change');
    }

    $("#btn_update").on('click',function(){
        var process = true;

        if($("#website_md").val() == ''){
            $("#website_md").focus();
            process = false;
        }

        if($("#branch_md").val() == ''){
            $("#branch_md").focus();
            process = false;
        }

        if($("#terminal_md").val() == ''){
            $("#terminal_md").focus();
            process = false;
        }

        if(process){
            updateTerminalBind();
        }
    });

    function branchIsOpen(rId){
      var value = 0;
      if($("#branchStatus-"+rId).is(":checked")){
         value = 1;
      }

        $.ajax({
            url: '{{ route("terminalAssignUpdate") }}',
            type: 'POST',
            data: {_token:'{{ csrf_token() }}',is_open:value,id:rId,mode:1},
            async: true,
            success: function(resp) {
                if (resp.status == 200){
                    swal('Success!','', 'success');
                }else{
                    // console.log();
                    swal('Error!',resp, 'error');
                }
            }
        });
    }

    function updateTerminalBind(){

        $.ajax({
            url: '{{ route("terminalAssignUpdate") }}',
            type: 'POST',
            data: $("#editFormTerminalBind").serialize(),
            async: true,
            success: function(resp) {
                if (resp.status == 200){
                    swal('Success!','','success');
                    location.reload();
                }else{
                    swal('Error!',resp.msg,'error');
                }
            }
        });
    }

    function loadBranchesDropdown() {
        $.ajax({
            url: '{{ url("get-branches-by-company") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                company: "{{session('company_id')}}"
            },
            async: true,
            success: function(resp) {
                if (resp != null) {
                    $("#branch,#branch_md").empty();
                    // $("#branch,#branch_md").attr('disabled', false);
                        $("#branch,#branch_md").append("<option value=''>Select<option>");
                        $.each(resp, function(i, v) {
                            $("#branch,#branch_md").append("<option  value='" + v.branch_id + "'>" + v.branch_name + "<option>");
                        })
                }
            }
        });
    }

    function loadTerminalsDropdown(id,element,selectedValue) {
        $.ajax({
            url: '{{ route("getTerminalBranches") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                branchId: id
            },
            async: true,
            success: function(resp) {
                if (resp != null) {
                    $("#"+element).attr('disabled', false);
                    $("#"+element).empty();
                    $("#"+element).append("<option value=''>Select<option>")
                    $.each(resp, function(i, v) {
                        // if(selectedValue != ''){

                        //   $("#"+element).append("<option "+(v.terminal_id == selectedValue ? 'selected' : '')+" value='" + v.terminal_id + "'>" + v.terminal_name + "<option>");
                        // }else{
                           $("#"+element).append("<option value='" + v.terminal_id + "'>" + v.terminal_name + "<option>");
                        // }
                    })

                    $("#"+element).val(selectedValue).trigger('change');
                }
            }
        });
    }

    function swalModal(id) {
        swal({
            title: "Delete",
            text: "Are you sure that you want to delete this record?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "text-danger",
            confirmButtonText: "YES",
            cancelButtonText: "NO",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '{{ route("deleteWebsiteTerminal") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id
                    },
                    success: function(resp) {
                        if (resp.status == 200) {
                            location.reload();
                        }else{
                            console.log("Error : "+resp.message);
                        }
                    }
                });
            } else {
                swal.close();
            }
        });
    }
</script>
@endsection
