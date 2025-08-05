<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HARGA POKOK PENJUALAN QUOTATION - {{$leads->nama_perusahaan}}</title>
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
        <p style="text-align:center">HARGA POKOK PENJUALAN {{strtoupper($quotation->kebutuhan)}}<br>
        @foreach($quotation->quotation_site as $site)
        {{strtoupper($site->nama_site)}}<br>
        @endforeach
        TAHUN {{$quotation->tahun_quotation}}</p>

        <table class="bordered">
            <thead class="text-center">
            <tr class="table-success">
                <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;text-align:center">HARGA POKOK PENJUALAN</th>
            </tr>
            <tr class="table-success">
                <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;text-align:center">{{$leads->nama_perusahaan}} ( Provisi = {{$quotation->provisi}} )</th>
            </tr>
            <tr class="table-success">
                <th colspan="3">&nbsp;</th>
                @foreach($quotation->quotation_site as $site)
                <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;text-align:center">{{$site->nama_site}}</th>
                @endforeach
            </tr>
            <tr class="table-success">
                <th rowspan="2" style="vertical-align: middle;text-align:center">No.</th>
                <th>Structure</th>
                <th rowspan="2" style="vertical-align: middle;text-align:center">%</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</th>
                @endforeach
            </tr>
            <tr class="table-success">
                <th>Jumlah Head Count ( Personil ) </th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <th class="text-center">{{$detailJabatan->jumlah_hc}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @php $nomorUrut = 1; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Gaji Pokok</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            @foreach($daftarTunjangan as $it => $tunjangan)
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">{{$tunjangan->nama}}</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            @endforeach
            @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan" || $quotation->thr=="Diberikan Langsung")
                <tr class="">
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left" class="">Tunjangan Hari Raya <b>( {{$quotation->thr}} )</b></td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">@if($quotation->thr=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @elseif($quotation->thr=="Ditagihkan") Ditagihkan terpisah @elseif($quotation->thr=="Diberikan Langsung") Diberikan Langsung Oleh Client @endif</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            @endif
            @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Kompensasi <b>( {{$quotation->kompensasi}} )</b></td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">@if($quotation->kompensasi=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @elseif($quotation->kompensasi=="Ditagihkan") Ditagihkan terpisah @endif</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            @endif
            @if($quotation->tunjangan_holiday=="Normatif" || $quotation->tunjangan_holiday=="Flat")
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Tunjangan Hari Libur Nasional <b>( {{$quotation->tunjangan_holiday}} @if($quotation->tunjangan_holiday=="Normatif") : {{"Rp. ".number_format($quotation->tunjangan_holiday_display,2,",",".")}} @endif )</b></td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">@if($quotation->tunjangan_holiday=="Normatif") Ditagihkan terpisah @elseif($quotation->tunjangan_holiday=="Flat") {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @endif</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            @endif
            @if($quotation->lembur=="Normatif" || $quotation->lembur=="Flat")
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Lembur <b>( {{$quotation->lembur}} @if($quotation->lembur=="Normatif") : {{"Rp. ".number_format($quotation->lembur_per_jam,2,",",".")}} Per Jam @endif )</b></td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">@if($quotation->lembur=="Normatif") Ditagihkan terpisah @elseif ($quotation->lembur=="Flat") {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            @endif
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">@if($quotation->penjamin =="BPJS") BPJS Kesehatan @else Asuransi Kesehatan Swasta @endif</td>
                <td style="text-align:center">{{number_format($quotation->persen_bpjs_kesehatan,2,",",".")}}%</td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">
                @if($detailJabatan->penjamin_kesehatan == "Takaful")
                {{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</td>
                @else
                {{"Rp. ".number_format($detailJabatan->bpjs_kesehatan,2,",",".")}}</td>
                @endif
                </td>
                @endforeach
            </tr>
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">BPJS Ketenagakerjaan</td>
                <td style="text-align:center">{{number_format($quotation->persen_bpjs_ketenagakerjaan,2,",",".")}}%</td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_ketenagakerjaan,2,",",".")}} <a href="javascript:void(0)"><i class="mdi mdi-magnify text-primary view-bpjs" data-persen-bpjs-jkk="{{$detailJabatan->persen_bpjs_jkk}}" data-persen-bpjs-jkm="{{$detailJabatan->persen_bpjs_jkm}}" data-persen-bpjs-jht="{{$detailJabatan->persen_bpjs_jht}}" data-persen-bpjs-jp="{{$detailJabatan->persen_bpjs_jp}}" data-bpjs-jkk="{{$detailJabatan->bpjs_jkk}}" data-bpjs-jkm="{{$detailJabatan->bpjs_jkm}}" data-bpjs-jht="{{$detailJabatan->bpjs_jht}}" data-bpjs-jp="{{$detailJabatan->bpjs_jp}}"></i></a></td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Provisi Seragam</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}}</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Provisi Peralatan</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</td>
                @endforeach
            </tr>
            @if($quotation->kebutuhan_id==3)
            @php $nomorUrut++; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Chemical</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</td>
                @endforeach
            </tr>
            @endif
            @php $nomorUrut++; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Overhead Cost</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_ohc,2,",",".")}}</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Bunga Bank ( {{$quotation->top}} )</td>
                <td style="text-align:center">{{$quotation->persen_bunga_bank}} %</td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->bunga_bank,2,",",".")}}</td>
                @endforeach
            </tr>
            @php $nomorUrut++; @endphp
            <tr>
                <td style="text-align:center">{{$nomorUrut}}</td>
                <td style="text-align:left">Insentif</td>
                <td style="text-align:center">{{$quotation->persen_insentif}} %</td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->insentif,2,",",".")}}</td>
                @endforeach
            </tr>
            <tr class="table-success">
                <td colspan="2" style="text-align:right" class="fw-bold">Total Biaya per Personil</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->total_personil,2,",",".")}}</td>
                @endforeach
            </tr>
            <tr>
                <td colspan="2" style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                <td style="text-align:center"></td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->sub_total_personil,2,",",".")}}</td>
                @endforeach
            </tr>
            <tr class="table-success">
                <td colspan="2" style="text-align:right" class="fw-bold">Total Sebelum Management Fee</td>
                <td style="text-align:center"></td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
                <td style="text-align:center">{{$quotation->persentase}} %</td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->nominal_management_fee,2,",",".")}}</td>
            </tr>
            @if($quotation->is_ppn == 1)
            <tr class="table-success">
                <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                <td style="text-align:center"></td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->grand_total_sebelum_pajak,2,",",".")}}</td>
            </tr>
            <tr class="table-success">
                <td colspan="2" style="text-align:right" class="fw-bold">Dasar Pengenaan Pajak</td>
                <td style="text-align:center"></td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->dpp,2,",",".")}}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right" class="fw-bold">PPN <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                <td style="text-align:center">12 %</td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->ppn,2,",",".")}}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                <td style="text-align:center">-2 %</td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pph,2,",",".")}}</td>
            </tr>
            @endif
            <tr class="table-success">
                <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                <td style="text-align:center"></td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_invoice,2,",",".")}}</td>
            </tr>
            <tr class="table-success">
                <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                <td style="text-align:center"></td>
                <td class="text-end" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pembulatan,2,",",".")}}</td>
            </tr>
            </tbody>
        </table>
        </table>
    </div>
</div>

</body>
<script>
        window.print();
    </script>
</html>
