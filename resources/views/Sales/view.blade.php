@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','Add Purchase Order')

@section('navpurchase','active')
@section('nav_addpurchase','active')

@section('content')
    <section class="panels-wells">
        <div class="card" style="margin-left: 40px;margin-right: 40px;">
            <div class="invoice-contact">
                <div class="col-md-8">
                    <div class="invoice-box row">
                        <div class="col-sm-4 text-center">
                            <!--  <h1>A</h1> -->

                            <center> <img width="200" height="200" class="media-object  img-circle" src="{{ asset('public/assets/images/company/'.(($company[0]->logo) ? $company[0]->logo : 'placeholder.jpg')) }}" alt="Generic placeholder image"/></center>

                        </div>
                        <div class="col-sm-8">
                            <table class="table table-responsive invoice-table">
                                <tbody>
                                <tr>
                                    <td>{{$company[0]->name}}</td>
                                </tr>
                                <tr>
                                    <td>{{date("d-m-Y")}}</td>
                                </tr>
                                <tr>
                                    <td><a href="mailto:demo@gmail.com" target="_top">{{$company[0]->email}}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{$company[0]->address}}</td>
                                </tr>
                                <tr>
                                    <td>{{$company[0]->ptcl_contact}}</td>
                                </tr>
                                <!--  <tr>

                                    <td><a href="#" target="_blank">www.demo.com</a>
                                    </td>
                                 </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- update-status-po -->
                <!-- $general[0]->purchase_id -->
                <div class="col-md-3">
                    <div class="row m-t-150 ">

                    </div>
                </div>
            </div>
            <div class="card-block">
                <div class="row invoive-info">
                    <div class="col-md-4 col-xs-12 invoice-client-info">
                        <h6>Customer Information :</h6>
                        <h6>{{$customers[0]->name}}</h6>
                        <p>{{$customers[0]->address}}.</p>
                        <p>{{$customers[0]->mobile}}</p>
                        <p>{{$customers[0]->phone}}</p>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <h6>Order Information :</h6>
                        <table class="table table-responsive invoice-table invoice-order">
                            <tbody>
                            <tr>
                                <th>Date :</th>
                                <td>{{date("d F Y",strtotime($receipt[0]->date))}}</td>
                            </tr>
                            <tr>
                                <th>Delivery :</th>
                                <td>{{($receipt[0]->delivery_date ? date("d F Y",strtotime($receipt[0]->delivery_date)) : '')}}</td>
                            </tr>
                            <tr>
                                <th>Status :</th>
                                <td>
                                    @if($receipt[0]->status == 1)
                                        <span class="tag tag-warning"> {{ $receipt[0]->order_status_name}}</span>
                                    @elseif($receipt[0]->status== 2)
                                        <span class="tag tag-success">  {{ $receipt[0]->order_status_name}}</span>
                                    @elseif($receipt[0]->status == 3)
                                        <span class="tag tag-info">  {{ $receipt[0]->order_status_name}}</span>
                                    @elseif($receipt[0]->status == 4)
                                        <span class="tag tag-danger">  {{ $receipt[0]->order_status_name}}</span>
                                    @elseif($receipt[0]->status == 5)
                                        <span class="tag tag-danger">  {{ $receipt[0]->order_status_name}}</span>
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <h6 class="m-b-20">Receipt Number : <span>{{$receipt[0]->receipt_no }}</span></h6>
                        <h6 class="text-uppercase txt-default">Total Due :
                            <span class="txt-default">Rs. {{number_format($receipt[0]->total_amount,2) }}</span>
                        </h6>
                        <h6 class="">Comments : </h6>

                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table invoice-detail-table">
                            <thead>
                            <tr class="thead-default">
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if($receiptDetails)
                                    @foreach($receiptDetails as $value)
                                        <tr>
                                            <td>
                                                <h6>{{$value->item_code." - ".$value->product_name}}</h6>
                                                <p>{{$value->product_description}}</p>
                                            </td>
                                            <td>{{$value->total_qty}}</td>
                                            <td>{{$value->total_amount/$value->total_qty}}</td>
                                            <td>{{$value->total_amount}}</td>
                                        </tr>
                                    @endforeach
                                @endif


                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-responsive invoice-table invoice-total">
                            <tbody>
                            <tr>
                                <th>Sub Total :</th>
                                <td>{{number_format($receiptAccount[0]->total_amount,2)}}</td>
                            </tr>
                            <tr>
                                <th>Sales Tax :</th>
                                <td>{{number_format($receiptAccountSubDetails[0]->sales_tax_amount,2)}}</td>
                            </tr>
                            <tr>
                                <th>Discount :</th>
                                <td>{{number_format($receiptAccountSubDetails[0]->discount_amount,2)}}</td>
                            </tr>
                            <tr>
                                <th>Service Tax :</th>
                                <td>{{number_format($receiptAccountSubDetails[0]->service_tax_amount,2)}}</td>
                            </tr>
                            <tr class="txt-info">
                                <th>
                                    <h5>Total  :</h5></th>
                                <td>
                                    <h5>{{number_format($receiptAccount[0]->total_amount,2)}}</h5></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h6>Terms And Condition :</h6>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            Duis aute irure dolor </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('scriptcode_three')
    <script type="text/javascript" src="{{ asset('public/assets/pages/button-fab.js') }}"></script>
    <script type="text/javascript">

    </script>
@endsection
