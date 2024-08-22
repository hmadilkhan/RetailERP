@extends('layouts.master-layout')

@section('title','Master')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
      <div class="card-header">
        <h5 class="card-header-text">Master Payment</h5>
        <h5 class=""><a href="{{ url('ledger-details',$masterID) }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>
      </div>
      <div class="card-block">
        <div class="row">
        
           <div class="col-lg-3 col-sm-3 col-xs-3">
               <div class="form-group">
                  <label class="form-control-label">Current Balance</label>
                  <input type='text' class="form-control" id="amount" name="amount" placeholder="Pending Amount" value="{{number_format((($balance == 0) ? '0' : $balance),0)}}" />  
                  <span class="help-block text-danger" id="dbox"></span>  
                </div>
          </div>
           <div class="col-lg-3 col-sm-3 col-xs-3">
               <div class="form-group">
                  <label class="form-control-label">Enter Payment</label><label id="manualAmount" class="form-control-label f-right text-danger">0</label>
                  <input type='text' class="form-control" id="debit" name="debit" placeholder="Enter Received Amount Here"/>  
                  <span class="help-block text-danger" id="dbox"></span>  
                </div>
          </div>
            <div class="col-lg-3 col-sm-3 col-xs-3">
               <div id="user" class="form-group">
                  <label class="form-control-label">Balance Amount</label>
                  <input type='text' class="form-control" id="bal" name="bal" placeholder="Enter Debit Amount"/>  
                  <span class="help-block text-danger" id="dbox"></span>  
                </div>
          </div>
          <div class="col-lg-3 col-sm-3 col-xs-3">
               <div class="form-group">
                  <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25">
                    <i class="icofont icofont-plus"> </i>Submit Payment
                  </button>  
                </div>
          </div>
        </div>
            
            
      </div>
    </div>
    <div class="card">
     <div class="card-header ">
         <h5 class="card-header-text ">Master Ledger Details</h5>

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
                 <th hidden="true"></th>
                 <th>Receipt No.</th>
                 <th>Total Amount</th>
                 <th>Debit</th>
                 <th>Credit</th>
                 <th>Balance</th>
                 <th>Paid Amount</th>
                 
              </tr>
           </thead>
         <tbody>
      	@if($details)
      		@foreach($details as $value)
      			<tr>
      				<td>
                 <div class="rkmd-checkbox checkbox-rotate">
                   <label class="input-checkbox checkbox-primary">
                     <input type="checkbox" id="checkbox32{{ $value->master_account_id }}" onchange="manual('{{ $value->master_account_id }}')" class="chkbx" data-id="{{ $value->master_account_id }}">
                       <span class="checkbox"></span>
                       </label>
                   <div class="captions"></div>
                 </div>
              </td>
	  				<td hidden="true"><label>{{$value->master_account_id }}</label></td> 
            <td hidden="true"><label>{{$value->receipt_id}}</label></td> 
	  				<td>{{$value->receipt_no}}</td>
	  				<td>{{number_format($value->total_amount,2)}}</td>
	  				<td>{{number_format($value->debit,2)}}</td>
            <td>{{number_format($value->credit,2)}}</td>
	  				<td><label id="netAmount{{ $value->master_account_id }}">{{number_format($value->total_amount - $value->debit,0)}}</label></td>
            <td><label id="amtlbl{{$value->master_account_id }}">0</label></td>
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
//$('#checkbox325').prop("checked", true);


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

   $('#debit').change(function(){
    if(mode == 1){

   		var total_paid = $('#debit').val();
   		$("#item tbody tr ").each(function(k){ 
   			var id ;
   			var amount ;
   			$(this).find("label").each(function(i){
   				if(i == 1)
   				{
   					id = $(this).text();
   				}
   				else if(i == 3)
   				{
            var a = $(this).text();
            a = a.replace(/\,/g,''); 
            a = parseInt(a,10);
   					total_paid = total_paid - a;
            console.log(total_paid);
            if(total_paid < 0)
            {
              $('#user').removeClass('has-success');
              $('#user').addClass('has-danger');
              $('#bal').val(total_paid);
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
              $('#bal').val(total_paid);
              $('#amtlbl'+id).html(a);
              $('#checkbox32'+id).prop("checked", true);
            }
   				}
   			});
   		}); 
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
        $('#debit').attr('disabled',false);
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
        $('#debit').attr('disabled',true);
        
      }
   });
   
  //set the global variable for checking Mode
  let mode = "";

  //Manual Mode Active
  function manual(id)
  {
    if(mode == 2){
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
          var cal = parseInt(amount) - parseInt($('#debit').val()); 
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
    var totalCredit = $('#amount').val();

    totalCredit = totalCredit.replace(/\,/g,'');
 
    if($('#debit').val() == "")
    {
      swal({
            title: "Error",
            text: "Enter Payment Account!",
            type: "error"
          });
    }
    else
    {
      var id;
      var receiptno;
      var netamt;
      var paidamt;

      $("#item tbody tr ").each(function(k){ 
        $(this).find("label").each(function(i){
          if(i == 1)
          {
            id = $(this).text();

          }
          else if(i == 2)
          {
            receiptno = $(this).text();

          }
          else if(i == 3)
          {
            netamt = $(this).text();

          }
          else if(i == 4)
          {
            paidamt =$(this).text();
            paidamt = paidamt.replace(/\,/g,'');

          }

        });
        
        totalCredit = totalCredit - paidamt;

         
        addPayment(id,receiptno,netamt,paidamt,totalCredit)
       //setTimeout(addPayment(id,netamt,paidamt,totalCredit) ,1000);
      });
    }
  });

  function addPayment(id,receiptno,net,bal,credit)
  {

    var status ;
    var amount;

    net = net.replace(/\,/g,''); 
    var calculation = parseInt(net) - parseInt(bal);
    if(bal == 0)
    {
      status = 2;
    }
    else if(calculation == 0)
    {
      status = 2;
      amount = net;
    }
    else
    {
      status = 1;
      amount = bal;
    }

    if(bal != 0){
     $.ajax({
            url: "{{url('/createPayment')}}",
            type: 'POST',
            data: {_token:"{{csrf_token()}}",id:id,master:'{{$masterID}}',receipt:receiptno,bal:calculation,net:net,status:status,amount:amount,totalCredit:credit},
              async:false,
            success:function(resp){
              if(resp == 1){
                swal({
                      title: "Thank You!!!",
                      text: "Payment Successfully Deducted from Your Account!",
                      type: "success"
                      },function(isConfirm){
                         if(isConfirm){
                           window.location="{{ url('ledger-payment',$masterID) }}";
                         }
                      });
              }
            }
          }); 
    }
  }
  </script>

@endsection