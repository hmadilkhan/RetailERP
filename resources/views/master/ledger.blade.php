@extends('layouts.master-layout')

@section('title','Master')

@section('breadcrumtitle','Add Expense')

@section('navmaster','active')

@section('content')


  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Master Ledger Details</h5>
         <a href="{{ url('ledger-payment',$masterID) }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Make Payment </a>
         <h5 class=""><a href="{{ url('get-masters') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
         
         
         </div>      
       <div class="card-block">
           <div class="project-table">
                 <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>
               
               <th>Name</th>
               <th>Receipt</th>
               <th>Total Amount</th>
               <th>Debit</th>
               <th>Credit</th>
               <th>Balance</th>
               <th>Total Balance</th>
               <th>Date</th>
               
            </tr>
         </thead>
         <tbody>
      
         	 @if($details)
                        @foreach ($details as $value)
			              <tr>
			                 <td>{{$value->name}}</td>
			                 <td>{{$value->receipt_no}}</td>
			                 <td>{{$value->total_amount}}</td>
			                 <td>{{$value->debit}}</td>
                       <td>{{$value->credit}}</td>
                       <td >{{$value->balance}}</td>
                       <td class="{{($value->TotalBalance < 0) ? 'text-danger' : 'text-success'}}">{{$value->TotalBalance}}</td>
                       <td>{{date("d F Y",strtotime($value->created_at))}}</td>
			             </tr>
                     	@endforeach
           @endif
     
         </tbody>
     </table>
        </div>
    </div>
   </div>
</section>
@endsection


@section('scriptcode_three')

  <script type="text/javascript">

    $("#btn_clear").on('click',function(){
      $("#debit").val('');

    });


   $('.table').DataTable({

        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Customer',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });
  
  </script>

@endsection