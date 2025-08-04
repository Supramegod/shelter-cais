@extends('layouts.master')
@section('title', 'Dashboard SDT Training')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span>Quotation</h4> -->
        <!-- Card Border Shadow -->
        @if(in_array(Auth::user()->role_id, [56]))
            <div class="row mt-3">
                <div class="col-xl-12">
                    <div class="card mb-4">
                        <div class="card-header p-0">
                            <div class="nav-align-top">
                                <ul class="nav nav-tabs nav-fill" role="tablist">
                                    <li class="nav-item">
                                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                            data-bs-target="#navs-aktifkan-anda" aria-controls="navs-aktifkan-anda"
                                            aria-selected="true">
                                            <i class="tf-icons mdi mdi-home-outline me-1"></i> Menunggu Aktifkan Site
                                            <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1">
                                                {{$jumlahMenungguManagerCrm}}
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <div class="tab-pane fade show active" id="navs-aktifkan-anda" role="tabpanel">
                                    <div class="table-responsive overflow-hidden table-aktifkan-anda">
                                        <table id="table-aktifkan-anda" class="dt-column-search table w-100 table-hover"
                                            style="text-wrap: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ID</th>
                                                    <th class="text-center">No.</th>
                                                    <th class="text-center">Tanggal</th>
                                                    <th class="text-center">Leads/Customer</th>
                                                    <th class="text-center">Kebutuhan</th>
                                                    <th class="text-center">Site</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Entitas</th>
                                                    <th class="text-center">Created By</th>
                                                    <th class="text-center">Aksi</th>
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
        @else
            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-primary"><i
                                            class="mdi mdi-file-sign mdi-20px"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{$jumlahTraining}} </h4>
                            </div>
                            <p class="mb-0 text-heading ">Training</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="mdi mdi-file-sign mdi-20px"></i>
                                    </span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{$jumlahClient}}</h4>
                            </div>
                            <p class="mb-0 text-heading ">Client</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-secondary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-file-sign mdi-20px"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{$jumlahTrainer}}</h4>
                            </div>
                            <p class="mb-0 text-heading ">Trainer</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2 pb-1">
                                <div class="avatar me-2">
                                    <span class="avatar-initial rounded bg-label-info"><i
                                            class="mdi mdi-file-sign mdi-20px"></i></span>
                                </div>
                                <h4 class="ms-1 mb-0 display-6">{{$jumlahMateri}}</h4>
                            </div>
                            <p class="mb-0 text-heading ">Materi</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row mt-3">
                <div class="col-xl-12">
                    <div class="card mb-4">
                    <div class="card-header p-0">
                        <div class="nav-align-top">
                        <ul class="nav nav-tabs nav-fill" role="tablist">
                            <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link active"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-approval-anda"
                                aria-controls="navs-approval-anda"
                                aria-selected="true">
                                <i class="tf-icons mdi mdi-home-outline me-1"></i> Approval Anda
                                @if(in_array(Auth::user()->role_id,[96,97,99]))
                                <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1">
                                @if(Auth::user()->role_id==96)
                                {{$jumlahMenungguDirSales}}
                                @elseif(Auth::user()->role_id==97)
                                {{$jumlahMenungguDirkeu}}
                                @elseif(Auth::user()->role_id==99)
                                {{$jumlahMenungguDirut}}
                                @endif
                                </span>
                                @endif
                            </button>
                            </li>
                            <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-menunggu-approval"
                                aria-controls="navs-menunggu-approval"
                                aria-selected="false">
                                <i class="tf-icons mdi mdi-account-outline me-1"></i> Quotation Menunggu Approval
                            </button>
                            </li>
                            <li class="nav-item">
                            <button
                                type="button"
                                class="nav-link"
                                role="tab"
                                data-bs-toggle="tab"
                                data-bs-target="#navs-belum-lengkap"
                                aria-controls="navs-belum-lengkap"
                                aria-selected="false">
                                <i class="tf-icons mdi mdi-message-text-outline me-1"></i> Quotation Belum Lengkap
                            </button>
                            </li>
                        </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="tab-pane fade show active" id="navs-approval-anda" role="tabpanel">
                                <div class="table-responsive overflow-hidden table-approval-anda">
                                    <table id="table-approval-anda" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Leads/Customer</th>
                                                <th class="text-center">Kebutuhan</th>
                                                <th class="text-center">Site</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Entitas</th>
                                                <th class="text-center">Created By</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- data table ajax --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-menunggu-approval" role="tabpanel">
                                <div class="table-responsive overflow-hidden table-menunggu-approval">
                                    <table id="table-menunggu-approval" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Leads/Customer</th>
                                                <th class="text-center">Kebutuhan</th>
                                                <th class="text-center">Site</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Entitas</th>
                                                <th class="text-center">Created By</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- data table ajax --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-belum-lengkap" role="tabpanel">
                                <div class="table-responsive overflow-hidden table-belum-lengkap">
                                    <table id="table-belum-lengkap" class="dt-column-search table w-100 table-hover" style="text-wrap: nowrap;">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID</th>
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Tanggal</th>
                                                <th class="text-center">Leads/Customer</th>
                                                <th class="text-center">Kebutuhan</th>
                                                <th class="text-center">Site</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Entitas</th>
                                                <th class="text-center">Created By</th>
                                                <th class="text-center">Aksi</th>
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
            </div> -->
        @endif
    </div>
@endsection

@section('pageScript')
    <script src="{{ asset('assets/js/dashboards-crm.js') }}"></script>

    @if(in_array(Auth::user()->role_id, [56]))
        <script>
            let dt_filter_table = $('.dt-column-search');

            // Formatting function for row details - modify as you need
            function format(d) {
                return (
                    '<dl>' +
                    '<dt>Status Leads Saat Ini :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">trial</dd>' +
                    '</dl>'
                );
            }
            var table = $('#table-aktifkan-anda').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('dashboard-aktifkan.list') }}",
                    data: function (d) { },
                },
                "createdRow": function (row, data, dataIndex) {
                    if (data.step != 100) {
                        $('td', row).css('background-color', '#f39c1240');
                        // $('td', row).css('color', '#fff');
                    } else if (data.is_aktif == 0) {
                        $('td', row).css('background-color', '#27ae6040');
                        // $('td', row).css('color', '#fff');
                    }
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                    data: 'quotation_id',
                    name: 'quotation_id',
                    visible: false,
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    className: 'text-center'
                }, {
                    data: 'tgl',
                    name: 'tgl',
                    className: 'text-center'
                }, {
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan',
                    className: 'text-center'
                }, {
                    data: 'kebutuhan',
                    name: 'kebutuhan',
                    className: 'text-center'
                }, {
                    data: 'nama_site',
                    name: 'nama_site',
                    className: 'text-center'
                }, {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                }, {
                    data: 'company',
                    name: 'company',
                    className: 'text-center'
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }],
                "language": datatableLang,
            });
        </script>
    @else
        <script>
            let dt_filter_table = $('.dt-column-search');

            // Formatting function for row details - modify as you need
            function format(d) {
                return (
                    '<dl>' +
                    '<dt>Status Leads Saat Ini :</dt>' +
                    '<dd style="font-weight:bold;color:#000056">trial</dd>' +
                    '</dl>'
                );
            }
            var table = $('#table-approval-anda').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('dashboard-approval.list') }}",
                    data: function (d) {
                        d.tipe = "menunggu-anda";
                    },
                },
                "createdRow": function (row, data, dataIndex) {
                    if (data.step != 100) {
                        $('td', row).css('background-color', '#f39c1240');
                        // $('td', row).css('color', '#fff');
                    } else if (data.is_aktif == 0) {
                        $('td', row).css('background-color', '#27ae6040');
                        // $('td', row).css('color', '#fff');
                    }
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                    data: 'quotation_id',
                    name: 'quotation_id',
                    visible: false,
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    className: 'text-center'
                }, {
                    data: 'tgl',
                    name: 'tgl',
                    className: 'text-center'
                }, {
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan',
                    className: 'text-center'
                }, {
                    data: 'kebutuhan',
                    name: 'kebutuhan',
                    className: 'text-center'
                }, {
                    data: 'nama_site',
                    name: 'nama_site',
                    className: 'text-center'
                }, {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                }, {
                    data: 'company',
                    name: 'company',
                    className: 'text-center'
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }],
                "language": datatableLang,
            });
        </script>
        <script>
            var tableMenungguApproval = $('#table-menunggu-approval').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('dashboard-approval.list') }}",
                    data: function (d) {
                        d.tipe = "menunggu-approval";
                    },
                },
                "createdRow": function (row, data, dataIndex) {
                    if (data.step != 100) {
                        $('td', row).css('background-color', '#f39c1240');
                        // $('td', row).css('color', '#fff');
                    } else if (data.is_aktif == 0) {
                        $('td', row).css('background-color', '#27ae6040');
                        // $('td', row).css('color', '#fff');
                    }
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                    data: 'quotation_id',
                    name: 'quotation_id',
                    visible: false,
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    className: 'text-center'
                }, {
                    data: 'tgl',
                    name: 'tgl',
                    className: 'text-center'
                }, {
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan',
                    className: 'text-center'
                }, {
                    data: 'kebutuhan',
                    name: 'kebutuhan',
                    className: 'text-center'
                }, {
                    data: 'nama_site',
                    name: 'nama_site',
                    className: 'text-center'
                }, {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                }, {
                    data: 'company',
                    name: 'company',
                    className: 'text-center'
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }],
                "language": datatableLang,
            });
        </script>

        <script>
            var tableBelumLengkap = $('#table-belum-lengkap').DataTable({
                scrollX: true,
                "iDisplayLength": 25,
                'processing': true,
                'language': {
                    'loadingRecords': '&nbsp;',
                    'processing': 'Loading...'
                },
                ajax: {
                    url: "{{ route('dashboard-approval.list') }}",
                    data: function (d) {
                        d.tipe = "quotation-belum-lengkap";
                    },
                },
                "createdRow": function (row, data, dataIndex) {
                    if (data.step != 100) {
                        $('td', row).css('background-color', '#f39c1240');
                        // $('td', row).css('color', '#fff');
                    } else if (data.is_aktif == 0) {
                        $('td', row).css('background-color', '#27ae6040');
                        // $('td', row).css('color', '#fff');
                    }
                },
                "order": [
                    [0, 'desc']
                ],
                columns: [{
                    data: 'quotation_id',
                    name: 'quotation_id',
                    visible: false,
                    searchable: false
                }, {
                    data: 'nomor',
                    name: 'nomor',
                    className: 'text-center'
                }, {
                    data: 'tgl',
                    name: 'tgl',
                    className: 'text-center'
                }, {
                    data: 'nama_perusahaan',
                    name: 'nama_perusahaan',
                    className: 'text-center'
                }, {
                    data: 'kebutuhan',
                    name: 'kebutuhan',
                    className: 'text-center'
                }, {
                    data: 'nama_site',
                    name: 'nama_site',
                    className: 'text-center'
                }, {
                    data: 'status',
                    name: 'status',
                    className: 'text-center'
                }, {
                    data: 'company',
                    name: 'company',
                    className: 'text-center'
                }, {
                    data: 'created_by',
                    name: 'created_by',
                    className: 'text-center'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    width: "10%",
                    orderable: false,
                    searchable: false,
                }],
                "language": datatableLang,
            });
        </script>
    @endif
@endsection