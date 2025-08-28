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
<p>Kami, <b>Shelter Indonesia</b> mengucapkan salam sejahtera untuk Bpk/Ibu dan seluruh jajaran
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
<b>Achmad Firmanto</b>
<br>Business Development
<br>+62 822 404 480 55
</p>
</div>
</div>
<div class="content">
    <div style="margin-top:50px;margin-right:20px;color:black !important;font-size:10pt !important">
        <table class="bordered">
            <thead class="text-center">
                <tr class="table-success">
                <th colspan="{{3+count($data->quotation_detail)}}" style="vertical-align: middle;text-align:center">BREAKDOWN PRICING {{$master->kebutuhan}}</th>
                </tr>
                <tr class="table-success">
                <th colspan="{{3+count($data->quotation_detail)}}" style="vertical-align: middle;text-align:center">{{$leads->nama_perusahaan}}</th>
                </tr>
                <tr class="table-success">
                <th colspan="{{3+count($data->quotation_detail)}}" style="vertical-align: middle;text-align:center">{{$master->nama_site}}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td class="fw-bold" style="text-align:center">Structure</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</th>
                @endforeach
                </tr>
                <tr>
                <td class="fw-bold">Jumlah Personil</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <th class="text-center">{{$detailJabatan->jumlah_hc}}</th>
                @endforeach
                </tr>
                <tr>
                <td class="fw-bold">1. BASE MANPOWER COST</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-center" class="text-center fw-bold">Unit/Month</th>
                @endforeach
                </tr>
                <tr>
                <td>Upah/Gaji</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($master->nominal_upah,2,",",".")}}</th>
                @endforeach
                </tr>
                @foreach($daftarTunjangan as $it => $tunjangan)
                <tr>
                <td>{{$tunjangan->nama}} &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-delete text-danger delete-tunjangan" data-nama="{{$tunjangan->nama}}"></i></a></th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</th>
                @endforeach
                </tr>
                @endforeach
                <tr class="table-success">
                <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</th>
                @endforeach
                </tr>
                <tr>
                <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-center fw-bold">Unit/Month</th>
                @endforeach
                </tr>
                @if($master->thr=="Ditagihkan" || $master->thr=="Diprovisikan")
                <tr>
                <td>Provisi Tunjangan Hari Raya (THR)</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">@if($master->thr=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</th>
                @endforeach
                </tr>
                @endif
                @if($master->kompensasi=="Ditagihkan" || $master->kompensasi=="Diprovisikan")
                <tr>
                <td>Provisi Kompensasi</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">@if($master->kompensasi=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @endif</th>
                @endforeach
                </tr>
                @endif
                @if($master->tunjangan_holiday=="Flat" || $master->tunjangan_holiday=="Normatif")
                <tr>
                <td>Tunjangan Hari Libur Nasional</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}}</th>
                @endforeach
                </tr>
                @endif
                @if($master->lembur=="Flat" || $master->lembur=="Normatif")
                <tr>
                <td>Lembur</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">@if($master->lembur=="Normatif") <b>Normatif</b> @else {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif</th>
                @endforeach
                </tr>
                @endif
                @if($master->penjamin=="BPJS")
                <tr>
                    <td>Premi BPJS TK J. Kecelakaan Kerja ( @if($master->resiko=="Sangat Rendah") 0,24 @elseif($master->resiko=="Rendah") 0,54 @elseif($master->resiko=="Sedang") 0,89 @elseif($master->resiko=="Tinggi") 1,27 @elseif($master->resiko=="Sangat Tinggi") 1,74 @endif % )</th>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,2,",",".")}}</th>
                    @endforeach
                </tr>
                <tr>
                    <td>Premi BPJS TK J. Kematian ( 0,30 % )</th>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,2,",",".")}}</th>
                    @endforeach
                </tr>
                @if($master->program_bpjs=="3 BPJS" || $master->program_bpjs=="4 BPJS")
                <tr>
                    <td>Premi BPJS TK J. Hari Tua ( 3,7 % )</th>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jht,2,",",".")}}</th>
                    @endforeach
                </tr>
                @endif
                @if($master->program_bpjs=="4 BPJS")
                <tr>
                    <td>Premi BPJS TK J. Pensiun ( 2 % )</th>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jp,2,",",".")}}</th>
                    @endforeach
                </tr>
                @endif
                <tr>
                    <td>Premi BPJS Kesehatan ( 4 % )</th>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kes,2,",",".")}}</th>
                    @endforeach
                </tr>
                @else
                <tr>
                    <td>Asuransi Kesehatan Swasta</th>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</th>
                    @endforeach
                </tr>
                @endif
                <tr>
                <td>Provisi Seragam</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format((ceil($detailJabatan->personil_kaporlap / 1000) * 1000),2,",",".")}}</th>
                @endforeach
                </tr>
                <tr>
                <td>Provisi Peralatan</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format((ceil($detailJabatan->personil_devices / 1000) * 1000),2,",",".")}}</th>
                @endforeach
                </tr>
                <tr>
                <td>Provisi Chemical</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-end">{{"Rp. ".number_format((ceil($detailJabatan->personil_chemical / 1000) * 1000),2,",",".")}}</th>
                @endforeach
                </tr>
                <tr class="table-success">
                <td class="fw-bold text-center">Total Exclude Base Manpower Cost</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</th>
                @endforeach
                </tr>
                <tr>
                <!-- <td class="fw-bold">3. BIAYA MONITORING & KONTROL</th>
                @foreach($data->quotation_detail as $detailJabatan)
                <td class="text-center fw-bold">Unit/Month</th>
                @endforeach
                </tr>
                <tr>
                <td style="text-align:left">Biaya Visit & Kontrol Operasional, visit CRM</td>
                @foreach($data->quotation_detail as $detailJabatan)
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
                @foreach($data->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil_coss,2,",",".")}}</td>
                @endforeach
                </tr>
                <tr class="">
                <td style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                @foreach($data->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil_coss,2,",",".")}}</td>
                @endforeach
                </tr>
                <tr class="">
                <td style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari sub total biaya</span> ( {{$master->persentase}} % )</td>
                @foreach($data->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee_coss,2,",",".")}}</td>
                @endforeach
                </tr>
                <tr class="">
                    <td style="text-align:right" class="fw-bold">Over Head Cost</td>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_ohc,2,",",".")}}</td>
                    @endforeach
                </tr>
                <tr class="table-success">
                <td style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                @foreach($data->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total_coss,2,",",".")}}</td>
                @endforeach
                </tr>
                <tr class="">
                    <td style="text-align:right" class="fw-bold">PPN <span class='text-danger'>*dari management fee</span> ( 12 % )</td>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn_coss,2,",",".")}}</td>
                    @endforeach
                </tr>
                <tr class="">
                    <td style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span> ( -2 % )</td>
                    @foreach($data->quotation_detail as $detailJabatan)
                    <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph_coss,2,",",".")}}</td>
                    @endforeach
                </tr>
                <tr class="table-success">
                <td style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                @foreach($data->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice_coss,2,",",".")}}</td>
                @endforeach
                </tr>
                <tr class="table-success">
                <td style="text-align:right" class="fw-bold">PEMBULATAN</td>
                @foreach($data->quotation_detail as $detailJabatan)
                <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan_coss,2,",",".")}}</td>
                @endforeach
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-3" style="padding-left:40px;color:black !important;font-size:9pt !important">
        <p><b><i>Note :</i></b>	<br>
        <b>Upah pokok base on Umk 2024 </b> <br>
        Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
        Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
        @if($master->program_bpjs=="2 BPJS")
        BPJS Ketenagakerjaan 2 Program (JKK, JKM).
        @elseif($master->program_bpjs=="3 BPJS")
        BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT).
        @elseif($master->program_bpjs=="4 BPJS")
        BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP).
        @endif
<span class="text-danger">Pengalian base on upah</span>		<br>
BPJS Kesehatan. <span class="text-danger">*base on Umk 2024</span> <br>
<br>
<span class="text-danger">*prosentase Bpjs Tk J. Kecelakaan Kerja disesuaikan dengan tingkat resiko sesuai ketentuan.</span>
</p>
</div>
</div>
        <!-- Halaman 4: Pendahuluan -->
        <div class="content">
            <div class="pendahuluan">
                <h1>KETENTUAN PENAWARAN HARGA</h1>
                <h1>JASA {{strtoupper($data->kebutuhan)}}</h1>
                <div style="margin-top:20px">
                    <table>
                        @foreach($listKerjasama as $kker => $valker)
                        <tr>
                            <td style="width:10%;vertical-align: top;">{{$kker+1}}.</td>
                            <td>{!! $valker->perjanjian !!}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

</body>
<script>
        window.print();
    </script>
</html>
