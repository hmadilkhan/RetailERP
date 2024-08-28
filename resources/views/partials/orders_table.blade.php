<style>
.table .collapse-content > td {
    padding: 0 .75rem;
}
.collapse-content > td > div {
    height: 0;
    opacity: 0;
}
.table .collapse-content.show > td {
    padding: .75rem;
}
.collapse-content.show > td > div {
    height: 100px;
    opacity: 1;
}
.collapse-content > td, .collapse-content > td > div  {
    -webkit-transition: all 1s ease;
    -moz-transition: all ease-in-out;
    transition: all 1s ease-in-out;
}
.collapse-content.show > td, .collapse-content.show > td > div {
    -webkit-transition: all 1s ease;
    -moz-transition: all 1s ease;
    transition: all 1s ease;
}
</style>
<div class="project-table">
    <table id="order_table" class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
               <th>Machine#</th>
               <th>Order#</th>
               <th>Date</th>
               <th>Time</th>
               <th>Branch</th>
               <th>Terminal</th>
               <th>Receipt No</th>
               <th>Customer</th>
               <th>OrderType</th>
               <th>Payment</th>
               <th>Status</th>
               <th>Amount</th>
               <th>Items/Total</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
             @if($orders)
                @foreach($orders as $key => $order)
					
                    <tr>
                        <td>{{$order->machine_terminal_count}}</td>
                        <td>{{$order->id}}</td>
                        <td>
							<div class="dropup">
								<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">{{date("d M Y ",strtotime($order->date))}}
								</button>
								<ul class="dropdown-menu">
								  <li><a href="#" ><strong>Stamp Date</strong></a></li>
								  <li><a href="#">{{date("d M Y ",strtotime($order->delivery_date))}}</a></li>
								  <li class="divider"></li>
								  <li><a href="#"><strong>Order Delivery Date</strong></a></li>
								  <li><a href="#">{{($order->order_delivery_date != "" ? date("d M Y ",strtotime($order->order_delivery_date)): '')}}</a></li>
								</ul>
							 </div>
						</td>
                        <td>{{date("h:i a",strtotime($order->time))}}</td>
                        <td>{{$order->branch_name}}</td>
                        <td>{{$order->terminal_name}}</td>
                        <td>{{$order->receipt_no}}</td>
                        <td>{{$order->name}}</td>
                        <td>{{$order->order_mode}}</td>
                        <td>{{$order->payment_mode}}</td>
                        <td><label class="label {{Custom_Helper::getColorName($order->order_status_name)}}">{{$order->order_status_name}}</label></td>
                        <td>{{$order->total_amount}}</td>
                        <td>{{$order->itemcount}}/{{$order->itemstotalqty}}</td>
                        <td>
						<i onclick='showOrderDetails("{{$order->id}}")' class='icofont icofont icofont-eye-alt icofont-1x text-info' data-toggle='tooltip' data-placement='top' title='' data-original-title='Show Order Details'></i>
						@if(session("roleId") != 20 && session("roleId") != 19)
                        
                        <i onclick='showReceipt("{{$order->receipt_no}}")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Show Receipt'></i>
						<i onclick='assignToServiceProviderModal("{{$order->id}}")' class='icofont icofont icofont-business-man' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign To Service Provider'></i>
						@endif
						@if($order->status != 12 && (session("roleId") != 20 && session("roleId") != 19))
						<i onclick='voidReceipt("{{$order->id}}")' class='alert-confirm text-danger icofont icofont icofont-delete-alt' data-toggle='tooltip' data-placement='top' title='' data-original-title='Mark as Void'></i>
						
						@endif
						@if($order->status != 4 && session("roleId") != 20 && session("roleId") != 19)
							<i onclick='deliveredReceipt("{{$order->id}}")' class='alert-confirm text-success icofont icofont icofont-tick-mark' data-toggle='tooltip' data-placement='top' title='' data-original-title='Mark as Delivered'></i>
						@endif
						@if(session("roleId") == 20 && $order->status == 6)
							<i onclick='assignToBranchModal("{{$order->id}}")' class='icofont icofont icofont-business-man' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign to Branch'></i>
						@endif
                        </td>
                    </tr>
                @endforeach
            @endif
         </tbody>
     </table>
     {{$orders->links()}}
</div>
<script type="text/javascript">
// var orders_total = "{{--$totalorders[0]->totalorders--}}";
// var amount_total = "{{--number_format($totalorders[0]->totalamount,2)--}}";
// console.log(orders_total,amount_total);
$(function() {
    $('.collapse-item').click(function(e) {
        e.preventDefault();
        
        const target = $(this).data('target');
        
        if (!$(target).hasClass('show')) {
            $('.collapse-content').removeClass('show');
        }
        
        $(target).toggleClass('show');
    });
});

// function showContent(key){
        // e.preventDefault();
        
        // const target = $("#game-"+key).data('target');
        // console.log("#game-"+key)
        // if (!$(target).hasClass('show')) {
            // $('.collapse-content').removeClass('show');
        // }
        
        // $(target).toggleClass('show');
// }
@php 
$collection = collect($totalorders);
$pending = $collection->filter(fn ($item) => $item->order_status_name == "Pending")->values()->all();
$processing = $collection->filter(fn ($item) => $item->order_status_name == "Processing")->values()->all();
$voidOrders = $collection->filter(fn ($item) => $item->order_status_name == "Void")->values()->all();
$deliveredOrders = $collection->filter(fn ($item) => $item->order_status_name == "Delivered")->values()->all();
@endphp


$("#pendingorders").html("{{(count($pending) > 0 ? $pending[0]->totalorders : 0)}}");
$("#processingorders").html("{{(count($processing) > 0 ? $processing[0]->totalorders : 0)}}");
$("#voidorders").html("{{(count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0)}}");
$("#deliveredorders").html("{{(count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0)}}");
$("#totalorders").html("{{(count($processing) > 0 ? $processing[0]->totalorders : 0) + (count($voidOrders) > 0 ? $voidOrders[0]->totalorders : 0) + (count($deliveredOrders) > 0 ? $deliveredOrders[0]->totalorders : 0) + (count($pending) > 0 ? $pending[0]->totalorders : 0)  }}");<?php // + (count($pendingorders) > 0 ? $pendingorders[0]->totalorders : 0) ; ?>
// $("#totalorders").html("{{--$totalorders[0]->totalorders--}}");
// $("#totalamount").html("{{--number_format($totalorders[0]->totalamount,2)--}}");
// $(document).on("click", "a", function(){
    // $(this).text("It works!");
// });

function showDates(){
	// alert();
}
</script>