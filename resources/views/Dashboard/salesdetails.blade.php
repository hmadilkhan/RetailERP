@extends('layouts.master-layout')

@section('title','Dashboard')

@section('breadcrumtitle','Dashboard')

@section('navdashboard','active')

@section('content')

<section class="panels-wells">
    <?php
        $itemQty = 0;
        $actualAmount = 0;
        $discountAmount = 0;
        $count = 0;
    ?>

    <div class="card">
     <div class="card-header">
     <?php $total_amount = 0; ?>

      </div>
       <div class="card-block">
           <a href="{{ url('/sales-details') }}" class="btn btn-success" ><h6 class="m-b-0 f-w-100" style="color:white;"><i class="icofont icofont-arrow-left"></i>Back</h6></a>

<div class="row">
    <div class="text-center"><h2>{{$names[0]->branch_name}}</h2></div>
    <div class="text-center"><h4>{{$names[0]->terminal_name}}</h4></div>
</div>
           <hr/>
@if($mode == "isdb")
<div class="row ">
            	<div class="text-center"><h2>ITEM SALES DATABASE</h2></div>
            	<div class="col-sm-12 col-md-12">
            		<table width="100%" class="table {{ count($details) == 0 ? 'table-responsive' : null }} dt-responsive table-striped nowrap ">
            			<thead>
            				<th>Item Code</th>
                            <th>Product Name</th>
            				<th>Qty</th>
            				<th>Total Amount</th>
            			</thead>
            			<tbody>
                            <?php
                                $qty = 0;
                                $total_amount = 0;
                             ?>
            				@foreach ($details as $value)
            				<?php
                                $count = $count + 1;
                                $total_amount = $total_amount + $value->total_amount;
                                $itemQty = $itemQty + $value->qty;
                                $qty = $qty + $value->qty;
            				?>
            					<tr>
            						<td>{{$value->item_code}}</td>
            						<td>{{$value->product_name}}</td>
            						<td>{{number_format($value->qty,2)}}</td>
            						<td>{{number_format($value->total_amount,2)}}</td>
            					</tr>
            				@endforeach
            			</tbody>
                        <tr>
                            <td colspan="2" ><label class="f-right f-24 f-w-900">Total :</label></td>
                            <td><label class=" f-24 f-w-900"> {{$qty}}</label></td>
                            <td><label class=" f-24 f-w-900"> {{number_format($total_amount,2)}}</label></td>
                            </tr>
            		</table>
            	</div>
            </div>
@elseif($mode == "ci")
  <div class="row ">
                <div class="text-center"><h2>CASH IN</h2></div>
                <div class="col-sm-12 col-md-12">
                    <table class="table {{ count($details) == 0 ? 'table-responsive' : null }} dt-responsive table-striped nowrap ">
                        <thead>
							<th>Amount</th>
							<th>Narration</th>
							<th>Date</th>
							<th>Time</th>
                        </thead>
                        <tbody>
                        @foreach ($details as $value)
                            <?php
                            $count = $count + 1;

                            ?>
                            <tr>
                                <td>{{$value->amount}}</td>
                                <td>{{$value->narration}}</td>
								<td>{{date("Y-m-d",strtotime($value->datetime))}}</td>
								<td>{{date("h:i a",strtotime($value->datetime))}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
@elseif($mode == "co")
   <div class="row ">
       <div class="text-center"><h2>CASH OUT</h2></div>
       <div class="col-sm-12 col-md-12">
           <table class="table {{ count($details) == 0 ? 'table-responsive' : null }} dt-responsive table-striped nowrap ">
               <thead>
               <th>Amount</th>
               <th>Narration</th>
			   <th>Date</th>
			   <th>Time</th>
               </thead>
               <tbody>
               @foreach ($details as $value)
                   <?php
                   $count = $count + 1;
                   ?>
                   <tr>
                       <td>{{$value->amount}}</td>
                       <td>{{$value->narration}}</td>
					   <td>{{date("Y-m-d",strtotime($value->datetime))}}</td>
                       <td>{{date("h:i a",strtotime($value->datetime))}}</td>
                   </tr>
               @endforeach
               </tbody>
           </table>
       </div>
   </div>
@elseif($mode == "sr")
   <div class="row ">
       <div class="text-center"><h2>SALES RETURN</h2></div>
       <div class="col-sm-12 col-md-12">
           <table class="table {{ count($details) == 0 ? 'table-responsive' : null }} dt-responsive table-striped nowrap ">
               <thead>
               <th>Receipt No</th>
               <th>Time</th>
               <th>Product Name</th>
               <th>Qty</th>
               <th>Amount</th>
               </thead>
               <tbody>
               @foreach ($details as $value)
                   <?php
                   $count = $count + 1;
                   ?>
                   <tr>
                       <td>{{$value->receipt_no}}</td>
                       <td>{{date("h:i a",strtotime($value->timestamp))}}</td>
                       <td>{{$value->product_name}}</td>
                       <td>{{$value->qty}}</td>
                       <td>{{$value->amount}}</td>
                   </tr>
               @endforeach
               </tbody>
           </table>
       </div>
   </div>
@elseif($mode == "ex")
<div class="row ">
       <div class="text-center"><h2>EXPENSES</h2></div>
       <div class="col-sm-12 col-md-12">
           <table class="table {{ count($details) == 0 ? 'table-responsive' : null }} dt-responsive table-striped nowrap ">
               <thead>
               <th>S.No.</th>
			   <th>Date</th>
			   <th>Category</th>
               <th>Details</th>
               <th>Amount</th>
               </thead>
               <tbody>
               @foreach ($details as $value)
                   <?php
                   $count = $count + 1;
                   ?>
                   <tr>
					   <td>{{$count}}</td>
					   <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                       <td>{{$value->expense_category}}</td>
                       <td>{{$value->expense_details}}</td>
                       <td>{{number_format($value->amount,2)}}</td>
                   </tr>
               @endforeach
               </tbody>
           </table>
       </div>
   </div>
@else
            <div class="row ">
            	<div class="text-center"><h2>{{($mode == 1 ? "CASH DETAILS" : ($mode == 2 ? "CREDIT CARD DETAILS" : ($mode == 3 ? "CUSTOMER CREDIT DETAILS" : "")))}}</h2></div>
            	<div class="col-sm-12 col-md-12">
            		<table class="table table-responsive dt-responsive table-striped nowrap">
            			<thead>
							<th>Receipt No.</th>
            				<th>Total Item Qty</th>
                            @if($mode == 3)
                                <th>Actual Amount</th>
                                <th>Discount Amount</th>
                            @endif
            				<th>Total Amount</th>
            				<th>Date</th>
            				<th>Time</th>
                            <th>Action</th>
            			</thead>
            			<tbody>
            				@foreach ($details as $value)
            				<?php
                                $count = $count + 1;
                                $total_amount = $total_amount + $value->total_amount;
                                $itemQty = $itemQty + $value->total_item_qty;
                                $actualAmount = $actualAmount + $value->ActualReceiptAmount;
                                $discountAmount = $discountAmount + $value->discount_amount;
            				?>
            					<tr>
            						<td><label class="f-w-900">{{$value->customer}}</label> </br> {{$value->receipt_no}}</td>
            						<td>{{$value->total_item_qty}}</td>
                                    @if($mode == 3)
                                        <td>{{$value->ActualReceiptAmount}}</td>
                                        <td>{{$value->discount_amount}}</td>
                                    @endif
            						<td>{{number_format($value->total_amount,0)}}</td>
            						<td>{{$value->date}}</td>
            						<td>{{date("h:i a",strtotime($value->time))}}</td>
                                    <td>
                                        <a href="{{url('print',$value->receipt_no)}}" class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Print'></a>
                                    </td>
            					</tr>
            				@endforeach
                             <tr>
                                <td></td>
                                <td> </td>
                                <td></td>
                                <td></td>
                                <td class="text-left"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="f-24 f-w-300">
                                <td> Total Receipts</td>
                                <td>total Items </td>
                                <td>Total Sales</td>
                                <td>Total Discount</td>
                                <td class="text-left">Net Amount</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="f-24 f-w-300">
                                <td> {{$count}}</td>
                                <td>{{number_format($itemQty,0)}}</td>
                                <td>{{number_format($actualAmount,0)}}</td>
                                <td>{{number_format($discountAmount,0)}}</td>
            					<td class="text-left">Rs. {{number_format($total_amount,0)}}</td>
                                <td></td>
                                <td></td>
            				</tr>
            			</tbody>
            		</table>
            	</div>
            </div>
@endif
        </div>
    </div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
	$(document).ready(function() {
    $('.table').DataTable( {
      order:[[0,"desc"]]

    } );
  });
</script>
@endsection


@section('css_code')
<style>
    .card-header {
        padding: 0px;
    }
</style>
@endsection
