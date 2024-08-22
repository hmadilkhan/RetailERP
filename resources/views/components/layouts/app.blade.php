<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>
    @include('partials.html-libs')
    @yield('css_code')
    @yield('scriptcode_one')
</head>

<body class="sidebar-mini fixed">
    <div class="loader-bg">
        <div class="loader-bar">
        </div>
    </div>
    <div id="cover-spin"></div>
    <!--wrapper-->
    <div class="wrapper">
        <!-- Navbar header-->
        @include('partials.header')
        <!-- end Navbar header -->
        <!-- Side-Nav-->
        <x-sidebar />
        <!-- end Side-Nav -->
        <div class="content-wrapper">
            <!-- Container-fluid starts -->
            <!-- Main content starts -->
            <div class="container-fluid" @hasSection('dashboardInlineCSS') @else style="padding-top:3.9rem;" @endif>
                <!-- start contect-->
                {{ $slot }}
                <!-- end contect-->
            </div>
            <!-- Main content ends -->
            <!-- Container-fluid ends -->
        </div>
    </div>
    @include('partials.js-libs')
    <script>
        $('.table').DataTable({
            bLengthChange: true,
            displayLength: 50,
            info: true,
            language: {
                search: '',
                searchPlaceholder: 'Search',
                lengthMenu: '<span></span> _MENU_'

            }
        });
    </script>
</body>

</html>