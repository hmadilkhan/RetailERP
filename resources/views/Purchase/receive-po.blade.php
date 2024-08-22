@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','Add Purchase Order')
@section('navVendorPO','active')
@section('navpurchase','active')
@section('nav_addpurchase','active')

@section('content')
@include('partials.loader')
<section class="panels-wells">
    <div class="card">
          <div class="card-header">
                <label class="f-30">Create Good Receiving Note </label>

                 <hr>
            </div>
           
         <div class="card-block">
          <input type="hidden" name="grn_id" id="grn_id">
        <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                           <h6>Vendor Information :</h6>
                           <h6>{{$general[0]->vendor_name}}</h6>
                           <p>{{$general[0]->address}}</p>
                           <p>{{$general[0]->vendor_contact}}</p>
                           <p>{{$general[0]->vendor_email}}</p>
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
                                    <th>Status :</th>
                                    <td>
                                      @if($general[0]->name == "Draft")
                                        <span class="tag tag-warning">  {{$general[0]->name }}</span>
                                      @elseif($general[0]->name== "Placed")
                                        <span class="tag tag-success">  {{$general[0]->name }}</span>
                                      @elseif($general[0]->name == "Received")
                                         <span class="tag tag-info">  {{$general[0]->name }}</span>
                                      @elseif($general[0]->name == "Cancelled")
                                        <span class="tag tag-danger">  {{$general[0]->name }}</span>
                                      @endif
                                       
                                    </td>
                                 </tr>
                                

                              </tbody>
                           </table>
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Purchase Order Number : <span>{{$general[0]->po_no }}</span></h6>
                           <h6 class="text-uppercase ">Total Due :
                                    <span>Rs. {{number_format($accounts[0]->balance_amount,2)}}</span>
                                </h6>
                           <h6 class="">Comments : {{$general[0]->comments}}</h6>

                        </div>
                     </div>
       
       <hr> <!-- product module -->

 
        <table id="item_table" class="table invoice-detail-table" width="100%"  cellspacing="0">
            <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Rec. Quantity</th>
{{--                    <th>Retail Price</th>--}}
{{--                    <th>Wholesale</th>--}}
{{--                    <th>Discount</th>--}}
                    <!-- <th>Total Amount</th> -->
                    
                </tr>
            </thead>
            <tbody>

             @if($general[0]->po_status_id == 7 || $general[0]->po_status_id == 5 || $general[0]->po_status_id == 2  ) 
                  @if(count($itemReceived) > 0)
                  @foreach ($itemReceived as $value)
                  @php $qty = $general[0]->po_status_id == 5  ?  ( $value->qty_return) : ( $value->quantity - $value->received ) @endphp
                   <tr>
                     <td style="display: none;"><label>{{$value->item_code}}</label></td>
                     <td style="display: none;"><label>{{$value->unit}}</label></td>
                     <td><label>{{$value->item_code.' - '.$value->product_name }}</label></td>
                     <td><label>{{$value->price }}</label></td>
                     <td><label>{{$qty }}</label></td>
                     <td><input class="form-control" id="rec{{$value->item_code}}" onchange="checkQty('{{$qty }}','{{$qty }}',this.id)" type="text" value="{{$qty}}" name="" {{ $qty == 0 ? 'disbaled="disabled"' : "" }} /></td>
                     <td style="display: none;"><input class="form-control" id="rp" value="{{($value->retail == '' ? 0 : $value->retail) }}"   type="text" name="" {{ $qty == 0 ? 'disbaled="disabled"' : "" }} /></td>
                     <td style="display: none;"><input class="form-control" id="wh" value="{{($value->wholesale == '' ? 0 : $value->wholesale) }}" type="text" name="" {{ $qty == 0 ? 'disbaled="disabled"' : "" }}  /></td>
                     <td style="display: none;"><input class="form-control" id="dis" value="0" type="text" name="" {{ $qty == 0 ? 'disbaled="disabled"' : "" }} /></td>
                     <td style="display: none;"><label>{{$value->p_item_details_id }}</label></td>
                     <td style="display: none;"><label>{{$value->total_amount}}</label></td>
                   </tr>
                 @endforeach
                 @else
                  @foreach ($receive as $value)
                   <tr>
                     <td style="display: none;"><label>{{$value->id}}</label></td>
                     <td style="display: none;"><label>{{$value->unit}}</label></td>
                     <td><label>{{$value->item_code.' - '.$value->product_name }}</label></td>
                     <td><label>{{$value->price }}</label></td>
                     <td><label>{{$value->quantity }}</label></td>
                     <td><input class="form-control" id="rec{{$value->item_code}}" onchange="checkQty('{{$value->quantity }}','{{$value->quantity }}',this.id)" value="{{$value->quantity }}"  type="text" name=""  /></td>
                     <td style="display: none;"><input class="form-control" id="rp"  value="{{($value->retail == '' ? 0 : $value->retail) }}"  type="text"  name=""/></td>
                     <td style="display: none;"><input class="form-control" id="wh"  value="{{($value->wholesale == '' ? 0 : $value->wholesale) }}" type="text" name=""  /></td>
                     <td style="display: none;"><input class="form-control" id="dis" value="0" type="text"  name=""/></td>
                     <td style="display: none;"><label>{{$value->p_item_details_id }}</label></td>
                     <td style="display: none;"><label>{{$value->total_amount}}</label></td>
                   </tr>
                  @endforeach
                 @endif
              @else
                  @foreach ($receive as $value)
                   <tr>
                     <td style="display: none;"><label>{{$value->id}}</label></td>
                     <td style="display: none;"><label>{{$value->unit}}</label></td>
                     <td><label>{{$value->item_code.' - '.$value->product_name }}</label></td>
                     <td><label>{{$value->price }}</label></td>
                     <td><label>{{$value->quantity }}</label></td>
                     <td><input class="form-control" id="rec{{$value->item_code}}" onchange="checkQty('{{$value->quantity }}','{{$value->quantity }}',this.id)" value="{{$value->quantity }}"  type="text" name=""  /></td>
                     <td style="display: none;"><input class="form-control" id="rp"  value="{{($value->retail == '' ? 0 : $value->retail) }}"  type="text"  name=""/></td>
                     <td style="display: none;"><input class="form-control" id="wh"  value="{{($value->wholesale == '' ? 0 : $value->wholesale) }}" type="text" name=""  /></td>
                     <td style="display: none;"><input class="form-control" id="dis" value="0" type="text"  name=""/></td>
                     <td style="display: none;"><label>{{$value->p_item_details_id }}</label></td>
                     <td style="display: none;"><label>{{$value->total_amount}}</label></td>
                   </tr>
                  @endforeach
              @endif
            </tbody>
        </table>


 <div class="row">
      <div class="col-lg-12 col-sm-12 ">
            <div class="form-group ">
                <button type="button" id="btnFinalSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25 f-right"  >
                      <i class="icofont icofont-plus"> </i>Submit
                </button>
            </div>       
        </div>  
 </div>  


           </div>
        </div>
    </section>   
@endsection


@section('scriptcode_three')
<script type="text/javascript">
    $(".select2").select2();
     $('#date,#rpdate').bootstrapMaterialDatePicker({
            format: 'DD-MM-YYYY',
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

    var count = 0;
    var amount = 0 ;
    var mode = "insert";

    var grn = "";

  function checkQty(qty,rec,id){

    if(parseInt($('#'+id).val()) > qty){
       swal_alert("Alert!","Cannot receive more than the Required Qty","error",false);
      $('#'+id).focus();
      $('#'+id).val(qty);
      }
  }

  function edit(rec,qty){

    $('#'+rec).removeAttr("disabled");
    $('#'+qty).removeAttr("disabled");
    $('#edit').css('display','none');
    $('#save').css('display','block');
  }


  var po_status = 0;
  var po = '{{$general[0]->purchase_id}}';
 
    $('#btnFinalSubmit').click(function(){
	   $('#btnFinalSubmit').attr('disabled','disabled');
	   $('#loader').removeClass('hidden')
       $.ajax({
            url : "{{url('/create-grn')}}",
            type : "POST",
            async : false,
            data : {_token : "{{csrf_token()}}"},
              success : function(result){
                $('#grn_id').val(result);
            }
          });

      $("#item_table tbody tr ").each(function(k){
          myVal = [];
          $(this).find("input,label").each(function(i){
            if($(this).text() != ""){
              myVal.push($(this).text());
            }else{
              myVal.push($(this).val());
            }
         });
          // console.log(myVal[10]);return false;
          var difference = myVal[4] - myVal[5]; //Checking Difference
          if(difference != 0){
            po_status = po_status + 1;
          }

          //Insert into GRN Item Details
          
            $.ajax({
              url : "{{url('/add-grn')}}",
              type : "POST",
              async : false,
              data : {_token : "{{csrf_token()}}",po:po,grn:$('#grn_id').val(),itemid:myVal[0],rec:myVal[5],uom:myVal[1],cp:myVal[10],rp:myVal[6],wp:myVal[7],dp:myVal[8],item_details_id:myVal[9],branch:"{{$general[0]->branch_id}}"},
                success : function(result){
                 //window.location = "/erp/view-purchases";
              }
            });
         
        });  
        //Change Purchase Order Status if it is Not Completely Received
  
         $.ajax({
            url : "{{url('/changeStatusPo')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}",po:po,status:po_status},
            success : function(result){
				  
               window.location = "{{ url('/view-purchases') }}";
            },
			complete: function() {
				$('#loader').addClass('hidden')
				$("#btnFinalSubmit").attr("disabled", false);
			},
          });
        
    });

function swal_alert(title,msg,type,mode){
    
      swal({
            title: title,
            text: msg,
            type: type
         },function(isConfirm){
         if(isConfirm){
            if(mode === true){
              window.location = "{{url('/view-purchases')}}";
            }
          }
      });
}


    
</script>
@endsection
