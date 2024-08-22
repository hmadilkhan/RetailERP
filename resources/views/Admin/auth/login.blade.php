<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>ERP-Login</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="description" content="Sabsoft">
    <meta name="keywords"
          content="Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="Sabsoft">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('public/assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- Google font-->
   <link href="{{ asset('public/assets/css6ff6.css?family=Ubuntu:400,500,700') }}" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="{{ asset('public/assets/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">

    <!--ico Fonts-->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/icon/icofont/css/icofont.css') }}">

    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/components/bootstrap/dist/css/bootstrap.min.css') }}">

    <!-- waves css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/components/Waves/dist/waves.min.css') }}">

    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/main.css') }}">

    <!-- Responsive.css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/responsive.css') }}">

    <!--color css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/assets/css/color/color-1.min.css') }}" id="color"/>

</head>
<body>
<section class="login p-fixed d-flex text-center bg-primary common-img-bg">
    <!-- Container-fluid starts -->
    <div class="container-fluid">
        <div class="row">

            <div class="col-sm-12">
                <div class="login-card card-block">
                    <form class="md-float-material" method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf
                   <div class="text-center">
                            <img src="{{ asset('public/assets/images/logo-black.png') }}">
                        </div>
                        <h3 class="text-center txt-primary">
                            Sign In to Admin panel
                        </h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="md-input-wrapper">
                                  <input id="username" type="text" name="username" class="md-form-control @error('username') is-invalid @enderror"autocomplete="username" autofocus/>
                                    <label>Username</label>
                                </div>
                                   @error('username')
                                    <span class="form-control-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="md-input-wrapper">
                                    <input type="password"  id="password" class="md-form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                                <span class="focus-input100" data-placeholder="&#xf191;"></span>
                                    <label>Password</label>
                                </div>
                                @error('password')
                                    <span class="form-control-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                       <div class="col-sm-6 col-xs-12">
                            <div class="rkmd-checkbox checkbox-rotate checkbox-ripple m-b-25">
                                <label class="input-checkbox checkbox-primary">
                                    <input type="checkbox" id="checkbox" >
                                    <span class="checkbox {{ old('remember') ? 'checked' : '' }}"></span>
                                </label>
                                <div class="captions">{{ __('Remember Me') }}</div>

                            </div>
                                </div>
                                @if (Route::has('password.request'))
                                 <div class="col-sm-6 col-xs-12 forgot-phone text-right">
                                   <a href="{{ route('password.request') }}" class="text-right f-w-600"> {{ __('Forgot Your Password?') }}</a>
                                 </div>
                                @endif
                        </div>

                        <div class="row">
                            <div class="col-xs-10 offset-xs-1">
                                <a href="index-2.html"> <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">LOGIN</button></a>
                            </div>
                        </div>
                        <!-- <div class="card-footer"> -->


                        <!-- </div> -->
                    </form>
                    <!-- end of form -->
                </div>
                <!-- end of login-card -->
            </div>
            <!-- end of col-sm-12 -->
        </div>
        <!-- end of row -->
    </div>
    <!-- end of container-fluid -->
</section>

<!-- Required Jqurey -->
<script src="{{asset('public/components/Jquery/dist/jquery.min.js') }}"></script>
<script src="{{asset('public/components/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- tether.js -->
<script src="{{asset('public/components/tether/dist/js/tether.min.js') }}"></script>
<!-- waves effects.js -->
<script src="{{asset('public/components/Waves/dist/waves.min.js') }}"></script>
<!-- Required Framework -->
<script src="{{asset('public/components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Custom js -->
<script type="text/javascript" src="{{asset('public/assets/pages/elements.js') }}"></script>



  </body>
</html>