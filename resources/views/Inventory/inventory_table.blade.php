<div class="project-table ">
    <table id="inventtbl" class="table table-striped nowrap dt-responsive m-t-10 dataTable no-footer dtr-inline">
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
              {{-- @php
                  $imageUrl = asset('storage/images/no-image.png');

                  if(in_array(session('company_id'),[95,102,104])){
                    if(!empty($inventory->product_image_url)){
                        $imageUrl = $inventory->product_image_url;
                    }else{
                       if(!empty($inventory->product_image)){
                        $imageUrl = asset('storage/images/products/'.$inventory->product_image);
                       }
                    }
                  }else{
                     if(!empty($inventory->product_image)){
                        $imageUrl = asset('storage/images/products/'.$inventory->product_image);
                     }
                  }
              @endphp --}}
                <tr>
                    <td>
                        <div class='rkmd-checkbox checkbox-rotate'>
                            <label class='input-checkbox checkbox-primary'>
                                <input type='checkbox' id='checkbox32{{ $inventory->id }}' class='chkbx'
                                    onclick='chkbox("checkbox32{{ $inventory->id }}")' data-id='{{ $inventory->id }}'>
                                <span class='checkbox'></span>
                            </label>
                            <div class='captions'></div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ Custom_Helper::getProductImageUrl($inventory) }}"
                            data-toggle="lightbox" data-footer=''>
                            <img width="12" height="12" data-modal="modal-12" src="{{asset('storage/images/no-image.png')}}"
                            data-src="{{ Custom_Helper::getProductImageUrl($inventory) }}"
                                class='d-inline-block img-circle lazy-load' alt='' >
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

                     @if(Auth::user()->username == 'demoadmin')
                     <div class="dropdown-success">
                        <button class="btn btn-inverse-default dropdown-toggle waves-effect " type="button" id="dropdown3"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                        <div class="dropdown-menu" aria-labelledby="dropdown3" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                          @if ($inventory->is_deal == 1)
                          <a href='/inventory/{{ $inventory->id }}/deal-products' class="dropdown-item waves-light waves-effect"
                              title='View Deal Products'>View Deal Products</a>
                      @else
                          {{-- <a href='/inventory/{{ $inventory->id }}/variable-products' class='m-r-1'
                              title='Create Variable & Addon Product'><i
                                  class='icofont icofont-plus text-success'></i></a> --}}
                            @if($inventory->pos_product_count != 0)
                              <a href='/inventory/{{ $inventory->id }}/variable-products' class="dropdown-item waves-light waves-effect"
                                  title='View Variable and Addon Products'>
                                  Create Variable & Addon
                              </a>
                            @else
                                  <a href='javascript:void(0)' onclick="productSetting({{ $inventory->id }})" class="dropdown-item waves-light waves-effect"
                                      title='Make a Deal & Variable Product'>View Variable & Addon</a>
                            @endif
                      @endif
                      <a onclick='show_barcode("{{ $inventory->item_code }}","{{ $inventory->product_name }}","{{ $inventory->retail_price }}")'
                        class="dropdown-item waves-light waves-effect" data-toggle='tooltip' data-placement='top'
                        title='Print Barcode' data-original-title='Barcode'>Print Barcode</a>
                    <a onclick='edit_route("{{ $inventory->slug }}")' class="dropdown-item waves-light waves-effect"
                        data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'>Edit</a>

                    <a href="javascript:void(0)" class='dropdown-item waves-light waves-effect'
                        onclick='deleteCall("{{ $inventory->id }}")' data-id='value.id' data-toggle='tooltip'
                        data-placement='top' title='' data-original-title='Delete'>Delete</a>
                    <a href="javascript:void(0)" onclick='assignToVendorModal("{{ $inventory->id }}")' class="dropdown-item waves-light waves-effect #3A6EFF" data-toggle='tooltip'
                        data-placement='top' title='' data-original-title='Assign To Vendors'> Assign To Vendors</a>

                        @if ($inventory->website_id != '')
                             <a href="javascript:void(0)" class="dropdown-item waves-light waves-effect" onclick="UnLinkwebsite({{ $inventory->id }},{{$inventory->website_id}},'{{ $inventory->website_name }}')"
                                data-toggle='tooltip' data-placement='top' title='' data-original-title='Unlink for {{$inventory->website_name }} website'>
                                Un-link to website</a>
                        @endif

                        </div>
                      </div>
                     @else


                        @if ($inventory->is_deal == 1)
                            <a href='/inventory/{{ $inventory->id }}/deal-products' class='m-r-1'
                                title='View Deal Products'><i class='icofont icofont-arrow-right text-success'></i></a>
                        @else
                            {{-- <a href='/inventory/{{ $inventory->id }}/variable-products' class='m-r-1'
                                title='Create Variable & Addon Product'><i
                                    class='icofont icofont-plus text-success'></i></a> --}}
                              @if($inventory->pos_product_count != 0)
                                <a href='/inventory/{{ $inventory->id }}/variable-products' class='m-r-1'
                                    title='View Variable and Addon Products'>
                                    <i class='icofont icofont-arrow-right text-success'></i>
                                </a>
                              @else
                                    <a href='javascript:void(0)' onclick="productSetting({{ $inventory->id }})" class='m-r-1'
                                        title='Make a Deal & Variable Product'><i
                                            class='icofont icofont-plus text-success'></i></a>
                              @endif
                        @endif


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

                            @if ($inventory->website_id != '')
                                 <a href="javascript:void(0)" class="text-info" onclick="UnLinkwebsite({{ $inventory->id }},{{$inventory->website_id}},'{{ $inventory->website_name }}')"
                                    data-toggle='tooltip' data-placement='top' title='' data-original-title='Unlink for {{$inventory->website_name }} website'>
                                    <i class="icofont icofont-link"></i></a>
                            @endif
                      @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <div class="button-group ">
        <div class="f-left">{!! $inventories->links('pagination::bootstrap-4') !!}</div>
        <div class="text-center f-center ">
            <p class="f-center text-md f-18 mt-3">
                {!! __('Showing') !!}
                <span class="">{{ $inventories->firstItem() }}</span>
                {!! __('to') !!}
                <span class="">{{ $inventories->lastItem() }}</span>
                {!! __('of') !!}
                <span class="">{{ $inventories->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>
        <a style="color:white;" target="_blank" href="{{ URL::to('get-export-csv-for-retail-price') }}"
            class="btn btn-md btn-success waves-effect waves-light f-right"><i class="icofont icofont-file-excel"> </i>
            Export to Excel Sheet
        </a>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let lazyImages = [].slice.call(document.querySelectorAll("img.lazy-load"));

        if ("IntersectionObserver" in window) {
            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove("lazy-load");
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        } else {
            // Fallback for older browsers
            lazyImages.forEach(function(lazyImage) {
                lazyImage.src = lazyImage.dataset.src;
            });
        }
    });

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
