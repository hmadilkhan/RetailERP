@extends('layouts.master-layout')

@section('title','Mobile App Promotion')

@section('breadcrumtitle','Promotion Details')

@section('navpromotion','active')

@section('content')

<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Mobile Promotion</h5>
          
         </div>     

       <div class="card-block">
	   <form method="post" class="form-horizontal" enctype="multipart/form-data" action="{{ route('insert-mobile-images') }}">
	   @csrf
			<div class="row">
				<div class="col-md-3">
               <label for="image" class="form-control-label">Image</label>
				
                    <div class="form-group {{ $errors->has('image') ? 'has-danger' : '' }}">
                                    <label for="image" class="custom-file">
                                                <input type="file" name="image[]" id="image" class="custom-file-input" multiple="5" required>
                                                <span class="custom-file-control"></span>
                                            </label>
                                @if ($errors->has('image'))
                                    <div class="form-control-feedback">{{ $errors->first('image') }}</div>
                                @endif  
                     </div>
			</div>
			<div class="col-lg-3 m-t-10">
			  <div class="form-group {{ $errors->has('country') ? 'has-danger' : '' }}">
			   <label class="form-control-label">Select Product</label>
			   <select class="select2"  data-placeholder="Select Product" id="product" name="product">
				<option value="">Select Product</option>
					@if($products)
					@foreach($products as $product)
						<option value="{{$product->id}}">{{$product->product_name}}</option>
					@endforeach
					@endif
			  </select>
				  @if ($errors->has('product'))
					<div class="form-control-feedback">Required field can not be blank.</div>
				  @endif
			</div>
			</div> 
			
			<div class="col-lg-3 m-t-10">
				 <div class="form-group {{ $errors->has('description') ? 'has-danger' : '' }}">
					<label class="form-control-label">Enter Description</label>
					 <input type="text" name="description" id="description" placeholder="Enter Description"   class="form-control" value="{{ old('description') }}"/>
				 </div>
			</div>
			
			<div class="col-md-3">
			   <label for="image" class="form-control-label"></label>
				 <div class="form-group row">
                <button class="btn btn-circle btn-primary f-left  m-t-10 m-l-20"  type="submit" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add Department"><i class="icofont icofont-plus" 
                  ></i>&nbsp; Save</button>.
              </div>
			  </div>

			</form>
			<div class="row">
				<div class="col-md-12">
					<div id="gallery" class="row gallery">
						@foreach($images as $value)
							<div class="img-wrap col-md-3  ">
								<img width=200 height=200 class="m-b-1" style="margin-left:10px;margin-top:20px;" src="{{ asset('public/assets/images/mobile/'.$value->image)}}"/>
								<center><label class="form-control-label m-b-1">{{$value->product_name}}</label></center>
								<center><label class="form-control-label m-b-1">{{$value->description}}</label></center>
								<center> <button onclick="deleteImage('{{$value->id}}','{{$value->image}}')" class="btn btn-danger btn-icon waves-effect waves-light m-t-10" type="button" ><i class="icofont icofont-ui-delete"></i></button></center>
							</div>
						@endforeach
					</div>
				</div>
			</div>
			
	   </div>
	 </div>
</section>
@endsection

@section('scriptcode_three')
  <script type="text/javascript">
	$(".select2").select2();
	var totallimit = 5;
	var usedlimit = "{{count($images)}}";
	var remaning_limit = totallimit - usedlimit;

	 $("#image").change(function() {
         // readURL(this,'simg');
         imagesPreview(this, 'div.gallery');
     });

     var imagesPreview = function(input, placeToInsertImagePreview) {

         if (input.files) {
             var filesAmount = input.files.length;
			 
			 if(filesAmount > remaning_limit){
				 alert("You can only upload "+ remaning_limit + (remaning_limit == 1 ? " image" : " images"));
				 $("#image").val(null);
			 }else{


             for (i = 0; i < filesAmount; i++) {
                 var reader = new FileReader();

                 reader.onload = function(event) {
                     $($.parseHTML('<img width=200 height=200 style="margin-left:20px;margin-top:20px;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                 }

                 reader.readAsDataURL(input.files[i]);
             }
			 }
         }

     };
	 
	 function deleteImage(id,image){
		 console.log(id)
		 console.log(image)
          swal({
                  title: "Are you sure?",
                  text: "Do you really want to delete this image?",
                  type: "warning",
                  showCancelButton: true,
                  confirmButtonClass: "btn-danger",
                  confirmButtonText: "delete it!",
                  cancelButtonText: "cancel plx!",
                  closeOnConfirm: false,
                  closeOnCancel: false
              },
              function(isConfirm){
                  if(isConfirm){
                      $.ajax({
                          url:'{{ url("/delete-mobile-image")}}',
                          data:{_token : "{{csrf_token()}}",id:id,image:image},
                          type:"POST",
                          success:function(result){
							  console.log(result)
							  if(result.status == 200){
								  swal("Success",  result.message, "success");
								  location.reload();
							  }else if(result.status != 200){
								  swal("Error", result.message, "error")
									// location.reload();
							  }
                          },error:function(err,res){
                              swal("Error", "Cannot delete image :)", "error")
                              // location.reload();
                          }
                      });


                  }else {
                      swal("Cancelled", "Your ٰimage is safe :)", "error");
                  }
              });
      }
 </script>

  @endsection