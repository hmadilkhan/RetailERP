@extends('layouts.master-layout')

@section('title','Tags')

@section('breadcrumtitle','Tags')

@section('navinventory','active')

@section('content')



<section class="panels-wells">

@if(Session::has('success'))
  <div class="alert alert-success">{{ Session::get('success') }}</div>
@endif


@if(Session::has('error'))
  <div class="alert alert-danger">{{ Session::get('error') }}</div>
@endif
    <div class="row">
        <div class="col-md-6">
         <div class="card">
              <div class="card-header">
                  <h5 class="card-header-text">{{ isset($id) ? 'Edit' : 'Create' }} Tag</h5>
                    @if(isset($id))
                         <a href="{{ route('tags.index') }}" class="f-right">Back to lists</a>
                    @endif                    
              </div>
              <div class="card-block">
                 @php 
                   $route = route("tags.store");
                    if(isset($id)){
                         $route = route("tags.update",$id);
                    }
                 @endphp                     
                  
                 <form method="post" class="form-horizontal" action="{{ $route }}">
                    @csrf
                    
                    @if(isset($id))
                        @method('PATCH')
                     @endif
                            <div class="form-group">
                                 @php 
                                    $tagName = old('name');
                                   
                                    if(isset($id)){
                                       $tagName = old('name') ? old('name') : $edit->name;
                                    }
                                 @endphp                                  
                                
                                <label>Tag Name</label>
                                <input type="text" class="form-control @error('name') 'has-danger' @enderror" placeholder="Brand Name like 'nikke' " name="name" id="name" value="{{ $tagName }}">
                                  @error('name')    
                                    <span class="text-danger" id="name_alert">{{ $message }}</span>
                                  @enderror   
                            </div>
    
                            <div class="form-group">
                                 @php 
                                    $tagSlug = old('slug');
                                   
                                    if(isset($id)){
                                       $tagSlug = old('slug') ? old('slug') : $edit->slug;
                                    }
                                 @endphp                                
                                <label>Slug</label>
                                <input type="text" class="form-control" placeholder="Slug" name="slug" id="slug" value="{{ $tagSlug }}">
                                <span class="text-danger" id="slug_alert"></span>
                            </div>
    
                            <div class="form-group">
                                <label>Priority</label>
                                <input type="number" min="0" value="0" class="form-control" placeholder="Priority" name="priority" id="priority">
                                <span class="text-danger" id="priority_alert"></span>
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
                <table id="table_tag" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
                    <thead>
                    <tr>
                        <th class="d-none">#</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <!--<th>Priority</th>-->
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($lists as $tag)
                         <tr id="row-">
                             <td class="d-none">{{ $tag->priority }}</td>
                             <td>{{ $tag->name }}</td>
                             <td>{{ $tag->slug }}</td>
                             <!--<td>{{-- $brand->priority --}}</td>-->
                             <td>
                                <a href="{{ route('tags.edit',$tag->id) }}" class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" ><i class="icofont icofont-ui-edit text-primary f-18"></i> </a>

                                <a data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete" onclick="remove({{ $tag->id }},'{{ $tag->name }}')"><i class="icofont icofont-ui-delete text-danger f-18"></i></a>
                                
                                <form action="{{ route('tags.destroy',$tag->id) }}" class="d-none" id="removeForm{{ $tag->id }}" method="post">
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
   
       $('#table_tag').DataTable({
            bLengthChange: true,
            displayLength: 10,
            info: true,
            order: [[0, 'desc']],
            language: {
                search:'',
                searchPlaceholder: 'Search Tag',
                lengthMenu: '<span></span> _MENU_'

            }
        });
        
    function remove(id,name){
  
            swal({
                title: "DELETE TAG",
                text: "Do you want to delete tag "+name+"?",
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


