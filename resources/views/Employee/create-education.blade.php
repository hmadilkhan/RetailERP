@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','View Employee')

@section('navemployees','active')

@section('navhire','active')

@section('navqualification','active')

@section('content')

<section class="panels-wells">
<div class="card" >
     <div class="card-header">
         <h5 class="card-header-text">Create Employee Education Details</h5>
            <a class="f-right" onclick="toggle()">
           <i class="icofont icofont-minus"></i>
         </a>
         </div>      
       <div class="card-block" id="insert-card">
       	 <form method="post" id="upload_form" enctype="multipart/form-data">
           
        {{ csrf_field() }}
        
           <div class="row">
                  <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Select Employee:</label>
                     <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" onchange="getdetails($('#employee').val())" >
                    <option value="">Select Employee</option>
                       @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
           	 <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Degree Name:</label>
            <input type="text" name="degree" id="degree" class="form-control"/>
        </div>
        </div>
          	 <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Institute Name:</label>
            <input type="text" name="institute" id="institute" class="form-control" />
        </div>
        </div>
        
           </div>
            <div class="row">
           <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Passing Year:</label>
            <input type="Number" name="passingyear" id="passingyear" class="form-control" />
        </div>
        </div>
 <div class="col-lg-4 col-md-4">
                    <a href="#">
                <img id="docimgs" src="{{ asset('assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                </a>
             <div class="form-group{{ $errors->has('docimg') ? 'has-danger' : '' }} ">
                 <label for="docimg" class="form-control-label">Upload Document</label>
                <br/>
                    <label for="docimg" class="custom-file">
                     <input type="file" name="docimg" id="docimg" class="custom-file-input">
                    <span class="custom-file-control"></span>
                    </label>
              </div>
              </div>

           </div>
               <button type="Submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" >   <i class="icofont icofont-plus"> </i>
                        Submit 
                    </button>
                </form>

           </div> 
 </div>


 <div class="card">
     <div class="card-header">
     	
                      <div class="form-group"> 
                        <label class="form-control-label">Select Employee:</label>
                     <select name="employee2" id="employee2" data-placeholder="Select Employee" class="form-control select2" onchange="getdetails($('#employee2').val())" >
                    <option value="">Select Employee</option>
                       @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
         </div>     
       <div class="card-block">
     <table id="tbleducation" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0" style="margin-top: -50px;">

         <thead>
            <tr>
              <th>Image</th>
               <th>Degree Name</th>
               <th>Passing Year</th>
               <th>Institution Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
               
         </tbody>
        
      
     </table>
  </div>
</div>

 
</section>
	 <form method="post" id="update_form" enctype="multipart/form-data">
        {{ csrf_field() }}
 <!-- modals -->
 <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Update Modal</h4>
             </div>
             <div class="modal-body">
          <div class="row">
                  <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Select Employee:</label>
                     <select name="employeemodal" id="employeemodal" data-placeholder="Select Employee" class="form-control select2">
                    <option value="">Select Employee</option>
                       @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
           	 <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Degree Name:</label>
            <input type="text" name="degreemodal" id="degreemodal" class="form-control"/>
        </div>
        </div>
          	 <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Institute Name:</label>
            <input type="text" name="institutemodal" id="institutemodal" class="form-control" />
        </div>
        </div>
        
           </div>
            <div class="row">
           <div class="col-lg-4 col-md-4">
           <div class="form-group">
            <label class="form-control-label">Passing Year:</label>
            <input type="Number" name="passingyearmodal" id="passingyearmodal" class="form-control" />
        </div>
        </div>
 <div class="col-lg-4 col-md-4">
 	<input type="hidden" name="oldimage" id="oldimage" value="">
 	<input type="hidden" name="educationid" id="educationid" value="">
                    <a href="#">
                <img id="docimgsmodal" src="{{ asset('assets/images/placeholder.jpg') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                </a>
             <div class="form-group{{ $errors->has('docimg') ? 'has-danger' : '' }} ">
                 <label for="docimgmodal" class="form-control-label">Upload Document</label>
                <br/>
                    <label for="docimgmodal" class="custom-file">
                     <input type="file" name="docimgmodal" id="docimgmodal" class="custom-file-input">
                    <span class="custom-file-control"></span>
                    </label>
              </div>
              </div>

           </div>
             </div>
             <div class="modal-footer">
                <button type="submit" id="btn_update" class="btn btn-success waves-effect waves-light">Update</button>
             </div>
          </div>
           </div>
        </div> 
    </form>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

  function readURL(input,id) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#'+id).attr('src', e.target.result);
    }
    
    reader.readAsDataURL(input.files[0]);
  }
}

$("#docimg").change(function() {
  readURL(this,'docimgs');
});

$("#docimgmodal").change(function() {
  readURL(this,'docimgsmodal');
});





$('#upload_form').on('submit', function(event){
event.preventDefault();
	if ($('#employee').val() == "") {
		 swal({
            title: "Error Message",
            text: "Please Select Employee!",
            type: "error"
              });
	}
	else if ($('#degree').val() == "") {
		 swal({
            title: "Error Message",
            text: "Degree Name Can not left blank!",
            type: "error"
              });
	}
	else{
		$.ajax({
    	 url: "{{url('/storeeducation')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(resp){
        if(resp == 1){
             swal({
                    title: "Success",
                    text: "Employee Education Submited Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                         window.location= "{{ url('/getqualification') }}";
                   }
               });
          }
     }

  });   
	}
}); 


function getdetails(id){

		$.ajax({
    	  url: "{{url('/getqualification-details')}}",
	        type: 'GET',
	        data:{_token:"{{ csrf_token() }}",
	        employee:id,
	        },
         
    success:function(result){
    	  			$("#tbleducation tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tbleducation tbody").append(
                          "<tr>" +
                           "<td class='text-center'><img width='42' height='42' src='assets/images/employees/documents/"+((result[count].document != "") ? result[count].document : 'placeholder.jpg')+"' class='d-inline-block img-circle' alt='"+result[count].document+"'/></td>" +
                            "<td>"+result[count].degree_name+"</td>" +  
                            "<td>"+result[count].passing_year+"</td>" +  
                            "<td>"+result[count].institute_name+"</td>" +  
                            "<td class='action-icon'><i onclick='edit("+result[count].education_id+","+"\""+ result[count].degree_name + "\","+"\""+ result[count].passing_year + "\","+"\""+ result[count].institute_name + "\","+"\""+ result[count].document + "\","+result[count].emp_id+")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>&nbsp;&nbsp;<i onclick='remove("+result[count].education_id+","+"\""+ result[count].degree_name + "\") 'class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i></td>"+
                          "</tr>" 
                         );
                    }
		  }
      });
}

//Alert confirm
function remove(id,degree){
 
      swal({
          title: "Are you sure?",
          text: "Do you want to Delete this "+degree+ " Document?",
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
                        url: "{{url('/deleteeducation')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Delete Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        getdetails($('#employee').val());
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled :)", "error");
           }
        });
  }

function edit(id,degree,year,institute,image,empid){
  $('#employeemodal').val(empid).change();
  $('#degreemodal').val(degree);
  $('#educationid').val(id);
  $('#oldimage').val(image);
  $('#passingyearmodal').val(year);
  $('#institutemodal').val(institute);

  $('#docimgsmodal').attr("src","./assets/images/employees/documents/"+image);

  $("#update-modal").modal("show");

}

$('#update_form').on('submit', function(event){
event.preventDefault();
$.ajax({
    	 url: "{{url('/updateeducation')}}",
    	 method: 'POST',
    	 data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(resp){
        if(resp == 1){
             swal({
                    title: "Success",
                    text: "Employee Education Updated Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
                         getdetails($('#employee2').val());
                         $("#update-modal").modal("hide");
                   }
               });
          }
     }

  });  
});

function toggle(){

  $('#insert-card').toggle();
}

  $('#insert-card').hide();

 </script>

@endsection


