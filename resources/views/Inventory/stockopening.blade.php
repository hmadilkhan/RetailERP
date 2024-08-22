@extends('layouts.master-layout')

@section('title','Stock Opening')

@section('breadcrumtitle','stock_opening')
@section('navinventory','active')
@section('stock_opening','active')

@section('content')

  <section class="panels-wells">

      <div class="card">
          <div class="card-header">
              <h5 class="card-header-text">Upload Inventory {{session("message")}}</h5>


              <button id="downloadsample" onclick="window.location.href='{{url('getcsv')}}';" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Download Sample" class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-plus m-r-5" ></i> Download Sample</button>

          </div>
          <div class="card-block">
              <form method='post' action='{{url('uploadStockOpening')}}' enctype='multipart/form-data' >
                  {{ csrf_field() }}
                  <div class="row col-md-4 " >


                      <div class="form-group {{ $errors->has('file') ? 'has-danger' : '' }} ">
                          <label for="vdimg" class="form-control-label">Select File </label>
                          <br/>
                          <label for="vdimg" class="custom-file">
                              <input type="file" name="file" id="vdimg" class="custom-file-input">
                              <span class="custom-file-control"></span>
                          </label>
                          @if ($errors->has('file'))
                              <div class="form-control-feedback">Required field can not be blank.</div>
                          @endif

                      </div>


                  </div>
                  <div class="row col-md-2 " >
                      <input type='submit' class="btn btn-primary m-l-5 m-t-35" name='submit' value='Import'>
                  </div>
              </form>
          </div>
      </div>

               <div class="card">
                  <div class="card-header">
                      <h5 class="card-header-text">Inventory Stock Opening</h5>

                  </div>
                  <div class="card-block">

        <form method="POST" class="form-horizontal" enctype="multipart/form-data" action="{{url('insert-stock-opening')}}">
          @csrf

                         <div class="form-control-feedback text-danger">{{Session::get('status').$status}}</div>


            <div class="row">

              <div class="col-lg-4 col-md-4">
                    <div class="form-group {{ $errors->has('branch') ? 'has-danger' : '' }}">
                        <label class="form-control-label">Branch</label>
                        <select class="form-control  select2" data-placeholder="Select Branch" id="branch" name="branch">
                            <option value="">Select Branch</option>
                            @if($branches)
                                @foreach($branches as $val)
									@if( old('branch') == $val->branch_id)
										<option  selected="selected" value="{{$val->branch_id}}">{{$val->branch_name}}</option>
									@else
											 <option  value="{{$val->branch_id}}">{{$val->branch_name}}</option>
										@endif
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('branch'))
                            <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                    </div>
                </div>

              <div class="col-lg-4 col-md-4">
                <div class="form-group {{ $errors->has('product') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Product</label>
                        <select class="form-control  select2" data-placeholder="Select Product" id="product" name="product">
                           <option value="">Select Product</option>
                           @if($product)
                                  @foreach($product as $val)
										@if( old('product') == $val->id)
												<option  selected="selected" value="{{$val->id}}">{{$val->department_name." | ".$val->item_code." | ".$val->product_name}}</option>
										 @else
											 <option  value="{{$val->id}}">{{$val->department_name." | ".$val->item_code." | ".$val->product_name}}</option>
										@endif
                                  @endforeach
                           @endif
                        </select>
                       @if ($errors->has('product'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>

              <div class="col-lg-4 col-md-4">
                <div class="form-group {{ $errors->has('uom') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Unit Of Measure</label>
                        <select  class="form-control  select2" data-placeholder="Select Unit Of Measure" id="uom" name="uom">
                           <option value="">Select Unit Of Measure</option>
                           @if($uom)
                                  @foreach($uom as $val)
                                    @if( old('uom') == $val->uom_id)
                                      <option selected="selected" value="{{$val->uom_id}}">{{$val->name}}</option>
                                    @else
                                      <option value="{{$val->uom_id}}">{{$val->name}}</option>
                                    @endif
                                  @endforeach
                           @endif
                        </select>
                       @if ($errors->has('uom'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>

               <div class="col-lg-3 col-md-3">
                <div class="form-group {{ $errors->has('qty') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Stock Qty</label>
                        <input type="text"  name="qty" id="qty" class="form-control"  />
                       @if ($errors->has('qty'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>

               <div class="col-lg-3 col-md-3">
                <div class="form-group {{ $errors->has('cp') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Item Cost Price</label>
                        <input type="text"  name="cp" id="cp" class="form-control" />
                       @if ($errors->has('cp'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>

               <div class="col-lg-4 col-md-4" style="display: none;">
                <div class="form-group {{ $errors->has('rp') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Retail Price</label>
                        <input type="text"  name="rp" id="rp" class="form-control" value="0" />
                       @if ($errors->has('rp'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>

               <div class="col-lg-4 col-md-4" style="display: none;">
                <div class="form-group {{ $errors->has('wp') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Wholesale Price</label>
                        <input type="text"  name="wp" id="wp" class="form-control"  value="0" />
                       @if ($errors->has('wp'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>


               <div class="col-lg-4 col-md-4" style="display: none;">
                <div class="form-group {{ $errors->has('dp') ? 'has-danger' : '' }}">
                      <label class="form-control-label">Discount Price</label>
                        <input type="text"  name="dp" id="dp" class="form-control" value="0" />
                       @if ($errors->has('dp'))
                          <div class="form-control-feedback">Required field can not be blank.</div>
                        @endif
                </div>
              </div>

              <div class="col-lg-4 col-md-4">
                <div class="form-group ">
                      <label class="form-control-label"></label>
                       <button class="btn btn-md btn-circle btn-primary m-t-20" type="submit"><i class="icofont icofont-plus"></i>&nbsp;Submit</button>
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
    $(".select2").select2();

    $('#product').change(function(){
      $.ajax({
            url: "{{url('/get-uom-id')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            id:$('#product').val(),
          },
            success:function(resp){
              console.log(resp[0].uom_id);
              $('#uom').val(resp[0].uom_id).change();
            }
      });

    });

    // $('#btnSubmit').click(function(){
    //   $.ajax({
    //         url: "{{url('/insert-stock-opening')}}",
    //         type: 'POST',
    //         data:{_token:"{{ csrf_token() }}",
    //         dateType : 'json',
    //         product:$('#product').val(),
    //         uom:$('#uom').val(),
    //         cp:$('#cp').val(),
    //         rp:$('#rp').val(),
    //         wp:$('#wp').val(),
    //         dp:$('#dp').val(),
    //         qty:$('#qty').val(),
    //       },
    //         success:function(resp){
    //           console.log(resp);
    //         }
    //   });
    // });


 </script>

@endsection



