<header class="main-header-top hidden-print bg-success">
    <a href="{{ route('home') }}" class="logo"><img class="img-fluid able-logo"
            src="{{ asset('storage/images/logo.png') }}" alt="Theme-logo"></a>
    <nav class="navbar navbar-static-top bg-success">
        <!-- Sidebar toggle button-->
        <a href="#!" data-toggle="offcanvas" class="sidebar-toggle"></a>
        <ul class="top-nav lft-nav">
        </ul>
        <!-- Navbar Right Menu-->
        <div class="navbar-custom-menu">
            <ul class="top-nav">

                <li class="dropdown">
                    <a href="#!" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"
                        class="dropdown-toggle drop icon-circle drop-image">
                        <span><img class="img-circle "
                                src="{{ asset('storage/images/users/' . (session('image') == '' ? 'person-placeholder.png' : session('image'))) }}"
                                style="width:40px;" alt="User Image"></span>
                        <span>{{ Auth::check() ? ucfirst(Auth::user()->username) : '' }}<i
                                class=" icofont icofont-simple-down"></i></span>
                    </a>
                    <ul class="dropdown-menu settings-menu">
                        <li class="p-0">
                            <div class="dropdown-divider m-0"></div>
                        </li>
                        <li><a href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();"><i
                                    class="icon-logout"></i> {{ __('content.logout') }}</a></li>
                    </ul>
                </li>
            </ul>

            <!-- search -->
            <div id="morphsearch" class="morphsearch">
                <!-- /morphsearch-content -->
                <span class="morphsearch-close"><i class="icofont icofont-search-alt-1"></i></span>
            </div>
            <!-- search end -->
        </div>
    </nav>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
