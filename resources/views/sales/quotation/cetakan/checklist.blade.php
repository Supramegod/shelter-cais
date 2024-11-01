<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHECKLIST - {{$leads->nama_perusahaan}}</title>
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
              padding-left:10px !important;
              border:1px solid black;
            }

        }
    </style>
</head>
<body>
<div class="container-fluid flex-grow-1 container-p-y" style="background-color:white">
<form class="overflow-hidden" action="{{route('quotation.save-edit-12')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="row">
                <div>
                  <table>
                      <tbody>
                        <tr>
                        <td colspan="4" class="text-center fw-bold" style="background-color:'#e8e8e8'">FORM <i>CHECKLIST</i> NEW SITE & PERJANJIAN KERJA SAMA</td>
                        </tr>
                        <tr>
                          <td>No. Quotation</td>
                          <td colspan="3">{{$quotationKebutuhan[0]->nomor}}</td>
                        </tr>
                        <tr>
                          <td style="width:30%">Tgl. Pengajuan Kerjasama</td>
                          <td colspan="3">{{$quotation->tgl_quotation}}</td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-center fw-bold ">PERSONAL INFORMASI</td>
                        </tr>
                        <tr>
                          <td>Pengusul Kerjasama</td>
                          <td colspan="3" class="fw-bold">{{$quotation->nama_perusahaan}}</td>
                        </tr>
                        <tr>
                          <td>Alamat Pengusul Kerjasama</td>
                          <td colspan="3">{{$leads->alamat}}</td>
                        </tr>
                        <tr>
                          <td>No. Telp Perusahaan</td>
                          <td colspan="3">@if($leads->telp_perusahaan!=null) {{$leads->telp_perusahaan}} @else - @endif</td>
                        </tr>
                        <tr>
                          <td>Penerima Kerjasama</td>
                          <td colspan="3" class="fw-bold">{{$quotationKebutuhan[0]->company}}</td>
                        </tr>
                        <tr>
                          <td>Hal Kerjasama</td>
                          <td colspan="3">PERJANJIAN KERJASAMA ALIH DAYA JASA {{strtoupper($quotationKebutuhan[0]->kebutuhan)}}</td>
                        </tr>
                        <tr>
                          <td>Jumlah Personel</td>
                          <td colspan="3">{{$quotationKebutuhan[0]->jumlah_personel}}</td>
                        </tr>
                        <tr>
                          <td>PIC</td>
                          <td colspan="3">{{$leads->pic}}</td>
                        </tr>
                        <tr>
                          <td>Jabatan PIC </td>
                          <td colspan="3">{{$leads->jabatan}}</td>
                        </tr>
                        <tr>
                          <td>No. Telp PIC </td>
                          <td colspan="3">{{$leads->no_telp}}</td>
                        </tr>
                        <tr>
                          <td>NPWP  </td>
                          <td colspan="3">{{$quotation->npwp}}</td>
                        </tr>
                        <tr>
                          <td>Alamat NPWP </td>
                          <td colspan="3">{{$quotation->alamat_npwp}}</td>
                        </tr>
                        <tr>
                          <td>PIC Invoice </td>
                          <td colspan="3">
                            <table class="table-bordered" style="width:80%;margin:5px">
                              <thead>
                                <tr>
                                  <th>Nama</th>
                                  <th>Jabatan</th>
                                  <th>No. Telp</th>
                                  <th>Email</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($listPic as $kpic => $pic)
                                <tr>
                                  <td>{{$pic->nama}}</td>
                                  <td>{{$pic->jabatan}}</td>
                                  <td>{{$pic->no_telp}}</td>
                                  <td>{{$pic->email}}</td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-center fw-bold ">INFORMASI KERJASAMA</td>
                        </tr>
                        <tr>
                          <td>Durasi Kerjasama</td>
                          <td>{{$quotation->durasi_kerjasama}}</td>
                          <td>Evaluasi {{$quotation->evaluasi_kontrak}}</td>
                          <td>{{$quotation->mulai_kontrak}} - {{$quotation->kontrak_selesai}}</td>
                        </tr>
                        <tr>
                          <td>Kontrak Karyawan</td>
                          <td>{{$quotation->jenis_kontrak}} {{$quotation->durasi_karyawan}}</td>
                          <td>Evaluasi {{$quotation->evaluasi_karyawan}}</td>
                          <td>Start {{$quotation->tgl_penempatan}}</td>
                        </tr>
                        <tr>
                          <td>Materai </td>
                          <td colspan="3">{{$quotation->materai}}</td>
                        </tr>
                        <tr>
                          <td>Hari Kerja dan Jam Kerja </td>
                          <td>{{$quotation->shift_kerja}}</td>
                          <td>{{$quotation->jam_kerja}}</td>
                          <td>{{$quotation->mulai_kerja}} s/d {{$quotation->selesai_kerja}}</td>
                        </tr>
                        <tr>
                          <td>System Kerja </td>
                          <td colspan="3">@if($quotation->lembur=="Tidak Ada") No Work No Pay @elseif($quotation->lembur!="") Ada Lembur @endif
                        </tr>
                        <tr>
                          <td>Kebijakan Cuti</td>
                          <td>{{$quotation->cuti}}</td>
                          <td>{{$quotation->gaji_saat_cuti}}</td>
                          <td>{{$quotation->prorate}} @if($quotation->prorate !=null) % @endif</td>
                        </tr>
                        <tr>
                          <td>Kunjungan Operasional </td>
                          <td>{{explode(" ",$quotation->kunjungan_operasional)[0]}} Kali dalam 1 {{explode(" ",$quotation->kunjungan_operasional)[1]}}</td>
                          <td colspan="2">{{$quotation->keterangan_kunjungan_operasional}}</td>
                        </tr>
                        <tr>
                          <td>Kunjungan Tim CRM </td>
                          <td>{{explode(" ",$quotation->kunjungan_tim_crm)[0]}} Kali dalam 1 {{explode(" ",$quotation->kunjungan_tim_crm)[1]}}</td>
                          <td colspan="2">{{$quotation->keterangan_kunjungan_tim_crm}}</td>
                        </tr>
                        <tr>
                          <td>Training </td>
                          <td colspan="3">{{$quotation->training}}</td>
                        </tr>
                        <tr id="list-training">
                          <td colspan="4">@foreach($listTrainingQ as $training) {{$training->nama}} @if(!$loop->last), @endif @endforeach</td>
                        </tr>
                        <tr>
                          <td>Tunjangan Hari Raya (THR)</td>
                          <td colspan="3">
                          @if($quotation->thr=="Tidak Ada")
                          <b>Tidak Ada</b>
                          @else
                            <b>{{$quotation->thr}}</b> terpisah H-45 hari raya base on upah pokok
                            <table class="table-bordered" style="width:100%;margin:5px">
                              <tr>
                                <td class="text-center"><b>No.</b></td>
                                <td class="text-center"><b>Schedule Plan</b></td>
                                <td class="text-center"><b>Time</b></td>
                              </tr>
                              <tr>
                                <td class="text-center">1</td>
                                <td>Penagihan Invoice THR </td>
                                <td>ditagihkan H-45</td>
                              </tr>
                              <tr>
                                <td class="text-center">2</td>
                                <td>Pembayaran Invoice THR</td>
                                <td>Maksimal h-14 hari raya</td>
                              </tr>
                              <tr>
                                <td class="text-center">3</td>
                                <td>Rilis THR</td>
                                <td>Maksimal h-7 Hari Raya</td>
                              </tr>
                            </table>
                          </td>
                          @endif
                        </tr>
                        @if($quotationKebutuhan[0]->penjamin=="Takaful")
                        <tr>
                          <td>Penjamin</td>
                          <td colspan="3">{{$quotationKebutuhan[0]->penjamin}}</td>
                        </tr>
                        @else
                        <tr>
                          <td>BPJS Ketenagakerjaan</td>
                          <td colspan="3">
                            <table class="table table-bordered" style="width:100%">
                              <thead>
                                <tr>
                                  <th class="text-center"><b>Deskripsi</b></th>
                                  <th class="text-center"><b>Perusahaan</b></th>
                                  <th class="text-center"><b>Tenaga Kerja</b></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center">JKK</td>
                                  <td class="text-center">@if($quotationKebutuhan[0]->resiko=="Sangat Rendah") 0,24 @elseif($quotationKebutuhan[0]->resiko=="Rendah") 0,54 @elseif($quotationKebutuhan[0]->resiko=="Sedang") 0,89 @elseif($quotationKebutuhan[0]->resiko=="Tinggi") 1,27 @elseif($quotationKebutuhan[0]->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                                  <td class="text-center">&nbsp;</td>
                                </tr>
                                <tr>
                                  <td class="text-center">JKM</td>
                                  <td class="text-center">0,3 %</td>
                                  <td class="text-center">&nbsp;</td>
                                </tr>
                                @if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                                <tr>
                                  <td class="text-center">JHT</td>
                                  <td class="text-center">3,7 %</td>
                                  <td class="text-center">2%</td>
                                </tr>
                                @endif
                                @if($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                                <tr>
                                  <td class="text-center">JP</td>
                                  <td class="text-center">2 %</td>
                                  <td class="text-center">1 %</td>
                                </tr>
                                @endif
                              </tbody>
                            </table>
                            <i>*base on Upah Pokok</i>
                          </td>
                        </tr>
                        <tr>
                          <td>BPJS Kesehatan</td>
                          <td colspan="3">
                            <table class="table table-bordered" style="width:100%">
                              <thead>
                                <tr>
                                  <th class="text-center"><b>Deskripsi</b></th>
                                  <th class="text-center"><b>Perusahaan</b></th>
                                  <th class="text-center"><b>Tenaga Kerja</b></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center">Kesehatan</td>
                                  <td class="text-center">4 %</td>
                                  <td class="text-center">1 %</td>
                                </tr>
                              </tbody>
                            </table>
                            <i>*Base on UMK update</i>
                          </td>
                        </tr>
                        @endif
                        <tr>
                          <td>Seragam</td>
                          <td colspan="3">detil terlampir</td>
                        </tr>
                        <tr>
                          <td>Kompensasi</td>
                          <td colspan="3">
                            {{$quotation->kompensasi}}
                          </td>
                        </tr>
                        <tr>
                          <td>Joker / Reliever </td>
                          <td colspan="3">{{$quotation->joker_reliever}}</td>
                        </tr>
                        <tr>
                          <td>Syarat Invoice </td>
                          <td colspan="3">{{$quotation->syarat_invoice}}</td>
                        </tr>
                        <tr>
                          <td><i>Term of Payment</i>&nbsp;<b>(TOP)</b></td>
                          <td colspan="3"><b>Talangan @if($quotation->top=="Lebih Dari 7 Hari"){{$quotation->jumlah_hari_invoice}} hari {{$quotation->tipe_hari_invoice}} @else $quotation->top @endif setelah invoice & lampiran diterima</b></td>
                        </tr>
                        <tr>
                          <td>Skema Cut Off, Invoice,Payroll dan Pembayaran
<br><br>
                          <i>(Wajib dilampirkan di dalam PKS)</i>
                          </td>
                          <td colspan="3">
                            <table class="table-bordered" style="width:95%;margin:5px">
                              <thead>
                                <tr>
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-center"><b>Schedule Plan</b></th>
                                  <th class="text-center"><b>Periode</b></th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td class="text-center">1</td>
                                  <td>Cut Off</td>
                                  <td>{{$salaryRuleQ->cutoff}}</td>
                                </tr>
                                <tr>
                                  <td class="text-center">2</td>
                                  <td>Crosscheck Absensi</td>
                                  <td>{{$salaryRuleQ->crosscheck_absen}}</td>
                                </tr>
                                <tr>
                                  <td class="text-center">3</td>
                                  <td>Pengiriman <i>Invoice</i></td>
                                  <td>{{$salaryRuleQ->pengiriman_invoice}}</td>
                                </tr>
                                <tr>
                                  <td class="text-center">4</td>
                                  <td>Perkiraan <i>Invoice</i> Diterima Pelanggan</td>
                                  <td>{{$salaryRuleQ->perkiraan_invoice_diterima}}</td>
                                </tr>
                                <tr>
                                  <td class="text-center">5</td>
                                  <td>Pembayaran <i>Invoice</i></td>
                                  <td>{{$salaryRuleQ->pembayaran_invoice}}</td>
                                </tr>
                                <tr>
                                  <td class="text-center">6</td>
                                  <td>Rilis <i>Payroll</i> / Gaji</td>
                                  <td>{{$salaryRuleQ->rilis_payroll}}</td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                        <tr>
                          <td>Lembur </td>
                          <td colspan="3">{{$quotation->lembur}}</td>
                        </tr>
                        <tr>
                          <td>Alamat Penagihan Invoice </td>
                          <td colspan="3">{{$quotation->alamat_penagihan_invoice}}</td>
                        </tr>
                        <tr>
                          <td>Catatan Site </td>
                          <td colspan="3">{{$quotation->catatan_site}}</td>
                        </tr>
                        <tr>
                          <td>Status Serikat </td>
                          <td colspan="3">{{$quotation->status_serikat}}</td>
                        </tr>
                        <tr>
                          <td>Penempatan/serah terima</td>
                          <td colspan="3">Start serah terima tanggal {{$quotation->tgl_penempatan}}</td>
                        </tr>
                      </tbody>
                  </table>
                </div>
              </div>
            </div>
          </form>
          <div style="margin-top:50px">
            <table style="width:100%">
                <tr>
                    <td class="text-end">{{$now}}</td>
                </tr>
            </table>
            <table style="width:100%" class="table-bordered">
                <thead>
                    <tr>
                        <th colspan="7" class="text-center">Mengetahui</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Recruitment<br>&nbsp;</td>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Dept. HRD<br>&nbsp;</td>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Dept. Ops<br>&nbsp;</td>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Dept. FAL<br>&nbsp;</td>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Dept. CRM<br>&nbsp;</td>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Sales<br>Manager</td>
                        <td class="text-center fw-bold"><br><br><br>................. <br>Branch Manager</td>





                    </tr>
                </tbody>
            </table>
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