@extends('layouts.master-layout')

@section('title','Purchase')

@section('breadcrumtitle','View Purchases')

@section('navpurchase','active')
@section('nav_viewpurchase','active')

@section('content')

 <section class="panels-wells">
 

  <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">DEPARTMENTS</h5>
         </div>      
       <!-- <div class="card-block"> -->
        
      <!-- </div> -->
  </div>

  <div class="row">
    <div class="col-xl-4 grid-item contact-user" >
       <div class="card">
                     <div class="card-header">
                        <h5 class="card-header-text">Product List</h5>
                     </div>
                     <div class="table-responsive " style="height: 550px;">
                        <div class="table-content">
                           <div class="project-table p-20">
                              <div class="md-group-add-on p-relative md-float-material">
                              <span class="md-add-on">
                                     <i class="icofont icofont-search-alt-2"></i>
                                    </span>
                              <div class="md-input-wrapper">
                                 <input type="text" class="md-form-control" id="search-users">
                                 <label>Search..</label>

                              </div>
                           </div>
                              <table id="productlist" class="table dt-responsive nowrap" width="100%" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l1.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6>Frock Designs</h6>
                                          <span>Lorem ipsum dolor </span>
                                       </td>
                                       <td>$456</td>
                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l6.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6> Style Tops </h6>
                                          <span>Interchargebla lens Digital Camera </span>
                                       </td>
                                       <td>$689</td>

                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l2.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6> Kurta Women </h6>
                                          <span>Lorem ipsum dolor </span>
                                       </td>
                                       <td>$755</td>

                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l3.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6> T Shirts For Women </h6>
                                          <span>Lorem ipsum dolor </span>
                                       </td>
                                       <td>$989</td>
                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l1.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6>Frock Designs</h6>
                                          <span>Lorem ipsum dolor </span>
                                       </td>
                                       <td>$456</td>

                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l6.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6> Style Tops </h6>
                                          <span>Interchargebla lens Digital Camera </span>
                                       </td>
                                       <td>$689</td>
                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l1.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6>Frock Designs</h6>
                                          <span>Lorem ipsum dolor sit </span>
                                       </td>
                                       <td>$456</td>

                                    </tr>
                                    <tr>
                                       <td class="img-pro">
                                          <img src="assets/images/e-commerce/product-list/pro-l6.png" class="img-fluid d-inline-block" alt="tbl">
                                       </td>
                                       <td class="pro-name">
                                          <h6> Style Tops </h6>
                                          <span>Interchargebla lens Digital Camera </span>
                                       </td>
                                       <td>$689</td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>

                     </div>
                  </div>          
    </div>

    <div class="col-xl-5 grid-item " style="height: 550px;">
       <div class="card">
        
          <div class="card-block">
             <table class="table table-striped full-width">
                         <thead>
                            <tr>
                               <th>Item Name</th>
                               <th>Unit Price</th>
                               <th>Qty</th>
                               <th>Amount</th>
                            </tr>
                         </thead>
                         <tbody>
                        
                         </tbody>
             </table>
          </div>
       </div>
    </div>

    <div class="col-xl-3 grid-item" style="height: 550px;">
       <div class="card">
             <div class="card-header">
                <h5 class="card-header-text">Order Bill</h5>
                <h5 class="card-header-text f-right">Receipt No: 123456</h5>
             </div>
          
        
          <div class="card-block">
            <div class="row">
               <div class="col-md-6"><label class="f-16 f-w-900"> Customer Name :</label></div>
               <div class="col-md-6"><label class="f-16  f-w-900 f-right">Walk-In-Customer</label></div>
            </div>
            <div class="row">
               <div class="col-md-6"><label class="f-16 f-w-900">Payment Mode :</label></div>
               <div class="col-md-6"><label class="f-16  f-w-900 f-right">Customer Credit</label></div>
            </div>
            <div class="row">
               <div class="col-md-6"><label class="f-16 f-w-500"> Date : {{date("Y-m-d")}}</label></div>
               <div class="col-md-6"><label class="f-16  f-w-500 f-right">Time : {{date("H:i:s")}}</label></div>
            </div>
              <table class="table table-striped full-width">
                         <thead>
                            <tr>
                               <th>Item Name</th>
                               <th>Unit Price</th>
                               <th>Qty</th>
                               <th>Amount</th>
                            </tr>
                         </thead>
                         <tbody>
                        
                         </tbody>
             </table>
             <hr/>
             <div class="row">
               <div class="col-md-6"><label> <h5>Total Amount</h5></label></div>
               <div class="col-md-6"><label class="f-right"><h5>15000</h5></label></div>
             </div>

             <div class="row">
               <div class="col-md-6"><label> <h5>Total Discount</h5></label></div>
               <div class="col-md-6"><label class="f-right"><h5>150</h5></label></div>
             </div>

             <div class="row">
               <div class="col-md-6"><label> <h5>Net Amount</h5></label></div>
               <div class="col-md-6"><label class="f-right"><h5>14850</h5></label></div>
             </div>
             
          </div>
       </div>
    </div>
  </div>

</section>
@endsection

@section('scriptcode_three')

<script type="text/javascript" src="{{ URL::asset('js/purchase-script.js') }} "></script>
<script type="text/javascript" src="{{ URL::asset('assets/pages/dashboard4.js')}}"></script>
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
                            $.each(result, function( index, value ) {
		                        $(".table tbody").append(
		                          "<tr>" +
		                          	"<td class='text-center'><img src='' class='d-inline-block img-circle ' alt=''></td>"+
		                            "<td>"+value.item_code +"</td>" +
		                            "<td>"+value.product_name +"</td>" +
		                            "<td>"+value.qty_rec+"</td>" +
		                          "</tr>"
		                         );
                    		});
                            
                         }
                    });
	}

  function getProducts()
  {
     $.ajax({
          url : "{{url('/get-inventory')}}",
          type : "POST",
          data : {_token : "{{csrf_token()}}"},
          success : function(result){
              $("#productlist tbody").empty();
                $.each(result, function( index, value ) {
                $("#productlist tbody").append(
                  "<tr>" +
                    "<td class='text-center'><img src='' class='d-inline-block img-circle ' alt=''></td>"+
                    "<td>"+value.item_code +"</td>" +
                    "<td>"+value.product_name +"</td>" +
                    "<td>"+value.qty_rec+"</td>" +
                  "</tr>"
                 );
                });
              }
          });
  }
</script>
@endsection
