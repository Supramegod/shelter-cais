<!doctype html>

<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
  data-assets-path="{{ asset('') }}" data-template="horizontal-menu-template-no-customizer">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>SHELTER - Customer & Activity Information System</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icons/favicon-shelter.png') }}" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
    rel="stylesheet" />

  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

  <!-- Menu waves for no-customizer fix -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
  <!-- Vendor -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

  <!-- Helpers -->
  <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>

  <style>
    body {
      background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('{{ asset('assets/img/backgrounds/background-login.jpg') }}') no-repeat center center fixed;
      background-size: cover;
    }
  </style>
</head>

<body>
  <!-- Content -->

  <div class="position-relative">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner py-4">
        <!-- Login -->
        <div class="card p-2">
          <!-- Logo -->
          <div class="app-brand justify-content-center mt-5">
            <a href="{{route('home')}}" class="app-brand-link gap-2">
              <span style="display:flex;justify-content:center">
                <img alt="logo" class="img-fluid img" src="{{ asset('assets/img/icons/icon-shelter.png')}}"
                  style="width:50%">
              </span>
            </a>
          </div>

          <div class="card-body mt-2">
            <h4 class="mb-2 text-center fst-italic mb-3">Customer & Activity Information System</h4>
            <p class="mb-4">Silahkan login untuk melanjutkan</p>

            <form id="form-auth" class="mb-3 need-validation" action="{{route('authenticate')}}" method="POST"
              novalidate>
              @csrf
              <div class="form-floating form-floating-outline mb-3">
                <input type="text"
                  class="form-control @if ($errors->any()) @if($errors->has('username')) is-invalid @else @endif @endif"
                  id="username" name="username" placeholder="Masukkan Username" autofocus />
                <label for="username">Username</label>
                @if($errors->has('username'))
                  <div class="invalid-feedback">{{ $errors->first('username') }}</div>
                @endif
              </div>
              <div class="mb-3">
                <div class="form-password-toggle">
                  <div class="input-group input-group-merge is-invalid" id="ct-password">
                    <div class="form-floating form-floating-outline">
                      <input type="password" id="password"
                        class="form-control @if ($errors->any()) @if($errors->has('password')) is-invalid @else   @endif @endif"
                        data-container="ct-password" name="password" placeholder="············">
                      <label for="password">Password</label>
                    </div>
                    <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                  </div>
                  @if($errors->has('password'))
                    <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                  @endif
                </div>
              </div>
              <div class="mb-3 d-flex justify-content-between">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember-me" />
                  <label class="form-check-label" for="remember-me"> Remember me </label>
                </div>
              </div>
              <div class="mb-3">
                <button class="btn btn-primary d-grid w-100" type="submit">Log in</button>
              </div>
            </form>

            <div class="d-flex justify-content-center gap-2">
              &nbsp;
            </div>
          </div>
        </div>
        <!-- /Login -->
      </div>
    </div>
  </div>

  <!-- / Content -->

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

  <!-- Main JS -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <!-- Page JS -->
  <script>
    @if(session('message'))
      alert({{ session('message') }});
    @endif
  </script>

</body>

</html>