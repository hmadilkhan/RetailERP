@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','View Purchases')
@section('navVendorPO','active')
@section('navpurchase','active')
@section('nav_viewpurchase','active')

@section('content')

 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">GOOD RECEIVING NOTE DETAILS</h5>
         </div>      
       <!-- <div class="card-block"> -->
     		
  		<!-- </div> -->
	</div>


	<div class="row">
               <div class="col-xl-3 contact-user">
                  <div class="users">
                     <div class="card users-main contact-box ">
                        <div class="card-block user-box">
                          <!--  <div class="md-group-add-on p-relative md-float-material">
                              <span class="md-add-on">
                                     <i class="icofont icofont-search-alt-2"></i>
                                    </span>
                              <div class="md-input-wrapper">
                                 <input type="text" class="md-form-control" id="search-users">
                                 <label>Search..</label>

                              </div>
                           </div> -->
                           @if($grn)
                           	@foreach($grn as $value)
	                           <div class="user-box-users" style="display: pointer;" onclick="getDetails('{{$value->rec_id}}')">
	                              <div class="media">
	                                 <a class="media-left" href="#">
	                                    <img class="media-object  img-circle" src="{{ asset('public/assets/images/complete.png') }}" alt="Generic placeholder image">
	                                    <div class="live-status bg-danger"></div>
	                                 </a>
	                                 <div class="media-body">
	                                    <div class="users-header">{{$value->GRN}}</div>
	                                    <div>{{$value->created_at}}</div>
	                                 </div>
	                              </div>
	                           </div>
	                        @endforeach
                           @endif


                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-9 contact-detail">
                  <div class="card media-contactBox">
                     <div class="card-block ">
                        <table class="table table-striped full-width">
         <thead>
            <tr>
               <th>Preview</th>
               <th>Product Code</th>
               <th>Name</th>
               <th>Received Qty</th>
               <th>Remaining Qty</th>
               <th>Total Received Qty</th>
            </tr>
         </thead>
	         <tbody>
	        
	         </tbody>
                 </table>
             </div>
                <!-- end of contact-mobi-front -->
             </div>
                  </div>

               </div>

</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript" src="{{ URL::asset('public/js/purchase-script.js') }} "></script>
<script type="text/javascript">
	function getDetails(id)
	{
		    $.ajax({
                        url : "{{url('/DetailsOfGrn')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}",id:id},
                        beforeSend: function(){
                        	$(".table tbody").empty();
        					$(".table tbody").append('<tr><td colspan="4"><center><div class="preloader3 loader-block"><div class="circ1"></div><div class="circ2"></div><div class="circ3"></div><div class="circ4"></div></div></td></tr></center>');
    					},
                        success : function(result){
                        	$(".table tbody").empty();
                           var remainingQuantity = 0;
                           var totalQty = 0;
                            $.each(result, function( index, value ) {
                              totalQty = parseFloat(value.lastReceived) + parseFloat(value.qty_rec); 
                              remainingQuantity = parseFloat(value.quantity) - (totalQty); 
		                        $(".table tbody").append(
		                          "<tr>" +
		                          	"<td class='text-center'><img src='' class='d-inline-block img-circle ' alt=''></td>"+
		                            "<td>"+value.item_code +"</td>" +
		                            "<td>"+value.product_name +"</td>" +
		                            "<td>"+value.qty_rec+"</td>" +
                                  "<td>"+remainingQuantity+"</td>" +
                                  "<td>"+totalQty+"</td>" +
		                          "</tr>"
		                         );
                    		});
                            
                         }
                    });
	}
</script>
@endsection
