<head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <!-- <title>Contact - Shelter</title> -->

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('public/assets/img/icons/favicon-shelter.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/fonts/materialdesignicons.css') }}" />
    <link rel="stylesheet" href="public/assets/vendor/fonts/materialdesignicons.css'" />
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/fonts/flag-icons.css') }}" />
    <link rel="stylesheet" href="'public/assets/vendor/fonts/flag-icons.css'" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="'public/assets/vendor/libs/node-waves/node-waves.css'" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="'public/assets/vendor/css/rtl/core.css'" />
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="'public/assets/vendor/css/rtl/theme-default.css'" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="'public/assets/css/demo.css'" />
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/pages/front-page.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/swiper/swiper.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/pages/front-page-landing.css') }}" />
    <!-- Helpers -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('public/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('js/front-config.js') }}"></script>
  </head>

<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Row -->
    <div class="row row-sm mt-2">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body pt-4">
                    <div class="table-responsive overflow-hidden table-data">
                        <table id="table-data-trainer" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap; width:80%">
                            <thead>
                                <tr>
                                    <th class="">Nama</th>
                                    <th class="">Divisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trainer as $value)
                                <tr>
                                    <td class="text-left">{{$value->nama}}</td>
                                    <td class="text-left">{{$value->divisi}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
    <!--/ Responsive Datatable -->
</div>
<!--/ Content -->
