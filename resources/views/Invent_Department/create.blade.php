@extends('layouts.master-layout')

@section('title','Create Inventory Department')

@section('breadcrumtitle','Create Department')
@section('navinventory','active')
@section('navinvent_depart','active')

@section('content')

<section class="panels-wells">

<form method="POST" id="deptform" class="form-horizontal" enctype="multipart/form-data">
   @csrf
   <div class="row">
     <div class="col-md-8 p-1">
        <div class="card">
             <div class="card-header">
                <h5 class="card-header-text" id="title-hcard"> Create Department</h5>
              </div>
              <div class="card-block"> 
            <div class="row">    
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Department Code <span class="text-danger">*</span></label>
                      <input class="form-control" type="text" name="code" id="code" placeholder='Department Code'/>
                      <span class="form-control-feedback text-danger" id="dptcode_alert"></span>
                  </div>
                </div>
            
    		    <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Department Name <span class="text-danger">*</span></label>
                      <input class="form-control" type="text"
                       name="deptname" id="deptname" placeholder='Department Name'/>
                       <span class="form-control-feedback text-danger" id="deptname_alert"></span>
                  </div>
                </div>   
                
                <div class="col-lg-4 col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Parent</label>
                      <select name="parent" id="parent" class="select2">
                          <option value="">Select</option>
                          @foreach($depart as $val)
                            <option value="{{ $val->department_id }}">{{ $val->department_name }}</option>
                          @endforeach
                      </select>    
                      <span class="form-control-feedback text-danger" id="parent_alert"></span>
                  </div>
                </div>                
                
            </div>  
           @if($websites)
            <hr/>
            <div class="form-group">
                <label for="showWebsite">
                    <input type="checkbox" id="showWebsite" name="showWebsite">
                    Show Product on Website
                </label>
            </div>
           <div class="row d-none" id="website-module">  
    		       <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Show website department name</label>
                      <input class="form-control" type="text"
                       name="webdeptname" id="webdeptname" placeholder='Show website department name'/>
                       <span class="form-control-feedback text-danger" id="webdeptname_alert"></span>
                  </div>
                </div>  
                <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Meta Title</label>
                      <input class="form-control" type="text"
                       name="metatitle" id="metatitle" placeholder='Meta Title'/>
                       <span class="form-control-feedback text-danger" id="metatitle_alert"></span>
                  </div>
                </div> 
                <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-control-label">Meta Description</label>
                      <textarea class="form-control" rows="5"
                       name="metadescript" id="metadescript" placeholder='Meta Description'></textarea>
                       <span class="form-control-feedback text-danger" id="metadescript_alert"></span>
                  </div>
                </div>                                   
            </div> 
           @endif
         </div>
       </div>
     </div> <!-- field portion-->
     <div class="col-md-4 p-1">
       <div class="form-group row">
          <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="submit" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus" 
            ></i>&nbsp; Save</button>.
              <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
            ></i> Clear</button>
       </div>

     <div class="card">
                  <div class="card-header">
                  <h4 for="departImage">Image</h4>
                  </div>
                  <div class="card-block p-2 p-t-0">
              <div class="form-group">
                       <a href="javascript:void(0)">
                        <img id="previewDepartImage" src="{{ asset('storage/images/placeholder.jpg') }}" height="180px" width="180px" class="thumb-img" alt="img">
                        </a>

                    <div class="form-group {{ $errors->has('departImage') ? 'has-danger' : '' }} m-t-10">
                                

                                    <label for="departImage" class="custom-file">
                                                <input type="file" name="departImage" id="departImage" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>         
                                @if ($errors->has('departImage'))
                                    <span class="form-control-feedback">{{ $errors->first('departImage') }}</span>
                                @endif
                              </div> 
                
              </div> 
              </div>    
              </div> 
        @if($websites)    
        <div class="card d-none" id="banner-imageBox">
                  <div class="card-header">
                     <h4 for="bannerImage">Banner</h4>
                  </div>
                  <div class="card-block p-2 p-t-0">
                    <div class="form-group">
                       <a href="javascript:void(0)">
                        <img id="previewDepartBannerImage" src="{{ asset('storage/images/placeholder.jpg') }}" height="180px" class="thumb-img width-100" alt="img">
                        </a>

                    <div class="form-group {{ $errors->has('bannerImage') ? 'has-danger' : '' }} m-t-10">
                                    <label for="bannerImage" class="custom-file">
                                                <input type="file" name="bannerImage" id="bannerImage" class="custom-file-input">
                                                <span class="custom-file-control"></span>
                                            </label>         
                                @if ($errors->has('bannerImage'))
                                    <span class="form-control-feedback">{{ $errors->first('bannerImage') }}</span>
                                @endif
                              </div> 
                
              </div> 
            </div>    
          </div> 
        @endif
     </div> <!-- col-md-4 close image portion -->
   </div>
   </form>
</section>    


@endsection


@section('scriptcode_three')

<script type="text/javascript">


   $(".select2").select2();
$("#parent").on('change',function(){
  if($(this).val() != ''){
    $("#showWebsite").trigger('click');
  }
});

$("#showWebsite").on('click',function(){
    
    if($(this).is(':checked')==true){
      $("#parent").val('').change();
        if($("#website-module").hasClass('d-none')){
            $("#website-module").removeClass('d-none');
        }
        
        
        if($("#banner-imageBox").hasClass('d-none')){
            $("#banner-imageBox").removeClass('d-none');
        }        
    }
    
    if($(this).is(':checked')==false){
        if(!$("#website-module").hasClass('d-none')){
            $("#website-module").addClass('d-none');
        }
        
        if(!$("#banner-imageBox").hasClass('d-none')){
            $("#banner-imageBox").addClass('d-none');
        }         
    }    
});


@if(old('metadescript'))
   $("#metadescript").val('{{ old("metadescript") }}');
@endif
</script>

@endsection