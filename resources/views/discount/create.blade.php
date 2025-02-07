@extends('layouts.master-layout')

@section('title','Create Discount')

@section('breadcrumtitle','Create Discount')

@section('navdiscount','active')

@section('content')

<form id="discountform" method="post" action="{{url('save-discount')}}">
  @method('post')
  @csrf
  <input type="hidden" id="discount_customer_eligibility" name="discount_customer_eligibility" value="1">
  <input type="hidden" id="discount_applies_to" name="discount_applies_to" value="1">
  <div id="daysdiv"></div>
  <div class=" col-lg-7 col-md-7">
    <div class="card card-block">
      <div class="col-lg-12 col-md-12">

        <div class="form-group">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" id="applyToVoucher" name="applyToVoucher">
             Do you want to create a discount through this code</label>
        </div>

        <div class="form-group">
          <label class="form-control-label">Discount Code <span class="text-danger">*</span></label>
          <label class="form-control-label f-right text-primary" style="cursor :pointer" onclick="makeid(8)">Generate Code</label>
          <input class="form-control" type="text" name="code" id="code" value="{{ old('code') }}" required/>

          <div class="form-control-feedback"><i>Customer will enter this discount code at checkout.</i></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Select Website</h5>
      </div>
      <div class="card-block">

        <div class="col-lg-12 col-md-12">
          <div class="form-group">
            <label class="form-control-label">Select Website</label>
            <select name="website" id="website" data-placeholder="Select Website" class="form-control select2">
              <option value="">Select Website</option>
              @if($websites)
              @foreach($websites as $website)
              <option value="{{$website->id}}">{{$website->name}}</option>
              @endforeach
              @endif
            </select>
            <div class="form-control-feedback"></div>
          </div>
        </div>
      </div>
    </div>

<!--Applies to-->
    <div class="card" id="divappliesTo">
      <div class="card-header">
        <h5 class="card-header-text">Applies To</h5>
      </div>
      <div class="card-block">
        <!-- RADIO BUTTONS STARTS -->
        <div class="col-lg-12 col-md-12">
          <div class="form-radio">
            <form>
              <div class="radio radiofill">
                <label>
                  <input type="radio" name="radio" checked="checked" id="entire" value="entire order" onchange="radiochange(this.id)" /><i class="helper"></i>Entire Order
                </label>
              </div>
              <div class="radio radiofill">
                <label>
                  <input type="radio" name="radio" id="collection" value="selected collections" onchange="radiochange(this.id)" /><i class="helper"></i>Selected Departments
                </label>
              </div>
              <div class="radio radiofill ">
                <label>
                  <input type="radio" name="radio" id="product" value="selected products" onchange="radiochange(this.id)" /><i class="helper"></i>Selected Products <a class="text-primary" onclick="$('#details-modal').modal('show')">( Show All products )</a>
                </label>
              </div>
            </form>
          </div>
        </div>
        <!-- RADIO BUTTONS ENDS -->

        <div id="divcategories" class="col-lg-12 col-md-12 d-none">
          <div class="form-group">
            <label class="form-control-label">Select Categories</label>
            <select name="ddlcategory[]" id="ddlcategory" data-placeholder="Select Categories" class="form-control select2" multiple="">
              <option value="">Select Categories</option>
              @if($departments)
              @foreach($departments as $value)
              <option value="{{ $value->department_id }}">{{ $value->department_name }}</option>
              @endforeach
              @endif

            </select>
            <div class="form-control-feedback"></div>
          </div>
        </div>

        <div id="divproducts" class="col-lg-12 col-md-12 d-none">
          <div class="form-group">
            <label class="form-control-label">Select Products</label>
            <select name="ddlproduct[]" id="ddlproduct" data-placeholder="Select Products" class="form-control select2" multiple="">
              <option value="">Select Products</option>
            </select>
            <div class="form-control-feedback"></div>
          </div>
        </div>
      </div>
    </div>

<!--/Applies to-->

    <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Options</h5>
      </div>
      <div class="card-block">
        <div class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Discount Type</label>
            <select name="type" id="type" data-placeholder="Select Discount Type" class="form-control select2">
              <option value="">Select Discount Type</option>
              @if($discountType)
              @foreach($discountType as $value)
              <option value="{{ $value->discount_type_id }}">{{ $value->type_name }}</option>
              @endforeach
              @endif

            </select>
            <div class="form-control-feedback"></div>
          </div>
        </div>

        <div id="divdiscountvalue" class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Discount Value <span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="discountvalue" id="discountvalue" />
          </div>
        </div>


        <div id="divminChkBox" class="col-lg-12 col-md-12 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" id="chkMinPurchase" name="chkMinPurchase">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions"> This discount requires a minimum purchase.</div>
        </div>

        <div id="minPurchaseDiv" class="col-lg-6 col-md-6 m-t-10" style="display: none;">
          <div class="form-group">
            <label class="form-control-label">Minimum Purchase Value</label>
            <input class="form-control" type="text" name="minValue" id="minValue" />
          </div>
        </div>

      </div>
    </div>

    <div class="card" id="buyonegetone" style="display: none">
      <div class="card-header">
        <h5 class="card-header-text">Customer Buys</h5>
      </div>
      <div class="card-block">
        <div class="col-lg-2 col-md-2 ">
          <div class="form-group">
            <label class="form-control-label">Quantity</label>
            <input class="form-control" type="text" name="buyQty" id="buyQty" />
          </div>
        </div>
        <div class="col-lg-10 col-md-10">
          <div class="form-group">
            <label class="form-control-label">Select Products</label>
            <select name="ddlbuyProducts[]" id="ddlbuyProducts" data-placeholder="Select Products" class="form-control select2" multiple="">
              <option value="">Select Products</option>

            </select>
            <div class="form-control-feedback"></div>
          </div>
        </div>
      </div>
      <hr />
      <div class="card-header">
        <h5 class="card-header-text">Customer Gets</h5>
      </div>
      <div class="card-block">
        <div class="col-lg-2 col-md-2 ">
          <div class="form-group">
            <label class="form-control-label">Quantity</label>
            <input class="form-control" type="text" name="getQty" id="getQty" />
          </div>
        </div>
        <div class="col-lg-10 col-md-10">
          <div class="form-group">
            <label class="form-control-label">Select Products</label>
            <select name="ddlgetProducts[]" id="ddlgetProducts" data-placeholder="Select Products" class="form-control select2" multiple="">
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
        <div class="col-lg-8 col-md-8">
          <div class="form-group">
            <label class="form-control-label">Select Discounted Value</label>
            <select name="ddldiscountedvalue" id="ddldiscountedvalue" data-placeholder="Select Discounted Value" class="form-control select2">
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
            <input class="form-control" type="text" name="discountedpercentage" id="discountedpercentage" />
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
                  <input type="radio" name="customerradio" checked="checked" id="everyone" value="1" onchange="customerchange(this.id)" /><i class="helper"></i>Everyone
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
            <select name="customers[]" id="customers" data-placeholder="Select Customers" class="form-control select2" multiple="">
              <option value="">Select Customers</option>


            </select>
            <div class="form-control-feedback"></div>
          </div>
        </div>
      </div>
    </div>



    <div class="card d-none" id="usageLimitsBox">
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
            <input class="form-control" type="text" name="totlusage" id="totlusage" />
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
        <h5 class="card-header-text">Select Days</h5>

     <div class="rkmd-checkbox checkbox-rotate checkbox-ripple f-right">
      <label class="input-checkbox checkbox-primary">
            <input type="checkbox" id="selectedAlldays">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2" for="selectedAlldays">Select All</div>
        </div>
      </div>

      <div class="card-block">
        <div class="col-lg-2 col-md-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('mon', value)" id="mon">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Monday</div>
        </div>
        <div class="col-lg-2 col-md-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('tue', value)" id="tue">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Tuesday</div>
        </div>
        <div class="col-lg-2 col-md-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('wed', value)" id="wed">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Wednesday</div>
        </div>
        <div class="col-lg-2 col-md-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('thu', value)" id="thu">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Thursday</div>
        </div>
        <div class="col-lg-2 col-md-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('fri', value)" id="fri">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Friday</div>
        </div>
        <div class="col-lg-2 col-md-2 m-t-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('sat', value)" id="sat">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Saturday</div>
        </div>
        <div class="col-lg-2 col-md-2 m-t-2 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" onclick="selectedCheckbox('sun', value)" id="sun">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions2">Sunday</div>
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
            <input type='text' class="form-control" id="startdate" name="startdate" placeholder="DD-MM-YYYY" value="{{ date('Y-m-d') }}"/>
            <span class="help-block text-danger" id="rpbox"></span>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Start Time</label>
            <input type='text' class="form-control" id="starttime" name="starttime" placeholder="H:i Am" value="{{ date('h:i A', strtotime(date('H:i'))) }}"/>
            <span class="help-block text-danger" id="rpbox"></span>
          </div>
        </div>

        <div class="col-lg-12 col-md-12 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" id="chkEndDate" name="chkEndDate">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions"> End Date.</div>
        </div>

        <div id="divEndSection" style="display: none;">
          <div class="col-lg-6 col-md-6">
            <div class="form-group">
              <label class="form-control-label">End Date</label>
              <input type='text' class="form-control" id="enddate" name="enddate" placeholder="DD-MM-YYYY" />
              <span class="help-block text-danger" id="rpbox"></span>
            </div>
          </div>
          <div class="col-lg-6 col-md-6">
            <div class="form-group">
              <label class="form-control-label">End Time</label>
              <input type='text' class="form-control" id="endtime" name="endtime" placeholder="DD-MM-YYYY" />
              <span class="help-block text-danger" id="rpbox"></span>
            </div>
          </div>
        </div>
      </div>
    </div>




    <div class="card">

      <button type="button" id="btnSubmit" class="btn btn-md btn-success waves-effect waves-light  f-right">
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
      <div id="disc_website"></div>
      <div id="disc_value"></div>
      <div id="disc_min"></div>
      <div id="disc_usage"></div>
      <div id="disc_date"></div>
    </ul>
  </div>



</div>
<br />
<br />
<div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Select products</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-9">
            <input type="text" name="search" id="search" placeholder="Search" class="form-control">
          </div>
          <div class="col-md-2">
            <button type="button" id="btnSearch" class="btn btn-success">Search</button>
          </div>
        </div>

        <div id="divProd" style="height:550px;overflow: scroll;">
          <table id="inventtbl" class="table table-striped nowrap dt-responsive ">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>

        <div id="showProduct_md"></div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSave" class="btn btn-success waves-effect waves-light f-right">
          Done
        </button>
      </div>

    </div>
  </div>
</div>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
  var page = 1;
  products = [];
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

  $("#website").on('change',function(){

      if($(this).val()!=''){
          $("#disc_website").html('<b>Website:</b> '+$("#website option:selected").text());
      }else{
          $("#disc_website").text('');
      }
  });

  $("#applyToVoucher").on('change',function(){
      if($(this).is(':checked')){
          $("#code").attr('readonly',true);
      }else{
          $("#code").attr('readonly',false);
      }
  });

  $('#chkMinPurchase').change(function() {
    if ($('#chkMinPurchase').prop("checked") == true) {
      $('#minPurchaseDiv').css('display', 'block');
    } else if ($('#chkMinPurchase').prop("checked") == false) {
      $('#minPurchaseDiv').css('display', 'none');
      $('#minValue').val('');
      $('#disc_min').empty();
    }
  });

  function radiochange(id) {
    if (id == "collection") {
    //   $('#divcategories').css('display', 'block');
    //   $('#divproducts').css('display', 'none');
      divBox_OnOff(0,'divproducts');
      divBox_OnOff(1,'divcategories');
      $('#product').empty();
      loadDepartments();
      $('#discount_applies_to').val("2");

      miniPurchaseBox(0);
    } else if (id == "product") {
      page = 1;
    //   $('#divproducts').css('display', 'block');
    //   $('#divcategories').css('display', 'none');
      divBox_OnOff(1,'divproducts');
      divBox_OnOff(0,'divcategories');
      $('#category').empty();
      loadProducts(page);
      $('#details-modal').modal("show");
      $('#discount_applies_to').val("3");

      miniPurchaseBox(0);

    } else {
      divBox_OnOff(0,'divproducts');
      divBox_OnOff(0,'divcategories');
      $('#discount_applies_to').val("1");
      miniPurchaseBox(1);
    }
    discountValueSet();
  }

  function divBox_OnOff(md,elementId){
    if(md == 1){
      if($("#"+elementId).hasClass('d-none')){
          $("#"+elementId).removeClass('d-none');
      }
    }else{
       if(!$("#"+elementId).hasClass('d-none')){
          $("#"+elementId).addClass('d-none');
      }
    }
  }

  function miniPurchaseBox(mode){
   if(mode == 1){
     if($("#divminChkBox").hasClass('d-none')){
         $("#divminChkBox").removeClass('d-none').addClass('rkmd-checkbox');

     }
   }else{
     if(!$("#divminChkBox").hasClass('d-none')){
         $("#divminChkBox").addClass('d-none').removeClass('rkmd-checkbox');
     }
   }
  }

  function customerchange(id) {
    if (id == "specificcustomer") {
      $('#divcustomers').css('display', 'block');
      loadCustomers();
      $('#discount_customer_eligibility').val("2");
      usageLimitsBox(1)
    } else {
      $('#divcustomers').css('display', 'none');
      $('#discount_customer_eligibility').val("1");
      usageLimitsBox(0)
    }
  }

  function usageLimitsBox(mode){
   if(mode == 1){
     if($("#usageLimitsBox").hasClass('d-none')){
         $("#usageLimitsBox").removeClass('d-none');

     }
   }else{
     if(!$("#usageLimitsBox").hasClass('d-none')){
         $("#usageLimitsBox").addClass('d-none');
     }
   }
  }

  $('#chkEndDate').change(function() {
    if ($('#chkEndDate').prop("checked") == true) {
      $('#divEndSection').css('display', 'block');
    } else if ($('#chkEndDate').prop("checked") == false) {
      $('#divEndSection').css('display', 'none');
      $('#endtime').val('');
      if ($('#startdate') != "") {
        var d = new Date($('#startdate').val());
        var month = myFunction($('#startdate').val());
        var value = "Active from " + month + " " + d.getDate();
        $('#disc_date').empty();
        $('#disc_date').append("<li>" + value + "</li>")
      } else {
        $('#disc_date').empty();
      }
    }
  });

  function loadDepartments() {
    $.ajax({
      url: "{{url('/load-department')}}",
      type: "GET",
      success: function(result) {
        $("#ddlcategory").empty();
        $("#ddlcategory").append("<option value=''>Select Categories</option>");
        $.each(result, function(item, value) {
          $("#ddlcategory").append(
            "<option value='" + value.department_id + "'>" + value.department_name + "</option>");
        });
      }
    });
  }

  loadBuyOneGetOneProducts();

  function loadBuyOneGetOneProducts() {
    $.ajax({
      url: "{{url('/load-products')}}",
      type: "GET",
      success: function(result) {
        $("#ddlbuyProducts").empty();
        $("#ddlgetProducts").empty();
        $("#ddlbuyProducts").append("<option value=''>Select Products</option>");
        $("#ddlgetProducts").append("<option value=''>Select Products</option>");
        $.each(result.data, function(item, value) {
          $("#ddlbuyProducts").append(
            "<option value='" + value.id + "'>" + value.product_name + "</option>");
        });
        $.each(result.data, function(item, value) {
          $("#ddlgetProducts").append(
            "<option value='" + value.id + "'>" + value.product_name + "</option>");
        });
      }
    });
  }

   $("#ddlproduct").on('select2:unselect', function (e) {
        var removedValue = e.params.data.id; // Get the deselected value
        if(removedValue){
            // Remove the deselected option from the select2 dropdown
            if($('#ddlproduct option[value="' + removedValue + '"]').remove()){
              removeToArray(removedValue);
              $('#ddlproduct').trigger('change.select2');
            }

        }
    });


  function loadProducts(page) {
    if (page == 1) {
      $("#inventtbl tbody").empty();
    }
    $.ajax({
      url: "{{url('/load-products')}}" + "?page=" + page,
      type: "GET",
      success: function(result) {
        $.each(result.data, function(index, value) {
          $("#inventtbl tbody").append(
            "<tr>" +
            "<td>" + value.item_code + "</td>" +
            "<td id='cell-prodName-"+value.id+"'>" + value.product_name + "</td>" +
            "<td id='action-cell-"+value.id+"'>" +
            "<i class='icofont icofont-ui-add text-success f-18 ' id='btn-add-prod-"+value.id+"' onclick='addToArray(" + value.id + ")' data-id='" + value.id + "' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>" +
            "</td>" +

            "</tr>"
          );
        });
      }
    });

    showProductSelected_list();
  }

  function loadProductsFromSearch(page) {
    if (page == 1) {
      $("#inventtbl tbody").empty();
    }
    $.ajax({
      url: "{{url('/load-products-by-search')}}" + "?page=" + page,
      type: "GET",
      data: {
        search: $("#search").val()
      },
      success: function(result) {
        $.each(result.data, function(index, value) {
          $("#inventtbl tbody").append(
            "<tr>" +
            "<td>" + value.item_code + "</td>" +
            "<td id='cell-prodName-"+value.id+"'>" + value.product_name + "</td>" +
            "<td id='action-cell-"+value.id+"'>" +
            "<i class='icofont icofont-ui-add text-success f-18' id='btn-add-prod-"+value.id+"' onclick='addToArray(" + value.id + ")' data-id='" + value.id + "' data-toggle='tooltip' data-placement='top' title='' data-original-title='Add Product'></i>" +
            "</td>" +

            "</tr>"
          );
        });

      }
    });
  }

  function addToArray(id) {
    if (!products.includes(id)) {
         products.push(id);

        //  $("#btn-add-prod-"+id).remove();
         $("#action-cell-"+id).append("<i class='icofont icofont-trash text-danger f-18 m-l-1' id='removeAction-"+id+"' onclick='removeToArray("+id+")' data-id='" + id + "' data-toggle='tooltip' data-placement='top' title='' data-original-title='Remove Product'></i>");

         $("#btn-add-prod-"+id).remove();
         showProductSelected_list();
    }
  }

  function showProductSelected_list(){
     if(products.length == 0){
         $("#showProduct_md").empty();
     }else{
         $("#showProduct_md").empty();
         $("#showProduct_md").addClass('m-t-2').append('<h3>Selected Product'+(products.length > 1 ? 's' : '')+'</h3>');
      $.each(products,function(i){
          $("#showProduct_md").append('<label class="badge badge-success badge-lg">'+$("#cell-prodName-"+products[i]).text()+' <span class="badge badge-danger badge-header3 pointer" onclick="removeToArray('+products[i]+')"><i class="icofont icofont-close"></i></span></label>'+(products.length > 1 ? '<br/>' : '')+'');
      });
     }
  }

  function removeToArray(id) {

    //   let index = products.indexOf(parseInt(id));

        //   if (index == 1) { // only splice array when item is found
        //     products.splice(index, 1); // 2nd parameter means remove one item only
        //     alert()
        //   }

    products = jQuery.grep(products, function(value) {
      return value != id;
    });

    $('#ddlproduct option[value="'+id+'"]').remove()

    //  $("#btn-add-prod-"+id).removeClass('icofont-ui-checked').addClass('icofont-ui-add');

     $("#action-cell-"+id).append("<i class='icofont icofont-ui-add text-success f-18' id='btn-add-prod-"+id+"' onclick='addToArray("+id+")' data-id='"+id+"' data-toggle='tooltip' data-placement='top' title='' data-original-title='Add Product'></i>");

     $("#removeAction-"+id).remove();
     showProductSelected_list();
  }

$('#details-modal').on('hidden.bs.modal', function () {
    if($("#ddlproduct option:selected").length == 0){
        $("#showProduct_md").empty();
        $.each(products,function(i){
            //   removeToArray(products[i])
            if($("#removeAction-"+products[i]).length == 1){
              $("#removeAction-"+products[i]).remove();
            //   alert($("#action-cell-"+products[i]).length)
               if($("#action-cell-"+products[i]).length == 0){
                   $("#action-cell-"+products[i]).empty();
                $("#action-cell-"+products[i]).append("<i class='icofont icofont-ui-add text-success f-18' id='btn-add-prod-"+products[i]+"' onclick='addToArray("+products[i]+")' data-id='"+products[i]+"' data-toggle='tooltip' data-placement='top' title='' data-original-title='Add Product'></i>");
               }
            }
        })
    }
})

  $("#btnSearch").click(function() {
    page = 1;
    loadProductsFromSearch(page);
  })
  $("#divProd").scroll(function() {
    page = page + 1;
    // ؕؕؕcount = 0;
    if ($('#search').val() != "") {
      loadProductsFromSearch(page);
    } else {
      loadProducts(page);
    }
  });


  $('#btnSave').click(function(e) {
    $("#details-modal").modal("hide");
    // console.log(products);
    $.ajax({
      url: "{{url('/load-products-for-dropdown')}}",
      type: "GET",
      data: {
        prod: products
      },
      success: function(result) {
        // console.log(result)
        $("#ddlproduct").empty();
        $("#ddlproduct").append("<option value=''>Select Products</option>");
        $.each(result, function(item, value) {
          $("#ddlproduct").append(
            "<option value='" + value.id + "' selected>" + value.name + "</i></option>"
          );
        });
      }
    });
  })


  function loadCustomers() {
    $.ajax({
      url: "{{url('/load-customers')}}",
      type: "GET",
      success: function(result) {
        $("#customers").empty();
        $("#customers").append("<option value=''>Select Customers</option>");
        $.each(result, function(item, value) {
          $("#customers").append(
            "<option value='" + value.id + "'>" + value.name + "</option>");
        });
      }
    });
  }

  //RANDOM CODE GENERATE
  function makeid(length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    $('#code').val(result.toUpperCase());
    $('#salename').html(result.toUpperCase());
  }

  $('#code').change(function() {
    var value = $('#code').val();
    $('#salename').html(value.toUpperCase());
  });
  //Set Mimimum purchase Summary
  $('#minValue').change(function() {
    var result = "On minimum amount of " + $('#minValue').val();
    $('#disc_min').append("<li>" + result + "</li>");
  });

  $('#discountvalue').change(function() {
    discountValueSet();
  });

  $('#type').change(function() {

    if ($('#type').val() == 3) {
      $('#buyonegetone').css('display', 'block');
      $('#divappliesTo').css('display', 'none');
      $('#divdiscountvalue').css('display', 'none');
      $('#divminChkBox').css('display', 'none');
      $('#discountvalue').val('');

    } else {
      $('#buyonegetone').css('display', 'none');
      $('#divappliesTo').css('display', 'block');
      $('#divdiscountvalue').css('display', 'block');
      $('#divminChkBox').css('display', 'block');
      discountValueSet();
    }
  });

  function discountValueSet() {
    if ($('#discountvalue').val() != "") {
      var check = $('#type').val();
      var result;
      if (check == 1) {
        result = $('#discountvalue').val() + "% off on " + $("input[name='radio']:checked").val();
      } else if (check == 2) {
        result = "Rs." + $('#discountvalue').val() + " off on " + $("input[name='radio']:checked").val();
      }
      $('#disc_value').empty();
      $('#disc_value').append("<li>" + result + "</li>")
    }
  }

  $('#chkTotalUsage').change(function() {
    if ($('#chkTotalUsage').prop("checked") == true) {
      $('#limitTotalDiv').css('display', 'block');
    } else if ($('#chkTotalUsage').prop("checked") == false) {
      $('#limitTotalDiv').css('display', 'none');
      $('#totlusage').val('');
      $('#disc_usage').empty();
    }
  });

  $('#totlusage').change(function() {
    var value = "";
    if ($('#chkTotalUsage').prop("checked") == true) {
      value = "Limit of " + $('#totlusage').val() + " uses";
      $('#disc_usage').empty();
      $('#disc_usage').append("<li>" + value + "</li>");
    } else if ($('#chkTotalUsage').prop("checked") == false) {
      if ($('#chkTotalUsage').prop("checked") == true && $('#chkonecustomer').prop("checked") == true) {
        value = "Limit of " + $('#totlusage').val() + " uses, one use per customer";
        $('#disc_usage').empty();
        $('#disc_usage').append("<li>" + value + "</li>");
      } else {
        $('#disc_usage').empty();
      }

    }
  });

  $('#chkonecustomer').change(function() {
    if ($('#chkTotalUsage').prop("checked") == true && $('#chkonecustomer').prop("checked") == true) {
      value = "Limit of " + $('#totlusage').val() + " uses, one use per customer";
      $('#disc_usage').empty();
      $('#disc_usage').append("<li>" + value + "</li>");
    } else if ($('#chkonecustomer').prop("checked") == true) {
      value = "One use per customer";
      $('#disc_usage').empty();
      $('#disc_usage').append("<li>" + value + "</li>");
    } else if ($('#chkonecustomer').prop("checked") == false) {
      if ($('#chkTotalUsage').prop("checked") == true) {
        value = "Limit of " + $('#totlusage').val() + " uses";
        $('#disc_usage').empty();
        $('#disc_usage').append("<li>" + value + "</li>");
      } else {
        $('#disc_usage').empty();
      }

    }

  });

  $('#startdate').change(function() {

    var d = new Date($('#startdate').val());
    var month = myFunction($('#startdate').val());
    var value = "Active from " + month + " " + d.getDate();
    $('#disc_date').empty();
    $('#disc_date').append("<li>" + value + "</li>")

  });

  $('#enddate').change(function() {
    var day = new Date($('#startdate').val());
    var monthname = myFunction($('#startdate').val());

    var d = new Date($('#enddate').val());
    var month = myFunction($('#enddate').val());

    var value = "Active from " + monthname + " " + day.getDate() + " to " + month + " " + d.getDate();
    $('#disc_date').empty();
    $('#disc_date').append("<li>" + value + "</li>")
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
  var daysArray = [];

  $("#selectedAlldays").on('click',function(){
      var Alldays = ['mon','tue','wed','thu','fri','sat','sun'];

     if($(this).is(':checked')){
      $.each(Alldays,function(i,v){
          if(!$('#'+v).is(':checked')){
              $('#'+v).attr('checked',true);

              if(jQuery.inArray(v, daysArray) === -1){
                   daysArray.push(v);

              }
          }
      })
     }else{
      $.each(Alldays,function(i,v){
          if($('#'+v).is(':checked')){
              $('#'+v).attr('checked',false);

              const index = daysArray.indexOf(v);
              // console.log(index)
              if (index > -1) { // only splice array when item is found

                daysArray.splice(index, 1); // 2nd parameter means remove one item only

              }
          }
      })
     }

  });

  function selectedCheckbox(id, val) {
    // var label = $(e).siblings("label");
    if ($('#' + id).is(':checked')) {
      daysArray.push(id);
      // console.log("Checked : ", daysArray)
    } else {
      const index = daysArray.indexOf(id);
      // console.log(index)
      if (index > -1) { // only splice array when item is found
        daysArray.splice(index, 1); // 2nd parameter means remove one item only
      }

      if($('#selectedAlldays').is(':checked')){
          $('#selectedAlldays').prop('checked', false);
      }
      console.log("UnChecked : ", daysArray)
    }

  }



  $('#btnSubmit').click(function(e) {
    e.preventDefault();

    if (daysArray.length > 0) {
      daysArray.forEach(function(item) {
        // console.log(item);
        $("#daysdiv").append('<input type="hidden" name="days[]" value="'+item+'"/>');
      });
    }

    $('#discountform').submit();

    // $.ajax({
    //   url: "{{url('/save-discount')}}",
    //   type: "POST",
    //   data: {
    //     _token: "{{csrf_token()}}",
    //     code: $('#code').val(),
    //     type: $('#type').val(),
    //     discountvalue: $('#discountvalue').val(),
    //     minValue: $('#minValue').val(),
    //     discount_customer_eligibility: $('#discount_customer_eligibility').val(),
    //     applies: $("input[name='radio']:checked").val(),
    //     eligibility: $("input[name='customerradio']:checked").val(),
    //     totlusage: $('#totlusage').val(),
    //     startdate: $('#startdate').val(),
    //     starttime: $('#starttime').val(),
    //     enddate: $('#enddate').val(),
    //     endtime: $('#endtime').val(),
    //     wesbiteId: $('#website').val(),
    //     discount_applies_to: $('#discount_applies_to').val(),
    //     days: daysArray
    //   },
    //   success: function(response) {
    //     console.log(response);
    //     if (response.status == 200) {
    //       window.location.href = "{{url('get-discount')}}";
    //     } else {
    //       alert("Error")
    //     }
    //   },
    //   error: function(jqXHR, textStatus, errorThrown) {
    //     alert("Some error occurred.")
    //     console.log(textStatus, errorThrown);
    //   }
    // });
  });

  $('#ddldiscountedvalue').change(function() {
    if ($('#ddldiscountedvalue').val() == 1) {
      $('#divdiscountpercentage').css('display', 'block');
    } else {
      $('#divdiscountpercentage').css('display', 'none');
    }
  });
</script>


@endsection
