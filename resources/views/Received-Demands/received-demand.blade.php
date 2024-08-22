@extends('layouts.master-layout')

@section('title','Received Demand')

@section('breadcrumtitle','Received Demand')

@section('navbranchoperation','active')
@section('navrecdemand','active')

@section('content')


 <section class="panels-wells">
    <div class="card">


     <div class="card-header">
         <h5 class="card-header-text">Requested Demand List</h5>
       
         </div>      
       <div class="card-block">

     <table id="demo-foo-filtering" class="table table-striped" data-filtering="true" data-show-toggle="false">

         <thead>
            <tr>
               <th>DO No</th>
               <th >Branch</th>
               <th>Generation Date</th>
               <th >Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
          @if ($demands)
             @foreach($demands as $value)
                 <tr id="{{$value->demand_id}}">
                   <td>DO-{{$value->demand_id}}</td>
                   <td >{{$value->branch_name}}</td>
                   <td >{{$value->date}}</td>
                   <td >
                    @if($value->name == "Draft")
                    <span class="tag tag-default">  {{$value->name }}</span>
                  @elseif($value->name == "Pending")
                    <span class="tag tag-success">  {{$value->name }}</span>
                  @elseif($value->name == "Approved")
                     <span class="tag tag-info">  {{$value->name }}</span>
                  @elseif($value->name == "Cancel")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                       @elseif($value->name == "Delivered")
                    <span class="tag tag-danger">  {{$value->name }}</span>
                         @elseif($value->name == "In-Process")
                    <span class="tag tag-primary">  {{$value->name }}</span>
                    @elseif($value->name == "Completed")
                    <span class="tag tag-info">  {{$value->name }}</span>
                  @endif
                  </td>
                 <td class="action-icon ">
                      <i class="icofont icofont-eye-alt text-primary p-r-10 f-18"  data-toggle="tooltip" data-placement="top" title="" data-original-title="View" onclick="btn_view('{{Crypt::encrypt($value->id)}}','{{$value->name }}')"  ></i>                   
                 </td> 

                 </tr>

                  @endforeach

  
          @endif
          
          
                
           
         </tbody>
        
      
     </table>
  </div>
</div>
@endsection

@section('scriptcode_three')
<script>

    
  $('.table').DataTable({
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Demand',
          lengthMenu: '<span></span> _MENU_'
   
        },
        
 
    });




  function btn_view(id,status){
    // window.location="/erp/demanddetails/"+id;
    window.location="{{url('/demand-details')}}"+"/"+id;

    // if (status == "In-Process") {
     // window.location= "/erp/view-transfer/"+id;
         
    // }

    // else{
    //    window.location= "/erp/received-demandpanel/"+id;
    // }
    

     
  } 

 

</script>


@endsection



