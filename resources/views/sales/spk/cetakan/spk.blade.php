<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Kerja - {{$data->nama_perusahaan}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 210mm;
            padding: 20mm;
            margin: auto;
        }
        h1, h2 {
            text-align: center;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        p {
            line-height: 1.5;
            text-align: justify;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
        }
        .label {
            width: 25%;
        }
        .signature {
            margin-top: 40px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><u>SURAT PERINTAH KERJA</u></h1>
        <h2>Nomor SPK : {{$data->nomor}}</h2>
        <br>
        <div class="content">
            <p>Yang bertandatangan dibawah ini:</p>
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td>: @if($pic!=null){{$pic->nama}}@endif</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>: @if($pic!=null){{$pic->jabatan}}@endif</td>
                </tr>
                <tr>
                    <td>Nama Perusahaan</td>
                    <td>: {{$data->nama_perusahaan}}</td>
                </tr>
                <tr>
                    <td>Alamat Perusahaan</td>
                    <td>: {{$leads->alamat}}</td>
                </tr>
                <tr>
                    <td>No. NPWP</td>
                    <td>: {{$quotation->npwp}}</td>
                </tr>
            </table>

            <p>Berdasarkan hasil diskusi & penawaran dari pihak SHELTER dengan {{$data->nama_perusahaan}}, maka dilakukan penunjukan kerjasama untuk penyediaan tenaga {{$quotationKebutuhan->kebutuhan}} sebanyak {{$quotation->total_hc}} personil yang akan mulai ditempatkan pertanggal {{$quotation->tgl_penempatan}} di lokasi {{$quotation->penempatan}} .</p>

            <p>Maka dengan ini mengeluarkan (SPK) Surat Perintah Kerjasama atau sebagai Confirmation Letter kepada:</p>

            <table>
                <tr>
                    <td class="label">Nama Perusahaan</td>
                    <td>: {{$company->name}}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: {{$company->address}}</td>
                </tr>
            </table>

            <p>Demikian surat penetapan kerjasama ini dikeluarkan untuk dapat dipergunakan sebagaimana mestinya. Dan selama proses pembuatan PKS (Perjanjian Kerjasama) belum selesai, maka SPK ini berlaku sebagai dasar penagihan invoice.</p>

            <p>Perihal pelaksanaan serta hak dan kewajiban pelaksana akan dituangkan dalam Surat Perjanjian Kerjasama (PKS).</p>

            <div class="signature">
                <p>Surabaya, {{$now}}</p>
                <br><br><br>
                <p>@if($pic!=null){{$pic->nama}}@else ............................... @endif</p>
                <p>{{$data->nama_perusahaan}}</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
