<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request - {{ $data->tipe_barang }} - {{ $leads->nama_perusahaan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        a {
            pointer-events: none;
            color: inherit;
            text-decoration: none;
        }

        * {
            -webkit-user-select: none;
            /* Blok seleksi teks di iOS */
            user-select: none;
            -webkit-touch-callout: none;
            /* Blok tap lama di iOS */
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
        }

        .table-barang {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }


        .table-primary {
            background-color: #cfe2ff;
        }

        .table-success {
            background-color: #d1e7dd;
            font-weight: bold;
        }


        .info-table,
        .info-table tr,
        .info-table td {
            border: collapse;
            font-size: 20px;
        }

        .signature-box {
            float: left;
            width: 30%;
            text-align: center;
            margin-right: 5%;
        }

        .signature-box .ttd {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 100%;
        }
    </style>
</head>

<body>
    <div style="width: 100%;  text-align: center; margin-bottom: 15px;">
        <img src="{{ public_path('assets/img/icons/icon-shelter.png') }}" alt="Logo"
            style="height: 20px; margin-bottom: 5px;">
        <div class=" kop text-center">
            <h2 style="margin: 0; font-size: 20px;">PT. SHELTER Nusantara</h2>
            <p style="margin: 2px 0; font-size: 12px;">Jl. Semampir Selatan. V A No.18
                Medokan Semampir, Kec. Sukolilo, Surabaya, Jawa Timur</p>
            <p style="margin: 2px 0; font-size: 12px;">Phone: 031-594 1687 | Email: info@shelter.co.id</p>
            </divcla>
        </div>
        <hr style="border: 1px solid black; margin-bottom: 10px;">
        <h2 class="text-center">PURCHASE REQUISITION </h2>



        <table class="info-table" style="width: 100%;">
            <tr>
                <!-- Kolom kiri -->
                <td style=" width: 50%;">
                    <table>
                        <tr>
                            <td><strong>Nomor</strong></td>
                            <td>: {{ $data->nomor }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal</strong></td>
                            <td>: {{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Customer</strong></td>
                            <td>: {{ $data->perusahaan }}</td>
                        </tr>
                    </table>
                </td>

                <!-- Kolom kanan -->
                <td style="width: 50%;">
                    <table>
                        <tr>
                            <td><strong>Sales</strong></td>
                            <td>: {{ $data->sales }}</td>
                        </tr>
                        <tr>
                            <td><strong>Cabang</strong></td>
                            <td>: {{ $data->wilayah->name }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <h3 class="mb-1">Daftar Barang {{ $data->tipe_barang }}</h3>
        @foreach ($listJenisBarang as $jenisItem)
            <div class="table-container mb-5">
                <table class="table table-barang">
                    <thead>
                        <tr class="table-primary text-center">
                            <th colspan="6">{{ $jenisItem->jenis_barang }}</th>
                        </tr>
                        <tr class="table-primary text-center">
                            <th>Kode Barang</th>
                            <th>Nama barang</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Merk Barang</th>
                            <th>Proyek</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listBarang as $item)
                            @if ($item->jenis_barang == $jenisItem->jenis_barang)
                                <tr>
                                    <td class="text-center">000{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td class="text-center">{{ $item->jumlah }}</td>
                                    <td class="text-center">{{ $item->satuan }}</td>
                                    <td class="text-center">{{ $item->merk }}</td>
                                    <td class="text-center">{{ $leads->nama_perusahaan }}</td>

                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        @endforeach


        <div class="signature-box mt-5" style="width: 200px; ">
            <p>Dicetak oleh</p>
            {{ $data->pencetak }}

        </div>

</body>
<script>
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Cegah refresh (F5 atau Ctrl+R)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
            e.preventDefault();
        }
    });

    // Nonaktifkan semua link
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target) {
            e.preventDefault();
            return false;
        }
    });
    // Blok touch gesture dua jari atau pinch
    document.addEventListener('gesturestart', function(e) {
        e.preventDefault();
    });
    // Blok klik kanan (mouse) dan tap lama (touchscreen)
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Cegah tap lama (long press)
    document.addEventListener('touchstart', function(e) {
        if (e.touches.length > 1) {
            e.preventDefault(); // dua jari
        }
    }, {
        passive: false
    });
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target) {
            e.preventDefault();
            return false;
        }
    });
</script>

</html>
