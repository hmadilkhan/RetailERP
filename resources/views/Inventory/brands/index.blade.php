@extends('layouts.master-layout')

@section('title','Brands')

@section('breadcrumtitle','Brands')

@section('navinventory','active')

@section('content')



<section class="panels-wells p-t-3">
@if(Session::has('success'))
  <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif


@if(Session::has('error'))
  <div class="alert alert-danger">{{ Session::get('error') }}</div>
@endif
    <div class="row ">
        <div class="col-md-6">
         <div class="card">
              <div class="card-header">
                  <h5 class="card-header-text">{{ isset($id) ? 'Edit' : 'Create' }} Brand</h5>
                    @if(isset($id))

                         <a href="{{ route('brands.index') }}" class="f-right">Back to lists</a>
                    @endif
              </div>
              <div class="card-block">

                 @php
                   $route = route("brands.store");
                    if(isset($id)){
                         $route = route("brands.update",$id);
                    }
                 @endphp
                 <form method="post" class="form-horizontal" enctype="multipart/form-data" action="{{ $route }}">
                     @csrf

                     @if(isset($id))
                        @method('PATCH')
                     @endif

                            <div class="form-group">
                                 @php
                                    $brandName = old('name');

                                    if(isset($id)){
                                       $brandName = old('name') ? old('name') : $edit->name;
                                    }
                                 @endphp
                                <label>Brand Name</label>
                                <input type="text" class="form-control @error('name') 'has-danger' @enderror" placeholder="Brand Name like 'nikke'" name="name" id="name"
                                value="{{ $brandName }}">
                              @error('name')
                                <span class="text-danger" id="name_alert">{{ $message }}</span>
                              @enderror
                            </div>

                            <div class="form-group">
                                 @php
                                    $brandSlug = old('slug');

                                    if(isset($id)){
                                       $brandSlug = old('slug') ? old('slug') : $edit->slug;
                                    }
                                 @endphp
                                <label>Slug</label>
                                <input type="text" class="form-control" placeholder="Slug" name="slug" id="slug" value="{{ $brandSlug }}">
                                <span class="text-danger" id="slug_alert"></span>
                            </div>

                            <div class="form-group">
                                <label>Parent</label>
                                <select class="form-control select2" placeholder="Parent" name="parent" id="parent">
                                    <option value="">Select</option>
                                    @if($lists)
                                       @php
                                           $parent = old('parent');
                                             if(isset($id)){
                                                $parent = $edit->parent;
                                             }
                                       @endphp
                                      @foreach($lists as $val)
                                        @if($val->parent == null)
                                         <option {{ $parent == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endif
                                      @endforeach
                                    @endif
                                </select>
                                <span class="text-danger" id="parent_alert"></span>
                            </div>

                            <div class="form-group">
                                @php
                                   $priority = old('priority') ? old('priority') : 0;
                                     if(isset($id)){
                                        $priority = $edit->priority ? $edit->priority : 0;
                                     }
                                @endphp
                                <label>Priority</label>
                                <input type="number" min="0" value="{{ $priority }}" class="form-control" placeholder="Priority" name="priority" id="priority">
                                <span class="text-danger" id="priority_alert"></span>
                            </div>

                            <div class="form-group">
                                 @php
                                    $brandImage = asset('storage/images/no-image.png');

                                    if(isset($id)){
                                        if(!empty($edit->image) && Storage::disk('public')->exists('images/brands/'.session('company_id').'/'.$edit->image)){
                                            $brandImage = asset('storage/images/brands/'.session('company_id').'/'.$edit->image);
                                        }

                                    }
                                 @endphp
                               <label class="form-control-label">Brand Logo</label>
                                 <img id="showImage" src="{{ $brandImage }}" class="thumb-img img-fluid" alt="{{ isset($id) ?  $edit->image : 'placeholder.jpg' }}" width="100px" height="100px">

                                  <label for="image" class="custom-file">
                                       <input type="file" id="image" name="image" class="custom-file-input">
                                       <span class="custom-file-control"></span>
                                  </label>
                                @if ($errors->has('image'))
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                @endif
                            </div>


                            <div class="form-group">
                                 @php
                                    $brandBanner = asset('storage/images/no-image.png');

                                    if(isset($id)){
                                        if(!empty($edit->banner) && Storage::disk('public')->exists('images/brands/'.session('company_id').'/'.$edit->banner)){
                                            $brandBanner = asset('storage/images/brands/'.session('company_id').'/'.$edit->banner);
                                        }

                                    }
                                 @endphp
                               <label class="form-control-label">Banner</label>
                                 <img id="showBanner" src="{{ $brandBanner }}" class="thumb-img" alt="{{ isset($id) ?  $edit->banner : 'placeholder.jpg' }}" width="380px" height="128px">

                                  <label for="banner" class="custom-file">
                                       <input type="file" id="banner" name="banner" class="custom-file-input">
                                       <span class="custom-file-control"></span>
                                  </label>
                                @if ($errors->has('banner'))
                                    <span class="text-danger">{{ $errors->first('banner') }}</span>
                                @endif
                            </div>

    		            <button type="submit" class="btn btn-success f-right">{{ isset($id) ? 'Save Changes' : 'Submit' }}</button>
    		       </form>
             </div>
          </div>
        </div>


        <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Lists</h5>
            </div>
            <div class="card-block">
                <table id="table_brand" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th class="d-none">#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Parent</th>
                        <th>Priority</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($lists as $brand)
                        @php
                            $parentTable = $lists->where('id',$brand->parent)->pluck('name');
                        @endphp
                         <tr id="row-"{{ $brand->id }}>
                             <td class="d-none">{{ $brand->priority }}</td>
                             <td>
                                 @php
                                   $image = asset('storage/images/no-image.png');
                                    if($brand->image != null){
                                       $path = 'storage/images/brands/'.session('company_id').'/'.$brand->image;
                                       $image = File::exists($path) ? asset('storage/images/brands/'.session('company_id').'/'.$brand->image) : asset('storage/images/placeholder.jpg');
                                    }
                                 @endphp
                                 <img src="{{ $image }}" class="thumb-img img-fluid" alt="{{ $brand->image == '' ? 'placeholder.jpg' : $brand->image }}" width="100px" height="100px">

                                 @if($brand->banner != null)
                                  @php
                                   $path = 'storage/images/brands/'.session('company_id').'/'.$brand->banner;
                                     if(File::exists($path)){
                                  @endphp
                                    <br/>
                                         <img src="{{ asset('storage/images/brands/'.session('company_id').'/'.$brand->banner) }}" class="thumb-img img-fluid" alt="{{ $brand->banner }}" width="100px" height="100px">
                                  @php
                                     }
                                  @endphp

                                 @endif
                               </td>
                             <td>{{ $brand->name }}</td>
                             <td>{{ $brand->slug }}</td>
                             <td>{{ $parentTable }}</td>
                             <td>{{ $brand->priority }}</td>
                             <td>
                                <a href="{{ route('brands.edit',$brand->id) }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" ><i class="icofont icofont-ui-edit text-primary f-18"></i> </a>

                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onclick="remove({{ $brand->id }},'{{ $brand->name }}')"><i class="icofont icofont-ui-delete text-danger f-18"></i></a>

                                <form action="{{ route('brands.destroy',$brand->id) }}" class="d-none" id="removeForm{{ $brand->id }}" method="post">
                                    @csrf
                                    @METHOD('DELETE')
                                </form>
                             </td>
                         </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        </div>


    </div>

 </section>




@endsection

@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('scriptcode_three')

<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

<script type="text/javascript">

   $(".select2").select2();

   $('#table_brand').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: true,
        order: [[0, 'desc']],
        language: {
            search:'',
            searchPlaceholder: 'Search Brand',
            lengthMenu: '<span></span> _MENU_'

        }

    });

    function readURL(input,elementId) {
        if (input.files && input.files[0]) {

            var reader = new FileReader();
            reader.onload = function(e) {
              $('#'+elementId).attr('src', e.target.result);

              $(".custom-file-control:lang(en)::after").text(e.target.result)
            }
            reader.readAsDataURL(input.files[0]);
        }
    }


    $("#banner").on('change',function(){
        readURL(this,'showBanner');
    })


    $("#image").on('change',function(){
        readURL(this,'showImage');
    })

    function remove(id,name){

            swal({
                title: "DELETE BRAND",
                text: "Do you want to delete brand "+name+"?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "YES",
                cancelButtonText: "NO",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm){
                if(isConfirm){
                      $("#removeForm"+id).submit();
                }else{
                    swal.close();
                }
            });


    }

</script>

@endsection



