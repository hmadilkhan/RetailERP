@extends('layouts.master-layout')

@section('title','Users-Details')

@section('breadcrumtitle','View Vendor')

@section('navpermission','active')

@section('content')
<section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                  	<h5 class="card-header-text"> Sales Permission for -- {{$terminal_name[0]->branch_name.' | '.$terminal_name[0]->terminal_name}}</h5>
                     <h5 class=""><a href="{{ url('terminals',$terminal) }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                     
                    </div>
                  <div class="card-block">
                <form action="{{url('create-permission')}}" method="GET">
                	<input type="hidden" name="id" value="{{(count($result) != 0 ? $result[0]->permission_id : 0)}}">
                	<input type="hidden" id="terminalId" name="terminalId" value="{{$terminal}}">
               	<div class="row">
               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="ob" class="custom-control-input" {{(count($result) != 0  ? ($result[0]->ob == 1 ? 'checked' : '') : '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Opening Balance</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cb" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->cb == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Closing Balance</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cashSales" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->cash_sale == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Cash Sales</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cardSales" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->card_sale == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Credit Card Sales</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="customerCredtSales" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->customer_credit_sale == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Customer Credit Sales</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="costing" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->cost == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Costing</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="r_cash" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->r_cash == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Customer Credit Return (Cash)</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="r_card" class="custom-control-input f-18" {{(count($result) != 0 ? ($result[0]->r_card == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Customer Credit Return (Credit Card)</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="r_cheque" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->r_cheque == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Customer Credit Return (Cheque)</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="saleReturn" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->sale_return == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Sale Return</span>
                                </label>
			                  </div>
			               </div>
               		</div>


               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="discounts" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->discount == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Discount</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cashIn" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->cash_in == 1 ? 'checked' : ''): '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Cash In</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		<div class="col-md-4 m-b-5">
               			<div class="form-group row">
                     		 <div class="col-md-10 has-success">
                         			<label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="cashOut" class="custom-control-input" {{(count($result) != 0 ? ($result[0]->cash_out == 1 ? 'checked' : '') : '')}}>
                                    <span class="custom-control-indicator"></span>
                                    <span class="custom-control-description f-18">Cash Out</span>
                                </label>
			                  </div>
			               </div>
               		</div>

               		
               		<button class="f-right btn btn-md btn-primary m-10">Submit</button>

               	</div> <!-- ROW DIV END !-->
                   
                    
               </form>
                   
                
                       
              
            
                  </div>
               </div>


            </section>
@endsection