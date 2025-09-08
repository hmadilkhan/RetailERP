@extends('layouts.master-layout')

@section('title','Vendor')

@section('breadcrumtitle','View Vendor')
@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Vendors Purchase Orders List</h5><br/>
                <label class="f-18 text-info f-italic">{{$name[0]->vendor_name}}</label>
                <a href="{{ route('vendors.create') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Vendor" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE VENDOR
                </a>

            </div>
            <div class="card-block">
                <table  id="placed" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th>Generation Date</th>
                        <th>PO No</th>
                        <th>Vendor</th>
                        <th>Branch</th>
                        <th>Delivery Date</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($po as $value)
                            <tr >
                                <td>{{$value->order_date }}</td>
                                <td> <a  href="{{route('view',$value->purchase_id)}}">{{$value->po_no }}</a></td>
                                <td>{{$value->vendor_name }}</td>
                                <td>{{$value->branch_name }}</td>
                                <td>{{$value->delivery_date }}</td>
                                <td>{{$value->payment_date }}</td>
                                <td>{{number_format($value->net_amount,2) }}</td>
                                <td>
                                    @if($value->name == "Draft")
                                        <span class="tag tag-default">  {{$value->name }}</span>
                                    @elseif($value->name == "Placed")
                                        <span class="tag tag-success">  {{$value->name }}</span>
                                    @elseif($value->name == "Received")
                                        <span class="tag tag-info">  {{$value->name }}</span>
                                    @elseif($value->name == "Cancelled")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @elseif($value->name == "Partially Received")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @elseif($value->name == "Partially Return")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @elseif($value->name == "Complete Return")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @elseif($value->name == "Partially Received")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @elseif($value->name == "Partial Payment")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @elseif($value->name == "Complete")
                                        <span class="tag tag-success">  {{$value->name }}</span>
                                    @elseif($value->name == "Replacement")
                                        <span class="tag tag-danger">  {{$value->name }}</span>
                                    @endif

                                </td>
                            </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('scriptcode_three')

    <script type="text/javascript">
        $('.table').DataTable({

            bLengthChange: true,
            displayLength: 10,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Search Vendor',
                lengthMenu: '<span></span> _MENU_'

            }

        });
    </script>
@endsection