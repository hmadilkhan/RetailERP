@extends('layouts.master-layout')

@section('title','Designation')

@section('breadcrumtitle','Edit Designation')

@section('navmanage','active')

@section('navdesignation','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Edi Designation</h5>
            <h6 class=""><a href="{{ url('/view-designation') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}
                
         <div class="row">
       
                            <div class="col-lg-12 col-md-12">
               <div class="form-group">
                  <label class="form-control-label">Designation Name</label>
                  <input class="form-control" type="text" name="desg" id="desg" value="{{$designation[0]->designation_name}}" />

                  <input class="form-control" type="hidden" name="desgid" id="desgid" value="{{$designation[0]->designation_id}}"  />

                </div>
              </div>
            
        </div>
       
      <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" ><i class="icofont icofont-ui-edit f-18 m-r-5"></i> Edit Designation </button>
                
               </form>  
           </div> 
 </div>
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

   $('#upload_form').on('submit', function(event){
  event.preventDefault();
  let value = $('#desg').val();
  if (value == '') {
     swal({
          title: "Error Message",
          text: "Designation Name Can not be blank!",
          type: "error"
     });
  }
  else{

     $.ajax({
            url: "{{url('/edit-designation')}}",
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success:function(resp){
                     swal({
                      title: "Operation Performed",
                      text: "Designation Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                      window.location= "{{ url('/view-designation') }}";
                      }
                       });
                  
                
             }

          });  
      }
});
 </script>

@endsection
