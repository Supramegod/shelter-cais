@extends('layouts.master')
@section('title', 'Track Customer Activity')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row gy-4 mb-5">
        <!-- Congratulations card -->
        <div class="col-xl-12">
            <div class="card h-100">
                <div class="card-header d-flex w-100" style="justify-content: space-between;">
                    <h4 class="card-title mb-1 d-flex flex-wrap">Track Aktifitas
                        @if($tipe=='Leads')
                            Leads / Customer
                        @elseif($tipe=='Quotation')
                            Quotation
                        @elseif($tipe=='SPK')
                            SPK
                        @elseif($tipe=='PKS')
                            PKS
                        @endif
                    </h4>
                    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informasi Leads / Customer -->
                        <div class="col-md-4 mb-3">
                            <div class="row mb-5">
                                <h5>Informasi Leads / Customer</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">Nomor : <b>{{$leads->nomor}}</b></li>
                                    <li class="list-group-item">Nama : {{$leads->nama_perusahaan}}</li>
                                    <li class="list-group-item">Telepon : {{$leads->telp_perusahaan}}</li>
                                    <li class="list-group-item">Alamat : {{$leads->alamat}}</li>
                                    <li class="list-group-item">Jenis Perusahaan : {{$leads->jenis_perusahaan}}</li>
                                </ul>
                            </div>
                            @foreach($quotation as $key => $quotation)
                                <div class="row mb-3">
                                    <h5>Quotation {{$key+1}}</h5>
                                    <ul class="list-group">
                                        <li class="list-group-item">Nomor : <b>{{$leads->nomor}}</b></li>
                                        <li class="list-group-item">Nama : {{$leads->nama_perusahaan}}</li>
                                        <li class="list-group-item">Telepon : {{$leads->telp_perusahaan}}</li>
                                        <li class="list-group-item">Alamat : {{$leads->alamat}}</li>
                                        <li class="list-group-item">Jenis Perusahaan : {{$leads->jenis_perusahaan}}</li>
                                    </ul>
                                </div>
                                @foreach($quotation as $key => $quotation)
                                    <div class="row mb-3">
                                        <h5>Quotation {{$key + 1}}</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item">Quotation : <b>{{$quotation->nomor}}</b></li>
                                            <li class="list-group-item">Perusahaan : {{$quotation->nama_perusahaan}}</li>
                                            <li class="list-group-item">kebutuhan : {{$quotation->kebutuhan}}</li>
                                            @foreach($quotation->site as $site)
                                                <li class="list-group-item">{{$site->nama_site}} :
                                                    @foreach($quotation->detail as $detail)
                                                        <br>- {{$detail->jabatan_kebutuhan}}
                                                    @endforeach
                                                </li>
                                            @endforeach
                                            <li class="list-group-item">SPK :
                                                <b>@if($quotation->spk != null){{$quotation->spk->nomor}} @else - @endif</b></li>
                                            <li class="list-group-item">PKS :
                                                <b>@if($quotation->pks != null){{$quotation->pks->nomor}} @else - @endif</b></li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Aktifitas Leads -->
                            <div class="offset-md-1 col-md-7 mb-3">
                                <h5>Aktifitas Leads</h5>
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
