@extends('layouts.master-layout')

@section('title','Branch Timings')

@section('breadcrumtitle','Branch Timings')

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
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Website</label>
                            <select name="website" id="website" data-placeholder="Select" class="form-control select2">
                                <option value="">Select</option>
                                @if($websites)
                                @php $oldWebsite = old('website'); @endphp
                                @foreach($websites as $val)
                                <option {{ !empty($terminalAssign) && $terminalAssign->website_id == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                                @endforeach
                                @endif
                            </select>
                            <div class="form-control-feedback" id="website_alert"></div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-control-label">Branch</label>
                            <select name="branch" id="branch" data-placeholder="Select" class="form-control select2" disabled>
                                <option value="">Select</option>
                            </select>
                            <div class="form-control-feedback" id="branch_alert"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!--<button class="btn btn-primary m-l-1 m-t-2 f-left" id="btnSearch" type="button">Search</button>-->
                    </div>
                </div>

        </div>
    </div>
</section>

<section class="panels-wells d-none" id="listBox">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Lists</h5>
        </div>
        <div id="tablediv" class="card-block">
        </div>
    </div>
</section>


@endsection

@section('scriptcode_one')
  {{-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script> --}}
@endsection

@section('scriptcode_three')
<script type="text/javascript">
    $(".select2").select2();

    // $('input.timepicker').datetimepicker({
    //     format: 'LT',
    //     icons: {
    //         time: "icofont icofont-clock-time",
    //         date: "icofont icofont-ui-calendar",
    //         up: "icofont icofont-rounded-up",
    //         down: "icofont icofont-rounded-down",
    //         next: "icofont icofont-rounded-right",
    //         previous: "icofont icofont-rounded-left"
    //     }
    // });



    $("#website").on('change', function() {
        loadBranchesDropdown($(this).val())
    });

    function loadBranchesDropdown(id) {
        $.ajax({
            url: '{{ route("getWebsiteBranches") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                websiteId: id
            },
            async: true,
            success: function(resp) {
                if (resp != null) {
                    $("#branch").empty();
                    $("#branch").append("<option  value=''>Select<option>");
                    $("#branch").attr('disabled', false);
                    $.each(resp, function(i, v) {
                        $("#branch").append("<option  value='" + v.branch_id + "'>" + v.branch_name + "<option>");
                    })
                }
            }
        });
    }

    $("#branch").on('change', function() {

       if($(this).val() != ''){
           if($("#website").val() == ''){
               $(this).focus();
               $("#website_alert").text('Please select website name').addClass('text-danger');
           }
        $.ajax({
                url: '{{ route("getBranchTiming") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    websiteId: $("#website").val(),
                    branchId: $("#branch").val()
                },

                success: function(resp) {
                     if($("#listBox").hasClass('d-none')){
                        $("#listBox").removeClass('d-none');
                    }
                    $("#tablediv").empty()
                    $("#tablediv").html(resp)
                }
            });
       }else{
          $(this).focus();
          $("#branch_alert").text('Please select branch name').addClass('text-danger');
       }
    });

    $("#btnSearch").click(function() {
        $.ajax({
            url: '{{ route("getBranchTiming") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                websiteId: $("#website").val(),
                branchId: $("#branch").val()
            },

            success: function(resp) {
                if($("#listBox").hasClass('d-none')){
                    $("#listBox").removeClass('d-none');
                }

                $("#tablediv").empty()
                $("#tablediv").html(resp)
            }
        });
    })
</script>
@endsection
