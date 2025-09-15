@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','Add Purchase Order')
@section('navVendorPO','active')
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
                        @if($general[0]->status_id != 4)
                       <div class="radial">
                          
                          @if($general[0]->status_id == 3 || $general[0]->status_id == 5)
                           <button class="icofont icofont-reply fa-3x"
                            id="fa-5" data-toggle="tooltip" data-placement="top" title="" data-original-title="Return" onclick="location='{{ URL('return/'.$general[0]->purchase_id )}}'"></button>
                            @endif
                            @if($general[0]->status_id == 1)
                           <button class="icofont icofont-ui-delete fa-3x" id="fa-6" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel"></button>
                           @endif
                           @if($general[0]->status_id == 2 || $general[0]->status_id == 7 || $general[0]->status_id == 5)
                           <a class="icofont icofont-truck-loaded fa-3x" id="fa-1" data-toggle="tooltip" data-placement="top" title="" data-original-title="Received" onclick="location='{{ URL('receive/'.$general[0]->purchase_id )}}'"></a>
                            @endif
                           @if($general[0]->status_id == 2 || $general[0]->status_id == 7 || $general[0]->status_id == 5)
                         <!--   <a class="icofont icofont-close fa-3x" id="fa-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel" onclick="location='{{ URL('update-status-po/'.$general[0]->purchase_id )}}'"></a> -->
                           @endif
                        
                           <button class="fab">
                                <i class="icofont icofont-plus fa-3x" id="plus"></i>
                            </button>
                          
                        </div>
                        @endif
                      </div>
                     </div>
                  </div>
                  <div class="card-block">
                    <div class="row">
                        <div class="col-xs-12" style="margin-bottom:15px;">
                        <strong>Status:</strong> 
                         @if($general[0]->status_id == 1)
                         <span class="tag tag-warning">  Draft</span>
                         @elseif($general[0]->status_id== 2)
                         <span class="tag tag-success">  Placed </span>
                         @elseif($general[0]->status_id == 3)
                         <span class="tag tag-info">  Received</span>
                         @elseif($general[0]->status_id == 4)
                         <span class="tag tag-danger">  Cancelled</span>
                         @elseif($general[0]->status_id == 5)
                         <span class="tag tag-danger">  Partially return</span>
                         @elseif($general[0]->status_id == 7)
                         <span class="tag tag-danger">  Partially Received</span>
                         @endif
                        </div>
                    </div>
                    <div class="clearfix"></div>
                     <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                           <h6>Vendor Information :</h6>
                           <h6>{{$vendor[0]->vendor_name}}</h6>
                           <p>{{$vendor[0]->address}}.</p>
                           <p>{{$vendor[0]->vendor_contact}}</p>
                           <p>{{$vendor[0]->vendor_email}}</p>
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6>Order Information :</h6>
                           <table class="table table-responsive invoice-table invoice-order">
                              <tbody>
                                 <tr>
                                    <th>Date :</th>
                                    <td>{{date("d F Y",strtotime($general[0]->order_date))}}</td>
                                 </tr>
                                 <tr>
                                    <th>Delivery :</th>
                                    <td>{{date("d F Y",strtotime($general[0]->delivery_date))}}</td>
                                 </tr>
                                 <tr>
                                    <th>Payment :</th>
                                    <td>
                                      <strong>{{date("d F Y",strtotime($general[0]->payment_date))}}</strong>
                                    </td>
                                 </tr>
                                

                              </tbody>
                           </table>
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Purchase Order Number : <span>{{$general[0]->po_no }}</span></h6>
                           @if($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5)
                             <h6 class="text-uppercase txt-default">Total Due :
                                <span class="txt-default">Rs. 
                                   <?php echo number_format(Custom_Helper::getDueTotal($received)+ $accounts[0]->shipment,2); ?>
                                </span>
                           </h6>

                           @else
                             <h6 class="text-uppercase txt-default">Total Due :
                                <span class="txt-default">Rs. 
                                  {{number_format($accounts[0]->balance_amount,2) }}
                                </span>
                             </h6>
                           @endif
                           <h6 class="">Comments : {{$general[0]->comments}}</h6>

                        </div>
                     </div>
                 
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table invoice-detail-table">
                              <thead>
                                 <tr class="thead-default">
                                    <th>Product</th>
                                    <th>UOM</th>
                                    <th>Batch #</th>
                                    <th>Exp Date</th>
                                    <th>Unit Price</th>
                                    <th>S.Tax</th>
                                    <th>Dis.</th>
                                    <th class="text-danger">Unit Cost</th>
                                    @if($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5)
                                    <th class="text-info">Received</th>
                                    @endif
                                    @if($general[0]->status_id == 5 || $general[0]->status_id == 6 )
                                    <th class="text-info">Return</th>
                                    @endif
                                    <th>Qty</th>
                                    <th>Gr.Amount</th>
                                    <th>Total S.Tax</th>
                                    <th>Total Dis.</th>
                                    <th>Net Total</th>
                                 </tr>
                              </thead>
                              <tbody>
                            <?php
                               $Tqty;$unitCost; $grAmnt; $taxAmnt;$DiscAmnt;$netAmnt;
                               $TotalTqty =0; $totalUnitCost =0; $totalGrAmnt =0; $totalTaxAmnt=0;$totalDiscAmnt=0;$totalNetAmnt=0;
                             ?>  
                            @if($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5)
                              @foreach($received as $value)
                              <?php  
                                  if($value->discount_by == 1 ){
                                      $discount =  ($value->price / 100) * $value->discount_per_item;
                                      // echo $discount;exit;
                                  }else{
                                      $discount = $value->discount_per_item;
                                  }
                                 $Tqty = $value->quantity; 
                                 $unitCost = ($value->price + $value->tax_per_item_value) - $discount;
                                 $grAmnt = $value->price * $value->quantity;
                                 $taxAmnt = ($value->tax_per_item_value * $value->quantity);
                                 $DiscAmnt = $discount * $value->quantity;
                                 $netAmnt =($grAmnt + $taxAmnt - $DiscAmnt);// $value->total_amount * $value->quantity;
                              ?>
                                 <tr>
                                    <td>
                                       <h6>{{$value->code." - ".$value->product_name}}</h6>
                                       <p>{{$value->product_description}}</p>
                                    </td>
                                   <td>{{$value->unitName}}</td>  
                                  <td>{{$value->batch_no}}</td>
                                  <td>{{$value->expiry_date}}</td>
                                  <td>{{number_format($value->price,2)}}</td>
                                  <td>{{number_format($value->tax_per_item_value,2)}}</td>
                                  <td>{{number_format($discount,2)}}</td>
                                  <td class="text-danger">{{number_format($unitCost,2)}}</td>
                                   <td class="text-info">{{$value->received }}</td>
                                     @if($general[0]->status_id == 5 || $general[0]->status_id == 6 )
                                        <td class="text-info">{{number_format($value->qty_return,2) }}</td>
                                      @endif
                                  <td>{{ number_format($Tqty,2)}}</td>
                                   <td>{{number_format($grAmnt,2)}}</td>  
                                    <td>{{number_format($taxAmnt,2)}}</td>
                                    <td>{{number_format($DiscAmnt,2)}}</td>
                                    <td>{{number_format($netAmnt,2)}}</td>
                                 </tr>
                                  <?php
                                   $TotalTqty = $TotalTqty + $Tqty;
                                   $totalGrAmnt = $totalGrAmnt  + $grAmnt;
                                   $totalTaxAmnt = $totalTaxAmnt + $taxAmnt;
                                   $totalDiscAmnt = $totalDiscAmnt + $DiscAmnt;
                                   $totalNetAmnt = $totalNetAmnt + $netAmnt;
                                  ?>
                                 @endforeach
                            @else
                               @if($items)
                                @foreach($items as $value)
                                   <?php  
                                  if($value->discount_by == 1 ){
                                      $discount =  ($value->price / 100) * $value->discount_per_item;
                                      // echo $discount;exit;
                                  }else{
                                      $discount = $value->discount_per_item;
                                  }
                                 $Tqty = $value->quantity; 
                                 $unitCost = ($value->price + $value->tax_per_item_value) - $discount;
                                 $grAmnt = $value->price * $value->quantity;
                                 $taxAmnt = ($value->tax_per_item_value * $value->quantity);
                                 $DiscAmnt = $discount * $value->quantity;
                                 $netAmnt =($grAmnt + $taxAmnt - $DiscAmnt);//$netAmnt = $value->total_amount * $value->quantity;
                              ?>
                                 <tr>
                                    <td>
                                       <h6>{{$value->item_code." - ".$value->product_name}}</h6>
                                       <p>{{$value->product_description}}</p>
                                    </td>
                                  <td>{{$value->unitName}}</td>  
                                  <td>{{$value->batch_no}}</td>
                                  <td>{{$value->expiry_date}}</td>
                                  <td>{{number_format($value->price,2)}}</td>
                                  <td>{{number_format($value->tax_per_item_value,2)}}</td>
                                  <td>{{number_format($discount,2)}}</td>
                                  <td class="text-danger">{{number_format($unitCost,2)}}</td>
                                  <td>{{ number_format($Tqty,2)}}</td>
                                  <td>{{number_format($grAmnt,2)}}</td>  
                                  <td>{{number_format($taxAmnt,2)}}</td>
                                  <td>{{number_format($DiscAmnt,2)}}</td>
                                  <td>{{number_format($netAmnt,2)}}</td>
                                 </tr>
                                 <?php
                                   $TotalTqty = $TotalTqty + $Tqty;
                                   $totalGrAmnt = $totalGrAmnt  + $grAmnt;
                                   $totalTaxAmnt = $totalTaxAmnt + $taxAmnt;
                                   $totalDiscAmnt = $totalDiscAmnt + $DiscAmnt;
                                   $totalNetAmnt = $totalNetAmnt + $netAmnt;
                                  ?>
                                 @endforeach
                              @endif
                            @endif
                             
                              </tbody>
                               <tfoot>
                                <tr>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                  <td></td>
                                    @if($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5)
                                    <td></td>
                                    @endif
                                    @if($general[0]->status_id == 5 || $general[0]->status_id == 6 )
                                    <td></td>
                                    @endif
                                  <td class="text-info" id="allItemQty">{{number_format($TotalTqty,2)}}</td>
                                  <td class="text-info" id="allItemGRAmnt">{{number_format($totalGrAmnt,2)}}</td>
                                  <td class="text-info" id="allItemSTax">{{number_format($totalTaxAmnt,2)}}</td>
                                  <td class="text-info" id="allItemDisc">{{number_format($totalDiscAmnt,2)}}</td>
                                  <td class="text-info" id="allItemNetAmnt">{{number_format($totalNetAmnt,2)}}</td>
                                  <td></td>
                                </tr>
                              </tfoot>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table table-responsive invoice-table invoice-total">
                              <tbody>
                                 @if($general[0]->status_id == 7 || $general[0]->status_id == 6 || $general[0]->status_id == 3 || $general[0]->status_id == 5)
                                  <tr>
                                    <th>GROSS AMOUNT:</th>
                                    <td><?php echo number_format(Custom_Helper::getSubTotal($received),2); ?></td>
                                 </tr>
                                <tr>
                                   <th>S.TAX AMOUNT</th>
                                   <td>{{$totalTaxAmnt}}</td>
                                 </tr>
                                 <tr>
                                   <th>DISC. AMOUNT</th>
                                   <td>{{$totalDiscAmnt}}</td>
                                 </tr>
                                 <!-- <tr>
                                    <th>Taxes (+):</th>
                                    <td><?php echo number_format(Custom_Helper::getTaxTotal($received),2); ?></td>
                                 </tr>
                                 <tr>
                                    <th>Discount (-):</th>
                                    <td>{{number_format($accounts[0]->discount,2)}}</td>
                                 </tr> -->
                                  <tr>
                                    <th>OTHER EXPENSES:</th>
                                    <td>{{number_format($accounts[0]->shipment,2)}}</td>
                                 </tr>
                                 <tr class="txt-info">
                                    <th>
                                       <h5>NET AMOUNT :</h5></th>
                                       <td>
                                         <h5>
										 {{number_format((Custom_Helper::getDueTotal($received) + $accounts[0]->shipment  ),2)}} 
                                          {{--{{number_format($netAmnt + $accounts[0]->shipment,2)}}--}}
                                         </h5>
                                       </td>
                                 </tr>

                                 @else
                                 <tr>
                                    <th>GROSS AMOUNT:</th>
                                    <td>{{number_format($accounts[0]->total_amount,2)}}</td>
                                 </tr>
                                 <tr>
                                   <th>S.TAX AMOUNT</th>
                                   <td>{{number_format($totalTaxAmnt,2)}}</td>
                                 </tr>
                                 <tr>
                                   <th>DISC. AMOUNT</th>
                                   <td>{{number_format($totalDiscAmnt,2)}}</td>
                                 </tr>
                               <!--   <tr>
                                    <th>Taxes (+):</th>
                                    <td>{{number_format($accounts[0]->tax_amount,2)}}</td>
                                 </tr>
                                 <tr>
                                    <th>Discount (-):</th>
                                    <td>{{number_format($accounts[0]->discount,2)}}</td>
                                 </tr> -->
                                  <tr>
                                    <th>OTHER EXPENSES:</th>
                                    <td>{{number_format($accounts[0]->shipment,2)}}</td>
                                 </tr>
                                 <tr class="txt-info">
                                    <th>
                                       <h5>NET AMOUNT:</h5></th>
                                    <td>
									<h5>{{number_format($accounts[0]->balance_amount,2)}}</h5>
                                       {{--<h5>{{number_format($accounts[0]->net_amount,2)}}</h5>--}}
									</td>
                                 </tr>
                                 @endif
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12">
               <div class="form-group">
                  <div class="button-group ">
{{--                     <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>--}}
{{--                        Export to Excel Sheet--}}
{{--                     </button>--}}
                     <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                        Print Pdf
                     </button>
                  </div>
            </div>
                        </div>
{{--                        <div class="col-sm-12">--}}
{{--                           <h6>Terms And Condition :</h6>--}}
{{--                           <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.--}}
{{--                              Duis aute irure dolor </p>--}}
{{--                        </div>--}}
                     </div>
                  </div>
               </div>
    </section>   
@endsection


@section('scriptcode_three')
<script type="text/javascript" src="{{ asset('public/assets/pages/button-fab.js') }}"></script>
<script type="text/javascript">
   function generate_pdf()
   {
      window.location = "{{url('purchasereport',$purchaseid)}}";
   }
</script>
@endsection
