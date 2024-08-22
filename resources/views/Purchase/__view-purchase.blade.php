@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','View Purchases')
@section('navVendorPO','active')
@section('navpurchase','active')
@section('nav_viewpurchase','active')

@section('content')

 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Purchases List (Draft, Received, Cancelled, Complete, Partial Received)</h5>
{{--         <p>(Draft, Received, Cancelled, Complete, Partial Payment)</p>--}}
         <a href="{{ route('add-purchase') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Create Purshase Order
              </a>
         </div>      
       <div class="card-block">

{{--            <ul class="nav nav-tabs md-tabs " role="tablist">--}}
{{--                    <li class="nav-item">--}}
{{--                       <a class="nav-link active" data-toggle="tab" href="#placed" role="tab">Placed</a>--}}
{{--                       <div class="slide"></div>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                       <a class="nav-link" data-toggle="tab" href="#received" role="tab">Received</a>--}}
{{--                       <div class="slide"></div>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                       <a class="nav-link" data-toggle="tab" href="#partially" role="tab">Partially Received</a>--}}
{{--                       <div class="slide"></div>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                       <a class="nav-link" data-toggle="tab" href="#draft" role="tab">Draft</a>--}}
{{--                       <div class="slide"></div>--}}
{{--                    </li>--}}
{{--                    <li class="nav-item">--}}
{{--                        <a class="nav-link" data-toggle="tab" href="#complete" role="tab">Complete</a>--}}
{{--                        <div class="slide"></div>--}}
{{--                    </li>--}}
{{--            </ul>--}}

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
                   <th>Action</th>
               </tr>
               </thead>
               <tbody>
               @foreach ($po as $value)

                       <tr >
                           <td>{{$value->order_date }}</td>
                           <td><a  href="{{route('view',$value->purchase_id)}}">{{$value->po_no }}</a></td>
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
                           <td class="action-icon">
                               @if($value->name == "Received")
                                   <a  href="{{url('grn-details',$value->purchase_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>
                               @endif
                               <a  href="{{route('view',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'muted disabled'  : 'primary' }} p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>

                               <a  href="{{route('edit',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'warning' : ($value->name == 'Placed' ? 'muted disabled' : 'muted disabled') }}  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>

                               <a  class="text-{{ $value->name == 'Draft' ? 'danger' : 'muted disabled' }} p-r-10 f-18 alert-confirm" data-id="{{ $value->purchase_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>
                           </td>
                       </tr>

               @endforeach
               </tbody>
           </table>


        <div class="tab-content">
{{--          <div class="tab-pane active m-t-10" id="placed" role="tabpanel">--}}
{{--        <table  id="placed" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">--}}
{{--         <thead>--}}
{{--            <tr>--}}
{{--              <th>Generation Date</th>--}}
{{--               <th>PO No</th>--}}
{{--               <th>Vendor</th>--}}
{{--               <th>Branch</th>--}}
{{--               <th>Delivery Date</th>--}}
{{--               <th>Payment Date</th>--}}
{{--               <th>Amount</th>--}}
{{--               <th>Status</th>--}}
{{--               <th>Action</th>--}}
{{--            </tr>--}}
{{--         </thead>--}}
{{--         <tbody>--}}
{{--              @foreach ($po as $value)--}}
{{--                @if($value->name == "Placed")--}}
{{--                 <tr >--}}
{{--                     <td>{{$value->order_date }}</td>--}}
{{--                     <td>{{$value->po_no }}</td>--}}
{{--                     <td>{{$value->vendor_name }}</td>--}}
{{--                     <td>{{$value->branch_name }}</td>--}}
{{--                     <td>{{$value->delivery_date }}</td>--}}
{{--                     <td>{{$value->payment_date }}</td>--}}
{{--                     <td>{{number_format($value->net_amount,2) }}</td>--}}
{{--                     <td>--}}
{{--                      @if($value->name == "Draft")--}}
{{--                        <span class="tag tag-default">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Placed")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Received")--}}
{{--                         <span class="tag tag-info">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Cancelled")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partial Payment")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Replacement")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @endif--}}
{{--                      --}}
{{--                    </td> --}}
{{--                     <td class="action-icon">--}}
{{--                      @if($value->name == "Received")--}}
{{--                        <a  href="{{url('grn-details',$value->purchase_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>--}}
{{--                      @endif--}}
{{--                        <a  href="{{route('view',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'muted disabled'  : 'primary' }} p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>--}}

{{--                       <a  href="{{route('edit',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'warning' : ($value->name == 'Placed' ? 'muted disabled' : 'muted disabled') }}  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>--}}

{{--                        <a  class="text-{{ $value->name == 'Draft' ? 'danger' : 'muted disabled' }} p-r-10 f-18 alert-confirm" data-id="{{ $value->purchase_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>--}}
{{--                     </td>               --}}
{{--                 </tr>--}}
{{--                 @endif--}}
{{--                @endforeach--}}
{{--         </tbody>--}}
{{--         </table>--}}
{{--          </div>--}}
{{--          <div class="tab-pane m-t-10" id="received" role="tabpanel">--}}
{{--            <table  id="received" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">--}}
{{--         <thead>--}}
{{--            <tr>--}}
{{--              <th>Generation Date</th>--}}
{{--               <th>PO No</th>--}}
{{--               <th>Vendor</th>--}}
{{--               <th>Branch</th>--}}
{{--               <th>Delivery Date</th>--}}
{{--               <th>Payment Date</th>--}}
{{--               <th>Amount</th>--}}
{{--               <th>Status</th>--}}
{{--               <th>Action</th>--}}
{{--            </tr>--}}
{{--         </thead>--}}
{{--         <tbody>--}}
{{--              @foreach ($po as $value)--}}
{{--                @if($value->name == "Received")--}}
{{--                 <tr >--}}
{{--                     <td>{{$value->order_date }}</td>--}}
{{--                     <td>{{$value->po_no }}</td>--}}
{{--                     <td>{{$value->vendor_name }}</td>--}}
{{--                     <td>{{$value->branch_name }}</td>--}}
{{--                     <td>{{$value->delivery_date }}</td>--}}
{{--                     <td>{{$value->payment_date }}</td>--}}
{{--                     <td>{{number_format($value->net_amount,2) }}</td>--}}
{{--                     <td>--}}
{{--                      @if($value->name == "Draft")--}}
{{--                        <span class="tag tag-default">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Placed")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Received")--}}
{{--                         <span class="tag tag-info">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Cancelled")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partial Payment")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Replacement")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @endif--}}
{{--                      --}}
{{--                    </td> --}}
{{--                     <td class="action-icon">--}}
{{--                      @if($value->name == "Received")--}}
{{--                        <a  href="{{url('grn-details',$value->purchase_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>--}}
{{--                      @endif--}}
{{--                        <a  href="{{route('view',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'muted disabled'  : 'primary' }} p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>--}}

{{--                       <a  href="{{route('edit',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'warning' : ($value->name == 'Placed' ? 'muted disabled' : 'muted disabled') }}  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>--}}

{{--                        <a  class="text-{{ $value->name == 'Draft' ? 'danger' : 'muted disabled' }} p-r-10 f-18 alert-confirm" data-id="{{ $value->purchase_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>--}}
{{--                     </td>               --}}
{{--                 </tr>--}}
{{--                 @endif--}}
{{--                @endforeach--}}
{{--         </tbody>--}}
{{--         </table>--}}
{{--          </div>--}}
{{--          <div class="tab-pane m-t-10" id="partially" role="tabpanel">--}}
{{--             <table  id="partially" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">--}}
{{--         <thead>--}}
{{--            <tr>--}}
{{--              <th>Generation Date</th>--}}
{{--               <th>PO No</th>--}}
{{--               <th>Vendor</th>--}}
{{--               <th>Branch</th>--}}
{{--               <th>Delivery Date</th>--}}
{{--               <th>Payment Date</th>--}}
{{--               <th>Amount</th>--}}
{{--               <th>Status</th>--}}
{{--               <th>Action</th>--}}
{{--            </tr>--}}
{{--         </thead>--}}
{{--         <tbody>--}}
{{--              @foreach ($po as $value)--}}
{{--                @if($value->name == "Partially Received")--}}
{{--                 <tr >--}}
{{--                     <td>{{$value->order_date }}</td>--}}
{{--                     <td>{{$value->po_no }}</td>--}}
{{--                     <td>{{$value->vendor_name }}</td>--}}
{{--                     <td>{{$value->branch_name }}</td>--}}
{{--                     <td>{{$value->delivery_date }}</td>--}}
{{--                     <td>{{$value->payment_date }}</td>--}}
{{--                     <td>{{number_format($value->net_amount,2) }}</td>--}}
{{--                     <td>--}}
{{--                      @if($value->name == "Draft")--}}
{{--                        <span class="tag tag-default">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Placed")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Received")--}}
{{--                         <span class="tag tag-info">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Cancelled")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partial Payment")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Replacement")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @endif--}}
{{--                      --}}
{{--                    </td> --}}
{{--                     <td class="action-icon">--}}
{{--                      @if($value->name == "Received")--}}
{{--                        <a  href="{{url('grn-details',$value->purchase_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>--}}
{{--                      @endif--}}
{{--                        <a  href="{{route('view',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'muted disabled'  : 'primary' }} p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>--}}

{{--                       <a  href="{{route('edit',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'warning' : ($value->name == 'Placed' ? 'muted disabled' : 'muted disabled') }}  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>--}}

{{--                        <a  class="text-{{ $value->name == 'Draft' ? 'danger' : 'muted disabled' }} p-r-10 f-18 alert-confirm" data-id="{{ $value->purchase_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>--}}
{{--                     </td>               --}}
{{--                 </tr>--}}
{{--                 @endif--}}
{{--                @endforeach--}}
{{--         </tbody>--}}
{{--         </table>--}}
{{--          </div>--}}
{{--          <div class="tab-pane m-t-10" id="draft" role="tabpanel">--}}
{{--             <table  id="draft" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">--}}
{{--         <thead>--}}
{{--            <tr>--}}
{{--              <th>Generation Date</th>--}}
{{--               <th>PO No</th>--}}
{{--               <th>Vendor</th>--}}
{{--               <th>Branch</th>--}}
{{--               <th>Delivery Date</th>--}}
{{--               <th>Payment Date</th>--}}
{{--               <th>Amount</th>--}}
{{--               <th>Status</th>--}}
{{--               <th>Action</th>--}}
{{--            </tr>--}}
{{--         </thead>--}}
{{--         <tbody>--}}
{{--              @foreach ($po as $value)--}}
{{--                @if($value->name == "Draft")--}}
{{--                 <tr >--}}
{{--                     <td>{{$value->order_date }}</td>--}}
{{--                     <td>{{$value->po_no }}</td>--}}
{{--                     <td>{{$value->vendor_name }}</td>--}}
{{--                     <td>{{$value->branch_name }}</td>--}}
{{--                     <td>{{$value->delivery_date }}</td>--}}
{{--                     <td>{{$value->payment_date }}</td>--}}
{{--                     <td>{{number_format($value->net_amount,2) }}</td>--}}
{{--                     <td>--}}
{{--                      @if($value->name == "Draft")--}}
{{--                        <span class="tag tag-default">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Placed")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Received")--}}
{{--                         <span class="tag tag-info">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Cancelled")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partial Payment")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Replacement")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @endif--}}
{{--                      --}}
{{--                    </td> --}}
{{--                     <td class="action-icon">--}}
{{--                      @if($value->name == "Received")--}}
{{--                        <a  href="{{url('grn-details',$value->purchase_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>--}}
{{--                      @endif--}}
{{--                        <a  href="{{route('view',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'muted disabled'  : 'primary' }} p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>--}}

{{--                       <a  href="{{route('edit',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'warning' : ($value->name == 'Placed' ? 'muted disabled' : 'muted disabled') }}  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>--}}

{{--                        <a  class="text-{{ $value->name == 'Draft' ? 'danger' : 'muted disabled' }} p-r-10 f-18 alert-confirm" data-id="{{ $value->purchase_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>--}}
{{--                     </td>               --}}
{{--                 </tr>--}}
{{--                 @endif--}}
{{--                @endforeach--}}
{{--         </tbody>--}}
{{--         </table>--}}
{{--          </div>--}}
{{--          <div class="tab-pane m-t-10" id="complete" role="tabpanel">--}}
{{--             <table  id="draft" class="table dt-responsive table-striped nowrap " width="100%"  cellspacing="0">--}}
{{--         <thead>--}}
{{--            <tr>--}}
{{--              <th>Generation Date</th>--}}
{{--               <th>PO No</th>--}}
{{--               <th>Vendor</th>--}}
{{--               <th>Branch</th>--}}
{{--               <th>Delivery Date</th>--}}
{{--               <th>Payment Date</th>--}}
{{--               <th>Amount</th>--}}
{{--               <th>Status</th>--}}
{{--               <th>Action</th>--}}
{{--            </tr>--}}
{{--         </thead>--}}
{{--         <tbody>--}}
{{--              @foreach ($po as $value)--}}
{{--                @if($value->name == "Complete")--}}
{{--                 <tr >--}}
{{--                     <td>{{$value->order_date }}</td>--}}
{{--                     <td>{{$value->po_no }}</td>--}}
{{--                     <td>{{$value->vendor_name }}</td>--}}
{{--                     <td>{{$value->branch_name }}</td>--}}
{{--                     <td>{{$value->delivery_date }}</td>--}}
{{--                     <td>{{$value->payment_date }}</td>--}}
{{--                     <td>{{number_format($value->net_amount,2) }}</td>--}}
{{--                     <td>--}}
{{--                      @if($value->name == "Draft")--}}
{{--                        <span class="tag tag-default">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Placed")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Received")--}}
{{--                         <span class="tag tag-info">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Cancelled")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete Return")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partially Received")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Partial Payment")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Complete")--}}
{{--                        <span class="tag tag-success">  {{$value->name }}</span>--}}
{{--                      @elseif($value->name == "Replacement")--}}
{{--                        <span class="tag tag-danger">  {{$value->name }}</span>--}}
{{--                      @endif--}}

{{--                    </td>--}}
{{--                     <td class="action-icon">--}}
{{--                      @if($value->name == "Received")--}}
{{--                        <a  href="{{url('grn-details',$value->purchase_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="GRN Details"><i class="icofont icofont-vehicle-delivery-van"></i></a>--}}
{{--                      @endif--}}
{{--                        <a  href="{{route('view',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'muted disabled'  : 'primary' }} p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>--}}

{{--                       <a  href="{{route('edit',$value->purchase_id)}}" class="text-{{ $value->name == 'Draft' ? 'warning' : ($value->name == 'Placed' ? 'muted disabled' : 'muted disabled') }}  p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>--}}

{{--                        <a  class="text-{{ $value->name == 'Draft' ? 'danger' : 'muted disabled' }} p-r-10 f-18 alert-confirm" data-id="{{ $value->purchase_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="icofont icofont-ui-delete"></i></a>--}}
{{--                     </td>--}}
{{--                 </tr>--}}
{{--                 @endif--}}
{{--                @endforeach--}}
{{--         </tbody>--}}
{{--         </table>--}}
{{--          </div>--}}
        </div>

    
  </div>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript" src="{{ URL::asset('public/js/purchase-script.js') }} "></script>
<script type="text/javascript">

  $(document).ready(function() {
    $('.table').DataTable( {
      order:[[0,"desc"]]
        
    } );
  });

   //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");
      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this customer!",
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
                        url: "{{url('/DeletePO')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){

                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove purchase order.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{url('/view-purchases')}}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Customer is safe :)", "error");
           }
        });
  });
</script>
@endsection
@section('css_code')
<style type="text/css">
  a.disabled{
    pointer-events:none;
    cursor: default;
  }
</style>
@endsection
