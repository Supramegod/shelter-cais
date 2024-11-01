<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COST STRUCTURE - {{$leads->nama_perusahaan}}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            font-size:11pt;
            text-align: justify;
            margin-right:20px;
        }
        @media print {
            .page-break-before { 
                page-break-before: always; 
            }
            .page-break-after { 
                page-break-after: always; 
            }
            .avoid-page-break-inside { 
                page-break-inside: avoid; 
            }
            table{
              width: 100%;
            }
            table>*>*>* {
              padding-top:0px !important;
              padding-bottom:0px !important;
              border:1px solid black;
            }

        }
    </style>
</head>
<body>
<div class="row page-break-after">
                <div style="margin-right:20px">
                  <table>
                    <thead class="text-center">
                      <tr class="table-success">
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">COST STRUCTURE {{$data->kebutuhan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}</th>
                      </tr>
                    </thead>              
                    <tbody>
                      <tr>
                        <td class="fw-bold">Structure</th>
                        <td class="text-center fw-bold">%</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">Jumlah Personil</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th class="text-center">{{$detailJabatan->jumlah_hc}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">1. BASE MANPOWER COST</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center" class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Upah/Gaji</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($quotationKebutuhan[0]->nominal_upah,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr>
                        <td>{{$tunjangan->nama}} &nbsp; <a href="javascript:void(0)"><i class="mdi mdi-delete text-danger delete-tunjangan" data-nama="{{$tunjangan->nama}}"></i></a></th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</th>
                        @endforeach
                      </tr>
                      @endforeach
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                      <tr>
                        <td>Provisi Tunjangan Hari Raya (THR)</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->thr=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</th>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                      <tr>
                        <td>Provisi Kompensasi</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->kompensasi=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @endif</th>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->tunjangan_holiday=="Flat" || $quotation->tunjangan_holiday=="Normatif")
                      <tr>
                        <td>Tunjangan Holiday</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotation->lembur=="Flat" || $quotation->lembur=="Normatif")
                      <tr>
                        <td>Lembur</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">@if($quotation->lembur=="Normatif") <b>Normatif</b> @else {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif</th>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotationKebutuhan[0]->penjamin=="BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Kecelakaan Kerja</th>
                          <td class="text-center">@if($quotationKebutuhan[0]->resiko=="Sangat Rendah") 0,24 @elseif($quotationKebutuhan[0]->resiko=="Rendah") 0,54 @elseif($quotationKebutuhan[0]->resiko=="Sedang") 0,89 @elseif($quotationKebutuhan[0]->resiko=="Tinggi") 1,27 @elseif($quotationKebutuhan[0]->resiko=="Sangat Tinggi") 1,74 @endif %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Premi BPJS TK J. Kematian</th>
                          <td class="text-center">0,30 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        @if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Hari Tua</th>
                          <td class="text-center">3,7 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jht,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                        <tr>
                          <td>Premi BPJS TK J. Pensiun</th>
                          <td class="text-center">2 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jp,2,",",".")}}</th>
                          @endforeach
                        </tr>
                        @endif
                        <tr>
                          <td>Premi BPJS Kesehatan</th>
                          <td class="text-center">4 %</th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kes,2,",",".")}}</th>
                          @endforeach
                        </tr>
                      @else
                        <tr>
                          <td>Takaful</th>
                          <td class="text-center"></th>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_takaful,2,",",".")}}</th>
                          @endforeach
                        </tr>
                      @endif
                      <tr>
                        <td>Provisi Seragam</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Peralatan</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Chemical</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Exclude Base Manpower Cost</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">3. BIAYA MONITORING & KONTROL</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td style="text-align:left">Biaya Visit & Kontrol Operasional, visit CRM</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td rowspan="5" style="text-align:right;font-weight:bold"><span data-kebutuhan_detail_id="{{$detailJabatan->id}}" data-quotation_kebutuhan_id="{{$detailJabatan->quotation_kebutuhan_id}}" class="edit-biaya-monitoring">Rp {{number_format($detailJabatan->biaya_monitoring_kontrol,2,",",".")}} <i class="mdi mdi-pencil text-warning"></i></span></td>
                        @endforeach
                      </tr>
                      <tr>
                        <td style="text-align:left">Biaya Komunikasi Rekrutmen, Pembinaan, Training Induction & Supervisi</td>
                        <td style="text-align:center"></td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Biaya Proses Kontrak Karyawan, Payroll, dll</td>
                        <td style="text-align:center"></td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Biaya Emergency Response Team</td>
                        <td style="text-align:center"></td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Biaya Investigasi Team</td>
                        <td style="text-align:center"></td>
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">Total Biaya per Personil <span class="text-danger">(1+2+3)</span></td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari sub total biaya</span></td>
                        <td style="text-align:center">{{$quotationKebutuhan[0]->persentase}} %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                        <tr class="">
                          <td style="text-align:right" class="fw-bold">PPn <span class='text-danger'>*dari management fee</span></td>
                          <td style="text-align:center">11 %</td>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="">
                          <td style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span></td>
                          <td style="text-align:center">-2 %</td>
                          @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan_coss,2,",",".")}}</td>
                        @endforeach
                      </tr>      
                    </tbody>
                  </table>
                </div>
                <div class="mt-3" style="padding-left:40px">
                  <p><b><i>Note :</i></b>	<br>
                  <b>Upah pokok base on Umk 2024 </b> <br>
Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
@if($quotationKebutuhan[0]->program_bpjs=="2 BPJS")
BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
@elseif($quotationKebutuhan[0]->program_bpjs=="3 BPJS")
BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
@elseif($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP). 
@endif
<span class="text-danger">Pengalian base on upah</span>		<br>
BPJS Kesehatan. <span class="text-danger">*base on Umk 2024</span> <br>
<br>
<span class="text-danger">*prosentase Bpjs Tk J. Kecelakaan Kerja disesuaikan dengan tingkat resiko sesuai ketentuan.</span>
</p>
</div>
</div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- Custom JavaScript (optional) -->
    <script>
        $(document).ready(function() {
            console.log("jQuery is ready!");
        });
    </script>
    <script>
        window.print();
    </script>
</body>
</html>