@extends('layouts.master-layout')

@section('title','Discount')

@section('breadcrumtitle','Add Expense')
@section('navdiscount','active')


@section('content')
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

/*img {
  height: 100%;

}*/

/*.insideDiv {
  width: 340px;
  height: 200px;
  background-color:transparent; 
  overflow-x: scroll;
  overflow-y: hidden;
  white-space: nowrap;
}
*/




</style>
<section class="panels-wells">

  <div class="card">
     <div class="card-header">
           <h5 class="card-header-text">Discount List</h5>
           <a href="{{url('/create-discount')}}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Create Discount
              </a>
        </div>
		<div class="col-md-12 m-b-2">
		<a href="{{($status == 2  ? url('/get-discount') :  url('/get-discount/2'))}}"> <div class="captions">{{($status == 2 ? 'Show Active Items' : 'Show In-Active Items')}}</div> </a>

                    </div>
        <div class="card-block">
		
     <table id="expensetb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               
               <th>ID</th>
               <th>Code</th>
               <th>Start Date</th>
               <th>Expiration Date</th>
               <th>Type</th>
               <th>Status</th>
               <th>Action</th>
               
            </tr>
         </thead>
         <tbody>
      		@if($discount)
      			@foreach($discount as $value)
      				<tr>
      					<td>{{$value->discount_id}}</td>
      					<td>{{$value->discount_code}}</td>
      					<td>{{$value->startdate.' '.$value->starttime}}</td>
      					<td>{{$value->enddate.' '.$value->endtime}}</td>
      					<td>{{$value->type_name}}</td>
      					<td>{{$value->name}}</td>
      					<td class="action-icon">

      							<a  class="p-r-10 f-18 text-primary" onclick="modelcall('{{ $value->discount_id }}')" title="" data-original-title="View"><i class="icofont icofont-eye-alt"></i></a>
                               
                                <a href="{{ url('/edit-discount') }}/{{ Crypt::encrypt($value->discount_id) }}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>
                                
                                <a class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="discountDelete('{{ $value->discount_id }}')" data-id="{{ $value->discount_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></a>

                             </td> 
      				</tr>
      			@endforeach
      		@endif
         	
     
         </tbody>
     </table>
    </div>
  </div>
</section>    


 <!--modal-->
                  <div class="modal fade modal-flex " id="sign-in-up" tabindex="-1" role="dialog">
                     <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                           <div class="sign-in-up">
                              <div class="sign-in-up-left"><i class="icofont icofont-sale-discount f-64" aria-hidden="true"></i>
                              <h1>Discount</h1>
                              	
                              </div>
                              <div class="sign-in-up-right">
                                 <h1 class="text-primary text-center">Dicount Details</h1>
                                 <hr class="primary-color" />
                                 <div class="row">
                                 	<div class="col-md-6">
                                 		<label class="f-16"> Code :</label>
                                 	</div>
                                 	<div class="col-md-6">
                                 		<label id="discount_code" class="f-16">Ramadan Offer</label>
                                 	</div>
                                 </div>
                                 <div class="row">
                                 	<div class="col-md-6">
                                 		<label class="f-16"> Type :</label>
                                 	</div>
                                 	<div class="col-md-6">
                                 		<label id="discount_type" class="f-16">By Percentage</label>
                                 	</div>
                                 </div>
                                 <div id="applyTo" class="row">
                                 	<div class="col-md-6">
                                 		<label class="f-16"> Applies To :</label>
                                 	</div>
                                 	<div class="col-md-6">
                                 		<label id="discount_applies" class="f-16">Specifc Collection</label>
                                 	</div>
                                 </div>
                                 <div class="row">
                                 	<div class="col-md-6">
                                 		<label class="f-16"> Starts at:</label>
                                 	</div>
                                 	<div class="col-md-6">
                                 		<label id="discount_starts" class="f-14">19-2-2020 4:15 PM</label>
                                 	</div>
                                 </div>
                                 <div class="row">
                                 	<div class="col-md-6">
                                 		<label class="f-16"> Expired at:</label>
                                 	</div>
                                 	<div class="col-md-6">
                                 		<label id="discount_ends" class="f-14">19-2-2020 4:15 PM</label>
                                 	</div>
                                 </div>

                                 <div id="main" class="row">
                                 	<div class="col-md-6">
                                 		<label class="f-16"> Status:</label>
                                 	</div>
                                 	<div class="col-md-6">
                                 		<label id="discount_status" class="f-16"><span id="discount_status_class" class="tag ">Active</span></label>
                                 	</div>
                                 </div>
                              <br/>
                                  <div class="container1" id="CatandPro" style="display: none;">
                                   <div class="inner">
                                    </div>
                                  </div>

                                  
                                  <div id="BuyandGet1" class="container2" style="display: none;">
                                  <h1 id="cust_buy_heading"></h1>

                                   <div id="buys" class="inner">
                                   </div>
                                 </div>
                                 

                                <div id="BuyandGet2" class="container2" style="display: none;">
                                  <h1 id="cust_get_heading"></h1>

                                   <div id="gets" class="inner">
                                   </div>
                                  </div>
                                </div>
                                 
                                 

                           </div>
                        </div>
                     </div>
                     <!-- end of modal fade -->
                  </div>
  
@endsection


@section('scriptcode_three')

  <script type="text/javascript" >
    
$(document).ready(function(){

$(".select2").select2();

   $('#expensetb').DataTable({
        displayLength: 50,
        info: false,
		"order" : [0,"DESC"],
        language: {
          search:'', 
          searchPlaceholder: 'Search Discount',
          lengthMenu: '<span></span> _MENU_'
   
        },
        
 
    });

});

function modelcall(id)
{
  $.ajax({
        url:'{{ url("get-discount-info") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",id:id},
        success:function(r){
          console.log(r[0].discount_code);
          $('#discount_code').html(r[0].discount_code);
          $('#discount_type').html(r[0].type_name);
          $('#discount_applies').html(r[0].applies_name);
          $('#discount_starts').html(r[0].starts);
          $('#discount_ends').html(r[0].ends);
          $('#discount_status_class').html(r[0].status);
          console.log(r[0].applies_name);
          if (r[0].applies_name == null) 
          {
            $('#applyTo').css('display','none');
          }

            if (r[0].status == "Active") 
            {
              $('#discount_status_class').addClass('tag-success')
            }
            else if (r[0].status == "In-Active")
            {
              $('#discount_status_class').addClass('tag-danger')
            }
            else
            {
              $('#discount_status_class').addClass('tag-warning')
            }
            //EMPTY DIVS AREA
            $('#fillDiv').empty();
            $('#buys').empty();
            $('#gets').empty();
            $('.inner').empty();
            
            //DISPLAY NONE
            $('#CatandPro').css('display','none');
            $('#BuyandGet1').css('display','none');
            $('#BuyandGet2').css('display','none');

            if (r[0].applies_name == "By Categories")
            {
              $('#CatandPro').css('display','block');
                  $.ajax({
                      url:'{{ url("get-discount-categories") }}',
                      type:"POST",
                      data:{_token : "{{csrf_token()}}",id:id},
                      success:function(r){
                          $('#fillDiv').empty();
                          $('.inner').empty();
                        $.each(r, function( index, value ) {
                            
                            
                            $('.inner').append(
                            
                                "<div class='floatLeft card thumb-block'>"+
                                 "<div class='thumb-img'>"+
                                    "<img src='{{asset('public/assets/images/task/task-u2.jpg')}}' width='190' height='170' class='tile'>"+
                                     "</div>"+
                                    "<div class='card-footer text-center'>"+
                                      "<label class='f-14'>"+ value.department_name+"</label>"+
                                   "</div></div>"
                              
                                           
                              );
                          });
                      }
                  });
            }
            else if(r[0].applies_name == "By Products")
            {
              $('#CatandPro').css('display','block');
                $.ajax({
                      url:'{{ url("get-discount-products") }}',
                      type:"POST",
                      data:{_token : "{{csrf_token()}}",id:id},
                      success:function(r){
                        $('.inner').empty();
                        $.each(r, function( index, value ) {
                            $('.inner').append(
                                "<div class='floatLeft card thumb-block'>"+
                                 "<div class='thumb-img'>"+
                                    "<img src='public/assets/images/products/"+value.image+"' width='190' height='170' class='tile '>"+
                                     "</div>"+
                                    "<div class='card-footer text-center'>"+
                                      "<label class='f-14'>"+ value.product_name+"</label>"+
                                   "</div></div>"       
                              );
                          });
                      }
                  });
            }
            else if(r[0].applies_name == "Entire Order")
            {
              $('#BuyandGet1').css('display','none');
              $('#BuyandGet2').css('display','none');
            }
            else
            {
              $('#BuyandGet1').css('display','block');
              $('#BuyandGet2').css('display','block');
               $.ajax({
                      url:'{{ url("get-customer-buys") }}',
                      type:"POST",
                      data:{_token : "{{csrf_token()}}",id:id},
                      success:function(r){
                        $('#cust_buy_heading').html("Customer Buy "+r[0].buy_qty+" Qty of Following")
                        $.each(r, function( index, value ) {
                            $('#buys').append(
                                "<div class='floatLeft1 card thumb-block'>"+
                                 "<div class='thumb-img'>"+
                                    "<img src='public/assets/images/products/"+value.image+"' width='150' height='120' class='tile '>"+
                                     "</div>"+
                                    "<div class='card-footer text-center'>"+
                                      "<label class='f-14'>"+ value.product_name+"</label>"+
                                   "</div></div>"       
                              );
                          });
                      }
                    });
                
                $.ajax({
                      url:'{{ url("get-customer-gets") }}',
                      type:"POST",
                      data:{_token : "{{csrf_token()}}",id:id},
                      success:function(r){
                          $('#cust_get_heading').html("Customer Gets "+r[0].get_qty+" Qty of Following")
                          $.each(r, function( index, value ) {
                            $('#gets').append(
                                "<div class='floatLeft1 card thumb-block'>"+
                                 "<div class='thumb-img'>"+
                                    "<img src='public/assets/images/products/"+value.image+"' width='150' height='150' class='tile '>"+
                                     "</div>"+
                                    "<div class='card-footer text-center'>"+
                                      "<label class='f-14'>"+ value.product_name+"</label>"+
                                   "</div></div>"       
                              );
                          });
                      }
                    });
            }
          }
      });
  $('#sign-in-up').modal("show");
}







  </script>
  
@endsection

@section('scriptcode_two')


<script type="text/javascript">

function swal_alert(title,msg,type,mode){

  swal({
        title: title,
        text: msg,
        type: type
     },function(isConfirm){
     if(isConfirm){
        if(mode==true){
          window.location = "{{ route('expense.index') }}";
        }
      }
  });
}


function resizeImg(imgId) {
    var img = document.getElementById(imgId);
    var $img = $(img);
    var maxWidth = 110;
    var maxHeight = 100;
    var width = img.width;
    var height = img.height;
    var aspectW = width / maxWidth;
    var aspectH = height / maxHeight;

    if (aspectW > 1 || aspectH > 1) {
        if (aspectW > aspectH) {
            $img.width(maxWidth);
            $img.height(height / aspectW);
        }
        else {
            $img.height(maxHeight);
            $img.width(width / aspectH);
        }
    }
}

function discountDelete(id){
	
            swal({
                    title: "Are you sure?",
                    text: "This campaign will be delete !",
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
                            url: "{{url('/remove-discount')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:id,mode:"delete"
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Deleted",
                                        text: "Campaign inactive successfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/get-discount') }}";
                                        }
                                    });
                                }
                            }

                        });

                    }else {
                        swal("Cancelled", "Your branch is safe :)", "error");
                    }
                });
}

</script>

@endsection

