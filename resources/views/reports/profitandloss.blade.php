@extends('layouts.master-layout')

@section('title','Profit and Loss Report')

@section('breadcrumtitle','Profit and Loss Report')

@section('navaccountsoperation','active')
@section('navreports','active')

@section('nav_profit','active')



@section('content')
<section class="panels-wells">
  <div class="card">
     <div class="card-header">
         <h1 class="card-header-text">Profit and Loss Report</h1>
            <hr>
         <h5 class="card-header-text">Filter</h5>
         <div class="row">
              
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
                           <button type="button" id="btnSubmit"  class="btn btn-md btn-primary waves-effect waves-light m-t-25" onclick="generate_pdf()">
                                  <i class="icofont icofont-search"></i>&nbsp;Search
                            </button>
                    </div>       
                </div> 
           </div>
         </div>     
       <div class="card-block">
       <?php $total = 0; $no = 0; ?>
     
     <br>
     <!-- <div class="button-group ">
        <button type="button" id="btnsubmit" class="btn btn-md btn-success waves-effect waves-light f-right" onclick="alert('Work in process')"><i class="icofont icofont-file-excel"> </i>
          Export to Excel Sheet
      </button>
       <button type="button" id="btndraft" onclick="generate_pdf()" class="btn btn-md btn-danger waves-effect waves-light f-right m-r-20"> <i class="icofont icofont-file-pdf"> </i>
          Print Pdf
      </button>
         </div>   -->
  </div>
</div>
    </section>
    @endsection

@section('scriptcode_three')

<script type="text/javascript">

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

 
  function generate_pdf()
  {
    window.location = "{{url('profit-and-loss')}}?first="+$('#from').val()+"&second="+$('#to').val();
  
  }


 

</script>
@endsection
