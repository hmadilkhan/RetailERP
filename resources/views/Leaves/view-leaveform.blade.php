@extends('layouts.master-layout')

@section('title','Employee')

@section('breadcrumtitle','Leave Form')

@section('navleavesdetails','active')

@section('navleaveform','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Leaves Application Details</h5>
         <a href="{{ url('/showleave_form') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus f-18 m-r-5"></i>Leave Form
              </a>
         </div>     

       <div class="card-block">

     <table id="tblleaves" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
              <th>Employee Name</th>
               <th>Leave Head</th>
               @if(session("roleId") == 2)
               <th>Branch Name</th>
               @endif
               <th>Avail Qty.</th>
               <th>From Date</th>
               <th>To Date</th>
               <th>Status</th>
               @if(session("roleId") == 2 || session("roleId") == 4)
               <th>Action</th>
               @endif
            </tr>
         </thead>
        <tbody>
                 @foreach($details as $value)
                 <tr>
                  <td >{{$value->emp_name}}</td>
                   <td >{{$value->leave_head}}</td>
                   @if(session("roleId") == 2)
                   <td >{{$value->branch_name}}</td>
                   @endif
                   
                   <td >{{$value->days}}</td>
                   <td >{{$value->from_date}}</td>
                   <td >{{$value->to_date}}</td>
                   <td >{{$value->leave_status}}</td>

            @if(session("roleId") == 2 || session("roleId") == 4)
                 <td class="action-icon">
                     <a  class="{{ $value->leave_status != 'Waiting for Approved' ? 'disabled' : '' }} m-r-10" data-toggle="tooltip" data-placement="top" title="Action" data-original-title="{{ $value->leave_status != 'Waiting for Approved' ? 'Disabled' : '' }}"><i class="icofont icofont-tools-alt-2 text-{{ $value->leave_status != 'Waiting for Approved' ? 'muted' :'primary'}} f-18"  <?php echo ($value->leave_status != "Waiting for Approved" ?  '' : 'onclick="action('.$value->id.','.$value->emp_id.','.$value->days.','.$value->updateid.','.$value->leave_id.')" '); ?>   ></i> </a>

                   <a class="m-r-10" data-toggle="tooltip" data-placement="top" title="Show balance" data-original-title="View" onclick="modalshow('{{$value->emp_id}}')"><i class="icofont icofont-eye-alt text-success f-18" ></i> </a>
                 </td>  
                 @endif        
                       
                 </tr>
                  @endforeach
     
                
           
         </tbody>
        
        
      
     </table>
  </div>
</div>
</section>
 <!-- modals -->
  <div class="modal fade modal-flex" id="action-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Action Modal</h4>
             </div>
             <div class="modal-body">
              <div class="row">
            <div class="col-lg-12 col-md-12">
              <input type="hidden" name="leaveid" id="leaveid" value="">
              <input type="hidden" name="empidmodal" id="empidmodal" value="">
              <input type="hidden" name="daysmodal" id="daysmodal" value="">
              <input type="hidden" name="updateid" id="updateid" value="">
              <input type="hidden" name="leaveidmodal" id="leaveidmodal" value="">
              
                      <div class="form-group"> 
                    <label class="form-control-label">Select Action</label>
                    <select name="actionmodal" id="actionmodal" data-placeholder="Select Action" class="form-control select2" onchange="updatestatus($('#actionmodal').val())">
                    <option value="">Select Action</option>
                         <option value="1">Approved</option>
                         <option value="2">Reject</option>
                </select>
                        </div>
                      </div>
              </div>
             </div>
             <div class="modal-footer">
            
             </div>
          </div>
           </div>
        </div> 



        <div class="modal fade modal-flex" id="balance-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Balance Modal</h4>
             </div>
             <div class="modal-body">
              <div class="row">
            <div class="col-lg-12 col-md-12">
           <div class="form-group">
             <table id="tblbalance"  class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
              <thead>
                <th>Leave Head</th>
                <th>Total Qty.</th>
                <th>Balance</th>
              </thead>
        <tbody>
        
         </tbody>
        
        
      
     </table>
                        </div>
                      </div>
              </div>
             </div>
             <div class="modal-footer">
            
             </div>
          </div>
           </div>
        </div> 

@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();

      $('#tblleaves').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });

  //Alert confirm
 $('.alert-confirm').on('click',function(){
    var id= $(this).data("id");

      swal({
          title: "Are you sure?",
          text: "Your will not be able to recover this imaginary file!",
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
                        url: "{{url('/remove-employee')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        empid:id,
                        statusid:2,
                        },
                        success:function(resp){
                            if(resp == 1){
                                 swal({
                                        title: "Deleted",
                                        text: "Do you want to remove Employee.",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/view-employee') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Your Employee is safe :)", "error");
           }
        });
  });

$('#chkactive').change(function(){
  if (this.checked) {
   $.ajax({
            url: "{{url('/view-inaciveemployee')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
          },
            success:function(result){
                if(result){
                   $("#tblemp tbody").empty();
                   for(var count =0;count < result.length; count++){

                        $("#tblemp tbody").append(
                          "<tr>" +
                             "<td class='text-center'><img width='42' height='42' src='public/assets/images/employees/images/"+((result[count].emp_picture != "") ? result[count].emp_picture : 'placeholder.jpg')+"' alt='"+result[count].emp_picture+"'/></td>" +
                            "<td>"+result[count].emp_acc+"</td>" +  
                            "<td>"+result[count].emp_name+"</td>" +  
                            "<td>"+result[count].emp_contact+"</td>" +  
                            "<td>"+result[count].designation_name+"</td>" +  
                            "<td>"+result[count].department_name+"</td>" +  
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><a class='m-r-10' onclick='details("+result[count].empid+")' data-toggle='tooltip' data-placement='top' data-original-title='View'><i class='icofont icofont-eye-alt text-primary f-18' ></i></a></td>"+
                          "</tr>"
                         );
                    }

                  }
             }
          }); 
  }
  else{
 window.location="{{ url('/view-employee') }}";
  }
});

function details(id){
window.location="{{ url('/details-employee')}}/"+id;
}

function action(id,empid,days,updateid,leaveid){
$('#action-modal').modal('show');
$('#leaveid').val(id);
$('#daysmodal').val(days);
$('#updateid').val(updateid);
$('#leaveidmodal').val(leaveid);

$('#empidmodal').val(empid);
}

function modalshow(empid){
$.ajax({
      url: "{{url('/getleavebalance')}}",
      type: 'GET',
      data:{_token:"{{ csrf_token() }}",
      empid:empid,
      },
      success:function(result){
     if(result){
       $("#tblbalance tbody").empty();
       for(var count =0;count < result.length; count++){
            $("#tblbalance tbody").append(
              "<tr>" +
                "<td>"+result[count].leave_head+"</td>" +  
                "<td>"+result[count].leave_qty+"</td>" +  
                "<td>"+result[count].balance+"</td>" +  
              "</tr>"
             );
        }

      }
      }

  });
  $('#balance-modal').modal('show');

}

function updatestatus(id){
  let text = "";
  if (id == 1) {
    text =  "Approved";
  }
  else{
    text = "Reject";
  }
   swal({
          title: "Are you sure?",
          text: "Do you want to "+text+ " leave request?",
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
                        url: "{{url('/updateleavestatus')}}",
                        type: 'PUT',
                        data:{_token:"{{ csrf_token() }}",
                        id:$('#leaveid').val(),
                        empid:$('#empidmodal').val(),
                        days:$('#daysmodal').val(),
                        updateid:$('#updateid').val(),
                        statusid:id,
                        leaveid:$('#leaveidmodal').val(),
                        },
                        success:function(resp){
                          console.log(resp);
                            if(resp == 1){
                                 swal({
                                        title: "success",
                                        text: "Operation Performed!",
                                        type: "success"
                                   },function(isConfirm){
                                       if(isConfirm){
                                        window.location="{{ url('/showleaves') }}";
                                       }
                                   });
                             }
                        }

                    });
              
           }else {
              swal("Cancelled", "Operation Cancelled :)", "error");
           }
        });
  }

 </script>

@endsection
