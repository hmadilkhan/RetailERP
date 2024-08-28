@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','Employee Details')

@section('navemployees','active')

@section('navhire','active')

@section('navemployee','active')

@section('content')
<section class="panels-wells"> 
		@if($details)
    <div class="card">

     <div class="card-header">
         <h5 class="card-header-text">Employee Details</h5>
         <span class="card-header-text f-right">{{$details[0]->status_name}}</span>

      <h6 class=""><a href="{{ url('/view-employee') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         
         </div> 
            	<div class="row">
       	 <div class="col-lg-2 col-md-2">
 			  <a href="#">
 	<img  src="{{ asset('assets/images/employees/images/'.(!empty($details[0]->emp_picture) ? $details[0]->emp_picture : 'placeholder.jpg').'') }}" class="d-inline-block img-circle m-l-15" alt="{{ !empty($details[0]->emp_picture) ? $details[0]->emp_picture : 'placeholder.jpg' }}" style="width: 128px;height: 128px;">
    </a>
</div>
</div>    

       <div class="card-block">
       	<div class="row">
       	 <div class="col-lg-6 col-md-6">
       	 <div class="form-group">

 <h5>Personal Details</h5>

     <table id="tblemp" class="table table-responsive nowrap" width="100%">
                <tr>
               <th>Employee Code:</th>
               <td>{{$details[0]->emp_acc}}</td> 	
                </tr>
                 <tr>
               <th>Employee Name:</th>
               <td>{{$details[0]->emp_name}}</td> 	
                </tr>
                  <tr>
               <th>Father Name:</th>
              <td>{{$details[0]->emp_fname}}</td> 	
                </tr>
                     <tr>
               <th>C.N.I.C Number:</th>
              <td>{{$details[0]->emp_cnic}}</td> 	
                </tr>
                 <tr>
              <th>Contact Number:</th>
              <td>{{$details[0]->emp_contact}}</td> 	
                </tr>
                  <tr>
              <th>Address:</th>
               <td>{{$details[0]->emp_address}}</td> 	
                </tr>
                  <tr>
              <th>Office Shift:</th>
               <td>{{$details[0]->shiftname}}</td>  
                </tr>
                    <tr>
              <th>Salary Category:</th>
               <td>{{$details[0]->category}}</td>  
                </tr>
      
     </table>
       	 </div>
       		</div>
       		 <div class="col-lg-6 col-md-6">
       	 <div class="form-group">
 <h5>Office Details</h5>
     <table id="tblemp" class="table table-responsive nowrap" width="100%">
                  <tr>
                 <th>Branch Name:</th>
              <td>{{$details[0]->branch_name}}</td> 	
                </tr>
                 <tr>
               <th>Designation:</th>
           <td>{{$details[0]->designation_name}}</td> 	
                </tr>
                 <tr>
               <th>Department:</th>
             <td>{{$details[0]->department_name}}</td> 	
                </tr>
                   <tr>
               <th>Date of Joining:</th>
               <td>{{$details[0]->date_of_joining}}</td> 	
                </tr>
                     <tr>
               <th>Basic Salary:</th>
               <td>{{$details[0]->basic_pay}}</td> 	
                </tr>
                    <tr>
                  <th>Over Time Amount:</th>
                   <td>{{$details[0]->amount}}</td>   
                </tr>
                     <tr>
                  <th>Over Time Duration:</th>
                   <td>{{$details[0]->duration}}</td>   
                </tr>
        
     </table>
       	 </div>
       		</div>
       	</div>

<div class="row">
       	 <div class="col-lg-4 col-md-4">
       	 <div class="form-group">
 <h5>Documnet Image 1</h5>
             <a href="{{ asset('assets/images/employees/documents/'.(!empty($details[0]->document1) ? $details[0]->document1 : 'placeholder.jpg').'') }}" data-toggle="lightbox">
                 <img  src="{{ asset('assets/images/employees/documents/'.(!empty($details[0]->document1) ? $details[0]->document1 : 'placeholder.jpg').'') }}" class="thumb-img img-fluid" alt="{{ !empty($details[0]->document1) ? $details[0]->document1 : 'placeholder.jpg' }}" style="width: 150px;height: 150px;">
             </a>

</div>
</div>
    <div class="col-lg-4 col-md-4">
        <div class="form-group">
            <h5>Documnet Image 2</h5>
            <a href="{{ asset('assets/images/employees/documents/'.(!empty($details[0]->document2) ? $details[0]->document2 : 'placeholder.jpg').'') }}" data-toggle="lightbox">
                <img  src="{{ asset('assets/images/employees/documents/'.(!empty($details[0]->document2) ? $details[0]->document2 : 'placeholder.jpg').'') }}" class="thumb-img img-fluid" alt="{{ !empty($details[0]->document2) ? $details[0]->document2 : 'placeholder.jpg' }}" style="width: 150px;height: 150px;">
            </a>
        </div>
    </div>
</div>



@if($details[0]->status_name == "In-Active")
<div class="button-group ">
        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="hire()"><i class="icofont icofont-plus"> </i>
          Hire Employee Again
      </button>
       <button type="button" id="btndraft" class="btn btn-md btn-default waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-save"> </i>
          Show Previous Recrod
      </button>
         </div>   
@endif
 
 

 
  </div>
</div>
@endif
</section>

<!-- modals -->
 <div class="modal fade modal-flex" id="emp-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Employee Job History</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
            <table id="tblemp" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
              <th>Date of Joining</th>
               <th>Date of Firing</th>
               <th>Reason of Firing</th>
            </tr>
         </thead>
         <tbody>
                 @foreach($getdata as $value)
                 <tr>
                   <td >{{$value->date_of_joining}}</td>
                   <td >{{$value->date_of_firing}}</td>
                   <td >{{$value->reason}}</td>
                 </tr>
                  @endforeach
         </tbody>
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

  $("#btndraft").on('click',function(){
  $("#emp-modal").modal("show");
});
  
function hire(){
  swal({
          title: "Are you sure?",
          text: "Do you wan to Hire Employee Again!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Hire Employee!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/hire-employee')}}",
                        type: 'POST',
                        data:{_token:"{{ csrf_token() }}",
                        empid:'{{$details[0]->empid}}',
                        statusid:1,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                               title: "success",
                               text: "Employee Hire Successfully!!",
                               type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-employee') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled:)", "error");
           }
        });
}
  //light box
  $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox();

  });
     
       </script>
@endsection