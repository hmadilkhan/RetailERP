@extends('layouts.master-layout')

@section('title','Customer Reviews')

@section('breadcrumtitle','Customer Reviews')

@section('navwebsite','active')

@section('content')
<section class="panels-wells p-t-3">
   
    @if(Session::has('error'))
         <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if(Session::has('success'))
         <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if(Auth::user()->username == 'uzair.kashee')
        <img src="{{ base_path('Retail/storage').'/images/no-image.jpg' }} " >
    @endif
  @php $url_parameter_webId = Request::has('id')  ? Request::get('id')  : null; @endphp
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Filter</h5>
      </div>
       <div class="card-block">
           <div class="col-md-4">
             <div class="form-group">
                <label class="form-control-label">Website</label>
                <select name="website" id="website" class="select2" data-placeholder="Select">
                    <option value="">Select</option>
                    @php $websiteValue = $url_parameter_webId != null ? $url_parameter_webId : old('websites') @endphp
                    @foreach($websites as $value) 
                    <option {{ $websiteValue == $value->id ? 'selected' : '' }} value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
                </select>
             </div>
           </div>
       </div>
    </div>  

    @if(isset($reviews))
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Lists</h5>
          </div>
       <div class="card-block">

     <table id="demandtb" class="table dt-responsive table-striped nowrap" width="100%" cellspacing="0">
         <thead>
            <tr>
               <th class="d-none"></th>
               <th>Image</th>
               <th>Customer</th>
               <th>Rating</th>
               <th>Title</th>
               <th>Review</th>
               <th>Product URL</th>
               <th>Action</th>
            </tr>
		</thead>
		<tbody>
              @foreach($reviews as $value)
                 @php 
                   $website_name = $websites->where('id',$value->website_id)->pluck('name'); 
                      $image = asset('storage/images/no-image.jpg');
                      
                      if(File::exists('storage/images/customer-review/'.$value->image)){
                          $image = asset('storage/images/customer-review/'.$value->image);
                      }
                 @endphp
				<tr>
                  <td class="d-none">{{ $value->id }}</td>  
				  <td class="text-center"><img width="42" height="42" src="{{ $image }}" class="d-inline-block img-circle " alt="{{ !empty($value->image) ? $value->image : 'placeholder.jpg' }}"></td>
				  <td>{{ $value->customer_name }} <br/> {{ $value->customer_email  }}</td>
				  <td>{{ $value->rating }}</td>
          <td>{{ $value->review_title }}</td>
				  <td><p>{{ $value->review }}</p></td>
          <td><a href="{{ $value->product_url }}">Go to Product Page</a></td>
				  <td class="action-icon">
					{{-- <a href="{{ route('testimonials.edit',$value->id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="remove({{ $value->id }},'{{ $website_name }}')" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
					<form action="{{ route('testimonials.destroy',$value->id) }}" method="post" id="removeForm{{ $value->id }}">
					    @csrf
					    @method('DELETE')
                        <input type="hidden" name="websiteId" value="{{ Crypt::encrypt($value->website_id) }}">
					</form> --}}
				  </td>
				</tr>
             @endforeach
         </tbody>
     </table>
  </div>
</div>
@endif
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
          searchPlaceholder: 'Search',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

    $("#website").on('change',function(){
        if($(this).val() != ''){
             window.location = location.origin+'/website/customer-reviews/'+$(this).val()+'/filter';
        }
    });
   
</script>
@endsection