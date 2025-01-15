<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-fluid">
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{route('home')}}" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
            <img src="{{asset('img/icons/icon-shelter.png')}}" style="width:200px" alt="Logo" />
        </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="mdi mdi-close align-middle"></i>
        </a>
    </div>

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="mdi mdi-menu mdi-24px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- Search -->
        <li class="nav-item navbar-search-wrapper me-1 me-xl-0">
            <a class="nav-link search-toggler fw-normal" href="javascript:void(0);">
            <i class="mdi mdi-magnify mdi-24px scaleX-n1-rtl"></i>
            </a>
        </li>
        <!-- /Search -->

        <!-- Quick links  -->
        <!-- <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-1 me-xl-0">
            <a
            class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <i class="mdi mdi-view-grid-plus-outline mdi-24px"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end py-0">
            <div class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                <a
                    href="javascript:void(0)"
                    class="dropdown-shortcuts-add text-muted"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Add shortcuts"
                    ><i class="mdi mdi-view-grid-plus-outline mdi-24px"></i
                ></a>
                </div>
            </div>
            <div class="dropdown-shortcuts-list scrollable-container">
                <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-calendar fs-4"></i>
                    </span>
                    <a href="app-calendar.html" class="stretched-link">Calendar</a>
                    <small class="text-muted mb-0">Appointments</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-file-document-outline fs-4"></i>
                    </span>
                    <a href="app-invoice-list.html" class="stretched-link">Invoice App</a>
                    <small class="text-muted mb-0">Manage Accounts</small>
                </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-account-outline fs-4"></i>
                    </span>
                    <a href="app-user-list.html" class="stretched-link">User App</a>
                    <small class="text-muted mb-0">Manage Users</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-shield-check-outline fs-4"></i>
                    </span>
                    <a href="app-access-roles.html" class="stretched-link">Role Management</a>
                    <small class="text-muted mb-0">Permission</small>
                </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-chart-pie-outline fs-4"></i>
                    </span>
                    <a href="index.html" class="stretched-link">Dashboard</a>
                    <small class="text-muted mb-0">Analytics</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-cog-outline fs-4"></i>
                    </span>
                    <a href="pages-account-settings-account.html" class="stretched-link">Setting</a>
                    <small class="text-muted mb-0">Account Settings</small>
                </div>
                </div>
                <div class="row row-bordered overflow-visible g-0">
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-help-circle-outline fs-4"></i>
                    </span>
                    <a href="pages-faq.html" class="stretched-link">FAQs</a>
                    <small class="text-muted mb-0">FAQs & Articles</small>
                </div>
                <div class="dropdown-shortcuts-item col">
                    <span class="dropdown-shortcuts-icon bg-label-secondary rounded-circle mb-2">
                    <i class="mdi mdi-dock-window fs-4"></i>
                    </span>
                    <a href="modal-examples.html" class="stretched-link">Modals</a>
                    <small class="text-muted mb-0">Useful Popups</small>
                </div>
                </div>
            </div>
            </div>
        </li> -->
        <!-- Quick links -->

        <!--/ Approval -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
            <a
            class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <i class="mdi mdi-file-sign mdi-24px"></i>
            @if(count($approval)>0)
            <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
            @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
            <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Approval</h6>
                <span class="badge rounded-pill bg-label-primary">{{count($approval)}}</span>
                </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                    @foreach($approval as $quot)
                    <a href="{{route('quotation.view',$quot->id)}}">
                        <li class="list-group-item list-group-item-action dropdown-notifications-item">
                            <div class="d-flex gap-2">
                            <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                                <h6 class="mb-1 text-truncate">{{$quot->nomor}}</h6>
                                <small class="text-truncate text-body">{{$quot->nama_site}}</small>
                                <small class="text-truncate text-body">membutuhkan approval anda</small>
                            </div>
                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                <small class="text-muted">{{$quot->tgl_quot}}</small>
                            </div>
                            </div>
                        </li>
                    </a>
                    @endforeach
                </ul>
            </li>
            @if(count($approval)>0)
            <li class="dropdown-menu-footer border-top p-2">
                <a href="{{route('dashboard-approval')}}" class="btn btn-primary d-flex justify-content-center">
                View all approval
                </a>
            </li>
            @endif
            </ul>
        </li>
        <!--/ Notification -->


        <!-- Notification -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
            <a
            class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
            href="javascript:void(0);"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false">
            <i class="mdi mdi-bell-outline mdi-24px"></i>
            <span class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
            <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                <h6 class="mb-0 me-auto">Notification</h6>
                <span class="badge rounded-pill bg-label-primary">8 New</span>
                </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <img src="{{ asset('img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">Congratulation Lettie 🎉</h6>
                        <small class="text-truncate text-body">Won the monthly best seller gold badge</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">1h ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">Charles Franklin</h6>
                        <small class="text-truncate text-body">Accepted your connection</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">12hr ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <img src="{{ asset('img/avatars/2.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">New Message ✉️</h6>
                        <small class="text-truncate text-body">You have new message from Natalie</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">1h ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-success"
                            ><i class="mdi mdi-cart-outline"></i
                        ></span>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">Whoo! You have new order 🛒</h6>
                        <small class="text-truncate text-body">ACME Inc. made new order $1,154</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">1 day ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <img src="{{ asset('img/avatars/9.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">Application has been approved 🚀</h6>
                        <small class="text-truncate text-body"
                        >Your ABC project application has been approved.</small
                        >
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">2 days ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-success"
                            ><i class="mdi mdi-chart-pie-outline"></i
                        ></span>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">Monthly report is generated</h6>
                        <small class="text-truncate text-body">July monthly financial report is generated </small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">3 days ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <img src="{{ asset('img/avatars/5.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">Send connection request</h6>
                        <small class="text-truncate text-body">Peter sent you connection request</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">4 days ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <img src="{{ asset('img/avatars/6.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1 text-truncate">New message from Jane</h6>
                        <small class="text-truncate text-body">Your have new message from Jane</small>
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">5 days ago</small>
                    </div>
                    </div>
                </li>
                <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                    <div class="d-flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="avatar me-1">
                        <span class="avatar-initial rounded-circle bg-label-warning"
                            ><i class="mdi mdi-alert-circle-outline"></i
                        ></span>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
                        <h6 class="mb-1">CPU is running high</h6>
                        <small class="text-truncate text-body"
                        >CPU Utilization Percent is currently at 88.63%,</small
                        >
                    </div>
                    <div class="flex-shrink-0 dropdown-notifications-actions">
                        <small class="text-muted">5 days ago</small>
                    </div>
                    </div>
                </li>
                </ul>
            </li>
            <li class="dropdown-menu-footer border-top p-2">
                <a href="javascript:void(0);" class="btn btn-primary d-flex justify-content-center">
                View all notifications
                </a>
            </li>
            </ul>
        </li>
        <!--/ Notification -->

        
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
                <img src="{{ asset('img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="#">
                <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                        <img src="{{ asset('img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                    </div>
                    <div class="flex-grow-1">
                    <span class="fw-medium d-block">{{Auth::user()->full_name}}</span>
                    <small class="text-muted">{{$user->role}}</small>
                    </div>
                </div>
                </a>
            </li>
            <li>
                <div class="dropdown-divider"></div>
            </li>
            <li>
                <a class="dropdown-item" href="#">
                <i class="mdi mdi-account-outline me-2"></i>
                <span class="align-middle">My Profile</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#">
                <i class="mdi mdi-cog-outline me-2"></i>
                <span class="align-middle">Settings</span>
                </a>
            </li>
            <li>
                <div class="dropdown-divider"></div>
            </li>
            <li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="mdi mdi-logout me-2"></i>                <span class="align-middle">Log Out</span>
                </a>
            </li>
            </ul>
        </li>
        <!--/ User -->
        </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper container-fluid d-none">
        <input
        type="text"
        class="form-control search-input border-0"
        placeholder="Search..."
        aria-label="Search..." />
        <i class="mdi mdi-close search-toggler cursor-pointer"></i>
    </div>
    </div>
</nav>