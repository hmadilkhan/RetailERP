<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
	  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
	<title>@yield('title')</title>
	@include('Admin.partials.html-libs')


@yield('css_code')

@yield('scriptcode_one')

</head>

@yield('scriptcode_two')

<body class="sidebar-mini fixed">
   <div class="loader-bg">
      <div class="loader-bar">
      </div>
   </div>
   <!--wrapper-->
    <div class="wrapper">
		 <!-- Navbar header-->
		@include('Admin.partials.header')
		<!-- end Navbar header -->
			<!-- Side-Nav-->-->
			  @include('Admin.partials.side-bar-nav')
			<!-- end Side-Nav -->
           <div class="content-wrapper">
             <!-- Container-fluid starts -->
                    <!-- Main content starts -->
                       <div class="container-fluid">

                             <!-- start contect-->
                             
			                       @yield('content')
                               <!-- end contect-->
					
       
            </div>
           <!-- Main content ends -->
         <!-- Container-fluid ends -->
      </div>
   </div>

   <!-- Required Jqurey -->
@include('Admin.partials.js-libs')

@yield('scriptcode_three')

</body>

</html>   