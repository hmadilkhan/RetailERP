@extends('layouts.master-layout')

@section('title','Create Branch')

@section('breadcrumtitle','Edit Discount')

@section('navdiscount','active')

@section('content')

<form method="post" action="{{url('save-discount')}}">
  @method('post')
  @csrf
  <input type="hidden" id="discount_customer_eligibility" name="discount_customer_eligibility" value="1">
  <input type="hidden" id="discount_applies_to" name="discount_applies_to" value="1">
  <div class=" col-lg-7 col-md-7">
    <div class="card card-block">
            <div class="col-lg-12 col-md-12">
               <div class="form-group">
                  <label class="form-control-label">Discount Code</label>
                  <label class="form-control-label f-right text-primary"></label>
                  <input class="form-control" type="text" 
                   name="code" id="code" value="{{$discountGen[0]->discount_code}}"  />
                    
                  <div class="form-control-feedback">Customer will enter this discount code at checkout.</div>
                </div>    
                </div>
     </div>

     <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Options</h5>
      </div>
     <div class="card-block">
            <div class="col-lg-6 col-md-6">
               <div class="form-group">
                    <label class="form-control-label">Discount Type</label>
                    <select name="type" id="type" data-placeholder="Select Discount Type" class="form-control select2" >
                        <option value="">Select Discount Type</option>
                        @if($discountType)
                          @foreach($discountType as $value)
                            @if($value->discount_type_id == $discountGen[0]->discount_type )
                              <option selected="" value="{{ $value->discount_type_id }}">{{ $value->type_name }}</option>
                            @endif
                    
                          @endforeach
                        @endif
                        
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
           </div>

     </div>
   </div>

   <div class="card" id="buyonegetone" >
      <div class="card-header">
        <h5 class="card-header-text">Customer Buys</h5>
      </div>
     <div class="card-block">
        <div class="col-lg-2 col-md-2 " >
               <div class="form-group">
                  <label class="form-control-label">Quantity</label>
                  <input class="form-control" type="text" name="buyQty" id="buyQty"   />
               </div>    
        </div>
        <div  class="col-lg-10 col-md-10">
               <div class="form-group">
                    <label class="form-control-label">Select Products</label>
                    <select name="ddlbuyProducts[]" id="ddlbuyProducts" data-placeholder="Select Products" class="form-control select2" multiple="" >
                        <option value="">Select Products</option>
                        
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
           </div>
     </div>
     <hr/>
     <div class="card-header">
        <h5 class="card-header-text">Customer Gets</h5>
      </div>
     <div class="card-block">
      <div class="col-lg-2 col-md-2 " >
               <div class="form-group">
                  <label class="form-control-label">Quantity</label>
                  <input class="form-control" type="text" name="getQty" id="getQty"   />
               </div>    
        </div>
        <div  class="col-lg-10 col-md-10">
               <div class="form-group">
                    <label class="form-control-label">Select Products</label>
                    <select name="ddlgetProducts[]" id="ddlgetProducts" data-placeholder="Select Products" class="form-control select2" multiple="" >
                        <option value="">Select Products</option>
                        
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
           </div>
     </div>
     <div class="card-header">
        <h5 class="card-header-text">AT A DISCOUNTED VALUE OF</h5>
      </div>
     <div class="card-block">
        <div  class="col-lg-8 col-md-8">
               <div class="form-group">
                    <label class="form-control-label">Select Discounted Value</label>
                    <select name="ddldiscountedvalue" id="ddldiscountedvalue" data-placeholder="Select Discounted Value" class="form-control select2"  >
                        <option value="">Select Discounted Value</option>
                         @if($discountvalue)
                          @foreach($discountvalue as $value)
                            <option value="{{ $value->dicount_value_id }}">{{ $value->name }}</option>
                          @endforeach
                        @endif
                        
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
           </div>
           <div id="divdiscountpercentage" class="col-lg-4 col-md-4" style="display: none;">
               <div class="form-group">
                  <label class="form-control-label">Percentage Value</label>
                  <input class="form-control" type="text" name="discountedpercentage" id="discountedpercentage"   />
               </div>    
        </div>
     </div>
   </div>
   

    <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Customer Eligibility</h5>
      </div>
     <div class="card-block">
        <!-- RADIO BUTTONS STARTS -->
          <div class="col-lg-12 col-md-12">
              <div class="form-radio">
                <form id="different">
                  <div class="radio radiofill">
                    <label>
                      <input type="radio" name="customerradio" checked="checked" id="everyone" value="1" onchange="customerchange(this.id)"/><i class="helper"></i>Everyone
                    </label>
                  </div>
                  <div class="radio radiofill">
                    <label>
                      <input type="radio" name="customerradio" id="specificcustomer" value="2" onchange="customerchange(this.id)" /><i class="helper"></i>Selected Customers
                    </label>
                  </div>
                  
                </form>
              </div>
          </div>
           <!-- RADIO BUTTONS ENDS -->

           <div id="divcustomers" class="col-lg-12 col-md-12" style="display: none;">
               <div class="form-group">
                    <label class="form-control-label">Select Customers</label>
                    <select name="customers[]" id="customers" data-placeholder="Select Customers" class="form-control select2" multiple="" >
                        <option value="">Select Customers</option>
                       
                        
                    </select>
                     <div class="form-control-feedback"></div>
                </div>
           </div>
     </div>
   </div>

    <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Usage Limits</h5>
      </div>
     <div class="card-block">
        <!-- RADIO BUTTONS STARTS -->
          <div class="col-lg-12 col-md-12 m-b-10">
              <div class="col-lg-12 col-md-12 rkmd-checkbox checkbox-rotate checkbox-ripple">
                    <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkTotalUsage" name="chkTotalUsage" value="1">
                    <span class="checkbox"></span>
                    <span class="ripple"></span></label>
                    <div class="captions"> Limit number of times this discount can be used in total.</div>
            </div>
          </div>

          <div id="limitTotalDiv" class="col-lg-6 col-md-6 m-l-40 m-b-10" style="display: none;">
               <div class="form-group">
                <label class="form-control-label"></label>
                  <input class="form-control" type="text" name="totlusage" id="totlusage"   />
               </div>    
            </div>

           <div class="col-lg-12 col-md-12">
              <div class="col-lg-12 col-md-12 rkmd-checkbox checkbox-rotate checkbox-ripple">
                    <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkonecustomer" name="chkonecustomer" value="1">
                    <span class="checkbox"></span>
                    <span class="ripple"></span></label>
                    <div class="captions"> Limit to one use per customer.</div>
            </div>
          </div>
           
           

           
     </div>
   </div>

   <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Active Dates</h5>
      </div>
     <div class="card-block">
        <div class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Start Date</label>
            <input type='text' class="form-control" id="startdate" name="startdate" placeholder="DD-MM-YYYY" value="{{$period[0]->startdate}}" />  
            <span class="help-block text-danger" id="rpbox"></span>  
          </div>
        </div>  
        <div class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Start Time</label>
            <input type='text' class="form-control" id="starttime" name="starttime" placeholder="DD-MM-YYYY" value="{{$period[0]->starttime}}" />  
            <span class="help-block text-danger" id="rpbox"></span>  
          </div>
        </div> 

         <div class="col-lg-12 col-md-12 rkmd-checkbox checkbox-rotate checkbox-ripple">
                    <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkEndDate" name="chkEndDate"  >
                    <span class="checkbox"></span>
                    <span class="ripple"></span></label>
                    <div class="captions"> End Date.</div>
          </div>

          <div id="divEndSection" style="display: none;">
            <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label class="form-control-label">End Date</label>
                  <input type='text' class="form-control" id="enddate" name="enddate" placeholder="DD-MM-YYYY" value="{{$period[0]->enddate}}" />  
                  <span class="help-block text-danger" id="rpbox"></span>  
                </div>
            </div>  
          <div class="col-lg-6 col-md-6">
              <div class="form-group">
                <label class="form-control-label">End Time</label>
                <input type='text' class="form-control" id="endtime" name="endtime" placeholder="DD-MM-YYYY" value="{{$period[0]->endtime}}" />  
                <span class="help-block text-danger" id="rpbox"></span>  
              </div>
           </div> 

          </div>


     </div>
  </div>

  <div class="card">
 
                 <button type="submit"   class="btn btn-md btn-success waves-effect waves-light  f-right"  >
                      <i class="icofont icofont-ui-check"> </i>Submit
                </button>
               
     
  </div>
</form>
</div>
<div class="card m-l-20 h-50 col-lg-4 col-md-4">
<div class="card-header">
     <h3 class="card-header-text f-24">SUMMARY</h3>
</div> 
<div class="col-lg-12 col-md-12">
           <h2 id="salename"></h2>
</div>
<div class="col-lg-12 col-md-12 m-l-30 m-b-20 ">
    <ul id="description" style="list-style-type:disc;">
      <div id="disc_value"></div>
      <div id="disc_min"></div>
      <div id="disc_usage"></div>
      <div id="disc_date"></div>
    </ul>
</div>
</div>
<br/>
<br/>

@endsection

@section('scriptcode_three')
<script type="text/javascript">
$(".select2").select2();

$('#startdate,#enddate').bootstrapMaterialDatePicker({
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

 $('#starttime,#endtime').datetimepicker({
        format: 'LT',
        icons: {
            time: "icofont icofont-clock-time",
            date: "icofont icofont-ui-calendar",
            up: "icofont icofont-rounded-up",
            down: "icofont icofont-rounded-down",
            next: "icofont icofont-rounded-right",
            previous: "icofont icofont-rounded-left"
        }
    });
startupFunction();
 function startupFunction()
 {
    
    var cust_eligibility = "{{$discountGen[0]->customer_eligibilty}}";
    var usage = "{{$usage[0]->usage_limit}}";
    var chkEnd = "{{$period[0]->enddate}}";
    var onetime = "{{$usage[0]->onetimeuse}}";
   
    if (cust_eligibility == 1) 
    {
      $( "#everyone" ).prop( "checked", true );
    }
    else
    {

      $( "#specificcustomer" ).prop( "checked", true );
      $('#divcustomers').css('display','block');
      loadCustomers();
      selectCustomers();
    }

    if (usage != 0) 
    {
      $('#chkTotalUsage').prop('checked',true);
      $('#limitTotalDiv').css('display','block');
      $('#totlusage').val(usage);
    }

    if (chkEnd != "") 
    {
      $('#chkEndDate').prop("checked",true);
      $('#divEndSection').css('display','block');
    }

    if (onetime == 1) 
    {
      $('#chkonecustomer').prop('checked',true);
    }
 }





function customerchange(id) {
  if(id == "specificcustomer")
  {
    $('#divcustomers').css('display','block');
    loadCustomers();
    $('#discount_customer_eligibility').val("2");
  }
  else 
  {
    $('#divcustomers').css('display','none');
    $('#discount_customer_eligibility').val("1");
  }
}

$('#chkEndDate').change(function(){
  if($('#chkEndDate').prop("checked") == true)
    {
        $('#divEndSection').css('display','block');
    }
    else if($('#chkEndDate').prop("checked") == false)
    {
        $('#divEndSection').css('display','none');   
        $('#endtime').val('');
        if($('#startdate') != "")
        {
          var d = new Date($('#startdate').val());
          var month = myFunction($('#startdate').val());
          var value = "Active from " +month+" "+d.getDate();
          $('#disc_date').empty();
          $('#disc_date').append("<li>"+value+"</li>")
        }
        else
        {
          $('#disc_date').empty();
        }
    }
});

function selectBuy()
{
  $.ajax({
      url : "{{url('/get-customer-buys')}}",
      type : "POST",
      success : function(result){
        $("#ddlcategory").empty();
        $("#ddlcategory").append("<option value=''>Select Categories</option>");
           $.each(result, function(item,value) {
               $("#ddlcategory").append(
                      "<option  value='"+value.department_id+"'>"+value.department_name+"</option>");
           });
      }
  });
}

function loadCustomers()
{
  $.ajax({
      url : "{{url('/load-customers')}}",
      type : "GET",
      success : function(result){
        $("#customers").empty();
        $("#customers").append("<option value=''>Select Customers</option>");
           $.each(result, function(item,value) {
               $("#customers").append(
                      "<option value='"+value.id+"'>"+value.name+"</option>");
           });
      }
  });
}


loadBuyOneGetOneProducts();
function loadBuyOneGetOneProducts()
{
  $.ajax({
      url : "{{url('/load-products')}}",
      type : "GET",
      success : function(result){
        $("#ddlbuyProducts").empty();
        $("#ddlgetProducts").empty();
        $("#ddlbuyProducts").append("<option value=''>Select Products</option>");
        $("#ddlgetProducts").append("<option value=''>Select Products</option>");
           $.each(result, function(item,value) {
               $("#ddlbuyProducts").append(
                      "<option value='"+value.id+"'>"+value.product_name+"</option>");
           });
           $.each(result, function(item,value) {
               $("#ddlgetProducts").append(
                      "<option value='"+value.id+"'>"+value.product_name+"</option>");
           });
      }
  });
  selectBuys();
  selectGets();
}

function selectBuys()
{
  $.ajax({
      url : "{{url('/get-customer-buys')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",id:"{{$discountGen[0]->discount_id}}"},
      success : function(result){
      		$('#buyQty').val(result[0].buy_qty);
           $.each(result, function(item,value) {
               $("#ddlbuyProducts option[value="+value.buy_product+"]").attr("selected","selected").change();
           });
      }
  });
}

function selectGets()
{
  $.ajax({
      url : "{{url('/get-customer-gets')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",id:"{{$discountGen[0]->discount_id}}"},
      success : function(result){
      	$('#getQty').val(result[0].get_qty);
           $.each(result, function(item,value) {
               // $("#ddlcategory").val(value.category_id).change();
               $("#ddlgetProducts option[value="+value.get_product+"]").attr("selected","selected").change();
           });
      }
  });
}

function selectCustomers()
{
  $.ajax({
      url : "{{url('/get-discount-customers')}}",
      type : "POST",
      data : {_token : "{{csrf_token()}}",id:"{{$discountGen[0]->discount_id}}"},
      success : function(result){
           $.each(result, function(item,value) {
               $("#customers option[value="+value.cust_id+"]").attr("selected","selected").change();
           });
      }
  });
}





//RANDOM CODE GENERATE
function makeid(length) 
{
   var result= '';
   var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   $('#code').val(result.toUpperCase());
   $('#salename').html(result.toUpperCase());

}

$('#code').change(function(){
  var value = $('#code').val();
  $('#salename').html(value.toUpperCase());
});
//Set Mimimum purchase Summary
$('#minValue').change(function(){
  var result = "On minimum amount of "+$('#minValue').val();
    $('#disc_min').append("<li>"+result+"</li>");
});

$('#discountvalue').change(function(){
  
 discountValueSet();
  
});









$('#chkonecustomer').change(function(){
    if($('#chkTotalUsage').prop("checked") == true && $('#chkonecustomer').prop("checked") == true)
    {
        value = "Limit of "+$('#totlusage').val()+" uses, one use per customer";
        $('#disc_usage').empty();
        $('#disc_usage').append("<li>"+value+"</li>");
    }
    else if($('#chkonecustomer').prop("checked") == true)
    {
        value = "One use per customer";
        $('#disc_usage').empty();
        $('#disc_usage').append("<li>"+value+"</li>");
    }
    else if($('#chkonecustomer').prop("checked") == false)
    {
      if($('#chkTotalUsage').prop("checked") == true)
      {
          value = "Limit of "+$('#totlusage').val()+" uses";
          $('#disc_usage').empty();
          $('#disc_usage').append("<li>"+value+"</li>");
      }
      else
      {
        $('#disc_usage').empty();
      }
        
      }
    
});

// var CurrentDate = new Date();
// console.log(CurrentDate.getDate()+" "+(CurrentDate.getMonth()  + 1)+" "+CurrentDate.getFullYear());
 
// chkEndDate

$('#startdate').change(function(){

  var d = new Date($('#startdate').val());
  var month = myFunction($('#startdate').val());
  var value = "Active from " +month+" "+d.getDate();
  $('#disc_date').empty();
  $('#disc_date').append("<li>"+value+"</li>")

});

$('#enddate').change(function(){
  var day = new Date($('#startdate').val());
  var monthname = myFunction($('#startdate').val());

  var d = new Date($('#enddate').val());
  var month = myFunction($('#enddate').val());

  var value = "Active from " +monthname+" "+day.getDate()+" to "+month+" "+d.getDate();
  $('#disc_date').empty();
  $('#disc_date').append("<li>"+value+"</li>")
});



function myFunction(date) {
  var month = new Array();
  month[0] = "January";
  month[1] = "February";
  month[2] = "March";
  month[3] = "April";
  month[4] = "May";
  month[5] = "June";
  month[6] = "July";
  month[7] = "August";
  month[8] = "September";
  month[9] = "October";
  month[10] = "November";
  month[11] = "December";

  var d = new Date(date);
  var n = month[d.getMonth()];
  return n;
}

$('#btnFinalSubmit').click(function(){
  
            $.ajax({
                url : "{{url('/save-discount')}}",
                type : "POST",
                data : {_token : "{{csrf_token()}}", code:$('#code').val(),type:$('#type').val(),disValue:$('#discountvalue').val(),min:$('#minValue').val(),applies:$("input[name='radio']:checked").val(),eligibility:$("input[name='customerradio']:checked").val(),totlusage:$('#totlusage').val(),startdate:$('#startdate').val(),starttime:$('#starttime').val(),enddate:$('#enddate').val(),endtime:$('#endtime').val()},
                success : function(result){
                  console.log(result);
               
                    
                 }
            });
          
       
});
$('#ddldiscountedvalue').change(function(){
  if($('#ddldiscountedvalue').val() == 1)
  {
    $('#divdiscountpercentage').css('display','block');
  }
  else
  {
    $('#divdiscountpercentage').css('display','none');
  }
});
</script>


@endsection