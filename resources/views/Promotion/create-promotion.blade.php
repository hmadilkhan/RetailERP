@extends('layouts.master-layout')

@section('title','Employee Promotion')

@section('breadcrumtitle','Promotion Details')

@section('navpromotion','active')

@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
	      <div class="form-control-feedback text-danger"><h5>{{Session::get('status').$status}}</h5></div>
         <h5 class="card-header-text">Promotion</h5>
          <h6 class=""><a href="{{ url('/showpromotion') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>     

       <div class="card-block">
<div class="row">
  <input type="hidden" name="allowid" id="allowid" value="0">
             <div class="col-lg-12 col-md-12">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                <select name="employees" id="employees" data-placeholder="Select Employee" class="form-control select2" onchange="getolddetails()" >
                    <option value="">Select Employee</option>
                    @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{$value->emp_acc}} | {{$value->emp_name}} | {{$value->department_name}} | {{$value->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>
           </div>

           <hr>
<div class="row">
  <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18">Designation:</label>
  </div>
   <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18 text-success" id="olddesg">----</label>
  </div>
  <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18">Basic Salary:</label>
  </div>
   <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18 text-success" id="oldsalary">----</label>
  </div>
    <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18">Tax Rate:</label>
  </div>
   <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18 text-success" id="oldtax">----</label>
  </div>
</div>
<div class="row">
     <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18">OT Amount:</label>
  </div>
   <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18 text-success" id="oldotamt">----</label>
  </div>
     <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18">OT Duration:</label>
  </div>
   <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18 text-success" id="oldotduration">----</label>
  </div>
    <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18">Sal Category:</label>
  </div>
   <div class="col-md-2 col-lg-2">
    <label class="form-control-label f-18 text-success" id="oldcat">----</label>
  </div>
</div>

           <hr>
           <div>  
           
           <table id="tblallowance" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
              <th>Allowance Head</th>
               <th>Amount</th>
               <th>Date</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
        <tbody>
          
         </tbody>
        
     </table>
   <button type="button" id="btnallowance" class="btn btn-md btn-info waves-effect waves-light f-right"> <i class="icofont icofont-plus"> </i>Add Allowance</button>
    
     </div>

<h3 style="color: gray;">Promotion Types & Factors</h3>
     <div class="row">
      <hr class="m-t-25">

              <div class="col-lg-4 col-md-4">
                <div class="form-group">
                <label class="form-control-label">Designation</label>
                <select name="designation" id="designation" data-placeholder="Select Designation" class="form-control select2" >
                    <option value="">Select Designation</option>
                </select>
                 <div class="form-control-feedback"></div>
                  </div>
              </div>
               <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Salary Category:</label>
                     <select name="cat" id="cat" data-placeholder="Select Category" class="form-control select2" onchange="place()" >
                    <option value="">Select Category</option>
                     @if($category)
                      @foreach($category as $value)
                        <option value="{{ $value->id }}">{{ $value->category }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                 <div class="col-lg-4 col-md-4">
           <div class="form-group {{ $errors->has('basicpay') ? 'has-danger' : '' }} ">
            <label class="form-control-label">Basic Salary | Per day Salary</label>
            <input type="number" name="basicpay" id="basicpay" placeholder="100"   class="form-control" value="{{ old('basicpay') }}"/>
             @if ($errors->has('basicpay'))
                <div class="form-control-feedback">Required field can not be blank.</div>
            @endif
        </div>
        </div>
            
     </div>
     <div class="row">
<div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Over Time Amount:</label>
                     <select name="otamount" id="otamount" data-placeholder="Over Time Amount" class="form-control select2" >
                    <option value="">Over Time Amount</option>
                       @if($otamount)
                      @foreach($otamount as $value)
                        <option value="{{ $value->otamount_id }}">{{ $value->amount }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                             <div class="col-lg-4 col-md-4">
                      <div class="form-group"> 
                        <label class="form-control-label">Over Time Count Duration:</label>
                     <select name="otduration" id="otduration" data-placeholder="Over Time Duration" class="form-control select2" >
                    <option value="">Over Time Duration</option>
                       @if($otduration)
                      @foreach($otduration as $value)
                        <option value="{{ $value->otduration_id }}">{{ $value->duration." minutes"}}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>

                      <div class="col-lg-4 col-md-4" id="dvtaxes">
                      <div class="form-group"> 
                        <label class="form-control-label">Tax Slab:</label>
                     <select name="taxslab" id="taxslab" data-placeholder="Select Tax Slab" class="form-control select2" >
                    <option value="">Select Tax Slab</option>
                       @if($taxslabs)
                      @foreach($taxslabs as $value)
                        <option value="{{ $value->tax_id }}">{{ $value->slab_min }} -- {{ $value->slab_max}}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
                  </div>
     <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="submitpromotion()"> <i class="icofont icofont-tick-mark"> </i>
                        Promote Employee
                    </button>
                    </div>       
                </div>  
            </div> 
   
  </div>
</div>

</section>
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
              <h1 class="f-30 text-primary text-center" id="allowancename"></label>
                      </h1>
             <div class="row"> 
            <div class="col-lg-12 col-md-12">
                      <div class="form-group"> 
             <label class="form-control-label">Amount</label>
           <input type="Number" name="amountmodal" id="amountmodal" class="form-control" value="" />
         <input type="hidden" name="allowanceid" id="allowanceid" class="form-control" value="" />
         <input type="hidden" name="allowanceheadid" id="allowanceheadid" value="">
                        </div>
                      </div>
              </div>
         </div>
             <div class="modal-footer">
                <button type="button" id="btn_desg" class="btn btn-primary waves-effect waves-light" onClick="update()">Update Allowance</button>
             </div>
          </div>
           </div>
        </div> 



 <!-- modals -->
  <div class="modal fade modal-flex" id="allowance-modal" tabindex="-1" role="dialog">
       <div class="modal-dialog modal-md" role="document">
          <div class="modal-content">
    <form method="post" id="upload_form" enctype="multipart/form-data">
        {{ csrf_field() }}
             <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <h4 class="modal-title">Allowance Modal</h4>
             </div>
             <div class="modal-body">
      <input type="hidden" name="employee" id="employee" value="">
              <div class="row">
             <div class="col-lg-6 col-md-6">
                      <div class="form-group"> 
                        <label class="form-control-label">Select Allowance Head:</label>
                     <select name="allowancehead" id="allowancehead" data-placeholder="Select Allowance" class="form-control select2" >
                    <option value="">Select Allowance</option>
                       @if($allowance)
                      @foreach($allowance as $value)
                        <option value="{{ $value->allowance_id }}">{{ $value->allowance_name }}</option>
                      @endforeach
                    @endif
                </select>
                        </div>
                      </div>
             <div class="col-lg-6 col-md-6">
           <div class="form-group">
            <label class="form-control-label">Amount:</label>
            <input type="text" name="amount" id="amount" class="form-control" />
        </div>
        </div>
              </div>

             </div>
             <div class="modal-footer">
             <button type="Submit" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" > <i class="icofont icofont-tick-mark"> </i> Submit </button>
             </div>
           </form>
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



function getolddetails(){
  
   $.ajax({
          url: "{{url('/getoldetails')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          empid:$('#employees').val(),
          },
          success:function(resp){
           $('#olddesg').html(resp[0].designation_name);
           $('#oldsalary').html(resp[0].basic_pay);
           $('#oldtax').html(resp[0].tax_amount);
           $('#oldotamt').html(resp[0].ot_amount);
           $('#oldotduration').html(resp[0].ot_duration);
           $('#oldcat').html(resp[0].salary_cat);
           $('#employee').val($('#employees').val());
          }
      });
   //get allowance
  gettbl_allowance($('#employees').val());

  //get designation
  getdesg($('#employees').val());
}

function getdesg(empid){
    $.ajax({
          url: "{{url('/getdesigbyempid')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          empid:empid,
        },
            success:function(resp){    
            $("#designation").empty();         
             for(var count=0; count < resp.length; count++){
             $("#designation").append("<option value=''>Select Designation</option>");
              $("#designation").append(
               "<option value='"+resp[count].designation_id+"'>"+resp[count].designation_name+"</option>");
                     }
             }

          }); 
}

function gettbl_allowance(employeeid){
    $.ajax({
            url: "{{url('/getallowance_byempid')}}",
            type: 'GET',
            dataType:"json",
            data:{_token:"{{ csrf_token() }}",
            empid:employeeid,
          },
            success:function(result){
                if(result){
                 $("#tblallowance tbody").empty();
                    for(var count =0;count < result.length; count++){
                        $("#tblallowance tbody").append(
                          "<tr>" +
                            "<td>"+result[count].allowance_name+"</td>" +  
                            "<td>"+result[count].amount+"</td>" +  
                            "<td>"+result[count].date+"</td>" +  
                            "<td>"+result[count].status_name+"</td>" +  
                            "<td class='action-icon'><i onclick='edit("+result[count].id+","+"\""+ result[count].allowance_name + "\","+"\""+ result[count].amount + "\","+result[count].allowance_id+")'class='icofont icofont-ui-edit text-primary f-18' data-toggle='tooltip' data-placement='top' title='' data-original-title='Edit'></i></td>"+
                          "</tr>" 
                         );
                    }
             }
           }
          });
}


function place(){
  if ($('#cat').val() == 1) {
  $("#basicpay").attr("placeholder","Please Enter One Day Salary");  
  }
  else{
    $("#basicpay").attr("placeholder","Please Enter Month Salary");  
  }
  
}

$('#btnallowance').click(function(){
$('#allowance-modal').modal("show");
});


$('#upload_form').on('submit', function(event){
event.preventDefault();
  if ($('#employees').val() == "") {
     swal({
            title: "Error Message",
            text: "Please Select Employee!",
            type: "error"
              });
  }
  else if ($('#allowancehead').val() == "") {
     swal({
            title: "Error Message",
            text: "Allowance Head Can not left blank!",
            type: "error"
              });
  }
  else if ($('#amount').val() == "" || $('#amount').val() == 0) {
     swal({
            title: "Error Message",
            text: "Amount Can not left blank!",
            type: "error"
              });
  }
  else{
    $.ajax({
       url: "{{url('/storeallowancedetails')}}",
       method: 'POST',
       data: new FormData(this),
         contentType: false,
         cache: false,
         processData: false,
         
    success:function(resp){
        if(resp != 0){
             swal({
                    title: "Success",
                    text: "Employee Allowance Added Successfully!",
                    type: "success"
               },function(isConfirm){
                   if(isConfirm){
         gettbl_allowance($('#employees').val());
         $('#allowance-modal').modal("hide");
         $('#allowid').val(1);
                   }
               });
          }
              else{
                    swal({
                      title: "Already exsit",
                      text: "Particular Allowance Already exsit!",
                      type: "warning"
                       });
                  }
     }

  });   
  }
}); 

function edit(id,allowance,amount,allowance_id){
  $('#amountmodal').val(amount);
  $('#allowanceid').val(id);
  $('#allowancename').html(allowance);
  $('#allowanceheadid').val(allowance_id);
  $("#update-modal").modal("show");
}

function update(){
  if ($('#amountmodal').val() == "" || $('#amountmodal').val() == 0 ) {
    swal({
            title: "Error Message",
            text: "Enter Amount was incorrect!",
            type: "error"
        });
  }
  else{
     $.ajax({
            url: "{{url('/allowance_increment')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            id:$('#allowanceid').val(),
            amount:$('#amountmodal').val(),
            employee:$('#employee').val(),
            allowancehead:$('#allowanceheadid').val(),
            },
            success:function(resp){
                if(resp){
                     swal({
                            title: "Success",
                            text: "Amount Updated Successfully!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                            $("#update-modal").modal("hide");
                             gettbl_allowance($('#employee').val());
                             $('#allowid').val(1);
                           }
                       });
                 }
            }

        });
  }


}

function submitpromotion(){
  if ($('#employees').val() == "") {
     swal({
            title: "Error Message",
            text: "Please Select Employee!",
            type: "error"
          });
  }
  else if ($('#cat').val() != "" && $('#basicpay').val() == "" && $('#basicpay').val() == 0) {
    
      swal({
            title: "Error Message",
            text: "Please Enter Salary!",
            type: "error"
          });
    
  }
  else if ($('#otamount').val() != "" && $('#otduration').val() == "") {
      swal({
            title: "Error Message",
            text: "Please Select Over Time Duration!",
            type: "error"
          });
  }
  else if ($('#taxslab').val() != "" && $('#basicpay').val() == "" && $('#basicpay').val() == 0) {
      swal({
            title: "Error Message",
            text: "Please Enter Salary!",
            type: "error"
          });
  }
  else{
     $.ajax({
            url: "{{url('/promotion')}}",
            type: 'PUT',
            data:{_token:"{{ csrf_token() }}",
            desg:$('#designation').val(),
            salcat:$('#cat').val(),
            basicpay:$('#basicpay').val(),
            otamount:$('#otamount').val(),
            otduration:$('#otduration').val(),
            taxslab:$('#taxslab').val(),
            empid:$('#employees').val(),
            allowance:$('#allowid').val(),
            },
            success:function(resp){
              console.log(resp);
                if(resp){
                     swal({
                      title: "Success",
                      text: "Promotion Letter Issue Successfully!",
                      type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                       window.location= "{{ url('/showpromotion') }}";
                           }
                       });
                 }
            }

        });
  }
}

 </script>


@endsection
