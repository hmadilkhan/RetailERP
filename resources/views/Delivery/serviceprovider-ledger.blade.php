@extends('layouts.master-layout')

@section('title','Service Provider Ledger')

@section('breadcrumtitle','Service Provider Ledger').

@section('navdelivery','active')

@section('navservices','active')

@section('content')
<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Currently you are on <span class="text-info" >{{$details[0]->provider_name}}</span> Ledger </h5>
          <a class="f-right" onclick="toggle()">
           <i class="icofont icofont-minus"></i>
         </a>
      <h6 class=""><a href="{{ url('/service-provider') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>

         <input type="hidden" id="providerid" name="providerid" value="{{$details[0]->id}}">
         </div>      
       <div class="card-block" id="insert-card">
 
           <div class="row">
         
          <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Enter Amount</label>
                   <input type="Number" min="1" name="amount" id="amount" class="form-control" placeholder="Enter Amount Here" />
                 <div class="form-control-feedback"></div>
                  </div>
              </div>

                     <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Select Mode</label>
                <select name="mode" id="mode" data-placeholder="Select Mode" class="form-control select2"  >
                    <option value="">Select Mode</option>
                    <option value="0">Nothing</option>
                    <option value="1">Debit</option>
                    <option value="2">Credit</option>
                </select>
                 <div class="form-control-feedback text-info">If you want to deposit amount in service provider account please select Credit OR If you want to clear previous amount so please select Debit</div>
                  </div>
              </div>

                      <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Narration | Comments</label>
                <textarea class="form-control" name="narration" id="narration"></textarea>
                 <div class="form-control-feedback text-info">If you want to add narration and comments for your transaction so please fill the textbox</div>
                  </div>
              </div>

       
            </div>

          <div class="button-group ">
                      <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="submit()" >   
                        <i class="icofont icofont-plus"> </i>
                        Submit Transaction
                    </button>
                    </div>  



           </div> 
 </div>
 <div class="card">

     <div class="card-header">
         <h5 class="card-header-text"><span class="text-info" >{{$details[0]->provider_name}}</span>  Ladger Details</h5>
         </div>  

       <div class="card-block">
        <!-- Search date and get pdf -->
          <br/>
        <div class="row m-t-20">
            <div class="col-md-1">
                <div class="form-group">
                    <label class="form-control-label">Closed</label>
                     <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                        <input type="checkbox" id="closed" name="closed" class="mainchk">
                        <span class="checkbox"></span>
                     </label>
                     <div class="captions"></div>
                  </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-control-label">From Date</label>
                    <input type='text' class="form-control" id="fromdate" name="fromdate" placeholder="DD-MM-YYYY"/>
                    <span class="help-block text-danger" id="rpbox"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-control-label">To Date</label>
                    <input type='text' class="form-control" id="todate" name="todate" placeholder="DD-MM-YYYY"/>
                    <span class="help-block text-danger" id="rpbox"></span>
                </div>
            </div>
            <div class="col-md-3">
                <button type="button" id="pdf"  class="btn btn-danger waves-effect waves-light m-t-25 m-l-10 f-left"  >
                    <i class="icofont icofont-ui-check"> </i>PDF
                </button>
            </div>
        </div>
        <hr/>
         <table id="tblledger" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
              <th>S.No #</th>
			  <th>SP No</th>
			  <th>Date</th>
              <th>Time</th>
              <th>Receipt No</th>
              <th>Total Amount</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Balance</th>
              <!--<th>Status</th>-->
              <th>Actions</th>
            </tr>
         </thead>
         <tbody>
          @if($details)
          @foreach($details as $key => $value)
          <tr>
           <td >{{$value->ladger_id}}</td> 
		   <td class="text-danger">{{$value->service_provider_order_no}}</td>
		   <td >{{date("d M Y", strtotime($value->date))}}</td>
           <td >{{date("h:i A", strtotime($value->date))}}</td>
		   <td>{{$value->receipt_no}}</td>
           <td>{{$value->receipt_total_amount}}</td>
           <td class="{{($value->debit > 0 ? 'text-danger' : '')}}">{{$value->debit}}</td>
           <td class="{{($value->credit > 0 ? 'text-success' : '')}}">{{$value->credit}}</td>
           <td  >{{$value->balance}}</td>
          <!-- <td>{{$value->closed == '0'?'Closed':'Active'}}</td> -->
           <td >
				<i href="javascript:void(0)" class="icofont icofont-ui-edit text-info" onclick="return serviceProviderNarration('{{$value->ladger_id}}','{{$value->narration}}')" data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit Narration'></i>
				<i href="javascript:void(0)" class="icofont icofont-list text-warning" onclick="return showDetails('{{$value->delivery_person_name}}','{{$value->contact_no}}','{{$value->vehicle_no}}','{{$value->service_provider_order_no}}')" data-toggle='tooltip' data-placement='top' title='' data-original-title='Show Details'></i>
				&nbsp;<i onclick='showReceipt("{{$value->receipt_no}}")' class='icofont icofont icofont-printer text-success' data-toggle='tooltip' data-placement='top' title='' data-original-title='Show Receipt'></i>
		   </td>
         </tr>
         @endforeach
         @endif
       </tbody>
     </table>
     </div>      
    </div>  
</section>

 <!-- modals -->
   <div class="modal fade modal-flex" id="category-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Add Category</h4>
             </div>
             <div class="modal-body">
               <div class="row">
                     <div class="col-md-12">
                      <div class="form-group"> 
                        <label class="form-control-label">Category Name:</label>
                         <input type="text"  name="catmodal" id="catmodal" class="form-control" />
                        </div>
                      </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" id="btn_depart" class="btn btn-success waves-effect waves-light" onClick="addcat()">Add Category</button>
             </div>
          </div>
           </div>
        </div> 

{{-- Model --}}
<div class="modal fade modal-flex" id="narration-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">EDIT SERVICE PROVIDER NARRATION</h4>
            </div>
            <form action="" method="post" id="editNarration">
                {{csrf_field()}}
                <input class="form-control" type="hidden" name="service_provider_narration_id" id="model-service-provider-id" value="" />
                <div class="row m-r-0 m-l-0 m-t-10">
                    <div class="messages"></div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-control-label">Narration</label>
                            <input class="form-control" type="text" name="narration" id="model-narration" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-success waves-effect waves-light f-right">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-flex" id="details-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">SERVICE PROVIDER DETAILS</h4>
            </div>
                <div class="row m-r-0 m-l-0 m-t-10">
                    <div class="messages"></div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Delivery Person Name</label>
                            <input class="form-control" type="text" name="narration" id="person-name" />
                        </div>
                    </div>
					<div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Service Provider Order No</label>
                            <input class="form-control" type="text" name="narration" id="sp-no" />
                        </div>
                    </div>
					<div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Contact Number</label>
                            <input class="form-control" type="text" name="narration" id="contact-number" />
                        </div>
                    </div>
					<div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-control-label">Vehicle Number</label>
                            <input class="form-control" type="text" name="narration" id="vehicle-number" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnCancel" class="btn btn-danger waves-effect waves-light f-right">
                        Close
                    </button>
                </div>
        </div>
    </div>
</div>
 
@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

     $("#mobnumb").tagsinput({
     maxTags: 10
    });


 $("#btn_category").on('click',function(){
  $('#catmodal').val('');
  $("#category-modal").modal("show");
});

 function addcat(){
  if ($('#catmodal').val() == "") {
    swal({
            title: "Error Message",
            text: "Category Can not left blank!",
            type: "error"
            });
  }
  else{

   $.ajax({
            url: "{{url('/store-category')}}",
            type: 'POST',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          category:$('#catmodal').val(),
        },
            success:function(resp){   
                if(resp != 0){
                     swal({
                      title: "Operation Performed",
                      text: "Category Created Successfully!",
                      type: "success"});

                      $("#category-modal").modal("hide");

                      $("#category").empty();
                     for(var count=0; count < resp.length; count++){
                      $("#category").append("<option value=''>Select Category</option>");
                      $("#category").append(
                        "<option value='"+resp[count].category_id+"'>"+resp[count].category+"</option>");
                     }
                  }
                  else{
                    swal({
                            title: "Already exsit",
                            text: "Particular Category Already exsit!",
                            type: "warning"
                       });
                  }
                 
             }

          }); 
  }
}

      $('#tblledger').DataTable({
        "order": [0, "desc"],
        bLengthChange: true,
        displayLength: 25,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

function submit(){

if ($('#amount').val() == "") {
  swal({
      title: "Error Message!",
      text: "Please Enter Amount!",
      type: "error"});
}
else if ($('#amount').val() == 0) {
  swal({
      title: "Error Message!",
      text: "Enter Amount Should be greater than 0!",
      type: "error"});
}
else if ($('#mode').val() == "") {
  swal({
      title: "Error Message!",
      text: "Please Select Mode!",
      type: "error"});
}
else if ($('#mode').val() == 0) {
  swal({
      title: "Error Message!",
      text: "Please Select Mode!",
      type: "error"});
}

else{
   $.ajax({
                    url: "{{url('/insert-ledger')}}",
                    type:"POST",
                   data:{_token:"{{ csrf_token() }}",
                    amount:$('#amount').val(),
                    mode:$('#mode').val(),
                    narration:$('#narration').val(),
                    providerid:$('#providerid').val(),
          },
                    dataType:"json",
                    success:function(resp){
                    if (resp != 0) {
                 swal({
                      title: "Operation Performed",
                      text: "Transaction Performed Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
							window.location = "{{url('/service-provider-ledger',Crypt::encrypt($details[0]->id))}}";
                      }
                       });
                    }
                    else{
                      swal("Error Message", "You can not cleared bills while balance is 0", "error");
                    }
                    }
                  });
   }
}

$('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Do You want to In-Active Delivery Charges?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "In-Active!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/inactive-charges')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        chargesid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "success",
                                        text: "Delivery Charges In-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/delivery-charges') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled :)", "error");
           }
        });
  });








function edit(id,branchid,name,charges){
$('#update-modal').modal('show');
$('#areanamemodal').val(name);
$('#chargesmodal').val(charges);
$('#chargesid').val(id);
$('#branchmodal').val(branchid).change();

}

  
function update(){

 $.ajax({
                    url: "{{url('/update-charges')}}",
                    type:"POST",
                   data:{_token:"{{ csrf_token() }}",
                     branch:$('#branchmodal').val(),
                    areaname:$('#areanamemodal').val(),
                    charges:$('#chargesmodal').val(),
                    chargesid:$('#chargesid').val(),
          },
                    dataType:"json",
                    success:function(resp){
                     if (resp != 0) {
                 swal({
                     title: "Operation Performed",
                      text: "Delivery Charges Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                         window.location = "{{url('/delivery-charges')}}";
                      }
                       });
                    }
                else{
                      swal({
                      title: "ALready Exsist!",
                      text: "Delivery Charges of this Area ALready Exsist!",
                      type: "error"});
                    }
                    }
                  });
}








$('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/inacive-delivery-charges')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tblcharges tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblcharges tbody").append(
                          "<tr>" +
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].area_name+"</td>" +  
                            "<td>"+result[count].charges+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/delivery-charges') }}";
  }
});

function reactive(id){
swal({
          title: "Are you sure?",
          text: "You want to Re-Active Delivery Charges!",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "yes plx!",
          cancelButtonText: "cancel plx!",
          closeOnConfirm: false,
          closeOnCancel: false
        },
        function(isConfirm){
          if(isConfirm){
                     $.ajax({
                        url: "{{url('/reactive-charges')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        chargesid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Re-Active",
                                        text: "Delivery Charges Re-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/delivery-charges') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled:)", "error");
           }
        });
}


function toggle(){

  $('#insert-card').toggle();
}

  $('#insert-card').hide();
  function serviceProviderNarration(id,narration){
   $('#model-service-provider-id').val(id);
   $('#model-narration').val(narration);
   $('#narration-modal').modal('show');
 }
 
 function showDetails(DeliveryPerson,Contact,Vehicle,ServiceProviderNo)
 {
	 
	 $("#person-name").val("");
	 $("#sp-no").val("");
	 $("#vehicle-number").val("");
	 $("#contact-number").val("");
	 $("#person-name").val(DeliveryPerson);
	 $("#sp-no").val(ServiceProviderNo);
	 $("#vehicle-number").val(Vehicle);
	 $("#contact-number").val(Contact);
	 $('#details-modal').modal('show');
 }

    // Edit Manual Payment
    $('#editNarration').on('submit', function(e) {
        e.preventDefault();
        var $form = $('#editNarration');
        // check if the input is valid using a 'valid' property
        var formStatus = $('#editNarration')[0].checkValidity();
        $.ajax({
            async: false,
            type: "POST",
            url: "<?php echo URL::to('edit-delivery-narration') ?>",
            data: $form.serialize(),
            success: function(response) {
                // var obj = $.parseJSON(response);
                var obj = response;
                if (obj.status == 'true') {
                    $('.messages').html('<div class="alert alert-success p-r-20 p-l-10" style="background-color:#dff0d8;color:#3c763d;border-color:d0e9c6" >' + obj.message + '</div>').fadeIn().delay(3000).fadeOut();
                    window.setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                   // console.log();
                    var message = response.message;
                    // $.each(response.message, function(key, value) {
                    //     message += value + '<br>';
                    // });

                    $('.messages').html('<div class="alert alert-danger">' + message + '</div>').fadeIn().delay(3000).fadeOut();
                }
            }

        });
        return false; //mark-2

    });
        $('#fromdate,#todate').bootstrapMaterialDatePicker({
        format: 'YYYY-MM-DD',
        time: false,
        clearButton: true,

        icons: {
        date: "icofont icofont-ui-calendar",
        up: "icofont icofont-rounded-up",
        down: "icofont icofont-rounded-down",
        next: "icofont icofont-rounded-right",
        previous: "icofont icofont-rounded-left"
        }
        });

         $('#pdf').click(function (e){
            e.preventDefault();
            var closed = false;
            if ($('#closed').is(":checked"))
            {
              closed = true;
            }
            if($('#fromdate').val() == "")
            {
                alert("Please Select From Date");
            }
            else if($('#todate').val() == "")
            {
                alert("Please Select To Date");
            }
            else{
                window.location = "{{url('serviceProviderLedgerPDF')}}?provide_id={{$providerID}}&from="+$('#fromdate').val()+"&to="+$('#todate').val()+"&closed="+closed+" "
            }
        });
	function showReceipt(ReceiptNo) {
		window.open("{{url('print')}}"+"/"+ReceiptNo);
	}
	
	$("#btnCancel").click(function(){
		$('#details-modal').modal('hide');
	})
 </script>

@endsection


