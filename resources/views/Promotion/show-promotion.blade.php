@extends('layouts.master-layout')

@section('title','Employee Promotion')

@section('breadcrumtitle','Promotion Details')

@section('navpromotion','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Promotion Details</h5>
         <a href="{{ url('/createpromotion') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Promote Employee
              </a>
         </div>     

       <div class="card-block">

     <table id="tblleaves" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
              <th>Employee Code | Name</th>
               <th>Promotion Date</th>
               <th>Action</th>
            </tr>
         </thead>
        <tbody>
        @foreach($details as $value)
                 <tr>
                  <td >{{$value->emp_acc}} | {{$value->emp_name}}</td>
                   <td >{{$value->date}}</td>
              <td><a class="m-r-10" data-toggle="tooltip" data-placement="top" title="Show Details" data-original-title="View" onclick="modalshow('{{$value->promotion_id}}')"><i class="icofont icofont-eye-alt text-success f-18" ></i> </a>
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
  $(".select2").select2();

      $('#tblleaves').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

 

 </script>

@endsection
