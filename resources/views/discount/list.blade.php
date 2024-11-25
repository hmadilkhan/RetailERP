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

/*.switch {*/
/*  position: relative;*/
/*  display: inline-block;*/
/*  width: 60px;*/
/*  height: 34px;*/
/*}*/

/* Hide default HTML checkbox */
.switch {
  position: relative;
  display: inline-block;
  width: 43px;
  height: 21px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 13px;
  width: 13px;
  left: 2px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
  /*content:'On';*/
}

input+.slider:before {
	/*content: "Off";*/
 }

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
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

           <a href="javascript:void(0)" class="btn btn-danger waves-effect waves-light f-right m-r-1 d-none" id="removeAllBtn" data-id="{{ $status }}"> <i class="icofont icofont-plus f-18 m-r-5"></i>Remove All
              </a>
        </div>
		<div class="col-md-12 m-b-2">
		<a href="{{($status == 2  ? url('/get-discount') :  url('/get-discount/in-active'))}}"> <div class="captions">{{($status == 2 ? 'Show Active Items' : 'Show In-Active Items')}}</div> </a>

                    </div>
        <div class="card-block responsive">

     <table id="expensetb" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>

               <th class="d-none">ID</th>
               <th><input type="checkbox" id="headCheckbox"></th>
               <th>Code</th>
               <th>Website Name</th>
               <th>Start Date</th>
               <th>Expiration Date</th>
               <th>Applies To</th>
               <th>Customer Eligibilty</th>
               <th>Type</th>
               <th>Open Discount</th>
               <th>Status</th>
               <th>Action</th>

            </tr>
         </thead>
         <tbody>
      		@if($discount)
      			@foreach($discount as $value)
      				<tr>
      					<td class="d-none">{{$value->discount_id}}</td>
      					<td><input type="checkbox" class="child-chkbx" value="{{ $value->discount_id }}"></td>
      					<td>{{$value->discount_code}}</td>
      					<td>{{$value->website_name}}</td>
      					<td>{{$value->startdate.' '.$value->starttime}}</td>
      					<td>{{$value->enddate.' '.$value->endtime}}</td>
      					<td>{{ $value->applies_name }}</td>
      					<td>{{ $value->customer_eligibilty == 1 ? 'EveryOne' : 'Limited'}}</td>
      					<td>{{$value->type_name.' ('.$value->discount_value.')' }}</td>
      					<td>{{ $value->open_discount == 1 ? 'Open Discount' : 'Voucher Apply' }}</td>
      					<td>
                          <label class="badge badge-{{ $value->status == 1 ? 'primary' : 'danger' }}">{{$value->status_name}}</label>
      					</td>
      					<td class="action-icon">
                         <!-- <div class="form-group">    -->
                         <!--     <label>-->
                         <!--       <input type="checkbox" onchange="" data-toggle="toggle" {{ $value->status == 1 ? 'checked' : '' }}>-->
                         <!--     </label> -->
                         <!--</div> -->

                                <!-- Rounded switch -->
                                <label class="switch m-r-1">
                                  <input type="checkbox" title="" data-original-title="Active/In-Active Switch"
                                  onclick="switchMode({{ $value->discount_id }},{{ $value->status }},'{{$value->discount_code}}',this)" {{ $value->status == 1 ? 'checked' : '' }}>
                                  <span class="slider round"></span>
                                </label>

      							<a  class="p-r-10 f-18 text-primary" onclick="modelcall('{{ $value->discount_id }}')" title="" data-original-title="View">
      							    <i class="icofont icofont-eye-alt"></i></a>

                                <!--<a href="{{-- url('/edit-discount') --}}/{{-- Crypt::encrypt($value->discount_id) --}}" class="p-r-10 f-18 text-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="icofont icofont-ui-edit"></i></a>-->

                                <a class="icofont icofont-ui-delete text-danger f-18 alert-confirm" onclick="discountDelete({{ $value->discount_id }},{{$status}})" data-id="{{ $value->discount_id }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></a>

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

                  <div class="modal fade modal-flex " id="createSchedule-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
                     <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                         <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            <h4 class="modal-title">Create Schedule</h4>
                          </div>

                          <div class="modal-body">
                              <input type="hidden" name="discount_id" id="discount_id_md">
                              <div class="row">
        <div class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Start Date</label>
            <input type='text' class="form-control" id="startdate" name="startdate" placeholder="DD-MM-YYYY" value="{{ date('Y-m-d') }}"/>
            <span class="help-block text-danger" id="rpbox"></span>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="form-group">
            <label class="form-control-label">Start Time</label>
            <input type='text' class="form-control" id="starttime" name="starttime" placeholder="H:i Am" value="{{ date('h:i A', strtotime(date('H:i'))) }}"/>
            <span class="help-block text-danger" id="rpbox"></span>
          </div>
        </div>

        <div class="col-lg-12 col-md-12 rkmd-checkbox checkbox-rotate checkbox-ripple">
          <label class="input-checkbox checkbox-primary">
            <input type="checkbox" id="chkEndDate" name="chkEndDate">
            <span class="checkbox"></span>
            <span class="ripple"></span></label>
          <div class="captions"> End Date.</div>
        </div>

        <div id="divEndSection" class="d-none">
          <div class="col-lg-6 col-md-6">
            <div class="form-group">
              <label class="form-control-label">End Date</label>
              <input type='text' class="form-control" id="enddate" name="enddate" placeholder="DD-MM-YYYY" />
              <span class="help-block text-danger" id="rpbox"></span>
            </div>
          </div>
          <div class="col-lg-6 col-md-6">
            <div class="form-group">
              <label class="form-control-label">End Time</label>
              <input type='text' class="form-control" id="endtime" name="endtime" placeholder="DD-MM-YYYY" />
              <span class="help-block text-danger" id="rpbox"></span>
            </div>
          </div>
        </div>
    </div>

                           </div>
                           <div class="modal-footer">
                               <button type="button" class="btn btn-success" onclick="re_active_discount()">Save Changes</button>
                        </div>
                        </div>
                     </div>
                     <!-- end of modal fade -->
                  </div>

@endsection


@section('scriptcode_one')
 <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('scriptcode_three')
 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

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

$("#headCheckbox").on('click',function(){
    if($(this).is(':checked')){
         $('.child-chkbx').prop('checked', true);
         if($("#removeAllBtn").hasClass('d-none')){
            $("#removeAllBtn").removeClass('d-none').addClass('d-inline-block');
         }
    }else{
         $('.child-chkbx').prop('checked', false);

         if(!$("#removeAllBtn").hasClass('d-none')){
            $("#removeAllBtn").addClass('d-none').removeClass('d-inline-block');
         }

    }
});

$("#removeAllBtn").on('click',function(){
    swal({
            title: "Are you sure?",
            text: "You want to remove all discount!",
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
              var idArray = [];
              $(".child-chkbx").each(function(){
                  if($(this).is(':checked')){
                      if($.inArray($(this).val(),idArray) == -1){
                          idArray.push($(this).val());
                      }
                  }
              })
                        $.ajax({
                            url: "{{url('/remove-discount')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:idArray,mode:'removeAll'
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Success!",
                                        text: "",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/get-discount') }}"+($(this).attr('data-id') == 2 ? 'in-active' : '');
                                        }
                                    });
                                }
                            }

                        });
            }else {
                swal("Cancelled", "Operation Cancelled:)", "error");
            }
        });
});

function switchMode(discId,status,voucher,element){
 var status_name = null;
 var value = 2;
    if($(element).is(':checked')){
        status_name = 'Active';
        value = 1;
    }else{
        status_name = 'In-Active';
        value = 2;
    }

    swal({
            title: "Are you sure?",
            text: "You want to "+status_name+" this discount voucher "+voucher+" !",
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

                  if(status == 1){
                        $.ajax({
                            url: "{{url('/remove-discount')}}",
                            type: 'POST',
                            data:{_token:"{{ csrf_token() }}",
                                id:discId,mode:value
                            },
                            success:function(resp){
                                if(resp == 1){
                                    swal({
                                        title: "Success!",
                                        text: "Campaign "+status_name+" successfully.",
                                        type: "success"
                                    },function(isConfirm){
                                        if(isConfirm){
                                            window.location="{{ url('/get-discount') }}";
                                        }
                                    });
                                }
                            }

                        });

                  }else{
                      swal.close();
                      $("#createSchedule-modal").modal('show');
                      $("#discount_id_md").val(discId);
                  }
            }else {
                swal("Cancelled", "Operation Cancelled:)", "error");
              if(status == 1){
                  $(element).prop('checked', true);
              }else{
                  $(element).prop('checked', false);
              }
            }
        });
}

function re_active_discount(){
        $.ajax({
            url: "{{route('reactiveDiscount')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
                id:$("#discount_id_md").val(),startdate:$("#startdate").val(),startime:$("#starttime").val(),endate:$("#enddate").val(),endtime:$("#endtime").val()
            },
            success:function(resp){
                if(resp == 1){
                    swal({
                        title: "Success!",
                        type: "success"
                    },function(isConfirm){
                        if(isConfirm){
                            window.location="{{ url('/get-discount') }}";
                        }
                    });
                }
            }

        });
}

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
                                    "<img src='storage/images/task/task-u2.jpg' width='190' height='170' class='tile'>"+
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
                                    "<img src='storage/images/products/"+value.image+"' width='150' height='150' class='tile '>"+
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

function discountDelete(id,md){

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
                                            window.location="{{ url('/get-discount') }}"+(md == 2 ? '/in-active' : '');
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

  $('#chkEndDate').change(function() {
    if ($('#chkEndDate').prop("checked") == true) {

         if($('#divEndSection').hasClass('d-none')){
             $('#divEndSection').removeClass('d-none');
         }
    //   $('#divEndSection').css('display', 'block');
    } else if ($('#chkEndDate').prop("checked") == false) {
         if(!$('#divEndSection').hasClass('d-none')){
             $('#divEndSection').addClass('d-none');
         }

    //   $('#divEndSection').css('display', 'none');
    //   $('#endtime').val('');
    //   if ($('#startdate') != "") {
    //     var d = new Date($('#startdate').val());
    //     var month = myFunction($('#startdate').val());
    //     var value = "Active from " + month + " " + d.getDate();
    //     $('#disc_date').empty();
    //     $('#disc_date').append("<li>" + value + "</li>")
    //   } else {
    //     $('#disc_date').empty();
    //   }
    }
  });

  $('#startdate,#enddate').bootstrapMaterialDatePicker({
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

  $('#starttime,#endtime').datetimepicker({
    format: 'LT',
    icons: {
      time: "icofont icofont-clock-time",
      date: "icofont icofont-ui-calendar",
      up: "icofont icofont-rounded-up",
      down: "icofont icofont-rounded-down",
      next: "icofont icofont-rounded-right",
      previous: "icofont icofont-rounded-left"
    }
  });

</script>

@endsection

