@extends('layouts.master')
@section('title','View Kontrak')
@section('content')
<style>
    .tab {
        display: inline-block;
        margin-left: 3em;
    }

</style>
<div class="container-fluid flex-grow-1 container-p-y" style="margin-top: 4rem !important;">
    <div class="row gy-4 mb-5">
        <div class="col-xl-12">
            <div class="card h-100 mt-3">
                <div class="card-header d-flex w-100" style="justify-content: space-between;">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <h4 class="card-title mb-1">Detail Kontrak</h4>
                            @if(count($listQuotation) == 0)
                            <span class="text-danger">
                                <strong>Belum ada Quotation.</strong>
                            </span>
                            @else
                            @foreach($listQuotation as $quotation)
                                @if($quotation->step < 100)
                                    <span class="text-warning">
                                        <strong>Quotation {{$quotation->nomor}} belum Lengkap.</strong>
                                    </span>
                                @endif
                            @endforeach
                            @endif
                        </div>
                        <div>
                            @if(count($listQuotation) == 0)
                            <a href="{{ route('lengkapi-quotation.add', $pks->id) }}" class="btn btn-primary">
                                <i class="mdi mdi-file-document-edit-outline"></i> &nbsp; Lengkapi Quotation
                            </a>
                            <a href="{{ route('quotation-sandbox.add', $pks->id) }}" class="btn btn-danger">
                                <i class="mdi mdi-file-document-edit-outline"></i> &nbsp; Quotation Sandbox
                            </a>
                            @else
                                @if($pks->status_pks_id == 6 && in_array(Auth::user()->role_id, [56, 2]))
                                    <a href="javascript:void(0)" class="btn btn-info waves-effect waves-light" id="aktifkan-site" data-id="{{$pks->id}}">
                                        <span class="me-1">Aktifkan Site</span>
                                        <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
                                    </a>
                                @elseif($pks->status_pks_id == 7)
                                    <a href="javascript:void(0)" class="btn btn-success waves-effect waves-light">
                                        <span class="me-1">Site Aktif</span>
                                        <i class="mdi mdi-check"></i>
                                    </a>
                                @endif
                                @if(!$isLowongan)
                                    <a href="javascript:void(0)" class="btn btn-warning waves-effect waves-light" id="buat-lowongan" data-id="{{$pks->id}}">
                                        <span class="me-1">Buat Lowongan</span>
                                        <i class="mdi mdi-arrow-right scaleX-n1-rtl"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0)" class="btn btn-success waves-effect waves-light">
                                        <span class="me-1">Lowongan Terbentuk</span>
                                        <i class="mdi mdi-check"></i>
                                    </a>
                                @endif
                                @if($pks->status_pks_id != 7)
                                    <a href="javascript:void(0)" id="btn-ajukan-ulang" class="btn btn-danger waves-effect waves-light">
                                        <span class="me-1">Ajukan Ulang Quotation</span>
                                        <i class="mdi mdi-reload scaleX-n1-rtl"></i>
                                    </a>
                                @endif
                            @endif
                            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- <div class="d-flex justify-content-between mb-3">
                        <button id="btn-toggle" class="btn btn-info btn-sm" onclick="toggleLeftPanel()">
                            <i class="mdi mdi-eye-off"></i>&nbsp; Hide Detail Kontrak
                        </button>
                    </div> -->
                    <div class="row">
                        <!-- Aktifitas Leads -->
                        <div class="mb-3" id="right-panel">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="informasi-dasar-tab" data-bs-toggle="tab" href="#informasi-dasar" role="tab" aria-controls="informasi-dasar" aria-selected="true">
                                        <i class="mdi mdi-information-outline"></i> &nbsp; Informasi Dasar
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab" aria-controls="activities" aria-selected="true">
                                        <i class="mdi mdi-calendar-check"></i> &nbsp; Aktifitas
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="hpp-tab" data-bs-toggle="tab" href="#hpp" role="tab" aria-controls="hpp" aria-selected="false">
                                    <i class="mdi mdi-cash-multiple"></i> &nbsp; HPP</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="coss-tab" data-bs-toggle="tab" href="#coss" role="tab" aria-controls="coss" aria-selected="false">
                                        <i class="mdi mdi-currency-usd"></i> &nbsp; Harga Jual</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="gpm-tab" data-bs-toggle="tab" href="#gpm" role="tab" aria-controls="gpm" aria-selected="false">
                                        <i class="mdi mdi-chart-line"></i> &nbsp; GPM</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="issues-tab" data-bs-toggle="tab" href="#issues" role="tab" aria-controls="issues" aria-selected="false">
                                        <i class="mdi mdi-alert-circle"></i> &nbsp; Issue</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="perjanjian-tab" data-bs-toggle="tab" href="#perjanjian" role="tab" aria-controls="perjanjian" aria-selected="false">
                                        <i class="mdi mdi-file-document-outline"></i> &nbsp; Perjanjian</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="informasi-dasar" role="tabpanel" aria-labelledby="informasi-dasar-tab">
                                     <!-- Informasi Leads / Customer -->
                                    <!-- <div class="col-md-4 mb-3" id="left-panel">
                                        <div class="row mb-5">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light" style="background-color: #007bff;">
                                                    <tr>
                                                        <th colspan="2" style="color:white !important">Informasi Kontrak</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Kontrak</td>
                                                        <td><b>{{$pks->nomor}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Perusahaan</td>
                                                        <td>{{$leads->nama_perusahaan}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Layanan</td>
                                                        <td>{{$pks->layanan}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal Kontrak</td>
                                                        <td>{{$pks->mulai_kontrak}} s/d {{$pks->kontrak_selesai}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Berakhir dalam</td>
                                                        <td>{{$pks->berakhir_dalam}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Site</td>
                                                        <td>{{$pks->nama_site}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sales</td>
                                                        <td>{{$pks->sales}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>CRM</td>
                                                        <td>{!! $pks->crm1 ?? '' !!}{!! $pks->crm2 ?? '' !!}{!! $pks->crm3 ?? '' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>SPV RO</td>
                                                        <td>{!! $pks->spv_ro ?? '' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>RO</td>
                                                        <td>{!! $pks->ro1 ?? '' !!}{!! $pks->ro2 ?? '' !!}{!! $pks->ro3 ?? '' !!}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Quotation</td>
                                                        <td><b>-</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>SPK</td>
                                                        <td><b>-</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row mb-5">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-light" style="background-color: #28a745;">
                                                    <tr>
                                                        <th colspan="2" style="color:white !important">Informasi Leads / Customer</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Nomor</td>
                                                        <td><b>{{$leads->nomor}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nama</td>
                                                        <td>{{$leads->nama_perusahaan}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Telepon</td>
                                                        <td>{{$leads->telp_perusahaan}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alamat</td>
                                                        <td>{{$leads->alamat}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jenis Perusahaan</td>
                                                        <td>{{$pks->jenis_perusahaan}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> -->

                                    <h6>1. Informasi Leads / Customer</h6>
                                    <div class="row mb-3">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td>Nomor</td>
                                                        <td colspan="3">: <a href="#"><b>{{$leads->nomor ?? '-'}}</b></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nama Perusahaan</td>
                                                        <td>: {{$leads->nama_perusahaan}}</td>
                                                        <td>Bidang Perusahaan</td>
                                                        <td>: {{$leads->bidang_perusahaan}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>PMA</td>
                                                        <td>: {{$leads->pma ?? '-'}}</td>
                                                        <td>Negara</td>
                                                        <td>: {{$leads->negara ?? '-'}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Provinsi</td>
                                                        <td>: {{$leads->provinsi ?? '-'}}</td>
                                                        <td>Kota</td>
                                                        <td>: {{$leads->kota ?? '-'}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kecamatan</td>
                                                        <td>: {{$leads->kecamatan ?? '-'}}</td>
                                                        <td>Kelurahan</td>
                                                        <td>: {{$leads->kelurahan ?? '-'}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alamat</td>
                                                        <td colspan="3">: {{$leads->alamat ?? '-'}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <h6>2. Informasi Quotation</h6>
                                    <div class="row mb-3">
                                        <div class="table-responsive overflow-hidden table-quotation">
                                            <table id="table-quotation" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No.</th>
                                                        <th class="text-center">Nomor</th>
                                                        <th class="text-center">Kebutuhan</th>
                                                        <th class="text-center">Jenis Kontrak</th>
                                                        <th class="text-center">Checklist</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($listQuotation as $index => $quotation)
                                                    <tr>
                                                        <td>{{$index + 1}}</td>
                                                        <td><b><a href="{{route('quotation.view',[$quotation->id])}}">{{$quotation->nomor}}</a></b></td>
                                                        <td>{{$quotation->kebutuhan}}</td>
                                                        <td>{{$quotation->jenis_kontrak}}</td>
                                                        <td>
                                                            <div class="d-flex justify-content-center gap-2 mt-2">
                                                                <a href="{{ route('pks.isi-checklist', ['id' => $quotation->id, 'pks_id' => $pks->id]) }}" class="btn btn-primary" title="Isi Checklist">
                                                                    <i class="mdi mdi-pencil"></i>
                                                                </a>
                                                                @if($quotation->materai !=null)
                                                                <a onclick="window.open('{{route('quotation.cetak-checklist', ['id' => $quotation->id, 'pks_id' => $pks->id])}}','name','width=600,height=400')" rel="noopener noreferrer" href="javascript:void(0)" class="btn btn-warning" title="Cetak Checklist">
                                                                    <i class="mdi mdi-printer"></i>
                                                                </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <h6>3. Informasi SPK</h6>
                                    <div class="row mb-3">
                                        <div class="table-responsive overflow-hidden table-spk">
                                            <table id="table-spk" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No.</th>
                                                        <th class="text-center">Nomor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($listSpk as $index => $spk)
                                                    <tr>
                                                        <td>{{$index + 1}}</td>
                                                        <td><b><a href="{{route('spk.view',[$spk->id])}}">{{$spk->nomor}}</a></b></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <h6>4. Informasi Site</h6>
                                    <div class="row mb-3">
                                        <div class="table-responsive overflow-hidden table-site">
                                            <table id="table-site" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">No.</th>
                                                        <th class="text-center">Nama Site</th>
                                                        <th class="text-center">Kota</th>
                                                        <th class="text-center">Penempatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody-site">
                                                    @foreach($pks->site as $index => $site)
                                                    <tr>
                                                        <td>{{$index + 1}}</td>
                                                        <td>{{$site->nama_site}}</td>
                                                        <td>{{$site->kota}}</td>
                                                        <td>{{$site->penempatan}}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                                    <div class="d-flex justify-content-end mb-3">
                                        <a href="{{ route('customer-activity.add-activity-kontrak', $pks->id) }}" class="btn btn-primary">
                                            <i class="mdi mdi-plus"></i> Tambah Aktifitas
                                        </a>
                                    </div>
                                    <ul class="timeline">
                                        @foreach($activityList as $activity)
                                            <li class="timeline-item">
                                                <span class="timeline-point timeline-point-primary"></span>
                                                <div class="timeline-event card card-border-shadow-primary mb-3 ">
                                                    <div class="card-header" style="padding-bottom:0px !important">
                                                        <button class="btn btn-danger btn-sm float-end" onclick="deleteAktifitas({{ $activity->id }})">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                        <h6 class="timeline-title">{{$activity->nomor}}</h6>
                                                        <h6 class="timeline-title">{{$activity->tipe}}</h6>
                                                    </div>
                                                    <div class="card-body" style="padding-top:0px !important">
                                                        @if($activity->tgl_realisasi)
                                                            <p class="card-text" style="margin-bottom:0px !important">Tanggal : {{ \Carbon\Carbon::createFromFormat('Y-m-d',$activity->tgl_realisasi)->isoFormat('D MMMM Y') }}</p>
                                                        @endif
                                                        @if($activity->jam_realisasi)
                                                            <p class="card-text" style="margin-bottom:0px !important">Jam : {{ \Carbon\Carbon::parse($activity->jam_realisasi)->format('H:i') }}</p>
                                                        @endif
                                                        @if($activity->start && $activity->end)
                                                            <p class="card-text" style="margin-bottom:0px !important">Jam : {{ \Carbon\Carbon::parse($activity->start)->format('H:i') }} s/d {{ \Carbon\Carbon::parse($activity->end)->format('H:i') }}</p>
                                                        @endif
                                                        @if($activity->durasi)
                                                            <p class="card-text" style="margin-bottom:0px !important">Durasi : {{$activity->durasi}}</p>
                                                        @endif
                                                        @if($activity->jenis_visit)
                                                            <p class="card-text" style="margin-bottom:0px !important">Jenis Visit : {{$activity->jenis_visit}}</p>
                                                        @endif
                                                        @if($activity->notes)
                                                            <p class="card-text" style="margin-bottom:0px !important">Notes : {{$activity->notes}}</p>
                                                        @endif
                                                        @if($activity->notes_tipe)
                                                            <p class="card-text" style="margin-bottom:0px !important">Notes : {{$activity->notes_tipe}}</p>
                                                        @endif
                                                        @if($activity->notulen)
                                                            <p class="card-text" style="margin-bottom:0px !important">Notulen : {{$activity->notulen}}</p>
                                                        @endif
                                                        <p class="card-text">Aktifitas pada : {{$activity->screated_at}}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="hpp" role="tabpanel" aria-labelledby="hpp-tab">
                                    @foreach($listQuotation as $index => $quotation)
                                        @if($quotation != null)
                                        @if($quotation->step >= 100)
                                            @include('sales.quotation.includes.hpp')
                                        @else
                                            <div class="alert alert-warning" role="alert">
                                                <strong>Quotation Belum Lengkap.</strong>
                                            </div>
                                        @endif
                                        @else
                                        <div class="alert alert-warning" role="alert">
                                            <strong>Data HPP tidak ditemukan.</strong>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="coss" role="tabpanel" aria-labelledby="coss-tab">
                                    @foreach($listQuotation as $index => $quotation)
                                        @if($quotation != null)
                                        @if($quotation->step >= 100)
                                            @include('sales.quotation.includes.coss')
                                        @else
                                            <div class="alert alert-warning" role="alert">
                                                <strong>Harga Jual Belum Lengkap.</strong>
                                            </div>
                                        @endif
                                        @else
                                        <div class="alert alert-warning" role="alert">
                                            <strong>Data Harga Jual tidak ditemukan.</strong>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="gpm" role="tabpanel" aria-labelledby="gpm-tab">
                                    @foreach($listQuotation as $index => $quotation)
                                        @if($quotation != null)
                                        @if($quotation->step >= 100)
                                            @include('sales.quotation.includes.gpm')
                                        @else
                                            <div class="alert alert-warning" role="alert">
                                                <strong>Quotation Belum Lengkap.</strong>
                                            </div>
                                        @endif
                                        @else
                                        <div class="alert alert-warning" role="alert">
                                            <strong>Data GPM tidak ditemukan.</strong>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                                    <div class="d-flex justify-content-end mb-3">
                                        <a href="{{ route('monitoring-kontrak.add-issue', $pks->id) }}" class="btn btn-primary">
                                            <i class="mdi mdi-plus"></i> Tambah Issue
                                        </a>
                                    </div>
                                    <ul class="timeline">
                                        @foreach($issues as $issue)
                                            <li class="timeline-item">
                                                <span class="timeline-point timeline-point-danger"></span>
                                                <div class="timeline-event card card-border-shadow-danger mb-3 ">
                                                    <div class="card-header" style="padding-bottom:0px !important">
                                                        <button class="btn btn-danger btn-sm float-end" onclick="deleteIssue({{ $issue->id }})">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                        <h6 class="timeline-title">{{$issue->jenis_keluhan}} - {{$issue->judul}}</h6>
                                                    </div>
                                                    <div class="card-body" style="padding-top:0px !important">
                                                        <p class="card-text">{{$issue->deskripsi}}</p>
                                                        <p class="card-text">Issue pada : {{$issue->screated_at}}</p>
                                                    </div>
                                                    @if($issue->url_lampiran)
                                                        <div class="card-footer">
                                                            <a href="{{ $issue->url_lampiran }}" target="_blank" class="btn btn-info">
                                                                <i class="mdi mdi-eye"></i> View Lampiran
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="perjanjian" role="tabpanel" aria-labelledby="perjanjian-tab">
                                    <div class="d-flex justify-content-end mb-3">
                                        <a href="#" target="_blank" class="btn btn-success">
                                            <i class="mdi mdi-printer"></i> Cetak Dokumen
                                        </a>
                                    </div>
                                    <ul class="timeline">
                                        @foreach($perjanjian as $key => $value)
                                            <li class="timeline-item">
                                                <span class="timeline-point timeline-point-info"></span>
                                                <div class="timeline-event card card-border-shadow-info mb-3">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>Pasal {{$value->pasal}}</strong>
                                                            <span class="mx-2">|</span>
                                                            <span>{{$value->judul}}</span>
                                                        </div>
                                                        <a href="{{ route('pks.edit-perjanjian', $value->id) }}" class="btn btn-warning btn-sm">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    </div>
                                                    <div class="card-body">
                                                        {!! $value->raw_text !!}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Congratulations card -->
    </div>
</div>
@endsection

@section('pageScript')
<script src="{{ asset('public/assets/js/dashboards-crm.js') }}"></script>
<script>
    window.addEventListener('pageshow', function (event) {
        if (sessionStorage.getItem('forceRefresh') === 'true') {
            sessionStorage.removeItem('forceRefresh');
            location.reload();
        }
    });
</script>
<script>
    function deleteIssue(issueId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Ingin menghapus issue ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sementara kami menghapus issue.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: "{{ route('monitoring-kontrak.delete-issue') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: issueId
                    },
                    success: function(response) {
                        if (response.status=="success") {
                            Swal.fire(
                                'Terhapus!',
                                'Issue berhasil dihapus.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus issue.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus issue.',
                            'error'
                        );
                    }
                });
            }
        });
    }
    function deleteAktifitas(aktifitasId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Ingin menghapus Aktifitas ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sementara kami menghapus aktifitas.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    url: "{{ route('monitoring-kontrak.delete-activity') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: aktifitasId
                    },
                    success: function(response) {
                        if (response.status=="success") {
                            Swal.fire(
                                'Terhapus!',
                                'Aktifitas berhasil dihapus.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus issue.',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus issue.',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>

<script>
    function toggleLeftPanel() {
        const left = document.getElementById('left-panel');
        const right = document.getElementById('right-panel');
        const toggleButton = document.getElementById('btn-toggle');
        if (left.style.display === 'none') {
            toggleButton.innerHTML = '<i class="mdi mdi-eye-off"></i>&nbsp; Hide Detail Kontrak';
        } else {
            toggleButton.innerHTML = '<i class="mdi mdi-eye"></i>&nbsp; Show Detail Kontrak';
        }

        if (left.style.display === 'none') {
            left.style.display = 'block';
            right.classList.remove('col-12');
            right.classList.add('col-md-7');
            right.classList.add('offset-md-1');
        } else {
            left.style.display = 'none';
            right.classList.remove('col-md-7', 'offset-md-1');
            right.classList.add('col-12');
        }
    }
</script>

<script>
  $('#btn-ajukan-ulang').on('click',function () {
    Swal.fire({
      title: 'Konfirmasi',
      text: `Apakah Anda ingin mengajukan quotation ulang untuk PKS nomor {{$pks->nomor}} ?`,
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
            let baseUrl = "{{ route('pks.ajukan-ulang-quotation', ['pks' => ':pks']) }}";
            let url = baseUrl.replace(':pks', {{$pks->id}});
            // Tambahkan alasan sebagai parameter URL
            url += `?alasan=${encodeURIComponent(alasan)}`;

            location.href = url;
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
  });


  $('#btn-upload-pks').on('click', function() {
        @if(!$isIsiChecklist)
        Swal.fire({
            icon: 'warning',
            title: 'Checklist Belum Lengkap',
            text: 'Silakan isi checklist pada quotation terlebih dahulu sebelum Upload PKS.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
        @endif
        // Menampilkan SweetAlert dengan form upload
        Swal.fire({
            title: 'Upload File',
            html: `
                <form id="uploadForm" enctype="multipart/form-data">
                    <input style="width:80%" type="file" id="file" name="file" class="swal2-input" accept="application/pdf">
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            preConfirm: () => {
                const fileInput = document.getElementById('file');

                // Validasi jika file tidak dipilih
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('Silakan pilih file terlebih dahulu');
                    return false;
                }

                const file = fileInput.files[0];
                const maxSize = 4 * 1024 * 1024; // 4MB dalam byte

                // Validasi ukuran file
                if (file.size > maxSize) {
                    Swal.showValidationMessage('Ukuran file terlalu besar! Maksimum 4MB.');
                    return false;
                }

                return file; // Mengembalikan file yang dipilih
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Ambil file dari form SweetAlert
                var file = result.value;
                var formData = new FormData();
                formData.append('file', file);
                formData.append('id',{{$pks->id}});
                formData.append('_token', '{{ csrf_token() }}'); // Pastikan ada CSRF token

                // Kirim file menggunakan AJAX
                $.ajax({
                    url: '{{route("pks.upload-pks")}}',  // URL untuk upload di Laravel
                    type: 'POST',
                    data: formData,
                    contentType: false,  // Jangan menetapkan tipe konten
                    processData: false,  // Jangan memproses data yang dikirim
                    success: function(response) {
                        Swal.fire('Berhasil!', 'File berhasil diupload', 'success');
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat upload', 'error');
                    }
                });
            }
        });
    });

    $('body').on('click', '#approve-pks', function() {
        @if(!$isIsiChecklist)
        Swal.fire({
            icon: 'warning',
            title: 'Checklist Belum Lengkap',
            text: 'Silakan isi checklist pada quotation terlebih dahulu sebelum approve PKS.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
        @endif
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
          "ot":$(this).data('ot'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
          type: "POST",
          url: "{{route('pks.approve')}}",
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

$('body').on('click', '#buat-lowongan', function() {
    @if($pks->status_pks_id != 7)
        Swal.fire({
            icon: 'warning',
            title: 'Site Belum Aktif',
            text: 'Silakan aktifkan site terlebih dahulu sebelum membuat lowongan.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
    @endif
    Swal.fire({
        icon: "question",
        title: "Apakah anda yakin ingin membuat lowongan untuk PKS ini?",
        showCancelButton: true,
        confirmButtonText: "Buat Lowongan",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Now loading',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
            var formData = {
                id: $(this).data('id'),
                _token: "{{ csrf_token() }}"
            };
            $.ajax({
                type: "POST",
                url: "{{ route('pks.buat-lowongan') }}",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: "Pemberitahuan",
                        html: "Lowongan berhasil dibuat.",
                        icon: "success",
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr, status, errorThrown) {
                    console.log("AJAX ERROR:", xhr.status, errorThrown);
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        html: "Terjadi kesalahan saat membuat lowongan. Status: " + xhr.status,
                        icon: "error",
                    });
                }
            });
        }
    });
});
  $('body').on('click', '#aktifkan-site', function() {
    @if(!$isIsiChecklist)
        Swal.fire({
            icon: 'warning',
            title: 'Checklist Belum Lengkap',
            text: 'Silakan isi checklist pada quotation terlebih dahulu sebelum mengaktifkan site.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
        return false;
    @endif
    Swal.fire({
      icon: "question",
      title: "Apakah anda yakin ingin mengaktifkan data ini ?",
      showCancelButton: true,
      confirmButtonText: "Aktifkan",
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Now loading',
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading()
          }
        });

        let formData = {
          "id":$(this).data('id'),
          "_token": "{{ csrf_token() }}"
        };

        $.ajax({
            type: "POST",
            url: "{{route('pks.aktifkan-site')}}",
            data: formData,
            success: function(response) {
                if (response.status == 'error') {
                Swal.fire({
                    title: "Pemberitahuan",
                    html: response.message,
                    icon: "error",
                });
                return;
                }

                let timerInterval;
                Swal.fire({
                title: "Pemberitahuan",
                html: "Data berhasil diaktifkan.",
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
                if (result.dismiss === Swal.DismissReason.timer) {
                    location.reload();
                }
                });
            },
            error: function(xhr, status, errorThrown) {
                console.log("AJAX ERROR:", xhr.status, errorThrown);
                Swal.fire({
                title: "Terjadi Kesalahan",
                html: "Terjadi kesalahan saat memproses permintaan. Status: " + xhr.status,
                icon: "error",
                });
            }
            });
      }
    });
  });
</script>
@endsection
