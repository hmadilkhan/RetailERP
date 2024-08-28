@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','View Employee')

@section('navemployees','active')

@section('navhire','active')

@section('navemployee','active')

@section('content')


 <section class="panels-wells">
	@if(session("message") != "")
		<div class="alert alert-danger" role="alert">
			{{session("message")}}
		</div>
	@endif
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Employee List</h5>
         <a href="{{ url('/show-employee') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Hire Employee
              </a>
         </div>     

       <div class="card-block">
           <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active Employees</div>
                  </div>
                  <br/>
                      <br/>

     <table id="tblemp" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
              <th>Image</th>
               <th>Emp Code</th>
               <th>Name</th>
               <th>Contact</th>
               <th>Designation</th>
               <th>Department</th>
               <th>Branch</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
                 @foreach($getemp as $value)
                 <tr>
                     <td class="text-center">
                    <img width="42" height="42" src="{{ asset('assets/images/employees/images/'.(!empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg' }}"/>
                             </td>
                   <td >{{$value->emp_acc}}</td>
                   <td >{{$value->emp_name}}</td>
                   <td >{{$value->emp_contact}}</td>
                   <td >{{$value->designation_name}}</td>
                   <td >{{$value->department_name}}</td>
                   <td >{{$value->branch_name}}</td>
                    <td >{{$value->status_name}}</td>
            
                 <td class="action-icon">

                   <a href="{{ url('/details-employee') }}/{{ $value->empid }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><i class="icofont icofont-eye-alt text-primary f-18" ></i> </a>
                    
                     <a href="{{ url('/edit-employee-show') }}/{{ $value->empid }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>

                <!--    <i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{ $value->empid }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i> -->

                 </td>          
                       
                 </tr>
                  @endforeach
     
                
           
         </tbody>
        
      
     </table>
  </div>
</div>
@endsection

@section('scriptcode_three')

<script type="text/javascript">

      $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Employee',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

  //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this imaginary file!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "delete it!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/remove-employee')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        empid:id,
                        statusid:2,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove Employee.",
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
              swal("Cancelled", "Your Employee is safe :)", "error");
           }
        });
  });

$('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/view-inaciveemployee')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tblemp tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblemp tbody").append(
                          "<tr>" +
                             "<td class='text-center'><img width='42' height='42' src='assets/images/employees/images/"+((result[count].emp_picture != "") ? result[count].emp_picture : 'placeholder.jpg')+"' alt='"+result[count].emp_picture+"'/></td>" +
                            "<td>"+result[count].emp_acc+"</td>" +  
                            "<td>"+result[count].emp_name+"</td>" +  
                            "<td>"+result[count].emp_contact+"</td>" +  
                            "<td>"+result[count].designation_name+"</td>" +  
                            "<td>"+result[count].department_name+"</td>" +  
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><a class='m-r-10' onclick='details("+result[count].empid+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-eye-alt text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/view-employee') }}";

 // href='/Jahanzaib_Haleem_ERP/details-employee/""'
  }
});

function details(id){
window.location="{{ url('/details-employee')}}/"+id;
}

 </script>

@endsection
