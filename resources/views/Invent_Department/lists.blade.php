@extends('layouts.master-layout')

@section('title', 'Inventory-Department')

@section('breadcrumtitle', 'View Inventory')
@section('navinventory', 'active')
@section('navinvent_depart', 'active')

@section('content')
    <section class="panels-wells p-t-30">
        <div class="row">

            <div class="col-lg-12 col-md-12">
                <div class="form-group">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-header-text">Sub Department List</h5>
                            <a href="{{ route('departmentCreate') }}" data-toggle="tooltip" data-placement="bottom"
                                title="" data-original-title="Create Department"
                                class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10">
                                <i class="icofont icofont-plus m-r-5"></i> Create Department</a>
                            <button type="button" id="btn_removeall" class="btn btn-danger f-right m-r-10 invisible"><i
                                    class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>


                        </div>
                        <div class="card-block">
                            <div class="project-table">
                                <table id="mainTable" class="table table-striped full-width">
                                    <thead>
                                        <tr>
                                            <th class="d-none">Code</th>
                                            <th>Code</th>
                                            <th>Image</th>
                                            <th>Department</th>
                                            <th>Website Department</th>
                                            <th>Sub Department</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($depart)
                                            @for ($d = 0; $d < sizeof($depart); $d++)
                                                @php $sectionValue = !empty($depart[$d]->inventoryDepartmentSection) ? $depart[$d]->inventoryDepartmentSection->pluck('section_id') : '' @endphp
                                                <tr>
                                                    <td class="d-none">{{ $depart[$d]->department_id }}</td>
                                                    <td class="pointer"
                                                        onclick="editdepart({{ $depart[$d]->department_id }})">
                                                        {{ $depart[$d]->code }}</td>
                                                    <td class="pointer"
                                                        onclick="editdepart({{ $depart[$d]->department_id }})">
                                                        @if (!empty($depart[$d]->image) && File::exists('storage/images/department/' . $depart[$d]->image))
                                                            <img id="img-tble-{{ $depart[$d]->department_id }}"
                                                                src="{{ asset('storage/images/department/' . $depart[$d]->image) }}"
                                                                alt="{{ $depart[$d]->image }}" height="64"
                                                                width="64" />
                                                        @else
                                                            <img id="img-tble-{{ $depart[$d]->department_id }}"
                                                                src="{{ asset('storage/images/no-image.jpg') }}"
                                                                alt="{{ $depart[$d]->image }}" height="64"
                                                                width="64" />
                                                        @endif
                                                    </td>
                                                    <td class="pointer"
                                                        onclick="editdepart({{ $depart[$d]->department_id }})">
                                                        {{ $depart[$d]->department_name }}</td>
                                                    <td class="pointer"
                                                        onclick="editdepart({{ $depart[$d]->department_id }})">
                                                        {{ $depart[$d]->website_department_name }}
                                                        <input type="hidden" id="deptCode_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->code }}">
                                                        <input type="hidden" id="deptName_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->department_name }}">
                                                        {{-- <input type="hidden" id="image_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->image }}"> --}}
                                                        <input type="hidden" id="desktopBanner_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->banner }}">
                                                        <input type="hidden" id="mobileBanner_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->mobile_banner }}">
                                                        <input type="hidden" id="websiteMode_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->website_mode }}">
                                                        <input type="hidden" id="websiteDeptName_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->website_department_name }}">
                                                        <input type="hidden" id="metaTitle_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->meta_title }}">
                                                        <input type="hidden" id="metaDescript_mdept{{ $depart[$d]->department_id }}" value="{{ $depart[$d]->meta_description }}">
                                                        <input type="hidden" id="depatSection_mdept{{ $depart[$d]->department_id }}" value="{{ $sectionValue }}">
                                                    </td>
                                                    <td class="pointer"
                                                        onclick="editsubdepart('{{ $depart[$d]->code }}','{{ $depart[$d]->department_id }}','{{ addslashes($depart[$d]->department_name) }}')">
                                                        @if ($depart)
                                                            @for ($sd = 0; $sd < sizeof($sdepart); $sd++)
                                                                @if ($sdepart[$sd]->department_id == $depart[$d]->department_id)
                                                                    <label>{{ $sdepart[$sd]->sub_depart_name }}</label>,
                                                                @endif
                                                            @endfor
                                                        @endif
                                                    </td>
                                                    <td class="action-icon">
                                                        <i onclick="addsubdepart('{{ $depart[$d]->department_id }}')"
                                                            class="text-success text-center icofont icofont-plus"
                                                            data-toggle="tooltip" data-placement="top" title=""
                                                            data-original-title="Add Sub-Department"></i>
                                                        <i onclick="deletedepart('{{ $depart[$d]->department_id }}')"
                                                            class="text-danger text-center icofont icofont-ui-delete"
                                                            data-toggle="tooltip" data-placement="top" title=""
                                                            data-original-title="Delete Department"></i>

                                                        @if ($websites)
                                                            @if (count($depart[$d]->websiteProducts) > 0 && count($depart[$d]->websiteProducts->pluck('website_id')) > 0)
                                                                {{-- {{$depart[$d]->websiteProducts->pluck('website_id')}} --}}
                                                                {{-- @if (count($depart[$d]->websiteProducts->pluck('website_id')) > 0) --}}
                                                                    <i class="icofont icofont-link text-info pointer"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="" data-original-title="Unlink Website"
                                                                        onclick="unLinkWebsite({{ $depart[$d]->department_id }})"></i>
                                                                @else
                                                                    <i class="icofont icofont-unlink text-muted pointer"
                                                                        data-toggle="tooltip" data-placement="top"
                                                                        title="" data-original-title="Link Website"
                                                                        onclick="linkWebsite({{ $depart[$d]->department_id }})"></i>
                                                                {{-- @endif --}}
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>
            </div>



        </div>

        <div class="modal fade modal-flex" id="depart-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Edit Department</h4>
                    </div>
                    <div class="modal-body">
                        <form id="editDepartmentForm" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Code:</label>
                                        <input type="text" name="editcode" id="editcode" class="form-control" />
                                        <input type="hidden" name="code" id="codeid" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-control-label">Department Name:</label>
                                        <input type="text" name="departname" id="depart" class="form-control" />
                                        <input type="hidden" name="departid" id="departid" />
                                    </div>
                                </div>


                                @if ($websites)
                                    <hr />
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="showWebsite_md">
                                                <input type="checkbox" id="showWebsite_md" name="showWebsite">
                                                Show on Website
                                            </label>
                                        </div>
                                    </div>

                                    <div class="d-none" id="website-module_md">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label">Show website department name</label>
                                                <input class="form-control" type="text" name="webdeptname"
                                                    id="webdeptname_md" />
                                                <span class="form-control-feedback text-danger"
                                                    id="webdeptname_md_alert"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label">Section</label>
                                                <select class="select2" name="sections[]" id="sections_md"
                                                    data-placeholder="Select" multiple>
                                                    <option value="">Select</option>
                                                    @foreach ($sections as $val)
                                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-control-feedback text-danger"
                                                    id="sections_md_alert"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label">Meta Title</label>
                                                <input class="form-control" type="text" name="metatitle"
                                                    id="metatitle_md" placeholder="Meta Title" />
                                                <span class="form-control-feedback text-danger"
                                                    id="metatitle_md_alert"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label">Meta Description</label>
                                                <textarea class="form-control" rows="5" name="metadescript" id="metadescript_md"
                                                    placeholder="Meta Description"></textarea>
                                                <span class="form-control-feedback text-danger"
                                                    id="metadescript_md_md_alert"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <img src="{{ asset('storage/images/no-image.jpg') }}" alt="placeholder.jpg"
                                            width="128" height="128" id="previewImg" /></br>
                                        <label for="departImage_md" class="form-control-label">Department
                                            Image</label></br>

                                        <label for="departImage_md" class="custom-file">
                                            <input type="file" name="departImage" id="departImage_md"
                                                class="custom-file-input">
                                            <span class="custom-file-control"></span>
                                        </label>
                                    </div>
                                    <span class="text-danger" id="departImage_md_alert"></span>
                                </div>

                                @if ($websites)
                                    <div class="col-md-12 d-none" id="banner-imageBox_md">
                                        <div class="form-group">
                                            <a href="javascript:void(0)">
                                                <img id="previewDepartBannerImage_md"
                                                    src="{{ asset('storage/images/placeholder.jpg') }}" height="180px"
                                                    class="thumb-img width-100" alt="img">
                                            </a>

                                            <div class="form-group m-t-10">
                                                <label for="bannerImage_md" class="form-control-label">Department
                                                    Desktop Banner</label>
                                                <label for="bannerImage_md" class="custom-file">
                                                    <input type="file" name="bannerImage" id="bannerImage_md"
                                                        class="custom-file-input">
                                                    <span class="custom-file-control"></span>
                                                </label>
                                            </div>
                                            <span class="text-danger" id="bannerImage_md_alert"></span>
                                        </div>
                                    </div>

                                    <div class="col-md-12 d-none" id="mobilebanner-imageBox_md">
                                        <div class="form-group">
                                            <a href="javascript:void(0)">
                                                <img id="previewDepartMobileBannerImage_md"
                                                    src="{{ asset('storage/images/placeholder.jpg') }}" width="150" height="200"
                                                    class="thumb-img" alt="img">
                                            </a>

                                            <div class="form-group m-t-10">
                                                <label for="mobileBannerImage_md" class="form-control-label">Department
                                                    Mobile Banner</label></br>
                                                <label for="mobileBannerImage_md" class="custom-file">
                                                    <input type="file" name="mobile_banner" id="mobileBannerImage_md"
                                                        class="custom-file-input">
                                                    <span class="custom-file-control"></span>
                                                </label>
                                            </div>
                                            <span class="text-danger" id="mobileBannerImage_md_alert"></span>
                                        </div>
                                    </div>                                    
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light"
                            onClick="updatedepart()">Edit Department</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade modal-flex" id="subdepart-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xlg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Edit Sub-Department</h4>
                        <hr />
                        <h4 class="modal-title" id="depart_modal_name">Department</h4>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="uhidd_id" id="uhidd_id" value="0" />
                        <input type="hidden" name="department_code" id="department_code" />

                        <table class="table full-width sb_tble">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Sub Department</th>
                                    <th>Website Department</th>
                                    <th>Image</th>
                                    <th>Desktop Banner</th>
                                    <th>Mobile Banner</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void()" onclick="window.location.href='{{ url('invent_dept') }}'"
                            data-dismiss="modal" class="btn btn-success waves-effect waves-light">OK</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-flex" id="subdepartment-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Add Sub Department</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <select class="form-control  select2" data-placeholder="Select Department"
                                        id="departmodal" name="departid">
                                        <option value="">Select Department</option>
                                        @if ($depart)
                                            @foreach ($depart as $val)
                                                @if (old('depart') == $val->department_id)
                                                    <option selected="selected" value="{{ $val->department_id }}">
                                                        {{ $val->department_name }}</option>
                                                @else
                                                    <option value="{{ $val->department_id }}">{{ $val->department_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Code:</label>
                                    <input type="text" name="code" id="subdepartcode" class="form-control"
                                        placeholder="Sub department code" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Sub Department Name:</label>
                                    <input type="text" name="subdepart" id="subdepartname" class="form-control"
                                        placeholder="Sub department name" />
                                </div>
                            </div>
                            @if($websites)
                            <div class="col-md-12">  
                            <hr/>
                            <div class="form-group">
                                <label for="showWebsite_sbdept_md" class="pointer">
                                    <input type="checkbox" id="showWebsite_sbdept_md" name="showWebsite">
                                    Show on Website
                                </label>
                            </div>
                        </div>  
                        <div class="col-md-12">    
                            <div class="d-none" id="website_module_sbdept_md">
                                <div class="form-group">
                                    <label class="form-control-label">Show website sub department name:</label>
                                    <input type="text" name="websubdepart" id="websubdepartname" class="form-control"
                                        placeholder="Show website sub department name" />
                                </div>
                            </div>
                        </div>  
                            @endif
                            <div class="col-md-12">
                                <div class="form-group">
                                    <img src="{{ asset('storage/images/no-image.jpg') }}" alt="placeholder.jpg"
                                        width="128" height="128" id="previewImg_sbmd" /></br>
                                    <label for="subdepartImage_add" class="form-control-label">Sub Department
                                        Image</label></br>

                                    <label for="subdepartImage_add" class="custom-file">
                                        <input type="file" name="subdepartImage" id="subdepartImage_add"
                                            class="custom-file-input">
                                        <span class="custom-file-control"></span>
                                    </label>
                                </div>
                            </div>
                          <div class="d-none" id="website_module_bannersbdept_md">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <img src="{{ asset('storage/images/no-image.jpg') }}" alt="placeholder.jpg"
                                        width="180" height="256" id="previewBanner_sbmd" /></br>
                                    <label for="subdepartBanner_add" class="form-control-label">Sub Department
                                        Banner Image</label></br>

                                    <label for="subdepartBanner_add" class="custom-file">
                                        <input type="file" name="subdepartBanner" id="subdepartBanner_add"
                                            class="custom-file-input">
                                        <span class="custom-file-control"></span>
                                    </label>
                                </div>
                            </div>   
                          </div>  
                          
                          <div class="d-none" id="website_module_mobileBannersbdept_md">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <img src="{{ asset('storage/images/no-image.jpg') }}" alt="placeholder.jpg"
                                        width="150" height="200" id="previewMobileBanner_sbmd" /></br>
                                    <label for="subdepartMobileBanner_add" class="form-control-label">Sub Department
                                        Mobile Banner</label></br>

                                    <label for="subdepartMobileBanner_add" class="custom-file">
                                        <input type="file" name="mobile_banner_sbdepart" id="subdepartMobileBanner_add"
                                            class="custom-file-input">
                                        <span class="custom-file-control"></span>
                                    </label>
                                </div>
                            </div>   
                          </div>                           
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light"
                            onClick="insertsubdepart()">Add Sub Department</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade modal-flex" id="department-website-connect-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="wesbite_md_title"> Wesbite</h4>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <input type="hidden" id="depart_webconn_md">
                            <select class="form-control  select2" data-placeholder="Select Website" id="website_md"
                                name="website_md">
                                <option value="">Select Website</option>
                                @if ($websites)
                                    @foreach ($websites as $val)
                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success waves-effect waves-light"
                            id="btn_website_connect_md">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css_code')
<style>
    @media (min-width: 992px) {
    .modal-xlg {
        max-width: 1600px;
    }
   }
</style>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $(".select2").select2();

        $("#subdpt").tagsinput({
            maxTags: 10
        });
        $('#mainTable').DataTable({

            bLengthChange: true,
            displayLength: 50,
            info: false,
            order: [
                [0, 'desc']
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search Department',
                lengthMenu: '<span></span> _MENU_'

            }


        });

        function deleteSubDepart(code) {
            swal({
                    title: "Are you sure?",
                    text: "This "+$("#tbx_"+code).val()+" sub department will be remove?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes plx!",
                    cancelButtonText: "cancel plx!"
                    // closeOnConfirm: false,
                    // closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                                 url:'{{ url("/delete-subdepartment") }}',
                                 type:'POST',
                                 data:{_token:'{{ csrf_token() }}',id:code},
                                 dataType:'json',
                                 success:function(resp,txtStatus,jxState){
                                      if(jxState.status == 200){
                                        swal({
                                                title: 'Success!',
                                                type: 'success'
                                            }, function(isConfirm) {
                                                if (isConfirm) {
                                                       $("#subDept_rowmd"+code).remove();
                                                }
                                            });
                                      }

                                      if(jxState.status == 500){
                                        swal('Error!',resp,'error');
                                      }                                     
                                 },error:function(e){
                                        console.log(e)
                                 }

                               });
                    } else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });

        }


        $("#btn_website_connect_md").on('click', function() {
            if ($("#website_md").val() == '') {
                swal('Error!', 'Select website name field is requried.', 'error');
            } else {
                websiteConnection($("#depart_webconn_md").val(), $("#website_md").val(), 'link');
            }
        });

        function linkWebsite(departId) {
            $("#department-website-connect-modal").modal('show');
            $("#website_md").val('').change();
            $("#depart_webconn_md").val(departId);
        }

        function unLinkWebsite(departId) {
            swal({
                    title: "Are you sure?",
                    text: "This department will be unlink from the website?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Unlink Website!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        websiteConnection(departId, '', 'unlink');
                    } else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });

        }

        function websiteConnection(departId, website, stcode) {
            $.ajax({
                url: "{{ route('department_website_connect') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    department: departId,
                    website_id: website,
                    status_code: stcode
                },
                dataType: 'json',
                success: function(resp, textStatus, jqXHR) {
                    console.log(resp);
                    console.log(jqXHR);
                    
                    if (jqXHR.status == 200) {
                        swal({
                                title: "Operation Performed",
                                text: "Successfully!",
                                type: "success"
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    if (website != '') {
                                        $("#department-website-connect-modal").modal("hide");
                                    }
                                    window.location = "{{ url('/invent_dept') }}";
                                }
                            });
                    }
                }

            })
        }

        function readURL(input, id) {
            if (input.files && input.files[0]) {
                var file = input.files[0];

                // Check if the file size is greater than 1MB (1,048,576 bytes)
                if (file.size > 1048576) {
                    swal('Error! File Size', 'File size must be less than 1MB.', 'error');
                    input.value = ''; // Clear the input
                    // $('#' + id).attr('src', ''); // Clear the image preview if any
                    $("#" + input.id).focus();
                    $("#" + input.id + '_alert').text('File size must be less than 1MB.');
                    return; // Exit the function
                }

                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + id).attr('src', e.target.result);
                }

                reader.readAsDataURL(file);
            }
        }
        // function readURL(input,id) {
        // 	  if (input.files && input.files[0]) {
        // 		var reader = new FileReader();

        // 		reader.onload = function(e) {
        // 		  $('#'+id).attr('src', e.target.result);
        // 		}

        // 		reader.readAsDataURL(input.files[0]);
        // 	}
        // }
        $("#subdepartBanner_add").change(function() {
            readURL(this, 'previewBanner_sbmd');
        });  
        
        $("#subdepartMobileBanner_add").change(function() {
            readURL(this, 'previewMobileBanner_sbmd');
        }); 

        $("#bannerImage_md").change(function() {
            readURL(this, 'previewDepartBannerImage_md');
        });

        $("#mobileBannerImage_md").change(function() {
            readURL(this, 'previewDepartMobileBannerImage_md');
        });        
        
        $("#departImage_md").change(function() {
            readURL(this, 'previewImg');
        });

        $("#subdepartImage_add").change(function() {
            readURL(this, 'previewImg_sbmd');
        });


        // $("#btn_clear").on('click',function(){
        //     $("#deptform")[0].reset();
        //     $("#subdpt").tagsinput('removeAll');
        // });


        //  $("#btn_save").on('click',function(event){


        $("#btn_update").on('click', function() {
            var maxSize = 1048576;
            if ($("#udeptname").val() == "") {
                $("#udeptname_alert").html('Department name is required.').addClass('text-danger');
            } else {
                $("#udeptname_alert").html('').removeClass('text-danger');
                $.ajax({
                    url: '{{ route('invent_deptup') }}',
                    type: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: $("#uhidd_id").val(),
                        depart: $("#udeptname").val()
                    },
                    dataType: "json",
                    success: function(r) {

                        if (r.state == 1) {
                            if (r.contrl != "") {
                                $("#" + r.contrl).focus();
                            }

                            swal_alert('Alert!', r.msg, 'error', false);
                            $("#udeptname_alert").html(r.msg).addClass('text-danger');

                        } else {
                            swal_alert('Success!', r.msg, 'success', false);

                        }
                    }
                });

            }

        });

        function edit_record(id, name) {

            $.ajax({
                url: 'invent_dept' + '/' + id + '/edit',
                type: "GET",
                dataType: "json",
                success: function(r) {
                    if (r != 0) {
                        $("#depat-modal").modal("show");
                        $("#udeptname").val(r[0].deptname);
                        $("#uhidd_id").val(id);
                        $(".sb_tble tbody").empty();
                        for (var s = 0; s < r.length; s++) {
                            $(".sb_tble tbody").append(
                                "<tr>" +
                                "<td><input type='text' value='" + r[s].sbname +
                                "' class='form-control' id='tbx_" + r[s].sb_id + "'/>" +
                                "<div class='form-control-feedback text-danger' id='tbx_" + r[s].sb_id +
                                "_alert'></div>" + "</td>" +
                                "<td class='action-icon'><i onclick='update(" + r[s].sb_id +
                                ")' class='icofont icofont-ui-edit text-primary' data-toggle='tooltip' data-placement='top' title='' data-original-title='Update'></i></td>" +
                                "</tr>"
                            );
                        }


                    } else {


                    }
                }

            });
        }



        function update(id, dept) {
            let process = true;
            let code    = $("#department_code").val();
            let subdepartImage       = $('#sdbptImg' + id).length ? $('#sdbptImg' + id)[0] : null;
            let subdepartBannerImage = $('#sdbptbannerImg' + id).length ? $('#sdbptbannerImg' + id)[0] : null;
            let subdepartMobileBanner = $('#mobile_banner_sdbedit' + id).length ? $('#mobile_banner_sdbedit' + id)[0] : null;

            if (code == $("#code_" + id).val() && $("#code_" + id).val() != '') {
                swal_alert('Alert!', "Cannot use the main department code.", 'error', false);
                process = false;
            } else if ($("#tbx_" + id).val() == "") {
                $("#tbx_" + id + "_alert").html('Sub-Department name is required.').addClass('text-danger');
                process = false;
            } else {
                $("#tbx_" + id + "_alert").html('').removeClass('text-danger');

                var formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr(
                    'content')); // Updated way of fetching CSRF token
                formData.append('sdepart', $("#tbx_" + id).val());
                formData.append('dept', dept);
                formData.append('code', $("#code_" + id).val());
                formData.append('id', id);
            
               if(subdepartImage != null){ 
                if (subdepartImage.files.length != 0 && subdepartImage.files[0].size <= 1048576) {
                    formData.append('subdepartImage', subdepartImage.files[0]);
                }
               }
           
               if(subdepartBannerImage  != null){  
                if (subdepartBannerImage.files.length != 0 && subdepartBannerImage.files[0].size <= 1048576) {
                    formData.append('subdepartBannerImage', subdepartBannerImage.files[0]);
                }
               }

               if(subdepartMobileBanner  != null){  
                if (subdepartMobileBanner.files.length != 0 && subdepartMobileBanner.files[0].size <= 1048576) {
                    formData.append('subdepartMobileBanner', subdepartMobileBanner.files[0]);
                }
               }
               
                // sdbptbannerImg

                if (process) {
                    $.ajax({
                        url: "{{ route('invent_sb_deptup') }}",
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(r) {
                            console.log(r);
                            if (r.state == 1) {
                                if (r.contrl != "") {
                                    $("#" + r.contrl).focus();
                                }

                                swal_alert('Alert!', r.msg, 'error', false);
                                $("#tbx_" + id + "_alert").text(r.msg).addClass('text-danger');
                            } else {
                                if ($("#tbx_" + id + "_alert").hasClass('text-danger')) {
                                    $("#tbx_" + id + "_alert").removeClass('text-danger').text('');
                                }

                                swal_alert('Success!', r.msg, 'success', false);
                            }
                        }
                    });
                }
            }
        }


        // 		$.ajax({
        // 			url:'{{-- route("invent_sb_deptup") --}}',
        // 			type:"POST",
        // 			data:{_token:"{{-- csrf_token() --}}",id:id,sdepart:$("#tbx_"+id).val(),dept:dept,code:$("#code_"+id).val()},
        // 			dataType:"json",
        // 			success:function(r){

        // 			 if(r.state == 1){
        // 				if(r.contrl != ""){
        // 				 $("#"+r.contrl).focus();

        // 				}

        // 				 swal_alert('Alert!',r.msg,'error',false);
        // 				 $("#tbx_"+id+"_alert").html(r.msg).addClass('text-danger');

        // 			 }else {
        // 				swal_alert('Success!',r.msg,'success',false);
        // 			 }
        // 		   }
        // 		}); 	
        // }

        // }



        function swal_alert(title, msg, type, mode) {

            swal({
                title: title,
                text: msg,
                type: type
            }, function(isConfirm) {
                if (isConfirm) {
                    if (mode == true) {
                        window.location = "{{ route('invent_dept.index') }}";
                    }
                }
            });
        }


        function editdepart(departid) {
            $("#departImage_md_alert,#bannerImage_md_alert").text('');

            if ($("#websiteMode_mdept"+departid).val() == 1) {
                $("#showWebsite_md").trigger('click');
            }

            if ($("#depatSection_mdept"+departid).val() != '') {
                $("#sections_md").val($("#depatSection_mdept"+departid).val()).trigger('change');
            }            

            $('#depart').val($("#deptName_mdept"+departid).val());
            $('#departid').val(departid);
            $('#editcode').val($("#deptCode_mdept"+departid).val());
            $('#webdeptname_md').val($("#websiteDeptName_mdept"+departid).val());
            

            $("#previewImg").attr('src', $("#img-tble-" + departid).attr('src'));
            if ($("#desktopBanner_mdept"+departid).val() != '') {
                $("#previewDepartBannerImage_md").attr('src', location.origin + '/storage/images/department/' +
                $("#desktopBanner_mdept"+departid).val());
            }

            if($("#mobileBanner_mdept"+departid).val() != ''){
                
                $("#previewDepartMobileBannerImage_md").attr('src', location.origin + '/storage/images/department/' +
                $("#mobileBanner_mdept"+departid).val());
            }

            $("#depart-modal").modal("show");
        }

        function editsubdepart(departcode, departid, departname) {
            //alert()
            $.ajax({
                url: "{{ url('/getsubdepart') }}",
                type: "GET",
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    departid: departid,
                },
                success: function(r) {
                    console.log(r)
                    $("#subdepart-modal").modal("show");
                    $("#depart_modal_name").html(departname);
                    $("#department_code").val(departcode);

                    $(".sb_tble tbody").empty();
                    for (var s = 0; s < r.length; s++) {

                        let imageColumn = "<td id='imgCell_md" + r[s].sub_department_id +
                            "'><div><input type='file' name='sdbptImg' class='d-none' id='sdbptImg" + r[s]
                            .sub_department_id + "'> <i id='btn_selectImg" + r[s].sub_department_id +
                            "' class='icofont icofont-upload text-success icofont-3x' onclick='selectImg(" + r[
                                s].sub_department_id + ")'></i></div></td>";

                        let bannerColumn = "<td id='bannerCell_md" + r[s].sub_department_id +
                            "'><div><input type='file' name='sdbptbannerImg' class='d-none' id='sdbptbannerImg" + r[s]
                            .sub_department_id + "'> <i id='btn_selectbannerImg" + r[s].sub_department_id +
                            "' class='icofont icofont-upload text-success icofont-3x' onclick='selectbannerImg(" + r[
                                s].sub_department_id + ")'></i></div></td>";    
                                
                        let mobileBannerColumn = "<td id='mobileBannerCell_md" + r[s].sub_department_id +
                            "'><div><input type='file' name='mobile_banner_sdbedit' class='d-none' id='mobile_banner_sdbedit" + r[s]
                            .sub_department_id + "'> <i id='btn_selectMobileBanner" + r[s].sub_department_id +
                            "' class='icofont icofont-upload text-success icofont-3x' onclick='selectMobilebanner(" + r[
                                s].sub_department_id + ")'></i></div></td>";                                

                        if (r[s].image != null && r[s].image != '') {
                            imageColumn = "<td id='imgCell_md" + r[s].sub_department_id + "'><div><img src='" +
                                location.origin + "/storage/images/department/" + r[s].image +
                                "' width='64' height='64'/><i class='icofont icofont-close text-danger' onclick='removeImgCell(" +
                                r[s].sub_department_id + ")'></i></div></td>";
                        }

                        if (r[s].banner != null && r[s].banner != '') {
                            bannerColumn = "<td id='bannerCell_md" + r[s].sub_department_id + "'><div><img src='" +
                                location.origin + "/storage/images/department/" + r[s].banner +
                                "' width='64' height='64'/><i class='icofont icofont-close text-danger' onclick='removeBannerImgCell(" +
                                r[s].sub_department_id + ")'></i></div></td>";
                        }

 
                        if (r[s].mobile_banner != null && r[s].mobile_banner != '') {
                            mobileBannerColumn = "<td id='mobileBannerCell_md" + r[s].sub_department_id + "'><div><img src='" +
                                location.origin + "/storage/images/department/" + r[s].mobile_banner +
                                "' width='64' height='64'/><i class='icofont icofont-close text-danger' onclick='removeMobileBannerCell(" +
                                r[s].sub_department_id + ")'></i></div></td>";
                        }                       

                        $(".sb_tble tbody").append(
                            "<tr id='subDept_rowmd"+r[s]
                            .sub_department_id+"'>" +
                            "<td><input type='text' value='" + (r[s].code != null ? r[s].code : '') +
                            "' placeholder='Department code' class='form-control' id='code_" + r[s]
                            .sub_department_id + "'/>" +
                            "<td><input type='text' value='" + r[s].sub_depart_name +
                            "' placeholder='Sub department name' class='form-control' id='tbx_" + r[s]
                            .sub_department_id + "'/>" +
                            "<div class='form-control-feedback text-danger' id='tbx_" + r[s]
                            .sub_department_id + "_alert'></div>" + "</td>" +
                            "<td><input type='text' value='" + (r[s].website_sub_department_name != null ?
                                r[s].website_sub_department_name : '') +
                            "' placeholder='Show website department name' class='form-control' id='tbxwb_" +
                            r[s].sub_department_id + "'/>" +
                            "<div class='form-control-feedback text-danger' id='tbxwb_" + r[s]
                            .sub_department_id + "_alert'></div>" + "</td>" +
                            imageColumn + bannerColumn + mobileBannerColumn +
                            "<td class='action-icon'><button type='button' onclick='update(" + r[s].sub_department_id + "," +
                            departid + ")' class='btn btn-primary m-r-1'> Update</button>"+
                            "<button type='button' onclick='deleteSubDepart("+r[s].sub_department_id +")' class='btn btn-danger'> Remove</button>"+
                            "</td>" +
                            "</tr>"
                        );
                        //   $(".sb_tble tbody").append(
                        //       "<tr>" +
                        //         "<td><input type='text' value='"+r[s].code +"' class='form-control' id='code_"+r[s].sub_department_id+"'/>"+
                        //         "<td><input type='text' value='"+r[s].sub_depart_name +"' class='form-control' id='tbx_"+r[s].sub_department_id+"'/>"+
                        //         "<div class='form-control-feedback text-danger' id='tbx_"+r[s].sub_department_id+"_alert'></div>"+"</td>"+
                        //         "<td class='action-icon'><i onclick='update("+r[s].sub_department_id+","+departid+")' class='btn btn-primary'> Update</i></td>"+
                        //       "</tr>"
                        //       );


                    }
                }

            });

        }

        function removeImgCell(id) {

            $("#imgCell_md" + id).empty();
            $("#imgCell_md" + id).append('<input type="file" name="sdbptImg" class="d-none" id="sdbptImg' + id +
                '"> <i id="btn_selectImg' + id +
                '" class="icofont icofont-upload text-success icofont-3x" onclick="selectImg(' + id + ')"></i>');
        }

        function removeBannerImgCell(id) {

          $("#bannerCell_md" + id).empty();
          $("#bannerCell_md" + id).append('<input type="file" name="sdbptbannerImg" class="d-none" id="sdbptbannerImg' + id +
              '"> <i id="btn_selectbannerImg' + id +
              '" class="icofont icofont-upload text-success icofont-3x" onclick="selectbannerImg(' + id + ')"></i>');
        }    

        function removeMobileBannerCell(id) {

            $("#mobileBannerCell_md" + id).empty();
            $("#mobileBannerCell_md" + id).append('<input type="file" name="sdbptbannerImg" class="d-none" id="mobile_banner_sdbedit' + id +
                '"> <i id="btn_selectMobileBanner' + id +
                '" class="icofont icofont-upload text-success icofont-3x" onclick="selectMobilebanner(' + id + ')"></i>');
        }  
        

        let imgdId = null;
        let bannerImgId = null;
        let mobileBannerId = null;

        function selectImg(id) {
            imgdId = id;
            $("#sdbptImg" + id).trigger('click');
            fileGet_sbd_md(id)
        }

        function fileGet_sbd_md(id) {
            $("#sdbptImg" + id).on('change', function() {
                $("#btn_selectImg" + id).remove();
                $("#imgCell_md" + id).append('<img src="" width="64" height="64" id="imgPreviewsdbpt' + id +
                    '"> <i class="icofont icofont-close text-danger" onclick="removeImgCell(' + id + ')"></i>');
                readURL(this, "imgPreviewsdbpt" + id);
            })
        }

        function selectbannerImg(id) {
          bannerImgId = id;
            $("#sdbptbannerImg" + id).trigger('click');
            bannerImgGet_sbd_md(id)
        }        

        function bannerImgGet_sbd_md(id) {
            $("#sdbptbannerImg" + id).on('change', function() {
                $("#btn_selectbannerImg" + id).remove();
                $("#bannerCell_md" + id).append('<img src="" width="64" height="64" id="bannerimgPreviewsdbpt' + id +
                    '"> <i class="icofont icofont-close text-danger" onclick="removeBannerImgCell(' + id + ')"></i>');
                readURL(this, "bannerimgPreviewsdbpt" + id);
            })
        }

        function selectMobilebanner(id) {
            mobileBannerId = id;
            $("#mobile_banner_sdbedit" + id).trigger('click');
            mobileBannerGet_sbd_md(id)
        } 
        
        function mobileBannerGet_sbd_md(id) {
            $("#mobile_banner_sdbedit" + id).on('change', function() {
                $("#btn_selectMobileBanner" + id).remove();
                $("#mobileBannerCell_md" + id).append('<img src="" width="64" height="64" id="mobilebannerPreviewsdbpt' + id +
                    '"> <i class="icofont icofont-close text-danger" onclick="removeMobileBannerCell(' + id + ')"></i>');
                readURL(this, "mobilebannerPreviewsdbpt" + id);
            })
        }        

        function updatedepart() {
            var form = document.getElementById('editDepartmentForm');
            var formData = new FormData(form);
            var process = true;
            if ($("#udeptname").val() == "") {
                $("#udeptname_alert").html('Department name is required.').addClass('text-danger');
                process = false;
            }

            if (process) {
                $.ajax({
                    url: "{{ url('/updatedepart') }}",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {
                        if (resp.state == 1) {
                            swal_alert('Alert!', resp.msg, 'error', false);
                        } else {
                            // if(resp != 0){
                            swal({
                                    title: "Operation Performed",
                                    text: "Department Updated Successfully!",
                                    type: "success"
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        $("#depart-modal").modal("hide");
                                        window.location = "{{ url('/invent_dept') }}";
                                    }
                                });
                        }
                    }
                });
            }
        }



        function addsubdepart(id) {
            $("#subdepartcode").val('');
            $('#subdepartname').val('');
            $('#subdepartImage_add').val('');
            $("#subdepartBanner_add").val('');
            $("#subdepartMobileBanner_add").val('');
            $('#departmodal').val(id).change();
            $("#subdepartment-modal").modal("show");
        }

        function deletedepart(id) {
            swal({
                    title: "Are you sure?",
                    text: "Department and its relative sub-department will also be delete !!!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Active it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: "{{ url('deletedepartment') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id
                            },
                            success: function(resp) {

                                if (resp.status == 200) {
                                    swal({
                                        title: "Deactivated",
                                        text: "Department deactivated Successfully .",
                                        type: "success"
                                    }, function(isConfirm) {
                                        if (isConfirm) {
                                            window.location = "{{ url('invent_dept') }}";
                                        }
                                    });
                                }
                            }

                        });

                    } else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });
        }

        function insertsubdepart() {

            if ($('#subdepartname').val() == '') {
                $('#subdepartname').focus();
                $('#subdepartname_alert_md').text('field is required');
            } else {

                var formData = new FormData();

                formData.append('_token', '{{ csrf_token() }}');
                formData.append('departid', $("#departmodal").val());
                formData.append('code', $("#subdepartcode").val());
                formData.append('subdepart', $("#subdepartname").val());
                formData.append('websubdepart', $("#websubdepartname").val())

                if ($('#subdepartImage_add')[0].files.length != 0) {
                    formData.append('subdepartImage', $('#subdepartImage_add')[0].files[0]);
                }

                if ($('#subdepartBanner_add')[0].files.length != 0) {
                    formData.append('subdepartBanner', $('#subdepartBanner_add')[0].files[0]);
                }

                if ($('#subdepartMobileBanner_add')[0].files.length != 0) {
                    formData.append('mobile_banner_sbdepart', $('#subdepartMobileBanner_add')[0].files[0]);
                }

                $.ajax({
                    url: "{{ url('/addsubdepart') }}",
                    type: 'POST',
                    data: formData,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(resp) {

                        if (resp != 0) {
                            swal({
                                    title: "Operation Performed",
                                    text: "Sub Department Added Successfully!",
                                    type: "success"
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        $("#subdepart-modal").modal("hide");
                                        window.location = "{{ url('/invent_dept') }}";
                                    }
                                });
                        } else {
                            swal({
                                title: "Already exsist",
                                text: "Particular Sub Department Already exsist!",
                                type: "warning"
                            });
                            $("#subdepart-modal").modal("hide");

                        }
                    }

                });
            }

        }

        // $("#parent").on('change', function() {

        //     if ($(this).val() != '') {
        //         $("#showWebsite").trigger('click');
        //     }

        // });

        // $("#showWebsite").on('click', function() {

        //     if ($(this).is(':checked') == true) {
        //         $("#parent").val('').change();
        //         if ($("#website-module").hasClass('d-none')) {
        //             $("#website-module").removeClass('d-none');
        //         }


        //         if ($("#banner-imageBox").hasClass('d-none')) {
        //             $("#banner-imageBox").removeClass('d-none');
        //         }
        //     }

        //     if ($(this).is(':checked') == false) {
        //         if (!$("#website-module").hasClass('d-none')) {
        //             $("#website-module").addClass('d-none');
        //         }

        //         if (!$("#banner-imageBox").hasClass('d-none')) {
        //             $("#banner-imageBox").addClass('d-none');
        //         }
        //     }
        // });

        $("#showWebsite_md").on('click', function() {

            if ($(this).is(':checked') == true) {
                if ($("#website-module_md").hasClass('d-none')) {
                    $("#website-module_md").removeClass('d-none');
                }


                if ($("#banner-imageBox_md").hasClass('d-none')) {
                    $("#banner-imageBox_md").removeClass('d-none');
                }

                if ($("#mobilebanner-imageBox_md").hasClass('d-none')) {
                    $("#mobilebanner-imageBox_md").removeClass('d-none');
                }                
                
            }

            if ($(this).is(':checked') == false) {
                if (!$("#website-module_md").hasClass('d-none')) {
                    $("#website-module_md").addClass('d-none');
                }

                if (!$("#banner-imageBox_md").hasClass('d-none')) {
                    $("#banner-imageBox_md").addClass('d-none');
                }

                if (!$("#mobilebanner-imageBox_md").hasClass('d-none')) {
                    $("#mobilebanner-imageBox_md").addClass('d-none');
                }                
            }
        });

$("#showWebsite_sbdept_md").on('click', function() {

if ($(this).is(':checked') == true) {
    if ($("#website_module_bannersbdept_md").hasClass('d-none')) {
        $("#website_module_bannersbdept_md").removeClass('d-none');
    }
    
    if ($("#website_module_mobileBannersbdept_md").hasClass('d-none')) {
        $("#website_module_mobileBannersbdept_md").removeClass('d-none');
    }

    if ($("#website_module_sbdept_md").hasClass('d-none')) {
        $("#website_module_sbdept_md").removeClass('d-none');
    }
}

if ($(this).is(':checked') == false) {
    if (!$("#website_module_bannersbdept_md").hasClass('d-none')) {
        $("#website_module_bannersbdept_md").addClass('d-none');
    }

    if (!$("#website_module_mobileBannersbdept_md").hasClass('d-none')) {
        $("#website_module_mobileBannersbdept_md").addClass('d-none');
    }    

    if (!$("#website_module_sbdept_md").hasClass('d-none')) {
        $("#website_module_sbdept_md").addClass('d-none');
    }
}
});
        @if (old('metadescript'))
            $("#metadescript").val('{{ old('metadescript') }}');
        @endif
    </script>

@endsection
