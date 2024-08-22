 @extends('layouts.master-layout')

@section('title','Demand-Received Panel')

@section('breadcrumtitle','View Demand')

@section('navtransfer','active')

@section('navrecdemand','active')

@section('content')
   <div class="card">
              
                       <div class="card-header">
                <h3 class="card-header-text">Demand Details
                 </h3><br>
                  <a href="{{ url('/received-demand') }}" id="btnback" name="btnback">Back to List
                                    </a>
                   
                     </div>

                  
                  
                  <div class="card-block">
                     <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                           <h6>From :</h6>
                              <h6 >{{$sender[0]->full_name}}</h6>
                        <p >{{$sender[0]->branch_name}}</p>
                        <p >{{$sender[0]->branch_address}}</p>
                           
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6>TO:</h6>
                            <h6 >{{$reciver[0]->full_name}}</h6>
                        <p id='branchto' >{{$reciver[0]->branch_name}}</p>
                        <p >{{$reciver[0]->branch_address}}</p>
                 
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Demand Number | <span id='demandid'>{{ $details == 0 ? '' : $details[0]->doid }}</span></h6>

                           <h6 class="text-uppercase txt-info">Created on :
                                    <span>{{ $details == 0 ? '' : $details[0]->date }}</span>
                                </h6>
                                 <h6 class="text-uppercase">Status:
                                    <span class="tag tag-default">{{ $details == 0 ? '' : $details[0]->status1 }}</span>
                                </h6>

                               
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table ">
                              <thead>
                                 <tr class="thead-default">

                                    <th>Item Code</th>
                                    <th>Product Name</th>
                                    <th>Demand Qty.</th>
                                    <th>Transfer Qty.</th>
                                    <th>Delivered Qty.</th>
                                    <th>Recivied Qty.</th>
                                    <th>Purchase Qty.</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @if($podetails) 
                                 @foreach($podetails as $value)
                 <tr>
                 
                  
                   <td >{{$value->product_id}}</td>
                   <td >{{$value->product_name}}</td>
                   <td >{{$value->demandqty}}</td>
                   <td >{{$value->transferqty}}</td>
                   <td >{{$value->deliverdqty}}</td>
                   <td >{{$value->grnqty}}</td>
                   <td><input type="text" class="form-control" id="poqty"></td>
                 </tr>
                  @endforeach
                @endif  
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
               <div id='abc'>
                 
               </div>

        <div class="row">
      <div class="col-lg-12 col-sm-12 ">
            <div class="form-group ">
                <button type="button" id="btnFinalSubmit" class="btn btn-md btn-primary waves-effect waves-light  f-right" onclick="update_status()"  >
                   View Transfer Orders
                </button>

            </div>       
        </div>  
 </div> 

@endsection  
@section('scriptcode_three')
@endsection  