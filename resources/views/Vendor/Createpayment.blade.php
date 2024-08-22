@extends('layouts.master-layout')

@section('title','Make Payment')

@section('breadcrumtitle','Make Payment')
@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
{{--    $('#cover-spin').show(0)--}}
{{--    $('#cover-spin').hide(0)--}}
  <section class="panels-wells">
    <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Vendor Received Payment</h5>
        <h5 class=""><a href="{{ url('ledgerlist',$slug) }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>
      </div>
      <div class="card-block">
        <div class="row">

           <div class="col-lg-2 col-sm-2 col-xs-2">
               <div class="form-group">
                  <label class="form-control-label">Current Balance</label>
                  <input readonly="true" type='text' class="form-control" id="amount" name="amount" placeholder="Pending Amount" value="{{number_format((($balance == 0) ? '0' : $balance[0]->balance),0)}}" />
                  <span class="help-block text-danger" id="dbox"></span>
                </div>
          </div>
		  <div class="col-lg-2 col-sm-2 col-xs-2">
               <div class="form-group">
                  <label class="form-control-label">Use Advance</label>
				   <div class="rkmd-checkbox checkbox-rotate f-right">
                    <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="chkactive" name="chkactive" class="mainchk" onchange="useAdvanceToggle()">
                        <span class="checkbox"></span>
                    </label>
                    
                </div>
                  <input readonly="true" type='text' class="form-control" id="advance" name="advance"  value="{{$advance}}" />
                  <span class="help-block text-danger" id="dbox"></span>
                </div>
          </div>
           <div class="col-lg-2 col-sm-2 col-xs-2">
               <div class="form-group">
                  <label class="form-control-label">Enter Payment</label><label id="manualAmount" class="form-control-label f-right text-danger">0</label>
                  <input type='text' class="form-control" id="debit" name="debit" placeholder="Enter Received Amount Here"/>
                  <span class="help-block text-danger" id="dbox"></span>
                </div>
          </div>
            <div class="col-lg-2 col-sm-2 col-xs-2">
               <div id="user" class="form-group">
                  <label class="form-control-label">Balance Amount</label>
                  <input readonly="true" type='text' class="form-control" id="bal" name="bal" />
                  <span class="help-block text-danger" id="dbox"></span>
                </div>
          </div>

            <div class="col-lg-2 col-sm-22 col-xs-22">
                <div class="form-group">
                    <label class="form-control-label">Payment Method</label>
                    <select class="form-control select2" data-placeholder="Payment Method" id="payMethod" name="payMethod">
                        <option value="">Payment Method</option>
                        <option value="Cash">Cash</option>
                        <option value="Credit Card">Bank Account</option>
                    </select>
                    <span class="help-block text-danger" id="vdbox"></span>
                </div>
            </div>
            <div id="credit" style="display: none;">
                <div class="col-lg-2 col-sm-2 col-xs-2">
                <div class="form-group">
                  <label class="form-control-label">Select Bank Account</label>
                  <select class="form-control select2" data-placeholder="Select Bank Account" id="bank" name="bank">
                      <option value="">Select Bank Account</option>
                       @if($banks)
                        @foreach($banks as $value)
                              <option value="{{$value->bank_account_id}}">{{$value->account_title}} | {{$value->account_no}} | {{$value->bank_name}} | {{$value->branch_name}}</option>
                        @endforeach
                       @endif
                  </select>
                  <span class="help-block text-danger" id="vdbox"></span>
                </div>
          </div>
                <div class="col-lg-2 col-sm-2 col-xs-2">
                <div class="form-group">
                  <label class="form-control-label">Cheque No</label>
                  <input  type='text' class="form-control" id="cheque" name="cheque" />
                  <span class="help-block text-danger" id="cheque"></span>
                </div>
          </div>
            </div>
        </div>
        <div class="row">
         <div class="col-lg-9 col-sm-9 col-xs-2">
          <label class="form-control-label">Narration</label>
          <textarea rows="2" class="form-control" id="narration" class="narration"></textarea>

         </div>
          <div class="col-lg-3 col-sm-3 col-xs-2">
               <div class="form-group">
				<label class="form-control-label"></label>
                  <button type="button" id="btnSubmit"  class="btn btn-primary waves-effect waves-light  m-t-30">
                    <i class="icofont icofont-plus"> </i>Submit Payment
                  </button>
                </div>
          </div>
        </div>


      </div>
    </div>
    <div class="card">
     <div class="card-header ">
         <h5 class="card-header-text ">Vendor Ledger Details</h5>

                 <div class="rkmd-checkbox checkbox-rotate f-right">
                    <label>Auto Adjustment</label>
                    <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="checkboxAuto" class="chkbx " data-id="">
                    <span class="checkbox"></span>
                 </div>



         </div>

       <div class="card-block">
           <div class="project-table">

         <table id="item" class="table table-striped nowrap dt-responsive" width="100%">
           <thead>
              <tr>
                 <th>

                 </th>
                 <th hidden="true"></th>
                 <th>Purchase No.</th>
                 <th>Total Amount</th>
                 <th>Tax Amount</th>
                 <th>Net Amount</th>
                 <th>Paid Amount</th>

              </tr>
           </thead>
         <tbody>
      	@if($purchases)
      		@foreach($purchases as $value)
      			<tr>
      				<td>
                         <div class="rkmd-checkbox checkbox-rotate">
                           <label class="input-checkbox checkbox-primary">
                             <input type="checkbox" id="checkbox32{{ $value->purchase_id }}" onchange="manual('{{ $value->purchase_id }}')" class="chkbx" data-id="{{ $value->purchase_id }}">
                               <span class="checkbox"></span>
                               </label>
                           <div class="captions"></div>
                         </div>
                    </td>
	  				<td hidden="true"><label>{{$value->purchase_id }}</label></td>
	  				<td>{{$value->po_no}}</td>
	  				<td>{{number_format($value->total_amount,2)}}</td>
	  				<td>{{number_format($value->tax_amount,2)}}</td>
	  				<td><label id="netAmount{{ $value->purchase_id }}">{{number_format($value->balance_amount,0)}}</label></td>
                    <td><label id="amtlbl{{$value->purchase_id }}">0</label></td>
      			</tr>
      		@endforeach
      	@endif


         </tbody>
     </table>
     <p class="f-14"><b>Note : The details of all the payments will be available after receiving the Purchase Order.</b></p>
        </div>
    </div>
   </div>
</section>
@endsection


@section('scriptcode_three')

  <script type="text/javascript">
	function useAdvanceToggle() {
		let paidAmount = parseInt($("#debit").val());
		let advance = "{{$advance}}";
		if(isNaN(paidAmount)){
			paidAmount = 0;
		}
		
		console.log(paidAmount + advance)

		if($('#chkactive').is(":checked")){
			$("#debit").val(paidAmount + parseInt(advance));
		}
		else{
			$("#debit").val(paidAmount - parseInt(advance));
		}
	}
//$('#checkbox325').prop("checked", true);
var car = [];
var advance = false;
// console.log("Array respone: "+car);
  $(".select2").select2();

   $('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'',
          searchPlaceholder: 'Search Purchase',
          lengthMenu: '<span></span> _MENU_'

        }

    });

    $('#payMethod').change(function() {

        if ($('#payMethod').val() == "Cash")
        {
            $('#credit').css('display','none');
        }
        else
        {
            $('#credit').css('display','block');
        }
    });

   $('#debit').change(function(){
	let debitamount = ($('#debit').val() != "" ? $('#debit').val() : 0)
    var cb = $('#amount').val();
	console.log(cb)
    cb = cb.replace(/\,/g,''); //remove comma from amount
    cb = parseFloat(cb);

    var totalBalance = parseFloat(cb) - parseFloat(debitamount);
    var netbalance = (parseFloat(cb) + parseFloat(debitamount));
     console.log("totalBalance "+(parseFloat(cb) + parseFloat(debitamount)));
    $('#bal').val(netbalance);
	
    if(mode == 1){
			var total_paid = parseFloat($('#debit').val());
			$("#item tbody tr ").each(function(k){
				var id ;
				var amount ;
				$(this).find("label").each(function(i){
					if(i == 1)
					{
						id = $(this).text();
					}
					else if(i == 2)
					{
						var a = $(this).text();
						a = a.replace(/\,/g,'');
						a = parseInt(a,10);
						total_paid = parseFloat(total_paid) - parseFloat(a);
					
						if(total_paid < 0)
						{
						  $('#user').removeClass('has-success');
						  $('#user').addClass('has-danger');
						  // $('#bal').val(total_paid);
						  var calc = a + total_paid;
						  if(calc > 0){
							$('#amtlbl'+id).html(a + total_paid);
							$('#checkbox32'+id).prop("checked", true);
						  }else{
							$('#amtlbl'+id).html(0);
							$('#checkbox32'+id).prop("checked", false);
						  }
						}
						else
						{
						  $('#user').removeClass('has-danger');
						  $('#user').addClass('has-success');
						  // $('#bal').val(total_paid);
						  $('#amtlbl'+id).html(a);
						  $('#checkbox32'+id).prop("checked", true);
						  
						  if(netbalance > 0){
							advance = true
						  }
						}
					}
				});
			});
   		}else{
			$('#amtlbl'+selectedPurchaseOrderId).html(parseFloat($('#debit').val()));
		}
   });
   //When Checkbox checked or unchecked fired the following function
   $('#checkboxAuto').change(function(){
      if($(this).prop("checked") == true)
      {
        mode = 1;
        notify('Auto Mode Active', 'inverse');
        $('#bal').val('');
        $('#debit').val('');
        // $('#debit').attr('disabled',false);
        $('#manualAmount').html('');

      }
      else
      {
        mode = 2;

        notify('Manual Mode Active', 'inverse');
        unchecked();
        $('#user').removeClass('has-success');
        $('#user').removeClass('has-danger');
        $('#manualAmount').css('display','block');
        $('#bal').val('');
        $('#debit').val('');
        // $('#debit').attr('disabled',true);

      }
   });

  //set the global variable for checking Mode
  let mode = "";
  let selectedPurchaseOrderId = "";

  //Manual Mode Active
  function manual(id)
  {
    if(mode == 2){
		selectedPurchaseOrderId = id;
      if($('#checkbox32'+id).prop("checked") == true)
        {
          var a = $('#netAmount'+id).html();
          $('#amtlbl'+id).html(a);
          a = a.replace(/\,/g,'');
          a = parseInt(a,10);
          var amount = a + parseInt($('#manualAmount').html());
          $('#manualAmount').html(amount);
          $('#debit').val(amount);
          var amount = $('#amount').val();
          amount = amount.replace(/\,/g,'');
          var cal = parseInt(amount) + parseInt($('#debit').val());
          $('#bal').val(cal);
        }
        else
        {
          var a = $('#netAmount'+id).html();
          $('#amtlbl'+id).html('0');
          a = a.replace(/\,/g,'');
          a = parseInt(a,10);
          var amount =  parseInt($('#manualAmount').html()) - a ;
          $('#manualAmount').html(amount);
          $('#debit').val(amount);
          var amount = $('#amount').val();
          amount = amount.replace(/\,/g,'');
          var cal = parseInt(amount) - parseInt($('#debit').val());
          $('#bal').val(cal);
        }

    }

  }

  //Unchecked all the check boxes on checked or unchecked
  function unchecked()
  {
    $("#item tbody tr ").each(function(k){
        var id ;
        $(this).find("label").each(function(i){
          if(i == 1)
          {
            id = $(this).text();
            $('#checkbox32'+id).prop("checked", false);
            $('#amtlbl'+id).html(0);
          }
        });
      });

  }
	
  //Set the states on first Page Load
  $('#checkboxAuto').prop("checked", true);
  $('#manualAmount').css('display','none');
  mode = 1;

  //Final Submit Calls Here
  $('#btnSubmit').click(function(e){
	$('#btnSubmit').prop('disabled', true);
    var totalCredit = $('#amount').val();
    totalCredit = totalCredit.replace(/\,/g,'');
	var batch_no = Date.now().toString(36) + Math.random().toString(36).substr(2);
	console.log(batch_no)
	console.log(advance)
	
	
    if($('#debit').val() == "")
    {
      swal({
            title: "Error",
            text: "Enter Payment Account!",
            type: "error"
          });
		$('#btnSubmit').prop('disabled', false);
    }
    else if($('#payMethod').val() == "")
    {
        swal({
            title: "Error",
            text: "Select Payment Mode !",
            type: "error"
        });
		$('#btnSubmit').prop('disabled', false);
    }
    else if($('#narration').val() == "")
    {
        swal({
            title: "Error",
            text: "Enter Narration !",
            type: "error"
        });
		$('#btnSubmit').prop('disabled', false);
    }

    else
    {

      var id;
      var netamt;
      var paidamt;
      var bankBalance;
      var cashBalance;
        id = "{{count($purchases)}}";

        if ($('#payMethod').val() == "Cash")
        {
            //Check Cash Ledger Balance for Payments
            $.ajax({
                url: "{{url('/get-cash-balance')}}",
                type: 'POST',
                data: {_token:"{{csrf_token()}}"},
                async : false,
                success:function(resp){
                    if(resp == ""){
                        cashBalance = parseFloat(0);
                    }else{
                        cashBalance = parseFloat(resp[0].balance);
                    }
                }
            });

            var paid = parseFloat($('#debit').val());

            if(paid > cashBalance){

                swal({
                    title: "Error",
                    text: "Cash ledger does not have sufficient Balance for this transaction!",
                    type: "error"
                });
				$('#btnSubmit').prop('disabled', false);

            }else{
				
                if (id == undefined) {
                    addPayment(id, $('#amount').val(), $('#debit').val(), $('#bal').val(), cashBalance, "Cash",batch_no)
                }else{

                    $("#item tbody tr ").each(function (k) {

                        $(this).find("label").each(function (i) {

                            if (i == 1) {
                                id = $(this).text();
                            } else if (i == 2) {
                                netamt = $(this).text();
                            } else if (i == 3) {
                                paidamt = $(this).text();
                            }else if(i == 0){
                                // console.log("Cash Manual Run" + $(this).text())
                            }

                        });
                        
                        netamt = netamt.replace(/\,/g, '');
						
                        totalCredit = parseFloat(totalCredit) + parseFloat(paidamt.replace(/\,/g, ''));
						
                        addPayment(id, netamt, paidamt, totalCredit, cashBalance,$('#payMethod').val(),batch_no)
                    });
                }

            }
        }
        else {

            // IF PAYMENT MODE SELECTED AS BANK THEN THIS CODE BLOCK RUNS
            if ($('#bank').val() == "") {
                swal({
                    title: "Error",
                    text: "Please Select Bank!",
                    type: "error"
                });
            } else if ($('#cheque').val() == "") {
                swal({
                    title: "Error",
                    text: "Enter Cheque Number!",
                    type: "error"
                });
            } else {
                $.ajax({
                    url: "{{url('/get-bank-balance')}}",
                    type: 'POST',
                    data: {_token: "{{csrf_token()}}", bankid: $('#bank').val()},
                    async: false,
                    success: function (resp) {
                        if(resp == "") {
                            bankBalance = parseFloat(0);
                        }else{
                            bankBalance = parseFloat(resp[0].balance);
                        }


                    }
                });
                var paid = parseFloat($('#debit').val());
                if (paid > bankBalance) {
                    swal({
                        title: "Error",
                        text: "Bank does not have sufficient Balance for this transaction!",
                        type: "error"
                    });
					$('#btnSubmit').prop('disabled', false);

                } else {
                    if (id == undefined) {

                        addPayment(id, $('#amount').val(), $('#debit').val(), $('#bal').val(), bankBalance, "Credit",batch_no)
                    } else {
                        $("#item tbody tr ").each(function (k) {
                            $(this).find("label").each(function (i) {

                                if (i == 1) {
                                    id = $(this).text();
                                } else if (i == 2) {
                                    netamt = $(this).text();
                                } else if (i == 3) {
                                    paidamt = $(this).text();
                                }
                            });
                            console.log("Bank Manual Run" + id)
                            netamt = netamt.replace(/\,/g, '');

                            totalCredit = parseFloat(totalCredit) + parseFloat(paidamt);
							console.log("Net Amount" + netamt + "--- Paid Amount : "+paidamt + "--- Total Credit : "+totalCredit)
                            // addPayment(id, netamt, paidamt, totalCredit, bankBalance,$('#payMethod').val())
                        });
                    }
                }
            }
        }//Main Bracket End
    }
  });

  function addPayment(id,net,bal,credit,bankBalance,mode,batch_no)
  {
	console.log("Batch No ",batch_no)
    var status ;
    var amount;

    net = net.replace(/\,/g,'');
	bal = bal.replace(/\,/g,'');
    var calculation = parseInt(net) - parseInt(bal);
	
    if(bal == 0)
    {
      status = 0;
    }
    else if(calculation == 0)
    {
      status = 9;
      amount = net;
    }
    else
    {
      status = 8;
      amount = bal;
    }
	
      if (mode == "Cash")
         {
         if(bal != 0) {
			 
             $.ajax({
                 url: "{{url('/make-cash-payment')}}",
                 type: 'POST',
                 data: {
                     _token: "{{csrf_token()}}",
                     id: id,
                     vendor: '{{$vendor}}',
                     vendorName : '{{$vendorName[0]->vendor_name}}',
                     bal: calculation,
                     status: status,
                     amount: amount,
                     totalCredit: credit,
					 batch_no:batch_no,
                     narration: $('#narration').val(),
                     payment: $('#debit').val()
                 },
                 async: false,
                 success: function (resp) {
					 console.log(resp)
					 $('#btnSubmit').prop('disabled', false);
					 
                     if (resp > 0) {
						 
						 if($('#bal').val() > 0){
							addAdvancePayment('{{$vendor}}',$("#bal").val(),$('#narration').val());
						 }else{
							 swal({
								 title: "Thank You!!!",
								 text: "Payment Successfully Deducted from Your Account!",
								 type: "success"
							 }, function (isConfirm) {
								 if (isConfirm) {
									 // Insert into bank ledger
									 
									 location.reload();
									 // window.location="{{ url('ledgerlist',$slug) }}";
								 }
							 });
						 }
                     } else {

                         swal({
                             title: "Cancelled",
                             text: "Cannot add Debit amount :",
                             type: "warning"
                         }, function (isConfirm) {
                             if (isConfirm) {
                                 window.location = "{{ url('ledgerlist',$vendor) }}";
                             }
                         });
                     }

                 }
				 
             });
         }
      }
      else{
         if(bal != 0){
             $.ajax({
                url: "{{url('/make-payment')}}",
                type: 'POST',
                data: {_token:"{{csrf_token()}}",id:id,vendor:'{{$vendor}}',bal:calculation,status:status,amount:amount,totalCredit:credit,accountid:$('#bank').val(),cheque_number:$('#cheque').val(),narration:$('#narration').val()},
                async:false,
                success:function(resp){
                  car.push(resp);
                  var credit = bankBalance - parseFloat($('#debit').val());

                  if($('#bal').val() > 0){

                    swal({
                        title: "Account Information",
                        text: "Do You want to debit the remaining amount?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes Please!",
                        cancelButtonText: "No cancel plx!",
                        closeOnConfirm: false,
                        closeOnCancel: false
                      },
                      function(isConfirm){

                        if(isConfirm){
                                   $.ajax({
                                      url: "{{ url('debit-payment')}}",
                                      type: 'POST',
                                      data:{_token:"{{ csrf_token() }}",id:id,vendor:'{{$vendor}}',bal:calculation,status:status,amount:$('#bal').val(),totalCredit:credit,accountid:$('#bank').val(),cheque_number:$('#cheque').val(),narration:$('#narration').val()},
                                      dataType:"json",
                                      success:function(resp){
										  $('#btnSubmit').prop('disabled', false);
                                          if(resp == 1){
                                               swal({
                                                      title: "Debit Inserted",
                                                      text: "Debit Successfully Added to Your Account ?",
                                                      type: "success"
                                                 },function(isConfirm){
                                                     if(isConfirm){
                                                       window.location="{{ url('ledgerlist',$vendor) }}";
                                                     }
                                                 });
                                           }
                                      }

                                  });

                         }else {

                             swal({
                                      title: "Cancelled",
                                      text: "Cannot add Debit amount :",
                                      type: "warning"
                                 },function(isConfirm){
                                     if(isConfirm){
                                        window.location="{{ url('ledgerlist',$vendor) }}";
                                     }
                                 });
                         }
                      });
                  }else{

                    swal({
                          title: "Thank You!!!",
                          text: "Payment Successfully Deducted from Your Account!",
                          type: "success"
                          },function(isConfirm){
                                   if(isConfirm){
                                      //Insert into bank ledger
                                      bankcredit(credit,"Payment to Vendor ID : {{$vendor}} ");
                                     // window.location="{{ url('ledgerlist',$vendor) }}";

                                   }
                          });

                  }
                }
              });
         }
      }
  }
  
  function addIntoLedger()
  {
		$.ajax({
			url: "{{url('/add-into-ledger')}}",
			type: 'POST',
			data: {
			 _token: "{{csrf_token()}}",
			 id: id,
			 vendor: '{{$vendor}}',
			 bal: calculation,
			 debit: amount,
			 totalCredit: credit,
			 narration: $('#narration').val(),
			 payment: $('#debit').val()
			},
			async: false,
			success: function (resp) {
				
			}
		})
  }

  function bankcredit(bal,narration)
  {
    var payment = 0;
    $.ajax({
          url: "{{ url('vendor-payment')}}",
          type: 'POST',
          async : false,
          data:{_token:"{{ csrf_token() }}",accountid:$('#bank').val(),cheque_number:$('#cheque').val(),credit:$('#debit').val(),balance:bal,narration:$('#narration').val()},
          success:function(resp){
              car.forEach(element => {
                $.ajax({
                  url: "{{ url('vendor-payment-details')}}",
                  type: 'POST',
                  data:{_token:"{{ csrf_token() }}",account:element,payment:resp},
                  success:function(resp){

                  }
                });


              });// ForEach Loop End
          }
      });

    $.ajax({
          url: "{{ url('add-credit-bank')}}",
          type: 'POST',
          data:{_token:"{{ csrf_token() }}",accountid:$('#bank').val(),cheque_number:$('#cheque').val(),credit:$('#debit').val(),balance:bal,narration:narration,mode:$('#payMethod').val()},
          dataType:"json",
          success:function(resp){
               window.location= "{{url('voucher')}}/"+car[0];
               window.location="{{ url('ledgerlist',$vendor) }}";
          }
      });
  }
  
  function addAdvancePayment(vendor_id,advance,narration)
  {
		swal({
			title: "Advance Amount",
			text: "Do You want to save the advance amount?",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-success",
			confirmButtonText: "Yes Please!",
			cancelButtonText: "No cancel plx!",
			closeOnConfirm: false,
			closeOnCancel: false
		  },
		  function(isConfirm){

			if(isConfirm){
				$.ajax({
					url: "{{ url('save-advance-payment')}}",
					type: 'POST',
					data:{_token:"{{ csrf_token() }}",date:"{{date('Y-m-d H:i:s')}}",vendor_id:vendor_id,debit:0,credit:advance,narration:narration},
					dataType:"json",
					success:function(resp){
					  $('#btnSubmit').prop('disabled', false);
						if(resp == 1){
							swal({
							  title: "Advance Saved",
							  text: "Advance Successfully Added to Your Vendor Account ?",
							  type: "success"
							},function(isConfirm){
							 if(isConfirm){
							   location.reload()
							 }
							});
						}
					}
				});

			}else {
				swal({
				 title: "Thank You!!!",
				 text: "Payment Successfully Deducted from Your Account!",
				 type: "success"
				}, function (isConfirm) {
				 if (isConfirm) {
					 location.reload();
				 }
				});
			}
		});

	// $.ajax({
		// url: "{{ url('save-advance-payment')}}",
		// type: 'POST',
		// data:{_token:"{{ csrf_token() }}",date:"{{date('Y-m-d H:i:s')}}",vendor_id:vendor_id,debit:0,credit:advance,narration:narration},
		// dataType:"json",
		// success:function(resp){
			// console.log(resp)
		// }
    // });  
  }

  // defaultCheck();
  // function defaultCheck()
  // {
  //   var car = [];
  //   car.push(1);
  //   car.push(2);
  //   // console.log(car);
  //   car.forEach(element => console.log(element));
  // }
  
	
  </script>



@endsection
