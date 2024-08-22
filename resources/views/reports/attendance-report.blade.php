@extends('layouts.master-layout')

@section('title','Reports')

@section('breadcrumtitle','Attendance Report')

@section('navattendance','active')
@section('navattereport','active')

@section('content')
<section class="panels-wells">
  <div class="card">
     <div class="card-header">
         <h1 class="card-header-text">Attendance Report</h1>
            <hr>
         <h5 class="card-header-text">Filter</h5>
         <div class="row">
                  <div class="col-lg-2 col-md-2">
            <div class="form-group">
                <label class="form-control-label">Select Branch</label>
                <select name="branch" id="branch" data-placeholder="Select Branch" class="form-control select2" onchange="getemp();" >
                    <option value="">Select Branch</option>
                    @if($branch)
                      @foreach($branch as $value)
                        <option value="{{ $value->branch_id }}">{{ $value->branch_name }}</option>
                      @endforeach
                    @endif
                </select>
                  </div>
                </div>
                <div class="col-lg-2 col-md-2">
            <div class="form-group">
                <label class="form-control-label">Select Employee</label>
                <select name="employee" id="employee" data-placeholder="Select Employee" class="form-control select2" >
                    <option value="">Select Employee</option>
                </select>
                  </div>
                </div>
                    <div class="col-lg-2 col-md-2">
                  <div class="form-group">
                      <label class="form-control-label">From Date</label>
                      <input class="form-control" type="text"
                       name="salarydatefrom" id="salarydatefrom" placeholder="DD-MM-YYYY" onchange="copydate()" />
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
                   <div class="col-lg-2 col-md-2">
            <div class="form-group">
                <label class="form-control-label">Select Approch</label>
                <select name="approch" id="approch" data-placeholder="Select Approch" class="form-control select2" >
                    <option value="">Select Approch</option>
                    <option value="1">Show Details</option>
                    <option value="2">Show Count</option>
                    
                </select>
                  </div>
                </div>
        <div class="col-lg-2  col-sm-2">
                    <div class="form-group">
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="getsheet()">
                                  <i class="icofont icofont-search"></i>
                            </button>
                    </div>       
                </div> 
           </div>
         </div>     
       <div class="card-block">
       
     <table id="tblsheet" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">

     <thead>
            <tr>
                 <th>Employee Name</th>
                 <th>Date</th>
                 <th>ClockIn</th>
                 <th>ClockOut</th>
                 <th>Late </th>
                 <th>Early</th>
                 <th>OverTime</th>
                 <th>ATT.Hrs</th>
            </tr>
         </thead>
         <tbody>
   
         </tbody>
        
      
     </table>
<br>
          <table id="tblcount" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0" style="display: none;">

     <thead>
            <tr>
               <th>Employee Name</th>
               <th>Present</th>
               <th>Absent</th>
               <th>Late </th>
               <th>Early</th>
               <th>OverTime</th>
            </tr>
         </thead>
         <tbody>
   
         </tbody>
        
      
     </table>

     <br>
     <div class="button-group ">
      <!--   <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>
          Export to Excel Sheet
      </button> -->
       <button type="button" id="btndraft" class="btn btn-md btn-success waves-effect waves-light f-right m-r-20" onclick="getpdf()"> <i class="icofont icofont-file-pdf"> </i>
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

 $('#tblsheet').DataTable({

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

  


function getpdf(){
  let branch = $('#branch').val();
    let emp = $('#employee').val();
    let date = $('#salarydatefrom').val();
    let todate = $('#salarydateto').val();
    let approch = $('#approch').val();
  window.location = "{{url('pdfattendance')}}?fromdate="+date+"&todate="+todate+"&branchid="+branch+"&empid="+emp+"&approchid="+approch;
}



function getsheet(){
    if ($('#branch').val() == '') {
  swal({
          title: "Error Message",
          text: "Please Select Branch First!",
          type: "error"
     });
     }
     else if ($('#approch').val() == ''){
      swal({
          title: "Error Message",
          text: "Please Select Approch!",
          type: "error"
     });
     }
     else{

      let empid = $('#employee').val();
      if (empid == "All") {
        empid = '';
      }
                  $.ajax({
                        url : "{{url('/attendancerpt')}}",
                        type : "GET",
                        data : {_token : "{{csrf_token()}}",
                        branchid:$('#branch').val(),
                        fromdate:$('#salarydatefrom').val(),
                        todate:$('#salarydateto').val(),
                        empid:empid,
                        approchid:$('#approch').val(),
                      },
                        dataType: 'json',
                        success : function(result){
                        if ($('#approch').val() == 1) {
                          $("#tblsheet tbody").empty();
                          $("#tblsheet thead").empty();
                          $("#tblsheet thead").append(
                               "<tr>"+
                                   "<th>Employee Name</th>"+
                                   "<th>Date</th>"+
                                   "<th>ClockIn</th>"+
                                   "<th>ClockOut</th>"+
                                   "<th>Late </th>"+
                                   "<th>Early</th>"+
                                   "<th>OverTime</th>"+
                                   "<th>ATT.Hrs</th>"+
                               "</tr>"
                           );
                        }
                        else{
                          $("#tblsheet tbody").empty();
                          $("#tblsheet thead").empty();
                           $("#tblsheet thead").append(
                               "<tr>"+
                                 "<th>Employee Name</th>"+
                                 "<th>Present</th>"+
                                 "<th>Absent</th>"+
                                 "<th>Late </th>"+
                                 "<th>Early</th>"+
                                 "<th>OverTime</th>"+
                               "</tr>"
                           );

                        }
                        
                   for(var count =0;count < result.length; count++){
                    if ($('#approch').val() == 1) {
                        $("#tblsheet tbody").append(
                          "<tr>" +
                            "<td class='pro-name' >"+result[count].emp_name+"</td>" +
                            "<td>"+result[count].date+"</td>" +  
                            "<td>"+result[count].clock_in+"</td>" +  
                            "<td>"+result[count].clockout+"</td>" +  
                            "<td>"+result[count].lates+" mint</td>" +  
                            "<td>"+result[count].earlys+" mint</td>" +  
                            "<td>"+result[count].ot+" mint</td>" +  
                            "<td>"+result[count].Atttime+"</td>" +  
                          "</tr>" 
                         );
                        }
                        else{
                          
                          $("#tblsheet tbody").append(
                          "<tr>" +
                            "<td class='pro-name' >"+result[count].emp_name+"</td>" +
                            "<td>"+result[count].present+"</td>" +  
                            "<td>"+result[count].absent+"</td>" +  
                            "<td>"+result[count].late+" mint</td>" +  
                            "<td>"+result[count].early+" mint</td>" +  
                            "<td>"+result[count].ot+" mint</td>" +  
                          "</tr>" 
                         );
                        }
                    }

                          }
                        });  
      
                  }
                }
 function copydate(){
  let date = $('#salarydatefrom').val();
  $('#salarydateto').val(date);
 }


 function getemp(){
  
   $.ajax({
            url: "{{url('/getemployees')}}",
            type: 'GET',
          data:{_token:"{{ csrf_token() }}",
          dataType:"json",
          branchid:$('#branch').val(),
        },
            success:function(resp){   
            $("#employee").empty();   
              if (resp != '') {
              $("#employee").append("<option value=''>Select Employee </option>");
            $("#employee").append("<option value='All'>All</option>");
                     for(var count=0; count < resp.length; count++){
                      $("#employee").append(
                        "<option value='"+resp[count].empid+"'>"+resp[count].emp_name+"</option>");
                  }
              }
             }
          }); 
 }

</script>
@endsection