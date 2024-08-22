@extends('layouts.master-layout')

@section('title','Edit Variations')

@section('breadcrumtitle','View Variations')
@section('navinventory','active')
@section('navinvent_variation','active')

@section('content')


    <section class="panels-wells">

         @if(Session::has('success'))
              <div class="alert alert-success">{{ Session::get('success') }}</div>
         @endif

         @if(Session::has('error'))
              <div class="alert alert-danger">{{ Session::get('error') }}</div>
         @endif


        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Edit Variation Product</h5>
            </div>
            <div class="card-block" id="insert-card">

                <form method="post" action="{{ route('updateVariation',$variatProd->id) }}" enctype="multipart/form-data">
                    @method('PATCH')
                    @csrf
                    <input type="hidden" name="variat_values_name" value="{{ old('variat_values_name') ? old('variat_values_name') : $variatProd->variation }}">
                    <div class="row">

                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('posproduct') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Product</label>
                                <select name="posproduct" id="posproduct" data-placeholder="Select Product" class="form-control select2"  >
                                    <option value="">Select Product</option>
                                    @php $oldProduct = old('posproduct') ? old('posproduct') : $variatProd->pos_variable_id @endphp
                                    @if($getposProduct)
                                        @foreach($getposProduct as $value)
                                            <option {{ $oldProduct == $value->pos_item_id ? 'selected' : null }} value="{{ $value->pos_item_id }}">{{ $value->product_name.' | '.$value->item_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('posproduct'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('variation') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Variation</label>
                                <select name="variation" id="variation" data-placeholder="Select Variation" class="form-control select2">
                                    <option value="">Select Variation</option>
                                    @php $oldVariat = old('variation') ? old('variation') : $variatProd->parent @endphp
                                    @if($variations)
                                        @foreach($variations as $value)
                                            <option {{ $oldVariat == $value->id ? 'selected' : null }} value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('variation'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('variat_values') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Select Variation Value</label>
                                <select name="variat_values" id="variat_values" data-placeholder="Select Variation Values" class="form-control select2" disabled>
                                    <option value="">Select Variation Values</option>
                                </select>
                                @if ($errors->has('variat_values'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif

                                @if ($errors->has('variation_id'))
                                    <div class="form-control-feedback">This variation values is already exists</div>
                                @endif                                
                            </div>
                        </div>

               </div>

              <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group {{ $errors->has('price') ? 'has-danger' : '' }} ">
                                <label class="form-control-label">Price</label>
                                <input type="text" name="price" id="price" class="form-control"  value="{{ old('price') ? old('price') : $variatProd->price }}" />
                                @if ($errors->has('price'))
                                    <div class="form-control-feedback">Required field can not be blank.</div>
                                @endif

                            </div>
                        </div>

                        <div class="col-lg-4 col-md-4">
                            <a href="#">
                                <img id="productimages" src="{{ route('imageVariatProduct',(empty($variatProd->image) ? 'no-image.png' : $variatProd->image )) }}" class="thumb-img img-fluid width-100" alt="img" style="width: 128px;height: 128px;">
                            </a>
                            <div class="form-group{{ $errors->has('productimage') ? 'has-danger' : '' }} ">
                                <label for="productimage" class="form-control-label">Product Image</label>
                                <br/>
                                <label for="productimage" class="custom-file">
                                    <input type="file" name="productimage" id="productimage" class="custom-file-input">
                                    <span class="custom-file-control"></span>
                                </label>

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-sm-12">
                        <div class="button-group ">
                             <button type="submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right">
                                Save Changes 
                            </button>
                            <a href="{{ route('listVariatProduct') }}" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-2" > <i class="icofont icofont-times"> </i>
                                Cancel 
                            </a>
                           
                        </div>
                    </div>


            </div>
            </form>
        </div>
</div>
   
   
</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript">
   
   var variat_values = [];

   $(".select2").select2();

    function readURL(input,id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#'+id).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#productimage").change(function() {
        readURL(this,'productimages');
    });

    $('#variation').on('change',function(){
        var id = $(this).val();
         if(id.length != 0){
                 getSubVariat(id,'');
         }
    })


    function getSubVariat(id,slted_val){

            $.ajax({
                     url:'{{ route("getVariat_values") }}',
                     type:'GET',
                     data:{id:id},
                     dataType:'json',
                     async:true,
                     success:function(resp,status){
                       
                        if(status == 'success'){
                           $("#variat_values").empty();
                           $('#variat_values').attr('disabled',false);
                           $.each(resp,function(i,v){
                               if(v.parent == id){
                                  var slectedVal = v.id == slted_val ? 'selected' : null;
                                   $("#variat_values").append('<option '+slectedVal+' value="'+v.id+'">'+v.name+'</option>');
                               }

                           })
                        }
                        
                     },error:function(r,s){

                     }
            });

    }

     $('#variat_values').on('change',function(){
        $('input[name="variat_values_name"]').val($("#variat_values option:selected").text());  
    }) 


    @if($oldVariat)
         getSubVariat('{{ $oldVariat }}','{{ $variatProd->variation_id }}');
    @endif  
</script>

@endsection
