<!doctype html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('') }}"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Contact - Shelter</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icons/favicon-shelter.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
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
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/front-page-landing.css') }}" />
    <!-- Helpers -->
    <link href="
https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css
" rel="stylesheet">
<script src="
https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js
"></script>
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('js/front-config.js') }}"></script>

  </head>

  <body>
  <script src="{{ asset('assets/vendor/js/dropdown-hover.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/mega-dropdown.js') }}"></script>

    <!-- Navbar: Start -->
    <nav class="layout-navbar container-fluid shadow-none py-0" style="background-color:#121240 !important;display: flex;height: 80px !important;justify-content: center;align-items: center;">
        <div class="navbar navbar-expand-lg landing-navbar border-0 px-3 px-md-4" style="background-color:#121240 !important;">
            <!-- Menu logo wrapper: Start -->
            <div class="navbar-brand app-brand demo d-xl-flex py-0 ml-1">
            <a href="https://shelterindonesia.id" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                <img src="{{asset('assets/img/icons/icon-light-shelter.png')}}" style="width:200px" alt="Logo" />
            </span>
            </a>
        </div>
        <!-- Menu logo wrapper: End -->
        <!-- Menu wrapper: Start -->
        <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
          <button
            class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 scaleX-n1-rtl"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <i class="tf-icons mdi mdi-close"></i>
          </button>
          
        </div>
        <div class="landing-menu-overlay d-lg-none"></div>
        <!-- Menu wrapper: End -->
        <!-- Toolbar: Start -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">
          
          <!-- navbar button: End -->
        </ul>
        <!-- Toolbar: End -->
      </div>
    </nav>
    <!-- Navbar: End -->

    <!-- Sections:Start -->

    <div data-bs-spy="scroll" class="scrollspy-example">
      <!-- Contact Us: Start -->
      <section id="landingContact" class="section-py bg-body landing-contact" style="padding-top:2rem !important">
        <div class="container bg-icon-left position-relative">
            <form class="card-body overflow-hidden" action="{{route('contact.save')}}" method="POST">
                @csrf
                <input type="hidden" name="platform" id="platform" value="{{$platform}}">
                <img
                    src="{{ asset('assets/img/front-pages/icons/bg-left-icon-light.png') }}"
                    alt="section icon"
                    class="position-absolute top-0 start-0"
                    data-speed="1" />
                <h6 class="text-center fw-semibold d-flex justify-content-center align-items-center mb-5">
                    
                    <span class="text-uppercase" style="font-size:3rem">KONTAK</span>
                </h6>
                <div class="row gy-4">
                    <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="rounded text-white card-body p-5" style="background-color:#121240">
                            <h1 class="fw-medium mb-1 text-white mt-2"><span class="fw-bold">Konsultasikan</span></h1>
                            <h1 class="fw-medium mb-1 text-white mt-3 mb-5">Bisnis Anda</h1>
                            <p class="mb-0 text-white" style="font-size:1rem">
                            Dapatkan solusi yang lebih baik untuk bisnis anda dengan bekerja sama bersama ahli di bidang industri ketenagakerjaan.
                            </p>
                        </div>
                    </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-body">
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" />
                                        <label for="nama">Nama</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                        <input type="number" class="form-control" id="no_telepon" name="no_telepon" placeholder="No. Telepon" />
                                        <label for="no_telepon">No. Telepon</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" />
                                        <label for="Email">Email</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <select id="kebutuhan" name="kebutuhan" class="form-select form-select-lg">
                                            <option value="">- Pilih Kebutuhan -</option>
                                            @foreach($kebutuhan as $value)
                                            <option value="{{$value->id}}">{{$value->nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" placeholder="Nama Perusahaan" />
                                        <label for="nama_perusahaan">Nama Perusahaan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Jabatan" />
                                        <label for="jabatan">Jabatan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <select id="branch" name="branch" class="form-select form-select-lg">
                                            <option value="">- Pilih Wilayah -</option>
                                            @foreach($branch as $value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                        <textarea class="form-control h-px-100" placeholder="Pesan" aria-label="Pesan" name="pesan" id="pesan"></textarea>
                                        <label for="pesan">Pesan</label>
                                        </div>
                                    </div>
                                    <!-- Google Recaptcha -->
                                    <div class="g-recaptcha mt-4" data-sitekey="6Lf-yCAqAAAAACD0QTRflbB4lo8Lvpn-FScg-eNp"></div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Kirim</button>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </section>
      <!-- Contact Us: End -->
    </div>

    <!-- / Sections:End -->

    <!-- Footer: Start -->
    <footer class="landing-footer">
      <div class="footer-top position-relative overflow-hidden" style="background-color:#121240 !important">
        <img
          src="../../assets/assets/img/front-pages/backgrounds/footer-bg.png"
          alt="footer bg"
          class="footer-bg banner-bg-img" />
        <div class="container position-relative">
          <div class="row gx-0 gy-4 g-md-5">
            <div class="col-lg-5">
            <a href="https://shelterindonesia.id" class="app-brand-link gap-2 mb-3">
            <span class="app-brand-logo demo">
                <img src="{{asset('assets/img/icons/icon-light-shelter.png')}}" style="width:200px" alt="Logo" />
            </span>
            </a>
              <p class="footer-text footer-logo-description mb-4">
                Facility Management Service
              </p>
              <div>
                <a href="https://www.facebook.com/shelterindonesiaofficial" class="footer-link me-2" target="_blank"
                ><i class="mdi mdi-facebook"></i
                ></a>
                <a href="https://www.instagram.com/shelterindonesia_official/" class="footer-link me-2" target="_blank"
                ><i class="mdi mdi-instagram"></i
                ></a>
                <a href="https://www.linkedin.com/company/shelterindonesia/" class="footer-link me-2" target="_blank"
                ><i class="mdi mdi-linkedin"></i
                ></a>
                <a href="https://www.youtube.com/@ShelterIndonesia" class="footer-link" target="_blank"
                ><i class="mdi mdi-youtube"></i
                ></a>
            </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <h6 class="footer-title mb-4">Kebutuhan</h6>
              <ul class="list-unstyled mb-0">
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/security-service" target="_blank" class="footer-link">Petugas Keamanan</a>
                </li>
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/cleaning-service-3/" target="_blank" class="footer-link">Kebutuhan Kebersihan</a>
                </li>
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/labour-supply-3/" target="_blank" class="footer-link">Pasokan Tenaga Kerja</a>
                </li>
              </ul>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
              <h6 class="footer-title mb-4">Kantor Cabang</h6>
              <ul class="list-unstyled mb-0">
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/contact/" class="footer-link">Jakarta</a>
                </li>
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/contact/" class="footer-link">Semarang</a>
                </li>
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/contact/" class="footer-link">Surabaya</a>
                </li>
                <li class="mb-3">
                  <a href="https://shelterindonesia.id/contact/" class="footer-link">Makassar</a>
                </li>
              </ul>
            </div>
            <div class="col-lg-3 col-md-4">
              <h6 class="footer-title mb-4">Informasi Kontak</h6>
              <p class="footer-text footer-logo-description mb-4">
                Jl. Semampir Sel. V A No.18
                Medokan Semampir, Kec. Sukolilo, Kota SBY, Jawa Timur 60119
              </p>
              <p class="footer-text footer-logo-description mb-4">
                Shelter Indonesia
              </p>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- Footer: End -->

    <!-- Core JS -->
    <!-- build:js assets/assets/vendor/js/core.js -->
     
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/nouislider/nouislider.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script async src="https://www.google.com/recaptcha/api.js">

    <!-- Main JS -->
    <script src="{{ asset('assets/js/front-main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/front-page-landing.js') }}"></script>
  </body>
</html>

<script>
@if(session()->has('success'))  
    Swal.fire({
        title: 'Pemberitahuan',
        html: '{{session()->get('success')}}',
        icon: 'success',
        customClass: {
        confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false
    });
@endif
@if(session()->has('error'))  
    Swal.fire({
        title: 'Pemberitahuan',
        html: '{{session()->get('error')}}',
        icon: 'warning',
        customClass: {
        confirmButton: 'btn btn-warning waves-effect waves-light'
        },
        buttonsStyling: false
    });
@endif  
</script>