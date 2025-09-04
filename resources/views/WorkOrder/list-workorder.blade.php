@extends('layouts.master-layout')

@section('title','Work Orders')

@section('breadcrumtitle','View Work Orders')

@section('navworkorder','active')

@section('content')

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Work Order List</h5>
                <a href="{{ url('repeat-job') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE WORK ORDER
                </a>

            </div>
            <div class="card-block">

                <div class="project-table">
                    <table class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Order Name</th>
                            <th>Order Date</th>
                            <th>Order Cost</th>
                            <th>Order Type</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($result)
                            @foreach($result as $value)
                                <tr>

                                    <td>{{$value->job_order_id}}</td>
                                    <td>{{$value->joborder_name}}</td>
                                    <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                                    <td>{{$value->cost}}</td>
                                    <td>{{$value->job_status_name}}</td>

                                    <td class="action-icon">

                                        <a href="{{ url('/getworkorderdetails') }}/{{ Crypt::encrypt($value->job_order_id) }}" class="p-r-10 f-18 text-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>


                                    <!-- <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->job_order_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel"></i> -->
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
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
            ordering: false, // 👈 disables ordering
            language: {
                search:'',
                searchPlaceholder: 'Search Work Order',
                lengthMenu: '<span></span> _MENU_'
            }
        });

        //Alert confirm
        $('.alert-confirm').on('click',function(){
            var id= $(this).data("id");
            alert(id);

            swal({
                    title: "Are you sure?",
                    text: "Your will not be able to reopen this Job Order!",
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
                            url: "{{url('/job-cancel')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,
                            },
                            success:function(resp){
                                console.log(resp);
                                if(resp == 1){
                                    swal({
                                        title: "Cancelled",
                                        text: "Job Cancelled Succesfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{url('job-order')}}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your Job Order is safe :)", "error");
                    }
                });
        });
        function onDelivery(jobid,qty,itemid,received){
            if (qty == received)
            {
                swal_alert("Error Message !","Product Cannot be Received","error",false);
            }
            else{


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
                                title: "Received Amount!",
                                text: "Enter Received Amount!:",
                                type: "input",
                                showCancelButton: true,
                                closeOnConfirm: false,
                                inputPlaceholder: "Should be greater than 0"
                            }, function (inputValue) {
                                if (isNaN(inputValue))
                                {
                                    swal_alert("Error Message !","Input is not in correct Format","error",false);
                                }
                                else if(inputValue < 0){
                                    swal_alert("Error Message !","Negative value is not allowed","error",false);
                                }
                                else if(inputValue > 0){
                                    received_finished_goods(jobid,itemid,inputValue,received,qty);
                                }
                                else{
                                    swal_alert("Error Message !","Input value must be greater than zero","error",false);
                                }
                            });
                        }else
                        {
                            swal("Cancelled", "User Cancelled the Operation :)", "error");
                        }
                    });
            }

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
                                    window.location="{{url('/job-order')}}";
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

    </script>

@endsection