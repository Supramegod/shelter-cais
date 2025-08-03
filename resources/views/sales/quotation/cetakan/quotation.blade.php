<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUOTATION - {{$leads->nama_perusahaan}}</title>
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
            margin: 0; /* Menghilangkan semua margin untuk pencetakan */
        }

        .cover {
            height: 100vh; /* Mengisi halaman penuh */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Memastikan gambar mengisi area dengan proporsional */
        }

        .content {
            page-break-after: always; /* Memastikan setiap konten di halaman baru */
            margin-left: 20mm;
            margin-right: 20mm;
            margin-bottom: 20mm;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
        }

        .kata-pengantar {
            margin-top: 40mm; /* Margin atas untuk konten */
        }
        .hasil-survey {
            margin-top: 0mm; /* Margin atas untuk konten */
        }
        .solusi-alih-daya {
            margin-top: 0mm; /* Margin atas untuk konten */
        }
        .pendahuluan {
            margin-top: 20mm; /* Margin atas untuk konten */
        }
        .bordered table {
            border-collapse: collapse; /* Menghilangkan jarak antar border */
            width: 100%; /* Mengisi lebar penuh halaman */
            margin-top: 20px; /* Jarak atas tabel */
        }
        .bordered th,.bordered td {
            border: 1px solid black; /* Border solid 1px hitam */
            text-align: left; /* Rata kiri untuk teks dalam sel */
            color:black !important;
            padding-left:5px;
            padding-right:5px;

        }

        .no-l-border {
            border-left:none !important;
        }
        .bordered th {
            background-color: #f2f2f2; /* Warna latar belakang untuk header */
        }

        @media print {
            th {
                background-color: #f2f2f2 !important; /* Mengatur warna latar belakang header saat mencetak */
                color: black; /* Pastikan teks berwarna hitam */
            }
            /* Menghilangkan warna latar belakang untuk sel data jika diinginkan */
            td {
                background-color: white !important; /* Mengatur latar belakang sel data saat mencetak */
            }
        }

    </style>
</head>
<body>

    <!-- Halaman 1: Cover -->
    <div class="cover">
        <img src="{{ asset('public/assets/img/cover-quotation-1.png') }}" alt="Cover Image">
    </div>

    <!-- Halaman 2: Kata Pengantar -->
    <div class="content">
        <div class="kata-pengantar"><br><br><br>
            <h1>SURAT <br>
            PENGANTAR</h1>
            <br>
<p>Dengan hormat,</p>
<p>Kami, <b>Shelter Indonesia</b> mengucapkan salam sejahtera untuk Bpk/Ibu {{$leads->pic}} dan seluruh jajaran
manajemen di <b>{{$leads->nama_perusahaan}}</b>. Sehubungan dengan semakin kompleksnya
tuntutan bisnis dimasa sekarang, kami ingin menawarkan layanan yang dapat membantu
meningkatkan efisiensi dan produktivitas operasional perusahaan Bpk/Ibu.</p>
<p><b>Shelter Indonesia</b> merupakan perusahaan yang berdedikasi untuk memberikan solusi Alih
Daya terkini dan berkualitas tinggi kepada pelanggan kami. Kami memiliki pengalaman yang
luas dalam menyediakan berbagai layanan Alih Daya di berbagai sektor industri. Melalui
pendekatan yang terintegrasi dengan teknologi dan berorientasi pada kualitas, kami
bertekad untuk menjadi mitra yang handal dalam mendukung kesuksesan dan pertumbuhan
perusahaan Bpk/Ibu.</p>
<p>
Kami berharap dapat mengadakan pertemuan lebih lanjut untuk membahas proposal
penawaran kami lebih detail. Terlampir, kami sertakan proposal lengkap yang merinci
layanan-layanan yang kami tawarkan bersama dengan biaya yang terkait.</p>

<p>Demikian surat pengantar ini kami sampaikan. Kami mengucapkan terima kasih atas
perhatian Bpk/Ibu, dan kami sangat berharap dapat menjadi mitra strategis yang membantu
mencapai tujuan bisnis perusahaan Bpk/Ibu.
</p>
<p>
Hormat kami,
<br><br><br>
<b>{{Auth::user()->full_name}}</b>
<br>{{Auth::user()->role}}
</p>
</div>
</div>
<!-- Halaman 3: HASIL SURVEY -->
<div class="content">
    <div class="hasil-survey">
        <br><br><br>
        <h1>HASIL <br>SURVEY</h1>
        <br>
    </div>
</div>
<!-- Halaman 4: SOLUSI ALIH DAYA -->
<div class="content">
    <div class="solusi-alih-daya">
        <br><br><br>
        <h1 style="text-align: left !important;">SOLUSI ALIH DAYA BESERTA FITUR LAYANANNYA</h1>
        <br>
    </div>
</div>
<div class="content">
    <div style="margin-top:50px;margin-right:20px;color:black !important;font-size:10pt !important">
        <p style="text-align:center;color:#002060;">[Lampiran Penawaran Harga Jasa Alih Daya Sesuai dengan Solusi atas Hasil Survey]<br>
        PENAWARAN HARGA JASA ALIH TENAGA {{strtoupper($quotation->kebutuhan)}}<br>
        @foreach($quotation->site as $site)
        {{strtoupper($site->nama_site)}}<br>
        @endforeach
        TAHUN {{$quotation->tahun_quotation}}</p>

        <table class="bordered">
            <thead class="text-center">
                <tr class="table-success">
                <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;text-align:center">BREAKDOWN PRICING {{$quotation->kebutuhan}}</th>
                </tr>
                <tr class="table-success">
                <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;text-align:center">{{$leads->nama_perusahaan}}</th>
                </tr>
                <tr class="table-success">
                <th>&nbsp;</th>
                @foreach($quotation->site as $site)
                <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;text-align:center">{{$site->nama_site}}</th>
                @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                <td class="fw-bold" style="text-align:center">Structure</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</th>
                @endforeach
                </tr>
                <tr>
                <td class="fw-bold">Jumlah Personil</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <th class="text-center">{{$detailJabatan->jumlah_hc}}</th>
                @endforeach
                </tr>
                <tr>
                <td class="fw-bold">1. BASE MANPOWER COST</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-center" class="text-center fw-bold">Unit/Month</th>
                @endforeach
                </tr>
                <tr>
                <td>Upah/Gaji</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</th>
                @endforeach
                </tr>
                @foreach($daftarTunjangan as $it => $tunjangan)
                <tr>
                <td>{{$tunjangan->nama}} &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-delete text-danger delete-tunjangan" data-nama="{{$tunjangan->nama}}"></i></a></th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</th>
                @endforeach
                </tr>
                @endforeach
                <tr class="table-success">
                <td class="fw-bold text-center">TOTAL</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</th>
                @endforeach
                </tr>
                <tr>
                <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-center fw-bold">Unit/Month</th>
                @endforeach
                </tr>
                @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan" || $quotation->thr=="Diberikan Langsung")
                <tr>
                <td>Provisi Tunjangan Hari Raya (THR)</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">@if($quotation->thr=="Ditagihkan")Ditagihkan Terpisah @elseif($quotation->thr=="Diberikan Langsung") Diberikan Langsung Oleh Client @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</th>
                @endforeach
                </tr>
                @endif
                @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                <tr>
                <td>Provisi Kompensasi</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">@if($quotation->kompensasi=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @endif</th>
                @endforeach
                </tr>
                @endif
                @if($quotation->tunjangan_holiday=="Flat" || $quotation->tunjangan_holiday=="Normatif")
                <tr>
                <td>Tunjangan Hari Libur Nasional</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">@if($quotation->tunjangan_holiday=="Flat") {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @else Ditagihkan Terpisah @endif</th>
                @endforeach
                </tr>
                @endif
                @if($quotation->lembur=="Flat" || $quotation->lembur=="Normatif")
                <tr>
                    <td>Lembur</th>
                    @foreach($quotation->quotation_detail as $detailJabatan)
                    <td class="text-end">@if($quotation->lembur=="Flat") {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @else Ditagihkan Terpisah @endif</th>
                    @endforeach
                </tr>
                @endif
                <tr>
                    <td>BPJS Kesehatan ( {{number_format($quotation->persen_bpjs_kesehatan,2,",",".")}}% )</th>
                    @foreach($quotation->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kesehatan,2,",",".")}}</th>
                    @endforeach
                </tr>
                <tr>
                    <td>BPJS Ketenagakerjaan ( {{number_format($quotation->persen_bpjs_ketenagakerjaan,2,",",".")}}% )</th>
                    @foreach($quotation->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_ketenagakerjaan,2,",",".")}}</th>
                    @endforeach
                <tr>
                <td>Provisi Seragam</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap_coss,2,",",".")}}</th>
                @endforeach
                </tr>
                <tr>
                <td>Provisi Peralatan</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices_coss,2,",",".")}}</th>
                @endforeach
                </tr>
                @if($quotation->kebutuhan_id==3)
                <tr>
                <td>Provisi Chemical</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical_coss,2,",",".")}}</th>
                @endforeach
                </tr>
                @endif
                <tr class="table-success">
                    <td class="fw-bold text-center">TOTAL</th>
                    @foreach($quotation->quotation_detail as $detailJabatan)
                    <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</th>
                    @endforeach
                </tr>
                <tr class="table-success">
                    <td class="fw-bold text-center">BIAYA PENGAWASAN DAN PELAKSANAAN LAPANGAN ( UNIT / MONTH )</th>
                    @foreach($quotation->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_ohc_coss,2,",",".")}}</td>
                    @endforeach
                </tr>
                <!-- <tr> -->
                <!-- <td class="fw-bold">3. BIAYA MONITORING & KONTROL</th>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td class="text-center fw-bold">Unit/Month</th>
                @endforeach
                </tr>
                <tr>
                <td style="text-align:left">Biaya Visit & Kontrol Operasional, visit CRM</td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td rowspan="5" style="text-align:right;font-weight:bold"><span data-quotation_detail_id="{{$detailJabatan->id}}" data-quotation_id="{{$detailJabatan->quotation_id}}" class="edit-biaya-monitoring">Rp {{number_format($detailJabatan->biaya_monitoring_kontrol,2,",",".")}} <i class="mdi mdi-pencil text-warning"></i></span></td>
                @endforeach
                </tr>
                <tr>
                <td style="text-align:left">Biaya Komunikasi Rekrutmen, Pembinaan, Training Induction & Supervisi</td>
                </tr>
                <tr>
                <td style="text-align:left">Biaya Proses Kontrak Karyawan, Payroll, dll</td>
                </tr>
                <tr>
                <td style="text-align:left">Biaya Emergency Response Team</td>
                </tr>
                <tr>
                <td style="text-align:left">Biaya Investigasi Team</td>
                </tr> -->
                <tr class="table-success">
                    <td style="text-align:right" class="fw-bold">Total Biaya per Personil <span class="text-danger">(1+2+3)</span></td>
                    @foreach($quotation->quotation_detail as $detailJabatan)
                    <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil_coss,2,",",".")}}</td>
                    @endforeach
                </tr>
                <tr class="">
                <td style="text-align:right" class="fw-bold">Harga Per Personel x Jumlah Personel</td>
                @foreach($quotation->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil_coss,2,",",".")}}</td>
                @endforeach
                </tr>
                <tr class="">
                    <td style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->nominal_management_fee_coss,2,",",".")}}</td>
                </tr>
                <tr class="table-success">
                    <td style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->grand_total_sebelum_pajak_coss,2,",",".")}}</td>
                </tr>
                <tr class="">
                    <td style="text-align:right" class="fw-bold">Dasar Pengenaan Pajak</td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->dpp_coss,2,",",".")}}</td>
                </tr>
                <tr class="">
                    <td style="text-align:right" class="fw-bold">PPN <span class='text-danger'>*dari management fee</span> ( 12 % )</td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->ppn_coss,2,",",".")}}</td>
                </tr>
                <tr class="">
                    <td style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span> ( -2 % )</td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pph_coss,2,",",".")}}</td>
                </tr>
                <tr class="table-success">
                    <td style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_invoice_coss,2,",",".")}}</td>
                </tr>
                <tr class="table-success">
                    <td style="text-align:right" class="fw-bold">PEMBULATAN</td>
                    <td style="text-align:right" class="" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pembulatan_coss,2,",",".")}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-3" style="padding-left:40px;color:black !important;font-size:9pt !important">
        <p><b><i>Note :</i></b>	<br>
        {!! $quotation->note_harga_jual !!}</p>
</div>
</div>
<div class="content">
    <h1>SYARAT DAN <br>
    KETENTUAN</h1>
    <br>
    <table style="border-spacing: 10px; width:100%">
        <thead>
        </thead>
        <tbody>
            @foreach ($listKerjasama as $key => $kerjasama)
            <tr>
                <td style="vertical-align: top;">{{$key+1}}.</td>
                <td>{!! $kerjasama->perjanjian !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
<script>
        window.print();
    </script>
</html>
