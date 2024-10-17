@extends('layouts.master-layout')

@section('title','Orders')

@section('breadcrumtitle','Orders Panel')

@section('navbranchoperation','active')
@section('navorder','active')

@section('content')


<div class="p-t-3">
    <div class="row bg-transparent m-2 p-2" style="padding-top:-3.9rem;">
		<div class="col-xl-12">
		<div class="card border shadow-none">
			<div class="card-body">
				<div class="col-xl-4 ">
				<div class="  d-flex align-items-start  pb-2">
					<div class="  bg-transparent py-2 px-4">
                        <h5 class="font-size-24 text-sm-end text-muted mb-5">Date Time : {{ $orders->dateTime }}</h5>
						<label class="label {{ Custom_Helper::getColorName($orders->order_status) }} font-size-28 border border-white rounded my-5 mt-3">

                            @if($orders->order_status == "Ready for Delivery")
							  Ready
						    @elseif($orders->order_status == "Dispatch")
							  Dispatch from workshop
						    @elseif($orders->order_status == "Order Picked Up")
							  Picked Up By Branch
						    @else
							  {{ $orders->order_status }}
						    @endif
						</label>
                    </div>
				</div>
				</div>
				<div class="col-xl-4 ">
					 <h3 class="font-size-24 text-center mb-4">Branch #</h3>
					 <h1 class="fs-1 text-center mb-4">{{$orders->branch}}</h1>
					 <h3 class="font-size-24 text-center mb-2">Order ID : {{$orders->url_orderid}}</h3>
                     <h4 class="text-center mb-2">Receipt No : {{$orders->receipt_no}}</h3>
				</div>
				<div class="col-xl-4">
				<div class="flex-shrink-0 items-center ms-2 py-2 ">
					<div class="text-sm-end mt-2 mt-sm-0 float-end">
						@if(session("roleId") == 19 or session("roleId") == 2)
							@if($orders->order_status == "Pending")
							<a href="{{url('/sales/change-order-status-from-website')}}/{{$orders->id}}/2/{{$orders->url_orderid}}" class="btn btn-warning text-white  ml-2">
							<i class="mdi mdi-cart-outline me-1"></i> Processing </a>
							@elseif($orders->order_status == "Processing")
							<a href="{{url('/sales/change-order-status-from-website')}}/{{$orders->id}}/3/{{$orders->url_orderid}}" class="btn btn-primary  ml-2">
							<i class="mdi mdi-cart-outline me-1"></i> Ready </a>
							@elseif($orders->order_status == "Ready for Delivery")
							<a href="{{url('/sales/change-order-status-from-website')}}/{{ $orders->id }}/6/{{$orders->url_orderid}}" class="btn btn-success  ml-2">
							<i class="mdi mdi-cart-outline me-1"></i> Dispatch  </a>
							@endif
						@endif
						@if($orders->order_statusId > 6)
<!-- 							<a href="{{--url('voucher')--}}/{{-- $orders->id --}}" class="btn btn-danger  ml-2" target="_blank">
							<i class="mdi mdi-cloud-print me-1"></i> Print </a> -->
						@endif
						<a href="{{url('web-orders-view')}}" class="btn btn-default text-muted">
                        <i class="mdi mdi-arrow-left me-1"></i> Back to Orders </a>
					</div>
				</div>
				</div>
			</div>
			</div>
		</div>
        <div class="col-xl-8">
             @if(Auth::user()->username == 'uzair.velveteen')
                    <?php //print_r($orders->products) ?>
             @endif
           <?php //print_r($orders->products) ?>

         @if($orders->website_type != 'restaurant')
            @foreach($orders->products as $key => $item)

            <div class="card border shadow-none">
                            <div class="card-body">
                                <div class="d-flex align-items-start border-bottom pb-3">
                                    <div class="me-4">
                                        {{-- @if(session('company_id') == 102)
                                          @php  $imageShow = asset('storage/images/placeholder.jpg') @endphp
                                         @if($item->image != '')
                                          @php
                                              $getImage_id = $item->image;
                                              $getExtension = pathinfo($getImage_id,PATHINFO_EXTENSION);

                                                $extensionCount =  0;
                                                if(!empty($getExtension)){
                                                    if(substr_count($item->url, $getExtension) != substr_count($getImage_id, $getExtension) ){
                                                     $extensionCount = $item->url != '' ? substr_count($item->url, $getExtension) : 0;
                                                    }
                                                }

                                                if(!Str::contains($item->image,$orders->company_name)){
                                                    $getImage_id = $orders->company_name.'/'.$item->image;
                                                    // if($extensionCount > 1){
                                                    //     $getImage_id .= '.'.$getExtension;
                                                    // }
                                                }
                                             $imageShow = !empty(Cloudinary::getUrl($getImage_id)) ? 'https://res.cloudinary.com/dl2e24m08/image/upload/f_webp,q_auto/'.$getImage_id.($extensionCount > 1 ? '.'.$getExtension : '') : asset('storage/images/placeholder.jpg')
                                            @endphp
                                         @endif
                                        <a href="{{ $imageShow }}" data-fancybox data-caption="Single Image">
                                         <img src="{{ $imageShow }}" alt="" class="avatar-lg rounded">
                                        </a>
                                         @else --}}
                                         @php $image = asset('storage/images/no-image.png') @endphp
                                        @if(File::exists('storage/images/products/'.$item->image))
                                        @php $image = route('imageOptimize',$item->image) @endphp
                                        @endif
                                        <a href="{{ $image }}" data-fancybox data-caption="Single Image">
                                         <img src="{{ $image }}" alt="" class="avatar-lg rounded productImage{{ $key }} " style="cursor:pointer;" onclick="showImage('{{ $key }}')">
                                        </a>
                                         {{-- @endif --}}
                                        </div>
                                    <div class="flex-grow-1 align-self-center overflow-hidden">
                                        <div>
                                            <!-- text-truncate  -->
                                            <h3><a href="#" class="code{{ $key }} text-dark fw-bold">({{ $item->item_code }}) </a> <a href="#" class="name{{ $key }} text-dark font-size-24">
                                                {{ $item->product_name }}
                                            {{-- @if($item->prod_variation != null)
                                                 ({{ $item->prod_variation->variable_name }})
                                            @endif --}}
                                            </a></h3>
                                        </div>
                                    </div>
                                </div>



                                        @if($item->prod_variation != null)
                                        <br>
                                       <div class="">
                                          @foreach($item->prod_variation as $variation)
                                            <div>
                                                <h5 class="text-muted font-size-18">{{ $variation->name }}</h5>
                                               @if(count($variation->values) > 0)
                                                @foreach($variation->values as $variation_val)
                                                    <strong class="m-l-2">{{ $variation_val->variate_name }}</strong>
                                                    @foreach($variation_val->variation as $sbvariat_val)
                                                    <h5 class="text-muted font-size-18">{{ $sbvariat_val->name }}</h5>
                                                      @foreach($sbvariat_val->values as $sbvariatval_val)
                                                      <strong class="m-l-2">{{ $sbvariatval_val->name }}</strong>
                                                      @endforeach
                                                    @endforeach
                                                @endforeach
                                               @endif
                                            </div>
                                          @endforeach
                                          </div>
                                          <hr/>
                                        @endif

                                <div>
                                    <div class="row">

                                        <div class="col-md-4">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Price</p>
                                                @if($item->discount_value != 0)
                                                <h5 class="mb-0 mt-2"><del>{{session("currency")." ".number_format($item->actual_price,0) }}</del></h5>
                                                @endif
                                                <h5 class="mb-0 mt-2">{{session("currency")." ".number_format($item->item_price,0) }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Quantity</p>
                                                <div class="d-inline-flex">
                                                        <h5>{{ number_format($item->total_qty,0) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Total</p>
                                                <h5>{{ session("currency")." ".(number_format($item->webcart_amount,0)) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- end card -->

                      @endforeach

            @else
            @foreach($orders->products as $key => $item)

            <div class="card border shadow-none">
                            <div class="card-body">
                                <div class="d-flex align-items-start border-bottom pb-3">
                                    <div class="me-4">
                                        {{-- @if(session('company_id') == 102)
                                          @php  $imageShow = asset('storage/images/placeholder.jpg') @endphp
                                         @if($item->image != '')
                                          @php
                                              $getImage_id = $item->image;
                                              $getExtension = pathinfo($getImage_id,PATHINFO_EXTENSION);

                                                $extensionCount =  0;
                                                if(!empty($getExtension)){
                                                    if(substr_count($item->url, $getExtension) != substr_count($getImage_id, $getExtension) ){
                                                     $extensionCount = $item->url != '' ? substr_count($item->url, $getExtension) : 0;
                                                    }
                                                }

                                                if(!Str::contains($item->image,$orders->company_name)){
                                                    $getImage_id = $orders->company_name.'/'.$item->image;
                                                    // if($extensionCount > 1){
                                                    //     $getImage_id .= '.'.$getExtension;
                                                    // }
                                                }
                                             $imageShow = !empty(Cloudinary::getUrl($getImage_id)) ? 'https://res.cloudinary.com/dl2e24m08/image/upload/f_webp,q_auto/'.$getImage_id.($extensionCount > 1 ? '.'.$getExtension : '') : asset('storage/images/placeholder.jpg')
                                            @endphp
                                         @endif
                                        <a href="{{ $imageShow }}" data-fancybox data-caption="Single Image">
                                         <img src="{{ $imageShow }}" alt="" class="avatar-lg rounded">
                                        </a>
                                         @else --}}
                                         @php $image = asset('storage/images/no-image.png') @endphp
                                        @if(File::exists('storage/images/products/'.$item->image))
                                        @php $image = route('imageOptimize',$item->image) @endphp
                                        @endif
                                        <a href="{{ $image }}" data-fancybox data-caption="Single Image">
                                         <img src="{{ $image }}" alt="" class="avatar-lg rounded productImage{{ $key }} " style="cursor:pointer;" onclick="showImage('{{ $key }}')">
                                        </a>
                                         {{-- @endif --}}
                                        </div>
                                    <div class="flex-grow-1 align-self-center overflow-hidden">
                                        <div>
                                            <!-- text-truncate  -->
                                            <h3><a href="#" class="code{{ $key }} text-dark fw-bold">({{ $item->item_code }}) </a> <a href="#" class="name{{ $key }} text-dark font-size-24">
                                                {{ $item->product_name }}
                                            {{-- @if($item->prod_variation != null)
                                                 ({{ $item->prod_variation->variable_name }})
                                            @endif --}}
                                            </a></h3>
                                        </div>
                                    </div>
                                </div>



                                        {{-- @if($item->prod_variation != null)
                                        <br>
                                       <div class="">
                                           <h5 class="font-size-18">Variation</h5>
                                          @foreach($item->prod_variation->variation as $variation)
                                            <div>
                                                <h5 class="text-muted font-size-18">{{ $variation->name }}</h5>
                                               @if(count($variation->values) > 0)
                                                 <table class="table">
                                                @foreach($variation->values as $variation_val)
                                                  <tr>
                                                    <th>{{ $variation_val->name }}</th>
                                                    <td>{{ $variation_val->price }}</td>
                                                  </tr>
                                                @endforeach
                                                </table>
                                               @endif
                                            </div>
                                          @endforeach
                                          </div>
                                        @endif --}}


                                        @if(count($item->prod_addons) > 0)
                                        <br>
                                       <div class="">
                                           <h5 class="font-size-18">Addon</h5>
                                          @foreach($item->prod_addons as $addon)
                                            <div>
                                                <h5 class="text-muted font-size-18">{{ $addon->name }}</h5>
                                               @if(count($addon->values) > 0)
                                                 <table class="table">
                                                @foreach($addon->values as $addon_val)
                                                  <tr>
                                                    <th>{{ $addon_val->name }}</th>
                                                    <td>{{ $addon_val->price }}</td>
                                                  </tr>
                                                @endforeach
                                                </table>
                                               @endif
                                            </div>
                                          @endforeach
                                          </div>
                                        @endif
                                <div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Price</p>
                                                @if($item->discount_value != 0)
                                                <h5 class="mb-0 mt-2"><del>{{session("currency")." ".number_format($item->actual_price,0) }}</del></h5>
                                                @endif
                                                <h5 class="mb-0 mt-2">{{session("currency")." ".number_format($item->item_price,0) }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Quantity</p>
                                                <div class="d-inline-flex">
                                                        <h5>{{ number_format($item->total_qty,0) }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mt-3">
                                                <p class="text-muted mb-2">Total</p>
                                                <h5>{{ session("currency")." ".(number_format($item->webcart_amount,0)) }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- end card -->

                      @endforeach

            @endif


        </div>

        <div class="col-xl-4">
            <div class="mt-5 mt-lg-0">
                <div class="card border shadow-none">
                    <div class="card-header bg-transparent border-bottom py-3 px-4">
                        <h5 class="font-size-16 mb-0">Order Details <span class="float-end">#{{$orders->url_orderid}}</span></h5>
                    </div>
                    <div class="card-body p-4 pt-2">

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
									<tr class="bg-light">
                                        <th>Website :</th>
                                        <td class="text-end">
                                            <span class="fw-bold">
                                                {{ $orders->website_name }}
                                            </span>
                                        </td>
                                    </tr>
									<tr class="bg-light">
                                        <th>Branch :</th>
                                        <td class="text-end">
                                            <span class="fw-bold">
                                                {{ $orders->branch }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Receipt Number :</td>
                                        <td class="text-end">{{ $orders->receipt_no }}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer : </td>
                                        <td class="text-end">{{($orders->customer->Name)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Contact Number :</td>
                                        <td class="text-end">{{($orders->customer->Mobile)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Land Mark :</td>
                                        <td class="text-end">{{($orders->customer->LandMark)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Address :</td>
                                        <td class="text-end">{{($orders->customer->Address)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                </div>
            </div>
            <div class="mt-5 mt-lg-0">
                <div class="card border shadow-none">
                    <div class="card-header bg-transparent border-bottom py-3 px-4">
                        <h5 class="font-size-16 mb-0">Delivery Detail</h5>
                    </div>
                    <div class="card-body p-4 pt-2">

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>

                                    <tr>
                                        <td>Area Name :</td>
                                        <td class="text-end">{{($orders->delivery_area_name)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Instructions :</td>
                                        <td class="text-end">{{($orders->delivery_instructions)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                </div>
            </div>
			<div class="mt-5 mt-lg-0">
                <div class="card border shadow-none">
                    <div class="card-header bg-transparent border-bottom py-3 px-4">
                        <h5 class="font-size-16 mb-0">Order Summary </h5>
                    </div>
                    <div class="card-body p-4 pt-2">

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>Sub Total :</td>
                                        <td class="text-end">{{session("currency")." ".($orders->sub_total)}}</td>
                                    </tr>
                                  @if($orders->discount_amount != 0)
                                    <tr>
                                        <td>Discount : </td>
                                        <td class="text-end">- {{ session("currency")." ".($orders->discount_amount).' ('.$orders->discount_percentage.'%)' }}</td>
                                    </tr>
                                  @endif
                                    <tr>
                                        <td>Delivery Charge :</td>
                                        <td class="text-end">{{ session("currency")." ".($orders->delivery_charges) }}</td>
                                    </tr>

                                    <tr class="bg-light">
                                        <td>Total Amount :</td>
                                        <td class="text-end">{{ session("currency")." ".($orders->total_amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- end row -->
<div class="modal fade in" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" data-dismiss="modal">
    <div class="modal-content"  >
		<div class="modal-header">
        <h5 id="modalTitle" class="modal-title">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img src="" class="imagepreview mt-2" style="width: 100%;" >
		<p class="text-left mt-2  fs-5"><span class="fw-bold">Comments : </span><span id="modalComments" class=" fs-5"></span></p>
      </div>
    </div>
  </div>
</div>
</div>

	 <div class="modal fade modal-flex" id="order-status-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 id="mod-title" class="modal-title">Select Rider</h4>
                </div>
                <div class="modal-body">
				<input type="hidden" id="modalreceipt" />
				<input type="hidden" id="modalreceiptno" />
				<input type="hidden" id="modalstatus" />
					 <select id="rider" class="form-control select2" dataplaceholder="Select Rider"  >
						{{--@foreach($riders as $rider)--}}
							<option  value="{{--$rider->id--}}">{{--$rider->provider_name--}}</option>
						{{--@endforeach--}}
					</select>
                </div>
                <div class="modal-footer">
                        <button type="button" id="btn_extra_item" class="btn btn-success waves-effect waves-light">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css_code')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css" integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css"
/>

<style>
body{
    margin-top:-3.5rem;
    background-color: #f1f3f7;
}

.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgb(76 175 80) !important;
}

.avatar-lg {
    height: 5rem;
    width: 5rem;
}

.font-size-18 {
    font-size: 18px!important;
}

.font-size-24 {
    font-size: 24px!important;
}

.font-size-28 {
    font-size: 28px!important;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

a {
    text-decoration: none!important;
}

.w-xl {
    min-width: 160px;
}

.card {
    margin-bottom: 24px;
    -webkit-box-shadow: 0 2px 3px #e4e8f0;
    box-shadow: 0 2px 3px #e4e8f0;
}

.card {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #eff0f2;
    border-radius: 1rem;
}
</style>
@endsection

@section("scriptcode_three")

<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

<script>
	function showImage(key){
			let image = $(".productImage"+key).attr('src');
			let comments = $(".comments"+key).text();
			let name = $(".name"+key).text();
			let code = $(".code"+key).text();

			$('.imagepreview').attr('src', image);
			$('#modalComments').html(comments);
			$('#modalTitle').html(code + " " + name);
			$('#imagemodal').modal('show');
		}

        function statusChange(id,receipt,receiptNo){

			// This is to check if the order status is dispatch i,e 6 than display modal to select rider
			if($('#'+id).val() == 6){
				$('#modalreceipt').val(receipt);
				$('#modalreceiptno').val(receiptNo);
				$('#modalstatus').val($('#'+id).val() );
				$('#order-status-modal').modal("show");
			}else{
				statusChangeFromDB(receipt,$('#'+id).val(),receiptNo,0)
			}

        }

		function statusChangeFromDB(receipt,status,receiptNo,rider)
		{
			 $.ajax({
                url: "{{--url('/change-order-status')--}}",
                type: 'POST',
                data:{_token:"{{ csrf_token() }}",
                    receipt:receipt,
                    status : status,
					rider:rider
                },
                success:function(result){
					orderSeen(receiptNo,receipt)
					$('#order-status-modal').modal("hide");
                    swal_alert("Success","Status changed successfully","success","false")
                }
            });
		}

		async function orderSeen(ReceiptNo,tbrowId){
			  $.ajax({
					url : "{{url('/order-seen')}}",
					type : "POST",
					data : {_token : "{{csrf_token()}}", receiptNo:ReceiptNo},
					dataType : 'json',
					success : function(resp){
						if(resp.status == true){
						       if($("#tbRow"+tbrowId).hasClass('bg-primary')){
                                    $("#tbRow"+tbrowId).removeClass('bg-primary');
                               }
						}
					}
				});
		}

</script>
@endsection
