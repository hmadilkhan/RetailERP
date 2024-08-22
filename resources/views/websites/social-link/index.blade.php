@extends('layouts.master-layout')

@section('title','Create Social Link')

@section('breadcrumtitle','Create Social Link')

@section('navwebsite','active')

@section('content')


<section class="panels-wells">
<div class="card">
  <div class="card-header">
    <h5 class="card-header-text">Create Social Link</h5>
  </div>      
    <div class="card-block form-inline">

      <div class="form-group m-r-2">
        <label class="form-control-label">Website</label>
        <select name="website" id="website" data-placeholder="Select" class="form-control select2">
          <option value="">Select</option>
          @if($websites)
             @php $oldWebsite = old('website');
            @foreach($websites as $val)
              <option {{ old('website') == $val->id ? 'selected' : '' }} value="{{ $val->id }}">{{ $val->name }}</option>
            @endforeach
          @endif
        </select>
        @error('website')
          <div class="form-control-feedback text-danger">Field is required please select it</div>
        @enderror
       </div>
        <div class="form-group m-r-2">
          <label class="form-control-label">Social Type</label>
          <select name="socialType" id="socialType" data-placeholder="Select" class="form-control select2">
            <option>Select</option>
              <option {{ old('socialType') == 'fb' ? 'selected' : '' }} value="fb">FaceBook</option>
              <option {{ old('socialType') == 'ig' ? 'selected' : '' }} value="ig">Instagram</option>
              <option {{ old('socialType') == 'youtube' ? 'selected' : '' }} value="youtube">YouTube</option>
          </select>
        </div>
        <div class="form-group  m-r-2">       
          <label for="url" class="form-control-label">URL</label>
          <input type="text" name="url" id="url" class="form-control">
        </div> 
                   
      <button class="btn btn-primary m-l-1 m-t-1" id="btn_create" type="button">Add</button>

    </div>
  </div>  
  
</section>
<section class="panels-wells">
   
    @if(Session::has('error'))
         <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
         <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Websites Social Link</h5>
         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th class="d-none">#</th>
               <th>Website</th>
               <th>Social Link</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
      @if(isset($websiteSlider))
       @foreach($websiteSlider as $value)
				<tr>
				  <td class="d-none">{{ $value->id }}</td>
				  <td>{{ $value->name }}</td>
				  <td>
                  
          </td>
				  <td>{{($value->status == 1 ? "Active" : "In-Active")}}</td>
				  <td class="action-icon">
					<a href="{{-- route('website.edit',$value->id) --}}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{-- $value->id --}}" data-toggle="tooltip" data-placement="top" data-original-title="Delete"></i>
				  </td>
				</tr>
       @endforeach
      @endif 
         </tbody>
     </table>
  </div>
</div>
</section>
@endsection

@section('scriptcode_three')



<script type="text/javascript">

  $(".select2").select2();

	$('.table').DataTable({
        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Social Link',
          lengthMenu: '<span></span> _MENU_'
        }
    });
 </script>
@endsection