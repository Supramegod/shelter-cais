@extends('layouts.master')
@section('title','View Monitoring Kontrak')
@section('content')
<style>
    .tab {
        display: inline-block;
        margin-left: 3em;
    }

</style>
<div class="container-fluid flex-grow-1 container-p-y" style="margin-top: 4rem !important;">
    <div class="row gy-4 mb-5">
        <!-- Congratulations card -->
        <div class="col-xl-12">
            <div class="card h-100 mt-3">
                <div class="card-header d-flex w-100" style="justify-content: space-between;">
                    <h4 class="card-title mb-1 d-flex flex-wrap">Detail Kontrak</h4>
                    <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <button id="btn-toggle" class="btn btn-info btn-sm" onclick="toggleLeftPanel()">
                            <i class="mdi mdi-eye-off"></i>&nbsp; Hide Detail Kontrak
                        </button>
                    </div>
                    <div class="row">
                        <!-- Informasi Leads / Customer -->
                        <div class="col-md-4 mb-3" id="left-panel">
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
                        <div class="offset-md-1 col-md-7 mb-3" id="right-panel">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab" aria-controls="activities" aria-selected="true">
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
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="activities" role="tabpanel" aria-labelledby="activities-tab">
                                    <div class="d-flex justify-content-end mb-3">
                                        <a href="{{ route('customer-activity.add-activity-kontrak', $pks->id) }}" class="btn btn-primary">
                                            <i class="mdi mdi-plus"></i> Tambah Aktifitas
                                        </a>
                                    </div>
                                    <ul class="timeline">
                                        @foreach($data as $activity)
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
                                    @if($quotation != null)
                                        @include('sales.quotation.includes.hpp')
                                        @else
                                        <div class="alert alert-warning" role="alert">
                                            <strong>Data HPP tidak ditemukan.</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="tab-pane fade" id="coss" role="tabpanel" aria-labelledby="coss-tab">
                                    @if($quotation != null)
                                        @include('sales.quotation.includes.coss')
                                        @else
                                        <div class="alert alert-warning" role="alert">
                                            <strong>Data Harga Jual tidak ditemukan.</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="tab-pane fade" id="gpm" role="tabpanel" aria-labelledby="gpm-tab">
                                    @if($quotation != null)
                                        @include('sales.quotation.includes.gpm')
                                        @else
                                        <div class="alert alert-warning" role="alert">
                                            <strong>Data GPM tidak ditemukan.</strong>
                                        </div>
                                    @endif
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
@endsection
