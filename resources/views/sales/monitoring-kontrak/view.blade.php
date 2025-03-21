@extends('layouts.master')
@section('title','View Monitoring Kontrak')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row gy-4 mb-5">
        <!-- Congratulations card -->
        <div class="col-xl-12">
            <div class="card h-100">
                <div class="card-header d-flex w-100" style="justify-content: space-between;">
                    <h4 class="card-title mb-1 d-flex flex-wrap">Detail Kontrak</h4>
                    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informasi Leads / Customer -->
                        <div class="col-md-4 mb-3">
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
                                            <td><b>{{ $quotation ? $quotation->nomor : '' }}</b></td>
                                        </tr>
                                        <tr>
                                            <td>SPK</td>
                                            <td><b>{{ $spk ? $spk->nomor : '' }}</b></td>
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
                        </div>
                        <!-- Aktifitas Leads -->
                        <div class="offset-md-1 col-md-7 mb-3">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab" aria-controls="activities" aria-selected="true">Aktifitas</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="issues-tab" data-bs-toggle="tab" href="#issues" role="tab" aria-controls="issues" aria-selected="false">Issue</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                                    <ul class="timeline">
                                        @foreach($data as $activity)
                                            <li class="timeline-item">
                                                <span class="timeline-point timeline-point-primary"></span>
                                                <div class="timeline-event card card-border-shadow-primary mb-3 ">
                                                    <div class="card-header" style="padding-bottom:0px !important">
                                                        <h6 class="timeline-title">{{$activity->tipe}}</h6>
                                                    </div>
                                                    <div class="card-body" style="padding-top:0px !important">
                                                        <p class="card-text">{{$activity->notes}}</p>
                                                        <p class="card-text">Aktifitas pada : {{$activity->screated_at}}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="issues" role="tabpanel" aria-labelledby="issues-tab">
                                    <ul class="timeline">
                                        @foreach($issues as $issue)
                                            <li class="timeline-item">
                                                <span class="timeline-point timeline-point-danger"></span>
                                                <div class="timeline-event card card-border-shadow-danger mb-3 ">
                                                    <div class="card-header" style="padding-bottom:0px !important">
                                                        <h6 class="timeline-title">{{$issue->jenis_keluhan}}</h6>
                                                    </div>
                                                    <div class="card-body" style="padding-top:0px !important">
                                                        <p class="card-text">{{$issue->deskripsi}}</p>
                                                        <p class="card-text">Issue pada : {{$issue->screated_at}}</p>
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
        </div>
        <!--/ Congratulations card -->
    </div>
</div>
@endsection

@section('pageScript')
<script src="{{ asset('public/assets/js/dashboards-crm.js') }}"></script>
@endsection
