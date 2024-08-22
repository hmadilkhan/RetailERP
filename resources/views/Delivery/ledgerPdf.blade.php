<html>
<head>
	<title> Invoice </title>
	<style>
		    /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
                **/
                @page {
                	margin: 0cm 0cm;
                }

                /** Define now the real margins of every page in the PDF **/
                body {
                	margin-top: 6cm;
                	margin-left: 0.8cm;
                	margin-right: 2cm;
                	margin-bottom: 2cm;
                }
                /** Define the header rules **/
                header {
                	position: fixed;
                	top: 0.3cm;
                	left: 0cm;
                	right: 0cm;
                	height: 2cm;
                	color: white;
                	text-align: center;
                	line-height: 1.5cm;
                }

                /** Define the footer rules **/
                footer {
                	position: fixed; 
                	bottom: 0cm; 
                	left: 0cm; 
                	right: 0cm;
                	height: 2cm;
                	color: black;
                	text-align: center;
                	line-height: 1.5cm;
                }
                .container{
                	width: 100%;
                }
                .headerImg{
                	width: 20%;
                	margin-left:0.2cm; 
                	float: left;
                }
                .headerText{
                	margin-top:0.8cm;
                   border-left: 1px solid black;
                   width: 50%;
                   float: right;
                   text-align: left;
                   padding-left:10px; 
                   color: black;
                   line-height:20px;
               }
               .contentInfo{
                   text-align: left;
                   width: 40%;
               }
               .bodyStart-1{
                   width: 50%;
                   float: left;   
               }
               .bodyStart-2{
                width: 50%;
                float: right;
                text-align: right;
            }
            .table{
                margin-top: 2cm;
                width: 700px;
                border: 0px !important;
                 border-collapse:separate;
  border-spacing: 0px 3px;
            }
            thead,tbody {
                border: 1px solid black;
            }
            td {
                text-align: center; 
                vertical-align: middle;
            }
            .grossInvoice td{
                border-top: solid black 1px;
            }
        </style>
    </head>
    <body>
       <!-- Define dompdf header and footer blocks before your subject matter content -->
       <header>
          <div class="container">
             <div class="headerImg">
                <img style="margin-left:10px;margin-top: 10px;" src="{{ asset('public/assets/images/company/').'/'.$company[0]->logo }}" width="150" height="150" />	
            </div>
            <div class="headerText">
                <p>
                   <b>Address:</b> {{$company[0]->address}} <br>
                   <b>Email:</b> {{$company[0]->email}} <br> 
                   <b>Contact:</b> {{$company[0]->mobile_contact}}
               </p>
           </div>
       </div>
   </header>

   <footer>
      All Rights Reserved. Copyright © <?php echo date("Y");?> 
  </footer>
  <main>
      <p class="contentInfo">
         <b>Service Provider</b> <br>
         {{$data[0]->provider_name}}
     </p>
     <hr>
     <div class="bodyStart-1">
        <p>
            <b>Invoice No:</b> {{$data[0]->id}} <br>
            <!-- <b>Customer Code:</b> 111 <br> -->
        </p>
    </div>
    <div class="bodyStart-2">
        <p>
            <b>Invoice date:</b> {{$to}} <br>
            <!-- <b>Invoice no:</b> 111 <br> -->
        </p>
    </div>
    <div>
        <p>Dear sir / Madam, <br>
            Our service for the peroid {{$from}} to {{$to}} as follows:
        </p>

    </div>
    <table class="table" >
     <thead>
        <tr>
           <th>Item Description</th>
           <th>Quantity</th>
           <th>Price</th>
           <th>Comm Base</th>
           <th>Total Value</th>
       </tr>
   </thead>
   <tbody>
    <?php $totalValue = 0; $gst = 0; ?>
    @foreach($data[0]->additional_charge as $value)
      @if($value->type == 'other'  )
      <?php $totalValue += $value->chargeValue; ?>
      <tr>
        <td>{{$value->chargeName}}</td>
       <td></td>
       <td> </td>
       <td></td>
       <td>{{$value->chargeValue}}</td>
     </tr>
      @elseif($value->type == 'commission')
      <?php $totalValue += (Custom_Helper::getComissionSalesTotalAmount($value->chargeValue,$from,$to,$provider_id) * $value->chargeValue)/100; ?>
        <tr>
        <td>{{$value->chargeName}}</td>
        <td></td>
        <td></td>
       <td >{{number_format(Custom_Helper::getComissionSalesTotalAmount($value->chargeValue,$from,$to,$provider_id),2)}}</td>
       <td>{{number_format((Custom_Helper::getComissionSalesTotalAmount($value->chargeValue,$from,$to,$provider_id) * $value->chargeValue)/100,2)}}</td>
   </tr>
      @endif
    @endforeach
    
   
   <tr class="grossInvoice">
        <td colspan="4" style="text-align:right !important;padding-right:10px;font-weight:bold">Net Tax Base</td>
        <td>{{number_format($totalValue,2)}}</td>
   </tr>
   <tr class="grossInvoice">
        <?php $gst = ($totalValue * $data[0]->percentage)/100 ?>  
        <td colspan="2" style="text-align:right !important;padding-right:10px;font-weight:bold">Net Tax Base</td>  
        <td>{{number_format($totalValue,2)}}</td>
        <td>GST {{$data[0]->percentage}}</td>
        <td>{{number_format(($totalValue * $data[0]->percentage)/100 
        ,2)}}</td>
   </tr>
    <tr class="grossInvoice">
       <td colspan="4" style="text-align:right !important;padding-right:10px;font-weight:bold">Gross Invoice Total</td>
       <td>{{number_format($totalValue + $gst,2)}}</td>
   </tr>
</tbody>
</table>
</main>
</body>
</html>