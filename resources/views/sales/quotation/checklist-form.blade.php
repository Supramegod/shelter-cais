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
        @if($quotation->is_aktif==0)
          @include('sales.quotation.step')
        @endif
        <div class="bs-stepper-content">
          <form class="card-body overflow-hidden" action="{{route('quotation.save-edit-12')}}" method="POST" enctype="multipart/form-data">        
            @csrf
            <input type="hidden" name="id" value="{{$quotation->id}}">
            <!-- Account Details -->
            <div id="account-details-1" class="content active">
              <div class="content-header mb-5 text-center">
                <h6 class="mb-3">FORM CHECKLIST NEW SITE & PERJANJIAN KERJA SAMA</h6>
                <h6>Leads/Customer : {{$quotation->nama_perusahaan}}</h6>
                <h6>Site : {{$quotation->nama_site}} - {{$quotation->kebutuhan}}</h6>
              </div>
              <div class="row mt-5">
                <div class="table-responsive overflow-hidden">
                  <table class="table table-hover" style="padding-right:0px !important">
                      <tbody>
                        <tr>
                          <td>No. Quotation</td>
                          <td colspan="4">{{$quotation->nomor}}</td>
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
                          <td colspan="3" class="fw-bold">{{$quotation->company}}</td>
                        </tr>
                        <tr>
                          <td>Hal Kerjasama</td>
                          <td colspan="3">PERJANJIAN KERJASAMA ALIH DAYA JASA {{strtoupper($quotation->kebutuhan)}}</td>
                        </tr>
                        <tr>
                          <td>Jumlah Personel</td>
                          <td colspan="3">{{$quotation->jumlah_personel}}</td>
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
                          <td class="text-center">List PIC<br><br>
                          <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#tambahPic">
                              Tambah
                            </button> </td>
                          <td colspan="3">
                            <div class="table-responsive overflow-hidden table-data">
                              <table id="table-data" class="dt-column-search table w-100" style="text-wrap: nowrap;">
                                <thead>
                                  <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>No. Telp</th>
                                    <th>Email</th>
                                    <th>Kuasa</th>
                                    <th></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {{-- data table ajax --}}
                                </tbody>
                              </table>
                            </div>
                          </td>
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
                          <td>Hari Kerja dan Jam Kerja</td>
                          <td>{{$quotation->shift_kerja}}</td>
                          <td>{{$quotation->jam_kerja}}</td>
                          <td>{{$quotation->mulai_kerja}} s/d {{$quotation->selesai_kerja}}</td>
                        </tr>
                        <tr>
                          <td>System Kerja</td>
                          <td colspan="3">@if($quotation->lembur=="Tidak Ada") No Work No Pay @elseif($quotation->lembur!="") Ada Lembur @endif
                          </td>
                        </tr>
                        <tr>
                          <td>Kebijakan Cuti</td>
                          <td>{{$quotation->cuti}}</td>
                          <td>{{$quotation->gaji_saat_cuti}}</td>
                          <td>{{$quotation->prorate}} @if($quotation->prorate !=null) % @endif</td>
                        </tr>
                        <tr>
                          <td>Kunjungan Operasional <span class="text-danger fw-bold">*</span></td>
                          <td>
                            <div class="input-group"> 
                              <input type="number" placeholder="jumlah kunjungan" name="jumlah_kunjungan_operasional" value="@if($quotation->kunjungan_operasional!=null){{explode(' ',$quotation->kunjungan_operasional)[0]}}@endif" id="jumlah_kunjungan_operasional" class="form-control minimal">
                              <span class="input-group-text" id="basic-addon41">Kali Dalam 1</span>
                            </div>
                          </td>
                          <td>
                            <select id="bulan_tahun_kunjungan_operasional" name="bulan_tahun_kunjungan_operasional" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->kunjungan_operasional=='') selected @endif>- Pilih Data -</option>  
                              <option value="Bulan" @if($quotation->kunjungan_operasional!=null)@if(explode(' ',$quotation->kunjungan_operasional)[1]=='Bulan') selected @endif @endif>Bulan</option>
                              <option value="Tahun" @if($quotation->kunjungan_operasional!=null)@if(explode(' ',$quotation->kunjungan_operasional)[1]=='Tahun') selected @endif @endif>Tahun</option>
                            </select>
                          </td>
                          <td>
                            <input type="text" placeholder="keterangan" name="keterangan_kunjungan_operasional" value="{{$quotation->keterangan_kunjungan_operasional}}" id="keterangan_kunjungan_operasional" class="form-control w-100">
                          </td>
                        </tr>
                        <tr>
                          <td>Kunjungan Tim CRM <span class="text-danger fw-bold">*</span></td>
                          <td>
                            <div class="input-group"> 
                            <input type="number" placeholder="jumlah kunjungan" name="jumlah_kunjungan_tim_crm" value="@if($quotation->kunjungan_tim_crm!=null){{explode(' ',$quotation->kunjungan_tim_crm)[0]}}@endif" id="jumlah_kunjungan_tim_crm" class="form-control minimal">
                              <span class="input-group-text" id="basic-addon41">Kali Dalam 1</span>
                            </div>
                          </td>
                          <td>
                            <select id="bulan_tahun_kunjungan_tim_crm" name="bulan_tahun_kunjungan_tim_crm" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->kunjungan_tim_crm=='') selected @endif>- Pilih Data -</option>  
                              <option value="Bulan" @if($quotation->kunjungan_tim_crm!=null)@if(explode(' ',$quotation->kunjungan_tim_crm)[1]=='Bulan') selected @endif @endif>Bulan</option>
                              <option value="Tahun" @if($quotation->kunjungan_tim_crm!=null)@if(explode(' ',$quotation->kunjungan_tim_crm)[1]=='Tahun') selected @endif @endif>Tahun</option>
                            </select>
                          </td>
                          <td>
                            <input type="text" placeholder="keterangan" name="keterangan_kunjungan_tim_crm" value="{{$quotation->keterangan_kunjungan_tim_crm}}" id="keterangan_kunjungan_tim_crm" class="form-control w-100">
                          </td>
                        </tr>
                        <tr>
                          <td>Training <span class="text-danger fw-bold">*</span></td>
                          <td>
                            <select id="ada_training" name="ada_training" class="form-select w-100" data-allow-clear="true" tabindex="-1">
                              <option value="" @if($quotation->training=='' || $quotation->training==null) selected @endif>- Pilih Data -</option>  
                              <option value="Ada" @if($quotation->training!='' && $quotation->training!=null && $quotation->training!='0') selected @endif>Ada</option>
                              <option value="Tidak Ada" @if($quotation->training=='0') selected @endif>Tidak Ada</option>
                            </select>
                          </td>
                          <td colspan="2">
                            <div class="input-group d-training" id="d-training"> 
                              <input type="number" min="0" max="100" name="training" placeholder="masukkan jumlah dalam 1 tahun" value="{{$quotation->training}}" class="form-control minimal" id="training">
                              <span class="input-group-text" id="basic-addon41">Kali Dalam 1 Tahun</span>
                            </div>
                          </td>
                        </tr>
                        <tr id="list-training">
                          <td>
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#basicModal">
                              List Training
                            </button>
                          </td>
                          <td colspan="3" id="data-list-training">@foreach($listTrainingQ as $training) {{$training->nama}} @if(!$loop->last), @endif @endforeach</td>
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
                        @if($quotation->penjamin=="Takaful")
                        <tr>
                          <td>Penjamin</td>
                          <td colspan="3">{{$quotation->penjamin}}</td>
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
                                  <td class="text-center">@if($quotation->resiko=="Sangat Rendah") 0,24 @elseif($quotation->resiko=="Rendah") 0,54 @elseif($quotation->resiko=="Sedang") 0,89 @elseif($quotation->resiko=="Tinggi") 1,27 @elseif($quotation->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                                  <td class="text-center">&nbsp;</td>
                                </tr>
                                <tr>
                                  <td class="text-center">JKM</td>
                                  <td class="text-center">0,3 %</td>
                                  <td class="text-center">&nbsp;</td>
                                </tr>
                                @if($quotation->program_bpjs=="3 BPJS" || $quotation->program_bpjs=="4 BPJS")
                                <tr>
                                  <td class="text-center">JHT</td>
                                  <td class="text-center">3,7 %</td>
                                  <td class="text-center">2%</td>
                                </tr>
                                @endif
                                @if($quotation->program_bpjs=="4 BPJS")
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
                          <td colspan="3"><b>Talangan @if($quotation->top=="Lebih Dari 7 Hari"){{$quotation->jumlah_hari_invoice}} hari {{$quotation->tipe_hari_invoice}} @else {{$quotation->top}} @endif setelah invoice & lampiran diterima</b></td>
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
                          <td>Lembur</td>
                          <td colspan="3">{{$quotation->lembur}}</td>
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

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">List Training</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="quotation_id" value="{{$quotation->id}}" >
        <table class="table table-stripped table-hover">
          <thead>
            <tr>
              <td>No.</td>
              <td>Jenis</td>
              <td>Nama Training</td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            @foreach($listTraining as $tr)
            <tr>
              <td>{{$tr->id}}</td>
              <td>{{$tr->jenis}}</td>
              <td>{{$tr->nama}}</td>
              <td><input class="form-check-input training-pilihan" type="checkbox" value="{{$tr->id}}" name="trainingList[]" @foreach($listTrainingQ as $trq) @if($trq->training_id==$tr->id) checked @endif @endforeach></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" id="btn-simpan-training" class="btn btn-primary">Simpan Training</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="tambahPic" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel1">Tambah PIC</h4>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <input type="text" id="nama_pic" class="form-control" placeholder="Masukkan Nama" />
              <label for="nama_pic">Nama</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <div class="input-group">
                <select id="jabatan_pic" class="form-select">
                  <option value="">- Pilih Jabatan -</option>
                  @foreach($listJabatanPic as $jabatanPic)
                    <option value="{{$jabatanPic->id}}">{{$jabatanPic->nama}}</option> 
                  @endforeach 
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <input type="number" id="no_telp_pic" class="form-control" placeholder="Masukkan No. Telp" />
              <label for="no_telp_pic">No Telp</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4 mt-2">
            <div class="form-floating form-floating-outline">
              <input type="text" id="email_pic" class="form-control" placeholder="Masukkan Email" />
              <label for="email_pic">Email PIC</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <button type="button" id="btn-tambah-pic" class="btn btn-primary">Tambah PIC</button>
      </div>
    </div>
  </div>
</div>


<!--/ Content -->
@endsection
@section('pageScript')
<script>
  $(document).ready(function(){
    $('#btn-simpan-training').on('click',function(){
      var checkedCount = $('.training-pilihan:checked').length;
      var jumlahTraining = $('#training').val();

      if(jumlahTraining==""){
        Swal.fire({
              title: "Pemberitahuan",
              html: "Belum memasukkan jumlah training per tahun",
              icon: "warning",
            });
            $('#basicModal').modal('toggle');

        return null;
      }

      if (jumlahTraining<checkedCount) {
        Swal.fire({
              title: "Pemberitahuan",
              html: "Training yang dipilih lebih dari jumlah training dalam 1 tahun",
              icon: "warning",
            });
            $('#basicModal').modal('toggle');

        return null;
      }
      
      var checkedValues = [];
      $('.training-pilihan:checked').each(function() {
          checkedValues.push($(this).val());
      });

      if(checkedValues.length==0){
        Swal.fire({
              title: "Pemberitahuan",
              html: "Belum ada training yang dipilih",
              icon: "warning",
            });
        return null;
      };

      let formData = {
        "training_id":checkedValues.join(", "),
        "quotation_id":$('#quotation_id').val(),
        "_token": "{{ csrf_token() }}"
      };

      $.ajax({
        type: "POST",
        url: "{{route('quotation.add-quotation-training')}}",
        data:formData,
        success: function(response){
          $('#data-list-training').text(response);
            $('#basicModal').modal('toggle');
        },
        error:function(error){
          console.log(error);
        }
      });
    });


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
      if(obj.materai==null || obj.materai==""){
        msg += "<b>Materai</b> belum dipilih </br>";
      }
      if(obj.jumlah_kunjungan_operasional==null || obj.jumlah_kunjungan_operasional==""){
        msg += "<b>Jumlah Kunjungan Operasional</b> belum diisi </br>";
      }
      if(obj.bulan_tahun_kunjungan_operasional==null || obj.bulan_tahun_kunjungan_operasional==""){
        msg += "<b>Bulan / Tahun Kunjungan Operasional</b> belum diisi </br>";
      }
      if(obj.jumlah_kunjungan_tim_crm==null || obj.jumlah_kunjungan_tim_crm==""){
        msg += "<b>Jumlah Kunjungan Tim CRM</b> belum diisi </br>";
      }
      if(obj.bulan_tahun_kunjungan_tim_crm==null || obj.bulan_tahun_kunjungan_tim_crm==""){
        msg += "<b>Bulan / Tahun Kunjungan Tim CRM</b> belum diisi </br>";
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
      if(obj.joker_reliever==null || obj.joker_reliever==""){
        msg += "<b>Joker / Reliever</b> belum dipilih </br>";
      }
      if(obj.syarat_invoice==null || obj.syarat_invoice==""){
        msg += "<b>Syarat Invoice</b> belum diisi </br>";
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
        $('#d-training').addClass('d-none');
        $('#list-training').addClass('d-none');
        
      }else{
        $('#d-training').removeClass('d-none');
        $('#list-training').removeClass('d-none');
        if(first!=1){
          $("#d-training").val("");
        }
      }
    }
    $('#ada_training').on('change', function() {
      showTraining(2);
    });
  });

  let table = $('#table-data').DataTable({
    scrollX: true,
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    'processing': true,
    'language': {
        'loadingRecords': '&nbsp;',
        'processing': 'Loading...'
    },
    ajax: {
        url: "{{ route('quotation.list-detail-pic') }}",
        data: function (d) {
            d.quotation_id = {{$quotation->id}};
        },
    },   
    "order":[
        [1,'asc']
    ],
    columns:[{
        data : 'id',
        name : 'id',
        visible: false,
        searchable: false
    },{
        data : 'nama',
        name : 'nama',
    },{
        data : 'jabatan',
        name : 'jabatan',
    },{
        data : 'no_telp',
        name : 'no_telp',
    },{
        data : 'email',
        name : 'email',
    },{
        data : 'kuasa',
        name : 'kuasa',
        width: "10%",
        orderable: false,
        searchable: false,
    },{
        data : 'aksi',
        name : 'aksi',
        width: "10%",
        orderable: false,
        searchable: false,
    }],
    "language": datatableLang,
  });
  
  $('body').on('click', '.btn-delete', function() {
    let formData = {
      "id":$(this).data('id'),
      "_token": "{{ csrf_token() }}"
    };

    let table ='#table-data';
    $.ajax({
      type: "POST",
      url: "{{route('quotation.delete-detail-pic')}}",
      data:formData,
      success: function(response){
        $(table).DataTable().ajax.reload();
      },
      error:function(error){
        console.log(error);
      }
    });
  });

  $('#btn-tambah-pic').on('click',function(){
    let msg="";
    let nama = $('#nama_pic').val();
    let jabatan = $("#jabatan_pic option:selected").val();
    let no_telp = $('#no_telp_pic').val();
    let email = $('#email_pic').val();

    if(nama==null || nama==""){
      msg += "<b>Nama PIC</b> belum diisi </br>";
    };
    if(jabatan==null || jabatan==""){
      msg += "<b>Jabatan PIC</b> belum dipilih </br>";
    };
    if(no_telp==null || no_telp==""){
      msg += "<b>No. Telp.</b> belum diisi </br>";
    };
    if(email==null || email==""){
      msg += "<b>Email</b> belum diisi </br>";
    };


    if(msg!=""){
      Swal.fire({
            title: "Pemberitahuan",
            html: msg,
            icon: "warning",
          });
      $('#nama_pic').val("");
      $("#jabatan_pic").val("").change();
      $('#no_telp_pic').val("");
      $('#email_pic').val("");
      $('#tambahPic').modal('toggle');
      return null;
    };

    let formData = {
      "nama":nama,
      "jabatan":jabatan,
      "no_telp":no_telp,
      "email":email,
      "quotation_id":$('#quotation_id').val(),
      "_token": "{{ csrf_token() }}"
    };

    $.ajax({
      type: "POST",
      url: "{{route('quotation.add-detail-pic')}}",
      data:formData,
      success: function(response){
        if(response=="Data Berhasil Ditambahkan"){
          let table ='#table-data';
          $(table).DataTable().ajax.reload();
          $('#tambahPic').modal('toggle');
        }else{
          Swal.fire({
            title: "Pemberitahuan",
            html: response,
            icon: "warning",
          });
          $('#nama_pic').val("");
          $("#jabatan_pic").val("").change();
          $('#no_telp_pic').val("");
          $('#email_pic').val("");
        }
      },
      error:function(error){
        console.log(error);
        $('#nama_pic').val("");
        $("#jabatan_pic").val("").change();
        $('#no_telp_pic').val("");
        $('#email_pic').val("");
      }
    });
  });

  $('body').on('change', '.set-is-kuasa', function() {
      if ($(this).is(':checked')) {
        let formData = {
          "id":$(this).data('id'),
          "quotation_id":$('#quotation_id').val(),
          "_token": "{{ csrf_token() }}"
        };
        $.ajax({
          type: "POST",
          url: "{{route('quotation.change-kuasa-pic')}}",
          data:formData,
          success: function(response){
            if(response=="Data Berhasil Ditambahkan"){
              let table ='#table-data';
              $(table).DataTable().ajax.reload();
            }else{
              Swal.fire({
                title: "Pemberitahuan",
                html: response,
                icon: "warning",
              });
            }
          },
          error:function(error){
            console.log(error);
          }
        });
      };
  });
    
</script>
@endsection