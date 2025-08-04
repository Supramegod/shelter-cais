<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Good Receipt - {{ $nomor_surat }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .main-table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .category-header {
            font-weight: bold;
            font-size: 14px;
            text-align: left;
            padding: 10px;
            background-color: #e8e8e8;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .signature {
            margin-top: 50px;
        }

        .signature table {
            width: 100%;
        }

        .signature td {
            text-align: center;
            padding: 20px 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div style="width: 100%; text-align: center; margin-bottom: 5px;">

        <img src="{{ public_path('assets/img/icons/icon-shelter.png') }}" alt="Logo" style="height: 20px;">
    </div>
    <div style="width: 100%; text-align: center;">
        <h2 style="margin: 0; font-size: 20px;">PT. SHELTER INDONESIA</h2>
        <p style="margin: 2px 0; font-size: 12px;">Jl. Semampir Sel. V A No.18, Medokan Semampir, Kec.
            Sukolilo, Kota SBY, Jawa Timur,60119, Indonesia</p>
        <p style="margin: 2px 0; font-size: 12px;">Telp: (031) 12345678 | Email: info@shelter.co.id</p>
    </div>
    </div>

    <hr style="border: 1px solid black; margin-bottom: 10px;">

    <h3 style="text-align: center; text-decoration: underline; margin-bottom: 5px;">GOOD RECEIPT</h3>

    <table style="width: 100%; font-size: 13px; margin-bottom: 10px;">
        <tr>
            <td style="width: 20%;"><strong>No. Surat</strong></td>
            <td style="width: 2%;">:</td>
            <td>{{ $nomor_surat }}</td>
        </tr>
        <tr>
            <td style="width: 20%;"><strong>No.po</strong></td>
            <td style="width: 2%;">:</td>
            <td>{{$kode_po }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal</strong></td>
            <td>:</td>
            <td>{{ $now }}</td>
        </tr>

    </table>


    {{-- Loop per jenis barang --}}
    @foreach ($listChemical as $jenis => $items)
        <table class="main-table" border="1" cellspacing="0" cellpadding="5" width="100%">
            <thead>
                <tr>
                    <th colspan="7" class="category-header" style="border: 1px solid black; text-align: center;">
                        {{ strtoupper($jenis) }}
                    </th>

                </tr>
                <tr>
                    <th rowspan="2" width="30%">Nama Barang</th>
                    <th rowspan="2" width="10%">Merk</th>
                    <th rowspan="2" width="10%">Jumlah</th>
                    <th colspan="2" class="center" width="30%">Checklist</th>
                    <th rowspan="2" width="20%">Keterangan</th>
                </tr>
                <tr>
                    <th class="center" width="15%">Terima</th>
                    <th class="center" width="15%">Kembalikan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td class="center">{{ $item->merk }}</td>
                        <td class="center">{{ $item->qty }}</td>
                        <td class="center">☐</td>
                        <td class="center">☐</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    @endforeach




    {{-- Tanda tangan --}}
    <div class="signature">
        <table>
            <tr>
                <td width="33%">
                    <strong>Diserahkan Oleh:</strong><br><br><br><br>
                    (...........................)<br>
                    <small>Nama & Tanda Tangan</small>
                </td>
                <td width="33%">
                    <strong>Diterima Oleh:</strong><br><br><br><br>
                    (...........................)<br>
                    <small>Nama & Tanda Tangan</small>
                </td>
                <td width="34%">
                    <strong>Disetujui Oleh:</strong><br><br><br><br>
                    (...........................)<br>
                    <small>Nama & Tanda Tangan</small>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak oleh: {{ $created_by }}</p>
        <p>Tanggal: {{ date('d-m-Y H:i:s') }}</p>
    </div>

</body>

</html>