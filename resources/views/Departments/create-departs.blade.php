@extends('layouts.master-layout')

@section('title','Departments')

@section('breadcrumtitle','Create Departments')

@section('navmanage','active')

@section('navdepartments','active')

@section('content')


 <section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Create Department</h5>
          <h6 class=""><a href="{{ url('/view-departments') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>      
       <div class="card-block">
        <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}
                
         <div class="row">
       
               <div class="col-lg-6 col-md-6">
                           <div class="form-group">
                                <label class="form-control-label">Branch</label>
                                <select name="branch" id="branch" data-placeholder="Select Department" class="form-control select2" >
                                    <option value="">Select Branch</option>
                                    @if($getbranch)
                                      @foreach($getbranch as $value)
                                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                            <div class="col-lg-6 col-md-6">
               <div class="form-group">
                  <label class="form-control-label">Department Name</label>
                  <input class="form-control" type="text" 
                   name="department" id="department" value="{{ old('branchname') }}"  />
                    @if ($errors->has('department'))
                  <div class="form-control-feedback">Required field can not be blank.</div>
                     @endif
                </div>
              </div>
            
        </div>
       
      <button type="submit" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right" ><i class="icofont icofont-plus f-18 m-r-5"></i> Create Department </button>
                
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
  let value = $('#department').val();
  if (value == '') {
     swal({
          title: "Error Message",
          text: "Department Name Can not be blank!",
          type: "error"
     });
  }
  else if ($('#branch').val() == '') {
    swal({
          title: "Error Message",
          text: "Please Select Branch!",
          type: "error"
     });
  }
  else{

     $.ajax({
            url: "{{url('/insert-departments')}}",
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success:function(resp){
                if(resp == 1){
                     swal({
                      title: "Operation Performed",
                      text: "Department Created Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                      window.location= "{{ url('/view-departments') }}";
                      }
                       });
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Department Already exsit!",
                            type: "warning"
                       });
                  }
             }

          });  
      }
});
 </script>

@endsection
