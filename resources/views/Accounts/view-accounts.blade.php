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
                      <a href="javascript:voide(0)" class="m-r-10" data-toggle="tooltip"
                         data-placement="top" title="" data-original-title="{{ isset($value->website_id) ?  'Unlink to website' : 'Link to website' }}">
                           <i class="icofont {{ isset($value->website_id) ?  'icofont-link text-success' : 'icofont-broken text-muted' }} f-10" ></i>
                         </a>
                    @endif

                 </td>
         	 </tr>

         	 @endforeach

         </tbody>


     </table>
  </div>
</div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
   $('.table').DataTable({
        displayLength: 10,
        info: false,
        language: {
          search:'',
          searchPlaceholder: 'Search Category',
          lengthMenu: '<span></span> _MENU_'

        },


    });
</script>
@endsection

