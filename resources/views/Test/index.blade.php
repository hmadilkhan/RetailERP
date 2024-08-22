@extends('layouts.horizontal-layout')

@section('content') 

<section class="panels-wells">
  <div class="card">
   <div class="card-header">
     <h5 class="card-header-text">Purchases List</h5>
     <a href="{{ route('add-purchase') }}" class="btn btn-primary waves-effect waves-light f-right d-inline-block"> <i class="icofont icofont-plus m-r-5"></i>Create Purshase Order
     </a>
   </div>      
   <div class="card-block">
   </div>
  </div>
  </section>
  
@endsection