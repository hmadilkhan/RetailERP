 @extends('layouts.master-layout')

@section('title','View Transfer Order')

@section('breadcrumtitle','View Tranfer')

@section('navtransfer','active')

@section('navtransferview','active')

@section('content')

<section class="panels-wells">

     <div class="row">
                  
                  <div class="col-xl-3 col-lg-6 grid-item">
                     <div class="card">
                        <div class="card-header">
                           <h5 class="card-header-text">Transfer Orders</h5>
                        </div>
                        <div class="row">
                           <div class="col-sm-12">
                      @if($gettransfer)
                       <?php $count = 0; ?>

					             @foreach($gettransfer as $value)
                      
                       <?php
                        $count++;
                       $val = $count % 2;
                       ?>
                              <div class="widget-timeline">
                                 <div class="media p-15 col-sm-12 {{($val > 0) ? 'abcd' : ''}}">

                                    <div class="media-body" >
                                       <h6 class="f-w-600 d-inline-block f-14">Transfer Order </h6>
                                       <span class="f-14"><b> | {{$value->transfer_No}} <b>
                                       	<a style="color: blue;" class="f-14" onclick="get_details('{{$value->transfer_id}}')">view details</a></span>
                                        <p class="m-b-5">Demand Order Number | <b>{{$demandid}}<b></p>
                                       <p class="m-b-5">{{$value->date}}</p>
                                        

                                    </div>
                                    <hr>

                                 </div>

                              </div>

                              @endforeach

                              @endif
                            

                           </div>
                        </div>



                     </div>
                  </div>
                  <!-- Notification Block end -->

                     <div id="dvdetails" style="display: none;" class="col-xl-9 grid-item">
                    <div class="card weather-live">
                        <div class="card-block bg-success">
                            <div class="m-b-5">
                                <h4  class=" d-inline-block">Transfer Order | </h4> <h4 id="toid" class=" d-inline-block"></h4>
                            </div>
                            <div class="row">
                <div class="col-lg-4" >

                    <div class="form-group" >
                     <label class="card-header-text" style="color: white;">From:</label> <br>
                      <label id="from" class="form-control-label"  style="color: white;"></label><br>
                      <label id="fromaddress" class="form-control-label"  style="color: white;"></label><br>
                    </div>
                </div>

                  <div class="col-lg-4 ">

                    <div class="form-group">
                       <label class="card-header-text"  style="color: white;">To:</label> <br>
                        <label id="to" class="form-control-label"  style="color: white;"></label><br>
                        <label id="toaddress" class="form-control-label"  style="color: white;"></label><br>
                    </div>
                </div>
                            </div>

                           
                                    
                                </div>
                                <div  class="card-block">
                                	 <table id="item_table" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

                                	
                                		<thead>
                                			<th>Item Code</th>
                                			<th>Product Name</th>
                                			<th>Product Qty.</th>
                                			<th>Status</th>
                                      <th>Action</th>
                                		</thead>
                                		<tbody>
                                			
                                		</tbody>
                                	</table>

                                    <br>
                                    <div class="button-group ">
{{--                                        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>--}}
{{--                                            Export to Excel Sheet--}}
{{--                                        </button>--}}
                                        <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
                                            Print Pdf
                                        </button>
                                    </div>
                                   
    							</div>

</div>
</div>

</div>
<div class="modal fade modal-flex" id="edit-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-sm" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Edit Quantity</h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group"> 
                                          <label class="form-control-label">Quantity</label>  
                                             <input type="number" placeholder="0" name="qty" id="qty" min="1" class="form-control" />
                                             <input type="hidden" id="transferid">
                                             <input type="hidden" id="toid">
                                            </div>
                                          </div>
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btn-edit" class="btn btn-success waves-effect waves-light" onclick="edit_transfer($('#qty').val(), $('#transferid').val(), $('#toid').val())">Update Quantity</button>
                                 </div>
                              </div>
                           </div>
                        </div> 

</section>

          
    @endsection
    @section('css_code')
    <style type="text/css">
      .abcd{
        background-color: #efefef;
      }
    </style>

    @endsection
    @section('scriptcode_three')
<script>

	function get_details(id){


		     $.ajax({
             url:"{{url('/transferordershow')}}",
             type:"POST",
             data:{_token:"{{csrf_token()}}",toid:id},
             success:function(resp){
   
      $('#dvdetails').css('display','Block');

             
				$('#toid').html(resp[0].transfer_No);
				$('#date').html(resp[0].date);
				$('#from').html(resp[0].branch_from);
        $('#fromaddress').html(resp[0].br_fr_address);
				$('#to').html(resp[0].branch_to);
        $('#toaddress').html(resp[0].br_to_address);
 				
 				$("#item_table tbody").empty();


				 $.each(resp,function(index,value){
				 	 $("#item_table tbody").append(
                          "<tr>" +
                            "<td class='pro-name'>"+value.item_code+"</td>" +
                            "<td>"+value.product_name+"</td>" +
                             "<td>"+value.qty+"</td>" +    
                             "<td>"+value.item_status+"</td>"+
                             "<td class='action-icon'><i class='icofont icofont-edit-alt text-default p-r-10 f-18' data-toggle='tooltip' data-placement='top' title='Edit' data-original-title='Edit' "+(value.item_status == "Completed" ? '' : "onclick='editmodal("+value.id+","+value.qty+","+id+")'")+"></i></td>" +
                             "</tr>"
                         );
				 	});
         }
         });
                                  
}

function editmodal(id,qty, transferid){
  $("#edit-modal").modal('show');
  $("#qty").val(qty);
  $("#transferid").val(id);
  $("#toid").val(transferid);
}

function edit_transfer(qty, id, transferid){
  $.ajax({
    url: "{{url('/edit_transfer')}}",
    type: 'PUT',
    data:{_token:"{{ csrf_token() }}",
    id:id,
    qty:qty,
  },
    success:function(resp){
        if(resp == 1){
             swal({
                    title: "Transfer Order",
                    text: "Transfer Order Updated Successfully!",
                    type: "success"
               });
              $("#edit-modal").modal('hide');
              get_details(transferid);
                     }
             }

          });        

}


    function generate_pdf()
    {
        window.location = "{{url('transferReport',$gettransfer[0]->transfer_id)}}";
    }

</script>
