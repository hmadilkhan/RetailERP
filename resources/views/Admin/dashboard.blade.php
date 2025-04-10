@extends('layouts.master-layout')

@section('title','Dashboard')

@section('breadcrumtitle','Dashboard')

@section('navdashboard','active')

@section('content')
  <!-- 4-blocks row start -->

            <div class="row dashboard-header">
               <div class="col-lg-3 col-md-4">
                  <div class="card dashboard-product">
                     <span>Products</span>
                     <h2 class="dashboard-total-products">4500</h2>
                     <span class="label label-warning">Sales</span>Arriving Today
                     <div class="side-box">
                        <i class="ti-signal text-warning-color"></i>
                     </div>
                  </div>
               </div>
               <div class="col-lg-3 col-md-4">
                  <div class="card dashboard-product">
                     <span>Products</span>
                     <h2 class="dashboard-total-products">37,500</h2>
                     <span class="label label-primary">Views</span>View Today
                     <div class="side-box ">
                        <i class="ti-gift text-primary-color"></i>
                     </div>
                  </div>
               </div>
               <div class="col-lg-3 col-md-4">
                  <div class="card dashboard-product">
                     <span>Products</span>
                     <h2 class="dashboard-total-products">$<span>30,780</span></h2>
                     <span class="label label-success">Sales</span>Reviews
                     <div class="side-box">
                        <i class="ti-direction-alt text-success-color"></i>
                     </div>
                  </div>
               </div>
               <div class="col-lg-3 col-md-4">
                  <div class="card dashboard-product">
                     <span>Products</span>
                     <h2 class="dashboard-total-products">$<span>30,780</span></h2>
                     <span class="label label-danger">Sales</span>Reviews
                     <div class="side-box">
                        <i class="ti-rocket text-danger-color"></i>
                     </div>
                  </div>
               </div>
            </div>
            <!-- 4-blocks row end -->
@endsection

@section('scriptcode_three')

<script type="text/javascript">


  <?php if(session('login_msg')) { ?> 
   $(document).ready(function(){

       notify('{{ session("login_msg") }}', 'success');
       <?php $_SESSION['login_msg'] = ''; ?>
   });

  <?php } ?>
</script>
 

@endsection
