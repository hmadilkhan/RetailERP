 @extends('layouts.master-layout')

@section('title','Deliviery Challan List')

@section('breadcrumtitle','Deliviery Challan List')

 @section('navbranchoperation','active')
 @section('navtransfer','active')

 @section('navchallanview','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Delivery Challan List</h5>
        
         </div>      
       <div class="card-block">


     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
               <th>Challan No.</th>
               <th>Delivered by</th>
               <th>Destination</th>
               <th>Delivered Date</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
             @foreach($challans as $value)
                 <tr>
                  
                   <td>DC-{{$value->DC_No}}</td>
                   <td >{{$value->deliverd_by}}</td>
                   <td>{{$value->destination}}</td>
                   <td >{{$value->date}}</td>
{{--                     <td>{{$value->branch_to}}</td>--}}
{{--                     <td>{{session('branch')}}</td>--}}
{{--                 --}}
                 <td class="action-icon">
                    
                      <i class="icofont icofont-eye-alt text-primary p-r-10 f-18"  data-toggle="tooltip" data-placement="top" title="" data-original-title="View" onclick="open_challan('{{$value->DC_id}}')" ></i>

                      @if($value->branch_to == session('branch'))
                            <a class="{{ $value->counter != 0 ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $value->counter != 0 ? 'Disabled' : 'Recived Challan' }}"><i class="icofont icofont-plus text-{{ $value->counter != 0 ? 'muted' :'success'}} f-18" <?php echo ($value->counter != 0 ?  '' : ' onclick="GRN('.$value->DC_id.') "'); ?>></i></a>

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
          searchPlaceholder: 'Search Challan',
          lengthMenu: '<span></span> _MENU_'
   
        },
        
 
    });
  function open_challan(id){

    window.location="{{url('/challandetails')}}"+"/"+id; 
  }

  function GRN(id){
    $.ajax({
              url: "{{url('/removetransferorder')}}",
              type: "PUT",
              data: {_token:"{{csrf_token()}}",
              id:id,
              statusid:9,
          },
              success:function(result){
                window.location="{{url('/createGRN')}}"+"/"+id; 
              }
            });
  }
</script>

@endsection