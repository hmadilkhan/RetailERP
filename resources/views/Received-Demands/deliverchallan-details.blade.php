 @extends('layouts.master-layout')

@section('title','Deliviery Challan Details')

@section('breadcrumtitle','Deliviery Challan Details')

 @section('navbranchoperation','active')
 @section('navtransfer','active')

@section('navchallanview','active')

@section('content')

<div class="card">
              
                       <div class="card-header">
                <h3 class="card-header-text">Delivery Challan Details
                 </h3><br>
                  <a href="{{ url('/challanlist') }}" id="btnback" name="btnback"><i class="icofont icofont-arrow-left"></i>Back to List
                                    </a>
                   <!--    
                              <button type="button" class="btn btn-primary btn-print-invoice waves-effect waves-light ">Print DC
                                    </button>
                              <button type="button" class="btn btn-danger waves-effect waves-light">Export Pdf
                                    </button>
                        
                          </div> -->
                     </div>

                   
                  <div class="card-block">
                     <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                          <h6>From :</h6>
                              <h6 >Branch Manager</h6>
                        <p >{{$details[0]->deliverd_by}}</p>
                        <p >{{$details[0]->del_add}}</p>
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6>TO:</h6>
                              <h6 >Branch Manager</h6>
                        <p >{{$details[0]->destination}}</p>
                        <p >{{$details[0]->des_add}}</p>
                 
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Challan Number | <span>{{ $details == 0 ? '' : $details[0]->DC_No }}</span></h6>

                           <h6 class="text-uppercase txt-info">Created on :
                                    <span>{{ $details == 0 ? '' : $details[0]->date }}</span>
                                </h6>

                                <h6 class="text-uppercase">Shipment Charges :
                                    <span> {{$details[0]->shipment_amount }}</span>
                                </h6>
                                 
                               
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table">
                              <thead>
                                 <tr class="thead-default">
                                    <th>Product id</th>
                                    <th>Product Name</th>
                                    <th>Delivered Quantity</th>
                                    <th>Cost Price</th>
                                    <th>Shipment Amount</th>
                                    <th>Total Cost</th>
                                 </tr>
                              </thead>

                              <tbody>
                                @if($details) 
                                 @foreach($details as $value)
                 <tr>
                  <td >{{$value->product_id}}</td>
                    <td >{{$value->product_name}}</td>
                   <td >{{$value->deliverd_qty}}</td>
                   <td >{{$value->cost_price}}</td>
                   <td >{{number_format(($value->shipment_charges == "" ? 0 : $value->shipment_charges),2)}}</td>
                   <td >{{number_format(($value->shipment_charges == "" ? 0 : $value->shipment_charges) + $value->cost_price,2)}}
                   </td>
                   
                 </tr>
                  @endforeach
                @endif  
                              </tbody>
                           </table>
                            <br>
                            <div class="button-group ">
                                {{--                                        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>--}}
                                {{--                                            Export to Excel Sheet--}}
                                {{--                                        </button>--}}
                                <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                                    Print Pdf
                                </button>
                            </div>
                        </div>
                     </div>
                     
              
                  </div>
               </div>


@endsection
 @section('scriptcode_three')
     <script>

         function generate_pdf()
         {
             window.location = "{{url('dcreport',$challanid)}}";
         }

     </script>