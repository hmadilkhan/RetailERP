@extends('layouts.master-layout')

@section('title','Bank Account')

@section('breadcrumtitle','Bank Account')
@section('navaccountsoperation','active')
@section('navbankaccount','active')


@section('content')
<section class="panels-wells">
<div class="card">

     <div class="card-header">
         <h5 class="card-header-text">Update Account Details</h5>
         <a href="{{ url('/view-accounts') }}" ><h6 class="m-b-0 f-w-400 text-primary"><i class="icofont icofont-arrow-left"></i>Back to list</h6></a>
         </div>      
       <div class="card-block">
           <form method="post" action="{{url('updateaccount')}}" enctype="multipart/form-data">
               @csrf
               @method('put')
               <input type="hidden" value="{{$getdetails[0]->bank_account_id}}" name="id">
               <input type="hidden" value="{{$getdetails[0]->image}}" name="prev_image">
       	 <div class="row">
           <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Select Bank</label>
                      
                            
                     <select class="select2 form-control" data-placeholder="Select Bank" id="bank" name="bank">
                      <option value="">Select Bank</option>
                        @foreach($getbank as $value)
                      <option {{$value->bank_name == $getdetails[0]->bank_name ? 'selected="selected"' : '' }} value="{{$value->bank_id}}">{{$value->bank_name}}</option>
                      @endforeach
                    </select>
				</div>
              </div>

           <div class="col-lg-4 col-md-4">
               <div class="form-group">
                  <label class="form-control-label">Select Branch</label>
                      
                           
                     <select class="select2 form-control" data-placeholder="Select Branch" id="branch" name="branch">
                      <option value="">Select Branch</option>
                       @foreach($getbranches as $value)
                      <option {{$value->branch_name == $getdetails[0]->branch_name ? 'selected="selected"' : ''}} value="{{$value->branch_id}}">{{$value->branch_name}}</option>
                      @endforeach
                    </select>
				</div>
              </div>

              <div class="col-lg-4 col-md-4">
                   <div class="form-group">
                  <label class="form-control-label">Account Title</label>
                  <input class="form-control" type="text" value="{{$getdetails[0]->account_title}}" 
                   name="accountitle" id="accountitle" required />
                   <span class="help-block"></span>
              </div>
                </div>

            </div>
            <div class="row">
           <div class="col-lg-4 col-md-4">
                   <div class="form-group">
                  <label class="form-control-label">Account Number</label>
                  <input class="form-control" type="number" value="{{$getdetails[0]->account_no}}"
                   name="accountno" id="accountno" placeholder="0000001123456702" required />
                   <span class="help-block"></span>
              </div>
  		</div>
  		  <div class="col-lg-4 col-md-4">
                   <div class="form-group">
                  <label class="form-control-label">Account Type</label>
                  <input class="form-control" type="text" value="{{$getdetails[0]->account_type}}"
                   name="accounttype" id="accounttype" placeholder="Current | Saving" required />
                   <span class="help-block"></span>
              </div>
  		</div>
                <div class="col-md-4">
                    <label for="image" class="form-control-label">Image</label>
                    <a href="#">
                        <img id="simg" src="{{ asset('public/assets/images/bank-account/'.(!empty($getdetails[0]->image) ? $getdetails[0]->image : 'placeholder.jpg').'') }}" class="thumb-img img-fluid width-100" alt="img" style="width: 100px;height: 100px;">
                    </a>
                    <div class="form-group {{ $errors->has('image') ? 'has-danger' : '' }} m-t-10">
                        <label for="image" class="custom-file">
                            <input type="file" name="image" id="image" class="custom-file-input" multiple>
                            <span class="custom-file-control"></span>
                        </label>
                        @if ($errors->has('image'))
                            <div class="form-control-feedback">{{ $errors->first('image') }}</div>
                        @endif
                    </div>

                </div>
</div>
           <button type="submit"  class="btn btn-md btn-primary waves-effect waves-light f-right"  >
               Update Account Details
           </button>
           </form>
       </div>
</div>

 <div class="modal fade modal-flex" id="bank-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Add Bank</h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group"> 
                                            <label class="form-control-label">Bank Name:</label>
                                             <input type="text"  name="bankname" id="bankname" class="form-control" />
                                            </div>
                                          </div>
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn_bank" class="btn btn-success waves-effect waves-light" onClick="addbank()">Add Bank</button>
                                 </div>
                              </div>
                           </div>
                        </div> 

 <div class="modal fade modal-flex" id="branch-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Add Branch</h4>
                                 </div>
                                 <div class="modal-body">
                                 	 <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group"> 
                                            <label class="form-control-label">Select Bank:</label>
                                              <select class="select2 form-control" data-placeholder="Select Bank" id="bankmodal" name="bankmodal">
						                      <option value="">Select Bank</option>
						                        @foreach($getbank as $value)
                   							   <option value="{{$value->bank_id}}">{{$value->bank_name}}</option>
                     							 @endforeach
						                    </select>
                                            </div>
                                          </div>
                                      </div>  
                                   <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group"> 
                                            <label class="form-control-label">Branch Name:</label>
                                             <input type="text"  name="branchname" id="branchname" class="form-control" />
                                            </div>
                                          </div>
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn_branch" class="btn btn-success waves-effect waves-light" onclick="addbranch()">Add Branch</button>
                                 </div>
                              </div>
                           </div>
                        </div> 

                         
          
              

                
              
            
 
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
	$(".select2").select2();

$("#btn_addbank").on('click',function(){
	$('#bankname').empty();
	$("#bank-modal").modal("show");

});

$("#btn_addbranch").on('click',function(){

	$("#branch-modal").modal("show");
});
    $("#image").change(function() {
        readURL(this,'simg');
    });

    function readURL(input,id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#'+id).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
function addbank(){	
	$.ajax({
            url: "{{url('/submitbankdetails')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            dataType:"json",
            bankname:$('#bankname').val(),
        	},
            success:function(resp){
                if(resp){
                     swal({
                            title: "Bank Created",
                            text: "Bank Created Successfully!",
                            type: "success"

                       });
                     $("#bank-modal").modal("hide");
					$("#bank").empty();
                     for(var count=0; count < resp.length; count++){
                     	$("#bank").append("<option value=''>Select Bank</option>");
                     	$("#bank").append(
                     		"<option value='"+resp[count].bank_id+"'>"+resp[count].bank_name+"</option>");
                     }
                  }
             }

          });   


}
function addbranch(){
	$.ajax({
            url: "{{url('/submitbankdetails')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            dataType:"json",
            bankname:$('#bankmodal option:selected').text(),
            branchname:$('#branchname').val(),
            bank_id:$('#bankmodal').val(),
        	},
            success:function(resp){
                if(resp){
                	console.log(resp);
                     swal({
                            title: "Branch Created",
                            text: "Branch Created Successfully!",
                            type: "success"

                       });
                     $("#branch-modal").modal("hide");
					$("#branch").empty();
                     for(var count=0; count < resp.length; count++){
                     	$("#branch").append("<option value=''>Select Branch</option>");
                     	$("#branch").append(
                     		"<option value='"+resp[count].branch_id+"'>"+resp[count].branch_name+"</option>");
                     }
                  }
             }

          });   


}

function update(id)
{
	$.ajax({
            url: "{{url('/updateaccount')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            tittle:$('#accountitle').val(),
            accnumber:$('#accountno').val(),
            bank_id:$('#bank').val(),
            branch_id:$('#branch').val(),
            acctype:$('#accounttype').val(),
            id:id,
        	}, 
            success:function(resp){
               if(resp == 1){
                     swal({
                            title: "Account Updated",
                            text: "Account Updated Successfully!",
                            type: "success"
                        },function(isConfirm){
         			if(isConfirm){
             			 window.location= "{{ url('/view-accounts') }}";
            					}
                       });
                  }
             }

          });   
}
</script>
@endsection