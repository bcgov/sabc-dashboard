
<!-- Top Bar Start -->
<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="/dashboard/admin" class="logo">
            <span>
                <img src="/dashboard/images/admin/logo-sm.png" alt="logo-small" class="d-block d-lg-none logo-sm m-2">
            </span>
            <span>
                <img src="/dashboard/images/admin/logo.png" alt="logo-large" class="logo-lg">
                <img src="/dashboard/images/admin/logo-dark.png" alt="logo-large" class="logo-lg logo-dark">
            </span>
        </a>
    </div>
    <!--end logo-->
    <!-- Navbar -->
    <nav class="navbar-custom">
        <ul class="list-unstyled topbar-nav float-right mb-0">

            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="javascript: void(0);" role="button" aria-haspopup="false" aria-expanded="false">

                    @if(isset($user))
                    <span class="ml-1 nav-user-name">{{$user->name}} <i class="mdi mdi-chevron-down"></i> </span>
                    @else
                        <span class="ml-1 nav-user-name"> <i class="mdi mdi-chevron-down"></i> </span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="javascript: void(0);"><i class="dripicons-gear text-muted mr-2"></i> Settings</a>
                    <a class="dropdown-item" href="/dashboard/logout"><i class="dripicons-exit text-muted mr-2"></i> Logout</a>
                </div>
            </li>
        </ul><!--end topbar-nav-->

        <ul class="list-unstyled topbar-nav mb-0">
            <li>
                <button class="button-menu-mobile nav-link waves-effect waves-light">
                    <i class="dripicons-menu nav-icon"></i>
                </button>
            </li>
        </ul>
    </nav>
    <!-- end navbar-->
</div>
<!-- Top Bar End -->
