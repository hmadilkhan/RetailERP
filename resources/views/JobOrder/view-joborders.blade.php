@extends('layouts.master-layout')

@section('title','Job Orders')

@section('breadcrumtitle','View Job Orders')

@section('navjoborder','active')

@section('content')


    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Job Order List</h5>
                <a href="{{ url('/create-job') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Job Order
                </a>
            </div>

            <div class="card-block">
                <div class="rkmd-checkbox checkbox-rotate">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkactive" class="mainchk">
                        <span class="checkbox"></span>
                    </label>
                    <div class="captions">Show In-Active Job Orders</div>
                </div>
                <br/>
                <br/>
				@if(auth()->user()->company->application_id == 2)
                <div class="project-table">
                    <table id="tbljoborders" class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Job Order Name</th>
                            <th>DineIn Cost</th>
                            <th>Takeaway & Delivery Cost</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($result)
                            @foreach($result as $value)
                                <tr>

                                    <td>{{$value->product_name}}</td>
                                    <td>{{$value->DineInCost + $value->infrastructure_cost}}</td>
                                    <td>{{$value->TakedelCost + $value->infrastructure_cost}}</td>
                                    <td class="action-icon">

                                        <a href="{{ url('/getdetails') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="p-r-10 f-18 text-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>
                                        &nbsp;

                                    <!--      <i class="icofont icofont-rotation f-20  text-info"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Repeat"></i>&nbsp;

                                <i class="icofont icofont-vehicle-delivery-van f-20  text-success" data-id="{{ $value->recipy_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delivery"></i>&nbsp; -->

                                        <a href="{{ url('/edit-job') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="p-r-10 f-18 text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                        <a class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top'  data-original-title='Delete' onclick='inactive("{{ $value->recipy_id }}")' ><i class='icofont icofont-ui-delete'></i></a>

                                    <!--  <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->recipy_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i> -->
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
				@else
				<div class="project-table">
                    <table id="tbljoborders" class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Job Order Name</th>
                            <th>ingredient Cost</th>
                            <th>Packing Cost</th>
                            <th>Infrastructure Cost</th>
                            <th>Total Cost</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($result)
                            @foreach($result as $value)
                                <tr>

                                    <td>{{$value->product_name}}</td>
                                    <td>{{$value->ingredients_cost}}</td>
                                    <td>{{$value->material_cost}}</td>
                                    <td>{{$value->infrastructure_cost}}</td>
                                    <td>{{$value->total_cost}}</td>

                                    <td class="action-icon">

                                        <a href="{{ url('/getdetails') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="p-r-10 f-18 text-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>
                                        &nbsp;

                                    <!--      <i class="icofont icofont-rotation f-20  text-info"  data-toggle="tooltip" data-placement="top" title="" data-original-title="Repeat"></i>&nbsp;

                                <i class="icofont icofont-vehicle-delivery-van f-20  text-success" data-id="{{ $value->recipy_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delivery"></i>&nbsp; -->

                                        <a href="{{ url('/edit-job') }}/{{ Crypt::encrypt($value->recipy_id) }}" class="p-r-10 f-18 text-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                        <a class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top'  data-original-title='Delete' onclick='inactive("{{ $value->recipy_id }}")' ><i class='icofont icofont-ui-delete'></i></a>

                                    <!--  <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->recipy_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i> -->
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
				@endif
            </div>
        </div>
    </section>
@endsection


@section('scriptcode_three')

    <script type="text/javascript">



        $('.table').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: false,
            language: {
                search:'',
                searchPlaceholder: 'Search Job Order',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        //Alert confirm
        $('.alert-confirm').on('click',function(){
            var id= $(this).data("id");

            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to recover this Job Order!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "delete it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{url('/deletejoborder')}}",
                            type: 'GET',
                            data:{_token:"{{ csrf_token() }}",
                                jobid:id,
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Job Order Removed Successfully!",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('/joborder')}}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your JobOrder is safe :)", "error");
                    }
                });
        });

        function onDelivery(jobid,qty,itemid,received){


            swal({
                    title: "Confirmation Message?",
                    text: "Do You want to Received this product!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-info",
                    confirmButtonText: "Yes plx!",
                    cancelButtonText: "Cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){

                        swal({
                            title: "Shipment Amount!",
                            text: "Enter Shipment Amount!:",
                            type: "input",
                            showCancelButton: true,
                            closeOnConfirm: false,
                            inputPlaceholder: "Should be greater than 0"
                        }, function (inputValue) {
                            if(inputValue > 0){
                                received_finished_goods(jobid,itemid,inputValue,received,qty);
                            }
                            else{
                                swal_alert("Error Message !","Please input some value","error",false);
                            }
                        });
                    }
                });
        }

        function received_finished_goods(jobid,itemid,recivedqty,received,qty)
        {
            var totalQty = qty - received;
            if(recivedqty == 0)
            {
                swal_alert("Error Message !","Cannot Received Zero","error",false);
            }
            else if (recivedqty > totalQty)
            {
                swal_alert("Error Message !","You can only received "+totalQty,"error",false);
            }
            else
            {
                $.ajax({
                    url: "{{url('/received-product')}}",
                    type: 'POST',
                    data:{_token:"{{ csrf_token() }}",
                        jobid:jobid,
                        itemid:itemid,
                        recivedqty:recivedqty,
                    },
                    success:function(resp){
                        if(resp == 1){
                            swal({
                                title: "Success",
                                text: "Received Successfully",
                                type: "success"
                            },function(isConfirm){
                                if(isConfirm){
                                    window.location="{{url('/joborder')}}";
                                }
                            });
                        }
                    }

                });
            }
        }




        function swal_alert(title,msg,type,mode){

            swal({
                title: title,
                text: msg,
                type: type
            },function(isConfirm){
                if(isConfirm){
                    if(mode === true){
                        window.location = "{{url('/view-purchases')}}";
                    }
                }
            });
        }


        $('#chkactive').change(function(){
            if (this.checked) {
                $.ajax({
                    url: "{{url('/joborder-inactive')}}",
                    type: 'GET',
                    dataType:"json",
                    data:{_token:"{{ csrf_token() }}",
                    },
                    success:function(result){
                        if(result){
                            $("#tbljoborders tbody").empty();
                            for(var count =0;count < result.length; count++){

                                $("#tbljoborders tbody").append(
                                    "<tr>" +
                                    "<td>"+result[count].product_name+"</td>" +
                                    "<td>"+result[count].ingredients_cost+"</td>" +
                                    "<td>"+result[count].material_cost+"</td>" +
                                    "<td>"+result[count].infrastructure_cost+"</td>" +
                                    "<td>"+result[count].total_cost+"</td>" +
                                    "<td class='action-icon'><a class='p-r-10 f-18 text-primary' data-toggle='tooltip' data-placement='top'  data-original-title='View' onclick='show("+result[count].recipy_id+")' ><i class='icofont icofont-eye-alt'></i></a>&nbsp;<a class='p-r-10 f-18 text-success' data-toggle='tooltip' data-placement='top'  data-original-title='Reactive' onclick='reactive("+result[count].recipy_id+","+result[count].product_id+")' ><i class='icofont icofont-check-circled'></i></a></td>"+
                                    "</tr>"
                //bahi agar edit laganay ae ho to niche rakha ha

                                );
                            }

                        }
                    }
                });
            }
            else{
                window.location="{{ url('/joborder') }}";


            }
        });
        // <a class='p-r-10 f-18 text-warning' data-toggle='tooltip' data-placement='top'  data-original-title='Edit' onclick='edit("+result[count].recipy_id+")' ><i class='icofont icofont-ui-edit'></i></a>
        
        function show(recipyid) {
            window.location="{{ url('/getdetails') }}"+"/"+recipyid;
        }
        function edit(recipyid) {
            window.location="{{ url('/edit-job') }}"+"/"+recipyid;
        }
        
        function reactive(recipyid,productid) {

            $.ajax({
                url: "{{url('/reactiverecipy')}}",
                type: 'POST',
                dataType:"json",
                data:{_token:"{{ csrf_token() }}",
                    recipyid:recipyid,
                    productid:productid,
                },
                success:function(resp){
                    if(resp == 1){
                        swal({
                            title: "Success",
                            text: "Job Order Re-active Successfully",
                            type: "success"
                        },function(isConfirm){
                            if(isConfirm){
                                window.location="{{url('/joborder')}}";
                            }
                        });
                    }
                    else{
                        swal({
                            title: "Error Message",
                            text: "Job Order Already Active, You can't active two job orders at same time!",
                            type: "warning"
                        },function(isConfirm){
                            if(isConfirm){
                                window.location="{{url('/joborder')}}";
                            }
                        });
                    }
                }
            });

        }

        function inactive(recipyid) {

            $.ajax({
                url: "{{url('/inactiverecipy')}}",
                type: 'POST',
                dataType:"json",
                data:{_token:"{{ csrf_token() }}",
                    recipyid:recipyid,
                },
                success:function(result){
                    if(result){
                        swal({
                            title: "Success",
                            text: "Job Order In-active Successfully",
                            type: "success"
                        },function(isConfirm){
                            if(isConfirm){
                                window.location="{{url('/joborder')}}";
                            }
                        });
                    }
                }
            });

        }
    </script>

@endsection
