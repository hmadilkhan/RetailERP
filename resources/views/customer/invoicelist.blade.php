@extends('layouts.master-layout')

@section('title','Customer')

@section('navcustomer','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
{{--                <h5 class="f-right"><a href="{{ url('/customers') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>--}}
                <button class="btn btn-success f-right" onclick="window.location = '{{url('customer')}}'"><i class=" text-center icofont icofont-arrow-left m-t-3 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back"></i>Back</button>
                <h1 class="">{{$customer[0]->name}}</h1>

                <h5 class="card-header-text">Customer Receipt Details</h5>

            </div>
            <div class="card-block">
               <div class="project-table">
                            <table  class="table dt-responsive table-striped table-bordered nowrap" width="100%">
                                <thead>
                                <tr>
                                    <th>Order#</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Branch</th>
                                    <th>Terminal</th>
                                    <th>Receipt No</th>
                                    <th>OrderType</th>
                                    <th>Payment Type</th>
{{--                                    <th>Payment Date</th>--}}
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @if($receipts)
                                        @foreach($receipts as $value)
                                            <tr>
                                                <td>{{$value->id}}</td>
                                                <td>{{$value->date}}</td>
                                                <td>{{date("H:i a",strtotime($value->time))}}</td>
                                                <td>{{$value->branch}}</td>
                                                <td>{{$value->terminal_name}}</td>
                                                <td>{{$value->receipt_no}}</td>
                                                <td>{{$value->order_mode}}</td>
                                                <td>{{$value->payment_mode}}</td>
{{--                                                <td>{{$value->date}}</td>--}}
                                                <td>{{number_format($value->total_amount,0)}}</td>
                                                <td>
                                                    <a  onclick="getBill('{{$value->receipt_no}}')" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-print"></i></a>
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
    {{--      MODAL START--}}
    <div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="mod-title" class="modal-title">Receipt Details</h4>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Receipt No :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="receiptno" class="">1234564897978</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Date :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="date" class="">2012-02-12</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Customer Name:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="name" class="">Muhammad Adil Khan</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Contact :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="mobile" class="">0311-1234567</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Order Type:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="type" class="">Take Away</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Status :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="status" class="">Pending</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="tablemodal" class="table " width="100%"  cellspacing="0">
                                <thead>
                                <th width="80%">Product Name</th>
                                <th>Qty</th>
                                <th>Amount</th>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>

                    <hr/>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Total Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="tamount" class="f-right">10000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Advance :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="receive" class="f-right">1000</label>
                        </div>
                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Bal. Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="bal" class="f-right">10000</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--           <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button> -->
                </div>
            </div>
        </div>
    </div>
    {{--      MODAL END--}}
@endsection


@section('scriptcode_three')

    <script type="text/javascript">
        $('.table').DataTable({

            bLengthChange: true,
            displayLength: 50,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Search Customer',
                lengthMenu: '<span></span> _MENU_'

            }

        });

        function getBill(receipt) {
            window.location = "{{url('print/')}}"+"/"+receipt;
            {{--$.ajax({--}}
            {{--    url: "{{url('/get-order-general')}}",--}}
            {{--    type: 'POST',--}}
            {{--    dataType:"json",--}}
            {{--    async : false,--}}
            {{--    data:{_token:"{{ csrf_token() }}",--}}
            {{--        receiptID:receipt,--}}
            {{--    },--}}
            {{--    beforeSend : function(){--}}
            {{--        // console.log("Data is loading");--}}
            {{--    },--}}
            {{--    success:function(result){--}}
            {{--        $('#product-modal').modal("show");--}}
            {{--        $('#receiptno').html(result[0].receipt_no);--}}
            {{--        $('#date').html(result[0].date);--}}
            {{--        $('#name').html(result[0].name);--}}
            {{--        $('#mobile').html(result[0].mobile);--}}
            {{--        $('#type').html(result[0].order_mode);--}}
            {{--        $('#status').html(result[0].order_status_name);--}}

            {{--        if(type == "Take Away")--}}
            {{--        {--}}
            {{--            $('#tamount').html("Rs. "+result[0].total_amount.toLocaleString());--}}
            {{--            $('#receive').html('0');--}}
            {{--            var balance = parseInt(result[0].total_amount) - parseInt(result[0].receive_amount);--}}
            {{--            $('#bal').html('0');--}}
            {{--        }else{--}}
            {{--            $('#tamount').html("Rs. "+result[0].total_amount.toLocaleString());--}}
            {{--            $('#receive').html("Rs. "+result[0].receive_amount.toLocaleString());--}}
            {{--            var balance = parseInt(result[0].total_amount) - parseInt(result[0].receive_amount);--}}
            {{--            $('#bal').html("Rs. "+balance.toLocaleString());--}}
            {{--        }--}}

            {{--        $.ajax({--}}
            {{--            url: "{{url('/get-items-by-receipt')}}",--}}
            {{--            type: 'POST',--}}
            {{--            dataType:"json",--}}
            {{--            data:{_token:"{{ csrf_token() }}",--}}
            {{--                id:result[0].id,--}}
            {{--            },--}}
            {{--            beforeSend : function(){--}}
            {{--                // console.log("Data is loading");--}}
            {{--            },--}}
            {{--            success:function(result){--}}
            {{--                $("#tablemodal tbody").empty();--}}
            {{--                for(var count =0;count < result.length; count++){--}}
            {{--                    $("#tablemodal tbody").append(--}}
            {{--                        "<tr>" +--}}
            {{--                        "<td >"+result[count].product_name+"</td>" +--}}
            {{--                        "<td >"+result[count].total_qty+"</td>" +--}}
            {{--                        "<td '>"+parseInt(result[count].total_amount).toLocaleString()+"</td>" +--}}
            {{--                        "</tr>"--}}
            {{--                    )--}}
            {{--                }--}}
            {{--            }--}}
            {{--        });--}}
            {{--    }--}}
            {{--});--}}
        }
    </script>
@endsection