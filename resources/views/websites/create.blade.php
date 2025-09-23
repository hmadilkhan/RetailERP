@extends('layouts.master-layout')
@section('title','Create Website')
@section('breadcrumtitle','Create Website')
@section('navcompany','active')
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12 m-t-2">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-globe mr-2"></i>Create New Website</h4>
                </div>
                <div class="card-body " style="padding:10px;">
                    <form method="POST" action="{{ route('website.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Company *</label>
                                    <select name="company_id" class="form-control select2">
                                        <option value="">Select Company</option>
                                        @if($companies)
                                            @foreach($companies as $company)
                                                <option {{ old('company_id') == $company->company_id ? 'selected' : '' }} value="{{ $company->company_id }}">{{ $company->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('company_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Website Type *</label>
                                    <select class="form-control" name="type">
                                        <option value="">Select Type</option>
                                        <option {{ old('type') == 'restaurant' ? 'selected' : '' }} value="restaurant">Restaurant</option>
                                        <option {{ old('type') == 'grocery' ? 'selected' : '' }} value="grocery">Grocery</option>
                                        <option {{ old('type') == 'boutique' ? 'selected' : '' }} value="boutique">Boutique</option>
                                    </select>
                                    @error('type')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Website Name *</label>
                                    <input name="name" type="text" class="form-control" placeholder="Enter website name" value="{{ old('name') }}">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Domain Name</label>
                                    <input name="url" type="url" class="form-control" placeholder="https://example.com" value="{{ old('url') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">UAN Number</label>
                                    <input name="uan_number" type="text" class="form-control" placeholder="Enter UAN number" value="{{ old('uan_number') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">WhatsApp Number</label>
                                    <input name="whatsapp" type="text" class="form-control" placeholder="Enter WhatsApp number" value="{{ old('whatsapp') }}">
                                </div>
                            </div>
                        </div>
                        
						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Github Token</label>
                                    <input name="github_token" type="text" class="form-control" placeholder="Enter Github Token" value="{{ old('github_token') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">github Owner</label>
                                    <input name="github_owner" type="text" class="form-control" placeholder="Enter Github Owner" value="{{ old('github_owner') }}">
                                </div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Github Repo</label>
                                    <input name="github_repo" type="text" class="form-control" placeholder="Enter Github Repo" value="{{ old('github_repo') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Github Branch</label>
                                    <input name="github_branch" type="text" class="form-control" placeholder="Enter Github Owner" value="{{ old('github_branch') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Logo</label>
                                    <div class="text-center m-b-1">
                                        <img id="preview" src="{{ asset('storage/images/placeholder.jpg') }}" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                    @error('logo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Favicon</label>
                                    <div class="text-center m-b-1 ">
                                        <img id="fpreview" src="{{ asset('storage/images/placeholder.jpg') }}" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                    <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*">
                                    @error('favicon')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 m-t-4">
                            <a href="{{ route('website.index') }}" class="btn btn-secondary m-r-1">
                                <i class="fas fa-times "></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary float-right">
                                <i class="fa fa-save m-l-1"></i>Create Website
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scriptcode_three')



<script type="text/javascript">

	$(".select2").select2();
	var count = 0;
	function readURL(input,id) {
	  if (input.files && input.files[0]) {
		var reader = new FileReader();

		reader.onload = function(e) {
		  $('#'+id).attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
	  }
	}

	$("#logo").change(function() {
	  readURL(this,'preview');
	});


	$("#favicon").change(function() {
	  readURL(this,'fpreview');
	});

	// function clone_field(){
	// 	console.log(count)
	// 	if(count <3){
	// 		count++;
	// 		$('#inputClone').clone().appendTo("#inputfieldClone");
	// 	}else{
	// 		alert("You can only select three at a time. "+count);
	// 	}
	// }

	// function social_clone_field(){
	// 	console.log(count)
	// 	if(count <3){
	// 		count++;
	// 		$('#socialInputClone').clone().appendTo("#socialinputfieldClone");
	// 	}else{
	// 		alert("You can only select three at a time. "+count);
	// 	}
	// }

	// $(document).on("click",".remove_row",function(){
	// 	count= count - 1;
	// 	$(this).closest('#inputClone').remove();
	// });
	// $(document).on("click",".social_remove_row",function(){
	// 	count= count - 1;
	// 	$(this).closest('#socialinputfieldClone').remove();
	// });


	$("#btn_contact_create").on('click',function(){

		var type   = $("#contactype").val();
		var number = $("#number").val();

           if(!type && !number){
           	   $("#alert_contact").text("both field is required");
           }else{
               //formData({id:webid,ctype:type,phone:number});
          }
	})


	function formData(arrayRow){
               // $.ajax({
               //          url:"",
               //          type:"POST",
               //          data:{arrayRow},
               //          dataType:'json',
               //          async:true,
               //          success:function(resp){
               //              if(resp.state == 200){
               //              	$.each(resp.data,function(i,v){
               //                       console.log(i+' : '+v)
               //              	})
               //              }
               //          }
               // })

	}
</script>


@endsection


@section('css_code')
<style>
.card { border: none; border-radius: 10px; }
.form-label { color: #495057; font-size: 14px; }
.form-control { border-radius: 6px; border: 1px solid #dee2e6; }
.form-control:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
.btn { border-radius: 6px; padding: 8px 20px; }
.img-thumbnail { border-radius: 8px; }
</style>
@endsection
