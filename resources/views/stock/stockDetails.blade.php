@extends('layouts.master-layout')

@section('title','Inventory')

@section('breadcrumtitle','View Inventory')

@section('navbranchoperation','active')
@section('navstock','active')

@section('content')



    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Stock</h5><br/>
         <a href="{{ url('/stock-list') }}" id="btnback" name="btnback"><i class="icofont icofont-arrow-left"></i>Back to List
         </a>

         <div class="text-center f-64 text-info">{{strtoupper($product[0]->product_name)}}</div>
     </div>      
       <div class="card-block m-t-10">

    	<ul class="nav nav-tabs tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#home3" role="tab">Branch Wise Stock Details</a>
    </li>
	<li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#messages6" role="tab">Cost Price Logs</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#profile3" role="tab">Purchase Wise Stock Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#messages3" role="tab">Transfer Wise Stock Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#messages4" role="tab">Date Wise Stock Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#messages5" role="tab">Stock Report</a>
    </li>
	
   
</ul>
 

 
<div class="tab-content">
    <div class="tab-pane active" id="home3" role="tabpanel">
    	<br/>
       <table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
                                    <thead>
                                       <tr>
                                          <th>Stock ID</th>
                                          <th>Date</th>
                                          <th>Stock Opening</th>
                                          <th>Balance</th>
                                          <th>Cost Price</th>
                                          <th>Retail Price</th>
                                          <th>WholeSale Price</th>
                                          <th>Discount Price</th>
                                          <th>Branch</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                     @if($stocks)
                                        @foreach ($stocks as $value)
                                           <tr>
                                             
                                              <td>{{$value->stock_id}}</td>
                                              <td>{{date('M d, Y',strtotime($value->date))}}</td>
                                              <td>{{$value->totalQty}}</td>
                                              <td>{{$value->qty}}</td>
                                              <td>{{$value->cost_price}}</td> <!-- Actaually there was a total_amount field from purchase order so i changed it to get cost from stock table -->
                                              <td>{{$value->retail_price}}</td>
                                              <td>{{$value->wholesale_price}}</td>
                                              <td>{{$value->discount_price}}</td>
                                              <td>{{$value->branch_name}}</td>
                                           </tr>
                                          @endforeach
                                        @endif
                                       
                                    </tbody>
                                 </table>
    </div>
    <div class="tab-pane" id="profile3" role="tabpanel">
        <table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
                                    <thead>
                                       <tr>
                                          <th>Purchase Order No</th>
                                          <th>GRN</th>
                                          <th>Vendor</th>
                                          <th>Product Name</th>
                                          <th>Cost Price</th>
                                          <th>Qty</th>
                                          <th>Received Date</th>
                                          <th>Status</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                     @if($purchase)
                                        @foreach ($purchase as $value)
                                           <tr>
                                              <td >{{$value->po_no}}</td>
                                              <td>{{$value->GRN}}</td>
                                              <td>{{$value->vendor_name}}</td>
                                              <td>{{$value->product_name}}</td>
                                              <td>{{number_format($value->cost_price,0)}}</td>
                                              <td>{{$value->qty_rec}}</td>
                                              <td>{{date('M d, Y',strtotime($value->created_at))}}</td>
                                              <td>{{$value->status}}</td>
                                           </tr>
                                          @endforeach
                                        @endif
                                       
                                    </tbody>
                                 </table>
    </div>
    <div class="tab-pane" id="messages3" role="tabpanel">
         <table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
                                    <thead>
                                       <tr>
                                          <th>Transfer ID</th>
                                          <th>GRN</th>
                                          <th>Date</th>
                                          <th>Product Name</th>
                                          <th>Qty</th>
                                          <th>Status</th>
                                          <th>From Branch</th>
                                          <th>To Branch</th>
                                          
                                       </tr>
                                    </thead>
                                    <tbody>
                                     @if($transfer)
                                        @foreach ($transfer as $value)
                                           <tr>
                                              <td >{{$value->Transfer_id}}</td>
                                              <td>{{$value->GRN}}</td>
                                              <td>{{date('M d, Y',strtotime($value->created_at))}}</td>
                                              <td>{{$value->product_name}}</td>
                                              <td>{{$value->qty_rec}}</td>
                                              <td>{{$value->status}}</td>
                                              <td>{{$value->fromBranch}}</td>
                                              <td>{{$value->toBranch}}</td>
                                           </tr>
                                          @endforeach
                                        @endif
                                       
                                    </tbody>
                                 </table>
    </div>
    <div class="tab-pane" id="messages4" role="tabpanel">
		<!--<div class="row m-t-2">
			<div class="col-lg-3 col-md-3">
				<div class="form-group">
					<label class="form-control-label">From Date:</label>
					<input type="text" class="form-control float-right"  autocomplete="off" id="from_date" placeholder="From Date" value="">
					<input type="hidden" id="hiddenamount" name="hiddenamount" value="0">
				</div>

			</div>
			<div class="col-lg-3 col-md-3">
				<div class="form-group">
					<label class="form-control-label">To Date:</label>
					<input type="text" class="form-control float-right"  autocomplete="off" id="to_date" placeholder="From Date" value="">
					<input type="hidden" id="hiddenamount" name="hiddenamount" value="0">
				</div>
			</div>
			<div class="col-lg-3 col-md-3">
				<div class="form-group">
					<label class="form-control-label">Select Terminal:</label>
					<select id="terminal" class="select2"></select>
					<input type="hidden" id="hiddenamount" name="hiddenamount" value="0">
				</div>
			</div>
		</div>--> 
        <table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>GRN</th>
                <th>Date</th>
                <th>Unit</th>
                <th>Cost Price</th>
                <th>Qty</th>
                <th>Branch</th>
                <th>Terminal</th>
                <th>Status</th>
                <th>View</th>
            </tr>
            </thead>
            <tbody>
            @if($details)
                <?php $qty = 0 ; ?>
                @foreach ($details as $detail)
                    <?php $qty = (int)$qty + (int)$detail->qty - ( (int)$detail->qty - (int)$detail->balance);?>
                    <tr>
                        <td >{{$detail->grn_id}}</td>
                        <td>{{date("M d, Y",strtotime($detail->date))}}</td>
                        <td>{{$detail->uom}}</td>
                        <td>{{ $detail->cost_price  }}</td>
						<!-- {{(int)$detail->qty - ((int) $detail->qty - (int) $detail->balance)}} -->
                        <td>{{($detail->purchase == "Sales" ? $detail->qty / $conversion_unit[0]->weight_qty: $detail->qty)}}</td>
                        <td>{{$detail->branch_name}}</td>
                        <td>{{($detail->purchase == "Sales"  ? $detail->terminal : "")}}</td>

                        <td>{{($detail->purchase > 0 ? 'By Purchase' : ($detail->transfer > 0 ? 'By Transfer' : ($detail->purchase == "Sales" ? "Sales" : 'Stock Opening')))}}</td>
                        <td>
                            @if($detail->purchase > 0 )
                                <a href="{{route('view',$detail->purchase)}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-eye-alt"></i></a>
                            @endif
                            @if($detail->transfer > 0)
                                <a href="{{url('challandetails',$detail->transfer)}}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-eye-alt"></i></a>
                            @endif
                            @if($detail->purchase == "Sales")
                                <a onclick="getBill('{{$detail->receipt_id}}')" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-eye-alt"></i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="size:54px;font-weight: bold;">Total :</td>
                    <td style="size:54px;font-weight: bold;">{{ $details[0]->balance}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif

            </tbody>
        </table>
    </div>

    <div class="tab-pane" id="messages5" role="tabpanel">
        <br/>
        <h5>Search By Dates</h5>
        <div class="row m-t-20">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-control-label">From Date</label>
                    <input type='text' class="form-control" id="fromdate" name="fromdate" placeholder="DD-MM-YYYY"/>
                    <span class="help-block text-danger" id="rpbox"></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-control-label">To Date</label>
                    <input type='text' class="form-control" id="todate" name="todate" placeholder="DD-MM-YYYY"/>
                    <span class="help-block text-danger" id="rpbox"></span>
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" id="fetch"  class="btn btn-success waves-effect waves-light m-t-25 f-left"  >
                    <i class="icofont icofont-ui-check"> </i>Fetch
                </button>
                <button type="button" id="pdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-l-10 f-left"  >
                    <i class="icofont icofont-ui-check"> </i>PDF
                </button>
            </div>
        </div>
        <hr/>
        <table id="filter" class="table dt-responsive nowrap" width="100%" cellspacing="0">
            <thead>
            <tr>
                <th>Date</th>
                <th>Ref #</th>
                <th>Narration</th>
                <th>Qty</th>
                <th>Stock</th>
                <th>Action By</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @if($report)
                <?php
                  $stock = 0.00;
                 ?>
                @foreach ($report as $value)
                    <?php
                     if($value->narration == 'Stock Opening' ){
                        $stock = (float)$value->stock;
                     }elseif($value->narration == 'Sales'){
                        $stock = $stock - (preg_match('/Sales/', $value->narration) ? (float)$value->qty ?? 1 / $value->weight_qty ?? 1 : (float)$value->qty ?? 1) ;
                     }elseif($value->narration == 'Sales Return'){
                        $stock = (float)$stock + (float)$value->qty;
                     }elseif($value->narration == 'Stock Purchase through Purchase Order'){
                        $stock =(float) $stock + (float)$value->qty;
                     }elseif($value->narration == 'Stock Opening from csv file'){
                        $stock =(float) $stock + (float)$value->qty;
                     }elseif($value->narration == 'Stock Return'){
                        $stock = (float)$stock - (float)$value->qty;
					 }elseif(preg_match('/Stock Adjustment/', $value->narration)){
                        $stock = (float)$stock + (float)$value->qty;
                     }
                     ?>
                    <tr>
                        <td>{{date('d M Y',strtotime($value->date))}}</td>
                        <td>{{$value->grn_id}}</td>
                        <td>{{$value->narration}}</td>
                        <td>{{(preg_match('/Sales/', $value->narration) ? $value->qty/$value->weight_qty : $value->qty)}}</td>
                        <td> {{$stock}}</td>
                        <td> {{$value->fullname}}</td>
                        <td>
                            @if (preg_match('/Purchase/', $value->narration) && $value->adjustment_mode == "")
                                <a href="{{route('view',$value->foreign_id)}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                                  <i class="icofont icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match(	'/Sales Return/', $value->narration))
                                <a href="{{url('sales-return',$value->foreign_id)}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">  <i class="icofont icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Sales/', $value->narration))
                                <a href="{{url('print',Custom_Helper::getReceiptID($value->foreign_id))}}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">  <i class="icofont icofont icofont-printer text-success"></i>
                                </a>
                            @elseif(preg_match('/Stock Opening/', $value->narration))

                            @endif
                        </td>

                    </tr>
                @endforeach
            @endif

            </tbody>
        </table>
    </div>
	
	<div class="tab-pane" id="messages6" role="tabpanel">
        <table id="widget-product-list" class="table dt-responsive nowrap" width="100%" cellspacing="0">
            <thead>
            <tr>
				<th>Date</th>
				<th>Time</th>
                <th>Cost Price</th>
                <th>Actual Price</th>
                <th>Tax Rate</th>
                <th>Tax Amount</th>
                <th>Retail</th>
                <th>Wholesale</th>
                <th>Online</th>
                <th>Discount</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @if($details)
                <?php $qty = 0 ; ?>
                @foreach ($pricelogs as $detail)
                    <tr>
                        <td >{{date("d M y",strtotime($detail->date))}}</td>
                        <td>{{date("h:i a",strtotime($detail->date))}}</td>
                        <td>{{$detail->cost_price}}</td>
                        <td>{{$detail->actual_price  }}</td>
                        <td>{{$detail->tax_rate}}</td>
                        <td>{{$detail->tax_amount}}</td>
                        <td>{{$detail->retail_price}}</td>
                        <td>{{$detail->wholesale_price}}</td>
                        <td>{{$detail->online_price}}</td>
                        <td>{{$detail->discount_price}}</td>
                        <td>
                            @if($detail->status_id == 2 )
                                <span class="text-danger">In-Active</span>
                            @endif
                            @if($detail->status_id == 1 )
                                <span class="text-success">Active</span>
                            @endif
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
    {{--      MODAL START--}}
    <div class="modal fade modal-flex" id="product-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="mod-title" class="modal-title">Receipt Details</h4>
                </div>
                <div class="modal-body">


                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Receipt No :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="receiptno" class="">1234564897978</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Date :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="date" class="">2012-02-12</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Customer Name:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="name" class="">Muhammad Adil Khan</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Contact :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="mobile" class="">0311-1234567</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="f-w-600">Order Type:</label>
                        </div>
                        <div class="col-md-3">
                            <label id="type" class="">Take Away</label>
                        </div>
                        <div class="col-md-3">
                            <label class="f-w-600 f-right">Status :</label>
                        </div>
                        <div class="col-md-3">
                            <label id="status" class="">Pending</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="tablemodal" class="table " width="100%"  cellspacing="0">
                                <thead>
                                <th width="80%">Product Name</th>
                                <th>Qty</th>
                                <th>Amount</th>

                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>

                    <hr/>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Total Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="tamount" class="f-right">10000</label>
                        </div>

                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Advance :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="receive" class="f-right">1000</label>
                        </div>
                        <div class="col-md-6">
                            <label class="f-w-600 f-left">Bal. Amount :</label>
                        </div>
                        <div class="col-md-6">
                            <label id="bal" class="f-right">10000</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!--           <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Add Product</button> -->
                </div>
            </div>
        </div>
    </div>
    {{--      MODAL END--}}
@endsection
@section('scriptcode_three')
    <script type="text/javascript">
		$(".select2").select2();
		$('#fromdate,#todate,#model_due_date').bootstrapMaterialDatePicker({
            format: 'YYYY-MM-DD',
            time: false,
            clearButton: true,

          icons: {
              date: "icofont icofont-ui-calendar",
              up: "icofont icofont-rounded-up",
              down: "icofont icofont-rounded-down",
              next: "icofont icofont-rounded-right",
              previous: "icofont icofont-rounded-left"
            }
        });

        $('#fetch').click(function (e){
            e.preventDefault();
            $.ajax({
                url: "{{url('/stockFilter')}}",
                type: 'POST',
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    id:'{{$product_id}}',
                    from:$('#fromdate').val(),
                    to:$('#todate').val(),
                },
                success: function (result) {
                    $("#filter tbody").empty();
                    $("#filter tbody").append(result.data)
                }
            });
        });

        $('#pdf').click(function (e){
            e.preventDefault();
            if($('#fromdate').val() == "")
            {
                alert("Please Select From Date");
            }
            else if($('#todate').val() == "")
            {
                alert("Please Select To Date");
            }
            else{
                window.location = "{{url('stockReportPDF')}}?from="+$('#fromdate').val()+"&to="+$('#todate').val()+"&id={{$product_id}}"
            }
        });

        function getBill(receipt) {

            $.ajax({
                url: "{{url('/get-order-general')}}",
                type: 'POST',
                dataType:"json",
                async : false,
                data:{_token:"{{ csrf_token() }}",
                    receiptID:receipt,
                },
                beforeSend : function(){
                    // console.log("Data is loading");
                },
                success:function(result){
         
                    $('#product-modal').modal("show");
                    $('#receiptno').html(result[0].receipt_no);
                    $('#date').html(result[0].date);
                    $('#name').html(result[0].name);
                    $('#mobile').html(result[0].mobile);
                    $('#type').html(result[0].order_mode);
                    $('#status').html(result[0].order_status_name);

                    if(type == "Take Away")
                    {
                        $('#tamount').html("Rs. "+result[0].total_amount.toLocaleString());
                        $('#receive').html('0');
                        var balance = parseInt(result[0].total_amount) - parseInt(result[0].receive_amount);
                        $('#bal').html('0');
                    }else{
                        $('#tamount').html("Rs. "+result[0].total_amount.toLocaleString());
                        $('#receive').html("Rs. "+result[0].receive_amount.toLocaleString());
                        var balance = parseInt(result[0].total_amount) - parseInt(result[0].receive_amount);
                        $('#bal').html("Rs. "+balance.toLocaleString());
                    }

                    $.ajax({
                        url: "{{url('/get-items-by-receipt')}}",
                        type: 'POST',
                        dataType:"json",
                        data:{_token:"{{ csrf_token() }}",
                            id:result[0].id,
                        },
                        beforeSend : function(){
                            // console.log("Data is loading");
                        },
                        success:function(result){
                            $("#tablemodal tbody").empty();
                            for(var count =0;count < result.length; count++){
                                $("#tablemodal tbody").append(
                                    "<tr>" +
                                    "<td >"+result[count].product_name+"</td>" +
                                    "<td >"+result[count].total_qty+"</td>" +
                                    "<td '>"+parseInt(result[count].total_amount).toLocaleString()+"</td>" +
                                    "</tr>"
                                )
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection