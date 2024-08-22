@extends('layouts.master-layout')

@section('title','Designation')

@section('breadcrumtitle','Create Designation')

@section('navmanage','active')

@section('navdesignation','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Create Designation</h5>
          <h6 class=""><a href="{{ url('/view-designation') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}
                
         <div class="row">
                <div class="col-lg-6 col-md-6">
               
                        <div class="form-group">
                                <label class="form-control-label">Select Department</label>
                                <select name="department" id="department" data-placeholder="Select Department" class="form-control select2" >
                                    <option value="">Select Department</option>
                                    @if($depart)
                                      @foreach($depart as $value)
                                        <option value="{{ $value->department_id }}">{{ $value->department_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                </div>
              </div>
       
          <div class="col-lg-6 col-md-6">
               <div class="form-group">
                  <label class="form-control-label">Designation Name</label>
                  <input class="form-control" type="text" name="desg" id="desg" />
                </div>
              </div>
            
        </div>
       
      <button type="submit" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right" ><i class="icofont icofont-plus f-18 m-r-5"></i> Create Designation </button>
                
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
            url: "{{url('/insert-designation')}}",
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success:function(resp){
            	console.log(resp);
                if(resp == 1){
                     swal({
                      title: "Operation Performed",
                      text: "Designation Created Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                      window.location= "{{ url('/view-designation') }}";
                      }
                       });
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Designation Already exsit!",
                            type: "warning"
                       });
                  }
             }

          });  
      }
});
 </script>

@endsection
