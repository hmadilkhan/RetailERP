@extends('layouts.master-layout')
@section('title', 'Inventory Deals')
@section('content')
    <section class="panels-wells">

        @if (Session::has('success'))
            <div class="row">
                <div class="alert alert-success">{{ Session::get('success') }}</div>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="row">
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Create Deal</h5>

            </div>
            <div class="card-block">

                <form method="POST" action="{{ route('dealStore') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('deal') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Deal</label>
                                <select name="deal" id="deal" class="form-control select2">
                                    <option value="">Select Deal</option>
                                    @foreach ($DealProducts as $val)
                                        <option {{ old('deal') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">
                                            {{ $val->product_name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('deal'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="form-inline">
                        <div class="form-group {{ $errors->has('group') ? 'has-danger' : '' }}">
                            <label class="form-control-label">Select Group Product</label>
                            <select name="group" id="group" class="form-control select2">
                                <option value="">Select</option>
                                @foreach ($InventGroups as $val)
                                    <option {{ old('group') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">
                                        {{ $val->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('group'))
                                <div class="form-control-feedback">Required field can not be blank.</div>
                            @endif
                        </div>

                        <div class="form-group m-l-5">
                            <label class="form-control-label">Select Products</label>
                            <select name="products[]" id="products" multiple="multiple" class="form-control select2"
                                style="width: 75%"></select>
                            <div class="form-control-feedback"></div>
                        </div>

                        <!--<button type="button" class="btn btn-primary m-t-2">+</button>-->
                    </div>

                    <button type="submit" class="btn btn-primary m-t-5">Submit</button>
                </form>

            </div>
        </div>
    </section>
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Lists</h5>
            </div>
            <div class="card-block">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="d-none">#</th>
                            <th>Deal</th>
                            <th>Deal Detail</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getRecord2 as $list_val)
                            @if (count($list_val->deals) > 0)
                                <tr>
                                    <td class="d-none">{{ $list_val->id }}</td>
                                    <td>{{ $list_val->product_name }}</td>
                                    <td class="pointer"
                                        onclick="getDetail({{ $list_val->id }},'{{ $list_val->deals }}','{{ addslashes($list_val->product_name) }}')">
                                        @foreach ($list_val->deals as $list_val2)
                                            <label class="badge badge-default pointer">
                                                {{ $list_val2->inventoryGroup->name }} </label>
                                        @endforeach
                                    </td>
                                    <td>
                                        <i onclick="deleteDeal('{{ $list_val->id }}','{{ $list_val->product_name }}')"
                                            class="text-danger text-center icofont icofont-trash" data-toggle="tooltip"
                                            data-placement="top" title="" data-original-title="Delete Deal"></i>

                                        <form id="removeDeal{{ $list_val->id }}" action="{{ route('dealRemove') }}"
                                            method="post">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $list_val->id }}">
                                            <input type="hidden" name="mode" value="">
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade modal-flex" id="dealDetail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Deal Details</h4>
                </div>
                <form action="{{ route('dealUpdate') }}" method="post">
                    @csrf
                    <div class="modal-body" id="md_body">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary waves-effect waves-light"
                            id="update_md">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('scriptcode_three')
    <script type="text/javascript">
        $(".select2").select2();
        var tmp = null;
        //   $("#products").tagsinput({
        //      maxTags: 80
        //     });
        @if (old('group'))
            $("#group").trigger('onchange');
        @endif

        $('.table').DataTable({
            bLengthChange: true,
            displayLength: 50,
            info: false,
            language: {
                search: '',
                searchPlaceholder: 'Search Deal',
                lengthMenu: '<span></span> _MENU_'

            }


        });

        $("#group").on('change', function() {
            getProducts($(this).val(), 0);
        });

        function getProducts(id, md) {
            // var tmp = null;
            $.ajax({
                url: '{{ route('getGroupValues') }}',
                type: 'GET',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                dataType: 'json',
                async: false,
                success: function(resp) {
                    //  console.log(resp);
                    if (resp != '') {
                        if (md == 0) {
                            $("#products").empty();
                            $("#products").append('<option value="">Select</option>');
                            $.each(resp, function(i, v) {
                                $("#products").append('<option selected value="' + v.id + '">' + v
                                    .name + '</option>');
                            })
                        } else {
                            tmp = resp;
                        }
                    }
                }
            });

        }

        function getDetail(id, dealsArray, dealName) {
            $("#dealDetail").modal('show');

            $("#md_body").empty();
            let deals = JSON.parse(dealsArray);

            $(".modal-title").text(dealName + ' Details');;
            $("#md_body").append('<input type="hidden" name="deal" value="' + id + '">')

            $.each(deals, function(index, value) {
                var click = 'onclick="deleteDealDetail(' + value.inventory_group.id + ',' + id + ')"';

                $("#md_body").append('<h3 id="dealCategory' + value.inventory_group.id +
                    '" class="displayInline">' + value.inventory_group.name +
                    '  </h3> <a href="javascript:void(0)" title="Remove ' + value.inventory_group.name + '" ' +
                    click + ' class="text-danger f-right"> <i class="icofont icofont-trash"></i> </a> ');
                $("#md_body").append(
                    '<div class="form-group"><label>Select Products</label><select id="md_product_' + value
                    .inventory_group.id + '" name="' + value.inventory_group.id +
                    '[]" multiple="multiple" class="form-control"></select></div>');
                getProducts(value.inventory_group.id, 1);
                $("#md_product_" + value.inventory_group.id).select2();

                $.each(value.get_deal_details, function(i) {
                    $("#md_product_" + value.inventory_group.id).append('<option selected value="' + tmp[i]
                        .id + '">' + tmp[i].name + '</option>');
                });
            })
        }

        function deleteDeal(id, name) {
            swal({
                title: "DELETE Deal",
                text: "Do you want to delete this deal " + name + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    if (id > 0) {
                        $("#removeDeal" + id).submit();
                    }
                } else {
                    swal({
                        title: "Cancel!",
                        text: "Deal " + name + " are safe :)",
                        type: "error"
                    }, function(isConfirm) {
                        if (isConfirm) {
                            window.location = "{{ route('listInventDeal') }}";
                        }
                    });
                }
            });
        }

        function deleteDealDetail(id, dealId) {
            swal({
                title: "DELETE " + $(".modal-title").text(),
                text: "Do you want to delete this " + $("#dealCategory" + id).text() + "?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    if (id > 0) {
                        $("#removeDeal" + dealId + " input[name='mode']").val(id);
                        $("#removeDeal" + dealId).submit();
                    }
                } else {
                    swal({
                        title: "Cancel!",
                        text: $(".modal-title").text() + " (" + $("#dealCategory" + id).text() +
                            ") are safe :)",
                        type: "error"
                    }, function(isConfirm) {
                        if (isConfirm) {
                            window.location = "{{ route('listInventDeal') }}";
                        }
                    });
                }
            });
        }
    </script>
@endsection
