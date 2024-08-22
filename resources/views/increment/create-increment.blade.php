@extends('layouts.master-layout')

@section('title','Employee Increment')

@section('breadcrumtitle','Increment Details')

@section('navincrement','active')


@section('content')


 <section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Make Increment</h5>
          <h6 class=""><a href="{{ url('/showincrement') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         </div>     

       <div class="card-block">
<div class="row">
             <div class="col-lg-5 col-md-5">
           <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" onchange="getbasicsal()" >
                    <option value="">Select Employee</option>
                    @if($getemp)
                      @foreach($getemp as $value)
                        <option value="{{ $value->empid }}">{{$value->emp_acc}} | {{$value->emp_name}} | {{$value->department_name}} | {{$value->branch_name}}</option>
                      @endforeach
                    @endif
                </select>
        </div>
        </div>

        <div class="col-lg-2 col-md-2">
         <div class="form-group">
      <label class="form-control-label">Previous Basic Salary</label>
     <h1 class="text-success f-30" id="basicsalary" style="margin-top: -20px;" >00</h1>
        </div>
        </div>

        <div class="col-lg-3 col-md-3">
         <div class="form-group">
      <label class="form-control-label">Increment Amount | Percentage</label>
     <input type="Number" class="form-control" name="percen" id="percen" placeholder="Enter Percentage" onchange="calper()"><br>
      <input type="Number" class="form-control" name="amt" id="amt" placeholder="Enter Amount" onchange="calamt()"><br>
        </div>
</div>
  

               <div class="col-lg-2 col-md-2">
         <div class="form-group">
      <label class="form-control-label">New Basic Salary</label>
     <h1 class="text-info f-30" id="finalsalary" style="margin-top: -20px;" >00</h1>
        </div>
        </div>
            
        
           </div>
           <div id="chkbox">
          <div class="rkmd-checkbox checkbox-rotate">
                     <label class="input-checkbox checkbox-primary">
                    <input type="checkbox" id="chkactive" class="mainchk">
                    <span class="checkbox"></span>
                      </label>
                     <div class="captions">Show Tax Slabs</div>
                  </div>
           </div>
           <hr>

           <div class="row" id="dvtaxes">
             <div class="col-lg-5 col-md-5" >
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
                <input type="hidden" name="taxper" id="taxper" value="{{ (count($taxslabs) != 0 ? $taxslabs[0]->percentage : '')}}" />
                        </div>
                      </div>
           <div class="col-lg-3 col-md-3">
         <div class="form-group">
      <label class="form-control-label">Old Tax Deduction Amount</label>
     <h1 class="text-success f-30" id="previoustaxrate" style="margin-top: -20px;" >00</h1>
        </div>
        </div>

               <div class="col-lg-3 col-md-3">
         <div class="form-group">
 <label class="form-control-label">New Tax Deduction Amount</label>
     <h1 class="text-info f-30" id="newtaxrate" style="margin-top: -20px;" >00</h1>
        </div>
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
     </div>
     <div class="row in">
             <div class="col-lg-12 col-sm-12 m-t-50">
                <div class="button-group ">
                      <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="submitincrement()"> <i class="icofont icofont-tick-mark"> </i>
                        Give Increment
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



 

@endsection

@section('scriptcode_three')

<script type="text/javascript">
  $(".select2").select2();



function getbasicsal(){
  $('#previoustaxrate').html('00');
   $.ajax({
          url: "{{url('/getbasicsalary')}}",
          type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          empid:$('#employee').val(),
          },
          success:function(resp){
            $('#basicsalary').html(resp[0].basic_pay);
            if (resp[0].tax_applicable_id == 1) {
              $('#dvtaxes').show();
              $('#chkbox').hide();
              $.ajax({
                url:"{{url('/gettaxslab_byempid')}}",
                type:'GET',
                data:{_token:"{{csrf_token()}}",
                empid:$('#employee').val(),
              },
              success:function(resp){
              $('#taxslab').val(resp[0].tax_id).change();
              $('#previoustaxrate').html(resp[0].tax_amount);
              }
              });
            }
            else{
              $('#chkbox').show();
             $('#dvtaxes').hide(); 
            }

          }
      });

   //get allowance
  gettbl_allowance($('#employee').val());

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

function edit(id,allowance,amount,allowance_id){
  $('#amountmodal').val(amount);
  $('#allowanceid').val(id);
  $('#allowancename').html(allowance);
  $('#allowanceheadid').val(allowance_id);
  $("#update-modal").modal("show");
}

function calper(){
  let per = $('#percen').val();
  let basic = $('#basicsalary').html();
  let amt = (parseInt(basic) * parseInt(per))/100;
  let newsal = (parseInt(amt) + parseInt(basic));
  $('#amt').val(amt);
  $('#finalsalary').html(newsal);
  getnewtaxrate();

}

function getnewtaxrate(){
  let annual = (parseInt($('#finalsalary').html()) * 12);
  let taxamt = (((annual * parseInt($('#taxper').val())) / 100) / 12);
  $('#newtaxrate').html(taxamt);
}


function calamt(){
   let amt = $('#amt').val();
  let basic = $('#basicsalary').html();

  let per = (parseInt(amt) / parseInt(basic))*100;
  let newsal = (parseInt(amt) + parseInt(basic));
  $('#percen').val(per);
  $('#finalsalary').html(newsal);
  getnewtaxrate();
}

$('#dvtaxes').hide();

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
                           }
                       });
                 }
            }

        });
  }


}

function submitincrement(){
    if ($('#employee').val() == "") {
    swal({
            title: "Error Message",
            text: "Please Select Employee First!",
            type: "error"
            });
  }
  // else if ($('#finalsalary').val() == "") {
  //  swal({
  //           title: "Error Message",
  //           text: "Operation Failed!",
  //           type: "error"
  //           }); 
  // }
  else{
    let taxid = 0;
    if ($('#taxslab').val() == "") {
      taxid = 0;
    }
    else{
      taxid = 1;
    }
      $.ajax({
            url: "{{url('/store_increment')}}",
            type: 'POST',
            data:{_token:"{{ csrf_token() }}",
            empid:$('#employee').val(),
            basicpay:$('#finalsalary').html(),
            tax:taxid,
            taxslabid:$('#taxslab').val(),
            taxamt:$('#newtaxrate').html(),
            },
            success:function(resp){
              console.log(resp);
                if(resp == 1){
                     swal({
                            title: "Success",
                            text: "Increment Generated Successfully!",
                            type: "success"
                       },function(isConfirm){
                           if(isConfirm){
                window.location= "{{ url('/showincrement') }}";
                           }
                       });
                 }
                 else if (resp == 0) {
                   swal({
                        title: "Error",
                        text: "Operation Failed!",
                        type: "error"
                       });
                 }
            }

        });
    }
  }




  $('#chkactive').change(function(){
  if (this.checked) {
    $('#dvtaxes').show();
    $('#taxslab').val('').change();
  }
  else{
   $('#dvtaxes').hide(); 
  }
});

  $('#chkbox').hide();


 </script>


@endsection
