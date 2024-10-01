<div class="left-sidenav">
    <ul class="metismenu left-sidenav-menu">

        <li>
            <a href="/dashboard/app_support"><i class="ti-home"></i><span>Home</span></a>
        </li>
        @if(isset($user_guid))
            <li>
                <a href="/dashboard/app_support/profile"><i class="ti-user"></i><span>Profile</span></a>
            </li>
            <li>
                <a href="/dashboard/app_support/applications"><i class="ti-agenda"></i><span>Applications</span></a>
            </li>
            <li>
                <a href="/dashboard/app_support/appendix_list"><i class="ti-archive"></i><span>Appendix List</span></a>
            </li>
        @endif

    </ul>
</div>
