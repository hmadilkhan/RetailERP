@extends('layouts.master-layout')

@section('title','Cash Ledger')

@section('breadcrumtitle','Bank Account')

@section('navaccounts','active')
@section('navcashledger','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">CASH LEDGER</h5>
          <a href="{{ url('/view-accounts') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
         </div>      
       <div class="card-block">
       	 <div class="row">
          
          <div class="col-lg-3 col-md-3">
            <div class="form-group">
                <label class="form-control-label">Date</label>
                <input class="form-control" type="text"
                 name="date" id="date" placeholder="DD-MM-YYYY"/>
                  <div class="form-control-feedback"></div>
            </div>
             </div>
              <div class="col-lg-3 col-md-3">
                <div class="form-group">
                    <label class="form-control-label">Debit</label>
                    <input class="form-control" type="number"
                     name="debit" id="debit" required value="0" />
                     <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-3 col-md-3">
                <div class="form-group">
                    <label class="form-control-label">Credit</label>
                    <input class="form-control" type="number"
                     name="credit" id="credit" required value="0" />
                     <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-3 col-md-3">
                <div class="form-group">
                <label class="form-control-label">Narrations</label>
                <input class="form-control" type="text"
                 name="narration" id="narration" placeholder="Enter Narration" required  />
                <span class="help-block"></span>
              </div>
          </div>
           </div>
          <button type="button" id="btndeposit" class="btn btn-md btn-primary waves-effect waves-light f-right" onclick="deposit()">
              Deposit Amount
          </button>
             </div>
             </div>   
          <div class="card">
             <div class="card-header">
                <h5 class="card-header-text">Cash Ledger Details</h5>
             </div>      
              <div class="card-block">
                <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                   <thead>
                      <tr>
                         <th>S.No</th>
                         <th>Date</th>
                         <th>Debit</th>
                         <th>Credit</th>
                         <th>Balance</th>
                         <th>Comments</th>
                          <th>Action</th>
                      </tr>
                   </thead>
                   <tbody>
                        @foreach($cashLedger as $value)
                        <tr>
                         <td>{{$value->id}}</td>
                         <td>{{$value->date}}</td>
                         <td>{{$value->debit}}</td>
                         <td>{{number_format($value->credit,2)}}</td>
                         <td>{{number_format($value->balance,2)}}</td>
                         <td onclick="editNarration('{{$value->id}}','{{$value->narration}}')">{{$value->narration}}</td>
                            <td class="action-icon">
                                <i onclick="generate_voucher('{{ $value->id }}')" class="text-danger text-center icofont icofont-file-pdf" data-toggle="tooltip" data-placement="top" title="" data-original-title="Voucher"></i>
                            </td>
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
                <h4 class="modal-title">Edit Comments</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editledgerid" name="editledgerid" value="">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Edit Comments</label>
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

 $('#demandtb').DataTable({
        displayLength: 10,
        info: true,
        order:[0,'DESC'],
        language: {
          search:'', 
          searchPlaceholder: 'Search Category',
          lengthMenu: '<span></span> _MENU_'
   
        }
    });

  $('#date').bootstrapMaterialDatePicker({
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

  function editNarration(id,narration) {
      $('#editcomments').val(narration);
      $('#editledgerid').val(id);
    $('#narration-modal').modal('show');
  }

  function deposit(){
      var debitCheck = $('#debit').val();
      var creditCheck = $('#credit').val();

      if($('#date').val() == "")
      {
        swal({
                title: "Error",
                text: "Please Select Date",
                type: "error"
           });
      }
      else if(debitCheck == 0 && creditCheck == 0)
      {
         swal({
                title: "Error",
                text: "Please Select Debit or Credit",
                type: "error"
           });
      }
      else if($('#narration').val() == "")
      {
         swal({
                title: "Error",
                text: "Please Enter Narration",
                type: "error"
           });
      }
      else
      {
          $.ajax({
            url: "{{url('/cashLedgerDeposit')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            narration:$('#narration').val(),
            date:$('#date').val(),
            debit:$('#debit').val(),
            credit:$('#credit').val(),
          },
            success:function(resp){
                console.log(resp);
                if(resp == 1){
                     swal({
                            title: "Operation Performed",
                            text: "Amount Deposit Successfully!",
                            type: "success"
                       });
                     window.location = "{{url('cash-deposit')}}";
                }else if(resp == 2)
                {
                    swal({
                        title: "Operation can not Performed",
                        text: "Cash Ledger does not have sufficient balance for this transaction!",
                        type: "error"
                    });
                }
             }

          }); 
      }  

     }



     
     function updatenarration() {
         $.ajax({
             url: "{{url('/editledgernarration')}}",
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
                         text: "Comments Updated Successfully!",
                         type: "success"
                     },function(isConfirm){
                         if(isConfirm){
                             window.location = "{{url('cash-deposit')}}";
                             id:$('#editledgerid').val('');
                                 narration:$('#editcomments').val('');
                         }
                     });
                 }
   }

         });
     }

function generate_pdf()
{
    window.location = "{{url('cashledgerPDF')}}";
}

function generate_voucher(id) {
    window.location = "{{url('cash_voucher')}}?id="+id;
}
</script>


@endsection



