@extends('layouts.master-layout')

@section('title','Vendor Ledgers')

@section('breadcrumtitle','Ledger Details')
@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
    <section class="panels-wells">

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Add Manual Adjustments </h5>
                <h5 class=""><a href="{{ url('vendors') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>

            </div>
            <div class="card-block">
                <form method="GET" action="{{url('adjustment')}}">
                    <input type="hidden" name="vendor_id" value="{{$vendorID}}"/>
                    <div class="row">
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Date</label>
                                <input class="form-control" type="text"
                                       name="date" id="date" placeholder="DD-MM-YYYY" value="{{ old('date') }}"/>
                                @error('date')
                                    <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group {{ $errors->has('debit') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Debit</label>
                                <input class="form-control" type="number"
                                       name="debit" id="debit" required value="0"  value="{{ old('debit') }}"/>
                                @error('debit')
                                  <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Credit</label>
                                <input class="form-control" type="number"
                                       name="credit" id="credit" required value="0" value="{{ old('credit') }}" />
                                @error('credit')
                                    <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('credit') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Narration</label>
                                <input class="form-control" type="text"
                                       name="narration" id="narration"   value="{{ old('narration') }}" />
                                @error('narration')
                                <div class="form-control-feedback">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <div class="form-group">
                                <label class="form-control-label"></label>
                                <button type="submit"  class="btn btn-md btn-primary waves-effect waves-light m-t-20" >
                                    Deposit Amount
                                </button>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
	
	<section class="panels-wells">
    
		<div class="card">
			<div class="card-header">
				<h5 class="card-header-text">Filter Ledger Details</h5>
			</div>
			<div class="card-block">
				<div class="row">
					<div class="col-lg-4 col-md-4">
						<div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
							<label class="form-control-label">From</label>
							<input class="form-control" type="text"
								   name="from" id="fromdate" placeholder="YYYY-MM-DD"/>
							@error('from')
								<div class="form-control-feedback">{{$message}}</div>
							@enderror
						</div>
					</div>
					<div class="col-lg-4 col-md-4">
						<div class="form-group {{ $errors->has('date') ? 'has-danger' : '' }}">
							<label class="form-control-label">To</label>
							<input class="form-control" type="text"
								   name="to" id="todate" placeholder="YYYY-MM-DD"/>
							@error('to')
								<div class="form-control-feedback">{{$message}}</div>
							@enderror
						</div>
					</div>
					<div class="col-lg-4 col-md-4">
						<label class="form-control-label"></label>
						<div class="button-group ">
							<button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-left "> <i class="icofont icofont-file-pdf"> </i>
							   Print Pdf
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

  <section class="panels-wells">
    
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Vendor Ledger Details - ( {{$vendor}} )</h5>
		 <a href="{{ url('create-payment',$slug) }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Make a Payment </a>
{{--         <h5 class=""><a href="{{ url('vendor-ledger-report',$vendorID) }}"><i class="btn btn-danger text-center icofont icofont-file-pdf p-r-20 f-18 f-right m-r-3" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back"> Print PDF</i></a></h5>--}}

{{--         <a href="{{ url('create-payment',$vendorID) }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Make Payment </a>--}}
{{--         <h5 class=""><a href="{{ url('vendors') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to List</i></a></h5>--}}
         
         </div>      
       <div class="card-block">
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>

                <th>ID#</th>
                <th>Date</th>
               <th>PO</th>
               <th>Total Amount</th>
               <th>Debit</th>
               <th>Credit</th>
               <th>Balance</th>
               <th>Narration</th>
               <th>Action</th>

               
            </tr>
         </thead>
         <tbody>
      
         	 @if($details)
                        @foreach ($details as $value)
			              <tr>
                              <td>{{$value->vendor_account_id}}</td>
                              <td>{{date("d F Y",strtotime($value->created_at))}}</td>
                              <td> <a  href="{{url('view',$value->purchase_id)}}">{{$value->po_no}}</a></td>
			                  <td>{{$value->total_amount}}</td>
			                  <td>{{number_format($value->debit,2)}}</td>
                              <td>{{number_format($value->credit,2)}}</td>
                              <td class="{{($value->balance >= 0) ? 'text-success' : 'text-danger'}}">{{number_format($value->balance,2)}}</td>
                              <td onclick="editNarration('{{$value->vendor_account_id}}','{{$value->narration}}')">{{$value->narration}}</td>
                               <td>
                                @if($value->debit != "")
                                    <a  href="{{url('voucher',$value->vendor_account_id)}}" class="text-success p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Voucher"><i class="icofont icofont icofont-print"></i></a>
{{--                                       @if($value->purchase_id != "")--}}
{{--                                         <a  href="{{url('view',$value->purchase_id)}}" class="text-info p-r-10 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Voucher"><i class="icofont icofont-eye-alt"></i></a>--}}
{{--                                       @endif--}}
                                @else

                                @endif
                              </td>
			             </tr>
                     	@endforeach
           @endif
     
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

   $('.table').DataTable({
       // "order": [[ 1, "asc" ]],
        bLengthChange: true,
        displayLength: 10,
        info: true,
        language: {
          search:'', 
          searchPlaceholder: 'Search..',
          lengthMenu: '<span></span> _MENU_'
   
        }
    });

 $('#date,#fromdate,#todate').bootstrapMaterialDatePicker({
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

   function loadAmount()
   {
        $.ajax({
            url : "{{url('/polist')}}",
            type : "POST",
            data : {_token : "{{csrf_token()}}", id:$("#po").children("option:selected").val()},
            success : function(result){

              $('#amount').val(result[0].net_amount);
            }
        });
   }

 function editNarration(id,narration) {
     $('#editcomments').val(narration);
     $('#editledgerid').val(id);
     $('#narration-modal').modal('show');
 }
 function updatenarration() {
     $.ajax({
         url: "{{url('/editvendornarration')}}",
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
                         window.location = "{{url('ledgerlist')}}/{{$slug}}";
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
		if($("#fromdate").val() == ""){
			$("#fromdate").focus();
		}else if($("#todate").val() == ""){
			$("#todate").focus();
		}else{
			window.location = "{{url('vendor-ledger-report',$slug)}}" + "/"+$("#fromdate").val() + "/"+$("#todate").val();
		}
	}


  
  </script>

@endsection