@extends('layouts.master-layout')

@section('title','Work Orders')

@section('breadcrumtitle','View Work Orders')

@section('navworkorder','active')

@section('content')

    <section class="panels-wells">
        <div class="card">
            <div class="card-header">

                <h5 class="card-header-text">Work Order Details</h5>
                <h6 class=""><a href="{{ url('/job-order') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>

            </div>
            <div class="card-block">
                <div class="row invoive-info">
                    <div class="col-md-4 invoice-client-info">
                        <h5>Work Order Name</h5>
                        <h6 >{{$sum[0]->joborder_name}}</h6>
                    </div>
                    <div class="col-md-4">
                        <h5>Created on</h5>
                        <h6 class="text-uppercase txt-info">{{date("d F Y",strtotime($sum[0]->created_at))}}</h6>
                    </div>
                    <div class="col-md-4 invoice-client-info">
                        <h5>Order Amount</h5>
                        <h6 >{{$sum[0]->cost}}</h6>
                    </div>
                </div>

                <div class="project-table">
                    <table class="table table-striped nowrap dt-responsive" width="100%">
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Dish Name</th>
                            <th>Order Quantity</th>
                            <th>Order Cost</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if($details)
                            @foreach($details as $value)
                                <tr>
                                    <td class="text-center">
                                        <img width="42" height="42" src="{{ asset('assets/images/products/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                                    </td>
                                    <td>{{$value->product_name}}</td>
                                    <td>{{$value->order_qty}}</td>
                                    <td>{{$value->job_cost}}</td>
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





    </script>

@endsection