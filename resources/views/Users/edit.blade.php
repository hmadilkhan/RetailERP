@extends('layouts.master-layout')

@section('title','User')

@section('breadcrumtitle','Create User')

@section('navbranchoperation','active')
@section('navuser','active')

@section('content')
<section class="panels-wells">
  
               <div class="card">
                  <div class="card-header">
                    <h5 class="card-header-text"> Update User</h5>
                      <h5 class=""><a href="{{ url('/usersDetails') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
                     
                    </div>
                  <div class="card-block">
               
                     <form method="POST" action="{{url('/user-update')}}" class="form-horizontal" enctype="multipart/form-data">
                      @method('PUT')
                        @csrf
                        <h5>User Authorization</h5>
                         <input type="hidden" name="id" id="id" class="form-control"  value="{{$userdetails[0]->id}}"/>
                         <div class="row">
                          <div class="col-md-4">
                            <div class="form-group {{ $errors->has('company') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Company</label>
                                <select name="company" id="company"  class="form-control select2" >
                                    <option value="">Select Company</option>
                                    @if($company)
                                      @foreach($company as $value)
                                        <option {{$value->company_id == $userdetails[0]->company_id ? 'selected="selected"' : '' }} 
                                         value="{{ $value->company_id }}">{{ $value->name}}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 @if ($errors->has('company'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div id="singleBranch" class="col-md-4" style="{{(!in_array($userdetails[0]->role,["Regional Manager","Sale Manager"]) ? 'display:block;' : 'display:none;')}}">
                            <div class="form-group">
                                <label class="form-control-label">Select Branch</label>
                                <select name="branch" id="branch"  class="form-control select2" >
                                    <option>Select Branch</option>
                                    @if($branch)
                                      @foreach($branch as $value)
                                        <option {{$value->branch_name == $userdetails[0]->branch_name ? 'selected="selected"' : '' }} 
                                         value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>

            <div id="multipleBranch" class="col-md-4" style="{{( !in_array($userdetails[0]->role,["Regional Manager","Sale Manager"]) ? 'display:none;' : 'display:block;')}}" >
                            <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                                <label class="form-control-label">Select Branch</label>
                                <select name="branches[]" id="multiplebranches"  class="form-control select2" multiple>
                                    <option value="">Select Branch </option>
                  @if($branch)
                    @foreach($branch as $value)
                    <option {{(in_array($value->branch_id, $userBranches->toArray()) ? 'selected' : '')}} 
                     value="{{ $value->branch_id }}">{{ $value->branch_name}}</option>
                    @endforeach
                  @endif
                                </select>
                                 @if ($errors->has('branch'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Select Roles</label>
                                <select name="role" id="role"  class="form-control select2" >
                                    <option>Select Role</option>
                                    @if($role)
                                      @foreach($role as $value)
                                        <option {{$value->role == $userdetails[0]->role ? 'selected="selected"' : '' }} 
                                         value="{{ $value->role_id }}">{{ $value->role}}</option>
                                      @endforeach
                                    @endif
                                </select>
                                 <div class="form-control-feedback"></div>
                            </div>
                        </div>
                               <div class="col-md-4">
                            <div class="form-group">
                               
                                 <input type="hidden" name="createdat" id="createdat" class="form-control"  value="{{$userdetails[0]->created_at}}"/>
                                 <input type="hidden" name="authid" id="authid" class="form-control"  value="{{$userdetails[0]->authorization_id}}"/>
                            </div>
                        </div>
                        </div>
                        <hr>
                        <h5>User Details</h5>
                       <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('fullname') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Full Name</label>
                                <input type="text" name="fullname" id="fullname" class="form-control"  value="{{$userdetails[0]->fullname}}"/>

                                 @if ($errors->has('fullname'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Email</label>
                                <input type="text" name="email" id="email" class="form-control" placeholder="something@gmail.com" value="{{$userdetails[0]->email}}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label">Contact Number</label>
                                <input type="text" name="contact" id="contact" class="form-control" placeholder="0300-1234567" value="{{$userdetails[0]->contact}}"  />
                            </div>
                        </div>
                            
                    </div>
                        <div class="row">
                     <div class="col-md-4">
                           <div class="form-group">
                                <label class="form-control-label">Country</label>
                                <select name="country" id="country" data-placeholder="Select Country" class="form-control select2" >
                                    <option>Select Country</option>
                                    @if($country)
                                      @foreach($country as $value)
                                        <option {{$value->country_name == $userdetails[0]->country_name ? 'selected="selected"' : '' }} 
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
                                <select name="city" id="city" data-placeholder="Select City" class="form-control select2" >
                                    <option>Select City</option>
                                    @if($city)
                                      @foreach($city as $value)
                                        <option {{$value->city_name == $userdetails[0]->city_name ? 'selected="selected"' : '' }}
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
                                <textarea name="address" id="address" class="form-control">{{ltrim($userdetails[0]->address)}}</textarea>
                            </div>
                        </div>
                    </div>

                     <div class="row">
                       

                        
                    </div>
                    <hr>
                    <h5>Set Login Authentication</h5>
                    <div class="row">
                      <div class="col-md-4">
                            <div class="form-group {{ $errors->has('username') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">User Name</label>
                                <input type="text" name="username" id="username" class="form-control"  value="{{$userdetails[0]->username}}"/>
                                 @if ($errors->has('username'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                
                            </div>
                        </div>
                            <div class="col-md-4">
                            <div class="form-group {{ $errors->has('password') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Password</label>
                                <input type="text" name="password" id="password" class="form-control"  value="{{$userdetails[0]->show_password}}"/>
                                 @if ($errors->has('password'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                                
                            </div>
                        </div>

                        <div class="col-md-4" >
                            <a href="#">
                                <img id="vdpimg" src="{{ asset('public/assets/images/users/'.$userdetails[0]->image ) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                                </a>
                             <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                                 <label for="vdimg" class="form-control-label">User Logo</label>
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



$("#country").on('change',function(){

   if($(this).val() != ""){
       $("#city").attr("disabled",false);
   }else {
    $("#city").attr("disabled",true);
   }
});

$("#company").on('change',function(){
   if($("#role").val() == 16)// For Regional Manager
  {
    getBranches("multiplebranches")
  }else
  {
    getBranches("branch")
  }
});

function getBranches(id)
{
  $.ajax({
  url: "{{url('/get-branches-by-company')}}",
  method: 'POST',
  data:{_token:"{{ csrf_token() }}",company:$('#company').val()},
  success:function(result){
    $("#"+id).empty();
    $.each(result, function( index, value ) {
      $("#"+id).append(
        "<option value='"+value.branch_id+"'>"+ value.branch_name + "</option>"
      );
    });

  }
  });
}

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
            url: "{{url('/chk-user')}}",
            method: 'POST',
            data:{_token:"{{ csrf_token() }}",username:$('#username').val()},
            success:function(resp){
              console.log(resp)
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
$("#role").on('change',function(){
  if($(this).val() == 16 || $(this).val() == 18)// For Regional Manager
  {
    $("#multipleBranch").css("display","block")
    $("#singleBranch").css("display","none")
    getBranches("multiplebranches")
    console.log($(this).val())
  }else{
     console.log($(this).val())
     $("#multipleBranch").css("display","none")
     $("#singleBranch").css("display","block")
  }
});
   </script>
@endsection