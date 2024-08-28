@extends('layouts.master-layout')

@section('title','Payroll')

@section('breadcrumtitle','Salary Details')

@section('navpayroll','active')

@section('navsaldetails','active')

@section('content')

<section class="panels-wells">
  <div class="card">
     <div class="card-header">
         <h1 class="card-header-text">Salary Details</h1>
            <hr>
         <h5 class="card-header-text">Filter</h5>
         <div class="row">
                  <div class="col-lg-3 col-md-3">
            <div class="form-group">
                <label class="form-control-label">Select Branch</label>
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" >
                    <option value="">Select Branch</option>
                    @if($branch)
                      @foreach($branch as $value)
                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                      @endforeach
                    @endif
                </select>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3">
            <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" >
                    <option value="">Select Employee</option>
                    @if($emp)
                      @foreach($emp as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_name }}</option>
                      @endforeach
                    @endif
                </select>
                  </div>
                </div>
                    <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label class="form-control-label">From Date</label>
                      <input class="form-control" type="text"
                       name="salarydatefrom" id="salarydatefrom" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
                 <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label class="form-control-label">To Date</label>
                      <input class="form-control" type="text"
                       name="salarydateto" id="salarydateto" placeholder="DD-MM-YYYY"/>
                        <div class="form-control-feedback"></div>
                  </div>
             </div>
        <div class="col-lg-2  col-sm-2">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="getdata()">
                                  <i class="icofont icofont-search"></i>
                            </button>
                    </div>       
                </div> 
           </div>
         </div>     
       <div class="card-block">
       
     <table id="tblsalary" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

         <thead>
            <tr>
              <th>Image</th>
               <th>Code | Employee Name</th>
               <th>Branch</th>
               <th>Payslip Date</th>
               <th>Gross</th>
               <th>Deduction</th>
               <th>Special</th>
               <th>Net</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
           @foreach($details as $value)
                 <tr>
                     <td class="text-center">
                    <img width="42" height="42" src="{{ asset('assets/images/employees/images/'.(!empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg').'') }}" class="d-inline-block img-circle " alt="{{ !empty($value->emp_picture) ? $value->emp_picture : 'placeholder.jpg' }}"/>
                      </td>
                   <td >{{$value->emp_acc}} | {{$value->emp_name}}</td>
                   <td >{{$value->branch_name}}</td>
                   <td >{{$value->date}}</td>
                   <td >{{$value->gross_salary}}</td>
                  <td >{{$value->deduction_salary}}</td>
                  <td >{{$value->special_amount}}</td>
                  <td >{{$value->net_salary}}</td>
                    <td class="action-icon">

                   <a  class="m-r-10" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pdf" onclick="getpdf('{{$value->empid}}','{{$value->date}}','{{$value->date}}')"><i class="icofont icofont-file-pdf text-danger f-18" ></i> </a>
                 </td>          
                 </tr>
                  @endforeach
         </tbody>
        
      
     </table>
     <br>
 <!--     <div class="button-group ">
        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>
          Export to Excel Sheet
      </button>
       <button type="button" id="btndraft" class="btn btn-md btn-default waves-effect waves-light f-right m-r-20" onclick="getpdf()"> <i class="icofont icofont-file-pdf"> </i>
          Print Pdf
      </button>
         </div> -->  
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

  $('#salarydatefrom, #salarydateto').bootstrapMaterialDatePicker({
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
  
  $("#branch").change(function(){
	  $("#employee").empty();
	  $.ajax({
        url:'{{ url("/get-employees-by-branch") }}',
        type:"POST",
        data:{_token : "{{csrf_token()}}",
			branch:$(this).val(),
		},
		success:function(result){
      // $('#branch').val('').change();
		   if(result.status == 200){
				$.each(result.employees , function(index, val) { 
				  $("#employee").append("<option value="+val.empid+">"+val.emp_name+"</option>");
				});
		   }
		}
	}); 
  });

  function getdata(){
    let branch =0;
    let emp =0;
    let date = '';
    let todate = '';
    if ($('#branch').val() > 0) {
      branch = $('#branch').val();
      emp = '';
    }
    else{
      branch = '';
    }
     if ($('#employee').val() > 0) {
      emp = $('#employee').val();
      branch= '';
    }
    else{
      emp = '';
    }
    if ($('#salarydatefrom').val() != '') {
      date = $('#salarydatefrom').val();
      todate = $('#salarydateto').val();
    }
    else{
      date = '';
      todate = '';
    }
    $.ajax({
        url:'{{ url("/getsalarydetails") }}',
        type:"GET",
        data:{_token : "{{csrf_token()}}",
        branchid:branch,
        empid:$('#employee').val(),
        fromdate:date,
        todate:todate,
      },
    success:function(result){
      // $('#branch').val('').change();
      if(result){
                   $("#tblsalary tbody").empty();
                   for(var count =0;count < result.length; count++){
                        $("#tblsalary tbody").append(
                          "<tr>" +
                          "<td class='text-center'><img width='42' height='42' class='d-inline-block img-circle' src='assets/images/employees/images/"+((result[count].emp_picture != "") ? result[count].emp_picture : 'placeholder.jpg')+"' alt='"+result[count].emp_picture+"'/></td>" +
                            "<td>"+result[count].emp_acc+" | "+result[count].emp_name+"</td>" +  
                            "<td>"+result[count].branch_name+"</td>" +  
                            "<td>"+result[count].date+"</td>" +  
                            "<td>"+result[count].gross_salary+"</td>" +  
                            "<td>"+result[count].deduction_salary+"</td>" +  
                            "<td>"+result[count].special_amount+"</td>" +  
                            "<td>"+result[count].net_salary+"</td>" +  
                            "<td class='action-icon'><a  class='m-r-10' data-toggle='tooltip' data-placement='top' data-original-title='Pdf' onclick='getpdf("+result[count].empid+","+"\""+ result[count].date + "\","+"\""+ result[count].date + "\")'><i class='icofont icofont-file-pdf text-danger f-18'></i> </a></td>"+
                          "</tr>"
                         );
                    }
                  }
     }
   }); 
  }

  

function getpdf(empid,fromdate,todate){
 window.location = "{{url('getpdf')}}?empid="+empid+"&fromdate="+fromdate+"&todate="+todate;
}
 
</script>
@endsection
