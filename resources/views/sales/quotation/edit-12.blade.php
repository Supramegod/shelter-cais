@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<div class="container-fluid flex-grow-1 container-p-y">
  <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales /</span> Quotation Baru</h4> -->
  <!-- Default -->
  <div class="row">
    <!-- Vertical Wizard -->
    <div class="col-12 mb-4">
      <div class="bs-stepper wizard-vertical vertical mt-2">
        @include('sales.quotation.step')
        <div class="bs-stepper-content">
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-12')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">FORM CHECKLIST NEW SITE & PERJANJIAN KERJA SAMA</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
              </div>
              <div class="row mt-5">
                <div class="table-responsive overflow-hidden table-data">
                  <table id="table-data" class="dt-column-search table table-hover" style="padding-right:0px !important">
                      <tbody>
                        <tr>
                          <td>No. Quotation</td>
                          <td colspan="4">{{$quotationKebutuhan[0]->nomor}}</td>
                        </tr>
                        <tr>
                          <td>Tgl. Pengajuan Kerjasama</td>
                          <td colspan="4">{{$quotation->tgl_quotation}}</td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-center fw-bold table-success">PERSONAL INFORMASI</td>
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
                          <td>NPWP <span class="text-danger fw-bold">*</span> </td>
                          <td colspan="3"><input type="text" value="{{$quotation->npwp}}" name="npwp" id="npwp" class="form-control w-100"></td>
                        </tr>
                        <tr>
                          <td>Alamat NPWP <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3"><input type="text" name="alamat_npwp" value="{{$quotation->alamat_npwp}}" id="alamat_npwp" class="form-control w-100"></td>
                        </tr>
                        <tr>
                          <td>PIC Invoice <span class="text-danger fw-bold">*</span></td>
                          <td><input type="text" placeholder="Masukkan nama" name="pic_invoice" value="{{$quotation->pic_invoice}}" id="pic_invoice" class="form-control w-100"></td>
                          <td><input type="text" placeholder="Masukkan No Telp" name="telp_pic_invoice" value="{{$quotation->telp_pic_invoice}}" id="telp_pic_invoice" class="form-control w-100"></td>
                          <td><input type="text" placeholder="Masukkan Email" name="email_pic_invoice" value="{{$quotation->email_pic_invoice}}" id="email_pic_invoice" class="form-control w-100"></td>
                        </tr>
                        <tr>
                          <td colspan="4" class="text-center fw-bold table-success">INFORMASI KERJASAMA</td>
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
                          <td>Materai <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                          <select id="materai" name="materai" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                            <option value="" @if($quotation->materai=='') selected @endif>- Pilih Data -</option>  
                            <option value="Personil" @if($quotation->materai=='Personil') selected @endif>Personil</option>  
                            <option value="Perusahaan" @if($quotation->materai=='Perusahaan') selected @endif>Perusahaan</option>  
                          </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Hari Kerja dan Jam Kerja <span class="text-danger fw-bold">*</span></td>
                          <td>
                            <select id="shift_kerja" name="shift_kerja" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->shift_kerja=='') selected @endif>- Pilih Data -</option>  
                              <option value="Non Shift" @if($quotation->shift_kerja=='Non Shift') selected @endif>Non Shift</option>  
                              <option value="2 Shift" @if($quotation->shift_kerja=='2 Shift') selected @endif>2 Shift</option>  
                              <option value="3 Shift" @if($quotation->shift_kerja=='3 Shift') selected @endif>3 Shift</option>  
                            </select>
                          </td>
                          <td>
                            <select id="jam_kerja" name="jam_kerja" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->jam_kerja=='') selected @endif>- Pilih Data -</option>  
                              <option value="7 Jam Kerja" @if($quotation->jam_kerja=='7 Jam Kerja') selected @endif>7 Jam Kerja</option>  
                              <option value="8 Jam Kerja" @if($quotation->jam_kerja=='8 Jam Kerja') selected @endif>8 Jam Kerja</option>  
                              <option value="12 Jam Kerja" @if($quotation->jam_kerja=='12 Jam Kerja') selected @endif>12 Jam Kerja</option>  
                            </select>
                          </td>
                          <td class="d-flex" style="align-items:center"><input type="time" name="mulai_kerja" value="{{$quotation->mulai_kerja}}" class="form-control w-50"> <span style="padding-left:5px;padding-right:5px">s/d</span> <input type="time" name="selesai_kerja" value="{{$quotation->selesai_kerja}}" class="form-control w-50"></td>
                        </tr>
                        <tr>
                          <td>System Kerja <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <select id="sistem_kerja" name="sistem_kerja" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->sistem_kerja=='') selected @endif>- Pilih Data -</option>  
                              <option value="No Work No Pay" @if($quotation->sistem_kerja=='No Work No Pay') selected @endif>No Work No Pay</option>  
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Kebijakan Cuti <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <select id="cuti" name="cuti" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->cuti=='') selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->cuti=='Ada') selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->cuti=='Tidak Ada') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Kunjungan Operasional <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3"><input type="text" placeholder="" name="kunjungan_operasional" value="{{$quotation->kunjungan_operasional}}" id="kunjungan_operasional" class="form-control w-100"></td>
                        </tr>
                        <tr>
                          <td>Kunjungan Tim CRM <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3"><input type="text" placeholder="" name="kunjungan_tim_crm" value="{{$quotation->kunjungan_tim_crm}}" id="kunjungan_tim_crm" class="form-control w-100"></td>
                        </tr>
                        <tr>
                          <td>Training <span class="text-danger fw-bold">*</span></td>
                          <td>
                            <select id="ada_training" name="ada_training" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->training=='') selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->training!='Tidak Ada' && $quotation->training!='') selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->training=='Tidak Ada') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                          <td colspan="2">
                            <select id="training" name="training" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->training=='') selected @endif>- Pilih Data -</option>  
                              <option value="1 Tahun 1 Kali" @if($quotation->training=='1 Tahun 1 Kali') selected @endif>1 Tahun 1 Kali</option>
                              <option value="1 Tahun 2 Kali" @if($quotation->training=='1 Tahun 2 Kali') selected @endif>1 Tahun 2 Kali</option>
                              <option value="1 Tahun 3 Kali" @if($quotation->training=='1 Tahun 3 Kali') selected @endif>1 Tahun 3 Kali</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Tunjangan Hari Raya (THR)</td>
                          <td colspan="3">
                          @if($quotation->thr=="Tidak Ada")
                          <b>Tidak Ada</b>
                          @else
                            <b>{{$quotation->thr}}</b> terpisah H-45 hari raya base on upah pokok
                            <table class="table table-bordered" style="width:100%">
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
                          <td>Kompensasi <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <select id="kompensasi" name="kompensasi" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->kompensasi=='') selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->kompensasi=='Ada') selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->kompensasi=='Tidak Ada') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Joker / Reliever <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <select id="joker_reliever" name="joker_reliever" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->joker_reliever=='') selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->joker_reliever=='Ada') selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->joker_reliever=='Tidak Ada') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Syarat Invoice <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <textarea rows="7" name="syarat_invoice" id="syarat_invoice" class="form-control">
@if($quotation->syarat_invoice!="" && $quotation->syarat_invoice !=null)
{{$quotation->syarat_invoice}}
@else
Invoice;
Faktur Pajak;
BA Kehadiran ttd Pihak Pertama & Kedua;
Absensi Manual ttd Pihak Pertama & Kedua;
Absensi dari System/Aplikasi.@endif</textarea>
                          </td>
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
                            <table class="table table-bordered" style="width:100%">
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
                          <td>Lembur <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <select id="lembur" name="lembur" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->lembur=='') selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->lembur=='Ada') selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->lembur=='Tidak Ada') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td>Alamat Penagihan Invoice <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3"><input type="text" value="{{$quotation->alamat_penagihan_invoice}}" placeholder="" name="alamat_penagihan_invoice" id="alamat_penagihan_invoice" class="form-control w-100"></td>
                        </tr>
                        <tr>
                          <td>Catatan Site <span class="text-danger fw-bold">*</span></td>
                          <td colspan="3">
                            <textarea rows="3" name="catatan_site" id="catatan_site" class="form-control">{{$quotation->catatan_site}}</textarea>
                          </td>
                        </tr>
                        <tr>
                          <td>Status Serikat <span class="text-danger fw-bold">*</span></td>
                          <td>
                            <select id="ada_serikat" name="ada_serikat" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->status_serikat=='' || $quotation->status_serikat==null) selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->status_serikat!='Tidak Ada' && $quotation->status_serikat!='' && $quotation->status_serikat!=null ) selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->status_serikat=='Tidak Ada') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                          <td colspan="2"><input type="text" placeholder="Nama Serikat" name="status_serikat" value="{{$quotation->status_serikat}}" id="status_serikat" class="form-control w-100 d-none"></td>
                        </tr>
                        <tr>
                          <td>Penempatan/serah terima</td>
                          <td colspan="3">Start serah terima tanggal {{$quotation->tgl_penempatan}}</td>
                        </tr>
                      </tbody>
                  </table>
                  Note : <span class="text-danger fw-b">*</span> Perlu diisi
                </div>
              </div>
              @include('sales.quotation.action')
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <hr class="container-m-nx mb-5" />
</div>

<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $(document).ready(function(){
    $('form').bind("keypress", function(e) {
      if (e.keyCode == 13) {               
        e.preventDefault();
        return false;
      }
    });
    
    $('#btn-submit').on('click',function(e){
      e.preventDefault();
      var form = $(this).parents('form');
      let msg = "";
      let obj = $("form").serializeObject();
      console.log(obj);
      
      if(obj.npwp==null || obj.npwp==""){
        msg += "<b>NPWP</b> belum diisi </br>";
      }
      if(obj.alamat_npwp==null || obj.alamat_npwp==""){
        msg += "<b>Alamat NPWP</b> belum diisi </br>";
      }
      if(obj.pic_invoice==null || obj.pic_invoice==""){
        msg += "<b>PIC Invoice</b> belum diisi </br>";
      }
      if(obj.telp_pic_invoice==null || obj.telp_pic_invoice==""){
        msg += "<b>Telp. PIC Invoice</b> belum diisi </br>";
      }
      if(obj.email_pic_invoice==null || obj.email_pic_invoice==""){
        msg += "<b>Email PIC Invoice</b> belum diisi </br>";
      }
      if(obj.materai==null || obj.materai==""){
        msg += "<b>Materai</b> belum dipilih </br>";
      }
      if(obj.shift_kerja==null || obj.shift_kerja==""){
        msg += "<b>Shift Kerja</b> belum dipilih </br>";
      }
      if(obj.jam_kerja==null || obj.jam_kerja==""){
        msg += "<b>Jam Kerja</b> belum dipilih </br>";
      }
      if(obj.mulai_kerja==null || obj.mulai_kerja==""){
        msg += "<b>Mulai Kerja</b> belum diisi </br>";
      }
      if(obj.selesai_kerja==null || obj.selesai_kerja==""){
        msg += "<b>Selesai Kerja</b> belum diisi </br>";
      }
      if(obj.sistem_kerja==null || obj.sistem_kerja==""){
        msg += "<b>Sistem Kerja</b> belum dipilih </br>";
      }
      if(obj.cuti==null || obj.cuti==""){
        msg += "<b>Cuti</b> belum dipilih </br>";
      }
      if(obj.kunjungan_operasional==null || obj.kunjungan_operasional==""){
        msg += "<b>Kunjungan Operasional</b> belum diisi </br>";
      }
      if(obj.kunjungan_tim_crm==null || obj.kunjungan_tim_crm==""){
        msg += "<b>Kunjungan Tim CRM</b> belum diisi </br>";
      }
      if(obj.ada_training==null || obj.ada_training==""){
        msg += "<b>Training</b> belum dipilih </br>";
      }else{
        if(obj.ada_training=="Ada"){
          if(obj.training==null || obj.training==""){
            msg += "<b>Durasi Training</b> belum dipilih </br>";
          }
        }
      }

      if(obj.kompensasi==null || obj.kompensasi==""){
        msg += "<b>Kompensasi</b> belum dipilih </br>";
      }
      if(obj.joker_reliever==null || obj.joker_reliever==""){
        msg += "<b>Joker / Reliever</b> belum dipilih </br>";
      }
      if(obj.syarat_invoice==null || obj.syarat_invoice==""){
        msg += "<b>Syarat Invoice</b> belum diisi </br>";
      }
      if(obj.lembur==null || obj.lembur==""){
        msg += "<b>Lembur</b> belum dipilih </br>";
      }
      if(obj.alamat_penagihan_invoice==null || obj.alamat_penagihan_invoice==""){
        msg += "<b>Alamat Penagihan Invoice</b> belum diisi </br>";
      }
      if(obj.catatan_site==null || obj.catatan_site==""){
        msg += "<b>Catatan Site</b> belum diisi </br>";
      }

      if(obj.ada_serikat==null || obj.ada_serikat==""){
        msg += "<b>Serikat</b> belum dipilih </br>";
      }else{
        if(obj.ada_serikat=="Ada"){
          if(obj.status_serikat==null || obj.status_serikat==""){
            msg += "<b>Serikat</b> belum diisi </br>";
          }
        }
      }
      
      if(msg == ""){
        form.submit();
      }else{
        Swal.fire({
          title: "Pemberitahuan",
          html: msg,
          icon: "warning"
        });
      }
    });
  
    showTraining(1);
    showSerikat(1);

    function showSerikat(first) {
    let selected = $("#ada_serikat option:selected").val();
      if (selected!="Ada") {
        $('#status_serikat').addClass('d-none');
      }else{
        $('#status_serikat').removeClass('d-none');
        if(first!=1){
          $("#status_serikat").val("").change();
        }
      }
    }
    $('#ada_serikat').on('change', function() {
      showSerikat(2);
    });

    function showTraining(first) {
    let selected = $("#ada_training option:selected").val();
      if (selected!="Ada") {
        $('#training').addClass('d-none');
      }else{
        $('#training').removeClass('d-none');
        if(first!=1){
          $("#training").val("").change();
        }
      }
    }
    $('#ada_training').on('change', function() {
      showTraining(2);
    });
  });
</script>
@endsection