@extends('layouts.master-layout')

@section('title','Customer List')

@section('navcustomer','active')

@section('content')
 <section class="panels-wells">
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-text">Upload Customer</h5>    
            <a href="{{ route('customer.create') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Create Customer" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5" ></i> CREATE CUSTOMER</a>

            <button id="downloadsample" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Download Sample" class="btn btn-success waves-effect waves-light f-right d-inline-block m-r-10"> <i class="icofont icofont-plus m-r-5" ></i> Download Sample</button>
        </div>
        <div class="card-block">
            <div class="row col-md-12 " >
              <form method='post' action='{{url('uploadFile')}}' enctype='multipart/form-data' >
              {{ csrf_field() }}
               <div class="form-group{{ $errors->has('vdimg') ? 'has-danger' : '' }} ">
                   <label for="vdimg" class="form-control-label">Select File </label>
                  <br/>
                      <label for="vdimg" class="custom-file">
                         <input type="file" name="file" id="vdimg" class="custom-file-input">
                        <span class="custom-file-control"></span>
                      </label>
                      <input type='submit' class="btn btn-primary m-l-10 m-t-1" name='submit' value='Import'>
                </div>
                </form>
           </div>
        </div>
    </div>
	<div class="card">
		<div class="card-header">
		   <h5 class="card-header-text">Customer List</h5>
		   <hr/>
		</div>
		<div class="card-header">
			<div class="card-block">
				<div id="table_data">
					@include('partials.customer_table')
				</div>
			</div>
		</div>
	</div>	 
</section>
@endsection