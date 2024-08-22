@extends('layouts.master-layout')

@section('title','Transfer Order')

@section('breadcrumtitle','View Transfer Order')

@section('navtransfer','active')

@section('navcreatetrf','active')


@section('content')
<section class="panels-wells">
	<div class="card">
              
                       <div class="card-header">
                <h3 class="card-header-text">Transfer Order Details
                 </h3><br>
                  <a href="{{ url('/trf_list') }}" id="btnback" name="btnback"><i class="icofont icofont-arrow-left"></i>Back to List
                                    </a>
                      <!--   <div class="f-right">
                              <button type="button" class="btn btn-primary btn-print-invoice waves-effect waves-light ">Print TO
                                    </button>
                              <button type="button" class="btn btn-danger waves-effect waves-light">Export Pdf
                                    </button>
                        
                          </div> -->
                     </div>

                   
                  <div class="card-block">
                     <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                        <h6>From :</h6>
                        <h6 >Administrator</h6>
                        <p>Head Office</p>
                        <p>Park Avenue, Groud Floor, Shahrah-e-Faisal, Malir</p>
                           
                        </div>
                        <div class="col-md-4 col-sm-6">
                        <h6>TO:</h6>
                        <h6 >Branch Manager</h6>
                        <p >{{$getdetails[0]->branch_name}}</p>
                        <p >{{$getdetails[0]->branch_address}}</p>
                 
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Transfer Number | <span>{{ $getdetails == 0 ? '' : $getdetails[0]->transfer_No }}</span></h6>

                           <h6 class="text-uppercase ">Created on :
                                    <span>{{ $getdetails == 0 ? '' : $getdetails[0]->date }}</span>
                                </h6>

                                <h6 class="text-uppercase txt-info">Shipment Amount:
                                    <span> {{$getdetails[0]->shipment_amount }}</span>
                                </h6>
                                 
                               
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table">
                              <thead>
                                 <tr class="thead-default">
                                    <th>Item Code</th>
                                    <th>Product Name</th>
                                    <th>Transfer Quantity</th>
                                    <th>Shipment Cost</th>
                                    <th>Cost Price</th>
                                    <th>Status</th>
                                 </tr>
                              </thead>

                              <tbody>
                                @if($getdetails) 
                                 @foreach($getdetails as $value)
                 <tr>
                  <td >{{$value->item_code}}</td>
                    <td >{{$value->product_name}}</td>
                   <td >{{$value->transfer_qty}}</td>
                   <td >{{$value->shipment_charges}}</td>
                   <td >{{$value->cp}}</td>
                 
                    <td >
                    @if($value->item_status == "Draft")
                    <span class="tag tag-default">  {{$value->item_status }}</span>
                  @elseif($value->item_status == "Placed")
                    <span class="tag tag-success">  {{$value->item_status }}</span>
                  @elseif($value->item_status == "Approved")
                     <span class="tag tag-info">  {{$value->item_status }}</span>
                  @elseif($value->item_status == "Cancel")
                    <span class="tag tag-danger">  {{$value->item_status }}</span>
                       @elseif($value->item_status == "Delivered")
                    <span class="tag tag-danger">  {{$value->item_status }}</span>
                         @elseif($value->item_status == "In-Process")
                    <span class="tag tag-primary">  {{$value->item_status }}</span>
                  @endif
                  </td>

                   </td>
                   
                 </tr>
                  @endforeach
                @endif  
                              </tbody>
                           </table>
                        </div>
                     </div>
                     
              
                  </div>
               </div>

</section>
@endsection
	