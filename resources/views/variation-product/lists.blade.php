@extends('layouts.master-layout')

@section('title','Product Variation')

@section('breadcrumtitle','View Product Variation')
@section('navinventory','active')
@section('navinvent_variat_product','active')

@section('content')

 <section class="panels-wells">
  <div class="row">

           <div class="col-lg-12 col-md-12">

             @if(Session::has('success'))
                  <div class="alert alert-success">{{ Session::get('success') }}</div>
             @endif

             @if(Session::has('error'))
                  <div class="alert alert-danger">{{ Session::get('error') }}</div>
             @endif

              <div class="form-group">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Lists</h5>

           <a href="{{ route('CreateVariatProd') }}" class="btn btn-primary f-right m-r-10"><i class="icofont icofont-plus f-18 "></i>&nbsp;Create</a>
           <button type="button" id="btn_removeall"  class="btn btn-danger f-right m-r-10 invisible"><i class="icofont icofont-ui-delete f-18 "></i>&nbsp;Remove</button>
           
           

         </div>      
       <div class="card-block">
    
           <div class="project-table">
     <table id="mainTable" class="table table-striped full-width">
         <thead>
            <tr>
               <th class="d-none">Code</th>
               <th>Image</th>
               <th>Product</th>
               <th>Variation</th>
               <th>Price</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
              @if($products)
                @foreach($products as $val)
                       <tr>
                         <td class="d-none">{{ $val->id }}</td>
                         <td><img src="{{ route('imageVariatProduct',(empty($val->image) ? 'no-image.png' : $val->image )) }}" alt="{{ (empty($val->image) ? 'no-image.png' : $val->image ) }}" /></td>
                         <td>{{ $val->product_name.' | '.$val->parent_prod }}</td>
                         <td>{{ $val->variat_name }}</td>
                         <td>{{ $val->price }}</td>
                         <td class="action-icon">

                             <a href="{{ route('editVariation',$val->id) }}"> <i onclick="" class="text-warning text-center icofont icofont-edit" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"></i></a>
                             
                             <a href="javascript:void(0)">
                             <i onclick="remove('{{ addslashes($val->product_name.' | '.$val->parent_prod) }}',{{ $val->id }})" class="text-danger text-center icofont icofont-trash" 
                             data-toggle="tooltip" data-placement="top" title="" data-original-title="Remove"></i> </a>

                             <form id="removeForm{{ $val->id }}" action="{{ route('removeVariation',$val->id) }}" method="post" class="d-none">
                                @csrf
                                @method('DELETE')
                             </form>
                       </td>            
                     </tr>
               @endforeach
             @endif
                     </tbody>
                 </table>
        </div>
         </div>
          

    </div>
      </div>
          </div>
    


    </div>

   
   
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">


   $(".select2").select2();

   $("#variat_values").tagsinput({
     maxTags: 10
    });
      $('#mainTable').DataTable( {

        bLengthChange: true,
        displayLength: 50,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Variations',
          lengthMenu: '<span></span> _MENU_'
   
        }
        
 
    } );
  
        function remove(name,id){

            swal({
                    title: "Are you sure?",
                    text: "Do you want to Delete "+ name +" product variation?",
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
                        $("#removeForm"+id).submit();
                    }else {
                        swal("Cancelled", "Product variations Safe :)", "error");
                    }
                });
        }

</script>

@endsection
