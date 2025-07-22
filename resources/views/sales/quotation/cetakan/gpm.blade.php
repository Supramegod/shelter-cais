<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANALISA GPM QUOTATION - {{$leads->nama_perusahaan}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            line-height: 1.5;
            color:#002060;
            text-align: justify;
        }

        @page {
            size: A4;
            margin: 0;
        }

        .content {
            page-break-after: always;
            margin-left: 20mm;
            margin-right: 20mm;
            margin-bottom: 20mm;
        }

        .bordered table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .bordered th,.bordered td {
            border: 1px solid black;
            text-align: left;
            color:black !important;
            padding-left:5px;
            padding-right:5px;
        }

        .bordered th {
            background-color: #f2f2f2;
        }

        @media print {
            th {
                background-color: #f2f2f2 !important;
                color: black;
            }
            td {
                background-color: white !important;
            }
        }
    </style>
</head>
<body>
<div class="content">
    <div style="margin-top:50px;margin-right:20px;color:black !important;font-size:10pt !important;">
        <p style="text-align:center">ANALISA GPM {{strtoupper($quotation->kebutuhan)}}<br>
        @foreach($quotation->quotation_site as $site)
        {{strtoupper($site->nama_site)}}<br>
        @endforeach
        TAHUN {{$quotation->tahun_quotation}}</p>

        <div class="row">
            <div class="table-responsive text-nowrap">
            <table class="bordered">
                <thead class="text-center">
                <tr class="table-success">
                    <th>Keterangan</th>
                    <th>HPP</th>
                    <th>Harga Jual</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:left">Nominal</td>
                        <td style="text-align:right">
                        {{"Rp. ".number_format($quotation->grand_total_sebelum_pajak,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        {{"Rp. ".number_format($quotation->grand_total_sebelum_pajak_coss,2,",",".")}}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:left">Total Biaya</td>
                        <td style="text-align:right">
                        {{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        {{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:left">Margin</td>
                        <td style="text-align:right">
                        {{"Rp. ".number_format($quotation->margin,2,",",".")}}
                        </td>
                        <td style="text-align:right">
                        {{"Rp. ".number_format($quotation->margin_coss,2,",",".")}}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="text-align:left">GPM</td>
                        <td class="fw-bold" style="text-align:right">
                        {{number_format($quotation->gpm,2,",",".")}} %
                        </td>
                        <td class="fw-bold" style="text-align:right">
                        {{number_format($quotation->gpm_coss,2,",",".")}} %
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        </table>
    </div>
</div>

</body>
<script>
        window.print();
    </script>
</html>
