@extends('layouts.master-layout')

@section('title','Loan')

@section('breadcrumtitle','Loan Details')

@section('navloan','active')

@section('navloandetails','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Loan Details</h5>
         <a href="{{ url('/show-issueloan') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Issue Loan
              </a>
         </div>     

       <div class="card-block">
        <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active Loan Details</div>
                  </div>
                  <br/>
                      <br/>


 <table id="tblloan" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
	<thead>
         <th>Employee Code | Name</th>
         <th>Branch</th>
         <th>Loan</th>
         <th>Date</th>
         <th>Balance</th>
         <th>Status</th>
         <th>Action</th>
	</thead>
	<tbody>
		 @foreach($details as $value)
                 <tr>
                   <td >{{$value->emp_acc}} | {{$value->emp_name}}</td>
                   <td >{{$value->branch_name}}</td>
                   <td >{{$value->loan_amount}}</td>
                   <td >{{$value->date}}</td>
                   <td >{{$value->balance}}</td>
                    <td >{{$value->status_name}}</td>
            
                 <td class="action-icon">
                 	  <a class="p-r-10 text-primary f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Installment Details" onclick="getinstallmetns('{{$value->loan_id}}')"><i class="icofont icofont-list"></i></a> 
					  <a class="text-danger p-r-10 f-18" onclick="showvoucher('{{$value->empid}}','{{$value->loan_id}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Voucher"><i class="icofont icofont-file-pdf"></i></a>
                  <!--   
                     <a href="{{ url('/edit-employee-show') }}/{{ $value->loan_id }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                   <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->loan_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
 -->
                 </td>          
                       
                 </tr>
                  @endforeach
		
		
	</tbody>
</table>
    


  </div>
</div>
</section>
<!-- modals -->
 <div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Installment Details</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">

            <table id="tblsheet" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Employee Name</th>
               <th>Installment Amount</th>
               <th>Installment Date</th>
               <th>Status</th>
            </tr>
         </thead>
         <tbody>
     </table>
                      </div>
                  </div>   
             </div>
          </div>
           </div>
        </div> 
@endsection

@section('scriptcode_three')

<script type="text/javascript">

 $('#tblloan').DataTable({

        bLengthChange: true,
        displayLength: 25,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search details',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

 //Alert confirm
 // $('.alert-confirm').on('click',function(){
 //    var id= $(this).data("id");

 //    alert(id);

 //      swal({
 //          title: "Are you sure?",
 //          text: "Your will not be able to recover this!",
 //          type: "warning",
 //          showCancelButton: true,
 //          confirmButtonClass: "btn-danger",
 //          confirmButtonText: "delete it!",
 //          cancelButtonText: "cancel plx!",
 //          closeOnConfirm: false,
 //          closeOnCancel: false
 //        },
 //        function(isConfirm){
 //          if(isConfirm){
 //                     $.ajax({
 //                        url: "{{url('/remove-loan')}}",
 //                        type: 'PUT',
 //                        data:{_token:"{{ csrf_token() }}",
 //                        loanid:id,
 //                        statusid:2,
 //                        },
 //                        success:function(resp){
 //                            if(resp == 1){
 //                                 swal({
 //                                        title: "Deleted",
 //                                        text: "Loan Details Deleted Successfully!",
 //                                        type: "success"
 //                                   },function(isConfirm){
 //                                       if(isConfirm){
 //                                        window.location="{{ url('/loandetails') }}";
 //                                       }
 //                                   });
 //                             }
 //                        }

 //                    });
              
 //           }else {
 //              swal("Cancelled", "Operation Cancelled:)", "error");
 //           }
 //        });
 //  });


 function getinstallmetns(id){
   $.ajax({
        url: "{{url('/getinstallments')}}",
        type: 'GET',
        data:{_token:"{{ csrf_token() }}",
        loanid:id,
        },
        success:function(result){
            $('#details-modal').modal('show');
            $("#tblsheet tbody").empty();
              for(var count =0;count < result.length; count++){
                $("#tblsheet tbody").append(
                  "<tr>" +
                    "<td class='pro-name' >"+result[count].emp_name+"</td>" +
                    "<td>"+result[count].installment_amount+"</td>" +  
                    "<td>"+result[count].date+"</td>" +  
                    "<td>"+result[count].status_name+"</td>" +  
                  "</tr>" 
                 );
                    }
        }

    });
 }


 $('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/getdetails_loan')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tblloan tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblloan tbody").append(
                          "<tr>" +
                            "<td>"+result[count].emp_acc+" | "+result[count].emp_name+"</td>" +  
                            "<td>"+result[count].branch_name+"</td>" +
                            "<td>"+result[count].loan_amount+"</td>" +
                            "<td>"+result[count].date+"</td>" +  
                            "<td>"+result[count].balance+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +
                            "<td class='action-icon'><a class='m-r-10' onclick='getinstallmetns("+result[count].loan_id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-list text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/loandetails') }}";
  }
});

 function showvoucher(id,loanid)
 {
	 if(id != ""){
		 window.open("{{url('loan-voucher')}}"+"?empid="+id+"&loan_id="+loanid)
	 }
 }
 
</script>
@endsection