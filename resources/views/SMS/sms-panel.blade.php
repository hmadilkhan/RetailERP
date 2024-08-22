@extends('layouts.master-layout')

@section('title','SMS Panel')

@section('breadcrumtitle','SMS Panel')

@section('navsms','active')

@section('content')

<section class="panels-wells">
<div class="card">
     <div class="card-header">
         <h5 class="card-header-text">SMS Panel</h5>
         </div>      
       <div class="card-block">
        <form method="post" id="deptform" enctype="multipart/form-data">
           
        {{ csrf_field() }}
<!--     <div class="md-input-wrapper">
      <input type="text" class="md-form-control mob_no md-static" data-mask="9999-999-999" />
      <label>Mobile No</label>
                           </div> -->
           <div class="row">
          <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Sender Name:</label>
                   <input type="text" name="sendername" id="sendername" class="form-control"/>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
        
   <!--      <div class="col-lg-8 col-md-8">
                <div class="form-group">
                <label class="form-control-label">Enter Message</label>
                <textarea name="message" id="message" class="form-control"></textarea>
                 
                 <div class="form-control-feedback"></div>
                  </div>
              </div> -->
           
          <div class="col-lg-4 col-md-4">
                
              <div class="form-group">
                  <label class="form-control-label">Mobile Numbers:</label>
                 <div class="md-input-wrapper">

                   
                      <input class="form-control mob_no md-static" id="mobnumb" name="mobnumb" type="text" data-mask="9999-999-999"  />
                    
                    </div>
                   <span class="form-control-feedback text-danger" id="subdpt_alert"></span>
              </div>
               <div class="form-control-feedback text-info">You can also add more than one number</div>
            

              </div>

                  <div class="col-lg-4 col-md-4">
              <div class="form-group row">
                <button class="btn btn-circle btn-primary f-left m-t-30 m-l-20"  type="button" id="btn_save" data-toggle="tooltip" data-placement="top" title="" data-original-title="Save" onclick="submitdata()"><i class="icofont icofont-plus"  
                  ></i>&nbsp; Save</button>.

                    <button class="btn btn-circle btn-danger f-left m-t-30 m-l-10" id="btn_clear" type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Clear"><i class="icofont icofont-error" 
                  ></i> Clear</button>
              </div>
            </div>
        
    
            </div>

               </form> 

        
           </div> 
 </div>
 <div class="card">

     <div class="card-header">
         <h5 class="card-header-text">Status Detail</h5>
         </div>      
       <div class="card-block">
             <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show In-Active Numbers</div>
                  </div>
                  <br/>
                      <br/>
         <table id="tblholiday" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Sender Name</th>
               <th>Mobile Numbers</th>
               <th>Status</th>
               <!-- <th>Message</th> -->
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
       @if($general)
                @for($i=0; $i < sizeof($general); $i++)
                       <tr>
                         <td>{{ $general[$i]->name}}</td>
                         <td>
                            @if($sub)
                              @for($j=0; $j < sizeof($sub); $j++)
                                 @if($sub[$j]->sms_id == $general[$i]->sms_id)
                                 <label>{{ $sub[$j]->mobile_number }}</label>,
                                 @endif
                              @endfor
                            @endif  
                             <td>{{ $general[$i]->status_name}}</td>
                              <td class="action-icon">
                     <a  class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit" onclick="edit('{{ $general[$i]->sms_id}}','{{ $general[$i]->name}}')"><i class="icofont icofont-ui-edit text-primary f-18" ></i> </a>
                        <i class="icofont icofont-ui-delete text-danger f-18" onclick="remove('{{$general[$i]->sms_id}}','{{$general[$i]->name}}')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"></i>
             
                 </td>          
                         </td>
                     </tr>
               @endfor
             @endif
      
           </tbody>
     </table>
     </div>      
          </div>  




</section>

 <!-- modals -->
 <div class="modal fade modal-flex" id="update-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Update Modal</h4>
             </div>
             <div class="modal-body">
              <div class="row">
                <div class="col-lg-8 col-md-8">
                    <label class="form-control-label">Sender Name:</label>
                    
                   <input type="text" name="sendernamemodal" id="sendernamemodal" class="form-control"/>

                     <input type="hidden" name="smsmodaid" id="smsmodaid" class="form-control"/>

                     
                </div>
                    <div class="col-lg-4 col-md-4">
<label class="form-control-label">Action</label>
                   <button type="button" class="btn btn-primary waves-effect waves-light" onClick="updategeneral()">Update Details</button>
</div>
                </div>
               <div class="row">
                <div class="col-lg-12 col-md-12">
                    <table id="tblsms" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
         <thead>
            <tr>
               <th>Mobile Numbers</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
      
      
           </tbody>
     </table>
     </div>
                  </div>   
             </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect waves-light" onClick="inactiveall()"><i class="icofont icofont-close-circled"></i>&nbsp; In-Active All</button>
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

$("#btn_clear").on('click',function(){

    $("#deptform")[0].reset();
    $("#mobnumb").tagsinput('removeAll');

});

 
      $('#tblholiday').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Employee',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

function submitdata(){

   $.ajax({
                    url: "{{url('/insert-sms')}}",
                    type:"POST",
                   data:{_token:"{{ csrf_token() }}",
                    sendername:$('#sendername').val(),
                    message:$('#message').val(),
                    mobile_number:$('#mobnumb').val(),
          },
                    dataType:"json",
                    success:function(resp){
                 swal({
                      title: "Operation Performed",
                      text: "Data Stored Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                         window.location = "{{url('view-sms')}}";
                      }
                       });
                    }
                  });
}



function edit(id,name){
  $.ajax({
                    url: "{{url('/getsmsdetails')}}",
                    type:"GET",
                   data:{_token:"{{ csrf_token() }}",
                    id:id,
          },
                    dataType:"json",
                    success:function(result){
                        $('#update-modal').modal('show');
                        $('#sendernamemodal').val(name);
                        $('#smsmodaid').val(id);
                           $("#tblsms tbody").empty();
                for(var count =0;count < result.length; count++){
                        $("#tblsms tbody").append(
                          "<tr>" +
                          "<div class='md-input-wrapper'>"+
                           "<td><input type='text' value='"+result[count].mobile_number +"' class='form-control mob_no md-static' data-mask='9999-9999999' id='tbx_"+result[count].id+"'/>"+
                          "</div>"+
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><i onclick='update("+result[count].id+")' class='icofont icofont-check-circled text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i>  <i onclick='deletenumb("+result[count].id+","+result[count].sms_id+")' class='icofont icofont-ui-delete text-danger f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Delete'></i>"+
                          "</tr>" 
                         );
                    }
                    }
                  });
}

   // <div >
   //                            <input type="text" class="md-form-control "  />
   //                            <label>Mobile No</label>
   //                         </div>

function update(id){

 $.ajax({
                    url: "{{url('/update-smsdetails')}}",
                    type:"POST",
                   data:{_token:"{{ csrf_token() }}",
                    id:id,
                    mobile_number:$("#tbx_"+id).val(),
          },
                    dataType:"json",
                    success:function(resp){
                 swal({
                      title: "Operation Performed",
                      text: "Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                          $('#update-modal').modal('hide');
                          window.location = "{{url('view-sms')}}";
                      }
                       });
                    }
                  });
}

function updategeneral(){

  $.ajax({
                    url: "{{url('/update-smsgeneral')}}",
                    type:"POST",
                   data:{_token:"{{ csrf_token() }}",
                    id:$("#smsmodaid").val(),
                    name:$("#sendernamemodal").val(),
          },
                    dataType:"json",
                    success:function(resp){
                 swal({
                      title: "Operation Performed",
                      text: "Updated Successfully!",
                      type: "success"},
                      function(isConfirm){
                      if(isConfirm){
                          $('#update-modal').modal('hide');
                          window.location = "{{url('view-sms')}}";
                      }
                       });
                    }
                  });
}


function deletenumb(id,smsid){
  let name = $('#sendernamemodal').val();
 swal({
          title: "Are you sure?",
          text: "You want to In-Active "+name+" Number!",
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
                        url: "{{url('/inactive-number')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        smsid:smsid,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Number Deleted Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-sms') }}";
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


function inactiveall(){
  
  swal({
          title: "Are you sure?",
          text: "You want to In-Active All Numbers!",
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
                        url: "{{url('/inactive-all')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        smsid:$('#smsmodaid').val(),
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "In-Active",
                                        text: "Number In-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-sms') }}";
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

function remove(id,name){
swal({
          title: "Are you sure?",
          text: "You want to In-Active All Numbers of "+name+ " ?",
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
                        url: "{{url('/inactive-all')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        smsid:id,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "In-Active",
                                        text: "Number In-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-sms') }}";
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

$('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/inactivedetails')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tblholiday tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblholiday tbody").append(
                          "<tr>" +
                            "<td>"+result[count].name+"</td>" +  
                            "<td>"+result[count].mobile_number+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><a class='m-r-10' onclick='reactive("+result[count].sms_id+","+result[count].id+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-check-circled text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/view-sms') }}";
  }
});

function reactive(smsid,id){
swal({
          title: "Are you sure?",
          text: "You want to Re-Active this Number!",
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
                        url: "{{url('/reactive')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:id,
                        smsid:smsid,
                        },
                        success:function(resp){
                          
                            if(resp == 1){
                                 swal({
                                        title: "Re-Active",
                                        text: "Number Re-Active Successfully!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-sms') }}";
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

 </script>

@endsection


