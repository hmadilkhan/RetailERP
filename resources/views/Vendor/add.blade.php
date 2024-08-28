@extends('layouts.master-layout')

@section('title','Add Vendor Product')

@section('breadcrumtitle','Add Discount')

@section('navcustomer','active')
<style>
.select2-container{
    margin-top:-5px !important;
}
.select2-selection{
    padding:10px !important;
}
</style>

@section('content')
    <section class="panels-wells">
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text" style="width:100%">
                <span class="text-left">Add Vendor Products</span>
                <span class="" style="float:right;">Vendor Name:   {{isset($vendorProducts[0]->vendor_name)?$vendorProducts[0]->vendor_name:''}} </span>
                </h5>
                <h5 class=""><a href="{{route('vendors.index')}}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h5>
            </div>
            <div class="card-block">
                <form method="POST" action="{{url('save-vendor-product')}}">
                    @csrf
                <input type="hidden" name="vendor" value="{{$vendor}}">
                <div class="row">
                <div class="col-lg-9 col-md-9">
                <div class="form-group {{ $errors->has('product') ? 'has-danger' : '' }}">
                    <label class="form-control-label">Select Product</label>
                    <select id="product" name="product[]" class="js-data-example-ajax form-control select2" multiple></select>
                </div>
                </div>
                   
                    <button type="submit"  class="btn btn-md btn-success waves-effect waves-light m-t-25 f-left">
                        <i class="icofont icofont-plus "></i> &nbsp;Add Product
                    </button>
                </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-header-text">Vendor Products List</h5>
            </div>
            <div class="card-block">

                <ul class="nav nav-tabs md-tabs " role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="act" data-toggle="tab" href="#home7" role="tab">Active</a>
                        <div class="slide"></div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pro" data-toggle="tab" href="#profile7" role="tab">InActive</a>
                        <div class="slide"></div>
                    </li>
                </ul>
                <div class="tab-content m-t-10">
                    <div class="tab-pane active" id="home7" role="tabpanel">
                        <div class="project-table">
                            <table class="table table-striped nowrap dt-responsive" width="100%">
                                <thead>
                                <tr>
                                    <th>Product Image</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($vendorProducts)
                                    @foreach($vendorProducts as $value)
                                        @if($value->status == 1)
                                            <tr>
                                                <td class='text-center'><img src='{{asset('assets/images/products/'.($value->image == "" ? "placeholder.jpg" :  $value->image))}}' alt="{{$value->image}}"/></td>
                                                <td>{{$value->item_code}}</td>
                                                <td>{{$value->product_name}}</td>
                                                <td>
                                                    <i  onclick="item_inactive('{{$value->vendor_product_id}}')" class="icofont icofont-ui-delete text-danger f-18" data-id="{{ $value->vendor_product_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="IN-ACTIVE"></i>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="profile7" role="tabpanel">
                        <div class="project-table">
                            <table class="table table-striped nowrap dt-responsive" width="100%">
                                <thead>
                                <tr>
                                    <th>Product Image</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($vendorProducts)
                                    @foreach($vendorProducts as $value)
                                        @if($value->status == 2)
                                            <tr>
                                                <td class='text-center'><img src='{{asset('assets/images/products/'.($value->image == "" ? "placeholder.jpg" :  $value->image))}}' alt="{{$value->image}}"/></td>
                                                <td>{{$value->item_code}}</td>
                                                <td>{{$value->product_name}}</td>
                                                <td>
                                                    <i  onclick="item_active('{{$value->vendor_product_id}}')" class="icofont icofont-ui-check text-success f-18" data-id="{{ $value->vendor_product_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="RE-ACTIVE"></i>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('scriptcode_three')

    <script type="text/javascript">
$('.js-data-example-ajax').select2({
  ajax: {
    url: '{{route("search-vendor-product")}}',
    dataType: 'json',
    processResults: function (data) {
      // Transforms the top-level key of the response object from 'items' to 'results'
      return {
            results: $.map(data.items, function (item) {
                return {
                    text: item.tag_value,
                    id: item.tag_id
                }
            })
        };
    }
    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
  },
  placeholder: 'Search for a Product',
  minimumInputLength: 1,
}); 

        $('.table').DataTable({
            bLengthChange: true,
            displayLength: 10,
            info: true,
            language: {
                search:'',
                searchPlaceholder: 'Select Product',
                lengthMenu: '<span></span> _MENU_'
            }
        });

        function item_inactive(id){

            swal({
                    title: "Are you sure?",
                    text: "This item will be the not be available for this vendor !!!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Inactive it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{ url('inactive-vendor-product')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "In-Activated",
                                        text: "Product inactivated Successfully .",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('add-vendor-product',$vendor) }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });
        }

        function item_active(id){

            swal({
                    title: "Are you sure?",
                    text: "This item will be the not be available for this vendor !!!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Reactive it!",
                    cancelButtonText: "cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if(isConfirm){
                        $.ajax({
                            url: "{{ url('active-vendor-product')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",id:id},
                            success:function(resp){

                                if(resp == 1){
                                    swal({
                                        title: "Activated",
                                        text: "Product activated Successfully .",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('add-vendor-product',$vendor) }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your Item is safe :)", "error");
                    }
                });
        }
		$("#product").change(function(e){

			$.ajax({
				url: "{{ url('get-vendors-by-product')}}",
				type: 'POST',
				data:{_token:"{{ csrf_token() }}",id:$(this).val()},
				success:function(resp){
					console.log(resp)
					
				}

			});
		})
    </script>

@endsection