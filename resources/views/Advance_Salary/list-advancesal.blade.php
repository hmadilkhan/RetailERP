@extends('layouts.master-layout')

@section('title','Advance Salary')

@section('breadcrumtitle','Advance Salary Details')

@section('navpayroll','active')

@section('navadvance','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Advance Salary Details</h5>
         <a href="{{ url('/show-advancesal') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Issue Advance
              </a>
         </div>     

       <div class="card-block">
           <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active Advance Details</div>
                  </div>
                  <br/>
                      <br/>
 <table id="tbladvance" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
	<thead>
         <th>Employee Code | Name</th>
         <th>Branch</th>
         <th>Date</th>
         <th>Advance Amount</th>         
         <th>Reason</th>
         <th>Status</th>
         <th>Action</th>
	</thead>
	<tbody>
		 @foreach($details as $value)
                 <tr>
                   <td>{{$value->emp_acc}} | {{$value->emp_name}}</td>
                   <td>{{$value->branch_name}}</td>
                   <td>{{$value->date}}</td>
                   <td>{{$value->amount}}</td>
                    <td>{{$value->reason}}</td>
                    <td>{{$value->status_name}}</td>
                    <td>
						<a class="text-danger p-r-10 f-18" onclick="showvoucher('{{$value->empid}}','{{$value->advance_id}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Voucher"><i class="icofont icofont-file-pdf"></i></a>
					</td>
            
                <!--  <td class="action-icon">
                    <a href="{{ url('/')}}/{{ $value->advance_id }}" class="p-r-10 text-primary f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Ledger"><i class="icofont icofont-list"></i></a>  -->
                  <!--   
                     <a href="{{ url('/edit-employee-show') }}/{{ $value->advance_id }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                   <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->advance_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
 -->
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

        bLengthChange: true,
        displayLength: 25,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search details',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

 $('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/getinactivedetails')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tbladvance tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tbladvance tbody").append(
                          "<tr>" +
                            "<td>"+result[count].emp_acc+" | "+result[count].emp_name+"</td>" +  
                            "<td>"+result[count].branch_name+"</td>" +
                            "<td>"+result[count].date+"</td>" +
                            "<td>"+result[count].amount+"</td>" +  
                            "<td>"+result[count].reason+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/view-advancelist') }}";
  }
});
 
 
 function showvoucher(id,advanceid)
 {
	 if(id != ""){
		 window.open("{{url('advance-salary')}}"+"?empid="+id+"&advance_id="+advanceid)
	 }
 }
</script>
@endsection