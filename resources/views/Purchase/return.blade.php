@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','Add Purchase Order')
@section('navVendorPO','active')
@section('navpurchase','active')
@section('nav_addpurchase','active')

@section('content')

<section class="panels-wells">
    <div class="card">
          <div class="card-header">
                <label class="f-30">Create Return Note </label>

                 <hr>
            </div>
           
         <div class="card-block">

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
                                    <th>Status :</th>
                                    <td>
                                       @if($general[0]->status_id == 1)
                                        <span class="tag tag-warning">  Draft</span>
                                      @elseif($general[0]->status_id== 1)
                                        <span class="tag tag-success">  Placed</span>
                                      @elseif($general[0]->status_id == 3)
                                         <span class="tag tag-info">  Received</span>
                                      @elseif($general[0]->status_id == 4)
                                        <span class="tag tag-danger">  Cancelled</span>
                                      @endif
                                       
                                    </td>
                                 </tr>
                                

                              </tbody>
                           </table>
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Purchase Order Number : <span>{{$general[0]->po_no }}</span></h6>
                           <h6 class="text-uppercase txt-info">Total Due :
                                    <span>Rs. {{number_format($accounts[0]->net_amount,2)}}</span>
                                </h6>
                           <h6 class="">Comments : {{$general[0]->comments}}</h6>

                        </div>
                     </div>
       
       <hr> <!-- product module -->
       <div class="row ">
        <div class="col-lg-10"></div>
            <div class="col-lg-2 col-sm-12 ">
                    <div class="form-group ">
                        <label class="form-control-label">Select Return Mode</label>
                        <select class="form-control select2 pull-right" data-placeholder="Select Return Mode" id="mode" name="mode">
                            <option value="">Select Mode</option>
                             <option value="1">Replacement</option>
                             <option value="2">Complete Return</option>
                             <option value="3">Partial Return</option>
                            
                        </select>
                        <span class="help-block text-danger" id="vdbox"></span>
                    </div>
                </div>
       </div>
 <hr> <!-- product module -->
        <table id="item_table" class="table invoice-detail-table" width="100%"  cellspacing="0">
            <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity Received</th>
                    <th>Return Quantity</th>
                     <th>Total Price</th>
                    <th>Narration</th>
                </tr>
            </thead>
         
            <tbody>
             
               
            </tbody>
        </table>
<!-- 
         <table id="return_table" class="table invoice-detail-table" width="100%"  cellspacing="0">
            <thead>
                <tr>
                    <th style="display: none;">ID</th>
                    <th>Item Code</th>
                    <th>Product</th>
                    <th>Unit Of Measure</th>
                    <th>Quantity</th>
                    <th>Quantity Received</th>
                    <th>Unit Price</th> 
                    <th>Total Amount</th>
                </tr>
            </thead>
         
            <tbody>
             
               
            </tbody>
        </table>
 -->
 <div class="row">
      <div class="col-lg-12 col-sm-12 ">
            <div class="form-group ">
                <button type="button" id="btnFinalSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25 f-right"  >
                      <i class="icofont icofont-ui-check"> </i>Return
                </button>
            </div>       
        </div>  
 </div>  

        <div class="row col-md-6">
              <table class="table">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>GR No</th>
                           <th>Created</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                      <?php $count = 1; ?>
                       @foreach ($GRN as $value)
                         <tr>
                           <td>{{$count++}}</td>
                           <td>{{$value->GRN }}</td>
                           <td>{{$value->created_at }}</td>
                           <td><button type="button" id="getItems" onclick="getAccounts('{{$value->rec_id }}')"  class="crm-action-edit"><i class="icofont icofont-ui-check"></i></button></td>
                        </tr>
                        @endforeach
                     </tbody>
              </table>
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



function edit(rec,qty){
  $('#'+rec).removeAttr("disabled");
  $('#'+qty).removeAttr("disabled");
  $('#edit').css('display','none');
  $('#save').css('display','block');
}

function save(qty,rp,id,rec,rp){



}


    
   var total_amount = 0.00;
   var purchase_status = 0;
   var difference = 0.00;
   var status = 0;
   var po = "";
    $('#btnFinalSubmit').click(function(){
       po = '{{$general[0]->purchase_id}}';

      if($('#mode').val() == ""){
        swal_alert("Error Message !","Please Select Mode First","error",false)
      }else{
      $("#item_table tbody tr ").each(function(k){
          myVal = [];
          $(this).find("input,label").each(function(i){
            if($(this).text() != ""){
              myVal.push($(this).text());
            }else{
              myVal.push($(this).val());
            }
         });
        
        difference = parseFloat(myVal[3]) -  parseFloat(myVal[4]);
        total_amount = total_amount + parseInt(myVal[5]);

        if($('#mode').val() == 1){
          status = 11;
        }else if($('#mode').val() == 3){
          status = 5;
        }else{
          status = 6;
        }
        var price = parseFloat(myVal[2]) *  parseFloat(myVal[4]);
        var qty = myVal[4]; 
        if(qty > 0){
          
        $.ajax({
          url : "{{url('/returnInsert')}}",
          type : "POST",
          data : {_token : "{{csrf_token()}}",po:po,GRN:myVal[8],rec_details_id:myVal[9],status:status,mode:$('#mode').val(),itemid:myVal[6],rec:myVal[3],uom:myVal[7],qty:qty,amount:price,stockid:myVal[0],narration:myVal[11]},
            success : function(result){
              if($('#mode').val() == 1){
                swal_alert("Success!","Purchase Order Return Successfully","success",true); 
              }
             
          }
        });
        }
      });    

      if($('#mode').val() == 2){
      $.ajax({
            url : "{{url('/AccountUpdate')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}",po:po},
              success : function(result){
                swal_alert("Success!","Purchase Order Return Successfully","success",true);
            }
          });
      }else{
          window.location = "{{url('/view-purchases')}}";
      }
    }

      if(difference == 0){
          purchase_status = 6;
        }else{
          purchase_status = 5;
      }

      // if($('#mode').val() == 2){
        
      // }
   
});

function getItems(poid,itemid,qty){
  var dbresult;
   $.ajax({
          url : "{{url('/getReceive')}}",
          type : "POST",
          data : {_token : "{{csrf_token()}}",po:poid,itemid:itemid,qty:qty},
          dataType : 'json',
            success : function(result){
             // console.log(result)
              
          }
        });
}


function getAccounts(id){

        $.ajax({
                url : "{{url('/get')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", grn:id},
                dataType : 'json',
                success : function(result){
                  console.log(result);
                     $("#item_table tbody").empty();

                    $.each(result, function( index, value ) {
            
                        $("#item_table tbody").append(
                          "<tr>" +
                            "<td style='display: none;'><label>"+value.stock_id +"</label></td>" +
                            "<td><label>"+value.product_name +"</label></td>" +
                            "<td><label>"+value.cost_price+"</label></td>" +
                            "<td><label>"+value.balance+"</label></td>" + 
                            ($('#mode').val() == 2 ? "<td ><input disabled='disabled' type='text' class='form-control' value='"+value.balance+"' /></td>" : "<td ><input id='qty"+value.stock_id +"'  type='text' class='form-control' value='"+value.balance+"' onchange='chkQty("+value.qty+",this.id)' /></td>" )+
                            "<td ><label>"+value.total_amount * value.qty+"</label></td>" +
                            "<td style='display: none;'><label>"+value.product_id+"</label></td>" +
                            "<td style='display: none;'><label>"+value.uom+"</label></td>" +
                            "<td style='display: none;'><label>"+value.grn_id+"</label></td>" +
                            "<td style='display: none;'><label>"+value.rec_details_id+"</label></td>" +
                            "<td ><label><input type='text' name='narration' value='' class='form-control' id='narration'/></label></td>" +
                          "</tr>"
                         );
                
                    });
                }
                });
}

 
                   
           
function getStockforCompleteReturn(){

        $.ajax({
                url : "{{url('/CompleteReturnPO')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", po:'{{$purchaseID}}'},
                dataType : 'json',
                success : function(result){
                  console.log(result);
                     $("#return_table tbody").empty();

                    $.each(result, function( index, value ) {
            
                        $("#return_table tbody").append(
                          "<tr>" +
                            "<td style='display: none;'><label>"+value.p_item_details_id +"</label></td>" +
                            "<td><label>"+value.item_code +"</label></td>" +
                            "<td><label>"+value.product_name+"</label></td>" +
                            "<td><label>"+value.unit+"</label></td>" + 
                            "<td ><label>"+value.quantity+"</label></td>" +
                            "<td ><label>"+value.received+"</label></td>" +
                            "<td ><label>"+value.price+"</label></td>" +
                            "<td ><label>"+value.total_amount +"</label></td>" +
                          "</tr>"
                         );
                    });
                }
                });
}

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

function chkQty(qty,bal)
{

  if($('#'+bal).val() > qty){
    swal_alert("Error Message !","Cannot Insert greater Qty","error",false)
    $('#'+bal).val(qty);
  }
}

$('#mode').change(function(){
   if($('#mode').val() == 2)
   {
      // getStockforCompleteReturn();
   }
});
    
</script>
@endsection
