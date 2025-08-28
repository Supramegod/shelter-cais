<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title></title> -->

    <!-- <style> -->

        <!-- Core CSS -->
        <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/rtl/core.css') }}" />
        <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/rtl/theme-default.css') }}" />
        <link rel="stylesheet" href="{{ asset('public/assets/css/demo.css') }}" />
        <link rel="stylesheet" href="{{ asset('public/assets/vendor/css/pages/front-page.css') }}" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/nouislider/nouislider.css') }}" />
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css" />

        <!-- @import url('https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js'); -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
         <!-- Additional embedded styles  -->
    <!-- </style> -->

</head>

<body>
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- Row -->
        <div class="row row-sm mt-12">
        <h1 style="text-align: center; color:#121240">LAPORAN TRAINING</h1>
         <br><br><br>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body pt-4">
                    <table class="table table-borderless" >
                        <tbody>
                            <tr>
                                <td style="width: 40%; padding:0%;">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm" >
                                            <tbody>
                                                <tr>
                                                    <th scope="row" style="width: 40%"><strong>B. Unit :</strong></th>
                                                    <td>
                                                        {{$data->laman}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" style="width: 40%"><strong>Tipe :</strong></th>
                                                    <td>
                                                        {{$data->tipe}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><strong>Waktu Mulai:</strong></th>
                                                    <td>
                                                        {{$data->waktu_mulai}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><strong>Tempat :</strong></th>
                                                    <td>
                                                        {{$data->tempat}}
                                                    </td>
                                                </tr>
                                                <!-- <tr>
                                                    <th scope="row"><strong>Number of Pages:</strong></th>
                                                    <td>
                                                        1
                                                    </td>
                                                </tr> -->
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                                <td style="width: 60%; padding:0%;">
                                    <div class="table-responsive" >
                                        <table class="table table-borderless table-sm">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" style="width: 30%"><strong>Area:</strong></th>
                                                    <td>
                                                        {{$data->area}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" style="width: 30%"><strong>Materi:</strong></th>
                                                    <td>
                                                        {{$data->materi}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><strong>Waktu Selesai:</strong></th>
                                                    <td>
                                                        {{$data->waktu_selesai}}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><strong>Keterangan:</strong></th>
                                                    <td>
                                                        {{$data->keterangan}}
                                                    </td>
                                                </tr>
                                                <!--<tr>
                                                    <th scope="row"><strong>Email:</strong></th>
                                                    <td>
                                                        info@example.com
                                                    </td>
                                                </tr> -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <div class="row row-sm mt-12">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <div class="">
                            <h6>Trainer</h6>
                            <table id="table-data-trainer" class="table"
                                style="text-wrap: nowrap; width:90%; border: 1px solid black;">
                                <thead>
                                    <tr>
                                        <th style='border: 1px solid black; text-align: center' class="">Nama</th>
                                        <th style='border: 1px solid black; text-align: center' class="">Divisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trainer as $value)
                                        <tr style='border: 1px solid black;'>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->nama}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->divisi}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <!-- </div> -->

        <!-- <div class="row row-sm mt-12"> -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <div class="">
                            <h6>Client</h6>
                            <table id="table-data-trainer" class="table"
                                style="text-wrap: nowrap; width:90%; border: 1px solid black;">
                                <thead>
                                    <tr>
                                        <th style='border: 1px solid black; text-align: center' class="">Nama</th>
                                        <th style='border: 1px solid black; text-align: center' class="">Area</th>
                                        <th style='border: 1px solid black; text-align: center' class="">B. Unit</th>
                                        <th style='border: 1px solid black; text-align: center' class="">Kab / Kota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($client as $value)
                                        <tr style='border: 1px solid black;'>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->client}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->area}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->laman}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->kab_kota}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- </div>
        <br><br> -->
            <!-- <div class="row row-sm mt-12"> -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <div class="">
                            <h6>Peserta</h6>
                            <table id="table-data-trainer" class="table"
                                style="text-wrap: nowrap; width:90%; border: 1px solid black;">
                                <thead>
                                    <tr>
                                        <th style='border: 1px solid black; text-align: center' class="">Nik</th>
                                        <th style='border: 1px solid black; text-align: center' class="">Nama</th>
                                        <th style='border: 1px solid black; text-align: center' class="">No Whatsapp
                                        </th>
                                        <th style='border: 1px solid black; text-align: center' class="">Position</th>
                                        <th style='border: 1px solid black; text-align: center' class="">Client</th>
                                        <th style='border: 1px solid black; text-align: center' class="">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peserta as $value)
                                        <tr style='border: 1px solid black;'>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->nik}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->nama}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->no_whatsapp}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->position}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->client}}</td>
                                            <td style='font-size: 10px; border: 1px solid black;' class="text-left">
                                                {{$value->status_employee}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body pt-4">
                        <div class="">
                            <h6>Galeri</h6>

                            @foreach($listImage as $value)
                            <figure>
                                <img src="{{ public_path('/uploads/sdt-training/image/').$value->file_name }}" alt="" style="width: 200px; height: 150px;">
                                <figcaption>{{$value->keterangan}}</figcaption>
                            </figure>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End Row -->
        <!--/ Responsive Datatable -->
    </div>
</body>

</html>

@section('pageScript')
<script>
    $(document).ready(function() {
        alert('sjsjsj');
    });
    // let table = new DataTable('#table-data-trainer');

    // var table = $('#table-data-trainer').DataTable({
    //   scrollX: true,
    //   "iDisplayLength": 25,
    //   'processing': true,
    //   'language': {
    //   'loadingRecords': '&nbsp;',
    //   'processing': 'Loading...'
    // }});
<script>
@endsection
