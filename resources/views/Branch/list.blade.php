@extends('layouts.master-layout')

@section('title','Branches')

@section('breadcrumtitle','Branches')

@section('navbranchoperation','active')
@section('navbranch','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Branches</h5>
         <a href="{{url('/createbranch')}}" class="btn btn-success waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Branch
              </a>

         </div>      
       <div class="card-block">

     <table  class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
        
         <thead>
            <tr>
              <th>Logo</th>
               <th>Branch Name</th>
               <th>City</th>
               <th>Mobile</th>
               <th>Email</th>
               <th>Address</th>
               <th>Action</th>
            </tr>
            </thead>
              <tbody>
          
              @foreach($details as $value)
                    <tr>
                      <td class="text-center">
                                   <img width="42" height="42" src="{{ asset('storage/images/branch/'.(!empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->branch_logo) ? $value->branch_logo : 'placeholder.jpg' }}">
                             </td>
                          
                          <td>{{$value->branch_name}}</td>
                          <td>{{$value->city_name}}</td>
                          <td>{{$value->branch_mobile}}</td>
                          <td>{{$value->branch_email}}</td>
                          <td>{{$value->branch_address}}</td>
                          <td class="action-icon">
							
                                <a href="{{ url('/branch-emails') }}/{{ Crypt::encrypt($value->branch_id) }}" class="p-r-10 f-18 text-info" data-toggle="tooltip" data-placement="top" title="" data-original-title="Emails"><i class="icofont icofont-email"></i></a>
							   
                                <a href="{{ url('/branch-edit') }}/{{ Crypt::encrypt($value->branch_id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <i class="icofont icofont-ui-delete text-danger f-18" onclick="deleteBranch('{{ $value->branch_id }}')" data-id="{{ $value->branch_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>&nbsp;

                                <a href="{{ url('/terminals') }}/{{ Crypt::encrypt($value->branch_id) }}" class="icofont icofont icofont-plus text-info f-18" data-id="{{ $value->branch_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Terminal"></a>

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
          searchPlaceholder: 'Search Branch',
          lengthMenu: '<span></span> _MENU_'
   
        }
    });

  //Alert confirm
 function deleteBranch(id){
	// alert()
    // var id= $(this).data("id");
	
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
                        url: "{{url('/removebranch')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove branch.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/branches') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your branch is safe :)", "error");
           }
        });
  };

</script>
@endsection

