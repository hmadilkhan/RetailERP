@extends('layouts.master-layout')

@section('title','Testimonials')

@section('breadcrumtitle','Testimonials')

@section('navwebsite','active')

@section('content')
<section class="panels-wells">
   
    @if(Session::has('error'))
         <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
         <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    

</section>
@endsection

@section('scriptcode_three')
<script type="text/javascript">
	$('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Website',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });
    
    function remove(webId,webName){
            swal({
                title: 'Remove Website',
                text:  'Are you sure remove slider from '+webName+' website?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: 'btn btn-danger',
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                     $("#removeForm"+webId).submit();
                }else{
                    swal.close();
                }
            });        
    }    
</script>
@endsection