@extends('layouts.master-layout')

@section('title','Bank Account')

@section('breadcrumtitle','Bank Account')
@section('navaccountsoperation','active')
@section('navbankaccount','active')


@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">{{$getaccounts[0]->account_title}} | Transaction Panel</h5>
         <a class="f-right text-primary" onclick="divhide()">Collapse</a>
          <a href="{{ url('/view-accounts') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
         </div>      
       <div class="card-block" id="details">
       	 <div class="row">
                    <div class="col-lg-3 col-md-3">
                         <div class="form-group">
                        <label class="form-control-label">Enter Cheque/Deposit Slip Number</label>
                        <input class="form-control" type="number"
                         name="Chequenumber" id="Chequenumber" placeholder="Enter Cheque/Deposit Slip Number" required  />
                         <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <div class="form-group">
                          <label class="form-control-label">Cheque/Deposit Date</label>
                          <input class="form-control" type="text"
                           name="Chequedate" id="Chequedate" placeholder="DD-MM-YYYY"/>
                            <div class="form-control-feedback"></div>
                      </div>
                    </div>
                     <div class="col-lg-2 col-md-3">
                         <div class="form-group">
                             <label class="form-control-label">Debit</label>
                             <select id="mode" name="mode" class="form-control select2" data-placeholder="Select Mode">
                                 <option value="">Select Mode</option>
                                 <option value="cash">Cash</option>
                                 <option value="cheque">Cheque</option>
                             </select>
                             <span class="help-block"></span>
                         </div>
                     </div>
                  <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <label class="form-control-label">Debit</label>
                        <input class="form-control" type="number"
                         name="debit" id="debit" required value="0" />
                         <span class="help-block"></span>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-3">
                    <div class="form-group">
                        <label class="form-control-label">Credit</label>
                        <input class="form-control" type="number"
                         name="credit" id="credit" required value="0" />
                         <span class="help-block"></span>
                    </div>
                  </div>
           </div>
           <div class="row">
               <div class="col-lg-12 col-md-12">
                   <div class="form-group">
                       <label class="form-control-label">Narration:</label>
                       <textarea class="form-control" type="text"
                                 name="narration" id="narration" placeholder="Enter Narration here"></textarea>
                       <span class="help-block"></span>
                   </div>
               </div>
           </div>
          <button type="button" id="btndeposit" class="btn btn-md btn-primary waves-effect waves-light f-right" onclick="deposit()">
              Submit Transaction
          </button>
             </div>
             </div>   
          <div class="card">
             <div class="card-header">
                <h5 class="card-header-text"> {{$getaccounts[0]->account_title}} | Bank Statement Details</h5>
             </div>      
              <div class="card-block">
                <table id="tbldetails" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                   <thead>
                      <tr>
                         <th>Cheque/Deposit No</th>
                         <th>Date</th>
                         <th>Debit</th>
                         <th>Credit</th>
                         <th>Balance</th>
                          <th>Narration</th>
                      </tr>
                   </thead>
                   <tbody>
                     @foreach($ledger as $value)
                     <tr>
                        <td>{{$value->cheque_number}}</td>
                        <td>{{$value->cheque_date}}</td>
                        <td>{{number_format($value->debit,2)}}</td>
                        <td>{{number_format($value->credit,2)}}</td>
                        <td>{{number_format($value->balance,2)}}</td>
                         <td onclick="editNarration('{{$value->bank_deposit_id}}','{{$value->narration}}')">{{$value->narration}}</td>
                     </tr>
                     @endforeach                 
                   </tbody>
                 </table>
                  <br>
                  <div class="button-group ">
                      <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>
                          Export to Excel Sheet
                      </button>
                      <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                          Print Pdf
                      </button>
                  </div>
              </div>
            </div>
</section>
<!-- modals -->
<div class="modal fade modal-flex" id="narration-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit Narration</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editledgerid" name="editledgerid" value="">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Edit Narration</label>
                            <textarea class="form-control" id="editcomments" name="editcomments"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect waves-light" onClick="updatenarration()">Update Narration</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
$(".select2").select2();
 $('#tbldetails').DataTable({
        displayLength: 10,
        info: true,
        language: {
          search:'', 
          searchPlaceholder: 'Search',
          lengthMenu: '<span></span> _MENU_'
   
        },
    });

  $('#Chequedate').bootstrapMaterialDatePicker({
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

     function deposit(){
      var debitCheck = $('#debit').val();
      var creditCheck = $('#credit').val();

      if(debitCheck == 0 && creditCheck == 0)
      {
         swal({
                title: "Error",
                text: "Please Select Debit or Credit",
                type: "error"
           });
      }
      else if($('#mode').val() == ""){
          swal({
              title: "Error",
              text: "Please Select Payment Mode",
              type: "error"
          });
      }
      else
      {
          $.ajax({
            url: "{{url('/depositamount')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            accountid:'{{$accountID}}',
            cheque_number:$('#Chequenumber').val(),
            cheque_date:$('#Chequedate').val(),
            debit:$('#debit').val(),
            credit:$('#credit').val(),
            narration:$('#narration').val(),
            mode:$('#mode').val()
        	},
            success:function(resp){

                if(resp == 1){
                     swal({
                            title: "Operation Performed",
                            text: "Amount Deposit Successfully!",
                            type: "success"
                       });
                     window.location = "{{url('create-deposit',$accountID)}}";
                  }
                else if(resp == 0){
                    swal({
                        title: "Already Exsist",
                        text: "Cheque/Deposit Number Already Exsist!",
                        type: "warning"
                    });
                }
                else if(resp == 2){
                    swal({
                        title: "Error Message",
                        text: "Operation Failed, Incorrect Balance!",
                        type: "error"
                    });
                }
             }

          }); 
      }  

     }

function divhide() {
    $('#details').toggle();
}

function editNarration(id,narration) {
    $('#editcomments').val(narration);
    $('#editledgerid').val(id);
    $('#narration-modal').modal('show');
}

function updatenarration() {
    $.ajax({
        url: "{{url('/editbankrnarration')}}",
        type: 'POST',
        data:{_token:"{{ csrf_token() }}",
            narration:$('#narration').val(),
            id:$('#editledgerid').val(),
            narration:$('#editcomments').val(),
        },
        success:function(resp){
            if(resp == 1){
                swal({
                    title: "Operation Performed",
                    text: "Narration Updated Successfully!",
                    type: "success"
                },function(isConfirm){
                    if(isConfirm){
                        window.location = "{{url('create-deposit')}}/{{$accountID}}}";
                        // id:$('#editledgerid').val('');
                        // narration:$('#editcomments').val('');
                        // $('#narration-modal').modal('hide');
                    }
                });
            }
        }

    });
}

function generate_pdf()
{
    window.location = "{{url('bankledgerPDF',$accountID)}}";

}

</script>


@endsection





