<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
	<title>@yield('title')</title>
	@include('partials.html-libs')
    @yield('css_code')
    @yield('scriptcode_one')
	<style>
/*
		.loader {
			width: 40px;
			height: 40px;
			--c:no-repeat linear-gradient(green 0 0);
			background: var(--c),var(--c),var(--c),var(--c);
			background-size: 21px 21px;
			animation: l5 1.5s infinite cubic-bezier(0.3,1,0,1);
			margin:auto;
			left:0;
			right:0;
			top:0;
			bottom:0;
			position:fixed;
		}
		@keyframes l5 {
		   0%   {background-position: 0    0,100% 0   ,100% 100%,0 100%}
		   33%  {background-position: 0    0,100% 0   ,100% 100%,0 100%;width:60px;height: 60px}
		   66%  {background-position: 100% 0,100% 100%,0    100%,0 0   ;width:60px;height: 60px}
		   100% {background-position: 100% 0,100% 100%,0    100%,0 0   }
		}*/
		
		#mySpinner{
  width:100%;
  height:100%;
  position:fixed;
  z-index:999;
  background:#fff;
  left:0;
  top:0;
}

@keyframes spinner {
from {transform:rotate(0deg);}
to {transform: rotate(360deg);}
}
.spinner:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  top: 50%;
  left: 50%;
  width: 30px;
  height: 30px;
  margin-top: -15px;
  margin-left: -15px;
  border-radius: 50%;
  border: 1px solid #ccc;
  border-top-color: #07d;
  animation: spinner .6s linear infinite;
}
		

	</style>
</head>
<!--<div class="loader">

</div>-->

<body class="sidebar-mini sidebar-collapse fixed">
<div class="wrapper">
		
		@include('partials.header')
         <x-sidebar/>
         <div class="content-wrapper">
			   <div class="container-fluid" @hasSection('dashboardInlineCSS')  @else style="padding-top:3.9rem;" @endif>
				   @yield('content')
				</div>
		 </div>
   </div>
</body>
@include('partials.js-libs')
@yield('scriptcode_three')
<script>
$('#mySpinner').addClass('spinner');
$(window).on("load", function(){
  setTimeout(function(){
    $('#mySpinner').remove();
  },9000)
  
})
	
</script>