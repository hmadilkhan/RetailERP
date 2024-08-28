@extends('Admin.layouts.master-layout')

@section('title','User')

@section('breadcrumtitle','Edit User')

@section('navuser','active')

@section('content')
 <section class="panels-wells">

               <div class="card">
                  <div class="card-header">
                  	<h5 class="card-header-text"> Edit User</h5>
                     <h5 class=""><a href="{{ route('usersDetails.index') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                     
                    </div>
                  <div class="card-block">
                     <form method="POST" action="{{ url('/update-users') }}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <h5>User Authorization</h5>
                         <div class="row">

                          <input type="hidden" name="userid" id="userid" value="{{$user[0]->id}}">
                          <input type="hidden" name="prevImg" id="prevImg" value="{{$user[0]->image}}">
                          <input type="hidden" name="authId" id="authId" value="{{$user[0]->authorization_id}}">

                           <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Select Company</label>
                                <select name="company" id="company" data-placeholder="Select Company" class="form-control select2" >
                                    <option value="">Select Company</option>
                                    @if($company)
                                      @foreach($company as $value)
                                        @if($value->company_id == $user[0]->company_id)
                                         <option selected="selected" value="{{ $value->company_id }}">{{ $value->name}}</option>
                                        @else
                                         <option value="{{ $value->company_id }}">{{ $value->name}}</option>
                                        @endif
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        	 <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Select Branch</label>
                                <select  name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
                                  @if($branch)
                                      @foreach($branch as $value)
                                        @if($value->branch_id == $user[0]->branch_id)
                                         <option selected="selected" value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>
                                        @else
                                         <option value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>
                                        @endif
                                      @endforeach
                                    @endif  
                                     
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Select Roles</label>
                                <select name="role" id="role" data-placeholder="Select Role" class="form-control select2" >
                                    <option value="">Select Role</option>
                                    @if($role)
                                      @foreach($role as $value)
                                        @if($value->role == $user[0]->role)
                                         <option selected="selected" value="{{ $value->role_id }}">{{ $value->role}}</option>
                                        @else
                                         <option value="{{ $value->role_id }}">{{ $value->role}}</option>
                                        @endif
                                        <
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                        </div>
                        <hr>
                        <h5>User Details</h5>
                       <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('fullname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Full Name</label>
                                <input type="text" name="fullname" id="fullname" class="form-control" value="{{ $user[0]->fullname }}"/>
                                 @if ($errors->has('fullname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group {{ $errors->has('email') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="something@gmail.com" value="{{ $user[0]->email }}"/>
                                @if ($errors->has('email'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('contact') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Contact Number</label>
                                <input type="text" name="contact" id="contact" class="form-control" placeholder="0300-1234567" value="{{ $user[0]->contact }}"/>
                                @if ($errors->has('contact'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>
                            
                    </div>
                        <div class="row">
                     <div class="col-md-4">
                           <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                                    <option value="">Select Country</option>
                                    @if($country)
                                      @foreach($country as $value)
                                        <option {{$value->country_name == $user[0]->country_name ? 'selected="selected"' : '' }} 
                                         value="{{ $value->country_id }}">{{ $value->country_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">City</label>
                                <select  name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                    @if($city)
                                      @foreach($city as $value)
                                        <option {{$value->city_name == $user[0]->city_name ? 'selected="selected"' : '' }} 
                                         value="{{ $value->city_id }}">{{ $value->city_name }}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Address</label>
                                <textarea name="address" id="address" class="form-control">{{$user[0]->address }}</textarea>
                            </div>
                        </div>
                    </div>

                     <div class="row">
                       

                        
                    </div>
                    <hr>
                    <h5>Set Login Authentication</h5>
                    <div class="row">
                    	<div class="col-md-4">
                            <div id="user" class="form-group {{ $errors->has('username') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">User Name</label>
                                <input type="text" name="username" id="username" class="form-control" value="{{ $user[0]->username }}"/>
                                 @if ($errors->has('username'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group {{ $errors->has('password') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" value="{{ $user[0]->show_password }}"/>
                                 @if ($errors->has('password'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                
                            </div>
                        </div>

                           <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('assets/images/users/'.$user[0]->image ) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">Profile Picture</label>
                                <br/>
                                    <label for="vdimg" class="custom-file">
                                                 <input type="file" name="vdimg" id="vdimg" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>
                                @if ($errors->has('vdimg'))
                                    <div class="form-control-feedback">{{ $errors->first('vdimg') }}</div>
                                @endif
                              </div>
                        </div>

                    </div>

                 

                    <button type="submit" id="btnsubmit" class="btn btn-md btn-primary waves-effect waves-light f-right">
                        Update User
                    </button>
                
                       
                      </form>
            
                  </div>
               </div>
            </section>    
  
@endsection

@section('scriptcode_three')
<script type="text/javascript">
   $(".select2").select2();

$("#company").on('change',function(){
       $("#branch").attr("disabled",false);
       console.log("ajax running")
       $.ajax({
            url: "{{url('/get-branches')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",id:$(this).val()},
            dataType:"json",
            success:function(resp){
                for(var count=0; count < resp.length; count++){
                    $("#branch").append("<option value=''>Select Branch</option>");
                    $("#branch").append(
                      "<option value='"+resp[count].branch_id+"'>"+resp[count].branch_name+"</option>");
                }
            }
        });

   
});

$("#country").on('change',function(){
   if($(this).val() != ""){
       $("#city").attr("disabled",false);
   }else {
    $("#city").attr("disabled",true);
   }
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

$("#vdimg").change(function() {
  readURL(this,'vdpimg');
});



$("#username").on('change',function(){
      $.ajax({
            url: "{{url('/check-user')}}",
            method: 'POST',
            data:{_token:"{{ csrf_token() }}",username:$('#username').val()},
            success:function(resp){
              console.log(resp);
              if(resp == 1){

                      $('#user').addClass('has-danger');
                      swal({
                            title: "Already exsist",
                            text: "Username Already exsist!",
                            type: "warning"
                       });
                      $('#username').val('');
                     
              }else{
                  $('#user').removeClass('has-danger');
                  $('#user').addClass('has-success');
              }

            }
         });
       $("#username").focus(); 
});

   </script>
@endsection