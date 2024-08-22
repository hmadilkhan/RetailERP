@extends('layouts.master-layout')

@section('title','Website Details')

@section('breadcrumtitle','Website Details')

@section('navwebsite','active')

@section('content')


<section class="panels-wells">

  @if(Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
  @endif

  @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
  @endif

<div class="card">
  <div class="card-header">
    <h5 class="card-header-text">Create Slider</h5>
  </div>      
    <div class="card-block form-inline">
     <form name="sliderForm" action="{{ route('sliderStore') }}" method="post" enctype="multipart/form-data">
      @csrf
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
          <label class="form-control-label">Inventory Department</label>
          <select name="depart" id="depart" data-placeholder="Select" class="form-control select2">
            <option>Select</option>
          @if($departments)

             @php $oldDepart = old('depart');
            @foreach($departments as $val)
              <option {{ old('depart') == $val->department_id ? 'selected' : '' }} value="{{ $val->department_id }}">{{ $val->department_name }}</option>
            @endforeach
          @endif
          </select>
          @error('depart')
            <div class="form-control-feedback text-danger">Field is required please select it</div>
          @enderror
        </div>
      

            

        <div class="form-group @error('image') 'has-danger' @enderror m-r-2">       
          <label for="image" class="form-control-label">Slide</label></br>

          <label for="image" class="custom-file">
          <input type="file" name="image" id="image" class="custom-file-input">
          <span class="custom-file-control"></span>
          </label>
          @error('image')
            <div class="form-control-feedback text-danger">{{ $message }}</div>
          @enderror
        </div> 
                   
      <button class="btn btn-primary m-l-1 m-t-1" id="btn_create" type="submit">Add</button>
      </form>
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
         <h5 class="card-header-text">Websites Slider</h5>
         </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th class="d-none">#</th>
               <th>Website</th>
               <th>Slider</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
  
       @foreach($websiteSlider as $value)
				<tr>
				  <td class="d-none">{{ $value->id }}</td>
				  <td>{{ $value->name }}</td>
				  <td>
           @foreach($websiteSliderList as $val)
                <img src="{{ asset('public/assets/images/website/sliders/'.Auth::user()->company_id.'/'.$value->id.'/'.$val->slide) }}" alt=" {{ $val->slide }}" width="32" height="32" />
           @endforeach
          </td>
				  <td>{{($value->status == 1 ? "Active" : "In-Active")}}</td>
				  <td class="action-icon">
					<a href="{{-- route('website.edit',$value->id) --}}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" data-id="{{-- $value->id --}}" data-toggle="tooltip" data-placement="top" data-original-title="Delete"></i>
				  </td>
				</tr>
       @endforeach
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
          searchPlaceholder: 'Search Web Slider',
          lengthMenu: '<span></span> _MENU_'
        }
    });


  $("#btn_create").on('click',function(){
      var webid = $("#website").val();
      var slide = $("#image").get(0).files.length;
      var formData = new FormData($('form[name="sliderForm"]'));


          if(webid == ''){

          }

          if(slide > 0){

                $('form[name="sliderForm"]').submit()

          // $.ajax({
          //   type:'POST',
          //   url: $('form[name="sliderForm"]').attr('action'),
          //   data:formData,
          //   cache:false,
          //   contentType: false,
          //   processData: false,
          //   async:true,
          //   success:function(data){
          //     if()
          //       console.log("success");
          //       console.log(data);
          //   },
          //   error: function(data){
          //       console.log("error");
          //       console.log(data);
          //   }
          // });
         }
  })
 </script>
@endsection