@extends('layouts.master-layout')

@section('title','Bank Account')

@section('breadcrumtitle','Bank Account')

@section('navaccountsoperation','active')
@section('navbankaccount','active')

@section('content')
<section class="panels-wells p-t-3">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Bank Account List</h5>
         <a href="{{ url('bankaccounts-details') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Account
              </a>

         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>

                <th>Image</th>
               <th>Account Title</th>
               <th>Account Number</th>
               <th>Account Type</th>
               <th>Bank</th>
               <th>Branch</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
         	 @foreach($getaccounts as $value)
         	 <tr>
                 <td class="text-center">
                     <img width="50" height="50" src="{{ asset('assets/images/bank-account/'.(!empty($value->image) ? $value->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}">
                 </td>
         	 	<td>{{$value->account_title}}</td>
         	 	<td>{{$value->account_no}}</td>
         	 	<td>{{$value->account_type}}</td>
         	 	<td>{{$value->bank_name}}</td>
         	 	<td>{{$value->branch_name}}</td>

         	 	     <td class="action-icon">
                     <a href="{{url('/getaccountdetails')}}/{{ Crypt::encrypt($value->bank_account_id) }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>
                     <a href="{{url('/create-deposit')}}/{{ Crypt::encrypt($value->bank_account_id) }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Credit/Debit"><i class="icofont icofont-ui-add text-primary f-10" ></i> </a>
                    @if($website != null)
                       <input type="hidden" id="bankAccountId{{ $value->bank_account_id }}" value="{{ Crypt::encrypt($value->bank_account_id) }}"/>
                       {{-- <input type="hidden" id="websiteId{{ $value->bank_account_id }}" value="{{ Crypt::encrypt($website->id) }}"/> --}}
                       <input type="hidden" id="websiteBankUniqueId{{ $value->bank_account_id }}" value="{{ Crypt::encrypt($value->website_bank_id) }}"/>
                      <a href="javascript:voide(0)" class="m-r-10" data-toggle="tooltip"
                         data-placement="top" title="" data-original-title="{{ isset($value->website_id) ?  'Unlink to website' : 'Link to website' }}"
                         onclick="website_setting({{ isset($value->website_id) ? $value->website_id : 0 }},'{{$value->bank_name}}',{{ $value->bank_account_id }})">
                           <i class="icofont {{ isset($value->website_id) ?  'icofont-link text-success' : 'icofont-broken text-muted' }} f-20" ></i>
                         </a>
                    @endif

                 </td>
         	 </tr>

         	 @endforeach

         </tbody>


     </table>
  </div>
</div>

<div class="modal fade modal-flex" id="website-detail-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Link to Website</h4>
			</div>
			<div class="modal-body">
                <input type="hidden" id="bank_md"/>
				<div class="form-group">
				   <select id="website_md" class="form-control select2">
					   <option>Select</option>
					   @foreach($website as $val)
						 <option value="{{ Crypt::encrypt($val->id) }}">{{ $val->name }}</option>
					   @endforeach
				   </select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="btnSubmit" class="btn btn-success waves-effect waves-light f-right">
					Save
				</button>
			</div>

		</div>
	</div>
</div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">

   $(".select2").select2();

   $('.table').DataTable({
        displayLength: 10,
        info: false,
        language: {
          search:'',
          searchPlaceholder: 'Search Category',
          lengthMenu: '<span></span> _MENU_'

        },


    });

    function website_setting(value,bank,code){
        if(value == 0){
            $("#bank_md").val($("#bankAccountId"+code).val());
           $("#website-detail-modal").modal('show');
        }else{
        swal({
                title: "UnLink to Website",
                text: "Do you want to UnLink from website for this "+bank+" bank account?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                        $.ajax({
                            url: '{{ route("bankUnLinkToWebsite") }}',
                            type:'POST',
                            data:{ _token:'{{ csrf_token() }}',
                                  uniqueId:$("#websiteBankUniqueId"+code).val()},
                            dataType:'json',
                            async:true,
                            success:function(resp,textStatus, jqXHR){
                                if(jqXHR.status == 200){
                                    swal({
                                            title: "Success!",
                                            text: "",
                                            type: "success"
                                        }, function(isConfirm) {
                                            if (isConfirm) {
                                                window.location = "{{ url('/view-accounts') }}";
                                            }
                                        });
                                }

                                if(jqXHR.status == 500){
                                    swal('Error!',resp,'error');
                                }
                            }
                        })
                }else{
                    swal("Cancel!","","error");
                }

            });
        }
    }

    $("#btnSubmit").on('click',function(){
        if($('#website_md').val() == ''){
            swal("Error!","Select website","error");
        }else{
            $.ajax({
                     url: '{{ route("bankLinkToWebsite") }}',
                     type:'POST',
                     data:{ _token:'{{ csrf_token() }}',
                            website:$("#website_md").val(),
                            bank:$("#bank_md").val()},
                     dataType:'json',
                     async:true,
                     success:function(resp,textStatus, jqXHR){
                        if(jqXHR.status == 200){
                                $("#website-detail-modal").modal('hide');
                                    swal({
                                            title: "Success!",
                                            text: "",
                                            type: "success"
                                        }, function(isConfirm) {
                                            if (isConfirm) {
                                                window.location = "{{ url('/view-accounts') }}";
                                            }
                                        });
                            }

                            if(jqXHR.status == 500){
                                swal('Error!',resp,'error');
                            }
                        }
                    });
        }

    });

</script>
@endsection

