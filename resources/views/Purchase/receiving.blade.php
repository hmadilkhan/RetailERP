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
                <h2 class="card-header-text">Create Purchase Orders <span id="poNumber"></span></h2>
            </div>
         <div class="card-block">

        <input type="hidden" id="hidd_amt" name="hidd_amt">
           <input type="hidden" id="id" name="id" value="">
           <input type="hidden" id="tax_value" name="tax_value" value="">
           <input type="hidden" id="edit_id" name="edit_id" value="">
            <div class="row">
                <!-- vendor select box -->
                <div class="col-lg-4 col-sm-12">
                    <div class="form-group">
                        <label class="form-control-label">Vendor</label>
                        <select class="form-control select2" data-placeholder="Select Vendor" id="vendor" name="vendor">
                            <option value="">Select Vendor</option>
                             @if($vendors)
                              @foreach($vendors as $val)
                                <option value="{{$val->id}}">{{ $val->vendor_name }}</option>
                              @endforeach
                             @endif
                        </select>
                        <span class="help-block text-danger" id="vdbox"></span>
                    </div>
                </div>
                <!-- Date -->
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="form-control-label">Date</label>
                        <input type='text' class="form-control" id="date" name="date" placeholder="DD-MM-YYYY"/>  
                        <span class="help-block text-danger" id="dbox"></span>  
                    </div>
                </div>  
                <!-- Receipt Date -->
                <div class="col-lg-4">
                  <div class="form-group">
                    <label class="form-control-label">Receipt Date</label>
                            <input type='text' class="form-control" id="rpdate" name="rpdate" placeholder="DD-MM-YYYY"/>  
                            <span class="help-block text-danger" id="rpbox"></span>  
                    </div>
                </div>  
             </div>
             <div class="row">

                <!-- reference -->
                <div class="col-lg-4">
                    <div class="form-group">
                      <label class="form-control-label">Reference</label>
                      <input type="text" id="ref" name="ref" class="form-control" />
                      <span class="help-block text-danger" id="refbox"></span> 
                    </div>
                </div>

                   <!-- Tax select box -->
                <div class="col-lg-4  col-sm-12">
                    <div class="form-group">
                          <label class="form-control-label">Tax</label>
                           <select class="select2 form-control" onchange="taxchange()" data-placeholder="Select Tax" id="tax" name="tax">
                            @if($tax)
                              @foreach($tax as $val)
                                <option value="{{$val->id}}">{{$val->name.' '.$val->value.'%'}}</option>
                              @endforeach
                             @endif
                           </select>
                        <span class="help-block text-danger" id="taxbox"></span> 
                    </div>       
                 </div>        
                 <!-- branch select box -->
                <div class="col-lg-4  col-sm-12">
                    <div class="form-group">
                        <label class="form-control-label">Branch</label>
                        <select class="form-control select2" data-placeholder="Select Branch" id="branch" name="branch">
                            <option >Select Branch</option>
                             @if($branch)
                                  @foreach($branch as $val)
                                  @if($val->branch_id == $lg_branchid)
                                     <option selected value="{{$val->branch_id}}">{{$val->branch_name}}</option>
                                  @else
                                     <option  value="{{$val->branch_id}}">{{$val->branch_name}}</option>
                                  @endif
                                   
                                  @endforeach
                             @endif
                        </select>
                         <span class="help-block text-danger" id="brbox"></span> 
                    </div>
                </div>
            </div>
       
       <hr> <!-- product module -->

          <div class="card-header">
                <h5 class="card-header-text">ADD PRODUCT </h5>
            </div>
 
       
            <div class="row">
                 <!-- product select box -->
                <div class="col-lg-3  col-sm-12">
                    <div class="form-group">
                         <label>Product</label>
                           <select class="select2 form-control" data-placeholder="Select Product" id="product" name="product">
                            <option value="">Select Product</option>
                             @if($products)
                                  @foreach($products as $val)
                                    <option value="{{$val->id}}">{{$val->product_name}}</option>
                                  @endforeach
                             @endif  
                           </select>
                        <span class="help-block"></span>
                    </div>
                </div>  

                 <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Unit of Measure</label>
                           <select class="select2 form-control" data-placeholder="UOM" id="uom" name="uom">
                            <option value="">UOM</option>
                            @if($uom)
                                  @foreach($uom as $val)
                                    <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                  @endforeach
                             @endif
                           </select>
                        <span class="help-block"></span>
                    </div>       
                </div>
                 <!-- qty select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Quantity</label>
                           <input type="text" placeholder="0" name="qty" id="qty" class="form-control" onchange="qty_change()"  />
                        <span class="help-block"></span>
                    </div>
                </div>  
                 <!-- price select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Price</label>
                           <input type="text" placeholder="0" name="price" id="price" class="form-control" onchange="price_change()" />
                        <span class="help-block"></span>
                    </div>       
                </div> 

                <!-- Amount box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Amount</label>
                           <input type="text" name="amount" placeholder="0" id="amount" class="form-control" />
                        <span class="help-block"></span>
                    </div>       
                </div> 
                   <!-- button  -->
                <div class="col-lg-1  col-sm-12">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25">
                                  <i class="icofont icofont-plus"> </i>
                            </button>
                    </div>       
                </div>             
             </div>   
        
<hr>
        <table id="item_table" class="table invoice-detail-table" width="100%"  cellspacing="0">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>



<div class="row m-t-40">
    <div class="col-lg-8">
        <div class="form-group">
            <textarea class="form-control" rows="3" placeholder="Comments" id="comments" name="comments"></textarea>
        </div>

    </div>
    <div class="col-lg-4 col-sm-12">
       <table id="" class="table table-responsive invoice-table invoice-total">
          <tbody>
             <tr>
                <th>Gross :</th>
                <td id="gross">0.00</td>
             </tr>
             <tr>
                <th>Taxes (<span id="txper">0%</span>) :</th>
                <td id="taxAmount">0.00</td>
             </tr>
             <tr class="txt-info">
                <th><h5>Net Amount</h5></th>
                <td><h5 id="net_amount">0.00</h5></td>
             </tr>
          </tbody>
       </table>
    </div>
 </div> 

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

    
   
  var po = "";
        $('#vendor').change(function(){
             $.ajax({
                 url:'{{ url("/getPurchaseMax") }}',
                 type:"GET",
                 success:function(result){
                    var res = parseInt(result) + 1;
                    $('#id').val(res);
                    po = "PO-"+res;
                    $('#poNumber').html("- "+po);
                    $.ajax({
                        url : "{{url('/create-purchases')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}", po_number:po,vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val()},
                        success : function(result){
                            
                            
                         }
                    });
                  }
                });
        });
              
          

        $('#btnSubmit').click(function(){
            if($('#product').val() == ""){
                alert("Select Product First ");
            }else if($('#uom').val() == ""){
                alert("Select Unit of Measure First ");
            }else if($('#quantity').val() == ""){
                alert("Select Quantity First ");
            }else if($('#price').val() == ""){
                 alert("Select Price First ");
            }else{
                if(mode == "insert"){
                    $.ajax({
                        url : "{{url('/purchases')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}",vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),product:$('#product').val(),unit:$('#uom').val(),quantity:$('#qty').val(),price:$('#price').val(),total_amount:$('#amount').val(),ID:$('#id').val()},
                        success : function(result){
                             var getid = $('#id').val();
                             getAccounts(getid);
                             getAccDetails(getid);
                            amount = amount + parseFloat($('#amount').val());
                            count = count + 1;
                            $("#product").val('').change();
                            $("#uom").val('').change();
                            $("#qty").val('');
                            $("#price").val('');
                            $("#discamount").val('');
                            $("#amount").val('');
                           
                         }
                    });
                }else{
                    $.ajax({
                        url : "{{url('/updateitems')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}",product:$('#product').val(),unit:$('#uom').val(),quantity:$('#qty').val(),price:$('#price').val(),total_amount:$('#amount').val(),ID:$('#edit_id').val()},
                        success : function(result){
                            mode = "insert";
                            var getid = $('#id').val();
                            getAccounts(getid);
                            getAccDetails(getid);
                            $("#product").val('').change();
                            $("#uom").val('').change();
                            $("#qty").val('');
                            $("#price").val('');
                            $("#discamount").val('');
                            $("#amount").val('');
                           
                         }
                    });
                }
                 
                }
        });

   
    function taxchange(){

        $('#txper').text($("#tax option:selected").text());
        var str = $("#tax option:selected").text();
        var matches = str.match(/(\d+)/); 
        $('#tax_value').val(matches[0]);
        calc = "";
        calc = ((parseFloat($('#gross').html()) / 100) * matches[0]);
       
        $('#hidd_amt').val(calc);
        $('#taxAmount').html(calc);
        var net = parseFloat($('#gross').html()) + parseFloat(calc);
        $('#net_amount').html(net);
     }

     function qty_change(){
        if($('#price').val() != ""){
            var amount = parseFloat($('#qty').val()) * parseFloat($('#price').val());
            $('#amount').val(amount);
        }
     }

    function price_change(){
            var gross = $('#qty').val() * $('#price').val();
            $('#amount').val(gross);
    }

    function setValues(gross){
        var gross_total ;
    
        gross_total = amount + gross;
        calc = ((parseFloat(gross_total) / 100) * $('#tax_value').val());


        if($('#tax_value').val() != ""){
            gross_total = gross_total + parseFloat($('#hidd_amt').val());
        }

        $('#gross').html(gross_total);
        $('#taxAmount').html(calc);
        $('#net_amount').html(gross_total+calc);

    }
    function getAccounts(id){
        $.ajax({
                url : "{{url('/getAccounts')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", id:id},
                dataType : 'json',
                success : function(result){
                    $("#item_table tbody").empty();
                    $.each(result, function( index, value ) {
                        $("#item_table tbody").append(
                          "<tr>" +
                            "<td>"+value.product_name +"</td>" +
                            "<td>"+value.quantity +"</td>" +
                            "<td>"+value.price+"</td>" +
                            "<td>"+value.total_amount+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItem("+value.p_item_details_id+","+value.id+","+value.unit+","+value.quantity+","+value.price+","+value.total_amount+")' class='icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"' onclick='deleteItem("+value.p_item_details_id+")' class='icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                    });
                }
                });
    }

    function updateItem(id,itemid,uom,qty,price,amount){
         mode = "update";
         $("#product").val(itemid).change();
         $("#uom").val(uom).change();
         $('#edit_id').val(id);
         $('#qty').val(qty);
         $('#price').val(price);
         $('#amount').val(amount);
    }

    function deleteItem(id){
       
    }

    function getAccDetails(id){

        $.ajax({
                url : "{{url('/AccDetails')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", id:id},
                success : function(result){
                    if($('#tax_value').val() == ""){
                        taxVal = 0;
                    }else{
                        taxVal = $('#tax_value').val();
                    }
                    $('#gross').html(result);
                    calc = ((parseFloat(result) / 100) * parseInt(taxVal));
                    $('#taxAmount').html(calc);
                    var sum = parseFloat(calc) + parseFloat(result);
                    $('#net_amount').html(sum);

                }
                });
    }

    $('#btnFinalSubmit').click(function(){
  
        if($('#vendor').val() == ""){
            //changeClass('vdbox','Alert! Require field.',1);
        }else if($('#date').val() == ""){
             //changeClass('dbox','Alert! Require field.',1);
        }else if($('#rpdate').val() == ""){
             //changeClass('rpbox','Alert! Require field.',1);
        }else if($('#ref').val() == ""){
           // changeClass('refbox','Alert! Require field.',1);
        }else{
           $.ajax({
            url : "{{url('/FinalSubmit')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}",vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),total_amount:$('#gross').html(),taxAmount:$('#taxAmount').html(),net_amount:$('#net_amount').html(),ID:$('#id').val()},
            success : function(result){
                swal_alert("Success!","Purchase Order Created Successfully","success",true);
            }
            });
        }
    });

    function changeClass(id,msg,mode){
      if(mode == 1){
        $('#'+id).html(msg);
      }else{
        $('#'+id).html('');
      }
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
</script>
@endsection
