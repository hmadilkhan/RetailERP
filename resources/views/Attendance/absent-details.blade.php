@extends('layouts.master-layout')

@section('title','Absent Details')

@section('breadcrumtitle','Absent Details')

@section('navattendance','active')

@section('navabsent','active')

@section('content')
<section class="panels-wells">
    <div class="card">
     <div class="card-header">
         <h5 class="card-header-text">Absent Details</h5>
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
                  @if($employee)
                      @foreach($employee as $value)
                        <option value="{{ $value->empid }}">{{ $value->emp_acc }} | {{ $value->emp_name }}</option>
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

     <table id="tblabsent" class="table dt-responsive table-striped nowrap" width="100%"  cellspacing="0">
        
         <thead>
            <tr>
               <th>Employee Code | Name</th>
               <th>Branch Name</th>
               <th>Shift Name</th>
               <th>Department Name</th>
               <th>Absent Date</th>
           
            </tr>
            </thead>
              <tbody>
            @if($details)
            @foreach($details as $value)
             <tr>
              <td>{{$value->emp_acc}} | {{$value->emp_name}}</td>
              <td>{{$value->branch_name}}</td>
              <td>{{$value->shiftname}}</td>
              <td>{{$value->department_name}}</td>
              <td class="card-header-text">{{$value->absent_date}}</td>
                    </tr>
            @endforeach
            @endif
              </tbody>
        
      
     </table>
  </div>
</div>
</section>
@endsection
@section('scriptcode_three')
<script type="text/javascript">
  $(".select2").select2();
     

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

      $('.table').DataTable({

        bLengthChange: true,
        displayLength: 10,
        info: false,
        language: {
          search:'', 
          searchPlaceholder: 'Search',
          lengthMenu: '<span></span> _MENU_'
   
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
				  $("#employee").append("<option val="+val.empid+">"+val.emp_name+"</option>");
				});
		   }
		}
	}); 
  });


function getdata() {
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
    //ye change krna ha namaz k bad
    $.ajax({
        url:'{{ url("/absent_details_filter") }}',
        type:"GET",
        data:{_token : "{{csrf_token()}}",
        branchid:branch,
        empid:emp,
        fromdate:date,
        todate:todate,
      },
    success:function(result){
      console.log(result);
      // $('#branch').val('').change();
      if(result){
                   $("#tblabsent tbody").empty();
                   for(var count =0;count < result.length; count++){
                        $("#tblabsent tbody").append(
                          "<tr>" +
                            "<td>"+result[count].emp_acc+" | "+result[count].emp_name+"</td>" +  
                            "<td>"+result[count].branch_name+"</td>" +
                            "<td>"+result[count].shiftname+"</td>" +  
                            "<td>"+result[count].department_name+"</td>" +  
                            "<td>"+result[count].absent_date+"</td>" +
                          "</tr>"
                         );
                    }
                  }
     }
  }); 
}      
  
</script>
@endsection

