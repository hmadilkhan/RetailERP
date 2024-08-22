 @extends('layouts.master-layout')

@section('title','Transfer Order Details')

@section('breadcrumtitle','Transfer Order Details')

@section('navtransfer','active')

@section('content')


<div class="card">
              
                       <div class="card-header">
                <h3 class="card-header-text">Transfer Order Details
                 </h3><br>
                  <a href="{{ url('/transferlist') }}" id="btnback" name="btnback"><i class="icofont icofont-arrow-left"></i>Back to List
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
                            <p >{{$getdetails[0]->branch_from}}</p>
                            <p >{{$getdetails[0]->br_fr_address}}</p>
                           
                        </div>
                        <div class="col-md-4 col-sm-6">
                        <h6>TO:</h6>
                        <h6 >Branch Manager</h6>
                        <p >{{$getdetails[0]->branch_to}}</p>
                        <p >{{$getdetails[0]->br_to_address}}</p>
                 
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Transfer Number | <span>{{ $getdetails == 0 ? '' : $getdetails[0]->transfer_No }}</span></h6>

                           <h6 class="text-uppercase ">Created on :
                                    <span>{{ $getdetails == 0 ? '' : $getdetails[0]->date }}</span>
                                </h6>

                                <h6 class="text-uppercase txt-info">Status:
                                    <span> {{$getdetails[0]->to_status }}</span>
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
                                    <th>Quantity</th>
                                    <th>Status</th>
                                 </tr>
                              </thead>

                              <tbody>
                                @if($getdetails) 
                                 @foreach($getdetails as $value)
                 <tr>
                  <td >{{$value->product_id}}</td>
                    <td >{{$value->product_name}}</td>
                   <td >{{$value->qty}}</td>
                 
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
                        @elseif($value->item_status == "Completed")
                            <span class="tag tag-primary">  {{$value->item_status }}</span>
                  @endif
                  </td>

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

 window.location = "{{url('transferReport',$getdetails[0]->transfer_id)}}";
 }

 </script>