@extends('layouts.master-layout')

@section('title','Expense Report')

@section('breadcrumtitle','Expense Report')
@section('navaccountsoperation','active')
@section('navreports','active')

@section('expense_report','active')



@section('content')
<section class="panels-wells">
  <div class="card">
     <div class="card-header">
         <h1 class="card-header-text">Expense Report</h1>
            <hr>
         <h5 class="card-header-text">Filter</h5>
         <div class="row">
                <div class="col-lg-3 col-md-3">
                <div class="form-group">
                <label class="form-control-label">Select Expense Category</label>
                <select name="category" id="category" data-placeholder="Select Expense Category" class="form-control select2" >
                    <option value="">Select Expense Category</option>

                    @if($category)
                      @foreach($category as $value)
                        <option value="{{ $value->exp_cat_id }}">{{ $value->expense_category }}</option>
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
                     <th>Category</th>
                     <th>Details</th>
                     <th>Amount</th>
                  </tr>
               </thead>
               <tbody>
               
                  
              </tbody>

              
        
      
     </table>
     <br>
     <div class="button-group ">
        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>
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
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search Employee',
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
  getdata();
  function getdata(){ 
  
    $.ajax({
        url:'{{ url("/expense-report-details") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
        category:$('#category').val(),
        first:$('#from').val(),
        second:$('#to').val(),
      },
      success:function(result){
        console.log(result);

        if(result){
           var balance = 0;
             $("#tblsalary tbody").empty();
             for(var count = 0; count < result.length; count++){
              if(parseInt(result[count].net_amount) > 0)
              {
                balance += parseInt(result[count].net_amount);
              }
                  $("#tblsalary tbody").append(
                    "<tr>" +
                      "<td>"+(count + 1) +"</td>" +  
                      "<td>"+result[count].expense_category+"</td>" +  
                      "<td>"+result[count].expense_details+"</td>" +  
                      "<td>"+(result[count].net_amount) * (1).toLocaleString()+"</td>" +  
                    "</tr>"
                   );
              }
              $("#tblsalary tbody").append(
                    "<tr>" +
                      "<td>"+(count + 1 )+"</td>" +  
                      "<td></td>" +  
                      "<td class='f-24'>Total Balance</td>" +  
                      "<td class='f-24'>"+(balance).toLocaleString()+"</td>" +  
                    "</tr>"
                   );
              
            }
       }
  }); 
  }

  function generate_pdf()
  {
    window.location = "{{url('expense-report-pdf')}}?category="+$('#category').val()+"&first="+$('#from').val()+"&second="+$('#to').val();
  
  }


 

</script>
@endsection
