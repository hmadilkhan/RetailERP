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
               <th>Items (Count/Total)</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
             @if($orders)
                @foreach($orders as $order)
                    <tr>
                        <td>{{$order->machine_terminal_count}}</td>
                        <td>{{$order->id}}</td>
                        <td>{{$order->date}}</td>
                        <td>{{$order->time}}</td>
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
                        <i onclick='showReceipt("{{$order->receipt_no}}")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Show Receipt'></i>
						<i onclick='assignToServiceProviderModal("{{$order->id}}")' class='icofont icofont icofont-business-man' data-toggle='tooltip' data-placement='top' title='' data-original-title='Assign To Service Provider'></i>
						@if($order->status != 12)
							<i onclick='voidReceipt("{{$order->id}}")' class='alert-confirm text-danger icofont icofont icofont-delete-alt' data-toggle='tooltip' data-placement='top' title='' data-original-title='Void Receipt'></i>
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
// var orders_total = "{{$totalorders[0]->totalorders}}";
// var amount_total = "{{number_format($totalorders[0]->totalamount,2)}}";
// console.log(orders_total,amount_total);
$("#totalorders").html("{{$totalorders[0]->totalorders}}");
$("#totalamount").html("{{number_format($totalorders[0]->totalamount,2)}}");
// $(document).on("click", "a", function(){
    // $(this).text("It works!");
// });
</script>