@extends('layouts.master-layout')

@section('title','Job Order List')

@section('breadcrumtitle','Add Expense')

@section('navjoborder','active')
@section('navjobordercreate','active')
@section('content')
 <section class="panels-wells"> 
		@if($details)
    <div class="card">

     <div class="card-header">
         <h5 class="card-header-text">Job Order Details</h5>
         

      <h6 class=""><a href="{{ url('/joborder') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back">Back to list</i></a></h6>
         
         </div> 

       <div class="card-block">
       	           	<div class="row">
       	 <div class="col-lg-12 col-md-12" style="background-color: Linen;">
       	 	<center>
       	 		<br>
       	 		<label class="card-header-text" style="font-size: xx-large; font-weight: bold;">{{$details[0]->finish_good}}</label><br>
       	 		<br>
 			  <a href="#">
 	<img  src="{{ asset('assets/images/products/'.(!empty($details[0]->image) ? $details[0]->image : 'placeholder.jpg').'') }}" class="d-inline-block img-circle m-l-15" alt="{{ !empty($details[0]->image) ? $details[0]->image : 'placeholder.jpg' }}" style="width: 180px;height: 180px;" >
    </a>
    <br>
    <br>
    </center>
</div>
</div>   
       	<div class="row">
       	 <div class="col-lg-12 col-md-12">
       	 <div class="form-group">
 <div class="project-table">
          <table class="table table-striped nowrap dt-responsive" width="100%">
         <thead>
            <tr>   
               <th>Raw Material</th>
               <th>Usage Quantity</th>
               <th>Amount</th>
            </tr>
         </thead>
         <tbody>
          @if($details)
            @foreach($details as $value)
              <tr>
                <td>{{$value->raw_material}}</td>
                <td>{{$value->usage_qty}}</td>
                <td>{{$value->amount}}</td>
              </tr>
            @endforeach
          @endif
         </tbody>
     </table>
        </div>
         <div class="col-lg-4 col-sm-4 f-right" >
       <table id="" class="table table-responsive invoice-table invoice-total">
 
          <tbody>
             <tr>
                <th>Job Cost :</th>
                <td>{{$details[0]->job_cost}}</td>
             </tr>
             <tr>
                <th>Infra-Structure Cost :</th>
                <td>{{$details[0]->infrastructure_cost}}</td>
             </tr>
             <tr class="txt-info">
                <th><h5>Total Amount</h5></th>
                <td>{{$details[0]->job_cost + $details[0]->infrastructure_cost}}</td>
             </tr>
      
          </tbody>

       </table>
    </div> 
       	 </div>
       		</div>
       	</div>
  </div>
</div>
@endif
</section>
@endsection
@section('scriptcode_three')

  <script type="text/javascript">

  </script>
@endsection