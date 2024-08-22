 @extends('layouts.master-layout')

@section('title','Deliviery Challan')

@section('breadcrumtitle','Deliviery Challan')

@section('navtransfer','active')

@section('navtransferview','active')

@section('content')
   <div class="card">
              
                       <div class="card-header">
                <h3 class="card-header-text">Create Delivery Challan
                 </h3><br>
                  <a href="{{ url('/transferlist') }}" id="btnback" name="btnback"><i class="icofont icofont-arrow-left"></i>Back to List
                                    </a>
                   
                     </div>

                  
                  
                  <div class="card-block">
                     <div class="row invoive-info">
                        <div class="col-md-4 col-xs-12 invoice-client-info">
                        <h6>From :</h6>
                        <h6 >Administrator</h6>
                        <p>Head Office</p>
                        <p>Park Avenue, Groud Floor, Shahrah-e-Faisal, Malir</p>
                        </div>

                        <div class="col-md-4 col-sm-6">
                        <h6>TO:</h6>
                        <h6 >Branch Manager</h6>
                        <p >{{$getdetails[0]->branch_from}}</p>
                        <p >{{$getdetails[0]->br_fr_address}}</p>
                        </div>
                        <div class="col-md-4 col-sm-6">
                           <h6 class="m-b-20">Transfer Order | <span id='transferno'>{{ $getdetails == 0 ? '' : $getdetails[0]->transfer_No }}</span></h6>

                           <h6 class="text-uppercase txt-info">Created on :
                                    <span>{{ $getdetails == 0 ? '' : $getdetails[0]->date }}</span>
                                </h6>
                                

                               
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-sm-12">
                           <table class="table invoice-detail-table">
                              <thead>
                                 <tr class="thead-default">

                                    <th>Item Code</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Cost Price </th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @if($getdetails) 
                                 @foreach($getdetails as $value)
                 <tr>
                   <td >{{$value->item_code}}</td>
                   <td >{{$value->product_name}}</td>
                   <td >{{$value->qty}}</td>
                   <td >{{$value->cost_price}}</td>
                   <td  >
                      <select class="select2 form-control" data-placeholder="Select Action" id="status{{$value->id}}" name="status" onchange=" changeselection(this.id, '{{$value->product_id}}','{{$value->id}}','{{$value->item_code}}','{{$value->cost_price}}')">

                        <option value="">Select Action</option>
                        @foreach($status as $value)
                        <option value="{{$value->status_id}}">{{$value->status_name}}</option>
                        @endforeach
                      </select>

                   </td>
                 </tr>
                  @endforeach
                @endif  
                              </tbody>
                           </table>
                        </div>
                     </div>
                                          <div class="row">
      <div class="col-lg-12 col-sm-12 ">
            <div class="form-group ">
                <button type="button" id="btnFinalSubmit" class="btn btn-md btn-primary waves-effect waves-light  f-right" data-toggle="modal" data-target="#shipment-modal"> 
                  <i class="icofont icofont-plus"></i>
                   Add Shipment Charges
                </button>

            </div>       
        </div>  
 </div> 
                  </div>
               </div>

               <div id='abc'>
                 
               </div>



 <div class="modal fade modal-flex" id="shipment-modal" tabindex="-1" role="dialog">
                           <div class="modal-dialog modal-md" role="document">
                              <div class="modal-content">
                                 <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    <h4 class="modal-title">Shipment Charges</h4>
                                 </div>
                                 <div class="modal-body">
                                   <div class="row">
                                         <div class="col-md-12">
                                          <div class="form-group"> 
                                          <label class="form-control-label">Enter Shipment Amount:</label>  
                                             <input type="text" placeholder="0" name="shipmentamt" id="shipmentamt" class="form-control" />
                                            </div>
                                          </div>
                                      </div>   
                                 </div>
                                 <div class="modal-footer">
                                    <button type="button" id="btnsubmit" class="btn btn-success waves-effect waves-light" onclick="update($('#shipmentamt').val())">Submit</button>
                                 </div>
                              </div>
                           </div>
                        </div> 

 
 @endsection  

@section('scriptcode_three')
   <script type="text/javascript">




   	 $(".select2").select2();

   	   function changeselection(id, productid, tableid, itemcode, cp){

          if ($('#'+id).val() == 3) {
              $('#abc').html('');
              updatetransferitem(3,tableid);

          }
          else if($('#'+id).val() == 2){

            stock_details(itemcode,productid,cp);

                updatetransferitem(2,tableid);




          }
          else{
          // alert($('#'+id).val() + " " +productid);

          }
      }

 function  stock_details(itemcode,productid,cp){
           $('#abc').html('');
                itcode=itemcode;
                  $.ajax({
                        url : "{{url('/stockdetails')}}",
                        type : "POST",
                        data : {_token : "{{csrf_token()}}",itemcode:itemcode},
                        dataType: 'json',
                        success : function(resp){
                          var count=0;
                          $.each(resp,function(index,value){
                            $('#abc').append('<div class="col-xl-4 col-lg-6 grid-item"><div class="card"><div class="card-block txt-white bg-spark-chart"><div> <h5 class="d-inline-block" id="brnch'+count+'" data-id="'+value.branch_id+'" data-value="'+value.branch_name+'">Branch, '+value.branch_name+'</h5></div><div class="text-center"><h1 class="f-w-100 f-90" >'+value.stock+'</h1><h5 class="txt-white bg-spark-chart" >Stock in Hand</h5></div></div><div class="card-block"><label>Enter Transfer Quantity:</label> <input type="text" placeholder="0" name="transferqty'+count+'" id="transferqty'+count+'" class="form-control" /><br><a class="btn btn-md btn-dark waves-effect waves-light f-right m-r-35" onclick="insert_challan('+value.product_id+','+count+','+cp+','+value.stock+')" >Make Deliviery Challan </a></div></div></div>')
                            count++;
                          });
                          }
                        }); 
                }


               function  updatetransferitem(statusid, tableid){
              

 
              $.ajax({
                    url : "{{url('/updatetransferitem')}}",
                    type : "PUT",
                    data : {_token : "{{csrf_token()}}",id:tableid,statusid:statusid},
                    success : function(resp){
                    	
                      }
                    }); 
         }

         function insert_challan(pid,count,cp,stock){
          
          let deduction = 0;
          deduction = stock - $("#transferqty"+count).val();

         	if ($("#transferqty"+count).val() == 0) {
            swal({
               title: "Error Message",
                text: "zero not acceptable!",
                type: "warning"
                 });
         	}
         	else{
         	$.ajax({
         		url: "{{url('/insertdeliverchallan')}}",
         		type: "POST",
         		data: {_token:"{{csrf_token()}}",
         		transferid:'{{$getdetails[0]->transfer_id}}',
         		// branchto:'{{$transferlist[0]->branch_to}}',
         		productid:pid,
         		qty:$("#transferqty"+count).val(),
         		cp:cp,
            deduction:deduction,
         	},
         	success:function(resp){
            console.log(resp);
               swal({
               title: "Delivery Challan Created",
                text: "Delivery Challan Created Successfully!",
                type: "success"
                 });
			$('#abc').html('');
         	}
         	});
         	}


         }

         function update(amt){

         	$.ajax({
         		url: "{{url('/updatechallan')}}",
         		type: "PUT",
         		data: {_token:"{{csrf_token()}}",
         		shipmentamt:amt,
         		transferid:'{{$getdetails[0]->transfer_id}}',
         	},
         	success:function(resp){
             $('#shipment-modal').modal('hide');
             update_transfer_status();
                swal({
                            title: "Delivery Challan Created",
                            text: "Operation Completed Successfully!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                                 window.location= "{{ url('/challanlist') }}";
                           }
                       });
         	
                    	}
         	});
          
   	}


     function update_transfer_status()
     {
             $.ajax({
              url: "{{url('/removetransferorder')}}",
              type: "PUT",
              data: {_token:"{{csrf_token()}}",
              id:'{{$getdetails[0]->transfer_id}}',
              statusid:8,
                     },
              success:function(resp){
                }
                  });
    }
  
   </script>
    @endsection