<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sabsoft - Login</title>
    <meta name="description" content="">
    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('storage/images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('storage/images/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" media="all" href="{{ asset('assets/css/merchant-public')}}" />
    <script src="{{ asset('assets/js/shared-31')}}"></script>


    <script src="{{ asset('assets/js/merchant-public-32')}}"></script>


    <script src="{{ asset('assets/js/trekkie.identity.min')}}"></script>

    <style>
        @media (max-height: 600px) {
            .login-card {
                padding-top: 1.8rem;
                padding-bottom: 0.5rem;
            }
        }
        .login-card {
            padding-top: 2rem;
            padding-right: 3rem;
            padding-bottom: 1rem;
            padding-left: 3rem;
        }
        img {
            display: block;
            max-width: 250%;
        }
        .login-card__header {
            margin-bottom: 1rem;
        }
        @media (min-width: 1025px) {
            .page-main:not(.split) {
                background: url("assets/images/extra-profile-bg.png");
                background-color: #084c3f;
            }
        }
        .page-main:not(.split) {
            background: url("assets/images/extra-profile-bg.png");
            background-color: #084c3f;
        }
    </style>

</head>
<body id="body-content" class="page fresh-ui" >
<style>          html, body {
        background-color: rgb(246, 246, 247);
        color: rgb(32, 34, 35);
    }
</style>
<div class="newDesignLanguage" style="--p-background:rgb(246, 246, 247);--p-background-hovered:rgb(241, 242, 243);--p-background-pressed:rgb(237, 238, 239);--p-background-selected:rgb(237, 238, 239);--p-surface:rgb(255, 255, 255);--p-surface-neutral:rgb(228, 229, 231);--p-surface-neutral-hovered:rgb(219, 221, 223);--p-surface-neutral-pressed:rgb(201, 204, 208);--p-surface-neutral-disabled:rgb(241, 242, 243);--p-surface-neutral-subdued:rgb(246, 246, 247);--p-surface-subdued:rgb(250, 251, 251);--p-surface-disabled:rgb(250, 251, 251);--p-surface-hovered:rgb(246, 246, 247);--p-surface-pressed:rgb(241, 242, 243);--p-surface-depressed:rgb(237, 238, 239);--p-backdrop:rgba(0, 0, 0, 0.5);--p-overlay:rgba(255, 255, 255, 0.5);--p-shadow-from-dim-light:rgba(0, 0, 0, 0.2);--p-shadow-from-ambient-light:rgba(23, 24, 24, 0.05);--p-shadow-from-direct-light:rgba(0, 0, 0, 0.15);--p-hint-from-direct-light:rgba(0, 0, 0, 0.15);--p-on-surface-background:rgb(241, 242, 243);--p-border:rgb(140, 145, 150);--p-border-neutral-subdued:rgb(186, 191, 195);--p-border-hovered:rgb(153, 158, 164);--p-border-disabled:rgb(210, 213, 216);--p-border-subdued:rgb(201, 204, 207);--p-border-depressed:rgb(87, 89, 89);--p-border-shadow:rgb(174, 180, 185);--p-border-shadow-subdued:rgb(186, 191, 196);--p-divider:rgb(225, 227, 229);--p-icon:rgb(92, 95, 98);--p-icon-hovered:rgb(26, 28, 29);--p-icon-pressed:rgb(68, 71, 74);--p-icon-disabled:rgb(186, 190, 195);--p-icon-subdued:rgb(140, 145, 150);--p-text:rgb(32, 34, 35);--p-text-disabled:rgb(140, 145, 150);--p-text-subdued:rgb(109, 113, 117);--p-interactive:rgb(44, 110, 203);--p-interactive-disabled:rgb(189, 193, 204);--p-interactive-hovered:rgb(31, 81, 153);--p-interactive-pressed:rgb(16, 50, 98);--p-focused:rgb(69, 143, 255);--p-surface-selected:rgb(242, 247, 254);--p-surface-selected-hovered:rgb(237, 244, 254);--p-surface-selected-pressed:rgb(229, 239, 253);--p-icon-on-interactive:rgb(255, 255, 255);--p-text-on-interactive:rgb(255, 255, 255);--p-action-secondary:rgb(255, 255, 255);--p-action-secondary-disabled:rgb(255, 255, 255);--p-action-secondary-hovered:rgb(246, 246, 247);--p-action-secondary-pressed:rgb(241, 242, 243);--p-action-secondary-depressed:rgb(109, 113, 117);--p-action-primary:rgb(0, 128, 96);--p-action-primary-disabled:rgb(241, 241, 241);--p-action-primary-hovered:rgb(0, 110, 82);--p-action-primary-pressed:rgb(0, 94, 70);--p-action-primary-depressed:rgb(0, 61, 44);--p-icon-on-primary:rgb(255, 255, 255);--p-text-on-primary:rgb(255, 255, 255);--p-surface-primary-selected:rgb(241, 248, 245);--p-surface-primary-selected-hovered:rgb(179, 208, 195);--p-surface-primary-selected-pressed:rgb(162, 188, 176);--p-border-critical:rgb(253, 87, 73);--p-border-critical-subdued:rgb(224, 179, 178);--p-border-critical-disabled:rgb(255, 167, 163);--p-icon-critical:rgb(215, 44, 13);--p-surface-critical:rgb(254, 211, 209);--p-surface-critical-subdued:rgb(255, 244, 244);--p-surface-critical-subdued-hovered:rgb(255, 240, 240);--p-surface-critical-subdued-pressed:rgb(255, 233, 232);--p-surface-critical-subdued-depressed:rgb(254, 188, 185);--p-text-critical:rgb(215, 44, 13);--p-action-critical:rgb(216, 44, 13);--p-action-critical-disabled:rgb(241, 241, 241);--p-action-critical-hovered:rgb(188, 34, 0);--p-action-critical-pressed:rgb(162, 27, 0);--p-action-critical-depressed:rgb(108, 15, 0);--p-icon-on-critical:rgb(255, 255, 255);--p-text-on-critical:rgb(255, 255, 255);--p-interactive-critical:rgb(216, 44, 13);--p-interactive-critical-disabled:rgb(253, 147, 141);--p-interactive-critical-hovered:rgb(205, 41, 12);--p-interactive-critical-pressed:rgb(103, 15, 3);--p-border-warning:rgb(185, 137, 0);--p-border-warning-subdued:rgb(225, 184, 120);--p-icon-warning:rgb(185, 137, 0);--p-surface-warning:rgb(255, 215, 157);--p-surface-warning-subdued:rgb(255, 245, 234);--p-surface-warning-subdued-hovered:rgb(255, 242, 226);--p-surface-warning-subdued-pressed:rgb(255, 235, 211);--p-text-warning:rgb(145, 106, 0);--p-border-highlight:rgb(68, 157, 167);--p-border-highlight-subdued:rgb(152, 198, 205);--p-icon-highlight:rgb(0, 160, 172);--p-surface-highlight:rgb(164, 232, 242);--p-surface-highlight-subdued:rgb(235, 249, 252);--p-surface-highlight-subdued-hovered:rgb(228, 247, 250);--p-surface-highlight-subdued-pressed:rgb(213, 243, 248);--p-text-highlight:rgb(52, 124, 132);--p-border-success:rgb(0, 164, 124);--p-border-success-subdued:rgb(149, 201, 180);--p-icon-success:rgb(0, 127, 95);--p-surface-success:rgb(174, 233, 209);--p-surface-success-subdued:rgb(241, 248, 245);--p-surface-success-subdued-hovered:rgb(236, 246, 241);--p-surface-success-subdued-pressed:rgb(226, 241, 234);--p-text-success:rgb(0, 128, 96);--p-decorative-one-icon:rgb(126, 87, 0);--p-decorative-one-surface:rgb(255, 201, 107);--p-decorative-one-text:rgb(61, 40, 0);--p-decorative-two-icon:rgb(175, 41, 78);--p-decorative-two-surface:rgb(255, 196, 176);--p-decorative-two-text:rgb(73, 11, 28);--p-decorative-three-icon:rgb(0, 109, 65);--p-decorative-three-surface:rgb(146, 230, 181);--p-decorative-three-text:rgb(0, 47, 25);--p-decorative-four-icon:rgb(0, 106, 104);--p-decorative-four-surface:rgb(145, 224, 214);--p-decorative-four-text:rgb(0, 45, 45);--p-decorative-five-icon:rgb(174, 43, 76);--p-decorative-five-surface:rgb(253, 201, 208);--p-decorative-five-text:rgb(79, 14, 31);--p-border-radius-base:0.4rem;--p-border-radius-wide:0.8rem;--p-card-shadow:0px 0px 5px var(--p-shadow-from-ambient-light), 0px 1px 2px var(--p-shadow-from-direct-light);--p-popover-shadow:-1px 0px 20px var(--p-shadow-from-ambient-light), 0px 1px 5px var(--p-shadow-from-direct-light);--p-modal-shadow:0px 6px 32px var(--p-shadow-from-ambient-light), 0px 1px 6px var(--p-shadow-from-direct-light);--p-top-bar-shadow:0 2px 2px -1px var(--p-shadow-from-direct-light);--p-override-none:none;--p-override-transparent:transparent;--p-override-one:1;--p-override-visible:visible;--p-override-zero:0;--p-override-loading-z-index:514;--p-button-font-weight:500;--p-choice-size:2rem;--p-icon-size:1rem;--p-choice-margin:0.1rem;--p-control-border-width:0.2rem;--p-text-field-spinner-offset:0.2rem;--p-text-field-focus-ring-offset:-0.4rem;--p-text-field-focus-ring-border-radius:0.7rem;--p-button-group-item-spacing:0.2rem;--p-top-bar-height:68px;--p-contextual-save-bar-height:64px;--p-banner-border-default:inset 0 0.2rem 0 0 var(--p-border), inset 0 0 0 0.2rem var(--p-border);--p-banner-border-success:inset 0 0.2rem 0 0 var(--p-border-success), inset 0 0 0 0.2rem var(--p-border-success);--p-banner-border-highlight:inset 0 0.2rem 0 0 var(--p-border-highlight), inset 0 0 0 0.2rem var(--p-border-highlight);--p-banner-border-warning:inset 0 0.2rem 0 0 var(--p-border-warning), inset 0 0 0 0.2rem var(--p-border-warning);--p-banner-border-critical:inset 0 0.2rem 0 0 var(--p-border-critical), inset 0 0 0 0.2rem var(--p-border-critical);--p-badge-mix-blend-mode:luminosity;--p-badge-font-weight:500;--p-non-null-content:'';--p-thin-border-subdued:0.1rem solid var(--p-border-subdued);--p-duration-1-0-0:100ms;--p-duration-1-5-0:150ms;--p-ease-in:cubic-bezier(0.5, 0.1, 1, 1);--p-ease:cubic-bezier(0.4, 0.22, 0.28, 1); color: var(--p-text);">

    <div class="page-main">
        <div class="page-content">
            <div class="login-card ">

                <header class="login-card__header">
                    <h1 class="login-card__logo">
                        <a href="{{route('home')}}">
                            <img alt="Log in to Sabsoft" src="{{ asset('storage/images/logo-black.png') }}" />
                        </a>          </h1>
                </header>

                <div class="login-card__content">
                    <div class="main-card-section">
                        <h2 class="ui-heading">Log in</h2>
                        <h3 class="ui-subheading ui-subheading--subdued">Continue to your store</h3>
                        @if(session("message"))
                            <label style="color: red;">{{session("message")}}</label>
                        @endif
                        <form  class="restyle2020-form" action="{{ route('login') }}" method="post">
                                        @foreach($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                            @csrf
                            <div class="ui-form__section">

                                <div class="restyle2020-form__forgot-store-link">

                                </div>

                                <div class="next-input-wrapper">
                                    <label class="next-label" for="Username">Username</label>
                                    <input autofocus="autofocus" placeholder="Enter Username" class="next-input" size="30" type="text" name="username" id="username" />
                                </div>
                                <div class="next-input-wrapper">
                                    <label class="next-label" for="Password">Password</label>
                                    <input autofocus="autofocus" placeholder="Enter Password" class="next-input" size="30" type="password" name="password" id="password" />
                                </div>

                            </div>
                            <button class="ui-button ui-button--primary ui-button--size-large" style="width: 100%;background-color: #4CAF50" type="submit" >Login</button>
                        </form>

                    </div>
                </div>


            </div>
        </div>
    </div>

</div>

</body>


</html>
