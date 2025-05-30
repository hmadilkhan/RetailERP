<section>
    <div wire:loading.class="d-flex flex-column" wire:loading>
        <div class='position-relative w-100 h-100 d-flex flex-column align-items-center bg-white justify-content-center'>
            <div class='spinner-border text-dark' role='status'>
                <span class='visually-hidden'>Loading...</span>
            </div>
        </div>
    </div>
    <div class="project-table">
        <table wire:loading.remove id="inventtbl"
            class="table table-striped nowrap dt-responsive m-t-10 dataTable no-footer dtr-inline">
            <thead>
                <tr>
                    <th>
                        <div class="rkmd-checkbox checkbox-rotate">
                            <label class="input-checkbox checkbox-primary">
                                <input type="checkbox" id="checkbox32" class="mainchk">
                                <span class="checkbox"></span>
                            </label>
                            <div class="captions"></div>
                        </div>
                    </th>
                    <th>Preview</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Depart</th>
                    <th>Sub-Depart</th>
                    <th>Price</th>
                    <th>GST%</th>
                    <th>Retail</th>
                    <th>Wholesale</th>
                    <th>Online</th>
                    <th>Stock</th>
                    <th>UOM</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventories as $inventory)
                    <tr>
                        <td>
                            <div class='rkmd-checkbox checkbox-rotate'>
                                <label class='input-checkbox checkbox-primary'>
                                    <input type='checkbox' id='checkbox32{{ $inventory->id }}' class='chkbx'
                                        onclick='chkbox("checkbox32{{ $inventory->id }}")'
                                        data-id='{{ $inventory->id }}'>
                                    <span class='checkbox'></span>
                                </label>
                                <div class='captions'></div>
                            </div>
                        </td>
                        <td>
                            <a href="{{ asset('storage/images/products/' . ($inventory->product_image == '' ? '/placeholder.jpg' : $inventory->product_image)) }}"
                                data-toggle="lightbox" data-footer=''>
                                <img width="12" height="12" data-modal="modal-12"
                                    src="{{ asset('storage/images/products/' . ($inventory->product_image == '' ? '/placeholder.jpg' : $inventory->product_image)) }}"
                                    class='d-inline-block img-circle ' alt=''>
                            </a>
                        </td>
                        <td>{{ $inventory->item_code }}</td>
                        <td>{{ $inventory->product_name }}</td>
                        <td>{{ $inventory->department_name }}</td>
                        <td>{{ $inventory->sub_depart_name }}</td>
                        <td>{{ $inventory->actual_price }}</td>
                        <td>{{ $inventory->tax_rate == null ? 0.0 : $inventory->tax_rate }}</td>
                        <td>{{ $inventory->retail_price }}</td>
                        <td>{{ $inventory->wholesale_price }}</td>
                        <td>{{ $inventory->online_price }}</td>
                        <td>{{ $inventory->stock }}</td>
                        <td>{{ $inventory->name }}</td>
                        <td>
                            <a onclick='show_barcode("{{ $inventory->item_code }}","{{ $inventory->product_name }}","{{ $inventory->retail_price }}")'
                                class='p-r-10 f-18 text-success' data-toggle='tooltip' data-placement='top'
                                title='Print Barcode' data-original-title='Barcode'><i
                                    class='icofont icofont-barcode'></i></a>
                            <a onclick='edit_route("{{ $inventory->slug }}")' class='p-r-10 f-18 text-warning'
                                data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'><i
                                    class='icofont icofont-ui-edit'></i></a>
                            <i class='icofont icofont-ui-delete text-danger f-18 '
                                onclick='deleteCall("{{ $inventory->id }}")' data-id='value.id' data-toggle='tooltip'
                                data-placement='top' title='' data-original-title='Delete'></i>
                            &nbsp;<i
                                onclick='assignToVendorModal("{{ $inventory->id }}") class="icofont icofont icofont-business-man #3A6EFF" data-toggle='tooltip'
                                data-placement='top' title='' data-original-title='Assign To Vendors'></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $inventories->links() }}
    <br>
    <div class="button-group ">
        <a style="color:white;" target="_blank" href="{{ URL::to('get-export-csv-for-retail-price') }}"
            class="btn btn-md btn-success waves-effect waves-light f-right"><i class="icofont icofont-file-excel"> </i>
            Export to Excel Sheet
        </a>
    </div>
</section>
@section('scriptcode_three')
    <script>
        var rem_id = [];

        function toggleDdSelect() {
            if ($(".chkbx:checked").length > 0) {
                $("#ddselect").css("display", "block");
            } else {
                $("#ddselect").css("display", "none");
            }
        }
        $(".mainchk").on('click', function() {
            const isChecked = $(this).is(":checked");
            $(".chkbx").prop("checked", isChecked).change();
            toggleDdSelect();
        });

        function chkbox(id) {
            const totalCheckboxes = $(".chkbx").length;
            const checkedCheckboxes = $(".chkbx:checked").length;
            // Update 'Select All' checkbox state
            $(".mainchk").prop("checked", totalCheckboxes === checkedCheckboxes);
            toggleDdSelect();
        }
        $(".subchk").on('click', function() {
            if ($(this).is(":checked")) {
                $("#btn_activeall").css("display", "block");
                $(".chkbx").each(function(index) {
                    $(this).attr("checked", true);
                });
            } else {
                $("#btn_activeall").css("display", "none");
                $(".chkbx").each(function(index) {
                    $(this).attr("checked", false);
                });
            }
        });
        $(".chkbx").on('click', function() {
            if ($(this).is(":checked")) {
                // $("#btn_activeall").removeClass('invisible');
                $("#btn_activeall").css("display", "block");
            } else {
                // $("#btn_activeall").addClass('invisible');
                $("#btn_activeall").css("display", "none");
            }
        });
        $("#btn_removeall").on('click', function() {
            var products = [];
            $(".chkbx").each(function(index) {
                if ($(this).is(":checked")) {
                    console.log($(this).data('id'))
                    if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                        rem_id.push($(this).data('id'));
                    }
                }
            });
            // console.log(rem_id)
            $.ajax({
                url: "{{ url('/get_names') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: rem_id
                },
                async: false,
                success: function(resp) {
                    for (var s = 0; s < resp.length; s++) {
                        products.push(resp[s].product_name);
                    }
                }
            });
            var names = products.join();
            swal({
                title: "INACTIVE PRODUCTS",
                text: "Do you want to inactive  " + names + " ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    if (rem_id.length > 0) {
                        $.ajax({
                            url: "{{ url('/all_invent_remove') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                inventid: rem_id,
                                statusid: 2
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products In-Active Successfully :)",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            fetch_data(1)
                                        }
                                    });
                                } else {
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }
                            }
                        });
                    }
                } else {
                    swal({
                        title: "Cancel!",
                        text: "All products are still inactive :)",
                        type: "error"
                    }, function(isConfirm) {
                        if (isConfirm) {
                            fetch_data(1)
                        }
                    });
                }
            });
        });
        $("#btn_activeall").on('click', function() {
            var products = [];
            $(".chkbx").each(function(index) {
                if ($(this).is(":checked")) {
                    if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                        rem_id.push($(this).data('id'));
                    }
                }
            });
            // console.log(rem_id)
            $.ajax({
                url: "{{ url('/get_names') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: rem_id
                },
                async: false,
                success: function(resp) {
                    for (var s = 0; s < resp.length; s++) {
                        products.push(resp[s].product_name);
                    }
                }
            });
            var names = products.join();
            swal({
                title: "RE-ACTIVE",
                text: "Do you want to activate  " + names + " this items?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    if (rem_id.length > 0) {
                        $.ajax({
                            url: "{{ url('/multiple-active-invent') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                inventid: rem_id
                            },
                            success: function(resp) {
                                if (resp == 1) {
                                    swal({
                                        title: "Success!",
                                        text: "All Products activated Successfully :)",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            fetch_data(1)
                                        }
                                    });
                                } else {
                                    swal("Alert!", "Products not Deleted:)", "error");
                                }
                            }
                        });
                    }
                } else {
                    swal({
                        title: "Cancel!",
                        text: "All products are safe:)",
                        type: "error"
                    }, function(isConfirm) {
                        if (isConfirm) {
                            fetch_data(1)
                            // $('#pro').removeClass("active");
                            // $('#act').addClass("active");
                        }
                    });
                }
            });
        });
        $('#btnwebsiteSave').click(function() {
            if ($('#website_md').val() == "") {
                swal("Cancelled", "Please select website :)", "error");
            } else {
                $(".chkbx").each(function(index) {
                    if ($(this).is(":checked")) {
                        if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                            rem_id.push($(this).data('id'));
                        }
                    }
                });
                $.ajax({
                    url: "{{ route('setProductAttribute_update') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventid: rem_id,
                        website: $('#website_md').val()
                    },
                    success: function(resp) {
                        if (resp == 'success') {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            swal("Cancelled", "Cannot change website. An error occured :)", "error");
                        }
                    }
                }); //ajax end
            } //else end
        });
        $('#btnbrandSave').click(function() {
            if ($('#brand_md').val() == "") {
                swal("Cancelled", "Please select brand :)", "error");
            } else {
                $(".chkbx").each(function(index) {
                    if ($(this).is(":checked")) {
                        if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                            rem_id.push($(this).data('id'));
                        }
                    }
                });
                $.ajax({
                    url: "{{ route('setProductAttribute_update') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventid: rem_id,
                        brand: $('#brand_md').val()
                    },
                    success: function(resp, textStatus, jqXHR) {
                        // console.log(jqXHR);
                        if (jqXHR.status == 200) {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            swal("Cancelled", "Cannot change brand. An error occured :)", "error");
                        }
                    }
                }); //ajax end
            } //else end
        });
        $('#btntagSave').click(function() {
            if ($('#tags_md').val() == "") {
                swal("Cancelled", "Please select brand :)", "error");
            } else {
                $(".chkbx").each(function(index) {
                    if ($(this).is(":checked")) {
                        if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                            rem_id.push($(this).data('id'));
                        }
                    }
                });
                $.ajax({
                    url: "{{ route('setProductAttribute_update') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventid: rem_id,
                        tags: $('#tags_md').val()
                    },
                    success: function(resp, textStatus, jqXHR) {
                        // console.log(jqXHR);
                        if (jqXHR.status == 200) {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            swal("Cancelled", "Cannot change brand. An error occured :)", "error");
                        }
                    }
                }); //ajax end
            } //else end
        });
        $('#btnDptSave').click(function() {
            if ($('#ddldepartment').val() == "") {
                swal("Cancelled", "Please Select Department :)", "error");
            } else {
                $(".chkbx").each(function(index) {
                    if ($(this).is(":checked")) {
                        if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                            rem_id.push($(this).data('id'));
                        }
                    }
                });
                $.ajax({
                    url: "{{ url('update_product_department') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventid: rem_id,
                        deptId: $('#ddldepartment').val()
                    },
                    success: function(resp) {
                        if (resp == 1) {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            swal("Cancelled", "Cannot change department. An error occured :)", "error");
                        }
                    }
                }); //ajax end
            } //else end
        });
        $('#btnUomSave').click(function() {
            if ($('#ddluom').val() == "") {
                swal("Cancelled", "Please Select Unit of Measure :)", "error");
            } else {
                $(".chkbx").each(function(index) {
                    if ($(this).is(":checked")) {
                        if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                            rem_id.push($(this).data('id'));
                        }
                    }
                });
                $.ajax({
                    url: "{{ url('update_product_uom') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventid: rem_id,
                        uomId: $('#ddluom').val()
                    },
                    success: function(resp) {
                        if (resp == 1) {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            swal("Cancelled", "Cannot change Unit of Measure. An error occured :)",
                                "error");
                        }
                    }
                }); //ajax end
            } //else end
        });
        $('#btnTaxSave').click(function() {
            if ($('#ddltax').val() == "") {
                swal("Cancelled", "Please Select Tax :)", "error");
            } else if ($('#tax_rate_new').val() == "") {
                swal("Cancelled", "Please Enter New Tax :)", "error");
            } else {
                $('#btnTaxSave').prop('disabled', true);
                $.ajax({
                    url: "{{ url('update_product_tax') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        prev_tax: $('#ddltax').val(),
                        new_tax: $('#tax_rate_new').val()
                    },
                    success: function(resp) {
                        if (resp == 1) {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            $('#btnTaxSave').prop('disabled', false);
                            swal("Cancelled", "Cannot change tax. An error occured :)", "error");
                        }
                    }
                });
            }
        });
        $('#btnsubdeptSave').click(function() {
            if ($('#ddlsubdept').val() == "") {
                swal("Cancelled", "Please Select Sub Department :)", "error");
            } else {
                $(".chkbx").each(function(index) {
                    if ($(this).is(":checked")) {
                        if (jQuery.inArray($(this).data('id'), rem_id) == -1) {
                            rem_id.push($(this).data('id'));
                        }
                    }
                });
                $.ajax({
                    url: "{{ url('update_product_subdepartment') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        inventid: rem_id,
                        subdeptId: $('#ddlsubdept').val(),
                        deptID: $('#ddldepartment1').val()
                    },
                    success: function(resp) {
                        if (resp == 1) {
                            window.location = "{{ url('inventory-list') }}"
                        } else {
                            swal("Cancelled", "Cannot change Sub Department. An error occured :)",
                                "error");
                        }
                    }
                }); //ajax end
            } //else end
        });
    </script>
@endsection
