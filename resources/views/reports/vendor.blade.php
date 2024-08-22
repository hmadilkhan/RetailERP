@extends('layouts.master-layout')

@section('title','Vendor Payable')

@section('breadcrumtitle','Vendor Payable')

@section('navaccountsoperation','active')
@section('navreports','active')
@section('navvendorreport','active')

@section('navvendorpay','active')

@section('content')
<section class="panels-wells">
  <div class="card">
     <div class="card-header">
         <h1 class="card-header-text">Vendor Payable</h1>
            <hr>
         <h5 class="card-header-text">Filter</h5>
         <div class="row">
                <div class="col-lg-3 col-md-3">
                <div class="form-group">
                <label class="form-control-label">Select Vendor</label>
                <select name="vendor" id="vendor" data-placeholder="Select Vendor" class="form-control select2" >
                    <option value="">Select Vendor</option>

                    @if($vendors)
                      @foreach($vendors as $value)
                        <option value="{{ $value->id }}">{{ $value->vendor_name }}</option>
                      @endforeach
                    @endif
                </select>
                  </div>
                </div>
                    <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label class="form-control-label">From Date</label>
                      <input class="form-control" type="text"
                       name="from" id="from" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
                 <div class="col-lg-3 col-md-3">
                  <div class="form-group">
                      <label class="form-control-label">To Date</label>
                      <input class="form-control" type="text"
                       name="to" id="to" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
        <div class="col-lg-2  col-sm-2">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="getdata()">
                                  <i class="icofont icofont-search"></i>&nbsp;Search
                            </button>
                    </div>       
                </div> 
           </div>
         </div>     
       <div class="card-block">
       <?php $total = 0; $no = 0; ?>
     <table id="tblsalary" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

               <thead>
                  <tr>
                     <th>S.No</th>
                     <th>Vendor Name</th>
                     <th>Contact</th>
                     <th>Balance</th>
                  </tr>
               </thead>
               <tbody>
                @foreach($details as $key => $value)
                 <tr>
                    <?php  $total = $total + ($value->balance * (-1)); $no++; ?>
                   <td >{{$key + 1}}</td>
                   <td >{{$value->vendor_name}}</td>
                   <td >{{$value->vendor_contact}}</td>
                   <td >{{number_format($value->balance  * (-1),2)}}</td>
                 </tr>
                  @endforeach
                  <tr >
                    <td>{{$no +1 }}</td>
                    <td></td>
                    <td class="f-24">Total Balance</td>
                    <td class="f-24">{{number_format($total,2)}}</td>
                  </tr>
                  
              </tbody>

              
        
      
     </table>
     <br>
     <div class="button-group ">
        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="generate_excel()"><i class="icofont icofont-file-excel"> </i>
          Export to Excel Sheet
      </button>
       <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
          Print Pdf
      </button>
         </div>  
  </div>
</div>
	</section>
	@endsection

@section('scriptcode_three')

<script type="text/javascript">
	$(".select2").select2();

      $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: true,
        language: {
          search:'', 
          searchPlaceholder: 'Search Vendors',
          lengthMenu: '<span></span> _MENU_'
   
        }

    });


  $('#from, #to').bootstrapMaterialDatePicker({
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

  function getdata(){
  
    $.ajax({
        url:'{{ url("/vendor-report-filter") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
        vendor:$('#vendor').val(),
        first:$('#from').val(),
        second:$('#to').val(),
      },
      success:function(result){
        if(result){
           var balance = 0;
             $("#tblsalary tbody").empty();
             for(var count = 0; count < result.length; count++){
              balance += result[count].balance;
                  $("#tblsalary tbody").append(
                    "<tr>" +
                      "<td>"+count +"</td>" +  
                      "<td>"+result[count].vendor_name+"</td>" +  
                      "<td>"+result[count].vendor_contact+"</td>" +  
                      "<td>"+(result[count].balance * (-1)).toLocaleString()+"</td>" +  
                    "</tr>"
                   );
              }
              $("#tblsalary tbody").append(
                    "<tr>" +
                      "<td>"+count + 1 +"</td>" +  
                      "<td></td>" +  
                      "<td class='f-24'>Total Balance</td>" +  
                      "<td class='f-24'>"+(balance * (-1)).toLocaleString()+"</td>" +  
                    "</tr>"
                   );
              
            }
       }
  }); 
  }

  function generate_pdf()
  {
    window.location = "{{url('payable')}}?vendor="+$('#vendor').val()+"&first="+$('#from').val()+"&second="+$('#to').val();
  }
  
  function generate_excel()
  {
    window.location = "{{url('export-vendor-ledger')}}?vendor="+$('#vendor').val()+"&first="+$('#from').val()+"&second="+$('#to').val();
  }


 

</script>
@endsection
