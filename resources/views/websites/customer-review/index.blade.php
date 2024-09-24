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
                   $statusName       = $value->status == 1 ? 'Active' : 'In-Active';
                   $statusLabelColor = $value->status == 1 ? 'text-success' : 'text-danger';
                   $website_name = $websites->where('id',$value->website_id)->pluck('name'); 
                      $image = asset('storage/images/no-image.jpg');
                      
                      if(File::exists('storage/images/customer-reviews/'.$value->image)){
                          $image = asset('storage/images/customer-reviews/'.$value->image);
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
          <td><span class="{{ $statusLabelColor }}">{{ $statusName }}</span></td>
				  <td class="action-icon">
            <label class="switch m-r-1">
              <input type="checkbox" title="" data-original-title="Active/In-Active Switch" 
              onclick="switchMode({{ $value->id }},{{ $value->status }},'{{ $value->customer_name }}',this)" {{ $value->status == 1 ? 'checked' : '' }}>
              <span class="slider round"></span>
              <form action="{{ route('activeInactiveCustomer_review') }}" method="POST" id="activeInactiveForm{{ $value->id }}">
                @csrf
                  <input type="hidden" name="id" value="{{ Crypt::encrypt($value->id) }}">
                  <input type="hidden" name="website" value="{{ Crypt::encrypt($value->website_id) }}"> 
                  <input type="hidden" name="stcode" value="{{ Crypt::encrypt($value->status) }}">             
              </form> 
            </label>					
					<i class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="removeReview({{ $value->id }},'{{ $value->customer_name }}','{{ $website_name }}')" data-id="{{ $value->id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
					<form action="{{ route('destroyCustomer_review',$value->id) }}" method="post" id="removeForm{{ $value->id }}">
					    @csrf
					    @method('DELETE')
                <input type="hidden" name="id" value="{{ Crypt::encrypt($value->id) }}">
                <input type="hidden" name="website" value="{{ Crypt::encrypt($value->website_id) }}">
					</form>
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

@section('css_code')
<style type="text/css">

.container1 {
  width: 480px; 
  height: 240px; 
  overflow-x: scroll;
  overflow-y: hidden;
}

.container2 {
  width: 480px; 
  height: 330px; 
  overflow-x: scroll;
  overflow-y: hidden;
}

.inner {
  height: 40px;
  white-space:nowrap; 
}

.floatLeft {
  width: 200px;
  height: 180px; 
  margin:10px 10px 50px 10px; 
  display: inline-block;
}

.floatLeft1 {
  width: 160px;
  height: 200px; 
  margin:10px 10px 50px 10px; 
  display: inline-block;
}

/* Hide default HTML checkbox */
.switch {
  position: relative;
  display: inline-block;
  width: 43px;
  height: 21px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 13px;
  width: 13px;
  left: 2px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
  /*content:'On';*/
}

input+.slider:before {
	/*content: "Off";*/
 }

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
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

function switchMode(id,status,customer,element){
 var status_name = null; 
 var value = 2;
    if($(element).is(':checked')){
        status_name = 'Active';
        value = 1;
    }else{
        status_name = 'In-Active';
        value = 2;
    }
    
    swal({
            title: "Are you sure?",
            text: "You want to "+status_name+" this "+customer+" customer review!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "yes plx!",
            cancelButtonText: "cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if(isConfirm){
                $("#activeInactiveForm"+id).submit();
            }else {
                swal("Cancelled", "Operation Cancelled:)", "error");
              if(status == 1){
                  $(element).prop('checked', true);
              }else{
                  $(element).prop('checked', false);
              }
            }
        });
}

function removeReview(id,customer,website){
  swal({
            title: "Are you sure?",
            text: "You want to remove this "+customer+" customer review from "+website+" website!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "yes plx!",
            cancelButtonText: "cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if(isConfirm){
                $("#removeForm"+id).submit(); 
            }else {
                swal("Cancelled", "Operation Cancelled:)", "error");
              if(status == 1){
                  $(element).prop('checked', true);
              }else{
                  $(element).prop('checked', false);
              }
            }
        });  
}
</script>
@endsection