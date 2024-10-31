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
              {{$data->nomor}}
               @if($master->step != 100)
                <span class="badge bg-label-warning rounded-pill">Data Belum Terisi Lengkap</span>
               @endif
            </h5>
            <p class="mt-0"> Tanggal Quotation : {{$master->stgl_quotation}}</p>
            <p class="text-body">{{$master->nama_perusahaan}} - {{$master->nama_site}}</p>
            <p class="text-body">Revisi Ke : {{$master->revisi}}</p>
            <div class="mt-2 mb-3">
              @if($data->is_aktif==1)
              <span class="badge bg-label-success rounded-pill mt-1">Quotation Telah Aktif</span>
              @else
                @if($master->step == 100 && $data->ot1 == null)
                <span class="badge bg-label-warning rounded-pill mt-1">Membutuhkan Approval Direktur Sales</span>
                @endif
                @if($master->step == 100 && $data->ot2 == null && $master->top=="Lebih Dari 7 Hari")
                <span class="badge bg-label-warning rounded-pill mt-1">Membutuhkan Approval Direktur Keuangan</span>
                @endif
                @if($master->step == 100 && $data->ot3 == null && $master->top=="Lebih Dari 7 Hari")
                <span class="badge bg-label-warning rounded-pill mt-1">Membutuhkan Approval Direktur Utama</span>
                @endif
              @endif
            </div>
          </div>
          <div class="d-flex align-content-center flex-wrap gap-2">
            @if($master->step != 100)
            <a href="{{route('quotation.step',['id'=>$master->id,'step'=>$master->step])}}" class="btn btn-primary"><i class="mdi mdi-list-box-outline"></i>&nbsp; Lanjutkan Pengisian</a>
            @else
              <a href="{{route('quotation.step',['id'=>$master->id,'step'=>12])}}" class="btn btn-warning"><i class="mdi mdi-pencil"></i>&nbsp;  Ubah Checklist</a>

            <!-- <button class="btn btn-warning" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-file-refresh"></i>&nbsp; Ajukan Ulang ( Ubah )</button> -->
              @if(Auth::user()->role_id==96 && $master->step == 100 && $data->is_aktif==0 && $data->ot1 == null)
                <button class="btn btn-primary" id="approve-quotation" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
              @elseif(Auth::user()->role_id==97 && $master->step == 100 && $data->is_aktif==0 && $data->ot2 == null && $master->top=="Lebih Dari 7 Hari")
                <button class="btn btn-primary" id="approve-quotation" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
              @elseif(Auth::user()->role_id==99 && $master->step == 100 && $data->is_aktif==0 && $data->ot2 != null && $data->ot1 != null && $data->ot3 == null && $master->top=="Lebih Dari 7 Hari")
                <button class="btn btn-primary" id="approve-quotation" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif ><i class="mdi mdi-draw-pen"></i>&nbsp; Approval</button>
              @endif
              @if($data->is_aktif==1)
                @if($data->spk==null)
                <a href="{{route('spk.add',['id'=> $data->id])}}" class="btn btn-info"><i class="mdi mdi-arrow-right"></i>&nbsp;  Create SPK</a>
                @else
                <a href="{{route('spk.view',$data->spk->id)}}" class="btn btn-success"><i class="mdi mdi-arrow-right"></i>&nbsp;  Lihat SPK</a>
                @endif
              @endif
              <div class="btn-group" role="group">
              <button id="btncetak" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" @if($data->is_aktif!=1) disabled @endif>
                Cetak Dokumen
              </button>
              <ul class="dropdown-menu" aria-labelledby="btncetak">
                <li><a class="dropdown-item" onclick="window.open('{{route('quotation.cetak-checklist',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)">Cetak Checklist</a></li>
                <li><a class="dropdown-item" onclick="window.open('{{route('quotation.cetak-coss',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)">Cetak Cost Structure</a></li>
                <li><a class="dropdown-item" onclick="window.open('{{route('quotation.cetak-quotation',$data->id)}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)">Cetak Quotation</a></li>
              </ul>
            </div>
            @endif
            <button id="delete-quotation" class="btn btn-danger" data-id="{{$data->id}}" @if($data->is_aktif==1) disabled @endif><i class="mdi mdi-trash-can"></i>&nbsp;  Delete</button>
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
        @if($master->step == 100 && $data->is_aktif == 0)
        <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'1','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="text-nowrap">
            <table class="table">
              <tr>
                <td>Dibuat Oleh</td>
                <td>: {{$master->created_by}}</td>
                <td>Dibuat Tanggal</td>
                <td>: {{$master->screated_at}}</td>
              </tr>
              <tr>
                <td>Jumlah Site</td>
                <td>: {{$master->jumlah_site}}</td>
                <td>Jenis Kontrak</td>
                <td>: {{$master->jenis_kontrak}}</td>
              </tr>
              <tr>
                <td>Mulai Kontrak</td>
                <td>: {{$master->smulai_kontrak}}</td>
                <td>Kontrak Selesai</td>
                <td>: {{$master->skontrak_selesai}}</td>
              </tr>
              <tr>
                <td>Durasi Kerjasama</td>
                <td>: {{$master->durasi_kerjasama}}</td>
                <td>Evaluasi Kontrak</td>
                <td>: {{$master->evaluasi_kontrak}}</td>
              </tr>
              <tr>
                <td>Durasi Karyawan</td>
                <td>: {{$master->durasi_karyawan}}</td>
                <td>Evaluasi Karyawan</td>
                <td>: {{$master->evaluasi_karyawan}}</td>
              </tr>
              <tr>
                <td>Penempatan</td>
                <td colspan="3">: {{$master->penempatan}}</td>
              </tr>
              <tr>
                <td>Tanggal Penempatan</td>
                <td>: {{$master->stgl_penempatan}}</td>
                <td>Salary Rule</td>
                <td>: {{$master->salary_rule}}</td>
              </tr>
              <tr>
                <td>TOP Invoice @if($master->top=="Lebih Dari 7 Hari")<span class="badge bg-label-warning rounded-pill">Butuh Approval Direksi</span>@endif</td>
                <td>: {{$master->top}}</td>
                @if($master->top=="Lebih Dari 7 Hari")
                <td colspan="2">( {{$master->jumlah_hari_invoice}} Hari {{$master->tipe_hari_invoice}} )</td>
                @else
                <td colspan="2">&nbsp;</td>
                @endif
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Service Info</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'2','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <tr>
                <td>Kebutuhan</td>
                <td>: {{$data->kebutuhan}}</td>
                <td>Entitas</td>
                <td>: {{$data->company}}</td>
              </tr>
              <tr>
                <td>Provinsi</td>
                <td>: {{$data->provinsi}}</td>
                <td>Kabupaten / Kota</td>
                <td>: {{$data->kota}}</td>
              </tr>
              <tr>
                <td>Upah</td>
                <td>: {{$data->upah}}</td>
                <td>Nominal Upah @if($data->nominal_upah<$data->umk)<span class="badge bg-label-warning rounded-pill">Butuh Approval</span>@endif</td>
                <td>: Rp {{number_format($data->nominal_upah,0,",",".")}}</td>
              </tr>
              <tr>
                <td>Manajemen Fee</td>
                <td>: {{$data->manajemen_fee}}</td>
                <td>Persentase @if($data->persentase<7) <span class="badge bg-label-warning rounded-pill">Butuh Approval</span> @endif</td>
                <td>: {{$data->persentase}} %</td>
              </tr>
              <tr>
                <td>Jenis Perusahaan</td>
                <td>: {{$data->jenis_perusahaan}}</td>
                <td>Resiko</td>
                <td>: {{$data->resiko}}</td>
              </tr>
              <tr>
                <td>Penjamin</td>
                <td>: {{$data->penjamin}}</td>
                @if($data->penjamin =="BPJS")
                <td>Jenis</td>
                <td>: {{$data->program_bpjs}}</td>
                @else
                <td>Nominal</td>
                <td colspan="2">: Rp {{number_format($data->nominal_takaful,0,",",".")}}</td>                
                @endif
              </tr>
              @if($data->penjamin =="BPJS")
              <tr>
                <td>Macam BPJS</td>
                <td colspan="3">: BPJS JKK , BPJS JKM @if($data->program_bpjs=="3 BPJS" || $data->program_bpjs=="4 BPJS") , BPJS JHT @endif @if($data->program_bpjs=="4 BPJS") , BPJS JP @endif</td>
              </tr>
              @endif
              <tr>
                <td>Hari/Jam Kerja</td>
                <td colspan="3">: {{$master->shift_kerja}} {{$master->jam_kerja}} {{$master->mulai_kerja}} s/d {{$master->selesai_kerja}}</td>
              </tr>
              <tr>
                <td>Cuti</td>
                <td colspan="3">: {{$master->cuti}}</td>
              </tr>
              <tr>
                <td>THR</td>
                <td>{{$master->thr}}</td>
                <td>Kompensasi</td>
                <td>{{$master->kompensasi}}</td>
              </tr>
              <tr>
                <td>Tunjangan Holiday</td>
                <td>{{$master->tunjangan_holiday}}</td>
                <td>Lembur</td>
                <td>{{$master->lembur}}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">{{$data->kebutuhan}} Details ( Total Headcount : {{$data->totalHc}} )</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'3','edit'=>1])}}">Edit</a></h6>
          @endif
        </div>
        <div class="card-body">
          <div class="card-header p-0">
            <div class="nav-align-top">
              <ul class="nav nav-tabs nav-fill" role="tablist">
                @foreach($kebutuhanDetail as $kkd => $detail)
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
              @foreach($kebutuhanDetail as $kkd => $detail)
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
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('leads.view',$master->leads_id)}}">Edit</a></h6>
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
                  <td class="text-center">@if($data->ot1 !=null)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
                </tr>
                @if($master->top=="Lebih Dari 7 Hari")
                <tr>
                  <td class="text-center">2</td>
                  <td>Direktur Keuangan</td>
                  <td class="text-center">@if($data->ot2 !=null)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
                </tr>
                <tr>
                  <td class="text-center">3</td>
                  <td>Direktur Utama</td>
                  <td class="text-center">@if($data->ot3 !=null)<i class="mdi mdi-check-circle text-success"></i>@else &nbsp; @endif</td>
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
          @if($master->step == 100 && $data->is_aktif == 0)
          <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'6','edit'=>1])}}">Edit</a>
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
          <h5 class="card-title m-0">Cost Structure</h5>
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
                  Cost Structure
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
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">HARGA POKOK BIAYA</th>
                      </tr>
                      <tr class="table-success">
                        <th colspan="{{3+count($quotationKebutuhan[0]->kebutuhan_detail)}}" style="vertical-align: middle;">{{$leads->nama_perusahaan}}</th>
                      </tr>
                      <tr class="table-success">
                        <th rowspan="2" style="vertical-align: middle;">No.</th>
                        <th>Structure</th>
                        <th rowspan="2" style="vertical-align: middle;">%</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th >{{$detailJabatan->jabatan_kebutuhan}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <th>Jumlah Head Count ( Personil ) </th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <th >{{$detailJabatan->jumlah_hc}}</th>
                        @endforeach
                      </tr>
                    </thead>              
                    <tbody>
                      <tr class="">
                        <td style="text-align:center">1</td>
                        <td style="text-align:left" class="">Gaji Pokok</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($quotationKebutuhan[0]->nominal_upah,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr class="">
                        <td style="text-align:center">{{2+$it}}</td>
                        <td style="text-align:left" class="">{{$tunjangan->nama}}</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},0,",",".")}}</td>
                        @endforeach
                      </tr>
                      @endforeach
                      @if($master->thr=="Ditagihkan" || $master->thr=="Diprovisikan")
                      <tr class="">
                        <td style="text-align:center">{{2+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Tunjangan Hari Raya <b>( {{$master->thr}} )</b></td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">@if($master->thr=="Diprovisikan"){{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,0,",",".")}}@elseif($master->thr=="Ditagihkan") Ditagihkan terpisah @endif</td>
                        @endforeach
                      </tr>
                      @endif
                      <tr class="">
                        <td style="text-align:center">{{3+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Kecelakaan Kerja</td>
                        <td style="text-align:center">@if($quotationKebutuhan[0]->resiko=="Sangat Rendah") 0,24 @elseif($quotationKebutuhan[0]->resiko=="Rendah") 0,54 @elseif($quotationKebutuhan[0]->resiko=="Sedang") 0,89 @elseif($quotationKebutuhan[0]->resiko=="Tinggi") 1,27 @elseif($quotationKebutuhan[0]->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center"></td>
                        <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Kematian</td>
                        <td style="text-align:center">0,3 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      @if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                      <tr class="">
                        <td style="text-align:center"></td>
                        <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Hari Tua</td>
                        <td style="text-align:center">3,7 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jht,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                      <tr class="">
                        <td style="text-align:center"></td>
                        <td style="text-align:left" class="">BPJS Ketenagakerjaan J. Pensiun</td>
                        <td style="text-align:center">2 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_jp,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      @endif
                      <tr class="">
                        <td style="text-align:center">{{4+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">BPJS Kesehatan </td>
                        <td style="text-align:center">4 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->bpjs_kes,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{5+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Provisi Seragam </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{6+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Provisi Peralatan </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_devices,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{7+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Over Head Cost </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_ohc,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:center">{{8+count($daftarTunjangan)}}</td>
                        <td style="text-align:left" class="">Chemical </td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->personil_chemical,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">Total Biaya per Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="">Management Fee (MF)</td>
                        <td style="text-align:center">{{$quotationKebutuhan[0]->persentase}} %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">PPn <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">11 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td colspan="2" style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">-2 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td colspan="2" style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan,0,",",".")}}</td>
                        @endforeach
                      </tr>             
                    </tbody>
                  </table>
                </div>
                <div class="mt-3" style="padding-left:40px">
                  <p><b><i>Note :</i></b>	<br>
Tunjangan hari raya (gaji pokok dibagi 12).		<br>
Tunjangan overtime flat		<br>
<i>Cover</i> 
@if($quotationKebutuhan[0]->program_bpjs=="2 BPJS")
BPJS Ketenagakerjaan 2 Program (JKK, JKM). 
@elseif($quotationKebutuhan[0]->program_bpjs=="3 BPJS")
BPJS Ketenagakerjaan 3 Program (JKK, JKM, JHT). 
@elseif($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
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
                        <td class="text-end">{{"Rp. ".number_format($quotationKebutuhan[0]->nominal_upah,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      @foreach($daftarTunjangan as $it => $tunjangan)
                      <tr>
                        <td>{{$tunjangan->nama}}</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->{$tunjangan->nama},0,",",".")}}</th>
                        @endforeach
                      </tr>
                      @endforeach
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Base Manpower Cost per Month (THP)</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_base_manpower,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td class="fw-bold">2. EXCLUDE BASE MANPOWER COST</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-center fw-bold">Unit/Month</th>
                        @endforeach
                      </tr>
                      @if($master->thr=="Ditagihkan" || $master->thr=="Diprovisikan")
                      <tr>
                        <td>Provisi Tunjangan Hari Raya (THR)</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">@if($master->thr=="Ditagihkan")Ditagihkan Terpisah @else {{"Rp. ".number_format($detailJabatan->tunjangan_hari_raya,0,",",".")}} @endif</th>
                        @endforeach
                      </tr>
                      @endif
                      <tr>
                        <td>Premi BPJS TK J. Kecelakaan Kerja</th>
                        <td class="text-center">@if($quotationKebutuhan[0]->resiko=="Sangat Rendah") 0,24 @elseif($quotationKebutuhan[0]->resiko=="Rendah") 0,54 @elseif($quotationKebutuhan[0]->resiko=="Sedang") 0,89 @elseif($quotationKebutuhan[0]->resiko=="Tinggi") 1,27 @elseif($quotationKebutuhan[0]->resiko=="Sangat Tinggi") 1,74 @endif %</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkk,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Premi BPJS TK J. Kematian</th>
                        <td class="text-center">0,30 %</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jkm,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      @if($quotationKebutuhan[0]->program_bpjs=="3 BPJS" || $quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                      <tr>
                        <td>Premi BPJS TK J. Hari Tua</th>
                        <td class="text-center">3,7 %</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jht,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      @endif
                      @if($quotationKebutuhan[0]->program_bpjs=="4 BPJS")
                      <tr>
                        <td>Premi BPJS TK J. Pensiun</th>
                        <td class="text-center">2 %</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_jp,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      @endif
                      <tr>
                        <td>Premi BPJS Kesehatan</th>
                        <td class="text-center">4 %</th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->bpjs_kes,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Seragam</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_kaporlap,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Peralatan</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_devices,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr>
                        <td>Provisi Chemical</th>
                        <td class="text-center"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="text-end">{{"Rp. ".number_format($detailJabatan->personil_chemical,0,",",".")}}</th>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td class="fw-bold text-center">Total Exclude Base Manpower Cost</th>
                        <td class="text-center fw-bold"></th>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td class="fw-bold" style="text-align:right">Rp {{number_format($detailJabatan->total_exclude_base_manpower,0,",",".")}}</th>
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
                        <td rowspan="5" style="text-align:right;font-weight:bold"><span data-kebutuhan_detail_id="{{$detailJabatan->id}}" data-quotation_kebutuhan_id="{{$detailJabatan->quotation_kebutuhan_id}}" class="edit-biaya-monitoring">Rp {{number_format($detailJabatan->biaya_monitoring_kontrol,0,",",".")}}</span></td>
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
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_personil_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">Sub Total Biaya All Personil</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->sub_total_personil_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="">Management Fee (MF) <span class="text-danger">*dari sub total biaya</span></td>
                        <td style="text-align:center">{{$quotationKebutuhan[0]->persentase}} %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->management_fee_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">Grand Total Sebelum Pajak</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->grand_total_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">PPn <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">11 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->ppn_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="">
                        <td style="text-align:right" class="fw-bold">PPh <span class='text-danger'>*dari management fee</span></td>
                        <td style="text-align:center">-2 %</td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pph_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">TOTAL INVOICE</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->total_invoice_coss,0,",",".")}}</td>
                        @endforeach
                      </tr>
                      <tr class="table-success">
                        <td style="text-align:right" class="fw-bold">PEMBULATAN</td>
                        <td style="text-align:center"></td>
                        @foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan)
                        <td style="text-align:right" class="">{{"Rp. ".number_format($detailJabatan->pembulatan_coss,0,",",".")}}</td>
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
                    <tbody>
                      <tr>
                        <td style="text-align:left">Nominal</td>
                        <td style="text-align:right">
                          @php
                          $totalNominal = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalNominal += $detailJabatan->total_invoice;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalNominal,0,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalNominalCoss = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalNominalCoss += $detailJabatan->total_invoice_coss;
                          }
                        @endphp
                          {{"Rp. ".number_format($totalNominalCoss,0,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">PPN</td>
                        <td style="text-align:right">
                        @php
                          $ppn = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $ppn += $detailJabatan->ppn;
                          }
                          @endphp
                          {{"Rp. ".number_format($ppn,0,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $ppnCoss = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $ppnCoss += $detailJabatan->ppn_coss;
                          }
                          @endphp
                          {{"Rp. ".number_format($ppnCoss,0,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Total Biaya</td>
                        <td style="text-align:right">
                        @php
                          $totalBiaya = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalBiaya += $detailJabatan->sub_total_personil;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiaya,0,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                          $totalBiayaCoss = 0;
                          foreach($quotationKebutuhan[0]->kebutuhan_detail as $detailJabatan){
                            $totalBiayaCoss += $detailJabatan->sub_total_personil_coss;
                          }
                          @endphp
                          {{"Rp. ".number_format($totalBiayaCoss,0,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td style="text-align:left">Margin</td>
                        <td style="text-align:right">
                          @php
                            $margin = $totalNominal-$ppn-$totalBiaya;
                          @endphp
                          {{"Rp. ".number_format($margin,0,",",".")}}
                        </td>
                        <td style="text-align:right">
                        @php
                            $marginCoss = $totalNominalCoss-$ppnCoss-$totalBiayaCoss;
                          @endphp
                          {{"Rp. ".number_format($marginCoss,0,",",".")}}
                        </td>
                      </tr>
                      <tr>
                        <td class="fw-bold" style="text-align:left">GPM</td>
                        <td class="fw-bold" style="text-align:right">
                          @php
                            $gpm = ($margin/$totalBiaya)*100;
                          @endphp
                          {{$gpm}} %
                        </td>
                        <td class="fw-bold" style="text-align:right">
                        @php
                            $gpmCoss = ($marginCoss/$totalBiayaCoss)*100;
                          @endphp
                          {{$gpmCoss}} %
                        </td>
                      </tr>
                    </tbody>
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
          <h5 class="card-title m-0">Detail Cost Structure</h5>
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
                @if($data->kebutuhan_id != 2)
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
                @if($master->step == 100 && $data->is_aktif == 0)
                <div class="col-12 d-flex justify-content-between">
                  <div></div>
                  <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'7','edit'=>1])}}" class="btn btn-primary btn-next w-20">
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
                @if($master->step == 100 && $data->is_aktif == 0)
                  <div class="col-12 d-flex justify-content-between">
                    <div></div>
                    <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'8','edit'=>1])}}" class="btn btn-primary btn-next w-20">
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
                @if($master->step == 100 && $data->is_aktif == 0)
                  <div class="col-12 d-flex justify-content-between">
                    <div></div>
                    <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'9','edit'=>1])}}" class="btn btn-primary btn-next w-20">
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
              @if($data->kebutuhan_id == 3)
              <div class="tab-pane fade" id="navs-top-chemical" role="tabpanel">
                <div class="row mb-5">
                @if($master->step == 100 && $data->is_aktif == 0)
                  <div class="col-12 d-flex justify-content-between">
                    <div></div>
                    <a href="{{route('quotation.step',['id'=>$data->id,'step'=>'10','edit'=>1])}}" class="btn btn-primary btn-next w-20">
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
          <h5 class="card-title m-0">Perjanjian Kerjasama</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'13','edit'=>1])}}">Edit</a></h6>
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
      <div class="col-12 col-lg-12">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center pb-0">
          <h5 class="card-title m-0">Cheklist Quotation</h5>
          @if($master->step == 100 && $data->is_aktif == 0)
          <h6 class="m-0"><a href="{{route('quotation.step',['id'=>$data->id,'step'=>'12','edit'=>1])}}">Edit</a></h6>
            @endif
          </div>
          <div class="card-body pt-0">
            <div class="row mt-5">
              <div class="table-responsive overflow-hidden">
                <table class="table table-hover" style="padding-right:0px !important">
                    <tbody>
                      <tr>
                        <td colspan="4" class="text-center fw-bold table-success">PERSONAL INFORMASI</td>
                      </tr>
                      <tr>
                        <td>Pengusul Kerjasama</td>
                        <td colspan="3" class="fw-bold">{{$master->nama_perusahaan}}</td>
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
                        <td colspan="3">PERJANJIAN KERJASAMA ALIH DAYA JASA {{strtoupper($data->kebutuhan)}}</td>
                      </tr>
                      <tr>
                        <td>Jumlah Personel</td>
                        <td colspan="3">{{$data->totalHc}}</td>
                      </tr>
                      <tr>
                        <td>NPWP  </td>
                        <td colspan="3">{{$master->npwp}}</td>
                      </tr>
                      <tr>
                        <td>Alamat NPWP </td>
                        <td colspan="3">{{$master->alamat_npwp}}</td>
                      </tr>
                      <tr>
                        <td colspan="4" class="text-center fw-bold table-success">INFORMASI KERJASAMA</td>
                      </tr>
                      <tr>
                        <td>Durasi Kerjasama</td>
                        <td>{{$master->durasi_kerjasama}}</td>
                        <td>Evaluasi {{$master->evaluasi_kontrak}}</td>
                        <td>{{$master->mulai_kontrak}} - {{$master->kontrak_selesai}}</td>
                      </tr>
                      <tr>
                        <td>Kontrak Karyawan</td>
                        <td>{{$master->jenis_kontrak}} {{$master->durasi_karyawan}}</td>
                        <td>Evaluasi {{$master->evaluasi_karyawan}}</td>
                        <td>Start {{$master->tgl_penempatan}}</td>
                      </tr>
                      <tr>
                        <td>Materai </td>
                        <td colspan="3">
                        {{$master->materai}}
                        </td>
                      </tr>
                      <tr>
                        <td>Hari Kerja dan Jam Kerja</td>
                        <td>{{$master->shift_kerja}}</td>
                        <td>{{$master->jam_kerja}}</td>
                        <td>{{$master->mulai_kerja}} s/d {{$master->selesai_kerja}}</td>
                      </tr>
                      <tr>
                        <td>System Kerja</td>
                        <td colspan="3">@if($master->lembur=="Tidak Ada") No Work No Pay @elseif($master->lembur!="") Ada Lembur @endif
                        </td>
                      </tr>
                      <tr>
                        <td>Kebijakan Cuti</td>
                        <td>{{$master->cuti}}</td>
                        <td>{{$master->gaji_saat_cuti}}</td>
                        <td>{{$master->prorate}} @if($master->prorate !=null) % @endif</td>
                      </tr>
                      <tr>
                        <td>Kunjungan Operasional </td>
                        <td colspan="2">{{explode(" ",$master->kunjungan_operasional)[0]}} Kali dalam 1 {{explode(" ",$master->kunjungan_operasional)[1]}}</td>
                        <td>
                        {{$master->keterangan_kunjungan_operasional}}
                        </td>
                      </tr>
                      <tr>
                        <td>Kunjungan Tim CRM </td>
                        <td colspan="2">{{explode(" ",$master->kunjungan_tim_crm)[0]}} Kali dalam 1 {{explode(" ",$master->kunjungan_tim_crm)[1]}}</td>
                        <td>
                        {{$master->keterangan_kunjungan_tim_crm}}
                        </td>
                      </tr>
                      <tr>
                        <td>Training </td>
                        <td colspan="3">{{$master->training}}</td>
                      </tr>
                      <tr>
                        <td>Tunjangan Hari Raya (THR)</td>
                        <td colspan="3">
                        @if($master->thr=="Tidak Ada")
                        <b>Tidak Ada</b>
                        @else
                          <b>{{$master->thr}}</b> terpisah H-45 hari raya base on upah pokok
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
                      @if($data->penjamin=="Takaful")
                      <tr>
                        <td>Penjamin</td>
                        <td colspan="3">{{$data->penjamin}}</td>
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
                                <td class="text-center">@if($data->resiko=="Sangat Rendah") 0,24 @elseif($data->resiko=="Rendah") 0,54 @elseif($data->resiko=="Sedang") 0,89 @elseif($data->resiko=="Tinggi") 1,27 @elseif($data->resiko=="Sangat Tinggi") 1,74 @endif %</td>
                                <td class="text-center">&nbsp;</td>
                              </tr>
                              <tr>
                                <td class="text-center">JKM</td>
                                <td class="text-center">0,3 %</td>
                                <td class="text-center">&nbsp;</td>
                              </tr>
                              @if($data->program_bpjs=="3 BPJS" || $data->program_bpjs=="4 BPJS")
                              <tr>
                                <td class="text-center">JHT</td>
                                <td class="text-center">3,7 %</td>
                                <td class="text-center">2%</td>
                              </tr>
                              @endif
                              @if($data->program_bpjs=="4 BPJS")
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
                          {{$master->kompensasi}}
                        </td>
                      </tr>
                      <tr>
                        <td>Joker / Reliever </td>
                        <td colspan="3">{{$master->joker_reliever}}</td>
                      </tr>
                      <tr>
                        <td>Syarat Invoice </td>
                        <td colspan="3">{{$master->syarat_invoice}}</td>
                      </tr>
                      <tr>
                        <td><i>Term of Payment</i>&nbsp;<b>(TOP)</b></td>
                        <td colspan="3"><b>Talangan @if($master->top=="Lebih Dari 7 Hari"){{$master->jumlah_hari_invoice}} hari {{$master->tipe_hari_invoice}} @else {{$master->top}} @endif setelah invoice & lampiran diterima</b></td>
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
                        <td colspan="3">{{$master->lembur}}</td>
                      </tr>
                      <tr>
                        <td>Alamat Penagihan Invoice </td>
                        <td colspan="3">{{$master->alamat_penagihan_invoice}}</td>
                      </tr>
                      <tr>
                        <td>Catatan Site </td>
                        <td colspan="3">{{$master->catatan_site}}</td>
                      </tr>
                      <tr>
                        <td>Status Serikat </td>
                        <td colspan="3">{{$master->status_serikat}}</td>
                      </tr>
                      <tr>
                        <td>Penempatan/serah terima</td>
                        <td colspan="3">Start serah terima tanggal {{$master->tgl_penempatan}}</td>
                      </tr>
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
              d.quotation_kebutuhan_id = {{$data->id}};
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
              d.quotation_id = {{$master->id}};
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
  
    @foreach($kebutuhanDetail as $kkd => $detail)
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
                d.quotation_kebutuhan_detail_id = {{$detail->id}};
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
                  quotation_kebutuhan_detail_id:{{$detail->id}}
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
    
</script>
@endsection