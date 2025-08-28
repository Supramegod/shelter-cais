<!-- resources/views/receiving_note.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Receiving Notes - {{ $nomor_surat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
        }

        .section-title {
            font-weight: bold;
        }

        .info-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .items-table th {
            background-color: #003366;
            color: #fff;
        }

        .totals {
            margin-top: 10px;
            width: 300px;
            float: right;
            border-collapse: collapse;
        }

        .totals td {
            border: 1px solid #000;
            padding: 6px;
        }

        .remarks {
            margin-top: 30px;
        }

        .remarks label {
            font-weight: bold;
        }

        .remarks p {
            border-top: 1px solid #000;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <!-- KOP -->
    <div style="width: 100%; text-align: center; margin-bottom: 5px;">
        <img src="{{ asset('assets/img/icons/icon-shelter.png') }}" alt="Logo" style="height: 20px;">
    </div>
    <div style="width: 100%; text-align: center;">
        <h2 style="margin: 0; font-size: 20px;">PT. SHELTER INDONESIA</h2>
        <p style="margin: 2px 0; font-size: 12px;">Jl. Semampir Sel. V A No.18, Medokan Semampir, Kec. Sukolilo,
            Kota SBY, Jawa Timur, 60119, Indonesia</p>
        <p style="margin: 2px 0; font-size: 12px;">Telp: (031) 12345678 | Email: info@shelter.co.id</p>
    </div>
    <hr>

    <h2>Receiving Notes - {{ $receivingNote->kategori_barang }}</h2>


    <!-- <table class="info-table">
        <tr>
            <td><span class="section-title">Nomor :</span> {{ $nomor_surat ?? '[Nomor RN]' }}</td>
            <td><span class="section-title">Tanggal:</span> {{ $now ?? '[Tanggal]' }}</td>
        </tr>
    </table> -->

    <table class="info-table">
        <tr>
            <td class="section-title">Informasi Pengiriman:</td>
            <td class="section-title">Perusahaan Penerima:</td>
        </tr>
        <tr>
            <td>
                Nomor : {{ $nomor_surat ?? '' }}<br>
                Tanggal Pengiriman: {{ $now ?? '' }}<br>
            </td>
            <td>
                Nama Perusahaan: {{ $requestpr->perusahaan ?? '' }}<br>
                Lokasi/Site: {{ $quotation->penempatan ?? '' }}<br>
            </td>
        </tr>
    </table>

    @foreach ($listChemical as $jenis => $items)
        <table class="items-table" border="1" cellspacing="0" cellpadding="5" width="100%" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th colspan="5" class="category-header" style="text-align: center; font-weight: bold;">
                        {{ strtoupper($jenis) }}
                    </th>
                </tr>
                <tr>
                    <th>BARANG</th>
                    <th>MERK</th>
                    <th>SATUAN</th>
                    <th>JUMLAH DIORDER</th>
                    <th>JUMLAH DITERIMA</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->nama_barang ?? '' }}</td>
                        <td>{{ $item->merk ?? '-' }}</td>
                        <td>{{ $item->satuan ?? '' }}</td>
                        <td style="text-align: center;">{{ $item->qty_ordered ?? $item->qty }}</td>
                        <td style="text-align: center;">{{ $item->qty }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach


    <!-- ... (bagian total, catatan, tanda tangan tetap) ... -->

    <div class="remarks">
        <label>Catatan Tambahan:</label>
    </div>




</body>

</html>