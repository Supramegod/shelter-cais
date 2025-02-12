@extends('layouts.master')
@section('title','Quotation')
@section('content')
<!--/ Content -->
<style>
  .dataTables_scrollHeadInner {
    width: 100% !important;
  }
</style>
<div class="container-fluid flex-grow-1 container-p-y"> 
  <div class="col-12 col-lg-12">
    <div class="card mb-2">
      <div class="card-header pb-1">
        <h5 class="card-title m-0">Quotation Information</h5>
      </div>
      <div class="card-body pt-1">
        <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
          <div class="d-flex flex-column justify-content-center">
            <h5 class="mb-1 mt-3">
            {{$quotation->nama_perusahaan}} - {{$quotation->nomor}} - Revisi Ke : {{$quotation->revisi}} <span class="badge bg-label-info rounded-pill mt-1">{{$quotation->status}}</span>
               @if($quotation->step != 100)
                <span class="badge bg-label-warning rounded-pill">Data Belum Terisi Lengkap</span>
               @endif
            </h5>
            <p class="mt-0"> Tanggal Quotation : {{$quotation->stgl_quotation}}</p>
            <p class="text-body">{{$quotation->nama_site}}</p>
            <div class="mt-2 mb-3">
              @if($quotation->is_aktif==1)
              <span class="badge bg-label-success rounded-pill mt-1">Quotation Telah Aktif</span>
              <div class="d-flex align-content-center flex-wrap gap-2 mt-3">
                @if($quotation->is_aktif==1)
                  @if($quotation->spk!=null)
                  <a href="{{route('spk.view',$quotation->spk->id)}}" class="btn btn-success"><i class="mdi mdi-arrow-right"></i>&nbsp;  Lihat SPK</a>
                  @endif
                  @if($quotation->pks!=null)
                  <a href="{{route('pks.view',$quotation->pks->id)}}" class="btn btn-success"><i class="mdi mdi-arrow-right"></i>&nbsp;  Lihat PKS</a>
                  @endif
                @endif
              </div>
              @else
                @if($quotation->step == 100 && $quotation->ot1 == null)
                <span class="badge bg-label-warning rounded-pill mt-1">Membutuhkan Approval Direktur Sales</span>
                @endif
                @if($quotation->step == 100 && $quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari")
                <span class="badge bg-label-warning rounded-pill mt-1">Membutuhkan Approval Direktur Keuangan</span>
                @endif
                @if($quotation->step == 100 && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari")
                <span class="badge bg-label-warning rounded-pill mt-1">Membutuhkan Approval Direktur Utama</span>
                @endif
              @endif
            </div>
          </div>
          <div class="d-flex align-content-center flex-wrap gap-2">
            @if($quotation->step != 100)
            <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>$quotation->step])}}" class="btn btn-primary"><i class="mdi mdi-list-box-outline"></i>&nbsp; Lanjutkan Pengisian</a>
            @else
            <!-- <button class="btn btn-warning" @if($quotation->is_aktif==1) disabled @endif><i class="mdi mdi-file-refresh"></i>&nbsp; Ajukan Ulang ( Ubah )</button> -->
              @if(Auth::user()->role_id==96 && $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot1 == null)
                <button class="btn btn-primary" id="approve-quotation" data-id="{{$quotation->id}}" @if($quotation->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
              @elseif(in_array(Auth::user()->role_id,[97,40]) && $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 == null && $quotation->top=="Lebih Dari 7 Hari")
                <button class="btn btn-primary" id="approve-quotation" data-id="{{$quotation->id}}" @if($quotation->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
              @elseif(Auth::user()->role_id==99 && $quotation->step == 100 && $quotation->is_aktif==0 && $quotation->ot2 != null && $quotation->ot1 != null && $quotation->ot3 == null && $quotation->top=="Lebih Dari 7 Hari")
                <!-- <button class="btn btn-primary" id="approve-quotation" data-id="{{$quotation->id}}" @if($quotation->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button> -->
              @endif
              @if($quotation->is_aktif==1)
                <!-- <a href="javascript:void(0)" class="btn btn-secondary" id="btn-copy-quotation"><i class="mdi mdi-content-copy"></i>&nbsp; Copy Ke</a> -->
                @if($quotation->spk==null)
                <a href="javascript:void(0)" class="btn btn-danger" id="btn-ajukan-quotation"><i class="mdi mdi-refresh"></i>&nbsp; Ajukan Ulang</a>
                <button type="button" onclick="window.location.href='{{route('spk.add',['id'=> $quotation->id])}}'" class="btn btn-info" @if($canCreateSpk==0) disabled @endif><i class="mdi mdi-arrow-right"></i>&nbsp;  Create SPK</button>
                @endif
              @endif
              <br>
              <div class="btn-group" role="group">
              <button id="btncetak" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" @if($quotation->is_aktif!=1) disabled @endif>
                Cetak Dokumen
              </button>
              <ul class="dropdown-menu" aria-labelledby="btncetak">
                <li><a class="dropdown-item" onclick="window.open('{{route('quotation.cetak-quotation',$quotation->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)">Cetak Quotation</a></li>
              </ul>
            </div>
            @endif
            <button id="delete-quotation" class="btn btn-danger" data-id="{{$quotation->id}}" @if($quotation->is_aktif==1) disabled @endif><i class="mdi mdi-trash-can"></i>&nbsp;  Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Details Table -->

  <div class="row">
    <div class="col-12 col-lg-7">
      <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title m-0">Quotation Info</h5>
        @if($quotation->step == 100 && $quotation->is_aktif == 0)
        <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'1','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="text-nowrap">
            <table class="table">
              <tr>
                <td>Dibuat Oleh</td>
                <td>: {{$quotation->created_by}}</td>
                <td>Dibuat Tanggal</td>
                <td>: {{$quotation->screated_at}}</td>
              </tr>
              <tr>
                <td>Jumlah Site</td>
                <td>: {{$quotation->jumlah_site}}</td>
                <td>Jenis Kontrak</td>
                <td>: {{$quotation->jenis_kontrak}}</td>
              </tr>
              <tr>
                <td>Mulai Kontrak</td>
                <td>: {{$quotation->smulai_kontrak}}</td>
                <td>Kontrak Selesai</td>
                <td>: {{$quotation->skontrak_selesai}}</td>
              </tr>
              <tr>
                <td>Durasi Kerjasama</td>
                <td>: {{$quotation->durasi_kerjasama}}</td>
                <td>Evaluasi Kontrak</td>
                <td>: {{$quotation->evaluasi_kontrak}}</td>
              </tr>
              <tr>
                <td>Durasi Kontrak Karyawan</td>
                <td>: {{$quotation->durasi_karyawan}}</td>
                <td>Evaluasi Karyawan</td>
                <td>: {{$quotation->evaluasi_karyawan}}</td>
              </tr>
              <tr>
                <td>Penempatan</td>
                <td colspan="3">: {{$quotation->penempatan}}</td>
              </tr>
              <tr>
                <td>Tanggal Penempatan</td>
                <td colspan="3">: {{$quotation->stgl_penempatan}}</td>
              </tr>
              <tr>
                <td>TOP Invoice @if($quotation->top=="Lebih Dari 7 Hari")<span class="badge bg-label-warning rounded-pill">Butuh Approval Direksi</span>@endif</td>
                <td>: {{$quotation->top}}</td>
                @if($quotation->top=="Lebih Dari 7 Hari")
                <td colspan="2">( {{$quotation->jumlah_hari_invoice}} Hari {{$quotation->tipe_hari_invoice}} )</td>
                @else
                <td colspan="2">&nbsp;</td>
                @endif
              </tr>
              <tr>
                <td colspan="4">
                  <div class="responsive-table">
                  <table class="table table-bordered">
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
                      <!-- <tr>
                        <td class="text-center">5</td>
                        <td>Pembayaran <i>Invoice</i></td>
                        <td>{{$salaryRuleQ->pembayaran_invoice}}</td>
                      </tr> -->
                      <tr>
                        <td class="text-center">5</td>
                        <td>Rilis <i>Payroll</i> / Gaji</td>
                        <td>{{$salaryRuleQ->rilis_payroll}}</td>
                      </tr>
                    </tbody>
                  </table>
                  </div>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Service Info</h5>
          @if($quotation->step == 100 && $quotation->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'2','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <tr>
                <td>Kebutuhan</td>
                <td>: {{$quotation->kebutuhan}}</td>
                <td>Entitas</td>
                <td>: {{$quotation->company}}</td>
              </tr>
              <tr>
                <td>Provinsi</td>
                <td>: {{$quotation->provinsi}}</td>
                <td>Kabupaten / Kota</td>
                <td>: {{$quotation->kota}}</td>
              </tr>
              <tr>
                <td>Upah</td>
                <td>: {{$quotation->upah}}</td>
                <td>Nominal Upah @if($quotation->nominal_upah<$quotation->umk)<span class="badge bg-label-warning rounded-pill">Butuh Approval</span>@endif</td>
                <td>: Rp {{number_format($quotation->nominal_upah,2,",",".")}}</td>
              </tr>
              <tr>
                <td>Manajemen Fee</td>
                <td>: {{$quotation->manajemen_fee}}</td>
                <td>Persentase @if($quotation->persentase<7) <span class="badge bg-label-warning rounded-pill">Butuh Approval</span> @endif</td>
                <td>: {{$quotation->persentase}} %</td>
              </tr>
              <tr>
                <td>Jenis Perusahaan</td>
                <td>: {{$quotation->jenis_perusahaan}}</td>
                <td>Resiko</td>
                <td>: {{$quotation->resiko}}</td>
              </tr>
              <tr>
                <td>Penjamin</td>
                <td>: {{$quotation->penjamin}}</td>
                @if($quotation->penjamin =="BPJS")
                <td>Jenis</td>
                <td>: {{$quotation->program_bpjs}}</td>
                @else
                <td>Nominal</td>
                <td colspan="2">: Rp {{number_format($quotation->nominal_takaful,2,",",".")}}</td>                
                @endif
              </tr>
              @if($quotation->penjamin =="BPJS")
              <tr>
                <td>Macam BPJS</td>
                <td colspan="3">: BPJS JKK , BPJS JKM @if($quotation->program_bpjs=="3 BPJS" || $quotation->program_bpjs=="4 BPJS") , BPJS JHT @endif @if($quotation->program_bpjs=="4 BPJS") , BPJS JP @endif</td>
              </tr>
              @endif
              <tr>
                <td>Hari/Jam Kerja</td>
                <td colspan="3">: {{$quotation->shift_kerja}} {{$quotation->jam_kerja}}</td>
              </tr>
              <tr>
                <td>Cuti</td>
                <td colspan="3">: {{$quotation->cuti}}</td>
              </tr>
              <tr>
                <td>THR</td>
                <td>{{$quotation->thr}}</td>
              </tr>
              <tr>
                <td>Kompensasi</td>
                <td>{{$quotation->kompensasi}}</td>
              </tr>
              <tr>
                <td>Tunjangan Hari Libur Nasional</td>
                <td>{{$quotation->tunjangan_holiday}}</td>
                <td colspan="2">@if($quotation->nominal_tunjangan_holiday !=null ) {{"Rp. ".number_format($quotation->nominal_tunjangan_holiday,2,",",".")}} @endif</td>
              </tr>
              <tr>
                <td>Lembur</td>
                <td>{{$quotation->lembur}}</td>
                <td colspan="2">@if($quotation->nominal_lembur !=null ) {{"Rp. ".number_format($quotation->nominal_lembur,2,",",".")}} @endif</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">{{$quotation->kebutuhan}} Details ( Total Headcount : {{$quotation->totalHc}} )</h5>
          @if($quotation->step == 100 && $quotation->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'3','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="card-header p-0">
            <div class="nav-align-top">
              <ul class="nav nav-tabs nav-fill" role="tablist">
                @foreach($quotation->detail as $kkd => $detail)
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link waves-effect @if($loop->first) active @endif" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-{{$detail->id}}" aria-controls="navs-top-{{$detail->id}}" aria-selected="true">
                    {{$detail->jabatan_kebutuhan}} ( {{$detail->jumlah_hc}} )
                  </button>
                </li>
                @endforeach
                <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content p-0">
              @foreach($quotation->detail as $kkd => $detail)
              <div class="tab-pane fade @if($loop->first) active show @endif" id="navs-top-{{$detail->id}}" role="tabpanel">
                <table class="w-100 mb-3">
                  <tr>
                    <td style="display:flex;justify-content:end">
                      <button class="btn btn-primary btn-input-requirement" id="btn-input-requirement-{{$detail->id}}" data-id="{{$detail->id}}"><i class="mdi mdi-pen"></i>&nbsp; Input Requirement</button>
                    </td>
                  </tr>
                </table>
                <div class="table-responsive overflow-hidden table-data-requirement-{{$detail->id}}">
                  <table id="table-data-requirement-{{$detail->id}}" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                      <thead>
                          <tr>
                              <th class="text-center">ID</th>
                              <th class="text-center">No.</th>
                              <th class="text-center">Requirement</th>
                              <th class="text-center">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                          {{-- data table ajax --}}
                      </tbody>
                  </table>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end align-items-center m-3 p-1">
          <div class="order-calculations">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-heading"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-5">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Customer details</h5>
          @if($quotation->step == 100 && $quotation->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('leads.view',$quotation->leads_id)}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-start align-items-center mb-4">
            <div class="d-flex flex-column">
              <a href="javascript:void(0)">
                <h6 class="mb-1">{{$leads->nama_perusahaan}}</h6>
              </a>
              <small>Customer ID: {{$leads->nomor}}</small>
            </div>
          </div>
          <!-- <div class="d-flex justify-content-between">
            <h6 class="mb-2">PIC info</h6>
          </div>
          <p class="mb-0">PIC : {{$leads->pic}}</p>
          <p class="mb-0">Jabatan : {{$leads->jabatan}}</p>
          <p class="mb-0">No. Telp : {{$leads->no_telp}}</p>
          <p class="mb-0">Email : {{$leads->email}}</p>
           -->
          <div class="mt-3 d-flex justify-content-between">
            <h6 class="mb-2">CRM / RO info</h6>
          </div>
          <p class="mb-1">CRM : {{$leads->crm}}</p>
          <p class="mb-1">RO : {{$leads->ro}}</p>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h6 class="card-title m-0">Informasi Approval</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>No.</th>
                  <th>Jabatan</th>
                  <th>Approve ?</th>
                </tr>
              </thead>
              <tbody class="">
                <tr>
                  <td class="text-center">1</td>
                  <td>Direktur Sales</td>
                  <td class="text-center">@if($quotation->ot1 !=null)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
                </tr>
                @if($quotation->top=="Lebih Dari 7 Hari")
                <tr>
                  <td class="text-center">2</td>
                  <td>Direktur Keuangan</td>
                  <td class="text-center">@if($quotation->ot2 !=null)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
                </tr>
                <tr>
                  <td class="text-center">3</td>
                  <td>Direktur Utama</td>
                  <td class="text-center">@if($quotation->ot3 !=null)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h6 class="card-title m-0">Informasi PIC</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Jabatan</th>
                  <th>No. Telp</th>
                  <th>Kuasa</th>
                </tr>
              </thead>
              <tbody class="">
                @foreach($listPic as $key => $pic)
                <tr>
                  <td>{{$pic->nama}}</td>
                  <td>{{$pic->jabatan}}</td>
                  <td>{{$pic->no_telp}}</td>
                  <td>@if($pic->is_kuasa ==1)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
          <h6 class="card-title m-0">Aplikasi Pendukung</h6>
          <h6 class="m-0">
          @if($quotation->step == 100 && $quotation->is_aktif == 0)
          <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'6','edit'=>1])}}">Edit</a>
          @endif
          </h6>
        </div>
        <div class="card-body">
          <div class="table-responsive text-nowrap">
            <table class="table table-hover">
              <tbody class="table-border-bottom-0">
                @foreach($aplikasiPendukung as $value)
                <tr>
                  <td>
                    <img src="{{$value->link_icon}}" alt="{{$value->aplikasi_pendukung}}" style="max-width:60px">
                  </td>
                  <td><span class="fw-medium">{{$value->aplikasi_pendukung}}</span></td>
                </tr>
                @endforeach

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-lg-12">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Harga Jual</h5>
        </div>
        <div class="card-body">
        <div class="card-header p-0">
        <div class="nav-align-top">
            <ul class="nav nav-tabs nav-fill" role="tablist">
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-hpp" aria-controls="navs-top-hpp" aria-selected="false" tabindex="-1">
                  HPP
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-coss" aria-controls="navs-top-coss" aria-selected="false" tabindex="-1">
                  Harga Jual
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-gpm" aria-controls="navs-top-gpm" aria-selected="false" tabindex="-1">
                  Analisa GPM
                </button>
              </li>
            <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span></ul>
          </div>
        </div>
          <div class="card-body">
            <div class="tab-content p-0">
              <div class="tab-pane fade active show" id="navs-top-hpp" role="tabpanel">
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      <thead class="text-center">
                        <tr class="table-success">
                          <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">HARGA POKOK BIAYA</th>
                        </tr>
                        <tr class="table-success">
                          <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}} ( Provisi = {{$quotation->provisi}} )</th>
                        </tr>
                        <tr class="table-success">
                          <th colspan="3">&nbsp;</th>
                          @foreach($quotation->quotation_site as $site)
                          <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;">{{$site->nama_site}}</th>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <th rowspan="2" style="vertical-align: middle;">No.</th>
                          <th>Structure</th>
                          <th rowspan="2" style="vertical-align: middle;">%</th>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <th >{{$detailJabatan->jabatan_kebutuhan}}</th>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <th>Jumlah Head Count ( Personil ) </th>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <th >{{$detailJabatan->jumlah_hc}}</th>
                          @endforeach
                        </tr>
                      </thead>              
                      <tbody>
                        @php $nomorUrut = 1; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Gaji Pokok</td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @foreach($daftarTunjangan as $it => $tunjangan)
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">{{$tunjangan->nama}}</td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}} </td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @endforeach
                        @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Tunjangan Hari Raya <b>( {{$quotation->thr}} )</b></td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">@if($quotation->thr=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}}  @elseif($quotation->thr=="Ditagihkan") Ditagihkan terpisah @endif</td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @endif
                        @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Kompensasi <b>( {{$quotation->kompensasi}} )</b></td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">@if($quotation->kompensasi=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @elseif($quotation->kompensasi=="Ditagihkan") Ditagihkan terpisah @endif</td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @endif
                        @if($quotation->tunjangan_holiday=="Normatif" || $quotation->tunjangan_holiday=="Flat")
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Tunjangan Hari Libur Nasional <b>( {{$quotation->tunjangan_holiday}} @if($quotation->tunjangan_holiday=="Normatif") : {{"Rp. ".number_format($quotation->tunjangan_holiday_display,2,",",".")}} @endif )</b></td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">@if($quotation->tunjangan_holiday=="Normatif") Ditagihkan terpisah @elseif($quotation->tunjangan_holiday=="Flat") {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @endif </td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @endif
                        @if($quotation->lembur=="Normatif" || $quotation->lembur=="Flat")
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Lembur <b>( {{$quotation->lembur}} @if($quotation->lembur=="Normatif") : {{"Rp. ".number_format($quotation->lembur_per_jam,2,",",".")}} Per Jam @endif )</b></td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">@if($quotation->lembur=="Normatif") Ditagihkan terpisah @elseif ($quotation->lembur=="Flat") {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif </td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        @endif
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">BPJS Kesehatan</td>
                          <td style="text-align:center">{{number_format($quotation->persen_bpjs_kesehatan,2,",",".")}}%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_kesehatan,2,",",".")}} </td>
                          @endforeach
                        </tr>
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">BPJS Ketenagakerjaan</td>
                          <td style="text-align:center">{{number_format($quotation->persen_bpjs_ketenagakerjaan,2,",",".")}}%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_ketenagakerjaan,2,",",".")}} <a href="javascript:void(0)"><i class="mdi mdi-magnify text-primary view-bpjs" data-persen-bpjs-jkk="{{$detailJabatan->persen_bpjs_jkk}}" data-persen-bpjs-jkm="{{$detailJabatan->persen_bpjs_jkm}}" data-persen-bpjs-jht="{{$detailJabatan->persen_bpjs_jht}}" data-persen-bpjs-jp="{{$detailJabatan->persen_bpjs_jp}}" data-bpjs-jkk="{{$detailJabatan->bpjs_jkk}}" data-bpjs-jkm="{{$detailJabatan->bpjs_jkm}}" data-bpjs-jht="{{$detailJabatan->bpjs_jht}}" data-bpjs-jp="{{$detailJabatan->bpjs_jp}}"></i></a></td>
                          @endforeach
                        </tr>                      
                        @php $nomorUrut++; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Provisi Seragam </td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,2,",",".")}} </td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Provisi Peralatan </td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_devices,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @if($quotation->kebutuhan_id==3)
                        @php $nomorUrut++; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Chemical </td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_chemical,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        @php $nomorUrut++; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Overhead Cost </td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_ohc,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Bunga Bank ( {{$quotation->top}} ) &nbsp; 
                            
                          </td>
                          <td style="text-align:center">{{$quotation->persen_bunga_bank}} %</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bunga_bank,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @php $nomorUrut++; @endphp
                        <tr class="">
                          <td style="text-align:center">{{$nomorUrut}}</td>
                          <td style="text-align:left" class="">Insentif 
                          </td>
                          <td style="text-align:center">{{$quotation->persen_insentif}} % </td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->insentif,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Total Biaya per Personil</td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                          <td style="text-align:center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Total Sebelum Management Fee</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_sebelum_management_fee,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
                          <td style="text-align:center">{{$quotation->persentase}} %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->nominal_management_fee,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->grand_total_sebelum_pajak,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Dasar Pengenaan Pajak</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->dpp,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="fw-bold">PPN <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">12 %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->ppn,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">-2 %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pph,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_invoice,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pembulatan,2,",",".")}}</td>
                        </tr>             
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-3" style="padding-left:40px">
                    <p><b><i>Note :</i></b>	<br>
  Tunjangan hari raya (gaji pokok dibagi {{$quotation->provisi}}).		<br>
  <i>Cover</i> 
  @if($quotation->program_bpjs=="2 BPJS")
  BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
  @elseif($quotation->program_bpjs=="3 BPJS")
  BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
  @elseif($quotation->program_bpjs=="4 BPJS")
  BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP). 
  @endif
  <span class="text-danger">Pengalian base on upah</span>		<br>
  <i>Cover</i> BPJS Kesehatan. <span class="text-danger">Pengalian base on UMK</span>		<br>
  </p>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="navs-top-coss" role="tabpanel">
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      <thead class="text-center">
                        <tr class="table-success">
                          <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">Harga Jual {{$quotation->kebutuhan}}</th>
                        </tr>
                        <tr class="table-success">
                          <th colspan="{{3+count($quotation->quotation_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}  ( Provisi = {{$quotation->provisi}} )</th>
                        </tr>
                        <tr class="table-success">
                          <th colspan="2">&nbsp;</th>
                          @foreach($quotation->quotation_site as $site)
                          <th colspan="{{$site->jumlah_detail}}" style="vertical-align: middle;">{{$site->nama_site}}</th>
                          @endforeach
                        </tr>+
                      </thead>              
                      <tbody>
                        <tr>
                          <td class="fw-bold">Structure</td>
                          <td class="text-center fw-bold">%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <th class="text-center">{{$detailJabatan->jabatan_kebutuhan}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">Jumlah Personil</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-center">{{$detailJabatan->jumlah_hc}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">1. BASE MANPOWER COST</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-center" class="text-center fw-bold">Unit/Month</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Upah/Gaji</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->nominal_upah,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @foreach($daftarTunjangan as $it => $tunjangan)
                        <tr>
                          <td>{{$tunjangan->nama}}</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endforeach
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-center fw-bold">Unit/Month</td>
                          @endforeach
                        </tr>
                        @if($quotation->thr=="Ditagihkan" || $quotation->thr=="Diprovisikan")
                        <tr>
                          <td>Provisi Tunjangan Hari Raya (THR)</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->thr=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->kompensasi=="Ditagihkan" || $quotation->kompensasi=="Diprovisikan")
                        <tr>
                          <td>Provisi Kompensasi</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->kompensasi=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->kompensasi,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->tunjangan_holiday=="Flat" || $quotation->tunjangan_holiday=="Normatif")
                        <tr>
                          <td>Tunjangan Hari Libur Nasional</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->tunjangan_holiday=="Normatif") <b>Ditagihkan Terpisah</b> @else {{"Rp. ".number_format($detailJabatan->tunjangan_holiday,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        @if($quotation->lembur=="Flat" || $quotation->lembur=="Normatif")
                        <tr>
                          <td>Lembur</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">@if($quotation->lembur=="Normatif") <b>Ditagihkan Terpisah</b> @else {{"Rp. ".number_format($detailJabatan->lembur,2,",",".")}} @endif</td>
                          @endforeach
                        </tr>
                        @endif
                        <tr>
                          <td>BPJS Kesehatan</td>
                          <td class="text-center">{{number_format($quotation->persen_bpjs_kesehatan,2,",",".")}}%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kesehatan,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>BPJS Ketenagakerjaan</td>
                          <td class="text-center">{{number_format($quotation->persen_bpjs_ketenagakerjaan,2,",",".")}}%</td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_ketenagakerjaan,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Provisi Seragam</td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap_coss,2,",",".")}} </td>
                          @endforeach
                        </tr>
                        <tr>
                          <td>Provisi Peralatan </td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @if($quotation->kebutuhan_id==3)
                        <tr>
                          <td>Provisi Chemical </td>
                          <td class="text-center"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        @endif
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Exclude Base Manpower Cost</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="fw-bold">3. BIAYA PENGAWASAN DAN PELAKSANAAN LAPANGAN ( UNIT / MONTH ) </th>
                          <td class="text-center fw-bold"></th>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_ohc_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td class="fw-bold text-center">Total Biaya per Personil (1+2+3)</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_personil_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td class="fw-bold text-center">Harga Per Personel x Jumlah Personel</td>
                          <td class="text-center fw-bold"></td>
                          @foreach($quotation->quotation_detail as $detailJabatan)
                          <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->sub_total_personil_coss,2,",",".")}}</td>
                          @endforeach
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Total Sebelum Management Fee</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_sebelum_management_fee_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari {{$quotation->management_fee}}</span></td>
                          <td style="text-align:center">{{$quotation->persentase}} %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->nominal_management_fee_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->grand_total_sebelum_pajak_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">Dasar Pengenaan Pajak</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->dpp_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="fw-bold">PPN <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">12 %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->ppn_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="">
                          <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>@if($quotation->ppn_pph_dipotong=="Management Fee")*dari management fee @else *dari Total Upah @endif</span></td>
                          <td style="text-align:center">-2 %</td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pph_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->total_invoice_coss,2,",",".")}}</td>
                        </tr>
                        <tr class="table-success">
                          <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                          <td style="text-align:center"></td>
                          <td style="text-align:right" colspan="{{count($quotation->quotation_detail)}}">{{"Rp. ".number_format($quotation->pembulatan_coss,2,",",".")}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-3" style="padding-left:40px">
                    <p><b><i>Note :</i></b>	<br>
                    <b>Upah pokok base on Umk 2024 </b> <br>
  Tunjangan overtime flat total 75 jam. <span class="text-danger">*jika system jam kerja 12 jam </span> <br>
  Tunjangan hari raya ditagihkan provisi setiap bulan. (upah/12) <br>
  @if($quotation->program_bpjs=="2 BPJS")
  BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
  @elseif($quotation->program_bpjs=="3 BPJS")
  BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
  @elseif($quotation->program_bpjs=="4 BPJS")
  BPJS Ketenagakerjaan 4 Program (JKK, JKM, JHT, JP). 
  @endif
  <span class="text-danger">Pengalian base on upah</span>		<br>
  BPJS Kesehatan. <span class="text-danger">*base on Umk 2024</span> <br>
  <br>
  <span class="text-danger">*prosentase Bpjs Tk J. Kecelakaan Kerja disesuaikan dengan tingkat resiko sesuai ketentuan.</span>
  </p>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="navs-top-gpm" role="tabpanel">
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      <thead class="text-center">
                        <tr class="table-success">
                          <th>Keterangan</th>
                          <th>HPP</th>
                          <th>Harga Jual</th>
                        </tr>
                      </thead>
                      @if($quotation->ppn_pph_dipotong=="Management Fee")
                      <tbody>
                        <tr>
                          <td style="text-align:left">Nominal</td>
                          <td style="text-align:right">
                            {{"Rp. ".number_format($quotation->total_invoice,2,",",".")}}
                          </td>
                          <td style="text-align:right">
                            {{"Rp. ".number_format($quotation->total_invoice_coss,2,",",".")}}
                          </td>
                        </tr>
                        <tr>
                          <td style="text-align:left">PPN</td>
                          <td style="text-align:right">
                            @if($quotation->ppn==0)
                            <b>PPN Ditanggung Customer</b>
                            @else
                              {{"Rp. ".number_format($quotation->ppn,2,",",".")}}
                            @endif
                          </td>
                          <td style="text-align:right">
                            @if($quotation->ppn_coss==0)
                            <b>PPN Ditanggung Customer</b>
                            @else
                              {{"Rp. ".number_format($quotation->ppn_coss,2,",",".")}}
                            @endif
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
                            {{"Rp. ".number_format($quotation->total_invoice-$quotation->ppn-$quotation->total_sebelum_management_fee,2,",",".")}}
                          </td>
                          <td style="text-align:right">
                            {{"Rp. ".number_format($quotation->total_invoice_coss-$quotation->ppn_coss-$quotation->total_sebelum_management_fee,2,",",".")}}
                          </td>
                        </tr>
                        <tr>
                          <td class="fw-bold" style="text-align:left">GPM</td>
                          <td class="fw-bold" style="text-align:right">
                            {{number_format((($quotation->total_invoice-$quotation->ppn-$quotation->total_sebelum_management_fee)/$quotation->total_sebelum_management_fee)*100,2,",",".")}} %
                          </td>
                          <td class="fw-bold" style="text-align:right">
                          {{number_format((($quotation->total_invoice_coss-$quotation->ppn_coss-$quotation->total_sebelum_management_fee)/$quotation->total_sebelum_management_fee)*100,2,",",".")}} %
                          </td>
                        </tr>
                      </tbody>
                      @else
                      
                      @endif
                      
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title m-0">Detail Harga Jual</h5>
        </div>
        <div class="card-body">
          <div class="card-header p-0">
            <div class="nav-align-top">
              <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link waves-effect active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-kaporlap" aria-controls="navs-top-kaporlap" aria-selected="true">
                    Kaporlap
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-ohc" aria-controls="navs-top-ohc" aria-selected="false" tabindex="-1">
                    OHC
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-devices" aria-controls="navs-top-devices" aria-selected="false" tabindex="-1">
                    Devices
                  </button>
                </li>
                @if($quotation->kebutuhan_id == 3)
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link waves-effect" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-chemical" aria-controls="navs-top-chemical" aria-selected="false" tabindex="-1">
                    Chemical
                  </button>
                </li>
                @endif
                <span class="tab-slider" style="left: 0px; width: 91.4062px; bottom: 0px;"></span>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="tab-content p-0">
              <div class="tab-pane fade active show" id="navs-top-kaporlap" role="tabpanel">
                <div class="row mb-5">
                @if($quotation->step == 100 && $quotation->is_aktif == 0)
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'7','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                      <span class="align-middle me-sm-1">Edit</span>
                  </a>
                </div>
                @endif
                </div>
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      @php
                      $totalKaporlap = 0;
                      @endphp
                      @foreach($listJenisKaporlap as $jenisKaporlap)
                      <thead class="text-center">
                        <tr class="table-primary">
                          <th>{{$jenisKaporlap->jenis_barang}}</th>
                          <th>Harga / Unit</th>
                          <th>Jumlah</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($listKaporlap as $kaporlap)
                        @if($kaporlap->jenis_barang == $jenisKaporlap->jenis_barang)
                          <tr>
                            <td>{{$kaporlap->nama}}</td>
                            <td style="text-align:right">Rp {{number_format($kaporlap->harga,0,",",".")}}</td>
                            <td style="text-align:center">{{$kaporlap->jumlah}}</td>
                          </tr>

                          @php
                            $totalKaporlap += ($kaporlap->jumlah*$kaporlap->harga);
                          @endphp
                        @endif
                        @endforeach
                      </tbody>
                      @endforeach
                      <tbody>
                      <tr class="table-success">
                        <td><b>TOTAL</b> </td>
                        <td style="text-align:right">Rp {{number_format($totalKaporlap,0,",",".")}}</td>
                        <td class="total-semua" style="text-align:right"></td>
                      </tr>
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="navs-top-ohc" role="tabpanel">
                <div class="row mb-5">
                @if($quotation->step == 100 && $quotation->is_aktif == 0)
                  <div class="col-12 d-flex justify-content-between">
                    <div></div>
                    <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'8','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                        <span class="align-middle me-sm-1">Edit</span>
                    </a>
                  </div>
                  @endif
                </div>
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      @php
                        $totalOhc = 0;
                      @endphp
                      @foreach($listJenisOhc as $jenisOhc)
                      <thead class="text-center">
                        <tr class="table-primary">
                          <th style="vertical-align: middle;">{{$jenisOhc->jenis_barang}}</th>
                          <th style="vertical-align: middle;">Harga / Unit</th>
                          <th>Jumlah</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($listOhc as $ohc)
                        @if($ohc->jenis_barang == $jenisOhc->jenis_barang)
                          <tr>
                            <td>{{$ohc->nama}}</td>
                            <td style="text-align:right">Rp {{number_format($ohc->harga,0,",",".")}}</td>
                            <td style="text-align:center">{{$ohc->jumlah}}</td>
                          </tr>
                          @php
                            $totalOhc += ($ohc->jumlah*$ohc->harga);
                          @endphp
                        @endif
                        @endforeach
                      </tbody>
                      @endforeach
                      <tbody>
                      <tr class="table-success">
                        <td><b>TOTAL</b> </td>
                        <td style="text-align:right">Rp {{number_format($totalOhc,0,",",".")}}</td>
                        <td class="total-semua" style="text-align:right"></td>
                      </tr>
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="navs-top-devices" role="tabpanel">
                <div class="row mb-5">
                @if($quotation->step == 100 && $quotation->is_aktif == 0)
                  <div class="col-12 d-flex justify-content-between">
                    <div></div>
                    <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'9','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                        <span class="align-middle me-sm-1">Edit Devices</span>
                    </a>
                  </div>
                @endif
                </div>
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      @php
                        $totalDevices = 0;
                      @endphp
                      @foreach($listJenisDevices as $jenisDevices)
                      <thead class="text-center">
                        <tr class="table-primary">
                          <th style="vertical-align: middle;">{{$jenisDevices->jenis_barang}}</th>
                          <th style="vertical-align: middle;">Harga / Unit</th>
                          <th>Jumlah</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($listDevices as $devices)
                        @if($devices->jenis_barang == $jenisDevices->jenis_barang)
                          <tr>
                            <td>{{$devices->nama}}</td>
                            <td style="text-align:right">Rp {{number_format($devices->harga,0,",",".")}}</td>
                            <td style="text-align:center">{{$devices->jumlah}}</td>
                          </tr>
                          @php
                            $totalDevices += ($devices->jumlah*$devices->harga);
                          @endphp
                        @endif
                        @endforeach
                      </tbody>
                      @endforeach
                      <tbody>
                      <tr class="table-success">
                        <td><b>TOTAL</b> </td>
                        <td style="text-align:right">Rp {{number_format($totalDevices,0,",",".")}}</td>
                        <td class="total-semua" style="text-align:right"></td>
                      </tr>
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>
              @if($quotation->kebutuhan_id == 3)
              <div class="tab-pane fade" id="navs-top-chemical" role="tabpanel">
                <div class="row mb-5">
                @if($quotation->step == 100 && $quotation->is_aktif == 0)
                  <div class="col-12 d-flex justify-content-between">
                    <div></div>
                    <a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'10','edit'=>1])}}" class="btn btn-primary btn-next w-20">
                        <span class="align-middle me-sm-1">Edit</span>
                    </a>
                  </div>
                @endif
                </div>
                <div class="row">
                  <div class="table-responsive text-nowrap">
                    <table class="table" >
                      @php
                        $totalChemical = 0;
                      @endphp
                      @foreach($listJenisChemical as $jenisChemical)
                      <thead class="text-center">
                        <tr class="table-primary">
                          <th style="vertical-align: middle;">{{$jenisChemical->jenis_barang}}</th>
                          <th style="vertical-align: middle;">Harga / Unit</th>
                          <th>Jumlah</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($listChemical as $chemical)
                        @if($chemical->jenis_barang == $jenisChemical->jenis_barang)
                          <tr>
                            <td>{{$chemical->nama}}</td>
                            <td style="text-align:right">Rp {{number_format($chemical->harga,0,",",".")}}</td>
                            <td style="text-align:center">{{$chemical->jumlah}}</td>
                          </tr>
                          @php
                            $totalChemical += ($chemical->jumlah*$chemical->harga);
                          @endphp
                        @endif
                        @endforeach
                      </tbody>
                      @endforeach
                      <tbody>
                      <tr class="table-success">
                        <td><b>TOTAL</b> </td>
                        <td style="text-align:right">Rp {{number_format($totalChemical,0,",",".")}}</td>
                        <td></td>
                      </tr>
                    </tbody>
                    </table>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center pb-0">
          <h5 class="card-title m-0">Syarat Dan Ketentuan Kerjasama</h5>
          @if($quotation->step == 100 && $quotation->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$quotation->id,'step'=>'12','edit'=>1])}}">Edit</a></h6>
            @endif
          </div>
          <div class="card-body pt-0">
            <div class="row mt-5">
              <div class="table-responsive overflow-hidden table-data">
                <table id="table-data-perjanjian" class="dt-column-search table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Nomor</th>
                            <th class="text-center">Perjanjian</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- data table ajax --}}
                    </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quotationModalLabel">Pilih Quotation Asal dan Tujuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <input type="hidden" id="quotationAsal" value="{{$quotation->id}}">
          <div class="mb-3">
            <label for="quotationTujuan" class="form-label">Quotation Tujuan</label>
            <select id="quotationTujuan" class="form-select">
              <option value="" selected>Pilih Quotation Tujuan</option>
              @foreach($quotationTujuan as $qtujuan)
              <option value="{{$qtujuan->id}}">{{$qtujuan->nomor}}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" id="simpan-copy-quotation" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="bpjsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Detail BPJS Ketenagakerjaan</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>No.</th>
                <th>Jenis BPJS TK</th>
                <th>Persentase</th>
                <th>Nominal</th>
              </tr>
            </thead>
            <tbody id="bpjs-details">
              <!-- BPJS details will be populated here -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  $('body').on('click', '.view-bpjs', function() {
    const bpjsDetails = [
      { jenis: 'JKK', persen: $(this).data('persen-bpjs-jkk'), nominal: $(this).data('bpjs-jkk') },
      { jenis: 'JKM', persen: $(this).data('persen-bpjs-jkm'), nominal: $(this).data('bpjs-jkm') },
      { jenis: 'JHT', persen: $(this).data('persen-bpjs-jht'), nominal: $(this).data('bpjs-jht') },
      { jenis: 'JP', persen: $(this).data('persen-bpjs-jp'), nominal: $(this).data('bpjs-jp') }
    ];

    let bpjsTableContent = '';
    let total = 0;
    let totalPersen = 0;
    bpjsDetails.forEach((detail, index) => {
      bpjsTableContent += `
        <tr>
          <td>${index + 1}</td>
          <td>${detail.jenis}</td>
          <td>${detail.persen}%</td>
          <td>Rp. ${parseFloat(detail.nominal).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        </tr>
      `;
      totalPersen += detail.persen;
      total += detail.nominal;
    });

    bpjsTableContent += `
        <tr>
          <td colspan="2" class="text-end">TOTAL</td>
          <td>${totalPersen}%</td>
          <td>Rp. ${parseFloat(total).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
        </tr>
      `;
    $('#bpjs-details').html(bpjsTableContent);
    $('#bpjsModal').modal('show');
  });
</script>

<!--/ Content -->
@endsection

@section('pageScript')
<script>
  $('#table-data').DataTable({
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
          url: "{{ route('quotation.list-detail-hc') }}",
          data: function (d) {
              d.quotation_id = {{$quotation->id}};
          },
      },   
      "order":[
          [0,'asc']
      ],
      columns:[{
          data : 'id',
          name : 'id',
          visible: false,
          searchable: false
      },{
          data : 'kebutuhan',
          name : 'kebutuhan',
          className:'text-center'
      },{
          data : 'jabatan_kebutuhan',
          name : 'jabatan_kebutuhan',
          className:'text-center'
      },{
          data : 'jumlah_hc',
          name : 'jumlah_hc',
          className:'text-center'
      }],
      "language": datatableLang,
    });

  $('body').on('click', '#delete-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menghapus data ini ?",
      showCancelButton: true,
      confirmButtonText: "Hapus",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.delete-quotation')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil dihapus.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                window.location.href="{{route('quotation')}}";
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });
  $('body').on('click', '#approve-quotation', function() {
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin menyetujui data ini ?",
      showCancelButton: true,
      confirmButtonText: "Approve",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('quotation.approve-quotation')}}",
          data:formData,
          success: function(response){
            let timerInterval;
            Swal.fire({
              title: "Pemberitahuan",
              html: "Data berhasil disetujui.",
              icon: "success",
              timer: 2000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft()}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                location.reload();
              }
            });
          },
          error:function(error){
            console.log(error);
          }
        });
      }
    });
  });

  $('#table-data-perjanjian').DataTable({
    scrollX: true,
    "bPaginate": false,
    "bFilter": false,
    "bInfo": false,
      'processing': true,
      'language': {
          'loadingRecords': '&nbsp;',
          'processing': 'Loading...'
      },
      ajax: {
          url: "{{ route('quotation.list-quotation-kerjasama') }}",
          data: function (d) {
              d.quotation_id = {{$quotation->id}};
          },
      },   
      "order":[
          [0,'asc']
      ],
      columns:[{
          data : 'id',
          name : 'id',
          visible: false,
          searchable: false
      },{
          data : 'nomor',
          name : 'nomor',
          className:'text-center',
          width: "10%",
      },{
          data : 'perjanjian',
          name : 'perjanjian',
          width: "70%",
      }],
      "language": datatableLang,
    });
  
    @foreach($quotation->detail as $kkd => $detail)
      let tableRequirement{{$detail->id}} = $('#table-data-requirement-{{$detail->id}}').DataTable({
        scrollX: true,
        "bPaginate": false,
      "bLengthChange": false,
      "sScrollXInner": "100%",
      "bFilter": false,
      "bInfo": false,
        'processing': true,
        'language': {
            'loadingRecords': '&nbsp;',
            'processing': 'Loading...'
        },
        ajax: {
            url: "{{ route('quotation.list-detail-requirement') }}",
            data: function (d) {
                d.quotation_detail_id = {{$detail->id}};
            },
        },   
        "order":[
            [0,'asc']
        ],
        columns:[{
            data : 'id',
            name : 'id',
            visible: false,
            searchable: false
        },{
            data : 'nomor',
            name : 'nomor',
            className:'text-center',
            width: "10%",
        },{
            data : 'requirement',
            name : 'requirement',
            className:'text-center'
        },{
            data : 'aksi',
            name : 'aksi',
            width: "10%",
            orderable: false,
            searchable: false,
        }],
        "language": datatableLang,
      });

      $('#btn-input-requirement-{{$detail->id}}').on('click', function() {
        Swal.fire({
            title: 'requirement',
            html: '<textarea id="textareaInput" class="swal2-textarea" placeholder="Masukkan requirement" style="height: 100px;"></textarea>',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: () => {
                const text = $('#textareaInput').val();
                if (!text) {
                    Swal.showValidationMessage('Requirement Harus Diisi !');
                }
                return text;
            }
        }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                type: "POST",
                url: "{{route('quotation.add-detail-requirement')}}",
                data: { 
                  "_token": "{{ csrf_token() }}",
                  requirement: result.value,
                  quotation_detail_id:{{$detail->id}}
                },
                success: function(response){
                  if(response=="Data Berhasil Ditambahkan"){
                    $('#table-data-requirement-{{$detail->id}}').DataTable().ajax.reload();
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
            }
          });
      });
    @endforeach

    $('body').on('click', '.btn-delete', function() {
      let formData = {
        "id":$(this).data('id'),
        "_token": "{{ csrf_token() }}"
      };

      let table ='#table-data-requirement-'+$(this).data('detail');
      $.ajax({
        type: "POST",
        url: "{{route('quotation.delete-detail-requirement')}}",
        data:formData,
        success: function(response){
          $(table).DataTable().ajax.reload();
        },
        error:function(error){
          console.log(error);
        }
      });
    });
    
    let baseUrl = "{{ route('quotation.copy-quotation', ['qasal' => ':qasal', 'qtujuan' => ':qtujuan']) }}";

    function redirectToQuotationCopy(qasal, qtujuan) {
        // Ganti placeholder `:qasal` dan `:qtujuan` dengan nilai aktual
        let url = baseUrl.replace(':qasal', qasal).replace(':qtujuan', qtujuan);
        location.href = url;
    }
    
    $("#simpan-copy-quotation").on('click',function() {
        let msg = "";
        let qasal = $("#quotationAsal").val();
        let qtujuan = $("#quotationTujuan").val();

        if(qasal==null || qasal==""){
            msg += "<b>Quotation Asal</b> belum dipilih </br>";
        }

        if(qtujuan==null || qtujuan==""){
            msg += "<b>Quotation Tujuan</b> belum dipilih </br>";
        }

        if(msg == ""){
            $("#quotationModal").modal("hide");                           

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Apakah Anda yakin ingin mengcopy Quotation '+$('#quotationAsal option:selected').text()+' ke Quotation '+$('#quotationTujuan option:selected').text()+'?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Copy',
                cancelButtonText: 'Batal'
                }).then((result) => {
                // Jika user mengklik "Ya"
                if (result.isConfirmed) {
                    redirectToQuotationCopy(qasal,qtujuan);
                }
            });
        }else{
            $("#quotationModal").modal("hide");                           
            Swal.fire({
            title: "Pemberitahuan",
            html: msg,
            icon: "warning"
            });
        }
    });
    
    $("#btn-copy-quotation").on("click",function(){
      $("#quotationModal").modal("show");
    })
    
    $("#btn-ajukan-quotation").on("click",function(){
      Swal.fire({
        title: 'Konfirmasi',
        text: `Apakah Anda ingin mengajukan quotation ulang untuk quotation nomor {{$quotation->nomor}}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ajukan Ulang',
        cancelButtonText: 'Batal',
        reverseButtons: true,
      }).then((result) => {
        if (result.isConfirmed) {
          // Memunculkan prompt untuk mengisi alasan
          Swal.fire({
            title: 'Masukkan Alasan',
            input: 'textarea',
            inputPlaceholder: 'Tuliskan alasan pengajuan ulang...',
            inputAttributes: {
              'aria-label': 'Tuliskan alasan pengajuan ulang'
            },
            showCancelButton: true,
            confirmButtonText: 'Ajukan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            preConfirm: (alasan) => {
              if (!alasan) {
                Swal.showValidationMessage('Alasan tidak boleh kosong');
                return false;
              }
              return alasan;
            }
          }).then((result) => {
            if (result.isConfirmed) {
              // Logika untuk memproses pengajuan ulang
              let alasan = result.value;
              Swal.fire({
                title: 'Berhasil!',
                text: 'Quotation diajukan ulang.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
              });

              // Bangun URL dengan alasan
              let baseUrl = "{{ route('quotation.ajukan-ulang-quotation', ['quotation' => ':quotation']) }}";
              let url = baseUrl.replace(':quotation', {{$quotation->id}});
              // Tambahkan alasan sebagai parameter URL
              url += `?alasan=${encodeURIComponent(alasan)}`;
                
              location.href = url;
              console.log("gogo");
              
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              Swal.fire({
                title: 'Dibatalkan',
                text: 'Pengajuan ulang dibatalkan.',
                icon: 'info',
                timer: 2000,
                showConfirmButton: false
              });
            }
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            title: 'Dibatalkan',
            text: 'Pengajuan ulang dibatalkan.',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
          });
        }
      });
    })
</script>
@endsection