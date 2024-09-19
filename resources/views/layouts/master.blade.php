<!doctype html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('public/assets/') }}"
  data-template="horizontal-menu-template">
  @include('layouts.head')
<body>
<!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <!-- Navbar -->
         @include('layouts.navbar')
        <!-- / Navbar -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Menu -->
            @include('layouts.menu')
            <!-- / Menu -->
            
            <!-- Content -->
            @yield('content')
            <!--/ Content -->

            <!-- Footer -->
            @include('layouts.foot')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
            </div>
          <!--/ Content wrapper -->
        </div>

        <!--/ Layout container -->
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    <!--/ Layout wrapper -->

    @include('layouts.script')
    
  </body>
</html>