@extends('layouts.master-layout')

@section('title','Vendor')

@section('breadcrumtitle','View Vendor')
@section('navVendorPO','active')
@section('navvendor','active')

@section('content')
  <section class="panels-wells">
    <div class="card">
     <div class="card-header">
          <h5 class=""><a href="{{ route('vendors.index') }}"><i class="text-primary text-center icofont icofont-arrow-left p-r-20 f-18" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back"> BACK</i></a></h5>
         <h5 class="card-header-text">Vendor Details</h5>
         </div>      
       <div class="card-block">

                  <div class="col-xl-4 col-lg-12 grid-item">
                        <div class="card-block text-center">
                           <div class="horizontal-card-img">
                              <h2>{{ $vendor->vendor_name }}</h2>
                              <img class="media-object img-circle" src="{{ asset('assets/images/vendors/'.(empty($vendor->image) ? 'placeholder.jpg' : $vendor->image) .'') }}" alt="{{ empty($vendor->image) ? 'placeholder.jpg' : $vendor->image }}">
                           </div>
                            <div class="row following">
                                <table class="table">
                                  <tr>
                                    <th>Email</th>
                                    <td>{{ $vendor->vendor_email }}</td>
                                  </tr>
                                  <tr>  
                                    <th>Contact</th>
                                    <td>{{ $vendor->vendor_contact }}</td>
                                  </tr>
                                  <tr>
                                    <th>Address</th>
                                    <td>{{ $vendor->address }}</td>
                                  </tr>
                                </table>
                           </div>                          
                        </div>
                  </div>


                                    <div class="col-xl-4 col-lg-12 grid-item">
                        <div class="card-block text-center">
                           <div class="horizontal-card-img">
                              <h2>{{ $vendor->vendor_name }}</h2>
                              <img class="media-object img-circle" src="{{ asset('assets/images/vendors/'.$vendor->image.'') }}" alt="Generic placeholder image">
                           </div>
                            <div class="row following">
                                <table class="table">
                                  <tr>
                                    <th>Email</th>
                                    <td>{{ $vendor->vendor_email }}</td>
                                  </tr>
                                  <tr>  
                                    <th>Contact</th>
                                    <td>{{ $vendor->vendor_contact }}</td>
                                  </tr>
                                  <tr>
                                    <th>Address</th>
                                    <td>{{ $vendor->address }}</td>
                                  </tr>
                                </table>
                           </div>                          
                        </div>
                  </div>
         
    </div>
   </div>
</section>
@endsection

@section('scriptcode_three')


@endsection
