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
                          <i id="btn_ven" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Vendor" ></i>
                        <select class="form-control select2" data-placeholder="Select Vendor" id="vendor" name="vendor">
                            <option value="">Select Vendor</option>
                             @if($vendors)
                              @foreach($vendors as $val)
                                <option value="{{$val->id}}">{{ $val->vendor_name." - " }}<b class="f-w-900">{{$val->company_name }}</b></option>
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
                    <label class="form-control-label">Delivery Date</label>
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
                        <select  class="form-control select2" data-placeholder="Select Branch" id="branch" name="branch">
                            <option value="">Select Branch</option>
                             @if($branch)
                                  @foreach($branch as $val)
                                  @if($val->branch_id == $lg_branchid)
                                     <option selected value="{{$val->branch_id}}">{{$val->branch_name}}</option>
                                  @else
                                     <option value="{{$val->branch_id}}">{{$val->branch_name}}</option>
                                  @endif
                                   
                                  @endforeach
                             @endif
                        </select>
                         <span class="help-block text-danger" id="brbox"></span> 
                    </div>
                </div>
            </div>
            <div class="row">
              <div class="col-lg-2  col-sm-12">
                <div class="form-group">
                  <label class="form-control-label">Discount By</label>
                  <select onchange="taxchange()" class="form-control" id="discount_by" name="discount_by" style="height: 2.2rem !important" >
                    <option value="1" selected>Percentage</option>
                    <option value="2">Amount</option>
                  </select>
                </div>
              </div>
            	<!-- <div class="col-lg-2  col-sm-12"> -->
                    <!-- <div class="form-group"> -->
                        <!-- <label class="form-control-label">PO Discount</label> -->
                      	<input type="hidden" onkeyup="taxchange()" id="discount" name="discount" class="form-control" value="0" />
                        <!-- <span class="help-block text-danger" id="taxbox"></span>  -->
                    <!-- </div>        -->
                <!-- </div>  -->

                 <div class="col-lg-4  col-sm-12">
                    <div class="form-group">
                        <label class="form-control-label">Other Expenses</label>
                      	<input type="text" onkeyup="otherExpenses()" id="delivery" name="delivery" class="form-control" value="0.00" />
                        <span class="help-block text-danger" id="taxbox"></span> 
                    </div>       
                </div>

                <div class="col-lg-4 col-sm-12">
                    <div class="form-group">
                        <label class="form-control-label">Payment Date</label>
                        <input type='text' class="form-control" id="payment" name="payment" placeholder="DD-MM-YYYY"/>
                        <span class="help-block text-danger" id="rpbox"></span>
                    </div>
                </div>

            </div>
       
       <hr> <!-- product module -->
          <div class="card-header">
                <h5 class="card-header-text">ADD PRODUCT </h5>
            </div>
             <div class="alert alert-primary" style="background-color: #cce5ff">
                <strong>Note :</strong> Tax & discount amount displaying for single item, (e.g; Tax X Qty = Total tax)
            </div>
            <div class="row">
               <!-- Per Item Tax -->
               <div class="col-lg-1  col-sm-12">
                    <div class="form-group">
                         <label>Item Tax</label>
                           <input type="checkbox" onclick="setPerItemTax(this)" class="form-control" id="per-item-tax-allow" name="per_item_tax_allow" value="" />
                        <span class="help-block"></span>
                    </div>
                </div> 
                  <!-- product select box -->
                   <div class="col-lg-4  col-sm-12">
                    <div class="form-group">
                         <label>Product</label>
						 <i id="btn_vendor_product" class="icofont icofont-plus f-right text-success" data-toggle="tooltip" data-placement="top" title="Add Vendor Product" ></i>
                           <select class="select2 form-control" data-placeholder="Select Product" id="product" name="product">
                                <option value="">Select Product</option>
                           </select>
                        <span class="help-block"></span>
                    </div>
                </div>  
                  <!-- Batch No -->
                  <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Batch No</label>
                           <input type="text" name="batch_no" id="batch-no" value="" class="form-control" placeholder="BATCH NO" />
                        <span class="help-block"></span>
                    </div>
                </div>
                  <!-- Expiry date -->
                  <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Expiry Date</label>
                         <input type='text' class="form-control" value="" id="expiry-date" name="expiry_date" placeholder="DD-MM-YYYY"/>  
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
                <div class="clearfix"></div>
                 <!-- price select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Unit Price</label>
                           <input type="text" onkeypress="return isNumberOrDecimalKey(event)"  placeholder="0" name="price" id="price" class="form-control" onkeyup="price_change()" />
                        <span class="help-block"></span>
                    </div>       
                </div>
                 <!-- Discount -->
                 <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                    <label>Discount (<span id="discbyperitem">By Percentage</span>)</label>
                           <input type="text" onkeypress="return isNumberOrDecimalKey(event)" placeholder="0" name="discount_per_item" id="discount-per-item" class="form-control" onkeyup="discount_change()" />
                        <span class="help-block"></span>
                    </div>       
                </div> 
                 <!-- Tax -->
                 <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Tax<span id="txperitem"></span></label>
                           <input type="text" readonly placeholder="0" name="tax_per_item" id="tax-per-item" class="form-control" onkeyup="tax_change()" />
                        <span class="help-block"></span>
                    </div>        
                </div> 
                <!-- Amount box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                        <label class="text-danger">Unit Cost</label>
                           <input type="text" readonly="readonly" value="0"  name="amount" placeholder="0" id="amount" class="form-control text-danger" />
                        <span class="help-block"></span>
                    </div>       
                </div> 
                  <!-- qty select box -->
                <div class="col-lg-2  col-sm-12">
                    <div class="form-group">
                         <label>Quantity</label>
                           <input type="text" onkeypress="return isNumberKey(event)" placeholder="0" name="qty" id="qty" class="form-control" onkeyup="qty_change()"  />
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
                    <th>UOM</th>
                    <th>Batch #</th>
                    <th>Exp Date </th>
                    <th>Unit Price</th>
                    <th>S.Tax</th>
                    <th>Dis.</th>
                    <th class="text-danger">Unit Cost</th>
                    <th>Qty</th>
                    <th>Gr.Amnt</th>
                    <th>Total S.Tax</th>
                    <th>Total Dis.</th>
                    <th>Net Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
             <tfoot>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td class="text-info" id="allItemQty">0.00</td>
              <td class="text-info" id="allItemGRAmnt">0.00</td>
              <td class="text-info" id="allItemSTax">0.00</td>
              <td class="text-info" id="allItemDisc">0.00</td>
              <td class="text-info" id="allItemNetAmnt">0.00</td>
              <td></td>
            </tr>
          </tfoot>
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
                <th>GROSS AMOUNT :</th>
                <td id="gross">0.00</td>
             </tr>
             <tr>
                <th>S.TAX AMOUNT :</th>
                <td id="hSTaxAmount">0.00</td>
             </tr>
             <tr>
                <th>DISC. AMOUNT :</th>
                <td id="hDiscAmount">0.00</td>
             </tr>
            <!--  <tr>
                <th>Taxes (<span id="txper">0%</span>) :</th>
                <td id="taxAmount">0.00</td>
             </tr> -->
            <!--  <tr>
                <th>Discount (<span id="discby">By Percentage</span>) :</th>
                <td id="disc">0.00</td>
             </tr> -->
             <tr>
                <th>OTHER EXPENSES :</th>
                <td id="deliveryamt">0.00</td>
             </tr>
             <tr class="txt-info">
                <th><h5>NET AMOUNT</h5></th>
                <td><h5 id="net_amount">0.00</h5></td>
             </tr>
          </tbody>
       </table>
    </div>
 </div> 

 <div class="row">
      <div class="col-lg-12 col-sm-12 ">
            <div class="form-group ">
                
                <button type="button" id="btnFinalSubmit"  class="btn btn-md btn-success waves-effect waves-light m-t-25 f-right"  >
                      <i class="icofont icofont-ui-check"> </i>Submit
                </button>
                <button type="button" id="btnDraft"  class="btn btn-md btn-default waves-effect waves-light m-t-25 m-r-25 f-right" >
                      <i class="icofont icofont-save"> </i>Save & Back
                </button>
            </div>       
        </div>  
 </div>  


           </div>
        </div>
    </section>   
	
	<div class="modal fade modal-flex" id="vendor-product-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-md" role="document">
				<div class="modal-content">
				   <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					  <h4 class="modal-title">Add Products</h4>
				   </div>
				   <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
								  <select id="vendorproduct" class="js-data-example-ajax form-control select2" multiple></select>
								</div>
							</div>
						</div>   
				   </div>
					<div class="modal-footer">
					  <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="addVendorProducts()">Add Vendor Products</button>
					</div>
				</div>
         </div>
    </div> 
@endsection


@section('scriptcode_three')
<script type="text/javascript">
      function defaultPage()
      {
        window.location = "{{ url('/view-purchases') }}";
      }

    $(".select2").select2();
     $('#date,#rpdate,#payment,#expiry-date').bootstrapMaterialDatePicker({
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


    var count = 0;
    var amount = 0 ;
    var mode = "insert";
    var po = "";
    

      $('#vendor').change(function(){
        loadVendorProducts();
      });
	  
	  function loadVendorProducts(){
		$.ajax({
			url : "{{url('/getVendorProduct')}}",
			type : "GET",
			data :  {id:$('#vendor').val()},
			success : function(result){
				$("#product").empty();
				$("#product").append("<option value=''>Select Products</option>");
				$.each(result, function( index, value ) {
					$("#product").append(
						"<option value='"+value.id+"'>"+ value.department_name + " | " + value.item_code + " | " +value.product_name + "</option>"
					);
				});
			}
		});
	  }

              
          

        $('#btnSubmit').click(function(){
            if ($('#vendor').val() == ""){
                swal_alert("Error Message !","Vendor is required ","error",false);
            }else if($('#product').val() == ""){
                swal_alert("Error Message !","Product is required ","error",false);
            }else if($('#uom').val() == ""){
                swal_alert("Error Message !","Unit of Measure is required","error",false);
            }else if($('#qty').val() == ""){
                swal_alert("Error Message !","Quantity is required ","error",false);
            }else if($('#qty').val() == 0){
                swal_alert("Error Message !","Quantity is required ","error",false);
            }else if($('#price').val() == ""){
                 swal_alert("Error Message !","Price is required ","error",false);
            }else if($('#price').val() == 0){
                 swal_alert("Error Message !","Price is required ","error",false);
            }else{ 
               
                if(mode == "insert"){

                    if ($('#id').val() == "") {
                         $.ajax({
                             url:'{{ url("/getPurchaseMax") }}',
                             type:"GET",
                             success:function(result){
                                var res = parseInt(result) + 1;
                                po = "PO-"+res;
                                $('#poNumber').html("- "+po);
                                $.ajax({
                                    url : "{{url('/create-purchases')}}",
                                    type : "POST",
                                    async : false,
                                    data : {_token : "{{csrf_token()}}", po_number:po,vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),payment:$('#payment').val()},
                                    success : function(result){
                                      
                                        $('#id').val(result);
                                        // $('#vendor').attr('disabled', 'disabled');

                                        $.ajax({
                                            url : "{{url('/purchases')}}",
                                            type : "POST",
                                            data : {_token : "{{csrf_token()}}",vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),product:$('#product').val(),unit:$('#uom').val(),tax_per_item_id:$('#tax').val(),tax_per_item_value:$('#tax-per-item').val(),discount_per_item:$('#discount-per-item').val(),batch_no:$('#batch-no').val(),expiry_date:$('#expiry-date').val(),discount_by:$("#discount_by").val(),quantity:$('#qty').val(),price:$('#price').val(),total_amount:$('#amount').val(),ID:$('#id').val(),payment:$('#payment').val()},
                                            success : function(result){
                                                if(result == 1){
                                                    swal_alert("Alert!","Item Already Exists ","error",false);
                                                    taxchange();
                                                    emptyFields();
                                                }else{
                                                    var getid = $('#id').val();
                                                    getAccounts(getid);
                                                    getAccDetails(getid);
                                                    amount = amount + parseFloat($('#amount').val());
                                                    count = count + 1;
                                                    emptyFields();
                                                }
                                             }
                                        });
                                        
                                     }
                                });
                              }
                            });

                    }else{                 
                        $.ajax({
                            url : "{{url('/purchases')}}",
                            type : "POST",
                            data : {_token : "{{csrf_token()}}",tax_per_item_value:$('#tax-per-item').val(),vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),product:$('#product').val(),unit:$('#uom').val(),quantity:$('#qty').val(),price:$('#price').val(),batch_no:$('#batch-no').val(),expiry_date:$('#expiry-date').val(),discount_by:$("#discount_by").val(),discount_per_item:$('#discount-per-item').val(),total_amount:$('#amount').val(),ID:$('#id').val(),payment:$('#payment').val()},
                            success : function(result){
                                if(result == 1){
                                    swal_alert("Alert!","Item Already Exists ","error",false);
                                    taxchange();
                                    emptyFields();
                                }else{
                                    var getid = $('#id').val();
                                    getAccounts(getid);
                                    getAccDetails(getid);
                                    amount = amount + parseFloat($('#amount').val());
                                    count = count + 1;
                                    emptyFields();
                                }
                             }
                        });
                 }

                }else{
                    $.ajax({
                        url : "{{url('/updateitems')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}",product:$('#product').val(),unit:$('#uom').val(),quantity:$('#qty').val(),price:$('#price').val(),batch_no:$('#batch-no').val(),expiry_date:$('#expiry-date').val(),discount_by:$("#discount_by").val(),tax_per_item_id:$('#tax').val(),tax_per_item_value:$('#tax-per-item').val(),discount_per_item:$('#discount-per-item').val(),total_amount:$('#amount').val(),ID:$('#edit_id').val()},
                        success : function(result){
                            mode = "insert";
                            var getid = $('#id').val();
                            getAccounts(getid);
                            getAccDetails(getid);
                            emptyFields();
                           
                           
                         }
                    });
                }
                 
                }
        });

   function emptyFields(){
            $("#product").val('').change();
            $("#uom").val('').change();
            $("#qty").val('');
            $("#price").val('');
            $("#discamount").val('');
            $("#amount").val(''); 
            $("#discount-per-item").val(''); 
            $("#tax-per-item").val('');
            $("#batch-no").val('');
            $("#expiry-date").val('');
            $('#per-item-tax-allow').prop('checked', false); 
   }

   function setPerItemTax(arg){
    if($(arg).prop('checked') === true){
      price_change();
    }else{
      $('#tax-per-item').val('');
      $('#txperitem').text('');
      discount_change();
    }
   }
   function setPerItemTaxCheckCal(){
    var calc = 0;
      $('#txperitem').text(' ('+$("#tax option:selected").text()+')');
      var str = $("#tax option:selected").text();
      var matches = str.match(/(\d+)/);
      // var gross = (parseFloat($('#qty').val()) * parseFloat($('#price').val())); 
      var gross = parseFloat($('#price').val()).toFixed(2); 
      if($('#amount').val() != '' && $('#amount').val() != 0){
        calc = (((gross) / 100) * (matches?matches[0]:0 ));
      }
      $('#tax-per-item').val(calc.toFixed(2));
      $('#amount').val((parseFloat(calc) + parseFloat($('#amount').val())).toFixed(2));
   }
   
   function otherExpenses(){
	   getAccDetails($("#id").val())
   }

   function taxchange(){

      $('#txper').text($("#tax option:selected").text());
      let str = $("#tax option:selected").text();
      let matches = str.match(/(\d+)/); 
      $('#tax_value').val(matches?matches[0]:0);
      calc = "";
      let grossamt = $('#gross').html().replace(",", "");
      calc = ((parseInt(grossamt) / 100) * (matches?matches[0]:0));
	  console.log(calc)
      $('#hidd_amt').val(calc.toFixed(2));
      // $('#taxAmount').html(calc.toFixed(2));
      
      // Discount
      let discount = $('#discount').val();
      let discCal = $('#discount').val();
      if(isNaN($('#discount').val()) || $('#discount').val() == '' ) {
        discount = 0;
      }

      let discount_by = $('select#discount_by option:selected').val();
      if(discount_by == 1){
          // $('#discby').html('By Percentage');
          discCal = ((parseFloat(grossamt) / 100) * discount);
          // $('#disc').html(parseFloat(discCal).toLocaleString());
      }else{
          // $('#discby').html('By Amount');
        // $('#disc').html(parseFloat(discount).toLocaleString());
      }

      $('#deliveryamt').html(parseInt($('#delivery').val()).toLocaleString());

      var net = parseInt(grossamt) - parseInt(discCal) + parseInt(calc) + parseInt($('#delivery').val());
	  // console.log(net.toLocaleString())
      $('#net_amount').html(net.toLocaleString());
      discount_change();
  }

     function qty_change(){
        if($('#price').val() != ""){
             var amount = (parseFloat($('#qty').val()) * parseFloat($('#price').val()));
             $('#amount').val(amount);
            discount_change();
        }
     }

     function price_change(){
              var gross = ($('#qty').val() * $('#price').val());
              $('#amount').val(0);
              if(gross != ''){
                $('#amount').val(gross);
              }
              discount_change();

    }

    function discount_change(){
        var discount_by = $('select#discount_by option:selected').val();
        // var gross = $('#qty').val() * $('#price').val();
        var gross = $('#price').val();
        var discount = $('#discount-per-item').val();
        var discCal = '';
        if(isNaN($('#discount-per-item').val()) || $('#discount-per-item').val() == '' ) {
          discount = 0;
        }
        if(discount_by == 1){
            $('#discbyperitem').html('By Percentage');
            discCal = parseFloat(gross) - ((parseFloat(gross) / 100) * parseFloat(discount));
        }else{
            $('#discbyperitem').html('By Amount');  
          discCal = parseFloat(gross) - parseFloat(discount);
        }
        $('#amount').val(0);
        if(gross != ''){
          $('#amount').val(discCal.toFixed(2));
        }
        var checked = $("input[id='per-item-tax-allow']:checked").length;
        if (checked > 0) {
          setPerItemTaxCheckCal();
        }
    } 

   

    function setValues(gross){
        var gross_total ;
    
        gross_total = amount + gross;
        calc = ((parseFloat(gross_total) / 100) * $('#tax_value').val());


        if($('#tax_value').val() != ""){
            gross_total = gross_total + parseFloat($('#hidd_amt').val());
        }

        $('#gross').html((gross_total.toLocaleString()).toFixed(2));
        // $('#taxAmount').html(calc);
		console.log(gross_total+calc)
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
                      var Tqty;var unitCost; var grAmnt; var taxAmnt;var DiscAmnt;var netAmnt;
                      var TotalTqty =0; var totalUnitCost =0; var totalGrAmnt =0; var totalTaxAmnt=0;var totalDiscAmnt=0;var totalNetAmnt=0;
                    $.each(result, function( index, value ) {
                        var discount = value.discount_per_item;
                        // var gross = parseFloat(value.price) * parseFloat(value.quantity);
                        var gross = parseFloat(value.price);
                        var batch_no = value.batch_no;;
                        var expiry_date = value.expiry_date;
                        if(value.discount_by == 1 ){
                            discount =  (parseFloat(gross) / 100) * parseFloat(discount);
                        }else{
                            discount = value.discount_per_item;
                        }
                        if(value.batch_no == null){
                           batch_no =  '-';
                        }
                        if(value.expiry_date == null){
                            expiry_date =  '-';
                        }
                        Tqty = value.quantity; 
                        unitCost = ((parseFloat(value.price) + parseFloat(value.tax_per_item_value)) - discount).toFixed(2);
                        grAmnt = value.price * value.quantity;
                        taxAmnt = (value.tax_per_item_value * value.quantity).toFixed(2);
                        DiscAmnt = discount * value.quantity;
                        netAmnt = value.total_amount * value.quantity;
                        $("#item_table tbody").append(
                        "<tr>" +
                          "<td>"+value.item_code+" - "+value.product_name+"</td>" + 
                          "<td>"+value.unitName+"</td>"+
                           "<td>"+batch_no+"</td>"+
                           "<td>"+expiry_date+"</td>"+
                            "<td>"+parseFloat(value.price.toLocaleString())+"</td>" +
                            "<td>"+value.tax_per_item_value.toLocaleString()+"</td>"+
                            "<td>"+discount.toLocaleString()+"</td>"+
                            "<td class='text-danger'>"+unitCost.toLocaleString() +"</td>"+
                            "<td>"+Tqty +"</td>" +
                            "<td>"+ grAmnt.toLocaleString() +"</td>" +
                            "<td>"+taxAmnt.toLocaleString()+"</td>" +
                            "<td>"+DiscAmnt.toLocaleString()+"</td>" +
                            "<td>"+(value.total_amount * value.quantity).toLocaleString()+"</td>" +
                            "<td class='action-icon'><i id='btn"+index+"' onclick='updateItem(\""+value.batch_no+"\",\""+value.expiry_date+"\","+value.p_item_details_id+","+value.id+","+value.unit+","+value.quantity+","+value.price+","+value.discount_per_item+","+value.tax_per_item_value+","+value.total_amount+")' class='text-warning icofont icofont-ui-edit' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>"+" &nbsp;"+"<i id='btn"+index+"' onclick='deleteItem("+value.p_item_details_id+","+"\""+value.product_name+"\")' class='text-danger icofont icofont-ui-delete' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>" +
                          "</tr>"
                         );
                        $('#allItemQty').text(TotalTqty = parseFloat(TotalTqty) + parseFloat(Tqty) );
                        $('#allItemGRAmnt').text((totalGrAmnt = parseFloat(totalGrAmnt) + parseFloat(grAmnt)).toLocaleString());
                        $('#allItemSTax').text((totalTaxAmnt = parseFloat(totalTaxAmnt) + parseFloat(taxAmnt)).toLocaleString());
                        $('#allItemDisc').text((totalDiscAmnt = parseFloat(totalDiscAmnt) + parseFloat(DiscAmnt)).toLocaleString());
                        $('#allItemNetAmnt').text((totalNetAmnt = parseFloat(totalNetAmnt) + parseFloat(netAmnt)).toLocaleString());
                    });
                    $('#hSTaxAmount').text(totalTaxAmnt);
                    $('#hDiscAmount').text(totalDiscAmnt.toFixed(2));
                }
                });
    }

    function updateItem(batch,expiry_date,id,itemid,uom,qty,price,discount,tax,amount){
         mode = "update";
         $("#expiry-date").val('');
         if(expiry_date != "null"){
            $("#expiry-date").val(expiry_date);
         }
         $("#batch-no").val('');
         if(batch != "null"){
            $("#batch-no").val(batch);
         }
         $("#product").val(itemid).change();
         $("#uom").val(uom).change();
         $('#edit_id').val(id);
         $('#qty').val(qty);
         $('#price').val(price);
         $('#amount').val(amount);
         $('#discount-per-item').val(discount);
         $('#tax-per-item').val(tax);
         if(tax != 0 && tax != null){
          $("#per-item-tax-allow").prop('checked', true);
         }
    }

    function deleteItem(id,name){
       swal({
          title: "Are you sure?",
          text: name+" will be deleted !",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "delete it!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/DeletePurchaseItems')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Product remove Successfully.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                         getAccounts($('#id').val());
                                         getAccDetails($('#id').val());
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Item is safe :)", "error");
           }
        });
    }

  function thousands_separators(num)
  {
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return num_parts.join(".");
  }

    function getAccDetails(id){

        $.ajax({
                url : "{{url('/AccDetails')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", id:id},
                success : function(result){
				
                  $('#txper').text($("#tax option:selected").text());
                  var str = $("#tax option:selected").text();
                  var matches = str.match(/(\d+)/); 
                  $('#tax_value').val(matches?matches[0]:0);

                    if($('#tax_value').val() == ""){
                        taxVal = 0;
                    }else{
                        taxVal = $('#tax_value').val();
                    }
                    
                    $('#gross').html(thousands_separators(result.unitCost));
                    // $('#gross').html(result.unitCost).toLocaleString());
                    
                    
                    calc = ((parseFloat(result) / 100) * parseInt(taxVal));
                    // $('#taxAmount').html(calc.toLocaleString());
                   
                    var discCal = $('#discount').val();
                    if(isNaN($('#discount').val()) || $('#discount').val() == '' ) {
                      discCal = 0;
                    }
                    var discount_by = $('select#discount_by option:selected').val();
                    if(discount_by == 1){
                        discCal = ((parseFloat(result) / 100) * discCal);
                        $('#disc').html(parseFloat(discCal).toLocaleString());
                    }else{
                      $('#disc').html(parseFloat(discCal).toLocaleString());
                    }

                    var sum =  parseFloat(result.totalCost)  + parseFloat($('#delivery').val()) ;
					$("#deliveryamt").html(parseFloat($('#delivery').val()))
                    $('#net_amount').html(sum.toLocaleString());

                }
              });
    }

    $('#btnFinalSubmit').click(function(){
    	let grossamt = $('#gross').html().replace(",", "");
        let taxamt = $('#hSTaxAmount').html().replace(",", "");//$('#taxAmount').html().replace(",", "");
        let netamt = $('#net_amount').html().replace(",", "");
		let discountAmount = $('#hDiscAmount').html().replace(",", "");

        if($('#vendor').val() == ""){
          swal_alert("Alert!","Select Vendor First ","error",false);
        }else if($('#date').val() == ""){
          swal_alert("Alert!","Purchase Order Date Field Required ","error",false);
        }else if($('#rpdate').val() == ""){
          swal_alert("Alert!","Receipt Date Required ","error",false);
        // }else if($('#ref').val() == ""){
        //   swal_alert("Alert!","Reference Field Required ","error",false);
        }else if($('#gross').html() == "0.00"){
          swal_alert("Alert!","Please Select Any Product","error",false);
        }else{
           $.ajax({
                url : "{{url('/FinalSubmit')}}",
                type : "POST",
                async: false,
                data : {_token : "{{csrf_token()}}",vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),total_amount:grossamt,taxAmount:taxamt,net_amount:netamt,ID:$('#id').val(),discount:discountAmount,delivery:$('#delivery').val(),branch:$('#branch').val(),payment:$('#payment').val()},
                success : function(result){
                    swal_alert("Success!","Purchase Order Created Successfully","success",true);
                }
            });
        }
    });

    $('#btnDraft').click(function(){
       $.ajax({
            url : "{{url('/Draft')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}",vendor:$('#vendor').val(),branch:$('#branch').val(),tax:$('#tax').val(),date:$('#date').val(),ref:$('#ref').val(),rpdate:$('#rpdate').val(),comments:$('#comments').val(),total_amount:$('#gross').html(),taxAmount:$('#taxAmount').html(),net_amount:$('#net_amount').html(),ID:$('#id').val(),discount:$('#discount').val(),discount_by:$('#discount_by').val(),delivery:$('#delivery').val(),payment:$('#payment').val()},
            success : function(result){
                window.location = "{{ url('/view-purchases') }}";
            }
       });
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

$("#btn_ven").on('click',function(){
  window.location = "{{url('/vendors/create')}}";
});

$('#btn_vendor_product').click(function(){
	$("#vendor-product-modal").modal("show");
})

	$('.js-data-example-ajax').select2({
	  ajax: {
		url: '{{route("search-inventory")}}',
		dataType: 'json',
		processResults: function (data) {
		  // Transforms the top-level key of the response object from 'items' to 'results'
		  return {
				results: $.map(data.items, function (item) {
					return {
						text: item.product_name,
						id: item.id
					}
				})
			};
		}
		// Additional AJAX parameters go here; see the end of this chapter for the full code of this example
	  },
	  placeholder: 'Search for a Product',
	  minimumInputLength: 1,
	});

function addVendorProducts(){
	$.ajax({
            url : "{{url('/add-vendor-products-from-purchase-order')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}",vendor:$('#vendor').val(),products:$('#vendorproduct').val()},
            success : function(result){
				// console.log(result)
				if(result == 1){
					$("#vendor-product-modal").modal("hide");
					loadVendorProducts();
					notify("Products added successfully","success")
				}else{
					$("#vendor-product-modal").modal("hide");
					notify("Some Error Occured","danger")
				}
            }
       });
}

function isNumberOrDecimalKey(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
$("#product").change(function(e){
	if(e.target.value != ""){
		$.ajax({
			url : "{{url('/get-uom-id')}}",
			type : "POST",
			data : {_token : "{{csrf_token()}}",id:e.target.value},
			success : function(result){
				console.log(result)
				if(result){
					$("#uom").val(result[0].uom_id).change();
				}
				
			}
		});
	}
})
</script>
@endsection
